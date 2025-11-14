<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - TabledIn</title>
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
                    <a href="{{ route('staff.dashboard') }}" class="px-4 py-1.5 rounded-sm bg-[#2563eb] text-white">Dashboard</a>
                    <a href="{{ route('staff.reservations') }}" class="px-4 py-1.5 rounded-sm border border-[#1e3a8a] hover:border-[#3b82f6]">Reservations</a>
                    <a href="{{ route('staff.seating.index') }}" class="px-4 py-1.5 rounded-sm border border-[#1e3a8a] hover:border-[#3b82f6]">Seating</a>
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
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Staff Dashboard</h1>
            <p class="text-gray-600 mt-2">Welcome back, {{ auth()->user()->name }}! Here's what's happening today.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Today's Reservations</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['today_count'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Reservations</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_count'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Restaurants</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_restaurants'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Customers</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_customers'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Today's Reservations -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Today's Reservations</h2>
                </div>
                <div class="p-6">
                    @if($todayReservations->count() > 0)
                        <div class="space-y-4">
                            @foreach($todayReservations as $reservation)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $reservation->customer->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $reservation->restaurant->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $reservation->reservation_time }} • {{ $reservation->number_of_guests }} guests</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($reservation->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($reservation->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                        <a href="{{ route('staff.reservations.show', $reservation) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No reservations for today</p>
                    @endif
                </div>
            </div>

            <!-- Pending Reservations -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Pending Reservations</h2>
                </div>
                <div class="p-6">
                    @if($pendingReservations->count() > 0)
                        <div class="space-y-4">
                            @foreach($pendingReservations->take(5) as $reservation)
                                <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $reservation->customer->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $reservation->restaurant->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $reservation->reservation_date->format('M d') }} at {{ $reservation->reservation_time }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('staff.reservations.show', $reservation) }}" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Review</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($pendingReservations->count() > 5)
                            <div class="mt-4 text-center">
                                <a href="{{ route('staff.reservations', ['status' => 'pending']) }}" class="text-blue-600 hover:text-blue-800 text-sm">View all {{ $pendingReservations->count() }} pending reservations</a>
                            </div>
                        @endif
                    @else
                        <p class="text-gray-500 text-center py-8">No pending reservations</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('staff.reservations') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Manage Reservations</p>
                        <p class="text-sm text-gray-600">View and update all reservations</p>
                    </div>
                </a>

                <a href="{{ route('staff.seating.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Seating Management</p>
                        <p class="text-sm text-gray-600">Manage table assignments and layout</p>
                    </div>
                </a>

                <a href="{{ route('staff.restaurants') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="p-2 bg-orange-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Restaurant Management</p>
                        <p class="text-sm text-gray-600">Manage restaurant details and tables</p>
                    </div>
                </a>

                <a href="{{ route('staff.customers') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="p-2 bg-purple-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Customer Management</p>
                        <p class="text-sm text-gray-600">View customer information and history</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
