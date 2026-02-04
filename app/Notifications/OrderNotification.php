<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class OrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    protected $order;

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
     * @param  \App\Models\Order  $order
     * @param  string  $type
     * @param  array  $data
     * @return void
     */
    public function __construct(Order $order, string $type, array $data = [])
    {
        $this->order = $order;
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
        // For admins: database + FCM
        // For design creators: database only
        if ($notifiable->hasAnyRole(['admin', 'super_admin'])) {
            return ['database', FcmChannel::class];
        }
        
        return ['database'];
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
            ->action('View Order', url('/orders/' . $this->order->id))
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
            'order_id' => $this->order->id,
            'order_status' => $this->order->status,
            'order_total' => $this->order->total,
            'message' => $this->getMessage(),
            'url' => '/orders/' . $this->order->id,
            'old_status' => $this->data['old_status'] ?? null,
            'new_status' => $this->data['new_status'] ?? null,
        ];
    }

    /**
     * Get the FCM representation of the notification.
     *
     * @return FcmMessage
     */
    public function toFcm(object $notifiable): FcmMessage
    {
        return (new FcmMessage(notification: new FcmNotification(
            title: $this->getSubject(),
            body: $this->getMessage(),
            image: null
        )))
            ->data([
                'type' => $this->type,
                'order_id' => (string) $this->order->id,
                'order_status' => $this->order->status,
                'url' => '/orders/' . $this->order->id,
            ])
            ->custom([
                'android' => [
                    'notification' => [
                        'color' => '#4CAF50',
                        'sound' => 'default',
                        'click_action' => url('/orders/' . $this->order->id),
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
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
            'order_created' => 'New Order Created',
            'order_status_changed' => 'Order Status Updated',
            'order_completed' => 'Order Completed',
            default => 'Order Notification',
        };
    }

    /**
     * Get the notification message.
     *
     * @return string
     */
    protected function getMessage(): string
    {
        return match ($this->type) {
            'order_created' => 'A new order has been placed for your design. Tap to view the order details.',
            'order_status_changed' => 'Your order #' . $this->order->id . ' status has been updated from "' .
                ($this->data['old_status'] ?? 'previous') . '" to "' .
                ($this->data['new_status'] ?? $this->order->status) . '".',
            'order_completed' => 'Your order #' . $this->order->id . ' has been completed successfully.',
            default => 'You have a new order notification.',
        };
    }
}
