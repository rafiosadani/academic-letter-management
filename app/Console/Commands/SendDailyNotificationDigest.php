<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyNotificationDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-notification-digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily notification digest email to users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending daily notification digest...');

        // Get users who have email_daily_digest enabled
        $users = User::whereHas('notificationSettings', function($q) {
            $q->where('channel_email', 1)
                ->where('email_daily_digest', 1);
        })->get();

        if ($users->isEmpty()) {
            $this->info('No users with daily digest enabled.');
            return Command::SUCCESS;
        }

        $sentCount = 0;

        foreach ($users as $user) {
            // Get unread notifications from last 24 hours
            $notifications = $user->unreadNotifications()
                ->where('created_at', '>=', now()->subDay())
                ->get();

            if ($notifications->isEmpty()) {
                continue;
            }

            // Send digest email
            try {
                Mail::to($user->email)->send(new \App\Mail\DailyNotificationDigest($user, $notifications));
                $sentCount++;
                $this->info("Digest sent to: {$user->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send digest to {$user->email}: {$e->getMessage()}");
            }
        }

        $this->info("Daily digest sent to {$sentCount} users!");

        return Command::SUCCESS;
    }
}
