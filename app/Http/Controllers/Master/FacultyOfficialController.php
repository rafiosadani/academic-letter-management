<?php

namespace App\Http\Controllers\Master;

use App\Enums\OfficialPosition;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\FacultyOfficial\CreateFacultyOfficialRequest;
use App\Http\Requests\Master\FacultyOfficial\UpdateFacultyOfficialRequest;
use App\Models\FacultyOfficial;
use App\Models\StudyProgram;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacultyOfficialController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', FacultyOfficial::class);

        $query = FacultyOfficial::with(['user.profile', 'studyProgram'])
            ->orderBy('start_date', 'desc');

        // Filter by deleted
        if ($request->has('view_deleted')) {
            $query->onlyTrashed();
        }

        // Filter by position
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Filter by status (active/ended)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'ended') {
                $query->ended();
            }
        }

        // Search by user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user.profile', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%");
            });
        }

        $facultyOfficials = $query->paginate(10)->withQueryString();

        // Get positions for filter
        $positions = OfficialPosition::cases();

        return view('master.faculty-officials.index', compact('facultyOfficials', 'positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', FacultyOfficial::class);

        $users = $this->getUsers();
        $positions = OfficialPosition::cases();
        $studyPrograms = $this->getStudyPrograms();

        return view('master.faculty-officials.form', compact('users', 'positions', 'studyPrograms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateFacultyOfficialRequest $request)
    {
        $this->authorize('create', FacultyOfficial::class);

        $facultyOfficial = null;

        try {
            DB::transaction(function () use ($request, &$facultyOfficial) {
                $facultyOfficial = FacultyOfficial::create([
                    'user_id' => $request->user_id,
                    'position' => $request->position,
                    'rank' => $request->rank,
                    'study_program_id' => $request->study_program_id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'notes' => $request->notes,
                ]);
            });

            // Get display info
            $userName = $facultyOfficial->user->profile->full_name ?? $facultyOfficial->user->email;
            $positionLabel = $facultyOfficial->position?->label();

            // LOG SUCCESS
            LogHelper::logSuccess('created', 'faculty official', [
                'faculty_official_id' => $facultyOfficial->id,
                'user_id' => $facultyOfficial->user_id,
                'user_name' => $userName,
                'position' => $facultyOfficial->position,
                'position_label' => $positionLabel,
                'study_program_id' => $facultyOfficial->study_program_id,
                'start_date' => $facultyOfficial->start_date->format('Y-m-d'),
                'end_date' => $facultyOfficial->end_date?->format('Y-m-d'),
            ], $request);

            return redirect()->route('master.faculty-officials.index')
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => "Penugasan jabatan untuk {$userName} berhasil ditambahkan!",
                    'position' => 'center-top',
                    'duration' => 4000
                ]);

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('create', 'faculty official', $e, [
                'request_data' => $request->except(['_token'])
            ], $request);

            return redirect()->back()->withInput()
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                    'position' => 'center-top',
                    'duration' => 6000,
                ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FacultyOfficial $facultyOfficial)
    {
        $this->authorize('view', $facultyOfficial);

        $facultyOfficial->load(['user.profile', 'studyProgram']);

        // Get all assignments for this user (for timeline)
        $userAssignments = FacultyOfficial::where('user_id', $facultyOfficial->user_id)
            ->with(['studyProgram'])
            ->orderBy('start_date', 'desc')
            ->get();

        return view('master.faculty-officials.show', compact('facultyOfficial', 'userAssignments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FacultyOfficial $facultyOfficial)
    {
        $this->authorize('update', $facultyOfficial);

        $facultyOfficial->load(['user.profile', 'studyProgram']);

        $users = $this->getUsers();
        $positions = OfficialPosition::cases();
        $studyPrograms = $this->getStudyPrograms();

        return view('master.faculty-officials.form', compact('facultyOfficial', 'users', 'positions', 'studyPrograms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFacultyOfficialRequest $request, FacultyOfficial $facultyOfficial)
    {
        $this->authorize('update', $facultyOfficial);

        $oldPosition = $facultyOfficial->position;
        $oldRank = $facultyOfficial->rank;
        $oldStartDate = $facultyOfficial->start_date->format('Y-m-d');
        $oldEndDate = $facultyOfficial->end_date?->format('Y-m-d');

        try {
            DB::transaction(function () use ($request, $facultyOfficial) {
                $facultyOfficial->update([
                    'user_id' => $request->user_id,
                    'position' => $request->position,
                    'rank' => $request->rank,
                    'study_program_id' => $request->study_program_id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'notes' => $request->notes,
                ]);
            });

            // Get display info
            $userName = $facultyOfficial->user->profile->full_name ?? $facultyOfficial->user->email;
            $positionLabel = $facultyOfficial->position?->label();

            // LOG SUCCESS
            LogHelper::logSuccess('updated', 'faculty official', [
                'faculty_official_id' => $facultyOfficial->id,
                'user_id' => $facultyOfficial->user_id,
                'user_name' => $userName,
                'old_position' => $oldPosition,
                'new_position' => $facultyOfficial->position,
                'old_rank' => $oldRank,
                'new_rank' => $facultyOfficial->rank,
                'old_start_date' => $oldStartDate,
                'new_start_date' => $facultyOfficial->start_date->format('Y-m-d'),
                'old_end_date' => $oldEndDate,
                'new_end_date' => $facultyOfficial->end_date?->format('Y-m-d'),
            ], $request);

            return redirect()->route('master.faculty-officials.index')
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => "Data penugasan jabatan {$userName} berhasil diperbarui!",
                    'position' => 'center-top',
                    'duration' => 4000
                ]);

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('update', 'faculty official', $e, [
                'faculty_official_id' => $facultyOfficial->id,
                'request_data' => $request->except(['_token'])
            ], $request);

            return redirect()->back()->withInput()
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                    'position' => 'center-top',
                    'duration' => 6000,
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FacultyOfficial $facultyOfficial)
    {
        $this->authorize('delete', $facultyOfficial);

        $userName = $facultyOfficial->user->profile->full_name ?? $facultyOfficial->user->email;
        $positionLabel = $facultyOfficial->position->label();
        $displayName = "{$userName} - {$positionLabel}";

        try {
            DB::transaction(function () use ($facultyOfficial) {
                $facultyOfficial->delete();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('deleted', 'faculty official', [
                'faculty_official_id' => $facultyOfficial->id,
                'user_id' => $facultyOfficial->user_id,
                'user_name' => $userName,
                'position' => $facultyOfficial->position,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Penugasan jabatan {$displayName} berhasil dihapus.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('master.faculty-officials.index');

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('delete', 'faculty official', $e, [
                'faculty_official_id' => $facultyOfficial->id,
            ]);

            return back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Gagal menghapus penugasan jabatan. Silakan coba lagi.',
                'position' => 'center-top',
                'duration' => 6000
            ]);
        }
    }

    /**
     * Restore a soft deleted faculty official.
     */
    public function restore($id)
    {
        $facultyOfficial = FacultyOfficial::onlyTrashed()->with('user.profile')->findOrFail($id);
        $this->authorize('restore', $facultyOfficial);

        try {
            $userName = $facultyOfficial->user->profile->full_name ?? $facultyOfficial->user->email;
            $positionLabel = $facultyOfficial->position?->label();

            DB::transaction(function () use ($facultyOfficial) {
                $facultyOfficial->restore();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('restored', 'faculty official', [
                'faculty_official_id' => $facultyOfficial->id,
                'user_id' => $facultyOfficial->user_id,
                'user_name' => $userName,
                'position' => $facultyOfficial->position,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Penugasan jabatan {$userName} - {$positionLabel} berhasil direstore.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->back();

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('restore', 'faculty official', $e, [
                'faculty_official_id' => $id,
            ]);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat restore penugasan jabatan.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }

    /**
     * Restore all soft deleted faculty officials.
     */
    public function restoreAll()
    {
        // Check permission manually (no specific model)
        if (!auth()->user()->can('restore', FacultyOfficial::class)) {
            abort(403);
        }

        try {
            $count = FacultyOfficial::onlyTrashed()->count();

            if ($count === 0) {
                session()->flash('notification_data', [
                    'type' => 'info',
                    'text' => 'Tidak ada penugasan jabatan yang perlu direstore.',
                    'position' => 'center-top',
                    'duration' => 4000
                ]);

                return redirect()->back();
            }

            DB::transaction(function () {
                FacultyOfficial::onlyTrashed()->restore();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('restored all', 'faculty official', [
                'restored_count' => $count,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Berhasil restore {$count} penugasan jabatan.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('master.faculty-officials.index');

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('restore all', 'faculty official', $e);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat restore semua penugasan jabatan.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }

    /**
     * Permanently delete a soft deleted faculty official.
     */
    public function forceDelete($id)
    {
        $facultyOfficial = FacultyOfficial::onlyTrashed()->with('user.profile')->findOrFail($id);
        $this->authorize('forceDelete', $facultyOfficial);

        try {
            $userName = $facultyOfficial->user->profile->full_name ?? $facultyOfficial->user->email;
            $positionLabel = $facultyOfficial->position?->label();
            $facultyOfficialId = $facultyOfficial->id;

            DB::transaction(function () use ($facultyOfficial) {
                $facultyOfficial->forceDelete();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('force deleted', 'faculty official', [
                'faculty_official_id' => $facultyOfficialId,
                'user_name' => $userName,
                'position' => $facultyOfficial->position,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Penugasan jabatan {$userName} - {$positionLabel} berhasil dihapus permanen.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->back();

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('force delete', 'faculty official', $e, [
                'faculty_official_id' => $id,
            ]);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat menghapus permanen penugasan jabatan.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }

    /**
     * Get users for dropdown
     */
    private function getUsers()
    {
        return User::withoutTrashed()
            ->with(['profile', 'roles'])
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['mahasiswa', 'administrator']);
            })
            ->orderBy(
                UserProfile::select('full_name')
                    ->whereColumn('user_profiles.user_id', 'users.id')
            )
            ->get()
            ->mapWithKeys(function ($user) {
                $name = $user->profile->full_name ?? $user->email;
                return [$user->id => $name];
            });
    }

    /**
     * Get study programs for dropdown
     */
    private function getStudyPrograms()
    {
        return StudyProgram::withoutTrashed()
            ->select('id', 'degree', 'name')
            ->orderBy('degree')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($program) {
                return [$program->id => $program->degree_name];
            });
    }
}
