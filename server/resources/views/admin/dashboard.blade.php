@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-value" id="totalEmployees">--</div>
            <div class="stat-label">Total Employees</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-value text-success" id="onlineEmployees">--</div>
            <div class="stat-label">Online Now</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-value text-danger" id="offlineEmployees">--</div>
            <div class="stat-label">Offline</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stat-value text-info" id="locationsToday">--</div>
            <div class="stat-label">Locations Today</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <h5>Live Location Map</h5>
            <div class="btn-group" role="group">
                <input type="checkbox" class="btn-check" id="activeOnlyCheck" autocomplete="off">
                <label class="btn btn-outline-primary btn-sm" for="activeOnlyCheck">Show Active Only</label>
            </div>
        </div>
        <div id="map"></div>
    </div>
    
    <div class="col-md-4">
        <h5 class="mb-3">Employees</h5>
        <div class="employee-list" id="employeeList">
            <div class="text-center text-muted py-4">Loading...</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Configuration
    const REFRESH_INTERVAL = {{ config('tracking.map_auto_refresh_seconds', 30) }} * 1000;
    let map, markers = {}, employeeData = [];
    let refreshCountdown = REFRESH_INTERVAL / 1000;
    let refreshTimer, countdownTimer;

    // Initialize map
    function initMap() {
        map = L.map('map').setView([37.7749, -122.4194], 10);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
    }

    // Fetch stats
    async function fetchStats() {
        try {
            const response = await fetch('/api/admin/stats');
            const result = await response.json();
            
            if (result.success) {
                document.getElementById('totalEmployees').textContent = result.data.total_employees;
                document.getElementById('onlineEmployees').textContent = result.data.online_employees;
                document.getElementById('offlineEmployees').textContent = result.data.offline_employees;
                document.getElementById('locationsToday').textContent = result.data.locations_today;
            }
        } catch (error) {
            console.error('Error fetching stats:', error);
        }
    }

    // Fetch locations
    async function fetchLocations() {
        const activeOnly = document.getElementById('activeOnlyCheck').checked;
        
        try {
            const response = await fetch(`/api/admin/locations/latest?active_only=${activeOnly ? '1' : '0'}`);
            const result = await response.json();
            
            if (result.success) {
                employeeData = result.data;
                updateMap();
                updateEmployeeList();
                flashRefreshIndicator();
            }
        } catch (error) {
            console.error('Error fetching locations:', error);
        }
    }

    // Update map markers
    function updateMap() {
        // Clear existing markers
        Object.values(markers).forEach(marker => map.removeLayer(marker));
        markers = {};

        if (employeeData.length === 0) {
            return;
        }

        // Add markers for each employee
        const bounds = [];
        
        employeeData.forEach(employee => {
            const icon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${employee.is_online ? '#28a745' : '#dc3545'}; 
                             width: 20px; height: 20px; border-radius: 50%; 
                             border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            const marker = L.marker([employee.lat, employee.lng], { icon })
                .bindPopup(`
                    <div style="min-width: 200px;">
                        <strong>${employee.name}</strong><br>
                        <small class="text-muted">${employee.email}</small><br>
                        <hr style="margin: 5px 0;">
                        <strong>Last Update:</strong> ${employee.last_seen || 'Unknown'}<br>
                        <strong>Accuracy:</strong> ${employee.accuracy ? employee.accuracy + 'm' : 'N/A'}<br>
                        ${employee.battery ? `<strong>Battery:</strong> ${employee.battery}%<br>` : ''}
                        <strong>Status:</strong> 
                        <span class="badge ${employee.is_online ? 'bg-success' : 'bg-danger'}">
                            ${employee.is_online ? 'Online' : 'Offline'}
                        </span>
                    </div>
                `);
            
            marker.addTo(map);
            markers[employee.employee_id] = marker;
            bounds.push([employee.lat, employee.lng]);
        });

        // Fit map to show all markers
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }

    // Update employee list
    function updateEmployeeList() {
        const listContainer = document.getElementById('employeeList');
        
        if (employeeData.length === 0) {
            listContainer.innerHTML = '<div class="text-center text-muted py-4">No employees found</div>';
            return;
        }

        listContainer.innerHTML = employeeData.map(employee => `
            <div class="employee-item" onclick="focusEmployee(${employee.employee_id})">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="${employee.is_online ? 'online-badge' : 'offline-badge'}"></span>
                        <strong>${employee.name}</strong>
                    </div>
                    ${employee.battery ? `<span class="badge bg-secondary">${employee.battery}%</span>` : ''}
                </div>
                <div class="last-update">${employee.last_seen || 'Never'}</div>
            </div>
        `).join('');
    }

    // Focus on employee marker
    function focusEmployee(employeeId) {
        const marker = markers[employeeId];
        if (marker) {
            map.setView(marker.getLatLng(), 15);
            marker.openPopup();
        }
    }

    // Flash refresh indicator
    function flashRefreshIndicator() {
        const indicator = document.getElementById('refreshIndicator');
        indicator.style.color = '#ffc107';
        setTimeout(() => {
            indicator.style.color = '#28a745';
        }, 300);
    }

    // Countdown timer
    function startCountdown() {
        refreshCountdown = REFRESH_INTERVAL / 1000;
        
        countdownTimer = setInterval(() => {
            refreshCountdown--;
            document.getElementById('refreshCountdown').textContent = refreshCountdown;
            
            if (refreshCountdown <= 0) {
                refreshCountdown = REFRESH_INTERVAL / 1000;
            }
        }, 1000);
    }

    // Start auto-refresh
    function startAutoRefresh() {
        fetchLocations();
        fetchStats();
        
        refreshTimer = setInterval(() => {
            fetchLocations();
            fetchStats();
        }, REFRESH_INTERVAL);
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        startAutoRefresh();
        startCountdown();

        // Handle active only checkbox
        document.getElementById('activeOnlyCheck').addEventListener('change', function() {
            fetchLocations();
        });
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        clearInterval(refreshTimer);
        clearInterval(countdownTimer);
    });
</script>
@endsection
