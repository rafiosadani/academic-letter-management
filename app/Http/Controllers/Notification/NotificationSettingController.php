<?php

namespace App\Http\Controllers\Notification;

use App\Enums\NotificationCategory;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationSettingController extends Controller
{
    /**
     * Display notification settings page
     */
    public function index()
    {
        $user = Auth::user();

        // Get all categories
        $categories = NotificationCategory::cases();

        // Get user settings for each category
        $settings = [];
        foreach ($categories as $category) {
            $settings[$category->value] = NotificationSetting::getOrCreate($user->id, $category->value);
        }

        return view('notifications.settings', compact('categories', 'settings'));
    }

    /**
     * Update notification settings
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'settings' => 'required|array',
            'settings.*.channel_email' => 'boolean',
            'settings.*.email_immediately' => 'boolean',
            'settings.*.email_daily_digest' => 'boolean',
        ]);

        dd($request);

        try {
            // Additional validation: If email is ON, must choose at least one delivery method
            foreach ($request->settings as $category => $settings) {
                $channelEmail = $settings['channel_email'] ?? false;
                $emailImmediately = $settings['email_immediately'] ?? false;
                $emailDailyDigest = $settings['email_daily_digest'] ?? false;

                // If email notification is enabled, must choose at least one method
                if ($channelEmail && !$emailImmediately && !$emailDailyDigest) {
                    $categoryLabel = NotificationCategory::from($category)->label();

                    LogHelper::logWarning('Notification settings validation failed: No delivery method selected', [
                        'user_id' => $user->id,
                        'category' => $category,
                    ], $request);

                    return redirect()->back()
                        ->withInput()
                        ->with('notification_data', [
                            'type' => 'error',
                            'text' => "Kategori \"{$categoryLabel}\" Jika mengaktifkan email, pilih minimal satu metode pengiriman (Langsung atau Ringkasan Harian).",
                            'position' => 'center-top',
                            'duration' => 6000
                        ]);
                }
            }

            // save settings
            DB::transaction(function () use ($request, $user) {
                foreach ($request->settings as $category => $settings) {
                    $channelEmail = $settings['channel_email'] ?? false;

                    NotificationSetting::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'category' => $category,
                        ],
                        [
                            'channel_database' => true, // Always ON
                            'channel_email' => $channelEmail,
                            'email_immediately' => $channelEmail ? ($settings['email_immediately'] ?? false) : false,
                            'email_daily_digest' => $channelEmail ? ($settings['email_daily_digest'] ?? false) : false,
                        ]
                    );
                }
            });

            LogHelper::logInfo('Notification settings updated successfully', [
                'user_id' => $user->id,
                'settings_count' => count($request->settings),
            ], $request);

            return redirect()->back()->with('notification_data', [
                'type' => 'success',
                'text' => 'Pengaturan notifikasi berhasil diperbarui!',
                'position' => 'center-top',
                'duration' => 4000
            ]);

        } catch (\Exception $e) {
            LogHelper::logError('update settings', 'notification_settings', $e, [
                'request_data' => $request->except(['_token'])
            ], $request);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'position' => 'center-top',
                'duration' => 6000
            ]);
        }
    }
}
