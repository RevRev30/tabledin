<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservation Table Updated</title>
    <style>
        .btn { display: inline-block; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; }
        .btn-primary { background: #2563eb; color: #ffffff; }
        .btn-danger { background: #dc2626; color: #ffffff; }
        .muted { color: #6b7280; font-size: 12px; }
    </style>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; line-height: 1.5; color: #111827;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 8px 0;">Your reservation was updated</h2>
        <p style="margin: 0 0 16px 0;">Hi {{ $reservation->customer->name ?? 'there' }},</p>
        <p style="margin: 0 0 16px 0;">
            @if($oldTableName && $newTableName)
                Your table has been changed from <strong>{{ $oldTableName }}</strong> to <strong>{{ $newTableName }}</strong>.
            @elseif($newTableName)
                A table has been assigned: <strong>{{ $newTableName }}</strong>.
            @else
                Your reservation details were updated.
            @endif
        </p>

        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <p style="margin: 0 0 6px 0;"><strong>Date:</strong> {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('M d, Y') }}</p>
            <p style="margin: 0 0 6px 0;"><strong>Time:</strong> {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('g:i A') }}</p>
            <p style="margin: 0 0 6px 0;"><strong>Guests:</strong> {{ $reservation->number_of_guests }}</p>
            @if($reservation->table)
            <p style="margin: 0 0 6px 0;"><strong>Table:</strong> {{ $reservation->table->table_name }}</p>
            @endif
        </div>

        <p style="margin: 0 0 16px 0;">Please confirm or cancel your reservation:</p>

        <div style="margin: 12px 0 24px 0;">
            <a class="btn btn-primary" href="{{ $confirmUrl ?? route('reservations.customer-confirm', $reservation) }}">Confirm reservation</a>
            <span style="display:inline-block; width: 8px;"></span>
            <a class="btn btn-danger" href="{{ $cancelUrl ?? route('reservations.customer-cancel', $reservation) }}">Cancel reservation</a>
        </div>

        <p class="muted">If the buttons don't work, copy and paste these links in your browser:</p>
        <p class="muted">Confirm: {{ $confirmUrl ?? route('reservations.customer-confirm', $reservation) }}</p>
        <p class="muted">Cancel: {{ $cancelUrl ?? route('reservations.customer-cancel', $reservation) }}</p>

        <p style="margin-top: 24px;">Thanks,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>


