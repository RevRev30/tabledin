@extends('layouts.app')

@section('title', 'Advanced Seating Management')

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
                            <option value="{{ $r->id }}" {{ ($restaurant->id ?? null) === $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
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
                        <a class="nav-link" href="#queue">
                            <i class="fas fa-users"></i> Walk-in Queue
                        </a>
                    </li>
                </ul>

                <!-- Real-time Status -->
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Live Status</span>
                    <span class="badge bg-success" id="connection-status">Online</span>
                </h6>
                <div class="px-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small>Available:</small>
                        <span class="badge bg-success" id="available-count">{{ $tables->where('status', 'available')->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Reserved:</small>
                        <span class="badge bg-warning" id="reserved-count">{{ $tables->where('status', 'reserved')->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Occupied:</small>
                        <span class="badge bg-danger" id="occupied-count">{{ $tables->where('status', 'occupied')->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Maintenance:</small>
                        <span class="badge bg-secondary" id="maintenance-count">{{ $tables->where('status', 'maintenance')->count() }}</span>
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
                    <button class="btn btn-sm btn-outline-warning w-100 mb-2" onclick="toggleEditMode()">
                        <i class="fas fa-edit"></i> Edit Layout
                    </button>
                    <button class="btn btn-sm btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#layoutSettingsModal">
                        <i class="fas fa-cog"></i> Settings
                    </button>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Advanced Seating Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0 align-items-center">
                    <div class="input-group input-group-sm me-2" style="width: 220px;">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <input type="date" id="reservationDate" class="form-control" value="{{ $selectedDate ?? now()->toDateString() }}" onchange="onDateChange()">
                    </div>
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshSeatingData()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAutoRefresh()">
                            <i class="fas fa-clock"></i> Auto Refresh
                        </button>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('staff.seating.index', ['restaurant_id' => $restaurant->id ?? null]) }}" class="btn btn-sm btn-outline-secondary">Basic View</a>
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#assignTableModal">
                            <i class="fas fa-plus"></i> Assign Table
                        </button>
                    </div>
                </div>
            </div>

            <!-- Seating Map Section -->
            <div id="seating-map" class="mb-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Restaurant Layout - Real-time View</h5>
                        <div class="d-flex gap-2">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" onclick="setViewMode('normal')">Normal</button>
                                <button type="button" class="btn btn-outline-primary" onclick="setViewMode('compact')">Compact</button>
                                <button type="button" class="btn btn-outline-primary" onclick="setViewMode('detailed')">Detailed</button>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" onclick="resetView()">
                                <i class="fas fa-expand-arrows-alt"></i> Reset View
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="seating-canvas-container" style="position: relative; overflow: auto; min-height: 600px; background: #f8f9fa;">
                            <canvas id="seating-canvas" width="800" height="600" style="border: 1px solid #ddd; background: #f8f9fa; cursor: crosshair; width: 100%;"></canvas>
                            
                            <!-- Canvas overlay for interactive elements -->
                            <div id="canvas-overlay" style="position: absolute; top: 0; left: 0; pointer-events: none; z-index: 10;"></div>
                            
                            <!-- Table info tooltip -->
                            <div id="table-tooltip" class="position-absolute bg-dark text-white p-2 rounded shadow" style="display: none; z-index: 20; max-width: 200px;">
                                <div id="tooltip-content"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Reservations Section -->
            <div id="reservations" class="mb-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Reservations ({{ \Carbon\Carbon::parse($selectedDate ?? now()->toDateString())->format('M d, Y') }})</h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="timeFilter" onchange="filterReservations()">
                                <option value="all">All Times</option>
                                <option value="morning">Morning (6AM-12PM)</option>
                                <option value="afternoon">Afternoon (12PM-6PM)</option>
                                <option value="evening">Evening (6PM-12AM)</option>
                            </select>
                            <button class="btn btn-sm btn-outline-primary" onclick="exportReservations()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="reservations-table">
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
                                <tbody>
                                    @foreach($todayReservations as $reservation)
                                    <tr data-reservation-id="{{ $reservation->id }}" data-time="{{ $reservation->reservation_time ? \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') : 'No time' }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-clock me-2 text-muted"></i>
                                                {{ $reservation->reservation_time ? \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') : 'No time' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $reservation->customer?->name ?? 'Unknown User' }}</strong>
                                                <br><small class="text-muted">{{ $reservation->customer?->email ?? 'No email' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $reservation->number_of_guests }} guests</span>
                                        </td>
                                        <td>
                                            @if($reservation->table)
                                                <span class="badge bg-success">{{ $reservation->table?->table_name ?? 'No Table' }}</span>
                                                <br><small class="text-muted">Capacity: {{ $reservation->table->capacity }}</small>
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
                                                @if(!$reservation->table)
                                                    <button class="btn btn-outline-primary" onclick="assignTable({{ $reservation->id }})" title="Assign Table">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-warning" onclick="reassignTable({{ $reservation->id }})" title="Reassign Table">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-outline-info" onclick="viewReservation({{ $reservation->id }})" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-success" onclick="checkInReservation({{ $reservation->id }})" title="Check In">
                                                    <i class="fas fa-check"></i>
                                                </button>
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

            <!-- Walk-in Queue Section -->
            <div id="queue" class="mb-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Walk-in Queue</h5>
                        <button class="btn btn-sm btn-primary" onclick="addWalkIn()">
                            <i class="fas fa-plus"></i> Add Walk-in
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row" id="queue-list">
                            <!-- Queue items will be populated here -->
                            <div class="col-12 text-center text-muted py-4">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <p>No walk-ins in queue</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Assign Table Modal -->
<div class="modal fade" id="assignTableModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Table to Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignTableForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reservationSelect" class="form-label">Select Reservation</label>
                                <select class="form-select" id="reservationSelect" required>
                                    <option value="">Choose a reservation...</option>
                                    @foreach($todayReservations->where('table_id', null) as $reservation)
                                    <option value="{{ $reservation->id }}" data-guests="{{ $reservation->number_of_guests }}">
                                        {{ $reservation->customer?->name ?? 'Unknown User' }} - {{ $reservation->reservation_time ? \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') : 'No time' }} ({{ $reservation->number_of_guests }} guests)
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tableSelect" class="form-label">Select Table</label>
                                <select class="form-select" id="tableSelect" required>
                                    <option value="">Choose a table...</option>
                                    @foreach($tables as $table)
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
                                        @if($computedStatus === 'available')
                                    <option value="{{ $table->id }}" data-capacity="{{ $table->capacity }}" data-zone="{{ $table->seatingZone?->zone_name }}">
                                        {{ $table->table_name }} (Capacity: {{ $table->capacity }}) - {{ $table->seatingZone?->zone_name }}
                                    </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Smart Assignment:</strong> The system will automatically filter tables based on the number of guests and show only suitable options.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitTableAssignment()">Assign Table</button>
            </div>
        </div>
    </div>
</div>

<!-- Layout Settings Modal -->
<div class="modal fade" id="layoutSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Layout Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="layoutSettingsForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="layoutName" class="form-label">Layout Name</label>
                                <input type="text" class="form-control" id="layoutName" value="{{ $layout?->layout_name ?? 'Main Dining' }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="layoutWidth" class="form-label">Width (px)</label>
                                <input type="number" class="form-control" id="layoutWidth" value="{{ $layout?->width ?? 800 }}" min="400" max="2000" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="layoutHeight" class="form-label">Height (px)</label>
                                <input type="number" class="form-control" id="layoutHeight" value="{{ $layout?->height ?? 600 }}" min="300" max="1500" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="autoRefreshInterval" class="form-label">Auto Refresh Interval (seconds)</label>
                                <select class="form-select" id="autoRefreshInterval">
                                    <option value="10">10 seconds</option>
                                    <option value="30" selected>30 seconds</option>
                                    <option value="60">1 minute</option>
                                    <option value="300">5 minutes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="defaultViewMode" class="form-label">Default View Mode</label>
                                <select class="form-select" id="defaultViewMode">
                                    <option value="normal" selected>Normal</option>
                                    <option value="compact">Compact</option>
                                    <option value="detailed">Detailed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveLayoutSettings()">Save Settings</button>
            </div>
        </div>
    </div>
</div>

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

#seating-canvas {
    cursor: crosshair;
    transition: all 0.3s ease;
}

#seating-canvas.edit-mode {
    cursor: move;
}

#seating-canvas:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.table-element {
    position: absolute;
    border: 2px solid #333;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s;
    border-radius: 4px;
}

.table-element:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    z-index: 5;
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

.zone-overlay {
    position: absolute;
    border: 2px dashed #007bff;
    background: rgba(0, 123, 255, 0.1);
    pointer-events: none;
    border-radius: 8px;
}

.queue-item {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    transition: transform 0.2s;
}

.queue-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.connection-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 5px;
}

.connection-indicator.online {
    background-color: #28a745;
    animation: pulse 2s infinite;
}

.connection-indicator.offline {
    background-color: #dc3545;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.view-mode-btn.active {
    background-color: #007bff;
    color: white;
}
</style>
@endpush

@push('scripts')
<script>
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
let autoRefreshInterval = null;
let autoRefreshEnabled = false;
let viewMode = 'normal';
let connectionStatus = 'online';

// Initialize the seating canvas
document.addEventListener('DOMContentLoaded', function() {
    canvas = document.getElementById('seating-canvas');
    ctx = canvas.getContext('2d');
    
    // Set canvas size to container and auto-fit
    function resizeCanvas() {
        const container = document.getElementById('seating-canvas-container');
        const containerWidth = container ? container.clientWidth : window.innerWidth - 100;
        const availableHeight = window.innerHeight - (container?.getBoundingClientRect().top || 0) - 100;
        canvas.width = Math.max(containerWidth, 800);
        canvas.height = Math.max(availableHeight, 600);
        drawSeatingLayout();
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();
    
    // Add canvas event listeners
    canvas.addEventListener('click', handleCanvasClick);
    canvas.addEventListener('mousemove', handleCanvasMouseMove);
    canvas.addEventListener('mouseleave', hideTooltip);
    
    // Initialize auto refresh
    startAutoRefresh();
    
    // Initialize smart table filtering
    initializeSmartFiltering();
});

function drawSeatingLayout() {
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Draw background
    ctx.fillStyle = '#f8f9fa';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Draw zones if in detailed mode
    if (viewMode === 'detailed') {
        drawZones();
    }
    
    // Draw tables
    seatingData.forEach(table => {
        drawTable(table);
    });
}

function drawZones() {
    // Draw seating zones
    const zones = @json($zones);
    zones.forEach(zone => {
        const coords = zone.zone_coordinates;
        if (coords && coords.width && coords.height) {
            ctx.fillStyle = zone.zone_color + '40';
            ctx.fillRect(coords.x, coords.y, coords.width, coords.height);
            
            ctx.strokeStyle = zone.zone_color;
            ctx.lineWidth = 2;
            ctx.setLineDash([5, 5]);
            ctx.strokeRect(coords.x, coords.y, coords.width, coords.height);
            ctx.setLineDash([]);
            
            // Draw zone name
            ctx.fillStyle = '#333';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(zone.zone_name, coords.x + coords.width/2, coords.y + 20);
        }
    });
}

function drawTable(table) {
    const x = table.position?.x || 100;
    const y = table.position?.y || 100;
    const width = viewMode === 'compact' ? 60 : 80;
    const height = viewMode === 'compact' ? 45 : 60;
    
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
    ctx.font = viewMode === 'compact' ? '10px Arial' : '12px Arial';
    ctx.textAlign = 'center';
    ctx.fillText(table.name, x + width/2, y + height/2 - 5);
    
    // Draw capacity
    ctx.font = viewMode === 'compact' ? '8px Arial' : '10px Arial';
    ctx.fillText(`(${table.capacity})`, x + width/2, y + height/2 + 10);
    
    // Draw reservation info if exists and in detailed mode
    if (table.current_reservation && viewMode === 'detailed') {
        ctx.font = '8px Arial';
        ctx.fillText(table.current_reservation.customer_name, x + width/2, y + height + 15);
    }
    
    // Draw status indicator
    ctx.fillStyle = color;
    ctx.beginPath();
    ctx.arc(x + width - 8, y + 8, 4, 0, 2 * Math.PI);
    ctx.fill();
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
    if (table) {
        canvas.style.cursor = 'pointer';
        showTooltip(event, table);
    } else {
        canvas.style.cursor = editMode ? 'move' : 'crosshair';
        hideTooltip();
    }
}

function showTooltip(event, table) {
    const tooltip = document.getElementById('table-tooltip');
    const content = document.getElementById('tooltip-content');
    
    content.innerHTML = `
        <strong>${table.name}</strong><br>
        Capacity: ${table.capacity}<br>
        Status: ${table.status}<br>
        ${table.current_reservation ? `Customer: ${table.current_reservation.customer_name}<br>Time: ${table.current_reservation.time}` : ''}
    `;
    
    tooltip.style.left = (event.clientX + 10) + 'px';
    tooltip.style.top = (event.clientY - 10) + 'px';
    tooltip.style.display = 'block';
}

function hideTooltip() {
    document.getElementById('table-tooltip').style.display = 'none';
}

function findTableAtPosition(x, y) {
    const width = viewMode === 'compact' ? 60 : 80;
    const height = viewMode === 'compact' ? 45 : 60;
    
    return seatingData.find(table => {
        const tableX = table.position?.x || 100;
        const tableY = table.position?.y || 100;
        
        return x >= tableX && x <= tableX + width && y >= tableY && y <= tableY + height;
    });
}

function setViewMode(mode) {
    viewMode = mode;
    
    // Update button states
    document.querySelectorAll('.view-mode-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    drawSeatingLayout();
}

function refreshSeatingData() {
    const date = document.getElementById('reservationDate')?.value || '{{ $selectedDate ?? now()->toDateString() }}';
    fetch(`/wtg/newproject/public/staff/seating/data?restaurant_id={{ $restaurant->id ?? '' }}&date=${encodeURIComponent(date)}`)
        .then(response => response.json())
        .then(data => {
            seatingData = data.tables;
            drawSeatingLayout();
            updateStatusCounts();
            updateReservationsTable();
        })
        .catch(error => {
            console.error('Error refreshing seating data:', error);
            updateConnectionStatus('offline');
        });
}

function onDateChange() {
    const url = new URL(window.location.href);
    const date = document.getElementById('reservationDate')?.value;
    if (date) url.searchParams.set('date', date);
    // Full reload to get server-rendered reservations list for selected date
    window.location.href = url.toString();
}

function updateStatusCounts() {
    const counts = {
        available: seatingData.filter(t => t.status === 'available').length,
        reserved: seatingData.filter(t => t.status === 'reserved').length,
        occupied: seatingData.filter(t => t.status === 'occupied').length,
        maintenance: seatingData.filter(t => t.status === 'maintenance').length
    };
    
    document.getElementById('available-count').textContent = counts.available;
    document.getElementById('reserved-count').textContent = counts.reserved;
    document.getElementById('occupied-count').textContent = counts.occupied;
    document.getElementById('maintenance-count').textContent = counts.maintenance;
}

// Placeholder: server renders the reservations for the selected date;
// we keep this function to avoid console errors when refreshSeatingData runs.
function updateReservationsTable() {
    // In this basic version, the table is server-rendered when the date changes.
}

function updateConnectionStatus(status) {
    connectionStatus = status;
    const indicator = document.getElementById('connection-status');
    indicator.textContent = status === 'online' ? 'Online' : 'Offline';
    indicator.className = `badge bg-${status === 'online' ? 'success' : 'danger'}`;
}

function startAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    
    const interval = parseInt(document.getElementById('autoRefreshInterval')?.value || 30) * 1000;
    autoRefreshInterval = setInterval(() => {
        if (autoRefreshEnabled) {
            refreshSeatingData();
        }
    }, interval);
}

function toggleAutoRefresh() {
    autoRefreshEnabled = !autoRefreshEnabled;
    const button = event.target;
    
    if (autoRefreshEnabled) {
        button.innerHTML = '<i class="fas fa-pause"></i> Stop Auto Refresh';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-warning');
        startAutoRefresh();
    } else {
        button.innerHTML = '<i class="fas fa-clock"></i> Auto Refresh';
        button.classList.remove('btn-warning');
        button.classList.add('btn-outline-primary');
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
    }
}

function initializeSmartFiltering() {
    const reservationSelect = document.getElementById('reservationSelect');
    const tableSelect = document.getElementById('tableSelect');
    
    reservationSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const guestCount = parseInt(selectedOption.dataset.guests || 0);
        
        // Filter tables based on capacity
        Array.from(tableSelect.options).forEach(option => {
            if (option.value === '') return;
            
            const capacity = parseInt(option.dataset.capacity || 0);
            const isSuitable = capacity >= guestCount && capacity <= guestCount + 2; // Allow some flexibility
            
            option.style.display = isSuitable ? 'block' : 'none';
            if (!isSuitable && option.selected) {
                option.selected = false;
            }
        });
    });
}

function updateTableStatus(tableId, status) {
    fetch(`/wtg/newproject/public/staff/seating/tables/${tableId}/status?restaurant_id={{ $restaurant->id ?? '' }}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            refreshSeatingData();
            showAlert('success', data.message);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error updating table status:', error);
        showAlert('error', 'Failed to update table status');
    });
}

function assignTable(reservationId) {
    document.getElementById('reservationSelect').value = reservationId;
    const modal = new bootstrap.Modal(document.getElementById('assignTableModal'));
    modal.show();
}

function submitTableAssignment() {
    const reservationId = document.getElementById('reservationSelect').value;
    const tableId = document.getElementById('tableSelect').value;
    
    if (!reservationId || !tableId) {
        showAlert('error', 'Please select both reservation and table');
        return;
    }
    
    fetch(`/wtg/newproject/public/staff/seating/reservations/${reservationId}/assign?restaurant_id={{ $restaurant->id ?? '' }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ table_id: tableId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('assignTableModal')).hide();
            refreshSeatingData();
            showAlert('success', data.message);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error assigning table:', error);
        showAlert('error', 'Failed to assign table');
    });
}

function filterReservations() {
    const filter = document.getElementById('timeFilter').value;
    const rows = document.querySelectorAll('#reservations-table tbody tr');
    
    rows.forEach(row => {
        const time = row.dataset.time;
        let show = true;
        
        if (filter !== 'all') {
            const hour = parseInt(time.split(':')[0]);
            switch(filter) {
                case 'morning':
                    show = hour >= 6 && hour < 12;
                    break;
                case 'afternoon':
                    show = hour >= 12 && hour < 18;
                    break;
                case 'evening':
                    show = hour >= 18 && hour < 24;
                    break;
            }
        }
        
        row.style.display = show ? '' : 'none';
    });
}

function exportReservations() {
    // Implementation for exporting reservations
    showAlert('info', 'Export feature coming soon!');
}

function addWalkIn() {
    // Implementation for adding walk-in customers
    showAlert('info', 'Walk-in management coming soon!');
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateConnectionStatus('online');
    refreshSeatingData();
});
</script>
@endpush
