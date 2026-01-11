<?php

namespace App\Http\Controllers\Setting;

use App\Enums\ApprovalAction;
use App\Enums\LetterType;
use App\Enums\OfficialPosition;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\ApprovalFlow\CreateApprovalFlowRequest;
use App\Http\Requests\Setting\ApprovalFlow\UpdateApprovalFlowRequest;
use App\Models\ApprovalFlow;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalFlowController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ApprovalFlow::class);

        $letterTypeFilter = $request->input('letter_type');

        // Get filters
        $filters = $request->only(['letter_type']);

        // Get grouped flows using model method
        $groupedFlows = ApprovalFlow::getGroupedFlows($filters);

        // Get letter types for filter
        $letterTypes = LetterType::cases();

        return view('settings.approval-flows.index', compact(
            'groupedFlows',
            'letterTypes',
            'letterTypeFilter'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', ApprovalFlow::class);

        // Pre-select letter type if provided
        $selectedLetterType = $request->input('letter_type');
        $letterTypeValue = old('letter_type', $selectedLetterType);

        $letterTypeIsExternal = $letterTypeValue && LetterType::from($letterTypeValue)->isExternal();
        $letterTypeLabel = $letterTypeValue
            ? LetterType::from($letterTypeValue)->label()
            : null;

        // Get next step number for selected letter type
        $nextStep = $selectedLetterType
            ? ApprovalFlow::getNextStepNumber($selectedLetterType)
            : 1;

        $letterTypeReadonly = !empty($selectedLetterType);

        $letterTypes = LetterType::cases();
        $positions = OfficialPosition::cases();
        $rejectActions = ApprovalAction::cases();

        return view('settings.approval-flows.form', compact(
            'letterTypes',
            'positions',
            'rejectActions',
            'selectedLetterType',
            'nextStep',
            'letterTypeReadonly',
            'letterTypeLabel',
            'letterTypeIsExternal'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateApprovalFlowRequest $request)
    {
        $this->authorize('create', ApprovalFlow::class);

        $approvalFlow = null;

        try {
            DB::transaction(function () use ($request, &$approvalFlow) {
                $approvalFlow = ApprovalFlow::create([
                    'letter_type' => $request->letter_type,
                    'step' => $request->step,
                    'step_label' => $request->step_label,
                    'required_positions' => $request->required_positions,
                    'can_edit_content' => $request->boolean('can_edit_content'),
                    'is_editable' => $request->boolean('is_editable'),
                    'on_reject' => $request->on_reject,
                    'is_final' => $request->boolean('is_final'),
                ]);
            });

            $letterTypeLabel = $approvalFlow->letter_type->label();
            $displayName = "{$letterTypeLabel} - Step {$approvalFlow->step}";

            // LOG SUCCESS
            LogHelper::logSuccess('created', 'approval flow', [
                'approval_flow_id' => $approvalFlow->id,
                'letter_type' => $approvalFlow->letter_type->value,
                'step' => $approvalFlow->step,
                'step_label' => $approvalFlow->step_label,
                'required_positions' => $approvalFlow->required_positions,
            ], $request);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Step {$approvalFlow->step} untuk {$letterTypeLabel} berhasil ditambahkan!",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('settings.approval-flows.show', $approvalFlow);

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('create', 'approval flow', $e, [
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
    public function show(ApprovalFlow $approvalFlow)
    {
        $this->authorize('view', $approvalFlow);

        $allSteps = ApprovalFlow::getFlowForLetter($approvalFlow->letter_type);

        return view('settings.approval-flows.show', compact(
            'approvalFlow',
            'allSteps',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApprovalFlow $approvalFlow)
    {
        $this->authorize('update', $approvalFlow);

        $letterTypeValue = old('letter_type', $approvalFlow->letter_type->value);
        $letterTypeIsExternal = LetterType::from($letterTypeValue)->isExternal();
        $letterTypeLabel = LetterType::from($letterTypeValue)->label();

        $letterTypes = LetterType::cases();
        $positions = OfficialPosition::cases();
        $rejectActions = ApprovalAction::cases();

        return view('settings.approval-flows.form', compact(
            'approvalFlow',
            'letterTypes',
            'positions',
            'rejectActions',
            'letterTypeLabel',
            'letterTypeIsExternal'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApprovalFlowRequest $request, ApprovalFlow $approvalFlow)
    {
        $this->authorize('update', $approvalFlow);

        $oldStep = $approvalFlow->step;
        $oldLabel = $approvalFlow->step_label;

        try {
            DB::transaction(function () use ($request, $approvalFlow) {
                $approvalFlow->update([
                    'letter_type' => $request->letter_type,
                    'step' => $request->step,
                    'step_label' => $request->step_label,
                    'required_positions' => $request->required_positions,
                    'can_edit_content' => $request->boolean('can_edit_content'),
                    'is_editable' => $request->boolean('is_editable'),
                    'on_reject' => $request->on_reject,
                    'is_final' => $request->boolean('is_final'),
                ]);
            });

            $letterTypeLabel = $approvalFlow->letter_type->label();

            // LOG SUCCESS
            LogHelper::logSuccess('updated', 'approval flow', [
                'approval_flow_id' => $approvalFlow->id,
                'letter_type' => $approvalFlow->letter_type->value,
                'old_step' => $oldStep,
                'new_step' => $approvalFlow->step,
                'old_label' => $oldLabel,
                'new_label' => $approvalFlow->step_label,
            ], $request);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Step {$approvalFlow->step} untuk {$letterTypeLabel} berhasil diperbarui!",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('settings.approval-flows.show', $approvalFlow);

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('update', 'approval flow', $e, [
                'approval_flow_id' => $approvalFlow->id,
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
    public function destroy(ApprovalFlow $approvalFlow)
    {
        $this->authorize('delete', $approvalFlow);

        $letterTypeLabel = $approvalFlow->letter_type->label();
        $step = $approvalFlow->step;
        $stepLabel = $approvalFlow->step_label;
        $letterType = $approvalFlow->letter_type;
        $displayName = "{$letterTypeLabel} - Step {$step}: {$stepLabel}";

        try {
            DB::transaction(function () use ($approvalFlow, $letterType) {
                $approvalFlow->delete();

                // Reorder remaining steps
                ApprovalFlow::reorderSteps($letterType);
            });

            // LOG SUCCESS
            LogHelper::logSuccess('deleted', 'approval flow', [
                'approval_flow_id' => $approvalFlow->id,
                'letter_type' => $letterType->value,
                'step' => $step,
                'step_label' => $stepLabel,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "{$displayName} berhasil dihapus permanen dan step lainnya telah direorder.",
                'position' => 'center-top',
                'duration' => 5000
            ]);

            // Redirect to first flow of same letter type or index
            $firstFlow = ApprovalFlow::where('letter_type', $letterType)->ordered()->first();

            if ($firstFlow) {
                return redirect()->route('settings.approval-flows.show', $firstFlow);
            }

            return redirect()->route('settings.approval-flows.index');

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('delete', 'approval flow', $e, [
                'approval_flow_id' => $approvalFlow->id,
            ]);

            return back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Gagal menghapus step. Silakan coba lagi.',
                'position' => 'center-top',
                'duration' => 6000
            ]);
        }
    }
}
