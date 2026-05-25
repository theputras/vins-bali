<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Booking $booking
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $carName = $this->booking->car->name ?? 'Unknown';

        return (new MailMessage)
            ->subject('🚗 Pesanan Rental Baru — ' . $this->booking->customer_name)
            ->greeting('Halo Admin!')
            ->line('Ada pesanan rental baru yang masuk.')
            ->line('**Nama Pelanggan:** ' . $this->booking->customer_name)
            ->line('**No. Telepon:** ' . $this->booking->customer_phone)
            ->line('**Mobil:** ' . $carName)
            ->line('**Rencana Tanggal Sewa:** ' . ($this->booking->rental_date ?? '-'))
            ->action('Lihat di Admin Panel', url('/vbpanel/bookings'))
            ->line('Silakan segera follow-up pelanggan.');
    }

    /**
     * Get the array representation of the notification (for database channel).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'customer_name' => $this->booking->customer_name,
            'customer_phone' => $this->booking->customer_phone,
            'car_name' => $this->booking->car->name ?? 'Unknown',
            'rental_date' => $this->booking->rental_date,
            'message' => 'Pesanan baru dari ' . $this->booking->customer_name . ' untuk ' . ($this->booking->car->name ?? 'Unknown'),
        ];
    }
}
