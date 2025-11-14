<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ReservationApproved extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The reservation instance.
     *
     * @var \App\Models\Reservation
     */
    public $reservation;

    /**
     * Create a new message instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $confirmUrl = URL::temporarySignedRoute('reservations.public-confirm', now()->addDays(2), ['reservation' => $this->reservation->id]);
        $cancelUrl = URL::temporarySignedRoute('reservations.public-cancel', now()->addDays(2), ['reservation' => $this->reservation->id]);

        return $this->subject('Your reservation has been approved')
                    ->view('emails.reservation-approved', [
                        'reservation' => $this->reservation,
                        'confirmUrl' => $confirmUrl,
                        'cancelUrl' => $cancelUrl,
                    ]);
    }
}
