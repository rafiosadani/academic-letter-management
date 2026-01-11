<?php

namespace App\Notifications;

use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Models\Approval;
use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalProcessedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Approval $approval;
    protected string $action; // 'approved' or 'rejected'
    protected ?string $note;

    /**
     * Create a new notification instance.
     */
    public function __construct(Approval $approval, string $action, ?string $note)
    {
        $this->approval = $approval;
        $this->action = $action;
        $this->note = $note;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $category = NotificationCategory::LETTER_APPROVAL->value;
        $settings = NotificationSetting::getOrCreate($notifiable->id, $category);

        $channels = [];

        if ($settings->shouldSendDatabase()) {
            $channels[] = 'database';
        }

        if ($settings->shouldSendImmediateEmail()) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->getTitle())
            ->greeting('Halo ' . $notifiable->profile->full_name . ',')
            ->line($this->getMessage());

        if ($this->note) {
            $mail->line('**Catatan:** ' . $this->note);
        }

        $mail->action('Lihat Detail Surat', route('letters.show', $this->approval->letterRequest));

        if ($this->action === 'approved') {
            $mail->line('Surat Anda sedang dalam proses persetujuan selanjutnya.');
        } else {
            $mail->line('Silakan periksa kembali dan ajukan surat baru jika diperlukan.');
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'action' => [
                'text' => 'Lihat Detail',
                'url' => route('letters.show', $this->approval->letterRequest),
                'method' => 'GET',
            ],
            'icon' => $this->action === 'approved' ? 'fa-circle-check' : 'fa-circle-xmark',
            'color' => $this->action === 'approved' ? 'success' : 'error',
            'category' => NotificationCategory::LETTER_APPROVAL->value,
            'priority' => $this->action === 'rejected' ? NotificationPriority::HIGH->value : NotificationPriority::NORMAL->value,
            'related_type' => 'LetterRequest',
            'related_id' => $this->approval->letterRequest->id,
            'metadata' => [
                'action' => $this->action,
                'step' => $this->approval->step,
                'step_label' => $this->approval->step_label,
                'note' => $this->note,
            ]
        ];
    }

    private function getTitle(): string
    {
        $stepLabel = $this->approval->step_label;

        if ($this->action === 'approved') {
            return "Surat Disetujui - {$stepLabel}";
        }

        return "Surat Ditolak - {$stepLabel}";
    }

    private function getMessage(): string
    {
        $letterType = $this->approval->letterRequest->letter_type->label();
        $stepLabel = $this->approval->step_label;
        $approverName = $this->approval->assigned_authority ?? 'Approver';

        if ($this->action === 'approved') {
            return "Pengajuan {$letterType} Anda telah disetujui oleh {$approverName} pada tahap {$stepLabel}.";
        }

        return "Pengajuan {$letterType} Anda ditolak oleh {$approverName} pada tahap {$stepLabel}.";
    }
}
