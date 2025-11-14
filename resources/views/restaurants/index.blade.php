<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Restaurants</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-gradient-to-b from-[#0b1220] to-[#0f172a] text-white">
        <div class="max-w-6xl mx-auto py-12 px-6">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#60a5fa]"></div>
                    <h1 class="text-2xl font-semibold">Find your next table</h1>
                </div>
                <a href="{{ request()->getBaseUrl() }}/" class="text-sm text-[#93c5fd] hover:text-white">Back to dashboard</a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($restaurants as $restaurant)
                    <div class="group relative overflow-hidden rounded-lg border border-[#1e293b] bg-[#0f172a]/60 backdrop-blur transition hover:border-[#2563eb] hover:shadow-[0_0_0_1px_#2563eb]">
                        <div class="aspect-[16/10] overflow-hidden">
                            @php
                                $hero = $restaurant->images[0] ?? $restaurant->logo;
                                if ($hero && !str_starts_with($hero, 'http')) {
                                    $hero = asset('storage/' . $hero);
                                }
                            @endphp
                            @if (!empty($hero))
                                <img src="{{ $hero }}" alt="{{ $restaurant->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" />
                            @else
                                <div class="h-full w-full bg-[#1e293b]"></div>
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-lg font-medium text-white">{{ $restaurant->name }}</div>
                                    <div class="text-xs text-[#93c5fd] mt-0.5">Capacity {{ $restaurant->max_capacity }}</div>
                                    <div class="text-sm text-[#cbd5e1] mt-1 line-clamp-2">{{ $restaurant->description }}</div>
                                    <div class="mt-2 text-xs text-[#94a3b8]">{{ $restaurant->address }}</div>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                @if ($restaurant->amenities)
                                    <div class="text-[11px] text-[#93c5fd] space-x-2">
                                        @foreach ($restaurant->amenities as $a)
                                            <span class="px-2 py-0.5 rounded-full bg-[#1e293b]">{{ $a }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <a href="{{ route('reservations.create', $restaurant) }}" class="px-3 py-2 rounded-sm bg-[#2563eb] text-white hover:bg-[#1d4ed8]">Book</a>
                            </div>
                            
                        </div>
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
                    </div>
                @empty
                    <div class="col-span-3 text-sm text-[#93c5fd]">No restaurants available.</div>
                @endforelse
            </div>
        </div>
    </body>
    </html>


