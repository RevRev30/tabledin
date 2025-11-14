<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ReservationTableUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $reservation;
    public ?string $oldTableName;
    public ?string $newTableName;

    public function __construct(Reservation $reservation, ?string $oldTableName = null, ?string $newTableName = null)
    {
        $this->reservation = $reservation;
        $this->oldTableName = $oldTableName;
        $this->newTableName = $newTableName;
    }

    public function build()
    {
        $subject = 'Your reservation table has been updated';

        $confirmUrl = URL::temporarySignedRoute('reservations.public-confirm', now()->addDays(2), ['reservation' => $this->reservation->id]);
        $cancelUrl = URL::temporarySignedRoute('reservations.public-cancel', now()->addDays(2), ['reservation' => $this->reservation->id]);

        return $this->subject($subject)
            ->view('emails.reservation-table-updated', [
                'reservation' => $this->reservation,
                'oldTableName' => $this->oldTableName,
                'newTableName' => $this->newTableName,
                'confirmUrl' => $confirmUrl,
                'cancelUrl' => $cancelUrl,
            ]);
    }
}


