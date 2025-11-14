<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Reserve at {{ $restaurant->name }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-gradient-to-b from-[#0b1220] to-[#0f172a] text-white">
        <div class="max-w-2xl mx-auto py-12 px-6">
            <a href="{{ route('restaurants.index') }}" class="text-sm text-[#93c5fd] hover:text-white">← Back to restaurants</a>
            <h1 class="text-3xl font-semibold mt-4 mb-2">Reserve at {{ $restaurant->name }}</h1>
            <p class="text-[#cbd5e1] mb-8">Choose your preferred date, time, and seating</p>

            @if (session('status'))
                <div class="rounded-sm border border-[#bbf7d0] bg-[#f0fdf4] text-[#166534] p-3 mb-6">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-[#0f172a]/60 backdrop-blur border border-[#1e293b] rounded-lg p-6">
                <form method="POST" action="{{ route('reservations.store') }}">
                    @csrf

                    <!-- Include restaurant id so controller knows which restaurant this is for -->
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}" />

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-2">Reservation Date</label>
                            <input type="date" name="reservation_date" value="{{ old('reservation_date') }}" required 
                                   class="w-full border border-[#1e293b] bg-[#0b1220] text-white rounded-sm px-3 py-2 focus:border-[#2563eb] focus:ring-1 focus:ring-[#2563eb]">
                            @error('reservation_date')<p class="text-sm text-[#ef4444] mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Time</label>
                            <select name="reservation_time" required 
                                    class="w-full border border-[#1e293b] bg-[#0b1220] text-white rounded-sm px-3 py-2 focus:border-[#2563eb] focus:ring-1 focus:ring-[#2563eb]">
                                <option value="" disabled selected>Select time</option>
                                @foreach ($timeSlots as $time)
                                    <option value="{{ $time }}" {{ old('reservation_time') == $time ? 'selected' : '' }}>
                                        {{ date('g:i A', strtotime($time)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reservation_time')<p class="text-sm text-[#ef4444] mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Number of Guests</label>
                        <input type="number" min="1" max="20" name="number_of_guests" value="{{ old('number_of_guests', 1) }}" required 
                               class="w-full border border-[#1e293b] bg-[#0b1220] text-white rounded-sm px-3 py-2 focus:border-[#2563eb] focus:ring-1 focus:ring-[#2563eb]">
                        @error('number_of_guests')<p class="text-sm text-[#ef4444] mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Table Selection</label>
                        <div class="grid gap-3">
                            @foreach ($tables as $table)
                                <label class="flex items-center p-3 border border-[#1e293b] rounded-sm hover:border-[#2563eb] cursor-pointer">
                                    <input type="radio" name="table_id" value="{{ $table->id }}" 
                                           {{ old('table_id') == $table->id ? 'checked' : '' }}
                                           class="mr-3 text-[#2563eb] focus:ring-[#2563eb]">
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $table->table_name }}</div>
                                        <div class="text-sm text-[#cbd5e1]">
                                            Seats {{ $table->capacity }} • {{ $table->location }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('table_id')<p class="text-sm text-[#ef4444] mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Special Requests (Optional)</label>
                        <textarea name="special_requests" rows="3" placeholder="Any special dietary requirements, celebrations, or preferences..."
                                  class="w-full border border-[#1e293b] bg-[#0b1220] text-white rounded-sm px-3 py-2 focus:border-[#2563eb] focus:ring-1 focus:ring-[#2563eb] placeholder:text-[#64748b]">{{ old('special_requests') }}</textarea>
                    </div>

                    <button type="submit" class="w-full px-6 py-3 rounded-sm bg-[#2563eb] text-white hover:bg-[#1d4ed8] font-medium transition">
                        Confirm Reservation
                    </button>
                </form>
            </div>
        </div>
    </body>
</html>


