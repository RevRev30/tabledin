<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function view(User $user, Reservation $reservation)
    {
        return $reservation->customer_id === $user->id;
    }

    public function update(User $user, Reservation $reservation)
    {
        return $reservation->customer_id === $user->id && in_array($reservation->status, ['pending', 'confirmed']);
    }

    public function delete(User $user, Reservation $reservation)
    {
        return $reservation->customer_id === $user->id && in_array($reservation->status, ['pending', 'confirmed']);
    }
}
