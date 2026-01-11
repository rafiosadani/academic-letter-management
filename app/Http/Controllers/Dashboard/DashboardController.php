<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Main dashboard - route based on role
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Get filter from request (default: this_month)
        $filter = $request->get('filter', 'this_month');

        // Route to appropriate dashboard based on role
        if ($user->hasRole('Mahasiswa')) {
            return $this->studentDashboard($filter);
        }

        if ($user->hasRole('Staf Akademik')) {
            return $this->staffDashboard($filter);
        }

        if ($user->hasRole('Kepala Subbagian Akademik')) {
            return $this->kasubbagDashboard($filter);
        }

        if ($user->hasRole('Wakil Dekan Bidang Akademik')) {
            return $this->wakilDekanDashboard($filter);
        }

        if ($user->hasRole('Administrator')) {
            return $this->adminDashboard($filter);
        }

        // Default fallback
        abort(403, 'Unauthorized access to dashboard');
    }

    /**
     * Student Dashboard
     */
    protected function studentDashboard(string $filter = 'this_month'): View
    {
        $user = auth()->user();

        $user->load('profile');

        $student = $user->profile;

        if (!$student) {
            abort(404, 'Profil mahasiswa belum tersedia');
        }

        $stats = $this->dashboardService->getStudentStats($student->id, $filter);
        $recentLetters = $this->dashboardService->getRecentLetters($student->id, 5);
        $inProgressLetters = $this->dashboardService->getInProgressLetters($student->id);

        return view('dashboard.student', compact('user', 'student', 'stats', 'recentLetters', 'inProgressLetters', 'filter'));
    }

    /**
     * Staff Dashboard
     */
    protected function staffDashboard(string $filter = 'this_month'): View
    {
        $user = auth()->user();

        $stats = $this->dashboardService->getStaffStats($user->id, $filter);
        $pendingApprovals = $this->dashboardService->getPendingApprovalsForStaff($user->id);
        $recentActivity = $this->dashboardService->getRecentActivityForStaff($user->id, 10);

        return view('dashboard.staff', compact('user', 'stats', 'pendingApprovals', 'recentActivity', 'filter'));
    }

    /**
     * Kasubbag Dashboard
     */
    protected function kasubbagDashboard(string $filter = 'this_month'): View
    {
        $user = auth()->user();

        $stats = $this->dashboardService->getKasubbagStats($user->id, $filter);
        $pendingApprovals = $this->dashboardService->getPendingApprovalsForKasubbag($user->id);
        $flowMetrics = $this->dashboardService->getApprovalFlowMetrics($filter);
        $bottleneckAnalysis = $this->dashboardService->getBottleneckAnalysis();

        return view('dashboard.kasubbag', compact('user', 'stats', 'pendingApprovals', 'flowMetrics', 'bottleneckAnalysis', 'filter'));
    }

    /**
     * Wakil Dekan Dashboard
     */
    protected function wakilDekanDashboard(string $filter = 'this_month'): View
    {
        $user = auth()->user();

        $stats = $this->dashboardService->getWakilDekanStats($filter);
        $pendingApprovals = $this->dashboardService->getPendingApprovalsForWakilDekan($user->id);
        $prodiStats = $this->dashboardService->getStatsByProdi($filter);
        $trendData = $this->dashboardService->getTrendData();
        $insights = $this->dashboardService->getInsights();

        return view('dashboard.wakil-dekan', compact('user', 'stats', 'pendingApprovals', 'prodiStats', 'trendData', 'insights', 'filter'));
    }

    /**
     * Admin Dashboard
     */
    protected function adminDashboard(string $filter = 'this_month'): View
    {
        $user = auth()->user();
        $actionFilter = request()->get('action', null);

        $stats = $this->dashboardService->getAdminStats($filter);
        $systemHealth = $this->dashboardService->getSystemHealth();
        $userDistribution = $this->dashboardService->getUserDistribution();
        $activityLog = $this->dashboardService->getRecentActivityLog(20, $actionFilter);
        $usageData = $this->dashboardService->getSystemUsageData($filter);

        return view('dashboard.admin', compact('user', 'stats', 'systemHealth', 'userDistribution', 'activityLog', 'usageData', 'filter', 'actionFilter'));
    }

    /**
     * AJAX endpoint for real-time data updates
     */
    public function getData(Request $request): JsonResponse
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'this_month');

        if ($user->hasRole('Mahasiswa')) {
            $data = $this->dashboardService->getStudentStats($user->id, $filter);
            return response()->json([
                'total' => $data['summary']['total'],
                'in_progress' => $data['summary']['in_progress'],
                'completed' => $data['summary']['completed'],
                'rejected' => $data['summary']['rejected'],
            ]);
        }

        if ($user->hasRole('Staf Akademik')) {
            $data = $this->dashboardService->getStaffStats($user->id, $filter);
            return response()->json([
                'pending' => $data['summary']['pending'],
                'approved_today' => $data['summary']['approved_today'],
                'total_month' => $data['summary']['total_month'],
                'avg_time' => $data['summary']['avg_time'],
            ]);
        }

        if ($user->hasRole('Kepala Subbagian Akademik')) {
            $data = $this->dashboardService->getKasubbagStats($user->id, $filter);
            return response()->json([
                'pending' => $data['summary']['pending'],
                'approved_today' => $data['summary']['approved_today'],
                'total_month' => $data['summary']['total_month'],
                'success_rate' => $data['summary']['success_rate'],
            ]);
        }

        if ($user->hasRole('Wakil Dekan Bidang Akademik')) {
            $data = $this->dashboardService->getWakilDekanStats($filter);
            return response()->json([
                'pending_tte' => $data['summary']['pending_tte'],
                'tte_today' => $data['summary']['tte_today'],
                'total_month' => $data['summary']['total_month'],
                'ytd_total' => $data['summary']['ytd_total'],
                'growth' => $data['summary']['growth'],
                'tat' => $data['summary']['tat'],
            ]);
        }

        if ($user->hasRole('Administrator')) {
            $data = $this->dashboardService->getAdminStats($filter);
            $health = $this->dashboardService->getSystemHealth();
            return response()->json([
                'total_users' => $data['summary']['total_users'],
                'total_letters' => $data['summary']['total_letters'],
                'pending' => $data['summary']['pending'],
                'today' => $data['summary']['today'],
                'this_month' => $data['summary']['this_month'],
                'failed_jobs' => $health['failed_jobs'],
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
