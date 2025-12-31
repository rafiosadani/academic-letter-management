<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\LetterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get Student Dashboard Statistics
     */
    public function getStudentStats(int $studentId): array
    {
        $currentMonth = Carbon::now()->startOfMonth();

        // Summary counts
        $total = LetterRequest::where('student_id', $studentId)->count();
        $inProgress = LetterRequest::where('student_id', $studentId)
            ->whereIn('status', ['in_progress', 'external_processing'])
            ->count();
        $completed = LetterRequest::where('student_id', $studentId)
            ->where('status', 'completed')
            ->count();
        $rejected = LetterRequest::where('student_id', $studentId)
            ->where('status', 'rejected')
            ->count();

        // This month stats
        $thisMonthTotal = LetterRequest::where('student_id', $studentId)
            ->where('created_at', '>=', $currentMonth)
            ->count();

        // Status distribution for pie chart
        $statusDistribution = [
            'in_progress' => $inProgress,
            'completed' => $completed,
            'rejected' => $rejected,
        ];

        // By letter type for bar chart
        $byType = LetterRequest::where('student_id', $studentId)
            ->select('letter_type', DB::raw('count(*) as total'))
            ->groupBy('letter_type')
            ->pluck('total', 'letter_type')
            ->toArray();

        // Average processing time (completed letters only)
        $avgTime = LetterRequest::where('student_id', $studentId)
            ->where('status', 'completed')
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)) as avg_days')
            ->value('avg_days');

        return [
            'summary' => [
                'total' => $total,
                'in_progress' => $inProgress,
                'completed' => $completed,
                'rejected' => $rejected,
                'this_month' => $thisMonthTotal,
                'success_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
                'avg_processing_time' => round($avgTime ?? 0, 1),
            ],
            'charts' => [
                'status_distribution' => $statusDistribution,
                'by_type' => $byType,
            ],
        ];
    }

    /**
     * Get recent letters for student
     */
    public function getRecentLetters(int $studentId, int $limit = 5)
    {
        return LetterRequest::where('student_id', $studentId)
            ->with(['semester', 'academicYear'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get in-progress letters with timeline for student
     */
    public function getInProgressLetters(int $studentId)
    {
        return LetterRequest::where('student_id', $studentId)
            ->whereIn('status', ['in_progress', 'external_processing'])
            ->with(['approvals' => function ($query) {
                $query->orderBy('step');
            }, 'approvals.assignedApprover.profile'])
            ->get();
    }

    /**
     * Get Staff Dashboard Statistics
     */
    public function getStaffStats(int $userId): array
    {
        $userPosition = User::find($userId)->currentOfficialPosition?->position;

        if (!$userPosition) {
            return $this->getEmptyStaffStats();
        }

        $currentMonth = Carbon::now()->startOfMonth();
        $today = Carbon::today();

        // Pending approvals (step 1 - Drafter)
        $pending = Approval::where('status', 'pending')
            ->where('is_active', true)
            ->whereJsonContains('required_positions', $userPosition->value)
            ->count();

        // Urgent (> 3 days)
        $urgent = Approval::where('status', 'pending')
            ->where('is_active', true)
            ->whereJsonContains('required_positions', $userPosition->value)
            ->whereHas('letterRequest', function ($q) {
                $q->where('created_at', '<=', Carbon::now()->subDays(3));
            })
            ->count();

        // Approved today
        $approvedToday = Approval::where('approved_by', $userId)
            ->where('status', 'approved')
            ->whereDate('approved_at', $today)
            ->count();

        // Total this month
        $totalMonth = Approval::where('approved_by', $userId)
            ->whereIn('status', ['approved', 'rejected'])
            ->where('approved_at', '>=', $currentMonth)
            ->count();

        // Approved vs rejected this month
        $approvedMonth = Approval::where('approved_by', $userId)
            ->where('status', 'approved')
            ->where('approved_at', '>=', $currentMonth)
            ->count();

        $rejectedMonth = Approval::where('approved_by', $userId)
            ->where('status', 'rejected')
            ->where('approved_at', '>=', $currentMonth)
            ->count();

        // Average processing time (total)
        $avgTime = Approval::where('approved_by', $userId)
            ->where('status', 'approved')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at) / 24) as avg_days')
            ->value('avg_days');

        // Average time per step (NEW!)
        $avgTimePerStep = $this->getAvgTimePerStep($userId, 'staff');

        // Activity last 7 days for chart
        $activityData = $this->getActivityLast7Days($userId);

        // Priority breakdown for radial chart
        $priorityBreakdown = [
            'urgent' => $urgent,
            'normal' => max(0, $pending - $urgent),
        ];

        return [
            'summary' => [
                'pending' => $pending,
                'urgent' => $urgent,
                'approved_today' => $approvedToday,
                'total_month' => $totalMonth,
                'avg_time' => round($avgTime ?? 0, 1),
                'success_rate' => $totalMonth > 0 ? round(($approvedMonth / $totalMonth) * 100, 1) : 0,
            ],
            'charts' => [
                'priority_breakdown' => $priorityBreakdown,
                'activity_7days' => $activityData,
            ],
            'performance' => [
                'approved_month' => $approvedMonth,
                'rejected_month' => $rejectedMonth,
                'avg_time_per_step' => $avgTimePerStep,
            ],
        ];
    }

    /**
     * Get average processing time per step
     */
    protected function getAvgTimePerStep(int $userId, string $role): array
    {
        $approvals = Approval::where('approved_by', $userId)
            ->where('status', 'approved')
            ->selectRaw('step, AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at) / 24) as avg_days')
            ->groupBy('step')
            ->get();

        $result = [];
        foreach ($approvals as $approval) {
            $result["step_{$approval->step}"] = round($approval->avg_days, 1);
        }

        return $result;
    }

    /**
     * Get activity data for last 7 days
     */
    protected function getActivityLast7Days(int $userId): array
    {
        $days = [];
        $approved = [];
        $rejected = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('D'); // Mon, Tue, etc

            $approvedCount = Approval::where('approved_by', $userId)
                ->where('status', 'approved')
                ->whereDate('approved_at', $date)
                ->count();

            $rejectedCount = Approval::where('approved_by', $userId)
                ->where('status', 'rejected')
                ->whereDate('approved_at', $date)
                ->count();

            $approved[] = $approvedCount;
            $rejected[] = $rejectedCount;
        }

        return [
            'categories' => $days,
            'approved' => $approved,
            'rejected' => $rejected,
        ];
    }

    /**
     * Get pending approvals for staff
     */
    public function getPendingApprovalsForStaff(int $userId)
    {
        $userPosition = User::find($userId)->currentOfficialPosition?->position;

        if (!$userPosition) {
            return collect();
        }

        return Approval::where('status', 'pending')
            ->where('is_active', true)
            ->whereJsonContains('required_positions', $userPosition->value)
            ->with(['letterRequest.student.profile', 'letterRequest.semester'])
            ->orderByRaw('TIMESTAMPDIFF(DAY, created_at, NOW()) DESC') // Oldest first
            ->get()
            ->map(function ($approval) {
                $daysWaiting = Carbon::parse($approval->created_at)->diffInDays(Carbon::now());
                $approval->days_waiting = $daysWaiting;
                $approval->priority = $daysWaiting > 3 ? 'urgent' : ($daysWaiting > 1 ? 'normal' : 'recent');
                return $approval;
            });
    }

    /**
     * Get recent activity for staff
     */
    public function getRecentActivityForStaff(int $userId, int $limit = 10)
    {
        return Approval::where('approved_by', $userId)
            ->whereIn('status', ['approved', 'rejected'])
            ->with(['letterRequest.student.profile'])
            ->latest('approved_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Empty stats for staff without position
     */
    protected function getEmptyStaffStats(): array
    {
        return [
            'summary' => [
                'pending' => 0,
                'urgent' => 0,
                'approved_today' => 0,
                'total_month' => 0,
                'avg_time' => 0,
                'success_rate' => 0,
            ],
            'charts' => [
                'priority_breakdown' => ['urgent' => 0, 'normal' => 0],
                'activity_7days' => [
                    'categories' => [],
                    'approved' => [],
                    'rejected' => [],
                ],
            ],
            'performance' => [
                'approved_month' => 0,
                'rejected_month' => 0,
                'avg_time_per_step' => [],
            ],
        ];
    }

    /**
     * Get Kasubbag Dashboard Statistics
     */
    public function getKasubbagStats(int $userId): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $today = Carbon::today();

        // Pending paraf (step 2)
        $pending = Approval::where('status', 'pending')
            ->where('is_active', true)
            ->where('step', 2)
            ->count();

        // Approved today
        $approvedToday = Approval::where('status', 'approved')
            ->where('step', 2)
            ->whereDate('approved_at', $today)
            ->count();

        // Total this month
        $totalMonth = Approval::where('step', 2)
            ->whereIn('status', ['approved', 'rejected'])
            ->where('approved_at', '>=', $currentMonth)
            ->count();

        // Success rate
        $approvedMonth = Approval::where('step', 2)
            ->where('status', 'approved')
            ->where('approved_at', '>=', $currentMonth)
            ->count();

        $successRate = $totalMonth > 0 ? round(($approvedMonth / $totalMonth) * 100, 1) : 0;

        // Average time per step
        $avgTimePerStep = $this->getAvgTimePerStep($userId, 'kasubbag');

        return [
            'summary' => [
                'pending' => $pending,
                'approved_today' => $approvedToday,
                'total_month' => $totalMonth,
                'success_rate' => $successRate,
            ],
            'performance' => [
                'avg_time_per_step' => $avgTimePerStep,
            ],
        ];
    }

    /**
     * Get pending approvals for Kasubbag
     */
    public function getPendingApprovalsForKasubbag(int $userId)
    {
        return Approval::where('status', 'pending')
            ->where('is_active', true)
            ->where('step', 2)
            ->with(['letterRequest.student.profile', 'letterRequest.semester'])
            ->latest('created_at')
            ->get()
            ->groupBy('letterRequest.letter_type');
    }

    /**
     * Get approval flow metrics (funnel data)
     */
    public function getApprovalFlowMetrics(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();

        $submitted = LetterRequest::where('created_at', '>=', $currentMonth)->count();

        $step1Approved = Approval::where('step', 1)
            ->where('status', 'approved')
            ->where('approved_at', '>=', $currentMonth)
            ->count();

        $step2Approved = Approval::where('step', 2)
            ->where('status', 'approved')
            ->where('approved_at', '>=', $currentMonth)
            ->count();

        $step3Approved = Approval::where('step', 3)
            ->where('status', 'approved')
            ->where('approved_at', '>=', $currentMonth)
            ->count();

        $completed = LetterRequest::where('status', 'completed')
            ->where('updated_at', '>=', $currentMonth)
            ->count();

        return [
            'submitted' => $submitted,
            'step1_approved' => $step1Approved,
            'step2_approved' => $step2Approved,
            'step3_approved' => $step3Approved,
            'completed' => $completed,
            'step1_pass_rate' => $submitted > 0 ? round(($step1Approved / $submitted) * 100, 1) : 0,
            'step2_pass_rate' => $step1Approved > 0 ? round(($step2Approved / $step1Approved) * 100, 1) : 0,
            'step3_pass_rate' => $step2Approved > 0 ? round(($step3Approved / $step2Approved) * 100, 1) : 0,
        ];
    }

    /**
     * Get bottleneck analysis (avg time per step)
     */
    public function getBottleneckAnalysis(): array
    {
        $steps = [1, 2, 3];
        $avgTimes = [];
        $labels = [];

        foreach ($steps as $step) {
            $avg = Approval::where('step', $step)
                ->where('status', 'approved')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at) / 24) as avg_days')
                ->value('avg_days');

            $avgTimes[] = round($avg ?? 0, 1);

            // Step labels
            if ($step === 1) $labels[] = 'Verifikasi Drafter';
            if ($step === 2) $labels[] = 'Paraf Kasubbag';
            if ($step === 3) $labels[] = 'TTE Wakil Dekan';
        }

        return [
            'labels' => $labels,
            'avg_times' => $avgTimes,
        ];
    }

    /**
     * Get Wakil Dekan Dashboard Statistics
     */
    public function getWakilDekanStats(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $currentYear = Carbon::now()->startOfYear();
        $today = Carbon::today();

        // Pending TTE (step 3)
        $pendingTte = Approval::where('status', 'pending')
            ->where('is_active', true)
            ->where('step', 3)
            ->count();

        // TTE today
        $tteToday = Approval::where('status', 'approved')
            ->where('step', 3)
            ->whereDate('approved_at', $today)
            ->count();

        // Total this month
        $totalMonth = Approval::where('step', 3)
            ->whereIn('status', ['approved', 'rejected'])
            ->where('approved_at', '>=', $currentMonth)
            ->count();

        // YTD total
        $ytdTotal = LetterRequest::where('created_at', '>=', $currentYear)->count();

        // Growth vs last year
        $lastYearTotal = LetterRequest::whereBetween('created_at', [
            $currentYear->copy()->subYear(),
            $currentYear->copy()->subYear()->endOfYear()
        ])->count();

        $growth = $lastYearTotal > 0 ? round((($ytdTotal - $lastYearTotal) / $lastYearTotal) * 100, 1) : 0;

        // TAT (Turnaround Time) - Total avg
        $tat = LetterRequest::where('status', 'completed')
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)) as avg_days')
            ->value('avg_days');

        return [
            'summary' => [
                'pending_tte' => $pendingTte,
                'tte_today' => $tteToday,
                'total_month' => $totalMonth,
                'ytd_total' => $ytdTotal,
                'growth' => $growth,
                'tat' => round($tat ?? 0, 1),
            ],
        ];
    }

    /**
     * Get pending approvals for Wakil Dekan
     */
    public function getPendingApprovalsForWakilDekan(int $userId)
    {
        return Approval::where('status', 'pending')
            ->where('is_active', true)
            ->where('step', 3)
            ->with(['letterRequest.student.profile', 'letterRequest.student.programStudi'])
            ->latest('created_at')
            ->get();
    }

    /**
     * Get stats by Program Studi
     */
    public function getStatsByProdi(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();

//        $stats = LetterRequest::where('created_at', '>=', $currentMonth)
//            ->join('student.profiles', 'letter_requests.student_id', '=', 'student.profiles.id')
//            ->select('student.profiles.studyProgram', DB::raw('count(*) as total'))
//            ->groupBy('student_profiles.studyProgram')
//            ->get();

        $stats = LetterRequest::where('letter_requests.created_at', '>=', $currentMonth)
            ->join('users', 'letter_requests.student_id', '=', 'users.id')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->join('study_programs', 'user_profiles.study_program_id', '=', 'study_programs.id')
            ->select(
                'study_programs.name as studyProgram',
                DB::raw('count(*) as total')
            )
            ->groupBy('study_programs.name')
            ->get();

        $labels = [];
        $data = [];

        foreach ($stats as $stat) {
            $labels[] = $stat->studyProgram ?? 'Unknown';
            $data[] = $stat->total;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get trend data (YoY comparison)
     */
    public function getTrendData(): array
    {
        $currentYear = Carbon::now()->year;
        $lastYear = $currentYear - 1;

        $months = [];
        $currentYearData = [];
        $lastYearData = [];

        for ($month = 1; $month <= 12; $month++) {
            $months[] = Carbon::create()->month($month)->format('M');

            // Current year
            $currentCount = LetterRequest::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->count();
            $currentYearData[] = $currentCount;

            // Last year
            $lastCount = LetterRequest::whereYear('created_at', $lastYear)
                ->whereMonth('created_at', $month)
                ->count();
            $lastYearData[] = $lastCount;
        }

        return [
            'categories' => $months,
            'series' => [
                [
                    'name' => (string) $lastYear,
                    'data' => $lastYearData,
                ],
                [
                    'name' => (string) $currentYear,
                    'data' => $currentYearData,
                ],
            ],
        ];
    }

    /**
     * Get insights and recommendations
     */
    public function getInsights(): array
    {
        $bottleneck = $this->getBottleneckAnalysis();
        $maxTime = max($bottleneck['avg_times']);
        $maxIndex = array_search($maxTime, $bottleneck['avg_times']);
        $bottleneckStep = $bottleneck['labels'][$maxIndex] ?? 'Unknown';

        $urgent = Approval::where('status', 'pending')
            ->where('is_active', true)
            ->whereHas('letterRequest', function ($q) {
                $q->where('created_at', '<=', Carbon::now()->subDays(5));
            })
            ->count();

        $growth = $this->getWakilDekanStats()['summary']['growth'];

        return [
            'bottleneck' => [
                'step' => $bottleneckStep,
                'avg_time' => $maxTime,
            ],
            'urgent_count' => $urgent,
            'growth' => $growth,
        ];
    }

    /**
     * Get Admin Dashboard Statistics
     */
    public function getAdminStats(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $today = Carbon::today();

        $totalUsers = User::count();
        $totalLetters = LetterRequest::count();
        $pending = LetterRequest::whereIn('status', ['in_progress', 'external_processing'])->count();
        $todaySubmissions = LetterRequest::whereDate('created_at', $today)->count();
        $thisMonthSubmissions = LetterRequest::where('created_at', '>=', $currentMonth)->count();

        return [
            'summary' => [
                'total_users' => $totalUsers,
                'total_letters' => $totalLetters,
                'pending' => $pending,
                'today' => $todaySubmissions,
                'this_month' => $thisMonthSubmissions,
            ],
        ];
    }

    /**
     * Get system health status
     */
    public function getSystemHealth(): array
    {
        // Check failed jobs
        $failedJobs = DB::table('failed_jobs')->count();

        // Database health (simple ping)
        $dbHealthy = true;
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbHealthy = false;
        }

        // Storage usage
        $storagePath = storage_path('app');
        $totalSpace = disk_total_space($storagePath);
        $freeSpace = disk_free_space($storagePath);
        $usedPercent = round((($totalSpace - $freeSpace) / $totalSpace) * 100, 1);

        return [
            'queue_status' => 'running', // Assume running (check via supervisor in production)
            'scheduler_status' => 'active',
            'database_status' => $dbHealthy ? 'healthy' : 'error',
            'storage_used_percent' => $usedPercent,
            'failed_jobs' => $failedJobs,
        ];
    }

    /**
     * Get user distribution
     */
    public function getUserDistribution(): array
    {
        $distribution = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('count(*) as total'))
            ->groupBy('roles.name')
            ->get();

        $labels = [];
        $data = [];

        foreach ($distribution as $item) {
            $labels[] = ucwords(str_replace('_', ' ', $item->name));
            $data[] = $item->total;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get recent activity log
     */
    public function getRecentActivityLog(int $limit = 20)
    {
        return LetterRequest::with(['student.profile'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get system usage data (last 30 days)
     */
    public function getSystemUsageData(): array
    {
        $days = [];
        $submissions = [];
        $approvals = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('M j');

            $submissionCount = LetterRequest::whereDate('created_at', $date)->count();
            $submissions[] = $submissionCount;

            $approvalCount = Approval::where('status', 'approved')
                ->whereDate('approved_at', $date)
                ->count();
            $approvals[] = $approvalCount;
        }

        return [
            'categories' => $days,
            'series' => [
                [
                    'name' => 'Submissions',
                    'data' => $submissions,
                ],
                [
                    'name' => 'Approvals',
                    'data' => $approvals,
                ],
            ],
        ];
    }
}