<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>My Reservations</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-gradient-to-b from-[#0b1220] to-[#0f172a] text-white">
        <div class="max-w-4xl mx-auto py-12 px-6">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#60a5fa]"></div>
                    <h1 class="text-2xl font-semibold">My Reservations</h1>
                </div>
                <a href="{{ route('restaurants.index') }}" class="text-sm text-[#93c5fd] hover:text-white">Browse restaurants</a>
            </div>

            @if (session('status'))
                <div class="rounded-sm border border-[#bbf7d0] bg-[#f0fdf4] text-[#166534] p-3 mb-6">
                    {{ session('status') }}
                </div>
            @endif

            @forelse ($reservations as $reservation)
                <div class="bg-[#0f172a]/60 backdrop-blur border border-[#1e293b] rounded-lg p-6 mb-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-medium">{{ $reservation->restaurant->name }}</h3>
                                @php
                                    $statusClass = match($reservation->status) {
                                        'confirmed' => 'bg-green-900 text-green-300',
                                        'pending'   => 'bg-yellow-900 text-yellow-300',
                                        'cancelled' => 'bg-red-900 text-red-300',
                                        default     => 'bg-gray-900 text-gray-300',
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-4 text-sm text-[#cbd5e1]">
                                <div>
                                    <div class="font-medium text-white mb-1">Reservation Details</div>
                                    <div>Date: {{ $reservation->reservation_date->format('M j, Y') }}</div>
                                    <div>Time: {{ date('g:i A', strtotime($reservation->reservation_time)) }}</div>
                                    <div>Guests: {{ $reservation->number_of_guests }}</div>
                                </div>
                                <div>
                                    <div class="font-medium text-white mb-1">Table Information</div>
                                    <div>Table: {{ $reservation->table?->table_name ?? 'Unassigned' }}</div>
                                    <div>Location: {{ $reservation->table?->location ?? 'N/A' }}</div>
                                    <div>Reference: {{ $reservation->reservation_reference }}</div>
                                </div>
                            </div>
                            
                            @if($reservation->special_requests)
                                <div class="mt-3 text-sm">
                                    <div class="font-medium text-white mb-1">Special Requests</div>
                                    <div class="text-[#cbd5e1]">{{ $reservation->special_requests }}</div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex gap-2 ml-4">
                            <a href="{{ route('reservations.show', $reservation) }}" 
                               class="px-3 py-1 text-sm rounded-sm bg-[#1e3a8a] hover:bg-[#1d4ed8]">View</a>
                            @if (Route::has('reservations.edit'))
                            <a href="{{ route('reservations.edit', $reservation) }}"
                               class="px-3 py-1 text-sm rounded-sm bg-[#0f766e] hover:bg-[#115e59]">Edit</a>
                            @endif

                            @if (!in_array($reservation->status, ['cancelled','completed']))
                            <button type="button"
                                    class="px-3 py-1 text-sm rounded-sm bg-[#dc2626] hover:bg-[#b91c1c]"
                                    onclick="showCancelConfirm({{ $reservation->id }})">
                                Cancel
                            </button>
                            @endif
                        </div>
                    </div>

                    @if (!in_array($reservation->status, ['cancelled','completed']))
                    <div id="cancel-confirm-{{ $reservation->id }}" class="hidden mt-3 flex items-center justify-between gap-3 p-3 rounded border border-[#334155] bg-[#0b1220]">
                        <div class="text-sm text-[#cbd5e1]">
                            Are you sure you want to cancel this reservation?
                        </div>
                        <div class="flex gap-2">
                            @if (Route::has('reservations.cancel'))
                            <form action="{{ route('reservations.cancel', $reservation) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 text-sm rounded-sm bg-[#dc2626] hover:bg-[#b91c1c]">
                                   Confirm Cancel
                                </button>
                            </form>
                            @else
                            <button type="button" disabled
                                class="px-3 py-1 text-sm rounded-sm bg-[#334155] text-[#94a3b8] cursor-not-allowed"
                                title="Cancel route not available">
                                Cancel not available
                            </button>
                            @endif
                            <button type="button" class="px-3 py-1 text-sm rounded-sm bg-[#1e293b] hover:bg-[#334155]"
                                    onclick="hideCancelConfirm({{ $reservation->id }})">
                                Keep
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <div class="text-[#64748b] mb-4">No reservations found</div>
                    <a href="{{ route('restaurants.index') }}" class="px-4 py-2 rounded-sm bg-[#2563eb] text-white hover:bg-[#1d4ed8]">
                        Make a reservation
                    </a>
                </div>
            @endforelse
        </div>

        <script>
        function showCancelConfirm(id) {
            const el = document.getElementById('cancel-confirm-' + id);
            if (el) el.classList.remove('hidden');
        }
        function hideCancelConfirm(id) {
            const el = document.getElementById('cancel-confirm-' + id);
            if (el) el.classList.add('hidden');
        }
        </script>
    </body>
</html>
