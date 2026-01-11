<?php

namespace App\Http\Controllers\Master;

use App\Helpers\CodeGeneration;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StudyProgram\CreateStudyProgramRequest;
use App\Http\Requests\Master\StudyProgram\UpdateStudyProgramRequest;
use App\Models\StudyProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudyProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StudyProgram::orderBy('degree', 'asc')
            ->orderBy('name', 'asc');

        if ($request->has('view_deleted')) {
            $query->onlyTrashed();
        }

        $studyPrograms = $query->filter($request->only('search'))
            ->paginate(10)
            ->withQueryString();

        return view('master.study-programs.index', compact('studyPrograms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.study-programs.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateStudyProgramRequest $request)
    {
        $studyProgram = null;
        try {
            DB::transaction(function () use ($request, &$studyProgram) {
                $code = $this->generateUniqueCode($request['degree'], $request['name']);

                $studyProgram = StudyProgram::create([
                    'code' => $code,
                    'name' => $request->name,
                    'degree' => $request->degree,
                ]);
            });

            // LOG SUCCESS
            LogHelper::logSuccess('created', 'study program', [
                'study_program_id' => $studyProgram->id,
                'study_program_code' => $studyProgram->code,
                'study_program_name' => $studyProgram->name,
                'degree' => $studyProgram->degree,
            ], $request);

            return redirect()->route('master.study-programs.index')
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => "Program Studi {$studyProgram->name} berhasil ditambahkan!",
                    'position' => 'center-top',
                    'duration' => 4000
                ]);

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('create', 'study program', $e, [
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
    public function show(StudyProgram $studyProgram)
    {
        $statistics = $this->getStudyProgramStatistics($studyProgram);

        // Count users in this study program
        $userCount = $studyProgram->userProfiles()->count();

        return view('master.study-programs.show', compact('studyProgram', 'statistics', 'userCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudyProgram $studyProgram)
    {
        return view('master.study-programs.form', compact('studyProgram'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudyProgramRequest $request, StudyProgram $studyProgram)
    {
        $oldName = $studyProgram->name;
        $oldDegree = $studyProgram->degree;
        $newCode = $studyProgram->code;

        if ($studyProgram->degree !== $request->degree || $studyProgram->name !== $request->name) {
            $newCode = $this->generateUniqueCode($request->degree, $request->name);
        }

        try {
            DB::transaction(function () use ($request, $studyProgram, $newCode) {
                $studyProgram->update([
                    'code' => $newCode,
                    'name' => $request->name,
                    'degree' => $request->degree,
                ]);
            });

            // LOG SUCCESS
            LogHelper::logSuccess('updated', 'study program', [
                'study_program_id' => $studyProgram->id,
                'study_program_code' => $studyProgram->code,
                'old_name' => $oldName,
                'new_name' => $studyProgram->name,
                'old_degree' => $oldDegree,
                'new_degree' => $studyProgram->degree,
            ], $request);

            return redirect()->route('master.study-programs.index')
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => "Data Program Studi {$studyProgram->name} berhasil diperbarui!",
                    'position' => 'center-top',
                    'duration' => 4000
                ]);

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('update', 'study program', $e, [
                'study_program_id' => $studyProgram->id,
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
    public function destroy(StudyProgram $studyProgram)
    {
        $studyProgramName = $studyProgram->name;
        $studyProgramId = $studyProgram->id;

        try {
            DB::transaction(function () use ($studyProgram) {
                $studyProgram->delete();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('deleted', 'study program', [
                'study_program_id' => $studyProgramId,
                'study_program_name' => $studyProgramName,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Data Program Studi {$studyProgramName} berhasil dihapus.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('master.study-programs.index');

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('delete', 'study program', $e, [
                'study_program_id' => $studyProgramId,
            ]);

            return back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Gagal menghapus Program Studi. Mungkin Program Studi ini sedang digunakan oleh data lain.',
                'position' => 'center-top',
                'duration' => 6000
            ]);
        }
    }

    /**
     * Restore a soft deleted study program.
     */
    public function restore($id)
    {
        try {
            $studyProgram = StudyProgram::onlyTrashed()->findOrFail($id);
            $studyProgramName = $studyProgram->name;

            DB::transaction(function () use ($studyProgram) {
                $studyProgram->restore();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('restored', 'study program', [
                'study_program_id' => $studyProgram->id,
                'study_program_name' => $studyProgramName,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Program Studi {$studyProgramName} berhasil direstore.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->back();

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('restore', 'study program', $e, [
                'study_program_id' => $id,
            ]);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat restore program studi.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }

    /**
     * Restore all soft deleted study programs.
     */
    public function restoreAll()
    {
        try {
            $count = StudyProgram::onlyTrashed()->count();

            if ($count === 0) {
                session()->flash('notification_data', [
                    'type' => 'info',
                    'text' => 'Tidak ada program studi yang perlu direstore.',
                    'position' => 'center-top',
                    'duration' => 4000
                ]);

                return redirect()->back();
            }

            DB::transaction(function () {
                StudyProgram::onlyTrashed()->restore();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('restored all', 'study program', [
                'restored_count' => $count,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Berhasil restore {$count} program studi.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('master.study-programs.index');

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('restore all', 'study program', $e);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat restore semua program studi.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }

    /**
     * Permanently delete a soft deleted study program.
     */
    public function forceDelete($id)
    {
        try {
            $studyProgram = StudyProgram::onlyTrashed()->findOrFail($id);
            $studyProgramName = $studyProgram->name;
            $studyProgramId = $studyProgram->id;

            DB::transaction(function () use ($studyProgram) {
                $studyProgram->forceDelete();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('force deleted', 'study program', [
                'study_program_id' => $studyProgramId,
                'study_program_name' => $studyProgramName,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Program Studi {$studyProgramName} berhasil dihapus permanen.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->back();

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('force delete', 'study program', $e, [
                'study_program_id' => $id,
            ]);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat menghapus permanen program studi.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }

    private function generateUniqueCode(string $degree, string $name): string
    {
        // Get first 3 characters from name (alphanumeric only)
        $namePrefix = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($name, 0, 3)));
        $namePrefix = str_pad($namePrefix, 3, 'X'); // Pad with X if less than 3 chars

        // Get counter
        $counter = StudyProgram::withTrashed()
                ->where('code', 'like', "{$degree}-{$namePrefix}-%")
                ->count() + 1;

        return sprintf('%s-%s-%03d', $degree, $namePrefix, $counter);
    }

    protected function getStudyProgramStatistics(StudyProgram $studyProgram): array
    {
        $roleConfig = [
            'mahasiswa' => [
                'label' => 'Jumlah Mahasiswa',
                'icon' => 'fa-solid fa-users',
                'color' => 'text-primary',
                'always_show' => true,
                'display_type' => 'count',
            ],
            'ketua program studi' => [
                'label' => 'Ketua Program Studi',
                'icon' => 'fa-solid fa-user-tie',
                'color' => 'text-warning',
                'always_show' => false,
                'display_type' => 'name',
                'empty_text' => 'Belum ditunjuk',
            ],
            'dosen' => [
                'label' => 'Jumlah Dosen',
                'icon' => 'fa-solid fa-chalkboard-user',
                'color' => 'text-success',
                'always_show' => false,
                'display_type' => 'count',
            ],
        ];

        $statistics = [];

        foreach ($roleConfig as $roleName => $config) {
            $query = $studyProgram->userProfiles()
                ->with('user') // Eager load user
                ->whereHas('user', function ($q) use ($roleName) {
                    $q->role($roleName);
                });

            $count = $query->count();

            if ($config['display_type'] === 'name') {
                $users = $query->get()->pluck('user');

                if ($config['always_show'] || $users->isNotEmpty()) {
                    $statistics[] = [
                        'role' => $roleName,
                        'label' => $config['label'],
                        'icon' => $config['icon'],
                        'color' => $config['color'],
                        'display_type' => 'name',
                        'count' => $count,
                        'user' => $users->first(),
                        'empty_text' => $config['empty_text'] ?? 'Tidak ada',
                    ];
                }
            } else {
                if ($config['always_show'] || $count > 0) {
                    $statistics[] = [
                        'role' => $roleName,
                        'label' => $config['label'],
                        'icon' => $config['icon'],
                        'color' => $config['color'],
                        'display_type' => 'count',
                        'count' => $count,
                    ];
                }
            }
        }

        return $statistics;
    }
}