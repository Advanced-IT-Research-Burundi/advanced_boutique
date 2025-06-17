<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
            //  $this->sendErrorNotification($e);
        });
    }

     /**
     * Envoyer une notification d'erreur par email
     */
    private function sendErrorNotification(Throwable $exception): void
    {
        try {
            // Ne pas envoyer de notifications en environnement de test
            if (app()->environment('testing')) {
                return;
            }

            // Ne pas envoyer pour certains types d'erreurs (404, etc.)
            if ($this->shouldntReportError($exception)) {
                return;
            }

            $errorData = $this->prepareErrorData($exception);

            // Envoyer l'email
            Mail::to(config('mail.error_notification.to'))
                ->send(new ErrorNotificationMail($errorData));

        } catch (\Exception $e) {
            // Log l'erreur d'envoi d'email sans créer une boucle infinie
            Log::error('Erreur lors de l\'envoi de notification d\'erreur: ' . $e->getMessage());
        }
    }

    /**
     * Vérifier si on doit envoyer une notification pour cette erreur
     */
    private function shouldntReportError(Throwable $exception): bool
    {
        // Types d'erreurs à ignorer pour les notifications email
        $ignoredErrors = [
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class, // 404
            \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class, // 405
            \Illuminate\Auth\AuthenticationException::class, // 401
            \Illuminate\Validation\ValidationException::class, // Erreurs de validation
            \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException::class, // 429
        ];

        foreach ($ignoredErrors as $ignoredError) {
            if ($exception instanceof $ignoredError) {
                return true;
            }
        }

        return false;
    }

    /**
     * Préparer les données d'erreur pour l'email
     */
    private function prepareErrorData(Throwable $exception): array
    {
        $request = request();

        return [
            'timestamp' => now()->format('d/m/Y H:i:s'),
            'environment' => app()->environment(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'Invité',
            'error_type' => get_class($exception),
            'error_message' => $exception->getMessage(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'error_code' => method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500,
            'stack_trace' => $exception->getTraceAsString(),
            'request_data' => [
                'get' => $request->query->all(),
                'post' => $this->filterSensitiveData($request->request->all()),
                'headers' => $this->filterSensitiveHeaders($request->headers->all()),
            ],
            'session_data' => $this->filterSensitiveData($request->session()->all() ?? []),
        ];
    }

    /**
     * Filtrer les données sensibles
     */
    private function filterSensitiveData(array $data): array
    {
        $sensitiveKeys = [
            'password', 'password_confirmation', 'current_password',
            'token', '_token', 'api_key', 'secret', 'private_key',
            'credit_card', 'ssn', 'social_security'
        ];

        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = '***FILTRÉ***';
            }
        }

        return $data;
    }

    /**
     * Filtrer les en-têtes sensibles
     */
    private function filterSensitiveHeaders(array $headers): array
    {
        $sensitiveHeaders = [
            'authorization', 'cookie', 'x-api-key', 'x-auth-token'
        ];

        foreach ($sensitiveHeaders as $header) {
            if (isset($headers[$header])) {
                $headers[$header] = ['***FILTRÉ***'];
            }
        }

        return $headers;
    }
}
