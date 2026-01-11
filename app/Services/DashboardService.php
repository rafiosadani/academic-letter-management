<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\LetterRequest;
use App\Models\Semester;
use App\Models\StudyProgram;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get date range based on filter
     */
    protected function getDateRange(string $filter): array
    {
        return match($filter) {
            'this_month' => [
                'start' => Carbon::now()->startOfMonth(),
                'end' => Carbon::now()->endOfMonth(),
            ],
            'last_30_days' => [
                'start' => Carbon::now()->subDays(30),
                'end' => Carbon::now(),
            ],
            'this_semester' => [
                'start' => $this->getCurrentSemesterStart(),
                'end' => $this->getCurrentSemesterEnd(),
            ],
            'all_time' => [
                'start' => Carbon::create(2000, 1, 1),
                'end' => Carbon::now(),
            ],
            default => [
                'start' => Carbon::now()->startOfMonth(),
                'end' => Carbon::now()->endOfMonth(),
            ],
        };
    }

    protected function getCurrentSemesterStart(): Carbon
    {
        $activeSemester = Semester::where('is_active', true)->first();
        return $activeSemester?->start_date ?? Carbon::now()->startOfMonth();
    }

    protected function getCurrentSemesterEnd(): Carbon
    {
        $activeSemester = Semester::where('is_active', true)->first();
        return $activeSemester?->end_date ?? Carbon::now()->endOfMonth();
    }

    /**
     * Get Student Dashboard Statistics
     */
    public function getStudentStats(int $studentId, string $filter = 'this_month'): array
    {
        $dateRange = $this->getDateRange($filter);

        // All time totals
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

        // Filtered stats
        $thisRangeTotal = LetterRequest::where('student_id', $studentId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $inProgressFiltered = LetterRequest::where('student_id', $studentId)
            ->whereIn('status', ['in_progress', 'external_processing'])
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();
        $completedFiltered = LetterRequest::where('student_id', $studentId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();
        $rejectedFiltered = LetterRequest::where('student_id', $studentId)
            ->where('status', 'rejected')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $statusDistribution = [
            'in_progress' => $inProgressFiltered,
            'completed' => $completedFiltered,
            'rejected' => $rejectedFiltered,
        ];

        $byType = LetterRequest::where('student_id', $studentId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->select('letter_type', DB::raw('count(*) as total'))
            ->groupBy('letter_type')
            ->pluck('total', 'letter_type')
            ->toArray();

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
                'this_month' => $thisRangeTotal,
                'success_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
                'avg_processing_time' => round($avgTime ?? 0, 1),
            ],
            'charts' => [
                'status_distribution' => $statusDistribution,
                'by_type' => $byType,
            ],
        ];
    }

    public function getRecentLetters(int $studentId, int $limit = 5)
    {
        return LetterRequest::where('student_id', $studentId)
            ->with(['semester', 'academicYear'])
            ->latest()
            ->limit($limit)
            ->get();
    }

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
    public function getStaffStats(int $userId, string $filter = 'this_month'): array
    {
        $userPosition = User::find($userId)->currentOfficialPosition?->position;

        if (!$userPosition) {
            return $this->getEmptyStaffStats();
        }

        $dateRange = $this->getDateRange($filter);
        $today = Carbon::today();

        $pending = Approval::where('status', 'pending')
            ->where('is_active', true)
            ->whereJsonContains('required_positions', $userPosition->value)
            ->count();

        $urgent = Approval::where('status', 'pending')
            ->where('is_active', true)
            ->whereJsonContains('required_positions', $userPosition->value)
            ->whereHas('letterRequest', function ($q) {
                $q->where('created_at', '<=', Carbon::now()->subDays(3));
            })
            ->count();

        $approvedToday = Approval::where('approved_by', $userId)
            ->where('status', 'approved')
            ->whereDate('approved_at', $today)
            ->count();

        $totalInRange = Approval::where('approved_by', $userId)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $approvedInRange = Approval::where('approved_by', $userId)
            ->where('status', 'approved')
            ->whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $rejectedInRange = Approval::where('approved_by', $userId)
            ->where('status', 'rejected')
            ->whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $avgTime = Approval::where('approved_by', $userId)
            ->where('status', 'approved')
            ->whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at) / 24) as avg_days')
            ->value('avg_days');

        $avgTimePerStep = $this->getAvgTimePerStep($userId, 'staff', $filter);
        $activityData = $this->getActivityLast7Days($userId);

        $priorityBreakdown = [
            'urgent' => $urgent,
            'normal' => max(0, $pending - $urgent),
        ];

        return [
            'summary' => [
                'pending' => $pending,
                'urgent' => $urgent,
                'approved_today' => $approvedToday,
                'total_month' => $totalInRange,
                'avg_time' => round($avgTime ?? 0, 1),
                'success_rate' => $totalInRange > 0 ? round(($approvedInRange / $totalInRange) * 100, 1) : 0,
            ],
            'charts' => [
                'priority_breakdown' => $priorityBreakdown,
                'activity_7days' => $activityData,
            ],
            'performance' => [
                'approved_month' => $approvedInRange,
                'rejected_month' => $rejectedInRange,
                'avg_time_per_step' => $avgTimePerStep,
            ],
        ];
    }

    /**
     * Get average processing time per step
     */
    protected function getAvgTimePerStep(int $userId, string $role, string $filter = 'this_month'): array
    {
        $dateRange = $this->getDateRange($filter);

        $approvals = Approval::where('approved_by', $userId)
            ->where('status', 'approved')
            ->whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])
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
            $days[] = $date->format('D');

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
                $totalHours = $approval->created_at->diffInHours(Carbon::now());
                $decimalDays = $totalHours / 24;
                $daysWaiting = (int) round($decimalDays);
                $daysWaiting = $daysWaiting < 1 ? 0 : $daysWaiting;

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
    public function getKasubbagStats(int $userId, string $filter = 'this_month'): array
    {
        $dateRange = $this->getDateRange($filter);
        $today = Carbon::today();

        $pending = Approval::where('status', 'pending')
            ->where('is_active', true)
            ->where('step', 2)
            ->count();

        $approvedToday = Approval::where('status', 'approved')
            ->where('step', 2)
            ->whereDate('approved_at', $today)
            ->count();

        $totalInRange = Approval::where('step', 2)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $approvedInRange = Approval::where('step', 2)
            ->where('status', 'approved')
            ->whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $successRate = $totalInRange > 0 ? round(($approvedInRange / $totalInRange) * 100, 1) : 0;
        $avgTimePerStep = $this->getAvgTimePerStepForRole('kasubbag', $filter);

        return [
            'summary' => [
                'pending' => $pending,
                'approved_today' => $approvedToday,
                'total_month' => $totalInRange,
                'success_rate' => $successRate,
            ],
            'performance' => [
                'avg_time_per_step' => $avgTimePerStep,
            ],
        ];
    }

    protected function getAvgTimePerStepForRole(string $role, string $filter = 'this_month'): array
    {
        $dateRange = $this->getDateRange($filter);
        $steps = [1, 2, 3];
        $result = [];

        foreach ($steps as $step) {
            $avg = Approval::where('step', $step)
                ->where('status', 'approved')
                ->whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at) / 24) as avg_days')
                ->value('avg_days');

            $result["step_{$step}"] = round($avg ?? 0, 1);
        }

        return $result;
    }

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

    public function getApprovalFlowMetrics(string $filter = 'this_month'): array
    {
        $dateRange = $this->getDateRange($filter);

        $submitted = LetterRequest::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        $step1Approved = Approval::where('step', 1)->where('status', 'approved')
            ->whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])->count();
        $step2Approved = Approval::where('step', 2)->where('status', 'approved')
            ->whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])->count();
        $step3Approved = Approval::where('step', 3)->where('status', 'approved')
            ->whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])->count();
        $completed = LetterRequest::where('status', 'completed')
            ->whereBetween('updated_at', [$dateRange['start'], $dateRange['end']])->count();

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

            if ($step === 1) $labels[] = 'Verifikasi Drafter';
            if ($step === 2) $labels[] = 'Paraf Kasubbag';
            if ($step === 3) $labels[] = 'TTE Wakil Dekan';
        }

        return [
            'labels' => $labels,
            'avg_times' => $avgTimes,
        ];
    }

    // ============================================
    // WAKIL DEKAN METHODS
    // ============================================

    public function getWakilDekanStats(string $filter = 'this_month'): array
    {
        $dateRange = $this->getDateRange($filter);
        $currentYear = Carbon::now()->startOfYear();
        $today = Carbon::today();

        $pendingTte = Approval::where('status', 'pending')->where('is_active', true)->where('step', 3)->count();
        $tteToday = Approval::where('status', 'approved')->where('step', 3)->whereDate('approved_at', $today)->count();
        $totalInRange = Approval::where('step', 3)->whereIn('status', ['approved', 'rejected'])
            ->whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])->count();
        $ytdTotal = LetterRequest::where('created_at', '>=', $currentYear)->count();

        $lastYearTotal = LetterRequest::whereBetween('created_at', [
            $currentYear->copy()->subYear(),
            $currentYear->copy()->subYear()->endOfYear()
        ])->count();
        $growth = $lastYearTotal > 0 ? round((($ytdTotal - $lastYearTotal) / $lastYearTotal) * 100, 1) : 0;

        $tat = LetterRequest::where('status', 'completed')
            ->whereBetween('updated_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)) as avg_days')
            ->value('avg_days');

        return [
            'summary' => [
                'pending_tte' => $pendingTte,
                'tte_today' => $tteToday,
                'total_month' => $totalInRange,
                'ytd_total' => $ytdTotal,
                'growth' => $growth,
                'tat' => round($tat ?? 0, 1),
            ],
        ];
    }

    public function getPendingApprovalsForWakilDekan(int $userId)
    {
        return Approval::where('status', 'pending')
            ->where('is_active', true)
            ->where('step', 3)
            ->with(['letterRequest.student.profile', 'letterRequest.student'])
            ->latest('created_at')
            ->get();
    }

    /**
     * Get stats by Program Studi
     */
    public function getStatsByProdi(string $filter = 'this_month'): array
    {
        $dateRange = $this->getDateRange($filter);

//        $stats = LetterRequest::where('created_at', '>=', $currentMonth)
//            ->join('student.profiles', 'letter_requests.student_id', '=', 'student.profiles.id')
//            ->select('student.profiles.studyProgram', DB::raw('count(*) as total'))
//            ->groupBy('student_profiles.studyProgram')
//            ->get();

        $stats = LetterRequest::whereBetween('letter_requests.created_at', [$dateRange['start'], $dateRange['end']])
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

    public function getTrendData(): array
    {
        $currentYear = Carbon::now()->year;
        $lastYear = $currentYear - 1;
        $months = [];
        $currentYearData = [];
        $lastYearData = [];

        for ($month = 1; $month <= 12; $month++) {
            $months[] = Carbon::create()->month($month)->format('M');
            $currentYearData[] = LetterRequest::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)->count();
            $lastYearData[] = LetterRequest::whereYear('created_at', $lastYear)
                ->whereMonth('created_at', $month)->count();
        }

        return [
            'categories' => $months,
            'series' => [
                ['name' => (string) $lastYear, 'data' => $lastYearData],
                ['name' => (string) $currentYear, 'data' => $currentYearData],
            ],
        ];
    }

    public function getInsights(): array
    {
        $bottleneck = $this->getBottleneckAnalysis();
        $maxTime = max($bottleneck['avg_times']);
        $maxIndex = array_search($maxTime, $bottleneck['avg_times']);
        $bottleneckStep = $bottleneck['labels'][$maxIndex] ?? 'Unknown';

        $urgent = Approval::where('status', 'pending')->where('is_active', true)
            ->whereHas('letterRequest', function ($q) {
                $q->where('created_at', '<=', Carbon::now()->subDays(5));
            })->count();

        $growth = $this->getWakilDekanStats()['summary']['growth'];

        return [
            'bottleneck' => ['step' => $bottleneckStep, 'avg_time' => $maxTime],
            'urgent_count' => $urgent,
            'growth' => $growth,
        ];
    }

    // ============================================
    // ADMIN METHODS
    // ============================================

    public function getAdminStats(string $filter = 'this_month'): array
    {
        $dateRange = $this->getDateRange($filter);
        $today = Carbon::today();

        $totalUsers = User::where('status', 1)->count();
        $totalStudyPrograms = StudyProgram::count();
        $totalLetters = LetterRequest::where('status', 'completed')->count();
        $pending = LetterRequest::whereIn('status', ['in_progress', 'external_processing'])->count();
        $todaySubmissions = LetterRequest::whereDate('created_at', $today)->count();
        $rangeSubmissions = LetterRequest::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();

        return [
            'summary' => [
                'total_users' => $totalUsers,
                'total_study_programs' => $totalStudyPrograms,
                'total_letters' => $totalLetters,
                'pending' => $pending,
                'today' => $todaySubmissions,
                'this_month' => $rangeSubmissions,
            ],
        ];
    }

    public function getSystemHealth(): array
    {
        $failedJobs = DB::table('failed_jobs')->count();
        $dbHealthy = true;
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbHealthy = false;
        }

        $storagePath = storage_path('app');
        $totalSpace = disk_total_space($storagePath);
        $freeSpace = disk_free_space($storagePath);
        $usedPercent = round((($totalSpace - $freeSpace) / $totalSpace) * 100, 1);

        return [
            'queue_status' => 'running',
            'scheduler_status' => 'active',
            'database_status' => $dbHealthy ? 'healthy' : 'error',
            'storage_used_percent' => $usedPercent,
            'failed_jobs' => $failedJobs,
        ];
    }

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

        return ['labels' => $labels, 'data' => $data];
    }

    public function getRecentActivityLog(int $limit = 20, string $action = null)
    {
        $query = LetterRequest::with(['student.profile']);

        if ($action) {
            switch ($action) {
                case 'submit':
                    $query->whereIn('status', ['in_progress', 'resubmitted']);
                    break;
                case 'approve':
                    $query->whereIn('status', ['approved', 'completed']);
                    break;
                case 'reject':
                    $query->where('status', 'rejected');
                    break;
                case 'cancel':
                    $query->where('status', 'cancelled');
                    break;
            }
        }

        return $query->latest()->limit($limit)->get();
//        return LetterRequest::with(['student.profile'])->latest()->limit($limit)->get();
    }

    public function getSystemUsageData(string $filter = 'this_month'): array
    {
        $dateRange = $this->getDateRange($filter);
        $startDate = Carbon::parse($dateRange['start']);
        $endDate = Carbon::parse($dateRange['end']);
        $totalDays = $startDate->diffInDays($endDate);
        $daysToShow = min($totalDays, 30);

        $days = [];
        $submissions = [];
        $approvals = [];

        for ($i = $daysToShow - 1; $i >= 0; $i--) {
            $date = $endDate->copy()->subDays($i);
            $days[] = $date->format('M j');
            $submissions[] = LetterRequest::whereDate('created_at', $date)->count();
            $approvals[] = Approval::where('status', 'approved')->whereDate('approved_at', $date)->count();
        }

        return [
            'categories' => $days,
            'series' => [
                ['name' => 'Pengajuan', 'data' => $submissions],
                ['name' => 'Persetujuan', 'data' => $approvals],
            ],
        ];
    }
}