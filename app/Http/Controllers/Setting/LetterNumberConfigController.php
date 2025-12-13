<?php

namespace App\Http\Controllers\Setting;

use App\Enums\LetterType;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\LetterNumberConfig\CreateLetterNumberConfigRequest;
use App\Http\Requests\Setting\LetterNumberConfig\UpdateLetterNumberConfigRequest;
use App\Models\LetterNumberConfig;
use App\Services\LetterNumberService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class LetterNumberConfigController extends Controller
{
    use AuthorizesRequests;

    protected LetterNumberService $letterNumberService;

    public function __construct(LetterNumberService $letterNumberService)
    {
        $this->letterNumberService = $letterNumberService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', LetterNumberConfig::class);

        // get all configs
        $configs = LetterNumberConfig::orderBy('letter_type')->get();

        // get current sequence for each type
        $sequences = [];
        foreach ($configs as $config) {
            $sequences[$config->letter_type->value] = $this->letterNumberService->getCurrentSequence($config->letter_type);
        }

        return view('settings.letter-number-configs.index', compact('configs', 'sequences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', LetterNumberConfig::class);

        // get letter types that need numbering (PDF type only)
        $letterTypes = collect(LetterType::cases())->filter(fn($type) => $type->needsAutoNumbering());

        // Get existing configs to exclude
        $existingTypes = LetterNumberConfig::pluck('letter_type')->map(fn($type) => $type->value)->toArray();
        $letterTypes = $letterTypes->filter(fn($type) => !in_array($type->value, $existingTypes));

        return view('settings.letter-number-configs.form', compact('letterTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateLetterNumberConfigRequest $request)
    {
        $this->authorize('create', LetterNumberConfig::class);

        $config = null;
        try {
            DB::transaction(function () use ($request) {
                $config = LetterNumberConfig::create($request->validated());

                // LOG SUCCESS
                LogHelper::logSuccess('created', 'letter number config', [
                    'config_id' => $config->id,
                    'letter_type' => $config->letter_type->value,
                    'prefix' => $config->prefix,
                    'code' => $config->code,
                ], $request);
            });

            $displayName = "{$config->letter_type->label()} ({$config->code})";

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Konfigurasi {$displayName} berhasil ditambahkan",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('settings.letter-number-configs.index');

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('create', 'letter number config', $e, [
                'request_data' => $request->except(['_token'])
            ], $request);

            return redirect()->back()->withInput()
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Gagal menambahkan konfigurasi. Silakan coba lagi.',
                    'position' => 'center-top',
                    'duration' => 6000,
                ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LetterNumberConfig $letterNumberConfig)
    {
        $this->authorize('update', $letterNumberConfig);

        $letterTypes = collect(LetterType::cases())->filter(fn($type) => $type->needsAutoNumbering());

        return view('settings.letter-number-configs.form', compact('letterNumberConfig', 'letterTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLetterNumberConfigRequest $request, LetterNumberConfig $letterNumberConfig)
    {
        $this->authorize('update', $letterNumberConfig);

        try {
            $oldData = $letterNumberConfig->toArray();

            DB::transaction(function () use ($request, $letterNumberConfig, $oldData) {
                $letterNumberConfig->update($request->validated());

                // LOG SUCCESS
                LogHelper::logSuccess('updated', 'letter number config', [
                    'config_id' => $letterNumberConfig->id,
                    'letter_type' => $letterNumberConfig->letter_type->value,
                    'old_data' => $oldData,
                    'new_data' => $letterNumberConfig->fresh()->toArray(),
                ], $request);
            });

            $displayName = "{$letterNumberConfig->letter_type->label()} ({$letterNumberConfig->code})";

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Konfigurasi {$displayName} berhasil diperbarui",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('settings.letter-number-configs.index');

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('update', 'letter number config', $e, [
                'config_id' => $letterNumberConfig->id,
                'request_data' => $request->except(['_token', '_method'])
            ], $request);

            return redirect()->back()->withInput()
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Gagal memperbarui konfigurasi. Silakan coba lagi.',
                    'position' => 'center-top',
                    'duration' => 6000,
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LetterNumberConfig $letterNumberConfig)
    {
        $this->authorize('delete', $letterNumberConfig);

        $displayName = "{$letterNumberConfig->letter_type->label()} ({$letterNumberConfig->code})";

        try {
            DB::transaction(function () use ($letterNumberConfig, $displayName) {
                $configId = $letterNumberConfig->id;
                $letterType = $letterNumberConfig->letter_type->value;

                $letterNumberConfig->delete();

                // LOG SUCCESS
                LogHelper::logSuccess('deleted', 'letter number config', [
                    'config_id' => $configId,
                    'letter_type' => $letterType,
                    'display_name' => $displayName,
                ]);
            });

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Konfigurasi {$displayName} berhasil dihapus",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('settings.letter-number-configs.index');
        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('delete', 'letter number config', $e, [
                'config_id' => $letterNumberConfig->id,
            ]);

            return back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Gagal menghapus konfigurasi. Silakan coba lagi.',
                'position' => 'center-top',
                'duration' => 6000
            ]);
        }
    }
}
