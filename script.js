// Configuration
const API_BASE_URL = "api.php"; // Change this to your API endpoint

// Global variables
let currentBranch = null;
let currentBranchName = "";
let allIPs = [];
let filteredIPs = [];
let branches = [];
let deviceTypes = [];
let pingModal = null;
let bulkPingModal = null;
let ipStatusCache = new Map(); // Cache for ping results
let currentPingIP = null; // For retry functionality
let bulkPingInProgress = false;

// Pagination variables
let currentPage = 1;
let entriesPerPage = 10;
let totalPages = 1;
let sortColumn = 1; // Default sort by IP address
let sortDirection = "asc";

// Initialize the application
document.addEventListener("DOMContentLoaded", function () {
  pingModal = new bootstrap.Modal(document.getElementById("pingModal"));
  bulkPingModal = new bootstrap.Modal(document.getElementById("bulkPingModal"));
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
    console.error("Failed to initialize app:", error);
  }
}

// Setup event listeners
function setupEventListeners() {
  document
    .getElementById("ipForm")
    .addEventListener("submit", handleFormSubmit);
  document
    .getElementById("showAddFormBtn")
    .addEventListener("click", showAddForm);
  document.getElementById("quickAddBtn").addEventListener("click", showAddForm);
  document.getElementById("cancelBtn").addEventListener("click", hideAddForm);
  document
    .getElementById("searchInput")
    .addEventListener("keyup", function (e) {
      currentPage = 1; // Reset to first page when searching
      filterAndPaginate();
    });
  document
    .getElementById("ipAddress")
    .addEventListener("input", updateIPRangeInfo);
}

// API Functions
async function apiCall(endpoint, options = {}) {
  try {
    const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
      headers: {
        "Content-Type": "application/json",
        ...options.headers,
      },
      ...options,
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
    branches = await apiCall("branches");
    displayBranches(branches);
    populateBranchSelects(branches);
  } catch (error) {
    console.error("Failed to load branches:", error);
    throw error;
  }
}

// Load device types from database
async function loadDeviceTypes() {
  try {
    deviceTypes = await apiCall("device-types");
    populateDeviceTypeSelects(deviceTypes);
  } catch (error) {
    console.error("Failed to load device types:", error);
    throw error;
  }
}

// Load IPs for selected branch
async function loadBranchIPs(branchId, branchName) {
  try {
    document.getElementById(
      "selectedBranchName"
    ).innerHTML = `<i class="fas fa-list"></i> ${branchName} - IP Addresses`;

    const container = document.getElementById("ipTableContainer");
    container.classList.add("show");

    document.getElementById("tableContent").innerHTML =
      '<div class="loading-spinner"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted">Loading data...</p></div>';

    allIPs = await apiCall(`ips?branch_id=${branchId}`);
    filteredIPs = [...allIPs]; // Initialize filtered IPs
    currentPage = 1; // Reset to first page
    updatePagination();
    displayIPTable();
    updateStats(allIPs);
    document.getElementById("statsRow").style.display = "flex";
    document.getElementById("exportSection").style.display = "block";
    document.getElementById("paginationContainer").style.display = "block";
  } catch (error) {
    console.error("Failed to load IPs:", error);
    document.getElementById("tableContent").innerHTML =
      '<div class="text-center py-5 text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><h5>Failed to load IP data</h5></div>';
  }
}

// Display branch buttons
function displayBranches(branches) {
  const container = document.getElementById("branchButtons");
  container.innerHTML = "";

  branches.forEach((branch) => {
    const button = document.createElement("button");
    button.className = "btn btn-outline-primary btn-branch";
    button.innerHTML = `<i class="fas fa-building"></i> ${branch.name}`;
    button.onclick = () => selectBranch(branch.id, branch.name, button);
    container.appendChild(button);
  });
}

// Select branch and load IPs
function selectBranch(branchId, branchName, buttonElement) {
  // Update active button
  document
    .querySelectorAll(".btn-branch")
    .forEach((btn) => btn.classList.remove("active"));
  buttonElement.classList.add("active");

  currentBranch = branchId;
  currentBranchName = branchName;
  loadBranchIPs(branchId, branchName);
}

// Populate branch select elements
function populateBranchSelects(branches) {
  const selects = ["branchSelect"];
  selects.forEach((selectId) => {
    const select = document.getElementById(selectId);
    select.innerHTML = '<option value="">Select Branch</option>';
    branches.forEach((branch) => {
      const option = document.createElement("option");
      option.value = branch.id;
      option.textContent = branch.name;
      select.appendChild(option);
    });
  });
}

// Populate device type select elements
function populateDeviceTypeSelects(deviceTypes) {
  const selects = ["deviceType"];
  selects.forEach((selectId) => {
    const select = document.getElementById(selectId);
    select.innerHTML = '<option value="">Select Device Type</option>';
    deviceTypes.forEach((type) => {
      const option = document.createElement("option");
      option.value = type.id;
      option.textContent = type.name;
      select.appendChild(option);
    });
  });
}

// Update statistics
function updateStats(ips) {
  const totalIPs = ips.length;
  const onlineIPs = ips.filter(
    (ip) => ipStatusCache.get(ip.ip_address) === "online"
  ).length;
  const offlineIPs = ips.filter(
    (ip) => ipStatusCache.get(ip.ip_address) === "offline"
  ).length;
  const deviceTypesCount = new Set(ips.map((ip) => ip.device_type_name)).size;

  document.getElementById("totalIPs").textContent = totalIPs;
  document.getElementById("onlineIPs").textContent = onlineIPs;
  document.getElementById("offlineIPs").textContent = offlineIPs;
  document.getElementById("deviceTypes").textContent = deviceTypesCount;
}

// Filter and paginate data
function filterAndPaginate() {
  const searchTerm = document.getElementById("searchInput").value.toLowerCase();

  // Filter data
  filteredIPs = allIPs.filter((ip) => {
    return (
      ip.ip_address.toLowerCase().includes(searchTerm) ||
      ip.device_name.toLowerCase().includes(searchTerm) ||
      ip.device_type_name.toLowerCase().includes(searchTerm) ||
      (ip.description && ip.description.toLowerCase().includes(searchTerm))
    );
  });

  // Sort data
  sortData();

  // Update pagination
  updatePagination();

  // Display table
  displayIPTable();
}

// Sort data
function sortData() {
  filteredIPs.sort((a, b) => {
    let aValue, bValue;

    switch (sortColumn) {
      case 0: // Status
        const aStatus = ipStatusCache.get(a.ip_address) || "unknown";
        const bStatus = ipStatusCache.get(b.ip_address) || "unknown";
        const statusOrder = { online: 0, testing: 1, offline: 2, unknown: 3 };
        aValue = statusOrder[aStatus];
        bValue = statusOrder[bStatus];
        break;
      case 1: // IP Address
        const aIP = a.ip_address
          .split(".")
          .map((num) => parseInt(num).toString().padStart(3, "0"))
          .join("");
        const bIP = b.ip_address
          .split(".")
          .map((num) => parseInt(num).toString().padStart(3, "0"))
          .join("");
        aValue = aIP;
        bValue = bIP;
        break;
      case 2: // Device Name
        aValue = a.device_name.toLowerCase();
        bValue = b.device_name.toLowerCase();
        break;
      case 3: // Device Type
        aValue = a.device_type_name.toLowerCase();
        bValue = b.device_type_name.toLowerCase();
        break;
      case 4: // Description
        aValue = (a.description || "").toLowerCase();
        bValue = (b.description || "").toLowerCase();
        break;
      default:
        return 0;
    }

    if (sortDirection === "asc") {
      return aValue < bValue ? -1 : aValue > bValue ? 1 : 0;
    } else {
      return aValue > bValue ? -1 : aValue < bValue ? 1 : 0;
    }
  });
}

// Update pagination controls
function updatePagination() {
  const totalEntries = filteredIPs.length;
  totalPages = Math.ceil(totalEntries / entriesPerPage);

  // Ensure current page is within bounds
  if (currentPage > totalPages && totalPages > 0) {
    currentPage = totalPages;
  } else if (currentPage < 1) {
    currentPage = 1;
  }

  // Update pagination info
  const startEntry =
    totalEntries > 0 ? (currentPage - 1) * entriesPerPage + 1 : 0;
  const endEntry = Math.min(currentPage * entriesPerPage, totalEntries);

  document.getElementById(
    "paginationInfo"
  ).textContent = `Showing ${startEntry} to ${endEntry} of ${totalEntries} entries`;

  // Generate pagination buttons
  generatePaginationButtons();
}

// Generate pagination buttons
function generatePaginationButtons() {
  const paginationNav = document.getElementById("paginationNav");
  paginationNav.innerHTML = "";

  if (totalPages <= 1) {
    return;
  }

  // First button
  const firstLi = document.createElement("li");
  firstLi.className = `page-item ${currentPage === 1 ? "disabled" : ""}`;
  firstLi.innerHTML = `<a class="page-link" href="#" onclick="goToPage(1)" title="First"><i class="fas fa-angle-double-left"></i></a>`;
  paginationNav.appendChild(firstLi);

  // Previous button
  const prevLi = document.createElement("li");
  prevLi.className = `page-item ${currentPage === 1 ? "disabled" : ""}`;
  prevLi.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${
    currentPage - 1
  })" title="Previous"><i class="fas fa-angle-left"></i></a>`;
  paginationNav.appendChild(prevLi);

  // Calculate page numbers to show
  let startPage = Math.max(1, currentPage - 2);
  let endPage = Math.min(totalPages, currentPage + 2);

  // Adjust range if we're near the beginning or end
  if (currentPage <= 3) {
    endPage = Math.min(5, totalPages);
  }
  if (currentPage > totalPages - 3) {
    startPage = Math.max(1, totalPages - 4);
  }

  // Show ellipsis and first page if needed
  if (startPage > 1) {
    const li = document.createElement("li");
    li.className = "page-item";
    li.innerHTML = `<a class="page-link" href="#" onclick="goToPage(1)">1</a>`;
    paginationNav.appendChild(li);

    if (startPage > 2) {
      const ellipsisLi = document.createElement("li");
      ellipsisLi.className = "page-item disabled";
      ellipsisLi.innerHTML = `<span class="page-link">...</span>`;
      paginationNav.appendChild(ellipsisLi);
    }
  }

  // Page number buttons
  for (let i = startPage; i <= endPage; i++) {
    const li = document.createElement("li");
    li.className = `page-item ${i === currentPage ? "active" : ""}`;
    li.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${i})">${i}</a>`;
    paginationNav.appendChild(li);
  }

  // Show ellipsis and last page if needed
  if (endPage < totalPages) {
    if (endPage < totalPages - 1) {
      const ellipsisLi = document.createElement("li");
      ellipsisLi.className = "page-item disabled";
      ellipsisLi.innerHTML = `<span class="page-link">...</span>`;
      paginationNav.appendChild(ellipsisLi);
    }

    const li = document.createElement("li");
    li.className = "page-item";
    li.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${totalPages})">${totalPages}</a>`;
    paginationNav.appendChild(li);
  }

  // Next button
  const nextLi = document.createElement("li");
  nextLi.className = `page-item ${
    currentPage === totalPages ? "disabled" : ""
  }`;
  nextLi.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${
    currentPage + 1
  })" title="Next"><i class="fas fa-angle-right"></i></a>`;
  paginationNav.appendChild(nextLi);

  // Last button
  const lastLi = document.createElement("li");
  lastLi.className = `page-item ${
    currentPage === totalPages ? "disabled" : ""
  }`;
  lastLi.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${totalPages})" title="Last"><i class="fas fa-angle-double-right"></i></a>`;
  paginationNav.appendChild(lastLi);
}

// Go to specific page
function goToPage(page) {
  if (page < 1 || page > totalPages || page === currentPage) {
    return;
  }
  currentPage = page;
  displayIPTable();
  generatePaginationButtons();
}

// Change entries per page
function changeEntriesPerPage() {
  entriesPerPage = parseInt(document.getElementById("entriesPerPage").value);
  currentPage = 1;
  updatePagination();
  displayIPTable();
}

// Display IP table with pagination
function displayIPTable() {
  const tableContent = document.getElementById("tableContent");

  if (filteredIPs.length === 0) {
    tableContent.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-network-wired fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No IP addresses found</h5>
                        <p class="text-muted">${
                          allIPs.length === 0
                            ? 'Click "Add New IP" to get started'
                            : "Try adjusting your search criteria"
                        }</p>
                    </div>
                `;
    document.getElementById("paginationContainer").style.display = "none";
    return;
  }

  // Calculate pagination
  const startIndex = (currentPage - 1) * entriesPerPage;
  const endIndex = startIndex + entriesPerPage;
  const pageIPs = filteredIPs.slice(startIndex, endIndex);

  let tableHTML = `
                <div class="table-responsive">
                    <table class="table table-hover" id="ipTable">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0)">
                                    Status 
                                    <i class="fas fa-sort${
                                      sortColumn === 0
                                        ? sortDirection === "asc"
                                          ? "-up"
                                          : "-down"
                                        : ""
                                    }"></i>
                                </th>
                                <th onclick="sortTable(1)">
                                    IP Address 
                                    <i class="fas fa-sort${
                                      sortColumn === 1
                                        ? sortDirection === "asc"
                                          ? "-up"
                                          : "-down"
                                        : ""
                                    }"></i>
                                </th>
                                <th onclick="sortTable(2)">
                                    Device Name 
                                    <i class="fas fa-sort${
                                      sortColumn === 2
                                        ? sortDirection === "asc"
                                          ? "-up"
                                          : "-down"
                                        : ""
                                    }"></i>
                                </th>
                                <th onclick="sortTable(3)">
                                    Device Type 
                                    <i class="fas fa-sort${
                                      sortColumn === 3
                                        ? sortDirection === "asc"
                                          ? "-up"
                                          : "-down"
                                        : ""
                                    }"></i>
                                </th>
                                <th onclick="sortTable(4)">
                                    Description
                                    <i class="fas fa-sort${
                                      sortColumn === 4
                                        ? sortDirection === "asc"
                                          ? "-up"
                                          : "-down"
                                        : ""
                                    }"></i>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

  pageIPs.forEach((ip) => {
    const cachedStatus = ipStatusCache.get(ip.ip_address);
    let statusClass, statusText, statusIcon;

    switch (cachedStatus) {
      case "online":
        statusClass = "ping-online";
        statusText = "Online";
        statusIcon = "fas fa-check-circle text-success";
        break;
      case "offline":
        statusClass = "ping-offline";
        statusText = "Offline";
        statusIcon = "fas fa-times-circle text-danger";
        break;
      case "testing":
        statusClass = "ping-testing";
        statusText = "Testing...";
        statusIcon = "fas fa-spinner fa-spin text-warning";
        break;
      default:
        statusClass = "ping-unknown";
        statusText = "Unknown";
        statusIcon = "fas fa-question-circle text-muted";
    }

    tableHTML += `
                    <tr id="row-${ip.ip_address.replace(/\./g, "-")}">
                        <td>
                            <div class="status-indicator">
                                <span class="ping-status ${statusClass}"></span>
                                <div>
                                    <i class="${statusIcon}"></i>
                                    <span class="status-text ms-1">${statusText}</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="ip-address">${
                          ip.ip_address
                        }</span></td>
                        <td><strong>${ip.device_name}</strong></td>
                        <td><span class="device-badge">${
                          ip.device_type_name
                        }</span></td>
                        <td><small class="text-muted">${
                          ip.description || "-"
                        }</small></td>
                        <td class="action-buttons">
                            <button class="btn btn-sm btn-outline-info" onclick="pingIP('${
                              ip.ip_address
                            }')" title="Ping Test">
                                <i class="fas fa-wifi"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="editIP(${
                              ip.id
                            }, '${ip.ip_address}', '${ip.device_name.replace(
      /'/g,
      "\\'"
    )}', ${ip.device_type_id}, ${ip.branch_id}, '${(
      ip.description || ""
    ).replace(/'/g, "\\'")}')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteIP(${
                              ip.id
                            })" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
  });

  tableHTML += "</tbody></table></div>";
  tableContent.innerHTML = tableHTML;

  // Update pagination info
  updatePagination();
}

// Sort table
function sortTable(columnIndex) {
  if (sortColumn === columnIndex) {
    sortDirection = sortDirection === "asc" ? "desc" : "asc";
  } else {
    sortColumn = columnIndex;
    sortDirection = "asc";
  }

  filterAndPaginate();
}

// Update single IP status in table
function updateIPStatus(ipAddress, status, responseTime = null) {
  const row = document.getElementById(`row-${ipAddress.replace(/\./g, "-")}`);
  if (!row) return;

  const statusCell = row.querySelector(".status-indicator");
  const pingStatusDot = statusCell.querySelector(".ping-status");
  const statusIcon = statusCell.querySelector("i");
  const statusText = statusCell.querySelector(".status-text");

  // Update cache
  ipStatusCache.set(ipAddress, status);

  // Update visual elements
  switch (status) {
    case "online":
      pingStatusDot.className = "ping-status ping-online";
      statusIcon.className = "fas fa-check-circle text-success";
      statusText.textContent = responseTime
        ? `Online (${responseTime.toFixed(1)}ms)`
        : "Online";
      break;
    case "offline":
      pingStatusDot.className = "ping-status ping-offline";
      statusIcon.className = "fas fa-times-circle text-danger";
      statusText.textContent = "Offline";
      break;
    case "testing":
      pingStatusDot.className = "ping-status ping-testing";
      statusIcon.className = "fas fa-spinner fa-spin text-warning";
      statusText.textContent = "Testing...";
      break;
    default:
      pingStatusDot.className = "ping-status ping-unknown";
      statusIcon.className = "fas fa-question-circle text-muted";
      statusText.textContent = "Unknown";
  }

  // Update stats
  updateStats(allIPs);
}

// Show add form
function showAddForm() {
  const form = document.getElementById("addIpForm");
  form.style.display = "block";
  form.scrollIntoView({ behavior: "smooth" });

  document.getElementById("ipForm").reset();
  document.getElementById("ipId").value = "";
  document.getElementById("formTitle").textContent = "Add New IP/Device";
  document.getElementById("submitText").textContent = "Add IP";

  if (currentBranch) {
    document.getElementById("branchSelect").value = currentBranch;
  }
}

// Hide add form
function hideAddForm() {
  document.getElementById("addIpForm").style.display = "none";
}

// Edit IP
function editIP(
  id,
  ipAddress,
  deviceName,
  deviceTypeId,
  branchId,
  description
) {
  showAddForm();

  document.getElementById("ipId").value = id;
  document.getElementById("ipAddress").value = ipAddress;
  document.getElementById("deviceName").value = deviceName;
  document.getElementById("deviceType").value = deviceTypeId;
  document.getElementById("branchSelect").value = branchId;
  document.getElementById("description").value = description;

  document.getElementById("formTitle").textContent = "Edit IP/Device";
  document.getElementById("submitText").textContent = "Update IP";

  updateIPRangeInfo();
}

// Handle form submission
async function handleFormSubmit(e) {
  e.preventDefault();

  const formData = new FormData(e.target);
  const isEdit = document.getElementById("ipId").value !== "";

  const ipData = {
    ip_address: formData.get("ipAddress"),
    device_name: formData.get("deviceName"),
    device_type_id: parseInt(formData.get("deviceType")),
    branch_id: parseInt(formData.get("branchSelect")),
    description: formData.get("description") || "",
  };

  try {
    if (isEdit) {
      const id = document.getElementById("ipId").value;
      await apiCall(`ips/${id}`, {
        method: "PUT",
        body: JSON.stringify(ipData),
      });
      showAlert("IP updated successfully!", "success");
    } else {
      await apiCall("ips", {
        method: "POST",
        body: JSON.stringify(ipData),
      });
      showAlert("IP added successfully!", "success");
    }

    hideAddForm();
    if (currentBranch) {
      loadBranchIPs(currentBranch, currentBranchName);
    }
  } catch (error) {
    console.error("Form submission error:", error);
    showAlert("Error saving IP record. Please try again.", "danger");
  }
}

// Delete IP
async function deleteIP(id) {
  if (!confirm("Are you sure you want to delete this IP record?")) {
    return;
  }

  try {
    await apiCall(`ips/${id}`, { method: "DELETE" });
    showAlert("IP deleted successfully!", "success");

    if (currentBranch) {
      loadBranchIPs(currentBranch, currentBranchName);
    }
  } catch (error) {
    console.error("Delete error:", error);
    showAlert("Error deleting IP record. Please try again.", "danger");
  }
}

// Enhanced Ping IP function
async function pingIP(ipAddress) {
  currentPingIP = ipAddress;

  // Update table status to testing
  updateIPStatus(ipAddress, "testing");

  document.getElementById("pingResults").innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Testing connectivity to <strong>${ipAddress}</strong>...</p>
                    <small class="text-muted">This may take a few seconds</small>
                </div>
            `;

  document.getElementById("retryPingBtn").style.display = "none";
  pingModal.show();

  try {
    const result = await apiCall("ping", {
      method: "POST",
      body: JSON.stringify({ ip_address: ipAddress }),
    });

    // Update status in table
    updateIPStatus(
      ipAddress,
      result.online ? "online" : "offline",
      result.response_time
    );

    // Show detailed results in modal
    const resultClass = result.online ? "ping-success" : "ping-failed";
    const statusIcon = result.online
      ? "fas fa-check-circle text-success"
      : "fas fa-times-circle text-danger";
    const statusBadge = result.online ? "bg-success" : "bg-danger";

    let diagnosticsHTML = "";
    if (!result.online && result.diagnostics && result.diagnostics.reason) {
      diagnosticsHTML = `
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Diagnostic:</strong> ${result.diagnostics.reason}
                        </div>
                    `;
    }

    document.getElementById("pingResults").innerHTML = `
                    <div class="ping-result-card ${resultClass}">
                        <div class="text-center mb-3">
                            <i class="${statusIcon} fa-3x mb-3"></i>
                            <h5>${ipAddress}</h5>
                            <span class="badge ${statusBadge} fs-6 mb-3">
                                ${result.online ? "Online" : "Offline"}
                            </span>
                        </div>
                        
                        ${
                          result.online && result.response_time
                            ? `<div class="text-center mb-3">
                                <span class="response-time">${result.response_time.toFixed(
                                  1
                                )}ms</span>
                                <small class="d-block text-muted">Response Time</small>
                            </div>`
                            : result.online
                            ? '<p class="text-center text-success">Connection successful</p>'
                            : '<p class="text-center text-danger">Host unreachable</p>'
                        }
                        
                        ${diagnosticsHTML}
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> 
                                Tested at: ${new Date(
                                  result.timestamp
                                ).toLocaleString()}
                            </small>
                        </div>
                    </div>
                `;

    document.getElementById("retryPingBtn").style.display = "inline-block";
  } catch (error) {
    console.error("Ping error:", error);
    updateIPStatus(ipAddress, "unknown");

    document.getElementById("pingResults").innerHTML = `
                    <div class="ping-result-card ping-failed">
                        <div class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <h5>Ping Test Failed</h5>
                            <p>Unable to perform ping test for ${ipAddress}</p>
                            <div class="alert alert-danger mt-3">
                                <strong>Error:</strong> ${
                                  error.message || "Unknown error occurred"
                                }
                            </div>
                        </div>
                    </div>
                `;

    document.getElementById("retryPingBtn").style.display = "inline-block";
  }
}

// Retry ping function
function retryPing() {
  if (currentPingIP) {
    pingIP(currentPingIP);
  }
}

// Ping all IPs function (updated to work with pagination)
async function pingAllIPs() {
  if (bulkPingInProgress || allIPs.length === 0) return;

  bulkPingInProgress = true;
  const totalIPs = allIPs.length;
  let completedPings = 0;

  // Show bulk ping modal
  document.getElementById("bulkPingResults").innerHTML = "";
  document.getElementById("pingProgress").textContent = `0 / ${totalIPs}`;
  document.getElementById("pingProgressBar").style.width = "0%";
  bulkPingModal.show();

  // Ping IPs in batches to avoid overwhelming the server
  const batchSize = 5;
  for (let i = 0; i < totalIPs; i += batchSize) {
    const batch = allIPs.slice(i, i + batchSize);
    const batchPromises = batch.map(async (ip) => {
      try {
        // Update status to testing
        updateIPStatus(ip.ip_address, "testing");

        const result = await apiCall("ping", {
          method: "POST",
          body: JSON.stringify({ ip_address: ip.ip_address }),
        });

        // Update status
        updateIPStatus(
          ip.ip_address,
          result.online ? "online" : "offline",
          result.response_time
        );

        // Add result to modal
        const resultHTML = `
                            <div class="d-flex justify-content-between align-items-center p-2 mb-1 rounded ${
                              result.online
                                ? "bg-success bg-opacity-10"
                                : "bg-danger bg-opacity-10"
                            }">
                                <div>
                                    <strong>${ip.ip_address}</strong>
                                    <small class="text-muted ms-2">${
                                      ip.device_name
                                    }</small>
                                </div>
                                <div>
                                    <span class="badge ${
                                      result.online ? "bg-success" : "bg-danger"
                                    }">
                                        ${result.online ? "Online" : "Offline"}
                                    </span>
                                    ${
                                      result.response_time
                                        ? `<small class="text-muted ms-2">${result.response_time.toFixed(
                                            1
                                          )}ms</small>`
                                        : ""
                                    }
                                </div>
                            </div>
                        `;
        document
          .getElementById("bulkPingResults")
          .insertAdjacentHTML("beforeend", resultHTML);

        completedPings++;
        const progress = (completedPings / totalIPs) * 100;
        document.getElementById(
          "pingProgress"
        ).textContent = `${completedPings} / ${totalIPs}`;
        document.getElementById("pingProgressBar").style.width = `${progress}%`;
      } catch (error) {
        console.error(`Ping failed for ${ip.ip_address}:`, error);
        updateIPStatus(ip.ip_address, "unknown");
        completedPings++;
      }
    });

    await Promise.all(batchPromises);

    // Small delay between batches
    if (i + batchSize < totalIPs) {
      await new Promise((resolve) => setTimeout(resolve, 1000));
    }
  }

  bulkPingInProgress = false;

  // Refresh the current page display to show updated statuses
  displayIPTable();

  showAlert(`Ping test completed for ${totalIPs} IP addresses`, "success");
}

// Update IP range information
function updateIPRangeInfo() {
  const ipInput = document.getElementById("ipAddress");
  const rangeInfo = document.getElementById("ipRangeInfo");
  const ip = ipInput.value;

  if (!ip.match(/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/)) {
    rangeInfo.textContent = "";
    return;
  }

  const parts = ip.split(".");
  const firstOctet = parseInt(parts[0]);
  const secondOctet = parseInt(parts[1]);

  let rangeType = "";
  if (firstOctet === 10) {
    rangeType = "Private Class A (10.0.0.0/8)";
  } else if (firstOctet === 172 && secondOctet >= 16 && secondOctet <= 31) {
    rangeType = "Private Class B (172.16.0.0/12)";
  } else if (firstOctet === 192 && secondOctet === 168) {
    rangeType = "Private Class C (192.168.0.0/16)";
  } else if (firstOctet === 127) {
    rangeType = "Loopback (127.0.0.0/8)";
  } else if (firstOctet >= 224 && firstOctet <= 239) {
    rangeType = "Multicast (224.0.0.0/4)";
  } else {
    rangeType = "Public IP Address";
  }

  rangeInfo.innerHTML = `<i class="fas fa-info-circle"></i> ${rangeType}`;
}

// Export to CSV (updated to work with filtered data)
function exportToCSV() {
  if (filteredIPs.length === 0) {
    showAlert("No data to export", "warning");
    return;
  }

  const headers = [
    "IP Address",
    "Device Name",
    "Device Type",
    "Branch",
    "Description",
    "Status",
    "Response Time",
  ];
  const csvContent = [
    headers.join(","),
    ...filteredIPs.map((ip) => {
      const status = ipStatusCache.get(ip.ip_address) || "unknown";
      const responseTime = status === "online" ? "N/A" : ""; // You might want to store response times separately
      return [
        ip.ip_address,
        `"${ip.device_name}"`,
        `"${ip.device_type_name}"`,
        `"${ip.branch_name}"`,
        `"${ip.description || ""}"`,
        status,
        responseTime,
      ].join(",");
    }),
  ].join("\n");

  const blob = new Blob([csvContent], { type: "text/csv" });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `${currentBranchName}_IP_Report_${
    new Date().toISOString().split("T")[0]
  }.csv`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  window.URL.revokeObjectURL(url);

  showAlert("CSV exported successfully!", "success");
}

// Print report (updated to work with filtered data)
function printReport() {
  const dataToExport = filteredIPs.length > 0 ? filteredIPs : allIPs;
  const onlineCount = dataToExport.filter(
    (ip) => ipStatusCache.get(ip.ip_address) === "online"
  ).length;
  const offlineCount = dataToExport.filter(
    (ip) => ipStatusCache.get(ip.ip_address) === "offline"
  ).length;
  const unknownCount = dataToExport.length - onlineCount - offlineCount;

  const printWindow = window.open("", "_blank");
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
                        ${
                          filteredIPs.length !== allIPs.length
                            ? `<p><em>Filtered Results: ${filteredIPs.length} of ${allIPs.length} total entries</em></p>`
                            : ""
                        }
                    </div>
                    
                    <div class="stats">
                        <div class="stat-box">
                            <div class="stat-number">${
                              dataToExport.length
                            }</div>
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
                            ${dataToExport
                              .map((ip) => {
                                const status =
                                  ipStatusCache.get(ip.ip_address) || "unknown";
                                const statusClass =
                                  status === "online"
                                    ? "online"
                                    : status === "offline"
                                    ? "offline"
                                    : "unknown";
                                return `
                                <tr>
                                    <td><span class="ip-address">${
                                      ip.ip_address
                                    }</span></td>
                                    <td>${ip.device_name}</td>
                                    <td>${ip.device_type_name}</td>
                                    <td>${ip.description || "-"}</td>
                                    <td><span class="${statusClass}">${status.toUpperCase()}</span></td>
                                </tr>
                                `;
                              })
                              .join("")}
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
  document.getElementById("connectionError").style.display = "block";
}

function hideConnectionError() {
  document.getElementById("connectionError").style.display = "none";
}

// Enhanced alert function
function showAlert(message, type) {
  const alertIcons = {
    success: "fa-check-circle",
    warning: "fa-exclamation-triangle",
    danger: "fa-times-circle",
    info: "fa-info-circle",
  };

  const alertHTML = `
                <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 1050; min-width: 300px; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);" role="alert">
                    <i class="fas ${alertIcons[type] || "fa-info-circle"}"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

  document.body.insertAdjacentHTML("beforeend", alertHTML);

  // Auto-dismiss after 4 seconds
  setTimeout(() => {
    const alert = document.querySelector(".alert");
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
          filterAndPaginate();
          updateStats(allIPs);
        }
      } catch (error) {
        console.warn("Auto-refresh failed:", error);
      }
    }
  }, 30000); // Refresh every 30 seconds
}

// Keyboard navigation for pagination
document.addEventListener("keydown", function (e) {
  if (document.getElementById("ipTableContainer").style.display !== "none") {
    switch (e.key) {
      case "ArrowLeft":
        if (e.ctrlKey && currentPage > 1) {
          e.preventDefault();
          goToPage(currentPage - 1);
        }
        break;
      case "ArrowRight":
        if (e.ctrlKey && currentPage < totalPages) {
          e.preventDefault();
          goToPage(currentPage + 1);
        }
        break;
      case "Home":
        if (e.ctrlKey && currentPage !== 1) {
          e.preventDefault();
          goToPage(1);
        }
        break;
      case "End":
        if (e.ctrlKey && currentPage !== totalPages) {
          e.preventDefault();
          goToPage(totalPages);
        }
        break;
    }
  }
});

// Prevent default link behavior for pagination
document.addEventListener("click", function (e) {
  if (e.target.closest(".page-link")) {
    e.preventDefault();
  }
});

// Start auto-refresh when page loads
// Uncomment the next line if you want auto-refresh
// startAutoRefresh();
