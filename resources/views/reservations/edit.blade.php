<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<title>Edit reservation</title>
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
		</nav>
	</header>

	<main class="max-w-3xl mx-auto p-6">
		<h1 class="text-2xl font-semibold mb-4">Edit reservation</h1>

		<form method="POST" action="{{ route('reservations.update', $reservation) }}" class="bg-white border rounded-lg p-6 shadow-sm">
			@csrf
			@method('PATCH')

			<div class="mb-4">
				<label class="block text-sm text-gray-700 mb-1">Date</label>
				<input type="date" name="reservation_date" value="{{ old('reservation_date', $reservation->reservation_date ?? $reservation->date ?? '') }}" class="w-full border rounded px-3 py-2">
				@error('reservation_date') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
			</div>

			<div class="mb-4">
				<label class="block text-sm text-gray-700 mb-1">Time</label>
				<input type="time" name="reservation_time" value="{{ old('reservation_time', $reservation->reservation_time ?? $reservation->time ?? '') }}" class="w-full border rounded px-3 py-2">
				@error('reservation_time') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
			</div>

			<div class="mb-4">
				<label class="block text-sm text-gray-700 mb-1">Number of guests</label>
				<input type="number" name="number_of_guests" min="1" value="{{ old('number_of_guests', $reservation->number_of_guests ?? $reservation->guests ?? 1) }}" class="w-full border rounded px-3 py-2">
				@error('number_of_guests') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
			</div>

			@if(!empty($tables) && $tables->count())
				<div class="mb-4">
					<label class="block text-sm text-gray-700 mb-1">Table</label>
					<select name="table_id" class="w-full border rounded px-3 py-2">
						<option value="">-- Select table --</option>
						@foreach($tables as $t)
							<option value="{{ $t->id }}" {{ (old('table_id', $reservation->table_id ?? '') == $t->id) ? 'selected' : '' }}>
								{{ $t->table_name }} ({{ $t->seats ?? '–' }} seats)
							</option>
						@endforeach
					</select>
					@error('table_id') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
				</div>
			@endif

			<div class="flex items-center gap-3 mt-4">
				<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save changes</button>
				<a href="{{ route('reservations.show', $reservation) }}" class="px-4 py-2 border rounded">Back</a>
			</div>
		</form>
	</main>
</body>
</html>
