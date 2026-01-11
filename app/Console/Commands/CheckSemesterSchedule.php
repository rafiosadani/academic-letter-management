<?php

namespace App\Console\Commands;

use App\Models\Semester;
use App\Models\User;
use App\Notifications\SemesterToggleSuggestion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckSemesterSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-semester-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if any semester should be activated based on current date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking semester schedule...');

        // Find semesters that should be active based on current date
        $suggestedSemesters = Semester::whereHas('academicYear', function($q) {
            $q->where('is_active', 1);
        })
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('is_active', 0)
            ->get();

        if ($suggestedSemesters->isEmpty()) {
            $this->info('No semester needs to be activated.');
            return Command::SUCCESS;
        }

        foreach ($suggestedSemesters as $semester) {
            // Send notification to admins
            $admins = User::role(['administrator', 'kepala subbagian akademik'])->get();

            Notification::send($admins, new SemesterToggleSuggestion($semester));

            $this->info("Notification sent for semester: {$semester->full_label}");
        }

        $this->info('Semester check completed!');

        return Command::SUCCESS;
    }
}
