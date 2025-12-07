<?php

namespace App\Http\Controllers\Master;

use App\Enums\SemesterType;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\AcademicYear\CreateAcademicYearRequest;
use App\Http\Requests\Master\AcademicYear\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use App\Notifications\AcademicYearCreated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AcademicYear::orderBy('year_label', 'desc');

        $academicYears = $query->filter($request->only('search'))
            ->paginate(10)
            ->withQueryString();

        return view('master.academic-years.index', compact('academicYears'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.academic-years.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAcademicYearRequest $request)
    {
        $academicYear = null;
        try {
            DB::transaction(function () use ($request, &$academicYear) {
                // Buat semua academic years ke inactive, jika nilanya is active
                if ($request->is_active) {
                    AcademicYear::where('is_active', 1)->update(['is_active' => 0]);
                    Semester::where('is_active', 1)->update(['is_active' => 0]);
                }

                $code = 'TA-' . $request->year_label;

                // Create academic year
                $academicYear = AcademicYear::create([
                    'code' => $code,
                    'year_label' => $request->year_label,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'is_active' => $request->is_active ?? 0,
                ]);

                // Auto-generate 2 semesters
                $this->generateSemesters($academicYear, $request->is_active ?? 0);
            });

            // Send notification to admins
            $admins = User::role(['administrator', 'kepala subbagian akademik'])->get();
            Notification::send($admins, new AcademicYearCreated($academicYear));

            // LOG SUCCESS
            LogHelper::logSuccess('created', 'academic year', [
                'academic_year_id' => $academicYear->id,
                'year_label' => $academicYear->year_label,
            ], $request);

            return redirect()->route('master.academic-years.index')
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => "Tahun Akademik {$academicYear->year_label} berhasil ditambahkan!",
                    'position' => 'center-top',
                    'duration' => 4000
                ]);

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('create', 'academic year', $e, [
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
    public function show(AcademicYear $academicYear)
    {
        $academicYear->load(['semesters']);

        return view('master.academic-years.show', compact('academicYear'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $academicYear)
    {
        return view('master.academic-years.form', compact('academicYear'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear)
    {
        $oldYearLabel = $academicYear->year_label;
        $oldIsActive = $academicYear->is_active;

        try {
            DB::transaction(function () use ($request, $academicYear) {
                // Buat semua academic years ke inactive, jika nilanya is active
                if ($request->is_active && !$academicYear->is_active) {
                    AcademicYear::where('id', '!=', $academicYear->id)
                        ->where('is_active', 1)
                        ->update(['is_active' => 0]);

                    Semester::where('is_active', 1)->update(['is_active' => 0]);
                }

                $code = 'TA-' . $request->year_label;

                $academicYear->update([
                    'code' => $code,
                    'year_label' => $request->year_label,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'is_active' => $request->is_active ?? 0,
                ]);

                // Update semester dates if academic year dates changed
                $this->updateSemesterDates($academicYear);
            });

            // LOG SUCCESS
            LogHelper::logSuccess('updated', 'academic year', [
                'academic_year_id' => $academicYear->id,
                'old_year_label' => $oldYearLabel,
                'new_year_label' => $academicYear->year_label,
                'old_is_active' => $oldIsActive,
                'new_is_active' => $academicYear->is_active,
            ], $request);

            return redirect()->route('master.academic-years.index')
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => "Tahun Akademik {$academicYear->year_label} berhasil diperbarui!",
                    'position' => 'center-top',
                    'duration' => 4000
                ]);

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('update', 'academic year', $e, [
                'academic_year_id' => $academicYear->id,
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
    public function destroy(AcademicYear $academicYear)
    {
        $yearLabel = $academicYear->year_label;
        $academicYearId = $academicYear->id;

        try {
            DB::transaction(function () use ($academicYear) {
                // Delete semesters first (cascade)
                $academicYear->semesters()->delete();

                // Delete academic year
                $academicYear->delete();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('deleted', 'academic year', [
                'academic_year_id' => $academicYearId,
                'year_label' => $yearLabel,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Tahun Akademik {$yearLabel} berhasil dihapus.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('master.academic-years.index');

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('delete', 'academic year', $e, [
                'academic_year_id' => $academicYearId,
            ]);

            return back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Gagal menghapus Tahun Akademik. Data ini mungkin sedang digunakan.',
                'position' => 'center-top',
                'duration' => 6000
            ]);
        }
    }

    // ==========================================================
    // HELPER METHODS
    // ==========================================================

    /**
     * Generate 2 semesters for academic year
     */
    private function generateSemesters(AcademicYear $academicYear, bool $setActiveGanjil = false)
    {
        $startDate = Carbon::parse($academicYear->start_date);
        $endDate = Carbon::parse($academicYear->end_date);
        $midDate = $startDate->copy()->addMonths(6);

        $semesters = [
            [
                'code' => 'TA-' . $academicYear->year_label . '-' . SemesterType::GANJIL->shortCode(),
                'academic_year_id' => $academicYear->id,
                'semester_type' => SemesterType::GANJIL,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $midDate->copy()->subDay()->format('Y-m-d'),
                'is_active' => $setActiveGanjil ? 1 : 0,
            ],
            [
                'code' => 'TA-' . $academicYear->year_label . '-' . SemesterType::GENAP->shortCode(),
                'academic_year_id' => $academicYear->id,
                'semester_type' => SemesterType::GENAP,
                'start_date' => $midDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'is_active' => 0,
            ]
        ];

        foreach ($semesters as $semesterData) {
            Semester::create($semesterData);
        }
    }

    /**
     * Update semester dates when academic year dates change
     */
    private function updateSemesterDates(AcademicYear $academicYear)
    {
        $startDate = Carbon::parse($academicYear->start_date);
        $endDate = Carbon::parse($academicYear->end_date);
        $midDate = $startDate->copy()->addMonths(6);

        $ganjil = $academicYear->semesters()->where('semester_type', SemesterType::GANJIL)->first();
        $genap = $academicYear->semesters()->where('semester_type', SemesterType::GENAP)->first();

        if ($ganjil) {
            $ganjil->update([
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $midDate->copy()->subDay()->format('Y-m-d'),
            ]);
        }

        if ($genap) {
            $genap->update([
                'start_date' => $midDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]);
        }
    }
}
