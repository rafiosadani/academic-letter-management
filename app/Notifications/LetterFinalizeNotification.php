<?php

namespace App\Notifications;

use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Models\LetterRequest;
use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LetterFinalizeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected LetterRequest $letterRequest;
    protected $letterNumber;
    protected $downloadUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(LetterRequest $letterRequest, $letterNumber, $downloadUrl)
    {
        $this->letterRequest = $letterRequest;
        $this->letterNumber = $letterNumber;
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $category = NotificationCategory::LETTER_STATUS->value;
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
        $letterType = $this->letterRequest->letter_type->label();
        $urlDetail = route('letters.show', $this->letterRequest);

        return (new MailMessage)
            ->subject('Surat Anda Telah Selesai Diproses')
            ->greeting('Halo ' . $notifiable->profile->full_name . ',')
            ->line($this->getMessage())
            ->line('**Detail Surat:**')
            ->line('- Jenis Surat: ' . $letterType)
            ->line('- Nomor: ' . $this->letterNumber)
            ->line('- Status: Sudah Final')
            ->action('Download Surat', $urlDetail)
            ->line('Surat dapat didownload melalui link di atas atau melalui halaman detail surat.')
            ->line('Terima kasih telah menggunakan sistem kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Surat Selesai Diproses',
            'message' => $this->getMessage(),
            'action' => [
                'text' => 'Lihat & Donwload',
                'url' => route('letters.show', $this->letterRequest),
                'method' => 'GET',
            ],
            'icon' => 'fa-file-arrow-down',
            'color' => 'success',
            'category' => NotificationCategory::LETTER_STATUS->value,
            'priority' => NotificationPriority::HIGH->value,
            'related_type' => 'LetterRequest',
            'related_id' => $this->letterRequest->id,
            'metadata' => [
                'letter_number' => $this->letterNumber,
                'letter_type' => $this->letterRequest->letter_type->value,
            ]
        ];
    }

    private function getMessage(): string
    {
        $letterType = $this->letterRequest->letter_type->label();

        return "Pengajuan {$letterType} Anda telah selesai diproses dan disetujui. Nomor surat: {$this->letterNumber}. Surat sudah dapat didownload.";
    }
}
