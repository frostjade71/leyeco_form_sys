/**
 * Requisition Dashboard JavaScript
 */

// Apply filters
function applyFilters() {
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchInput').value;
    
    const url = new URL(window.location.href);
    url.searchParams.set('status', status);
    url.searchParams.set('search', search);
    
    window.location.href = url.toString();
}

// Handle search with debounce
let searchTimeout;
function handleSearch(event) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        if (event.key === "Enter") {
            applyFilters();
        }
    }, 300);
}

// View request
function viewRequest(id) {
    const modal = document.getElementById('requestModal');
    const modalBody = document.getElementById('modalBody');
    
    // Show modal
    modal.style.display = 'flex';
    
    // Load request details
    fetch(`api/get_request.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modalBody.innerHTML = renderRequestDetails(data.request);
            } else {
                modalBody.innerHTML = `
                    <div style="text-align: center; padding: 40px;">
                        <p style="color: var(--danger-color);">${data.message}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = `
                <div style="text-align: center; padding: 40px;">
                    <p style="color: var(--danger-color);">An error occurred while loading the request.</p>
                </div>
            `;
        });
}

// Render request details
function renderRequestDetails(request) {
    let html = `
        <div class="request-details">
            <div class="details-section">
                <h3>Request Information</h3>
                <div class="details-grid">
                    <div class="detail-item">
                        <label>RF Control Number</label>
                        <div class="rf-number">${request.rf_control_number}</div>
                    </div>
                    <div class="detail-item">
                        <label>Status</label>
                        <div><span class="status-badge ${request.status.toLowerCase()}">${request.status.toUpperCase()}</span></div>
                    </div>
                    <div class="detail-item">
                        <label>Requester Name</label>
                        <div>${request.requester_name}</div>
                    </div>
                    <div class="detail-item">
                        <label>Department</label>
                        <div>${request.department}</div>
                    </div>
                    <div class="detail-item full-width">
                        <label>Purpose</label>
                        <div>${request.purpose}</div>
                    </div>
                </div>
            </div>
            
            <div class="details-section">
                <h3>Requisition Items</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${request.items.map(item => `
                            <tr>
                                <td>${item.quantity}</td>
                                <td>${item.unit}</td>
                                <td>${item.description}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
            
            <div class="modal-actions">
                <button class="btn btn-outline" onclick="closeModal()">Close</button>
                <a href="view_request.php?id=${request.id}" class="btn btn-primary">View Full Details</a>
            </div>
        </div>
    `;
    
    return html;
}

// Close modal
function closeModal() {
    document.getElementById('requestModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('requestModal');
    if (event.target === modal) {
        closeModal();
    }
}
