<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ErrorNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $errorData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $errorData)
    {
        $this->errorData = $errorData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $priority = $this->getErrorPriority();
        $appName = config('app.name', 'Advanced ITB');

        return new Envelope(
            subject: "🚨 [{$priority}] Erreur {$this->errorData['error_code']} sur {$appName}",
            tags: ['error', 'notification', 'code-' . $this->errorData['error_code']],
            metadata: [
                'error_type' => $this->errorData['error_type'],
                'environment' => $this->errorData['environment'],
                'timestamp' => $this->errorData['timestamp'],
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.error-notification',
            text: 'emails.error-notification-text',
            with: [
                'errorData' => $this->errorData,
                'priority' => $this->getErrorPriority(),
                'priorityColor' => $this->getPriorityColor(),
                'errorIcon' => $this->getErrorIcon(),
            ],
        );
    }

    /**
     * Déterminer la priorité de l'erreur
     */
    private function getErrorPriority(): string
    {
        $code = $this->errorData['error_code'];

        if ($code >= 500) {
            return 'CRITIQUE';
        } elseif ($code >= 400) {
            return 'ERREUR';
        } else {
            return 'AVERTISSEMENT';
        }
    }

    /**
     * Couleur selon la priorité
     */
    private function getPriorityColor(): string
    {
        $priority = $this->getErrorPriority();

        return match($priority) {
            'CRITIQUE' => '#dc3545',
            'ERREUR' => '#fd7e14',
            'AVERTISSEMENT' => '#ffc107',
            default => '#6c757d'
        };
    }

    /**
     * Icône selon le type d'erreur
     */
    private function getErrorIcon(): string
    {
        $code = $this->errorData['error_code'];

        return match(true) {
            $code >= 500 => '💥',
            $code == 404 => '🔍',
            $code == 403 => '🔒',
            $code == 401 => '🚪',
            $code == 419 => '⏰',
            $code == 429 => '🚦',
            default => '⚠️'
        };
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
