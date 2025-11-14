<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<title>Reservation Details</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-white text-gray-900">
	<header class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between border-b">
		<div class="flex items-center gap-3">
			<div class="w-3 h-3 rounded-full bg-[#60a5fa]"></div>
			<span class="font-semibold">TabledIn</span>
		</div>
		<nav class="flex items-center gap-3 text-sm">
			<a href="{{ route('restaurants.index') }}" class="px-3 py-1 rounded border">Restaurants</a>
			<a href="{{ route('reservations.index') }}" class="px-3 py-1 rounded border">My reservations</a>
			<a href="{{ route('profile.edit') }}" class="px-3 py-1 rounded border">Profile</a>
		</nav>
	</header>

	<main class="max-w-3xl mx-auto p-6">
		<h1 class="text-2xl font-semibold mb-4">Reservation details</h1>

		<div class="bg-white border rounded-lg shadow-sm p-6">
			<div class="mb-4">
				<div class="text-sm text-gray-500">Restaurant</div>
				<div class="font-medium text-lg">{{ $reservation->restaurant->name ?? '—' }}</div>
			</div>

			<div class="grid grid-cols-2 gap-4 mb-4">
				<div>
					<div class="text-sm text-gray-500">Date</div>
					<div class="font-medium">{{ \Illuminate\Support\Str::limit($reservation->reservation_date ?? $reservation->date ?? '—', 10) }}</div>
				</div>
				<div>
					<div class="text-sm text-gray-500">Time</div>
					<div class="font-medium">{{ $reservation->reservation_time ?? $reservation->time ?? '—' }}</div>
				</div>
				<div>
					<div class="text-sm text-gray-500">Guests</div>
					<div class="font-medium">{{ $reservation->number_of_guests ?? $reservation->guests ?? '—' }}</div>
				</div>
				<div>
					<div class="text-sm text-gray-500">Table</div>
					<div class="font-medium">{{ optional($reservation->table)->table_name ?? 'Unassigned' }}</div>
				</div>
			</div>

			<div class="mb-4">
				<div class="text-sm text-gray-500">Status</div>
				<div class="font-medium">{{ ucfirst($reservation->status ?? 'unknown') }}</div>
			</div>

			<div class="flex items-center gap-3 mt-4">
				<a href="{{ route('reservations.edit', $reservation) }}" class="px-4 py-2 bg-blue-600 text-white rounded">Edit</a>
				@if (Route::has('reservations.cancel'))
				<form action="{{ route('reservations.cancel', $reservation) }}" method="POST" onsubmit="return confirm('Cancel this reservation?')">
					@csrf
					<button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Cancel</button>
				</form>
				@endif
			</div>
		</div>
	</main>
</body>
</html>
