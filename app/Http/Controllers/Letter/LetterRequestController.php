<?php

namespace App\Http\Controllers\Letter;

use App\Enums\LetterType;
use App\Http\Controllers\Controller;
use App\Models\LetterRequest;
use App\Services\LetterRequestService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LetterRequestController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected LetterRequestService $letterRequestService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', LetterRequest::class);

        $status = $request->query('status');

        $letters = $this->letterRequestService->getStudentLetters(
            auth()->user(),
            $status
        );

        return view('letters.index', compact('letters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $this->authorize('create', LetterRequest::class);

        $letterTypeValue = $request->query('type');

        if (!$letterTypeValue) {
            return view('letters.select-type', [
                'letterTypes' => LetterType::cases(),
            ]);
        }

        $letterType = LetterType::tryFrom($letterTypeValue);

        if (!$letterType) {
            return redirect()->route('letters.create')
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Jenis surat tidak valid',
                    'position' => 'center-top',
                    'duration' => 3000,
                ]);
        }

        // Check if student can submit this letter type
        $canSubmit = $this->letterRequestService->canSubmitLetterType(auth()->user(), $letterType);

        if (!$canSubmit['can_submit']) {
            return redirect()->route('letters.create')
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => $canSubmit['reason'],
                    'position' => 'center-top',
                    'duration' => 4000,
                ])
                ->with('alert_data', [
                    'type' => 'error',
                    'title' => 'Data Belum Lengkap',
                    'message' => $canSubmit['reason'],
                    'missing_fields' => $canSubmit['missing_fields'],
                ])
                ->with('alert_show_id', 'alert-profile-incomplete');
        }

        $formFields = $letterType->formFields();
        $requiredDocuments = $letterType->requiredDocuments();

        return view('letters.form', compact('letterType', 'formFields', 'requiredDocuments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LetterRequest $letterRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LetterRequest $letterRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LetterRequest $letterRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LetterRequest $letterRequest)
    {
        //
    }
}
