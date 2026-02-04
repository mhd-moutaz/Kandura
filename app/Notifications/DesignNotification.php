<?php

namespace App\Notifications;

use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class DesignNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The design instance.
     *
     * @var \App\Models\Design
     */
    protected $design;

    /**
     * The notification type.
     *
     * @var string
     */
    protected $type;

    /**
     * Additional data.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Design  $design
     * @param  string  $type
     * @param  array  $data
     * @return void
     */
    public function __construct(Design $design, string $type, array $data = [])
    {
        $this->design = $design;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->greeting('Hello!')
            ->line($this->getMessage())
            ->action('View Design', url('/designs/' . $this->design->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'design_id' => $this->design->id,
            'design_name' => $this->design->name,
            'message' => $this->getMessage(),
            'url' => '/designs/' . $this->design->id,
        ];
    }

    /**
     * Get the FCM representation of the notification.
     *
     * @return FcmMessage
     */
    public function toFcm(object $notifiable): FcmMessage
    {
        $designName = is_array($this->design->name)
            ? ($this->design->name['en'] ?? 'New Design')
            : $this->design->name;

        return (new FcmMessage(notification: new FcmNotification(
            title: $this->getSubject(),
            body: $this->getMessage(),
            image: null
        )))
            ->data([
                'type' => $this->type,
                'design_id' => (string) $this->design->id,
                'url' => '/designs/' . $this->design->id,
            ])
            ->custom([
                'android' => [
                    'notification' => [
                        'color' => '#4CAF50',
                        'sound' => 'default',
                        'click_action' => url('/designs/' . $this->design->id),
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default'
                        ],
                    ],
                ],
                'webpush' => [
                    'notification' => [
                        'icon' => '/favicon.ico',
                        'badge' => '/favicon.ico',
                    ],
                    'fcm_options' => [
                        'link' => url('/designs/' . $this->design->id),
                    ],
                ],
            ]);
    }

    /**
     * Get the notification subject.
     *
     * @return string
     */
    protected function getSubject(): string
    {
        return match ($this->type) {
            'design_created' => 'New Design Created',
            default => 'Design Notification',
        };
    }

    /**
     * Get the notification message.
     *
     * @return string
     */
    protected function getMessage(): string
    {
        $designName = is_array($this->design->name)
            ? ($this->design->name['en'] ?? 'New Design')
            : ($this->design->name ?? 'New Design');

        return match ($this->type) {
            'design_created' => 'A new design "' . $designName . '" has been created.',
            default => 'You have a new design notification.',
        };
    }
}
