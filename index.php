<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced IP Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --background-color: #f8fafc;
            --surface-color: #ffffff;
            --border-color: #e2e8f0;
            --blue-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            --light-blue-gradient: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        }

        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f8fafc 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            margin-top: 20px;
            margin-bottom: 40px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(37, 99, 235, 0.1);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(37, 99, 235, 0.1);
            padding: 24px;
            margin-bottom: 24px;
        }

        .header-card {
            text-align: center;
            background: var(--blue-gradient);
            color: white;
            border: none;
            margin-bottom: 30px;
        }

        .header-card h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .btn-branch {
            margin: 5px;
            min-width: 140px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            background: white;
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-branch:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            background: var(--primary-color);
            color: white;
        }

        .btn-branch.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        .table-container {
            display: none;
        }

        .table-container.show {
            display: block;
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stats-row {
            margin-bottom: 20px;
        }

        .stat-card {
            background: var(--blue-gradient);
            color: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .search-container {
            position: relative;
            margin-bottom: 20px;
        }

        .search-container .fas {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
        }

        .search-input {
            padding-left: 40px;
            border-radius: 12px;
            border: 2px solid var(--border-color);
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.1);
            background: white;
        }

        .table thead {
            background: var(--blue-gradient);
        }

        .table thead th {
            border: none;
            color: white;
            font-weight: 600;
            padding: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .table thead th:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(37, 99, 235, 0.05);
            transform: scale(1.01);
        }

        .ip-address {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: var(--primary-color);
            padding: 4px 8px;
            background: rgba(37, 99, 235, 0.1);
            border-radius: 6px;
        }

        .device-badge {
            background: var(--blue-gradient);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .action-buttons .btn {
            margin: 0 2px;
            border-radius: 8px;
            padding: 6px 12px;
            transition: all 0.3s ease;
        }

        .action-buttons .btn:hover {
            transform: translateY(-1px);
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid var(--border-color);
            transition: border-color 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--blue-gradient);
            border: none;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        .loading-spinner {
            text-align: center;
            padding: 60px 20px;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-color: var(--primary-color);
            border-right-color: transparent;
        }

        .ping-status {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .ping-online {
            background-color: #10b981;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
        }

        .ping-offline {
            background-color: #ef4444;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
        }

        .ping-unknown {
            background-color: #64748b;
            animation: none;
        }

        .ping-testing {
            background-color: #f59e0b;
            box-shadow: 0 0 8px rgba(245, 158, 11, 0.6);
        }

        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.3);
        }

        .modal-header {
            border-bottom: 2px solid var(--border-color);
            border-radius: 16px 16px 0 0;
            background: var(--light-blue-gradient);
        }

        .modal-title {
            color: var(--primary-dark);
            font-weight: 700;
        }

        .export-buttons {
            margin-bottom: 20px;
        }

        .quick-add {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .quick-add-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--blue-gradient);
            border: none;
            color: white;
            font-size: 24px;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
            transition: all 0.3s ease;
        }

        .quick-add-btn:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.6);
        }

        .ip-range-indicator {
            font-size: 0.8rem;
            color: var(--primary-color);
            margin-top: 5px;
            font-weight: 500;
        }

        .connection-error {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            color: #92400e;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .ping-result-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin: 10px 0;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.1);
        }

        .ping-success {
            border-left-color: #10b981;
        }

        .ping-failed {
            border-left-color: #ef4444;
        }

        .response-time {
            font-weight: bold;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .header-card h1 {
                font-size: 2rem;
            }
            
            .btn-branch {
                min-width: 100px;
                margin: 3px;
            }
            
            .quick-add {
                bottom: 20px;
                right: 20px;
            }
        }

        /* Enhanced button styles */
        .btn-outline-info {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-info:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-success {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border: none;
        }

        .btn-outline-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }

        /* Ping status indicators with better visibility */
        .status-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-text {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-online {
            color: #059669;
        }

        .status-offline {
            color: #dc2626;
        }

        .status-unknown {
            color: #64748b;
        }

        .status-testing {
            color: #d97706;
        }
    </style>
</head>
<body>
    <div class="container-fluid main-container">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="glass-card header-card">
                    <h1>
                        <i class="fas fa-network-wired"></i>
                        IP Management System
                    </h1>
                    <p class="mb-0 mt-2">Comprehensive network asset tracking and management</p>
                </div>
            </div>
        </div>

        <!-- Connection Status -->
        <div id="connectionError" class="connection-error" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Connection Error:</strong> Unable to connect to the backend server. Please check your API configuration.
        </div>

        <!-- Statistics Row -->
        <div class="row stats-row" id="statsRow" style="display: none;">
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <span class="stat-number" id="totalIPs">0</span>
                    <span class="stat-label">Total IPs</span>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <span class="stat-number" id="onlineIPs">0</span>
                    <span class="stat-label">Online</span>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <span class="stat-number" id="offlineIPs">0</span>
                    <span class="stat-label">Offline</span>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <span class="stat-number" id="deviceTypes">0</span>
                    <span class="stat-label">Device Types</span>
                </div>
            </div>
        </div>

        <!-- Branch Selection -->
        <div class="row">
            <div class="col-12">
                <div class="glass-card">
                    <h4 class="mb-3" style="color: var(--primary-color);">
                        <i class="fas fa-building"></i> Select Branch
                    </h4>
                    <div id="branchButtons" class="d-flex flex-wrap justify-content-center"></div>
                </div>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="row export-buttons" id="exportSection" style="display: none;">
            <div class="col-12">
                <div class="glass-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" style="color: var(--primary-color);">
                            <i class="fas fa-download"></i> Export Data
                        </h5>
                        <div>
                            <button class="btn btn-outline-success btn-sm me-2" onclick="exportToCSV()">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </button>
                            <button class="btn btn-outline-primary btn-sm me-2" onclick="printReport()">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="pingAllIPs()">
                                <i class="fas fa-broadcast-tower"></i> Ping All
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- IP Table -->
        <div class="row">
            <div class="col-12">
                <div class="glass-card table-container" id="ipTableContainer">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 id="selectedBranchName" class="mb-0" style="color: var(--primary-color);">
                            <i class="fas fa-list"></i> IP Addresses
                        </h4>
                        <button class="btn btn-success" id="showAddFormBtn">
                            <i class="fas fa-plus"></i> Add New IP
                        </button>
                    </div>
                    
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control search-input" id="searchInput" placeholder="Search IPs, devices, or types...">
                    </div>
                    
                    <div id="tableContent">
                        <div class="loading-spinner">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3 text-muted">Loading data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit IP Form -->
        <div class="row">
            <div class="col-12">
                <div class="glass-card" id="addIpForm" style="display: none;">
                    <h5 class="mb-4" style="color: var(--primary-color);">
                        <i class="fas fa-plus"></i> <span id="formTitle">Add New IP/Device</span>
                    </h5>
                    <form id="ipForm">
                        <input type="hidden" id="ipId" name="ipId">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="ipAddress" class="form-label">IP Address</label>
                                <input type="text" class="form-control" id="ipAddress" name="ipAddress" required 
                                       pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$" placeholder="192.168.1.100">
                                <div class="ip-range-indicator" id="ipRangeInfo"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="deviceName" class="form-label">Device Name</label>
                                <input type="text" class="form-control" id="deviceName" name="deviceName" required placeholder="Enter device name">
                            </div>
                            <div class="col-md-4">
                                <label for="deviceType" class="form-label">Device Type</label>
                                <select class="form-select" id="deviceType" name="deviceType" required>
                                    <option value="">Select Device Type</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="branchSelect" class="form-label">Branch</label>
                                <select class="form-select" id="branchSelect" name="branchSelect" required>
                                    <option value="">Select Branch</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="description" class="form-label">Description (Optional)</label>
                                <input type="text" class="form-control" id="description" name="description" placeholder="Additional notes">
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-outline-secondary me-2" id="cancelBtn">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> <span id="submitText">Add IP</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Add Button -->
    <div class="quick-add">
        <button class="quick-add-btn" id="quickAddBtn" title="Quick Add IP">
            <i class="fas fa-plus"></i>
        </button>
    </div>

    <!-- Bootstrap Modal for Ping Test -->
    <div class="modal fade" id="pingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-broadcast-tower"></i> Ping Test Results
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="pingResults">
                    <div class="text-center">
                        <div class="spinner-border" role="status"></div>
                        <p class="mt-2">Testing connectivity...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="retryPingBtn" onclick="retryPing()" style="display: none;">
                        <i class="fas fa-redo"></i> Retry
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Ping Progress Modal -->
    <div class="modal fade" id="bulkPingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-broadcast-tower"></i> Bulk Ping Test
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Progress:</span>
                            <span id="pingProgress">0 / 0</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="pingProgressBar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                    <div id="bulkPingResults" style="max-height: 300px; overflow-y: auto;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <script>
        // Configuration
        const API_BASE_URL = 'api.php'; // Change this to your API endpoint
        
        // Global variables
        let currentBranch = null;
        let currentBranchName = '';
        let allIPs = [];
        let branches = [];
        let deviceTypes = [];
        let pingModal = null;
        let bulkPingModal = null;
        let ipStatusCache = new Map(); // Cache for ping results
        let currentPingIP = null; // For retry functionality
        let bulkPingInProgress = false;

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            pingModal = new bootstrap.Modal(document.getElementById('pingModal'));
            bulkPingModal = new bootstrap.Modal(document.getElementById('bulkPingModal'));
            setupEventListeners();
            initializeApp();
        });

        // Initialize app data
        async function initializeApp() {
            try {
                await loadBranches();
                await loadDeviceTypes();
                hideConnectionError();
            } catch (error) {
                showConnectionError();
                console.error('Failed to initialize app:', error);
            }
        }

        // Setup event listeners
        function setupEventListeners() {
            document.getElementById('ipForm').addEventListener('submit', handleFormSubmit);
            document.getElementById('showAddFormBtn').addEventListener('click', showAddForm);
            document.getElementById('quickAddBtn').addEventListener('click', showAddForm);
            document.getElementById('cancelBtn').addEventListener('click', hideAddForm);
            document.getElementById('searchInput').addEventListener('keyup', filterTable);
            document.getElementById('ipAddress').addEventListener('input', updateIPRangeInfo);
        }

        // API Functions
        async function apiCall(endpoint, options = {}) {
            try {
                const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        ...options.headers
                    },
                    ...options
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return await response.json();
            } catch (error) {
                console.error(`API call failed for ${endpoint}:`, error);
                throw error;
            }
        }

        // Load branches from database
        async function loadBranches() {
            try {
                branches = await apiCall('branches');
                displayBranches(branches);
                populateBranchSelects(branches);
            } catch (error) {
                console.error('Failed to load branches:', error);
                throw error;
            }
        }

        // Load device types from database
        async function loadDeviceTypes() {
            try {
                deviceTypes = await apiCall('device-types');
                populateDeviceTypeSelects(deviceTypes);
            } catch (error) {
                console.error('Failed to load device types:', error);
                throw error;
            }
        }

        // Load IPs for selected branch
        async function loadBranchIPs(branchId, branchName) {
            try {
                document.getElementById('selectedBranchName').innerHTML = 
                    `<i class="fas fa-list"></i> ${branchName} - IP Addresses`;
                
                const container = document.getElementById('ipTableContainer');
                container.classList.add('show');
                
                document.getElementById('tableContent').innerHTML = 
                    '<div class="loading-spinner"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted">Loading data...</p></div>';
                
                allIPs = await apiCall(`ips?branch_id=${branchId}`);
                displayIPTable(allIPs);
                updateStats(allIPs);
                document.getElementById('statsRow').style.display = 'flex';
                document.getElementById('exportSection').style.display = 'block';
                
            } catch (error) {
                console.error('Failed to load IPs:', error);
                document.getElementById('tableContent').innerHTML = 
                    '<div class="text-center py-5 text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><h5>Failed to load IP data</h5></div>';
            }
        }

        // Display branch buttons
        function displayBranches(branches) {
            const container = document.getElementById('branchButtons');
            container.innerHTML = '';
            
            branches.forEach(branch => {
                const button = document.createElement('button');
                button.className = 'btn btn-outline-primary btn-branch';
                button.innerHTML = `<i class="fas fa-building"></i> ${branch.name}`;
                button.onclick = () => selectBranch(branch.id, branch.name, button);
                container.appendChild(button);
            });
        }

        // Select branch and load IPs
        function selectBranch(branchId, branchName, buttonElement) {
            // Update active button
            document.querySelectorAll('.btn-branch').forEach(btn => btn.classList.remove('active'));
            buttonElement.classList.add('active');
            
            currentBranch = branchId;
            currentBranchName = branchName;
            loadBranchIPs(branchId, branchName);
        }

        // Populate branch select elements
        function populateBranchSelects(branches) {
            const selects = ['branchSelect'];
            selects.forEach(selectId => {
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">Select Branch</option>';
                branches.forEach(branch => {
                    const option = document.createElement('option');
                    option.value = branch.id;
                    option.textContent = branch.name;
                    select.appendChild(option);
                });
            });
        }

        // Populate device type select elements
        function populateDeviceTypeSelects(deviceTypes) {
            const selects = ['deviceType'];
            selects.forEach(selectId => {
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">Select Device Type</option>';
                deviceTypes.forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.id;
                    option.textContent = type.name;
                    select.appendChild(option);
                });
            });
        }

        // Update statistics
        function updateStats(ips) {
            const totalIPs = ips.length;
            const onlineIPs = ips.filter(ip => ipStatusCache.get(ip.ip_address) === 'online').length;
            const offlineIPs = ips.filter(ip => ipStatusCache.get(ip.ip_address) === 'offline').length;
            const unknownIPs = totalIPs - onlineIPs - offlineIPs;
            const deviceTypesCount = new Set(ips.map(ip => ip.device_type_name)).size;

            document.getElementById('totalIPs').textContent = totalIPs;
            document.getElementById('onlineIPs').textContent = onlineIPs;
            document.getElementById('offlineIPs').textContent = offlineIPs;
            document.getElementById('deviceTypes').textContent = deviceTypesCount;
        }

        // Display IP table
        function displayIPTable(ips) {
            const tableContent = document.getElementById('tableContent');
            
            if (ips.length === 0) {
                tableContent.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-network-wired fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No IP addresses found</h5>
                        <p class="text-muted">Click "Add New IP" to get started</p>
                    </div>
                `;
                return;
            }

            let tableHTML = `
                <div class="table-responsive">
                    <table class="table table-hover" id="ipTable">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0)">Status <i class="fas fa-sort"></i></th>
                                <th onclick="sortTable(1)">IP Address <i class="fas fa-sort"></i></th>
                                <th onclick="sortTable(2)">Device Name <i class="fas fa-sort"></i></th>
                                <th onclick="sortTable(3)">Device Type <i class="fas fa-sort"></i></th>
                                <th onclick="sortTable(4)">Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            ips.forEach(ip => {
                const cachedStatus = ipStatusCache.get(ip.ip_address);
                let statusClass, statusText, statusIcon;
                
                switch(cachedStatus) {
                    case 'online':
                        statusClass = 'ping-online';
                        statusText = 'Online';
                        statusIcon = 'fas fa-check-circle text-success';
                        break;
                    case 'offline':
                        statusClass = 'ping-offline';
                        statusText = 'Offline';
                        statusIcon = 'fas fa-times-circle text-danger';
                        break;
                    case 'testing':
                        statusClass = 'ping-testing';
                        statusText = 'Testing...';
                        statusIcon = 'fas fa-spinner fa-spin text-warning';
                        break;
                    default:
                        statusClass = 'ping-unknown';
                        statusText = 'Unknown';
                        statusIcon = 'fas fa-question-circle text-muted';
                }
                
                tableHTML += `
                    <tr id="row-${ip.ip_address.replace(/\./g, '-')}">
                        <td>
                            <div class="status-indicator">
                                <span class="ping-status ${statusClass}"></span>
                                <div>
                                    <i class="${statusIcon}"></i>
                                    <span class="status-text ms-1">${statusText}</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="ip-address">${ip.ip_address}</span></td>
                        <td><strong>${ip.device_name}</strong></td>
                        <td><span class="device-badge">${ip.device_type_name}</span></td>
                        <td><small class="text-muted">${ip.description || '-'}</small></td>
                        <td class="action-buttons">
                            <button class="btn btn-sm btn-outline-info" onclick="pingIP('${ip.ip_address}')" title="Ping Test">
                                <i class="fas fa-wifi"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="editIP(${ip.id}, '${ip.ip_address}', '${ip.device_name.replace(/'/g, "\\'")}', ${ip.device_type_id}, ${ip.branch_id}, '${(ip.description || '').replace(/'/g, "\\'")}')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteIP(${ip.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            tableHTML += '</tbody></table></div>';
            tableContent.innerHTML = tableHTML;
        }

        // Update single IP status in table
        function updateIPStatus(ipAddress, status, responseTime = null) {
            const row = document.getElementById(`row-${ipAddress.replace(/\./g, '-')}`);
            if (!row) return;

            const statusCell = row.querySelector('.status-indicator');
            const pingStatusDot = statusCell.querySelector('.ping-status');
            const statusIcon = statusCell.querySelector('i');
            const statusText = statusCell.querySelector('.status-text');

            // Update cache
            ipStatusCache.set(ipAddress, status);

            // Update visual elements
            switch(status) {
                case 'online':
                    pingStatusDot.className = 'ping-status ping-online';
                    statusIcon.className = 'fas fa-check-circle text-success';
                    statusText.textContent = responseTime ? `Online (${responseTime.toFixed(1)}ms)` : 'Online';
                    break;
                case 'offline':
                    pingStatusDot.className = 'ping-status ping-offline';
                    statusIcon.className = 'fas fa-times-circle text-danger';
                    statusText.textContent = 'Offline';
                    break;
                case 'testing':
                    pingStatusDot.className = 'ping-status ping-testing';
                    statusIcon.className = 'fas fa-spinner fa-spin text-warning';
                    statusText.textContent = 'Testing...';
                    break;
                default:
                    pingStatusDot.className = 'ping-status ping-unknown';
                    statusIcon.className = 'fas fa-question-circle text-muted';
                    statusText.textContent = 'Unknown';
            }

            // Update stats
            updateStats(allIPs);
        }

        // Show add form
        function showAddForm() {
            const form = document.getElementById('addIpForm');
            form.style.display = 'block';
            form.scrollIntoView({ behavior: 'smooth' });
            
            document.getElementById('ipForm').reset();
            document.getElementById('ipId').value = '';
            document.getElementById('formTitle').textContent = 'Add New IP/Device';
            document.getElementById('submitText').textContent = 'Add IP';
            
            if (currentBranch) {
                document.getElementById('branchSelect').value = currentBranch;
            }
        }

        // Hide add form
        function hideAddForm() {
            document.getElementById('addIpForm').style.display = 'none';
        }

        // Edit IP
        function editIP(id, ipAddress, deviceName, deviceTypeId, branchId, description) {
            showAddForm();
            
            document.getElementById('ipId').value = id;
            document.getElementById('ipAddress').value = ipAddress;
            document.getElementById('deviceName').value = deviceName;
            document.getElementById('deviceType').value = deviceTypeId;
            document.getElementById('branchSelect').value = branchId;
            document.getElementById('description').value = description;
            
            document.getElementById('formTitle').textContent = 'Edit IP/Device';
            document.getElementById('submitText').textContent = 'Update IP';
            
            updateIPRangeInfo();
        }

        // Handle form submission
        async function handleFormSubmit(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const isEdit = document.getElementById('ipId').value !== '';
            
            const ipData = {
                ip_address: formData.get('ipAddress'),
                device_name: formData.get('deviceName'),
                device_type_id: parseInt(formData.get('deviceType')),
                branch_id: parseInt(formData.get('branchSelect')),
                description: formData.get('description') || ''
            };

            try {
                if (isEdit) {
                    const id = document.getElementById('ipId').value;
                    await apiCall(`ips/${id}`, {
                        method: 'PUT',
                        body: JSON.stringify(ipData)
                    });
                    showAlert('IP updated successfully!', 'success');
                } else {
                    await apiCall('ips', {
                        method: 'POST',
                        body: JSON.stringify(ipData)
                    });
                    showAlert('IP added successfully!', 'success');
                }

                hideAddForm();
                if (currentBranch) {
                    loadBranchIPs(currentBranch, currentBranchName);
                }
                
            } catch (error) {
                console.error('Form submission error:', error);
                showAlert('Error saving IP record. Please try again.', 'danger');
            }
        }

        // Delete IP
        async function deleteIP(id) {
            if (!confirm('Are you sure you want to delete this IP record?')) {
                return;
            }
            
            try {
                await apiCall(`ips/${id}`, { method: 'DELETE' });
                showAlert('IP deleted successfully!', 'success');
                
                if (currentBranch) {
                    loadBranchIPs(currentBranch, currentBranchName);
                }
            } catch (error) {
                console.error('Delete error:', error);
                showAlert('Error deleting IP record. Please try again.', 'danger');
            }
        }

        // Enhanced Ping IP function
        async function pingIP(ipAddress) {
            currentPingIP = ipAddress;
            
            // Update table status to testing
            updateIPStatus(ipAddress, 'testing');
            
            document.getElementById('pingResults').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Testing connectivity to <strong>${ipAddress}</strong>...</p>
                    <small class="text-muted">This may take a few seconds</small>
                </div>
            `;
            
            document.getElementById('retryPingBtn').style.display = 'none';
            pingModal.show();

            try {
                const result = await apiCall('ping', {
                    method: 'POST',
                    body: JSON.stringify({ ip_address: ipAddress })
                });
                
                // Update status in table
                updateIPStatus(ipAddress, result.online ? 'online' : 'offline', result.response_time);
                
                // Show detailed results in modal
                const resultClass = result.online ? 'ping-success' : 'ping-failed';
                const statusIcon = result.online ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger';
                const statusBadge = result.online ? 'bg-success' : 'bg-danger';
                
                let diagnosticsHTML = '';
                if (!result.online && result.diagnostics && result.diagnostics.reason) {
                    diagnosticsHTML = `
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Diagnostic:</strong> ${result.diagnostics.reason}
                        </div>
                    `;
                }

                document.getElementById('pingResults').innerHTML = `
                    <div class="ping-result-card ${resultClass}">
                        <div class="text-center mb-3">
                            <i class="${statusIcon} fa-3x mb-3"></i>
                            <h5>${ipAddress}</h5>
                            <span class="badge ${statusBadge} fs-6 mb-3">
                                ${result.online ? 'Online' : 'Offline'}
                            </span>
                        </div>
                        
                        ${result.online && result.response_time ? 
                            `<div class="text-center mb-3">
                                <span class="response-time">${result.response_time.toFixed(1)}ms</span>
                                <small class="d-block text-muted">Response Time</small>
                            </div>` : 
                            result.online ? 
                            '<p class="text-center text-success">Connection successful</p>' :
                            '<p class="text-center text-danger">Host unreachable</p>'
                        }
                        
                        ${diagnosticsHTML}
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> 
                                Tested at: ${new Date(result.timestamp).toLocaleString()}
                            </small>
                        </div>
                    </div>
                `;
                
                document.getElementById('retryPingBtn').style.display = 'inline-block';
                
            } catch (error) {
                console.error('Ping error:', error);
                updateIPStatus(ipAddress, 'unknown');
                
                document.getElementById('pingResults').innerHTML = `
                    <div class="ping-result-card ping-failed">
                        <div class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <h5>Ping Test Failed</h5>
                            <p>Unable to perform ping test for ${ipAddress}</p>
                            <div class="alert alert-danger mt-3">
                                <strong>Error:</strong> ${error.message || 'Unknown error occurred'}
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('retryPingBtn').style.display = 'inline-block';
            }
        }

        // Retry ping function
        function retryPing() {
            if (currentPingIP) {
                pingIP(currentPingIP);
            }
        }

        // Ping all IPs function
        async function pingAllIPs() {
            if (bulkPingInProgress || allIPs.length === 0) return;
            
            bulkPingInProgress = true;
            const totalIPs = allIPs.length;
            let completedPings = 0;
            
            // Show bulk ping modal
            document.getElementById('bulkPingResults').innerHTML = '';
            document.getElementById('pingProgress').textContent = `0 / ${totalIPs}`;
            document.getElementById('pingProgressBar').style.width = '0%';
            bulkPingModal.show();
            
            // Ping IPs in batches to avoid overwhelming the server
            const batchSize = 5;
            for (let i = 0; i < totalIPs; i += batchSize) {
                const batch = allIPs.slice(i, i + batchSize);
                const batchPromises = batch.map(async (ip) => {
                    try {
                        // Update status to testing
                        updateIPStatus(ip.ip_address, 'testing');
                        
                        const result = await apiCall('ping', {
                            method: 'POST',
                            body: JSON.stringify({ ip_address: ip.ip_address })
                        });
                        
                        // Update status
                        updateIPStatus(ip.ip_address, result.online ? 'online' : 'offline', result.response_time);
                        
                        // Add result to modal
                        const resultHTML = `
                            <div class="d-flex justify-content-between align-items-center p-2 mb-1 rounded ${result.online ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10'}">
                                <div>
                                    <strong>${ip.ip_address}</strong>
                                    <small class="text-muted ms-2">${ip.device_name}</small>
                                </div>
                                <div>
                                    <span class="badge ${result.online ? 'bg-success' : 'bg-danger'}">
                                        ${result.online ? 'Online' : 'Offline'}
                                    </span>
                                    ${result.response_time ? `<small class="text-muted ms-2">${result.response_time.toFixed(1)}ms</small>` : ''}
                                </div>
                            </div>
                        `;
                        document.getElementById('bulkPingResults').insertAdjacentHTML('beforeend', resultHTML);
                        
                        completedPings++;
                        const progress = (completedPings / totalIPs) * 100;
                        document.getElementById('pingProgress').textContent = `${completedPings} / ${totalIPs}`;
                        document.getElementById('pingProgressBar').style.width = `${progress}%`;
                        
                    } catch (error) {
                        console.error(`Ping failed for ${ip.ip_address}:`, error);
                        updateIPStatus(ip.ip_address, 'unknown');
                        completedPings++;
                    }
                });
                
                await Promise.all(batchPromises);
                
                // Small delay between batches
                if (i + batchSize < totalIPs) {
                    await new Promise(resolve => setTimeout(resolve, 1000));
                }
            }
            
            bulkPingInProgress = false;
            showAlert(`Ping test completed for ${totalIPs} IP addresses`, 'success');
        }

        // Filter table
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('ipTable');
            
            if (!table) return;
            
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length - 1; j++) {
                    if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        }

        // Sort table
        function sortTable(columnIndex) {
            const table = document.getElementById('ipTable');
            if (!table) return;
            
            const tbody = table.tBodies[0];
            const rows = Array.from(tbody.rows);
            
            rows.sort((a, b) => {
                let aText, bText;
                
                if (columnIndex === 0) { // Status column
                    const aStatus = a.cells[columnIndex].querySelector('.status-text').textContent;
                    const bStatus = b.cells[columnIndex].querySelector('.status-text').textContent;
                    const statusOrder = { 'Online': 0, 'Testing...': 1, 'Offline': 2, 'Unknown': 3 };
                    return statusOrder[aStatus] - statusOrder[bStatus];
                } else if (columnIndex === 1) { // IP address sorting
                    const aIP = a.cells[columnIndex].textContent.trim().split('.').map(num => parseInt(num));
                    const bIP = b.cells[columnIndex].textContent.trim().split('.').map(num => parseInt(num));
                    
                    for (let i = 0; i < 4; i++) {
                        if (aIP[i] !== bIP[i]) {
                            return aIP[i] - bIP[i];
                        }
                    }
                    return 0;
                } else {
                    aText = a.cells[columnIndex].textContent.trim();
                    bText = b.cells[columnIndex].textContent.trim();
                    return aText.localeCompare(bText);
                }
            });
            
            rows.forEach(row => tbody.appendChild(row));
        }

        // Update IP range information
        function updateIPRangeInfo() {
            const ipInput = document.getElementById('ipAddress');
            const rangeInfo = document.getElementById('ipRangeInfo');
            const ip = ipInput.value;
            
            if (!ip.match(/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/)) {
                rangeInfo.textContent = '';
                return;
            }
            
            const parts = ip.split('.');
            const firstOctet = parseInt(parts[0]);
            const secondOctet = parseInt(parts[1]);
            
            let rangeType = '';
            if (firstOctet === 10) {
                rangeType = 'Private Class A (10.0.0.0/8)';
            } else if (firstOctet === 172 && secondOctet >= 16 && secondOctet <= 31) {
                rangeType = 'Private Class B (172.16.0.0/12)';
            } else if (firstOctet === 192 && secondOctet === 168) {
                rangeType = 'Private Class C (192.168.0.0/16)';
            } else if (firstOctet === 127) {
                rangeType = 'Loopback (127.0.0.0/8)';
            } else if (firstOctet >= 224 && firstOctet <= 239) {
                rangeType = 'Multicast (224.0.0.0/4)';
            } else {
                rangeType = 'Public IP Address';
            }
            
            rangeInfo.innerHTML = `<i class="fas fa-info-circle"></i> ${rangeType}`;
        }

        // Export to CSV
        function exportToCSV() {
            if (allIPs.length === 0) {
                showAlert('No data to export', 'warning');
                return;
            }

            const headers = ['IP Address', 'Device Name', 'Device Type', 'Branch', 'Description', 'Status', 'Response Time'];
            const csvContent = [
                headers.join(','),
                ...allIPs.map(ip => {
                    const status = ipStatusCache.get(ip.ip_address) || 'unknown';
                    const responseTime = status === 'online' ? 'N/A' : ''; // You might want to store response times separately
                    return [
                        ip.ip_address,
                        `"${ip.device_name}"`,
                        `"${ip.device_type_name}"`,
                        `"${ip.branch_name}"`,
                        `"${ip.description || ''}"`,
                        status,
                        responseTime
                    ].join(',');
                })
            ].join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${currentBranchName}_IP_Report_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            showAlert('CSV exported successfully!', 'success');
        }

        // Print report
        function printReport() {
            const onlineCount = allIPs.filter(ip => ipStatusCache.get(ip.ip_address) === 'online').length;
            const offlineCount = allIPs.filter(ip => ipStatusCache.get(ip.ip_address) === 'offline').length;
            const unknownCount = allIPs.length - onlineCount - offlineCount;
            
            const printWindow = window.open('', '_blank');
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>IP Management Report - ${currentBranchName}</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 20px; 
                            color: #333;
                        }
                        .header { 
                            text-align: center; 
                            margin-bottom: 30px; 
                            padding-bottom: 20px;
                            border-bottom: 3px solid #2563eb;
                        }
                        .header h1 { 
                            color: #2563eb; 
                            margin: 0;
                        }
                        .stats { 
                            display: flex; 
                            justify-content: space-around; 
                            margin: 30px 0; 
                        }
                        .stat-box { 
                            text-align: center; 
                            padding: 15px; 
                            border: 2px solid #e2e8f0; 
                            border-radius: 8px;
                            background: #f8fafc;
                        }
                        .stat-number {
                            font-size: 24px;
                            font-weight: bold;
                            color: #2563eb;
                        }
                        table { 
                            width: 100%; 
                            border-collapse: collapse; 
                            margin-top: 20px; 
                        }
                        th, td { 
                            border: 1px solid #e2e8f0; 
                            padding: 12px; 
                            text-align: left; 
                        }
                        th { 
                            background: #2563eb;
                            color: white;
                            font-weight: bold;
                        }
                        tr:nth-child(even) {
                            background: #f8fafc;
                        }
                        .online { color: #059669; font-weight: bold; }
                        .offline { color: #dc2626; font-weight: bold; }
                        .unknown { color: #64748b; }
                        .ip-address { 
                            font-family: 'Courier New', monospace; 
                            background: #f1f5f9;
                            padding: 4px;
                            border-radius: 4px;
                        }
                        @media print { 
                            .no-print { display: none; }
                            body { margin: 10px; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>IP Management Report</h1>
                        <h2>${currentBranchName}</h2>
                        <p>Generated on: ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</p>
                    </div>
                    
                    <div class="stats">
                        <div class="stat-box">
                            <div class="stat-number">${allIPs.length}</div>
                            <div>Total IPs</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number">${onlineCount}</div>
                            <div>Online</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number">${offlineCount}</div>
                            <div>Offline</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number">${unknownCount}</div>
                            <div>Unknown</div>
                        </div>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Device Name</th>
                                <th>Device Type</th>
                                <th>Description</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${allIPs.map(ip => {
                                const status = ipStatusCache.get(ip.ip_address) || 'unknown';
                                const statusClass = status === 'online' ? 'online' : 
                                                  status === 'offline' ? 'offline' : 'unknown';
                                return `
                                <tr>
                                    <td><span class="ip-address">${ip.ip_address}</span></td>
                                    <td>${ip.device_name}</td>
                                    <td>${ip.device_type_name}</td>
                                    <td>${ip.description || '-'}</td>
                                    <td><span class="${statusClass}">${status.toUpperCase()}</span></td>
                                </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                    
                    <div style="margin-top: 30px; font-size: 12px; color: #64748b; text-align: center;">
                        This report was generated by the IP Management System
                    </div>
                </body>
                </html>
            `;
            
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
            }, 500);
        }

        // Connection error handling
        function showConnectionError() {
            document.getElementById('connectionError').style.display = 'block';
        }

        function hideConnectionError() {
            document.getElementById('connectionError').style.display = 'none';
        }

        // Enhanced alert function
        function showAlert(message, type) {
            const alertIcons = {
                'success': 'fa-check-circle',
                'warning': 'fa-exclamation-triangle', 
                'danger': 'fa-times-circle',
                'info': 'fa-info-circle'
            };
            
            const alertHTML = `
                <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 1050; min-width: 300px; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);" role="alert">
                    <i class="fas ${alertIcons[type] || 'fa-info-circle'}"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', alertHTML);
            
            // Auto-dismiss after 4 seconds
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 4000);
        }

        // Auto-refresh functionality (optional)
        function startAutoRefresh() {
            setInterval(async () => {
                if (currentBranch && !bulkPingInProgress) {
                    try {
                        const refreshedIPs = await apiCall(`ips?branch_id=${currentBranch}`);
                        if (JSON.stringify(refreshedIPs) !== JSON.stringify(allIPs)) {
                            allIPs = refreshedIPs;
                            displayIPTable(allIPs);
                            updateStats(allIPs);
                        }
                    } catch (error) {
                        console.warn('Auto-refresh failed:', error);
                    }
                }
            }, 30000); // Refresh every 30 seconds
        }

        // Start auto-refresh when page loads
        // Uncomment the next line if you want auto-refresh
        // startAutoRefresh();
    </script>
</body>
</html>