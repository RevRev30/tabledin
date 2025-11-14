<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Reservation Details - TabledIn Staff</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
	<header class="bg-white shadow-sm border-b">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="flex justify-between items-center h-16">
				<div class="flex items-center gap-3">
					<div class="w-3 h-3 rounded-full bg-[#60a5fa]"></div>
					<span class="font-semibold tracking-wide text-xl">TabledIn Staff</span>
				</div>
				<nav class="flex items-center gap-3 text-sm">
					<a href="{{ route('staff.dashboard') }}" class="px-4 py-1.5 rounded-sm border border-[#1e3a8a] hover:border-[#3b82f6]">Dashboard</a>
					<a href="{{ route('staff.reservations') }}" class="px-4 py-1.5 rounded-sm border border-[#1e3a8a] hover:border-[#3b82f6]">Reservations</a>
					<a href="{{ route('staff.seating.index', array_filter(['restaurant_id' => $reservation->restaurant_id ?? null])) }}" class="px-4 py-1.5 rounded-sm border border-[#1e3a8a] hover:border-[#3b82f6]">Seating</a>
					<a href="{{ route('staff.restaurants') }}" class="px-4 py-1.5 rounded-sm border border-[#1e3a8a] hover:border-[#3b82f6]">Restaurants</a>
					<a href="{{ route('staff.customers') }}" class="px-4 py-1.5 rounded-sm border border-[#1e3a8a] hover:border-[#3b82f6]">Customers</a>
					<form action="{{ route('logout') }}" method="POST">
						@csrf
						<button class="px-4 py-1.5 rounded-sm bg-red-600 hover:bg-red-700 text-white">Logout</button>
					</form>
				</nav>
			</div>
		</div>
	</header>

	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
		<div class="mb-6 flex items-center justify-between">
			<div>
				<h1 class="text-3xl font-bold text-gray-900">Reservation Details</h1>
				<p class="text-gray-600 mt-2">View and manage a single reservation.</p>
			</div>
			<a href="{{ url()->previous() ?: route('staff.reservations') }}" class="inline-flex items-center px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">← Back</a>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
			<!-- Details -->
			<div class="lg:col-span-2 space-y-6">
				<div class="bg-white rounded-lg shadow p-6">
					<h2 class="text-xl font-semibold text-gray-900 mb-4">Customer Information</h2>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<div class="text-sm text-gray-500">Name</div>
							<div class="text-sm font-medium text-gray-900">{{ $reservation->customer->name ?? 'N/A' }}</div>
						</div>
						<div>
							<div class="text-sm text-gray-500">Email</div>
							<div class="text-sm font-medium text-gray-900">{{ $reservation->customer->email ?? 'N/A' }}</div>
						</div>
					</div>
				</div>

				<div class="bg-white rounded-lg shadow p-6">
					<h2 class="text-xl font-semibold text-gray-900 mb-4">Reservation Information</h2>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<div class="text-sm text-gray-500">Restaurant</div>
							<div class="text-sm font-medium text-gray-900">{{ $reservation->restaurant->name ?? 'N/A' }}</div>
						</div>
						<div>
							<div class="text-sm text-gray-500">Date & Time</div>
							<div class="text-sm font-medium text-gray-900">
								{{ optional($reservation->reservation_date)->format('M d, Y') ?? \Carbon\Carbon::parse($reservation->reservation_date ?? now())->format('M d, Y') }}
								@isset($reservation->reservation_time)
									at {{ $reservation->reservation_time }}
								@endisset
							</div>
						</div>
						<div>
							<div class="text-sm text-gray-500">Guests</div>
							<div class="text-sm font-medium text-gray-900">{{ $reservation->number_of_guests }}</div>
						</div>
						<div>
							<div class="text-sm text-gray-500">Table</div>
							<div class="text-sm font-medium text-gray-900">{{ $reservation->table?->table_name ?? 'Unassigned' }}</div>
						</div>
						<div>
							<div class="text-sm text-gray-500">Status</div>
							<span class="inline-block mt-1 px-2 py-1 text-xs font-medium rounded-full
								@if($reservation->status === 'confirmed') bg-green-100 text-green-800
								@elseif($reservation->status === 'pending') bg-yellow-100 text-yellow-800
								@elseif($reservation->status === 'cancelled') bg-red-100 text-red-800
								@elseif($reservation->status === 'completed') bg-blue-100 text-blue-800
								@else bg-gray-100 text-gray-800
								@endif">
								{{ ucfirst($reservation->status) }}
							</span>
						</div>
					</div>
					
					@if($reservation->special_requests)
					<div class="mt-6 pt-6 border-t border-gray-200">
						<div class="text-sm text-gray-500 mb-2">Special Requests</div>
						<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
							<p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $reservation->special_requests }}</p>
						</div>
					</div>
					@endif
				</div>
			</div>

			<!-- Actions -->
			<div class="space-y-6">
				<div class="bg-white rounded-lg shadow p-6">
					<h3 class="text-lg font-semibold text-gray-900 mb-4">Update Status</h3>
					<form action="{{ route('staff.reservations.updateStatus', $reservation) }}" method="POST" class="space-y-4">
						@csrf
						@method('PATCH')
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
							<select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
								<option value="pending" {{ $reservation->status === 'pending' ? 'selected' : '' }}>Pending</option>
							<option value="approved" {{ $reservation->status === 'approved' ? 'selected' : '' }}>Approved</option>
							{{-- Removed direct Confirmed option to require customer approval --}}
								<option value="cancelled" {{ $reservation->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
								<option value="completed" {{ $reservation->status === 'completed' ? 'selected' : '' }}>Completed</option>
							</select>
						</div>
						<button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Update Status</button>
					</form>
				</div>

				<div class="bg-white rounded-lg shadow p-6">
					<h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
					<div class="space-y-3">
						@if($reservation->status === 'pending')
							<form action="{{ route('staff.reservations.approve', $reservation) }}" method="POST">
								@csrf
								<button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
									✓ Approve & Notify Customer
								</button>
							</form>
						@endif

						{{-- Removed awaiting customer banner for approved status --}}

						@if($reservation->status === 'confirmed')
							<form action="{{ route('staff.reservations.complete', $reservation) }}" method="POST">
								@csrf
								<button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
									Mark as Completed
								</button>
							</form>
						@endif

						@if(!in_array($reservation->status, ['cancelled', 'completed']))
							<form action="{{ route('staff.reservations.cancel', $reservation) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?')">
								@csrf
								<button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
									Cancel Reservation
								</button>
							</form>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
