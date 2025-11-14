<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management - TabledIn Staff</title>
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
                    <a href="{{ route('staff.reservations') }}" class="px-4 py-1.5 rounded-sm border border-[#1e3a8a] hover:border-[#3b82f6]">Reservations</a>
                    <a href="{{ route('staff.seating.index') }}" class="px-4 py-1.5 rounded-sm border border-[#1e3a8a] hover:border-[#3b82f6]">Seating</a>
                    <a href="{{ route('staff.restaurants') }}" class="px-4 py-1.5 rounded-sm bg-[#2563eb] text-white">Restaurants</a>
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
            <h1 class="text-3xl font-bold text-gray-900">Restaurant Management</h1>
            <p class="text-gray-600 mt-2">View and manage restaurant information, tables, and reservations.</p>
        </div>

        <!-- Restaurants Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($restaurants as $restaurant)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $restaurant->name }}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($restaurant->is_active) bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                @if($restaurant->is_active) Active
                                @else Inactive
                                @endif
                            </span>
                        </div>

                        <p class="text-sm text-gray-600 mb-4">{{ $restaurant->description }}</p>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tables:</span>
                                <span class="font-medium">{{ $restaurant->tables_count }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Reservations:</span>
                                <span class="font-medium">{{ $restaurant->reservations_count }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Max Capacity:</span>
                                <span class="font-medium">{{ $restaurant->max_capacity }}</span>
                            </div>
                        </div>

                        @if($restaurant->amenities)
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 mb-1">Amenities:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($restaurant->amenities as $amenity)
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">{{ $amenity }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex gap-2">
                            <a href="{{ route('restaurants.index') }}" class="flex-1 bg-blue-600 text-white text-center px-3 py-2 rounded text-sm hover:bg-blue-700">
                                View Public
                            </a>
                            <a href="{{ route('staff.reservations', ['restaurant_id' => $restaurant->id]) }}" class="flex-1 bg-green-600 text-white text-center px-3 py-2 rounded text-sm hover:bg-green-700">
                                View Reservations
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($restaurants->count() === 0)
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No restaurants found</h3>
                <p class="mt-1 text-sm text-gray-500">No restaurants are currently registered in the system.</p>
            </div>
        @endif
    </div>
</body>
</html>
