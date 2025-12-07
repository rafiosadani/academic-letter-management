<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test send email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        Mail::raw('Test email dari Laravel Siperta!', function($message) use ($email) {
            $message->to($email)
                ->subject('Test Email - Siperta');
        });

        $this->info("✅ Email sent to: {$email}");
    }
}
