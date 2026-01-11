<?php

namespace App\Http\Controllers\Setting;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\General\UpdateGeneralSettingRequest;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    use AuthorizesRequests;
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Show the form for editing settings.
     */
    public function edit()
    {
//        $this->authorize('view', Setting::class);

        // Get all settings grouped by category
        $settings = $this->settingService->getAllGrouped();

        return view('settings.general.edit', compact('settings'));
    }

    /**
     * Update settings in storage.
     */
    public function update(UpdateGeneralSettingRequest $request) {
//        $this->authorize('update', Setting::class);

        try {
            DB::transaction(function () use ($request) {
                $data = $request->validated();

                // separate file and text inputs
                $textInputs = [];
                $fileInputs = [];

                foreach ($data as $key => $value) {
                    if ($request->hasFile($key)) {
                        $fileInputs[$key] = $value;
                    } else {
                        $textInputs[$key] = $value;
                    }
                }

                // update text inputs
                if (!empty($textInputs)) {
                    $this->settingService->updateMany($textInputs);
                }

                // upload and update images
                foreach ($fileInputs as $key => $file) {
                    $this->settingService->uploadImage($key, $file);
                }

                // LOG SUCCESS
                LogHelper::logSuccess('updated', 'general settings', [
                    'updated_keys' => array_keys($data),
                ], $request);
            });

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => 'Pengaturan umum berhasil diperbarui.',
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('settings.general.edit');
        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('update', 'general settings', $e, [
                'request_data' => $request->except(['_token', '_method'])
            ], $request);

            return redirect()->back()->withInput()
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Gagal memperbarui pengaturan. Silakan coba lagi.',
                    'position' => 'center-top',
                    'duration' => 6000,
                ]);
        }
    }
}
