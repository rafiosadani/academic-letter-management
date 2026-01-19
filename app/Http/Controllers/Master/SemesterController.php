<?php

namespace App\Http\Controllers\Master;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use App\Notifications\SemesterActivated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Semester::with(['academicYear'])
            ->orderBy('academic_year_id', 'desc')
            ->orderByRaw("FIELD(semester_type, 'Genap', 'Ganjil')");

        $semesters = $query->filter($request->only('search'))
            ->paginate(10)
            ->withQueryString();

        return view('master.semesters.index', compact('semesters'));
    }

    /**
     * Toggle active status of semester
     */
    public function toggleActive(Semester $semester)
    {
        $oldStatus = $semester->is_active;
        $semesterLabel = $semester->full_label;

        try {
            DB::transaction(function () use ($semester) {
                // Set all semesters to inactive
                Semester::where('is_active', 1)->update(['is_active' => 0]);

                // Set all academic years to inactive
                AcademicYear::where('is_active', 1)->update(['is_active' => 0]);

                // Activate selected semester
                $semester->update(['is_active' => 1]);

                // Activate parent academic year
                $semester->academicYear()->update(['is_active' => 1]);
            });

            // Send notification to admins
            $admins = User::role(['administrator', 'kepala subbagian akademik'])->get();
            Notification::send($admins, new SemesterActivated($semester));

            // LOG SUCCESS
            LogHelper::logSuccess('toggled active', 'semester', [
                'semester_id' => $semester->id,
                'semester_label' => $semesterLabel,
                'old_status' => $oldStatus,
                'new_status' => $semester->is_active,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Semester {$semesterLabel} berhasil diaktifkan!",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->back();

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('toggle active', 'semester', $e, [
                'semester_id' => $semester->id,
            ]);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat mengaktifkan semester.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }
}
