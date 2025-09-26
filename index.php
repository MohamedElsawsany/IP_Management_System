<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced IP Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
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

                    <!-- Pagination Controls -->
                    <div class="pagination-container" id="paginationContainer" style="display: none;">
                        <div class="pagination-controls">
                            <div class="d-flex align-items-center">
                                <span class="me-2">Show:</span>
                                <select class="entries-select" id="entriesPerPage" onchange="changeEntriesPerPage()">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="ms-2">entries</span>
                            </div>
                            
                            <nav aria-label="Table pagination">
                                <ul class="pagination mb-0" id="paginationNav">
                                    <!-- Pagination buttons will be generated here -->
                                </ul>
                            </nav>
                            
                            <div class="pagination-info" id="paginationInfo">
                                Showing 0 to 0 of 0 entries
                            </div>
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
    <script src="script.js"></script>
</body>
</html>