@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-6">Seating Management</h1>

    {{-- Add a small defensive style to ensure modal sits above overlays --}}
    <style>
        /* Ensure bootstrap modals or custom modals are top-most */
        .modal { z-index: 11000 !important; }
        .modal-backdrop { z-index: 10999 !important; }
    </style>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Tables</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Example table card (replace your current "Reassign" action markup with the form below) --}}
            @foreach($tables as $table)
            <div class="table-card p-4 border rounded-lg shadow-sm bg-gray-50">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-lg font-semibold">Table {{ $table->id }}</h3>
                    <span class="text-sm {{ $table->status == 'available' ? 'text-green-500' : 'text-red-500' }}">
                        {{ ucfirst($table->status) }}
                    </span>
                </div>

                {{-- Old (non-clickable) action — remove/replace it --}}
                {{-- <a href="#" class="btn-reassign">Reassign</a> --}}

                {{-- New: POST form to reassign a reservation to this table.
                     - Adjust hidden input names to match your controller (reservation_id, to_table_id, from_table_id, date, etc.)
                     - If you invoke via AJAX, you can keep this form and submit via JS; this works as a fallback too.
                --}}
                <form method="POST" action="{{ route('staff.seating.reassign') }}" class="inline-block">
                    @csrf
                    {{-- Include any required fields your controller needs --}}
                    <input type="hidden" name="reservation_id" value="{{ $selectedReservation->id ?? '' }}">
                    <input type="hidden" name="to_table_id" value="{{ $table->id }}">
                    <input type="hidden" name="from_table_id" value="{{ $selectedReservation->table_id ?? '' }}">
                    <input type="hidden" name="date" value="{{ request()->get('date') ?? now()->toDateString() }}">
                    <button type="submit"
                            class="px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-sm rounded"
                            title="Reassign reservation to this table">
                        Reassign
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection