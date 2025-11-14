<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations - TabledIn Staff</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-[#60a5fa]"></div>
                    <span class="font-semibold tracking-wide text-xl">TabledIn Staff</span>
                </div>
                <nav class="flex items-center gap-3 text-sm">
                    <a href="{{ route('staff.dashboard') }}" class="px-4 py-1.5 rounded-sm border border-[#1e3a8a] hover:border-[#3b82f6]">Dashboard</a>
                    <a href="{{ route('staff.reservations') }}" class="px-4 py-1.5 rounded-sm bg-[#2563eb] text-white">Reservations</a>
                    <a href="{{ route('staff.seating.index', array_filter(['restaurant_id' => request('restaurant_id')])) }}" class="px-4 py-1.5 rounded-sm border border-[#1e3a8a] hover:border-[#3b82f6]">Seating</a>
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
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Reservations Management</h1>
            <p class="text-gray-600 mt-2">Manage all customer reservations and bookings.</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div>
                    <label for="restaurant_id" class="block text-sm font-medium text-gray-700 mb-2">Restaurant</label>
                    <select name="restaurant_id" id="restaurant_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Restaurants</option>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}" {{ request('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                                {{ $restaurant->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input
                        type="date"
                        name="date"
                        id="date"
                        value="{{ request('reservation_date', request('date')) }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <!-- also send reservation_date so whichever the controller expects will match -->
                    <input type="hidden" name="reservation_date" value="{{ request('reservation_date', request('date')) }}">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Filter
                    </button>
                    <a href="{{ route('staff.reservations') }}" class="w-full bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200 text-center">
                        Reset
                    </a>
                    <!-- Or use a button:
                    <button type="button" onclick="resetReservationFilters()" class="w-full bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200">
                        Reset
                    </button>
                    -->
                </div>
            </form>
        </div>

        <!-- Reservations Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">All Reservations</h2>
            </div>

            @if($reservations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restaurant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guests</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reservations as $reservation)
                                <tr class="hover:bg-gray-50" data-date="{{ optional($reservation->reservation_date)->toDateString() }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $reservation->customer->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $reservation->customer->email }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $reservation->restaurant->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $reservation->reservation_date->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $reservation->reservation_time }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $reservation->number_of_guests }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $reservation->table?->table_name ?? 'Unassigned' }}</div>
                                        <div class="text-sm text-gray-500">Capacity: {{ $reservation->table?->capacity ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($reservation->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($reservation->status === 'cancelled') bg-red-100 text-red-800
                                            @elseif($reservation->status === 'completed') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('staff.reservations.show', $reservation) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        @if($reservation->status === 'pending')
                                            <a href="{{ route('staff.reservations.show', $reservation) }}" class="text-green-600 hover:text-green-900 mr-3">Review</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $reservations->appends(request()->query())->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No reservations found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters to see more results.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>

<script>
// Client-side fallback filter by date (if backend didn't filter)
(function () {
    const params = new URLSearchParams(window.location.search);
    const qDate = params.get('reservation_date') || params.get('date');
    if (!qDate) return;
    document.querySelectorAll('tbody tr[data-date]').forEach(tr => {
        const rowDate = tr.getAttribute('data-date');
        tr.style.display = (rowDate === qDate) ? '' : 'none';
    });
})();

// Optional reset helper if you switch to a button instead of link
function resetReservationFilters() {
    try {
        const form = document.querySelector('form[method="GET"]');
        if (form) form.reset();
    } catch (e) {}
    window.location.href = "{{ route('staff.reservations') }}";
}
</script>
