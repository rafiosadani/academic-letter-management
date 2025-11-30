<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class LogHelper
{
    /**
     * Log successful operation (create, update, delete)
     *
     * Contoh penggunaan:
     * LogHelper::logSuccess('created', 'role', ['role_id' => 1, 'role_name' => 'Admin'], $request);
     *
     * @param string $action - Nama action: 'created', 'updated', 'deleted'
     * @param string $entity - Nama entity: 'role', 'user', 'permission', dll
     * @param array $data - Data yang ingin dicatat
     * @param Request|null $request - Request object (opsional)
     * @return void
     */
    public static function logSuccess(string $action, string $entity, array $data, ?Request $request = null)
    {
        $context = self::buildContext($data, $request);

        $message = ucfirst($entity) . " {$action} successfully";

        Log::info($message, $context);
    }

    /**
     * Log error/exception
     *
     * Contoh penggunaan:
     * LogHelper::logError('create', 'role', $exception, ['request_data' => [...]], $request);
     *
     * @param string $action - Nama action: 'create', 'update', 'delete'
     * @param string $entity - Nama entity: 'role', 'user', 'permission', dll
     * @param \Exception $exception - Exception object
     * @param array $additionalData - Data tambahan untuk context
     * @param Request|null $request - Request object (opsional)
     * @return void
     */
    public static function logError(string $action, string $entity, \Exception $exception, array $additionalData = [], ?Request $request = null)
    {
        $context = array_merge(
            [
                'error_message' => $exception->getMessage(),
                'error_code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ],
            $additionalData,
            self::buildContext([], $request)
        );

        $message = "Failed to {$action} " . strtolower($entity);

        Log::error($message, $context);
    }

    /**
     * Log warning (unauthorized attempt, validation issue, etc)
     *
     * Contoh penggunaan:
     * LogHelper::logWarning('Attempt to edit non-editable role', ['role_id' => 1], $request);
     *
     * @param string $message - Warning message
     * @param array $data - Data context
     * @param Request|null $request - Request object (opsional)
     * @return void
     */
    public static function logWarning(string $message, array $data = [], ?Request $request = null)
    {
        $context = self::buildContext($data, $request);

        Log::warning($message, $context);
    }

    /**
     * Log general information
     *
     * Contoh penggunaan:
     * LogHelper::logInfo('User logged in', ['user_id' => 1], $request);
     *
     * @param string $message - Info message
     * @param array $data - Data context
     * @param Request|null $request - Request object (opsional)
     * @return void
     */
    public static function logInfo(string $message, array $data = [], ?Request $request = null)
    {
        $context = self::buildContext($data, $request);

        Log::info($message, $context);
    }

    /**
     * Build context array dengan informasi request dan user
     * Fungsi private untuk membangun data context yang konsisten
     *
     * @param array $data - Data tambahan
     * @param Request|null $request - Request object
     * @return array
     */
    private static function buildContext(array $data = [], ?Request $request = null): array
    {
        $context = $data;

        // Tambahkan informasi user jika sedang login
        if (auth()->check()) {
            $context['user_id'] = auth()->id();
            $context['user_name'] = auth()->user()->name ?? null;
            $context['user_email'] = auth()->user()->email ?? null;
        }

        // Tambahkan informasi request jika ada
        if ($request) {
            $context['ip_address'] = $request->ip();
            $context['user_agent'] = $request->userAgent();
            $context['url'] = $request->fullUrl();
            $context['method'] = $request->method();
        } else {
            // Fallback jika request tidak dikirim, ambil dari global request()
            $context['ip_address'] = request()->ip();
            $context['user_agent'] = request()->userAgent();
            $context['url'] = request()->fullUrl();
            $context['method'] = request()->method();
        }

        // Tambahkan timestamp
        $context['timestamp'] = now()->toDateTimeString();

        return $context;
    }
}