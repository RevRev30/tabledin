@extends('layouts.app')

@section('title', 'Seating Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Seating Management</span>
                </h6>
                @isset($restaurants)
                <div class="px-3 mb-3">
                    <label class="form-label small mb-1">Restaurant</label>
                    <select class="form-select form-select-sm" onchange="switchRestaurant(this.value)">
                        @foreach($restaurants as $r)
                            <option value="{{ $r->id }}" {{ $r->id === $restaurant->id ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endisset
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#seating-map">
                            <i class="fas fa-map"></i> Seating Map
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#reservations">
                            <i class="fas fa-calendar-alt"></i> Today's Reservations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#table-status">
                            <i class="fas fa-table"></i> Table Status
                        </a>
                    </li>
                </ul>

                <!-- Live Status -->
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Live Status</span>
                    <span class="badge bg-success" id="connection-status">Online</span>
                </h6>
                <div class="px-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small>Available:</small>
                        <span class="badge bg-success" id="available-count">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Reserved:</small>
                        <span class="badge bg-warning" id="reserved-count">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Occupied:</small>
                        <span class="badge bg-danger" id="occupied-count">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <small>Maintenance:</small>
                        <span class="badge bg-secondary" id="maintenance-count">0</span>
                    </div>
                </div>

                <!-- Quick Actions -->
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Quick Actions</span>
                </h6>
                <div class="px-3">
                    <button class="btn btn-sm btn-outline-primary w-100 mb-2" onclick="refreshSeatingData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-sm btn-outline-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#assignTableModal">
                        <i class="fas fa-plus"></i> Assign Table
                    </button>
                </div>

                <!-- Walk-in Queue -->
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Walk-in Queue</span>
                </h6>
                <div class="px-3 mb-3 small">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Waiting:</span>
                        <span class="badge bg-secondary" id="queue-waiting-count">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Current Token:</span>
                        <span class="badge bg-info" id="queue-current-token">—</span>
                    </div>
                    <div class="mb-1">
                        <div class="d-flex justify-content-between">
                            <span>Name:</span>
                            <span id="queue-current-name" class="text-muted">—</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Party:</span>
                            <span id="queue-current-size" class="text-muted">—</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Est. Wait:</span>
                        <span class="badge bg-warning" id="queue-estimate">0m</span>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small mb-1">Seat current at table</label>
                        <select class="form-select form-select-sm" id="queue-table-select">
                            <option value="">Choose available table...</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-warning flex-fill" onclick="callNext()">Call Next</button>
                        <button type="button" class="btn btn-sm btn-success flex-fill" onclick="seatCurrentWalkIn()">Seat Current</button>
                    </div>
                    <form id="enqueueForm" class="mb-2">
                        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                        <div class="mb-1">
                            <input class="form-control form-control-sm" name="customer_name" placeholder="Customer name" required>
                        </div>
                        <div class="mb-1">
                            <input class="form-control form-control-sm" name="customer_phone" placeholder="Phone (optional)">
                        </div>
                        <div class="mb-2">
                            <input type="number" min="1" class="form-control form-control-sm" name="party_size" placeholder="Party size" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-primary flex-fill" onclick="enqueueWalkIn()">Add Walk-in</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="refreshQueue()">Refresh</button>
                        </div>
                    </form>
                </div>

                <!-- Table Status Legend -->
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Table Status</span>
                </h6>
                <div class="px-3">
                    <div class="d-flex align-items-center mb-1">
                        <div class="status-indicator available me-2"></div>
                        <small>Available</small>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <div class="status-indicator reserved me-2"></div>
                        <small>Reserved</small>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <div class="status-indicator occupied me-2"></div>
                        <small>Occupied</small>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <div class="status-indicator maintenance me-2"></div>
                        <small>Maintenance</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Seating Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0 align-items-center">
                        <div class="input-group input-group-sm me-2" style="width: 220px;">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="date" id="reservationDate" class="form-control" value="{{ $selectedDate ?? now()->toDateString() }}" onchange="onDateChange()">
                        </div>
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshSeatingData()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignTableModal">
                                <i class="fas fa-plus"></i> Assign Table
                            </button>
                        </div>
                    </div>
            </div>

            <!-- Seating Map Section -->
            <div id="seating-map" class="mb-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Restaurant Layout</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary" onclick="resetView()">
                                <i class="fas fa-expand-arrows-alt"></i> Reset View
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if(count($tables) === 0)
                            <div class="p-5 text-center text-muted">No tables found for this restaurant and date.</div>
                        @else
                            <div id="seating-canvas-container" style="position: relative; overflow: auto; min-height: 600px;">
                                <canvas id="seating-canvas" width="800" height="600" style="border: 1px solid #ddd; background: #f8f9fa;"></canvas>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Today's Reservations Section -->
            <div id="reservations" class="mb-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Reservations ({{ \Carbon\Carbon::parse($selectedDate ?? now()->toDateString())->format('M d, Y') }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Customer</th>
                                        <th>Guests</th>
                                        <th>Table</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="reservations-table">
                                    @foreach($todayReservations as $reservation)
                                    <tr data-reservation-id="{{ $reservation->id }}">
                                        <td>{{ $reservation->reservation_time ? \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') : 'No time' }}</td>
                                        <td>{{ $reservation->customer?->name ?? 'Unknown User' }}</td>
                                        <td>{{ $reservation->number_of_guests }}</td>
                                        <td>
                                            @if($reservation->table)
                                                <span class="badge bg-info">{{ $reservation->table?->table_name ?? 'No Table' }}</span>
                                            @else
                                                <span class="badge bg-warning">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $reservation->status === 'confirmed' ? 'success' : ($reservation->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($reservation->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @php $resStatus = strtolower($reservation->status); @endphp
                                                @if(in_array($resStatus, ['completed','cancelled']))
                                                    <button class="btn btn-outline-secondary" disabled title="Reservation is {{ ucfirst($resStatus) }}">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                @else
                                                    @if(!$reservation->table)
                                                        <button class="btn btn-outline-primary" onclick="assignTable({{ $reservation->id }})">
                                                            <i class="fas fa-plus"></i> Assign
                                                        </button>
                                                    @else
                                                        <button class="btn btn-outline-warning" onclick="reassignTable({{ $reservation->id }})">
                                                            <i class="fas fa-exchange-alt"></i> Reassign
                                                        </button>
                                                    @endif
                                                @endif
                                                <a href="{{ route('staff.reservations.show', $reservation) }}" class="btn btn-outline-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Status Overview -->
            <div id="table-status" class="mb-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Table Status Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row" id="table-status-grid">
                            @foreach($tables as $table)
                            <div class="col-md-3 mb-3">
                                <div class="card table-card" data-table-id="{{ $table->id }}">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">{{ $table->table_name }}</h6>
                                        <p class="card-text">
                                            <small class="text-muted">Capacity: {{ $table->capacity }}</small><br>
                                            @php 
                                                $currentReservation = $table->reservations->first();
                                                $today = now()->toDateString();
                                                $selected = $selectedDate ?? now()->toDateString();
                                                if ($currentReservation) {
                                                    $computedStatus = $currentReservation->status === 'confirmed' ? 'reserved' : ($currentReservation->status === 'pending' ? 'pending' : ($table->status === 'maintenance' ? 'maintenance' : 'available'));
                                                } else {
                                                    if ($selected === $today) {
                                                        $computedStatus = in_array($table->status, ['occupied','reserved']) ? $table->status : ($table->status === 'maintenance' ? 'maintenance' : 'available');
                                                    } else {
                                                        $computedStatus = $table->status === 'maintenance' ? 'maintenance' : 'available';
                                                    }
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $computedStatus === 'available' ? 'success' : ($computedStatus === 'reserved' ? 'warning' : ($computedStatus === 'occupied' ? 'danger' : ($computedStatus === 'pending' ? 'info' : 'secondary'))) }}">
                                                {{ ucfirst($computedStatus) }}
                                            </span>
                                        </p>
                                        @if($table->reservations->count() > 0)
                                            @php $currentReservation = $table->reservations->first(); @endphp
                                            <small class="text-muted">
                                                {{ $currentReservation->customer?->name ?? 'Unknown User' }}<br>
                                                {{ $currentReservation->reservation_time ? \Carbon\Carbon::parse($currentReservation->reservation_time)->format('H:i') : 'No time' }}
                                            </small>
                                        @endif
                                        <div class="mt-2">
                                            <div class="btn-group btn-group-sm">
                                                @php 
                                                    $isToday = ($selectedDate ?? now()->toDateString()) === now()->toDateString(); 
                                                    $statuses = [
                                                        'available' => ['icon' => 'check', 'color' => 'primary'],
                                                        'reserved' => ['icon' => 'bookmark', 'color' => 'warning'],
                                                        'occupied' => ['icon' => 'user', 'color' => 'danger'],
                                                        'maintenance' => ['icon' => 'tools', 'color' => 'secondary'],
                                                    ];
                                                @endphp
                                                @foreach($statuses as $statusKey => $info)
                                                    <button 
                                                        class="btn btn-{{ $table->status === $statusKey ? $info['color'] : 'outline-' . $info['color'] }}{{ $table->status === $statusKey ? ' active' : '' }}" 
                                                        onclick="updateTableStatus({{ $table->id }}, '{{ $statusKey }}')" 
                                                        {{ $isToday ? '' : 'disabled title=Status changes allowed only for today' }}
                                                        {{ $table->status === $statusKey ? 'disabled' : '' }}
                                                    >
                                                        <i class="fas fa-{{ $info['icon'] }}"></i>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Assign Table Modal -->
<div class="modal fade" id="assignTableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign/Reassign Table to Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignTableForm">
                    <div class="mb-3">
                        <label for="reservationSelect" class="form-label">Select Reservation</label>
                        <select class="form-select" id="reservationSelect" required>
                            <option value="">Choose a reservation...</option>
                            @foreach($todayReservations as $reservation)
                            <option value="{{ $reservation->id }}" {{ $reservation->table_id ? 'data-has-table="true"' : '' }}>
                                {{ $reservation->customer?->name ?? 'Unknown User' }} - {{ $reservation->reservation_time ? \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') : 'No time' }} ({{ $reservation->number_of_guests }} guests)
                                @if($reservation->table_id)
                                    - Currently: {{ $reservation->table?->table_name ?? 'Unknown Table' }}
                                @else
                                    - Unassigned
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tableSelect" class="form-label">Select Table</label>
                        <select class="form-select" id="tableSelect" required>
                            <option value="">Choose a table...</option>
                            @foreach($tables as $table)
                                @php 
                                    $currentReservation = $table->reservations->first();
                                    $computedStatus = $table->status;
                                    if ($currentReservation) {
                                        if ($currentReservation->status === 'confirmed') {
                                            $computedStatus = 'reserved';
                                        } elseif ($currentReservation->status === 'pending') {
                                            $computedStatus = 'pending';
                                        }
                                    }
                                @endphp
                            <option value="{{ $table->id }}" data-capacity="{{ $table->capacity }}" {{ $computedStatus !== 'available' ? 'disabled' : '' }}>
                                {{ $table->table_name }} (Capacity: {{ $table->capacity }}) - {{ ucfirst($computedStatus) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitTableAssignment()" id="assignTableBtn">Assign Table</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form used as a reliable POST fallback for assign/reassign (includes CSRF) -->
<form id="assignTableHiddenForm" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="table_id" id="hidden_table_id" value="">
    <input type="hidden" name="new_table_id" id="hidden_new_table_id" value="">
</form>

@endsection

@push('styles')
<style>
.sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 48px 0 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar-sticky {
    position: relative;
    top: 0;
    height: calc(100vh - 48px);
    padding-top: .5rem;
    overflow-x: hidden;
    overflow-y: auto;
}

.sidebar .nav-link {
    font-weight: 500;
    color: #333;
}

.sidebar .nav-link.active {
    color: #007bff;
}

.sidebar .nav-link:hover {
    color: #007bff;
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.status-indicator.available {
    background-color: #28a745;
}

.status-indicator.reserved {
    background-color: #ffc107;
}

.status-indicator.occupied {
    background-color: #dc3545;
}

.status-indicator.maintenance {
    background-color: #6c757d;
}

.table-card {
    transition: transform 0.2s;
}

.table-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

#seating-canvas {
    cursor: crosshair;
    width: 100%;
}

#seating-canvas.edit-mode {
    cursor: move;
}

.table-element {
    position: absolute;
    border: 2px solid #333;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s;
}

.table-element:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.table-element.available {
    border-color: #28a745;
    background: rgba(40, 167, 69, 0.1);
}

.table-element.reserved {
    border-color: #ffc107;
    background: rgba(255, 193, 7, 0.1);
}

.table-element.occupied {
    border-color: #dc3545;
    background: rgba(220, 53, 69, 0.1);
}

.table-element.maintenance {
    border-color: #6c757d;
    background: rgba(108, 117, 125, 0.1);
}

/* Ensure alerts are fully visible */
.alert {
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow: visible !important;
    max-width: 100% !important;
    min-height: auto !important;
    padding: 15px !important;
    margin: 10px 0 !important;
    font-size: 14px !important;
    line-height: 1.4 !important;
    display: block !important;
    position: relative !important;
    z-index: 1050 !important;
}

.alert .btn-close {
    position: absolute !important;
    top: 10px !important;
    right: 10px !important;
}

/* Responsive fixes: stack sidebar and content on small screens */
@media (max-width: 767.98px) {
    .sidebar {
        position: static !important;
        top: auto !important;
        bottom: auto !important;
        left: auto !important;
        width: 100% !important;
        height: auto !important;
        padding: 0 !important;
        box-shadow: none !important;
        z-index: auto !important;
        background: transparent !important;
    }
    /* Ensure main content uses full width and isn't pushed aside */
    main.col-md-9.ms-sm-auto.col-lg-10.px-md-4 {
        margin-left: 0 !important;
        width: 100% !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    /* Slightly reduce min height so it fits smaller screens */
    #seating-canvas-container {
        min-height: 420px !important;
    }
}

/* Ensure main content is shifted right of the fixed sidebar on larger screens
   so the header (including the date/calendar control) stays visible and clickable.
   Adjust 240px if your sidebar width differs. */
@media (min-width: 768px) {
    .sidebar {
        width: 240px; /* keep a consistent fixed width */
    }
    main.col-md-9.ms-sm-auto.col-lg-10.px-md-4 {
        margin-left: 240px !important;
    }
    .container-fluid {
        padding-left: 0; /* avoid extra left padding when sidebar is fixed */
    }
}
</style>
@endpush

@push('scripts')
<script>
const ASSIGN_URL_BASE = "{{ url('staff/seating/reservations') }}";
const APP_TODAY = '{{ now()->toDateString() }}';
function switchRestaurant(id) {
    const url = new URL(window.location.href);
    url.searchParams.set('restaurant_id', id);
    const date = document.getElementById('reservationDate')?.value;
    if (date) url.searchParams.set('date', date);
    window.location.href = url.toString();
}
let seatingData = @json($tables);
let editMode = false;
let canvas, ctx;
let selectedTable = null;
const TABLE_WIDTH = 80;
const TABLE_HEIGHT = 60;

// Initialize the seating canvas
document.addEventListener('DOMContentLoaded', function() {
    canvas = document.getElementById('seating-canvas');
    ctx = canvas.getContext('2d');
    
    // Set canvas size to consume container and resize on window changes
    function resizeCanvas() {
        const container = document.getElementById('seating-canvas-container');
        const containerWidth = container ? container.clientWidth : window.innerWidth - 100;
        const availableHeight = window.innerHeight - (container?.getBoundingClientRect().top || 0) - 100;
        canvas.width = Math.max(containerWidth, 800);
        canvas.height = Math.max(availableHeight, 600);
        applyAutoLayoutIfMissing();
        drawSeatingLayout();
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();
    // Fetch latest data immediately on load so availability reflects current state
    refreshSeatingData();
    
    // Add canvas event listeners
    canvas.addEventListener('click', handleCanvasClick);
    canvas.addEventListener('mousemove', handleCanvasMouseMove);
    
    // Start real-time updates
    setInterval(refreshSeatingData, 30000); // Refresh every 30 seconds
    
    // Ensure assign table button is enabled
    const assignButton = document.querySelector('button[onclick="submitTableAssignment()"]');
    if (assignButton) {
        assignButton.disabled = false;
        assignButton.style.opacity = '1';
        assignButton.style.cursor = 'pointer';
        console.log('Assign button enabled');
    }
    
    // Add event listener for modal show
    const assignModal = document.getElementById('assignTableModal');
    if (assignModal) {
        assignModal.addEventListener('shown.bs.modal', function() {
            console.log('Assign table modal shown');
            // Ensure table dropdown reflects most recent availability
            populateTableSelect();
            const button = this.querySelector('button[onclick="submitTableAssignment()"]');
            if (button) {
                button.disabled = false;
                button.style.opacity = '1';
                button.style.cursor = 'pointer';
                console.log('Button enabled on modal show');
                
                // Add click test
                button.addEventListener('click', function(e) {
                    console.log('Button clicked!', e);
                });
            }
        });
    }
    
    // Test button click directly
    const assignBtn = document.getElementById('assignTableBtn');
    if (assignBtn) {
        assignBtn.addEventListener('click', function(e) {
            console.log('Direct button click detected!', e);
        });
    }
});

// If tables don't have saved positions, auto-arrange them into neat rows by prefix
function applyAutoLayoutIfMissing() {
    if (!Array.isArray(seatingData)) return;

    const startX = 120, startY = 120, colGap = 140, rowGap = 140;
    const tableWidth = TABLE_WIDTH; // keep in sync with drawTable

    // determine max columns that fit within current canvas width; ensure at least 1
    const availableWidth = (canvas?.width || 800) - startX * 2;
    const computedCols = Math.max(1, Math.floor((availableWidth + (colGap - tableWidth)) / colGap));
    const maxCols = Math.min(6, computedCols); // cap columns to keep rows compact

    // Find tables that already have a valid position
    const hasPos = t => t && t.position && typeof t.position.x === 'number' && typeof t.position.y === 'number';
    const positioned = seatingData.filter(hasPos);
    const missing = seatingData.filter(t => !hasPos(t));
    if (missing.length === 0) return; // nothing to do

    // Compute baseline row below the lowest positioned table to avoid overlap
    const lowestY = positioned.length ? Math.max(...positioned.map(t => t.position.y || 0)) : (startY - rowGap);
    let currentRow = Math.max(0, Math.floor((lowestY - startY) / rowGap) + 1);

    // Group missing by prefix so W/C/B/T stay in intuitive bands
    const groups = { W: [], C: [], B: [], T: [], OTHER: [] };
    missing.forEach(t => {
        const label = (t.name || t.table_name || '').toString().trim();
        const first = label.charAt(0).toUpperCase() === 'T' && label.startsWith('Table') ? 'T' : label.charAt(0).toUpperCase();
        const key = ['W','C','B','T'].includes(first) ? first : 'OTHER';
        groups[key].push(t);
    });
    // sort within each group by numeric suffix if present (e.g., T1..T12, B1..B7)
    const numericKey = t => {
        const label = (t.name || t.table_name || '').toString();
        const match = label.match(/(\d+)/);
        return match ? parseInt(match[1], 10) : Number.MAX_SAFE_INTEGER;
    };
    Object.keys(groups).forEach(k => groups[k].sort((a,b) => numericKey(a) - numericKey(b)));

    const rowOrder = ['W','C','B','T','OTHER'];
    rowOrder.forEach(key => {
        const row = groups[key];
        const totalSubRows = Math.max(1, Math.ceil(row.length / maxCols));
        for (let r = 0; r < totalSubRows; r++) {
            const startIndex = r * maxCols;
            const endIndex = Math.min(startIndex + maxCols, row.length);
            const countThisRow = endIndex - startIndex;
            const rowWidth = (countThisRow - 1) * colGap;
            const dynamicStartX = Math.max(60, (canvas.width - rowWidth) / 2 - tableWidth / 2);
            for (let i = 0; i < countThisRow; i++) {
                const t = row[startIndex + i];
                t.position = { x: dynamicStartX + i * colGap, y: startY + currentRow * rowGap };
            }
            currentRow += 1;
        }
    });

    // expand canvas height if needed to fit all rows
    const neededHeight = startY + (currentRow + 1) * rowGap;
    if (canvas && neededHeight > canvas.height) {
        canvas.height = neededHeight;
    }
}

function drawSeatingLayout() {
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Compute content bounds to auto-fit into view
    let maxX = 0, maxY = 0;
    seatingData.forEach(t => {
        const x = (t.position?.x || 100) + TABLE_WIDTH;
        const y = (t.position?.y || 100) + TABLE_HEIGHT + 40; // include labels
        if (x > maxX) maxX = x;
        if (y > maxY) maxY = y;
    });
    const margin = 80;
    const scaleX = canvas.width / (maxX + margin);
    const scaleY = canvas.height / (maxY + margin);
    const scale = Math.min(1, scaleX, scaleY);

    // Draw background
    ctx.fillStyle = '#f8f9fa';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    ctx.save();
    ctx.scale(scale, scale);
    // Draw tables at scaled context
    seatingData.forEach(table => {
        drawTable(table);
    });
    ctx.restore();
}

function drawTable(table) {
    const x = table.position?.x || 100;
    const y = table.position?.y || 100;
    const width = TABLE_WIDTH;
    const height = TABLE_HEIGHT;
    
    // Set color based on status
    let color = '#28a745'; // available
    if (table.status === 'reserved') color = '#ffc107';
    else if (table.status === 'occupied') color = '#dc3545';
    else if (table.status === 'maintenance') color = '#6c757d';
    
    // Draw table rectangle
    ctx.fillStyle = color;
    ctx.fillRect(x, y, width, height);
    
    // Draw border
    ctx.strokeStyle = '#333';
    ctx.lineWidth = 2;
    ctx.strokeRect(x, y, width, height);
    
    // Draw table name
    ctx.fillStyle = '#333';
    ctx.font = '12px Arial';
    ctx.textAlign = 'center';
    ctx.fillText(table.name, x + width/2, y + height/2 - 5);
    
    // Draw capacity
    ctx.font = '10px Arial';
    ctx.fillText(`(${table.capacity})`, x + width/2, y + height/2 + 10);

    // Draw location label (Window/Corner/Main Dining)
    ctx.font = '10px Arial';
    const zoneLabel = getZoneLabel(table);
    ctx.fillText(zoneLabel, x + width/2, y + height + 16);
    
    // Draw reservation info if exists
    if (table.current_reservation) {
        ctx.font = '8px Arial';
        ctx.fillText(table.current_reservation.customer_name, x + width/2, y + height + 28);
    }
}

function getZoneLabel(table) {
    const label = (table.name || table.table_name || '').toString().trim();
    const first = label.startsWith('Table') ? 'T' : label.charAt(0).toUpperCase();
    if (first === 'W') return 'Window';
    if (first === 'C') return 'Corner';
    return 'Main Dining';
}

function handleCanvasClick(event) {
    const rect = canvas.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    
    // Check if clicked on a table
    const clickedTable = findTableAtPosition(x, y);
    if (clickedTable) {
        showTableDetails(clickedTable);
    }
}

function handleCanvasMouseMove(event) {
    const rect = canvas.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    
    const table = findTableAtPosition(x, y);
    canvas.style.cursor = table ? 'pointer' : 'crosshair';
}

function findTableAtPosition(x, y) {
    // account for current scale for hit detection
    const transform = ctx.getTransform();
    const scale = transform.a || 1;
    const invScale = scale ? 1/scale : 1;
    const scaledX = x * invScale;
    const scaledY = y * invScale;
    return seatingData.find(table => {
        const tableX = table.position?.x || 100;
        const tableY = table.position?.y || 100;
        const width = TABLE_WIDTH;
        const height = TABLE_HEIGHT;
        return scaledX >= tableX && scaledX <= tableX + width && scaledY >= tableY && scaledY <= tableY + height;
    });
}

function showTableDetails(table) {
    // Create a modal or tooltip to show table details
    const modal = new bootstrap.Modal(document.getElementById('tableDetailsModal'));
    // Populate modal with table data
    // Show modal
}

function refreshSeatingData() {
    const date = document.getElementById('reservationDate')?.value || '{{ $selectedDate ?? now()->toDateString() }}';
    const url = `/wtg/newproject/public/staff/seating/data?restaurant_id={{ $restaurant->id }}&date=${encodeURIComponent(date)}&ts=${Date.now()}`;
    fetch(url, { cache: 'no-store', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(data => {
            seatingData = data.tables;
            applyAutoLayoutIfMissing();
            drawSeatingLayout();
            updateTableStatusGrid();
            populateTableSelect();
            updateStatusCounts();
            updateConnectionStatus('online');
        })
        .catch(() => updateConnectionStatus('offline'));
}

function onDateChange() {
    const url = new URL(window.location.href);
    const date = document.getElementById('reservationDate')?.value;
    if (date) url.searchParams.set('date', date);
    // Full reload so server-rendered reservations list matches the date
    window.location.href = url.toString();
}

function updateTableStatusGrid() {
    // Update the table status cards and button groups
    seatingData.forEach(table => {
        const card = document.querySelector(`[data-table-id="${table.id}"]`);
        if (card) {
            // Update the status badge
            const badge = card.querySelector('.badge');
            if (badge) {
                badge.className = `badge bg-${getStatusColor(table.status)}`;
                badge.textContent = table.status.charAt(0).toUpperCase() + table.status.slice(1);
            }
            
            // Update button group to reflect active status
            const btnGroup = card.querySelector('.btn-group');
            if (btnGroup) {
                const buttons = btnGroup.querySelectorAll('button');
                buttons.forEach(btn => {
                    const statusMatch = btn.getAttribute('onclick')?.match(/'([^']+)'/);
                    if (statusMatch) {
                        const btnStatus = statusMatch[1];
                        const isActive = table.status === btnStatus;
                        
                        // Remove all color classes
                        btn.classList.remove('btn-primary', 'btn-warning', 'btn-danger', 'btn-secondary',
                                           'btn-outline-primary', 'btn-outline-warning', 'btn-outline-danger', 'btn-outline-secondary', 'active');
                        
                        // Get the color for this status
                        const colorMap = {
                            'available': 'primary',
                            'reserved': 'warning',
                            'occupied': 'danger',
                            'maintenance': 'secondary'
                        };
                        const color = colorMap[btnStatus] || 'secondary';
                        
                        // Apply correct class based on active state
                        if (isActive) {
                            btn.classList.add(`btn-${color}`, 'active');
                            btn.disabled = true;
                        } else {
                            btn.classList.add(`btn-outline-${color}`);
                            btn.disabled = false;
                        }
                    }
                });
            }
        }
    });
    
    // Update Live Status counts
    updateStatusCounts();
    // Redraw the canvas
    drawSeatingLayout();
}

function updateStatusCounts() {
    if (!Array.isArray(seatingData)) return;
    const counts = {
        available: seatingData.filter(t => t.status === 'available').length,
        reserved: seatingData.filter(t => t.status === 'reserved').length,
        occupied: seatingData.filter(t => t.status === 'occupied').length,
        maintenance: seatingData.filter(t => t.status === 'maintenance').length
    };
    const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    setText('available-count', counts.available);
    setText('reserved-count', counts.reserved);
    setText('occupied-count', counts.occupied);
    setText('maintenance-count', counts.maintenance);
}

function updateConnectionStatus(status) {
    const indicator = document.getElementById('connection-status');
    if (!indicator) return;
    indicator.textContent = status === 'online' ? 'Online' : 'Offline';
    indicator.className = `badge bg-${status === 'online' ? 'success' : 'danger'}`;
}

// ------- Walk-in Queue logic -------
function refreshQueue() {
    const url = "{{ route('staff.queue.index') }}" + `?restaurant_id={{ $restaurant->id }}&ts=${Date.now()}`;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            const q = data.queue || {};
            setQueueText('queue-waiting-count', q.waiting_count ?? 0);
            setQueueText('queue-current-token', q.current_token ?? '—');
            setQueueText('queue-estimate', (q.estimate_minutes ?? 0) + 'm');
            setQueueText('queue-current-name', (q.current && q.current.name) ? q.current.name : '—');
            setQueueText('queue-current-size', (q.current && q.current.size) ? q.current.size : '—');
        })
        .catch(() => {});
}

function setQueueText(id, val) { const el = document.getElementById(id); if (el) el.textContent = val; }

function enqueueWalkIn() {
    const form = document.getElementById('enqueueForm');
    const formData = new FormData(form);
    fetch("{{ route('staff.queue.enqueue') }}", {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        body: formData
    })
    .then(async r => ({ ok: r.ok, data: await r.json() }))
    .then(({ ok, data }) => {
        if (!ok || !data.success) throw new Error(data.message || 'Failed to enqueue');
        form.reset();
        refreshQueue();
        showAlert('success', `Token issued: ${data.entry.token_number}`);
    })
    .catch(err => showAlert('error', err.message || 'Failed to add walk-in'));
}

function callNext() {
    const payload = new FormData();
    payload.append('restaurant_id', '{{ $restaurant->id }}');
    fetch("{{ route('staff.queue.callNext') }}", {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        body: payload
    })
    .then(async r => ({ ok: r.ok, data: await r.json() }))
    .then(({ ok, data }) => {
        if (!ok || !data.success) throw new Error(data.message || 'No waiting parties');
        refreshQueue();
        showAlert('success', `Calling ${data.entry.customer_name} (Token ${data.entry.token_number})`);
    })
    .catch(err => showAlert('error', err.message || 'Failed to call next'));
}

// Initial queue fetch
document.addEventListener('DOMContentLoaded', refreshQueue);

function seatCurrentWalkIn() {
    const tableId = document.getElementById('queue-table-select')?.value;
    if (!tableId) { showAlert('error', 'Choose an available table'); return; }

    // Fetch current queue snapshot
    const url = "{{ route('staff.queue.index') }}" + `?restaurant_id={{ $restaurant->id }}&ts=${Date.now()}`;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error('Unable to read queue');
            const q = data.queue || {};
            const waiting = Array.isArray(q.waiting) ? q.waiting : [];
            // Prefer the explicitly called current entry; otherwise seat first waiting
            const entryId = (q.current && q.current.id) ? q.current.id : (waiting[0]?.id);
            if (!entryId) throw new Error('No walk-in waiting to seat');

            const form = new FormData();
            form.append('id', entryId);
            form.append('table_id', tableId);

            return fetch("{{ route('staff.queue.seatToTable') }}", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: form
            });
        })
        .then(async r => ({ ok: r.ok, data: await r.json() }))
        .then(({ ok, data }) => {
            if (!ok || !data.success) throw new Error(data.message || 'Failed to seat walk-in');
            refreshSeatingData();
            refreshQueue();
            showAlert('success', data.message || 'Walk-in seated');
        })
        .catch(err => showAlert('error', err.message || 'Failed to seat walk-in'));
}

// Rebuild the table dropdown options from latest seatingData so no manual page refresh is needed
function populateTableSelect() {
    const tableSelect = document.getElementById('tableSelect');
    if (!tableSelect) return;

    const previousValue = tableSelect.value;
    tableSelect.innerHTML = '<option value="">Choose a table...</option>';

    seatingData.forEach(t => {
        const option = document.createElement('option');
        option.value = t.id;
        option.setAttribute('data-capacity', t.capacity);
        const tableLabel = (t.name || t.table_name || `Table ${t.id}`);
        option.textContent = `${tableLabel} (Capacity: ${t.capacity}) - ${t.status.charAt(0).toUpperCase() + t.status.slice(1)}`;
        if (t.status !== 'available') option.disabled = true;
        tableSelect.appendChild(option);
    });

    // Restore selection if still valid
    if (previousValue && Array.from(tableSelect.options).some(o => o.value === previousValue)) {
        tableSelect.value = previousValue;
    }

    // Populate the walk-in seating select with available tables
    const queueSeatSelect = document.getElementById('queue-table-select');
    if (queueSeatSelect) {
        const prev = queueSeatSelect.value;
        queueSeatSelect.innerHTML = '<option value="">Choose available table...</option>';
        seatingData.forEach(t => {
            if (t.status === 'available') {
                const opt = document.createElement('option');
                opt.value = t.id;
                const label = (t.name || t.table_name || `Table ${t.id}`);
                opt.textContent = `${label} (Cap: ${t.capacity})`;
                queueSeatSelect.appendChild(opt);
            }
        });
        if (prev && Array.from(queueSeatSelect.options).some(o => o.value === prev)) {
            queueSeatSelect.value = prev;
        }
    }
}

function getStatusColor(status) {
    switch(status) {
        case 'available': return 'success';
        case 'reserved': return 'warning';
        case 'occupied': return 'danger';
        case 'maintenance': return 'secondary';
        default: return 'secondary';
    }
}

function updateTableStatus(tableId, status) {
    const selected = document.getElementById('reservationDate')?.value || '{{ $selectedDate ?? now()->toDateString() }}';
    if (selected !== APP_TODAY) {
        showAlert('error', 'Status changes are only allowed for today. Switch the date to today to update table status.');
        return;
    }

    const url = "{{ route('staff.seating.updateStatus') }}" + "?restaurant_id={{ $restaurant->id }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Optimistic UI update BEFORE fetch
    const t = Array.isArray(seatingData) ? seatingData.find(x => Number(x.id) === Number(tableId)) : null;
    if (t) {
        const oldStatus = t.status;
        t.status = status;
        updateTableStatusGrid();
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ table_id: tableId, status })
        })
        .then(async r => {
            const raw = await r.text();
            let data; try { data = JSON.parse(raw); } catch { data = { success: false, message: raw || 'Invalid response' }; }
            if (!r.ok) throw new Error(data?.message || `HTTP ${r.status}`);
            return data;
        })
        .then(data => {
            if (data.success) {
                showAlert('success', data.message || 'Table status updated.');
                // DON'T call refreshSeatingData - optimistic update is already done
            } else {
                // Revert on failure
                t.status = oldStatus;
                updateTableStatusGrid();
                showAlert('error', data.message || 'Failed to update table status');
            }
        })
        .catch(err => {
            console.error('Error updating table status:', err);
            // Revert on error
            t.status = oldStatus;
            updateTableStatusGrid();
            showAlert('error', 'Failed to update table status: ' + (err.message || 'network error'));
        });
    }
}

function assignTable(reservationId) {
    // Check if selected reservation is assignable (not completed/cancelled)
    const row = document.querySelector(`[data-reservation-id="${reservationId}"]`);
    const statusBadge = row?.querySelector('td:nth-child(5) .badge');
    const status = statusBadge ? statusBadge.textContent.trim().toLowerCase() : '';
    if (['completed','cancelled'].includes(status)) {
        showAlert('error', `Cannot assign a table to a ${status} reservation.`);
        return;
    }
    // Show assign table modal with pre-selected reservation
    document.getElementById('reservationSelect').value = reservationId;
    const modal = new bootstrap.Modal(document.getElementById('assignTableModal'));
    modal.show();
}

function reassignTable(reservationId) {
    const row = document.querySelector(`[data-reservation-id="${reservationId}"]`);
    const statusBadge = row?.querySelector('td:nth-child(5) .badge');
    const status = statusBadge ? statusBadge.textContent.trim().toLowerCase() : '';
    if (['completed','cancelled'].includes(status)) {
        showAlert('error', `Cannot reassign a table for a ${status} reservation.`);
        return;
    }
    const modal = new bootstrap.Modal(document.getElementById('assignTableModal'));
    document.getElementById('reservationSelect').value = reservationId;
    modal.show();
}

// Force fresh data every time
function refreshSeatingData() {
    const date = document.getElementById('reservationDate')?.value || '{{ $selectedDate ?? now()->toDateString() }}';
    const url = `/wtg/newproject/public/staff/seating/data?restaurant_id={{ $restaurant->id }}&date=${encodeURIComponent(date)}&ts=${Date.now()}`;
    fetch(url, { cache: 'no-store', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(data => {
            seatingData = data.tables;
            applyAutoLayoutIfMissing();
            drawSeatingLayout();
            updateTableStatusGrid();
            populateTableSelect();
            updateStatusCounts();
            updateConnectionStatus('online');
        })
        .catch(() => updateConnectionStatus('offline'));
}

// Assign/Reassign via fetch (prevent full page navigation and update the row instantly)
function submitTableAssignment() {
    const reservationId = document.getElementById('reservationSelect').value;
    const tableSelect = document.getElementById('tableSelect');
    const tableId = tableSelect.value;
    const btn = document.getElementById('assignTableBtn');

    const reservationOption = document.querySelector(`#reservationSelect option[value="${reservationId}"]`);
    const hasExistingTable = reservationOption && reservationOption.getAttribute('data-has-table') === 'true';

    if (!reservationId || !tableId) {
        showAlert('error', 'Please select both reservation and table');
        return;
    }

    const row = document.querySelector(`[data-reservation-id="${reservationId}"]`);
    const statusBadge = row?.querySelector('td:nth-child(5) .badge');
    const status = statusBadge ? statusBadge.textContent.trim().toLowerCase() : '';
    if (['completed','cancelled'].includes(status)) {
        showAlert('error', `Cannot assign/reassign a table for a ${status} reservation.`);
        return;
    }

    const endpoint = hasExistingTable ? 'reassign' : 'assign';
    const actionUrl = `${ASSIGN_URL_BASE}/${reservationId}/${endpoint}?restaurant_id={{ $restaurant->id }}`;

    // Send both keys so the controller accepts either signature
    const payload = { table_id: tableId, new_table_id: tableId };

    if (btn) { btn.disabled = true; btn.innerText = hasExistingTable ? 'Reassigning...' : 'Assigning...'; }
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    fetch(actionUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    })
    .then(async r => {
        const raw = await r.text();
        let data; try { data = JSON.parse(raw); } catch { data = { success: false, message: raw || 'Invalid response' }; }
        if (!r.ok) throw new Error(data?.message || `HTTP ${r.status}`);
        return data;
    })
    .then(data => {
        if (!data.success) throw new Error(data.message || 'Operation failed');

        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('assignTableModal'))?.hide();

        // Optimistically update the reservations table row "Table" cell and button to Reassign
        if (row) {
            const tableCell = row.querySelector('td:nth-child(4)');
            if (tableCell) {
                const label = tableSelect.options[tableSelect.selectedIndex]?.text?.split('(')[0].trim() || `Table ${tableId}`;
                tableCell.innerHTML = `<span class="badge bg-info">${label}</span>`;
            }
            const actions = row.querySelector('.btn-group');
            if (actions) {
                const assignBtn = actions.querySelector('button.btn-outline-primary');
                if (assignBtn) {
                    assignBtn.classList.remove('btn-outline-primary');
                    assignBtn.classList.add('btn-outline-warning');
                    assignBtn.innerHTML = `<i class="fas fa-exchange-alt"></i> Reassign`;
                    assignBtn.setAttribute('onclick', `reassignTable(${reservationId})`);
                }
            }
        }

        // Mark the option as having a table
        if (reservationOption) reservationOption.setAttribute('data-has-table', 'true');

        refreshSeatingData(); // cache-busted, will update Live Status and map
        showAlert('success', data.message || 'Table assigned.');
    })
    .catch(err => {
        console.error('Assign/Reassign failed:', err);
        showAlert('error', err.message || 'Failed to assign/reassign table.');
    })
    .finally(() => {
        if (btn) { btn.disabled = false; btn.innerText = 'Assign Table'; }
    });
}

function resetView() {
    // Reset canvas view to show all tables
    canvas.style.transform = 'scale(1)';
    canvas.style.transformOrigin = '0 0';
}

function showAlert(type, message) {
    console.log('showAlert called:', type, message);
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.style.cssText = `
        white-space: normal !important;
        word-wrap: break-word !important;
        min-height: auto !important;
        overflow: visible !important;
        max-width: 100% !important;
        padding: 15px !important;
        margin: 10px 0 !important;
        font-size: 14px !important;
        line-height: 1.4 !important;
        display: block !important;
        position: relative !important;
        z-index: 1050 !important;
    `;
    alertDiv.innerHTML = `
        <div style="white-space: normal; word-wrap: break-word; max-width: 100%;">
            <strong>Error Details:</strong><br>
            ${message}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" style="position: absolute; top: 10px; right: 10px;"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    if (container) {
        // Remove any existing alerts first
        const existingAlerts = container.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        container.insertBefore(alertDiv, container.firstChild);
        console.log('Alert added to container with full visibility');
    } else {
        console.error('Container not found!');
        // Fallback: add to body
        document.body.insertBefore(alertDiv, document.body.firstChild);
    }
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush

