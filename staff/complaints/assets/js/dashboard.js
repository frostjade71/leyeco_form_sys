/**
 * LEYECO III Forms Management System
 * Complaints Dashboard JavaScript
 */

// Apply filters
function applyFilters() {
  const status = document.getElementById("statusFilter").value;
  const search = document.getElementById("searchInput").value;

  const params = new URLSearchParams(window.location.search);

  if (status) {
    params.set("status", status);
  } else {
    params.delete("status");
  }

  if (search) {
    params.set("search", search);
  } else {
    params.delete("search");
  }

  params.delete("page"); // Reset to page 1

  window.location.search = params.toString();
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

// Change page
function changePage(page) {
  const params = new URLSearchParams(window.location.search);
  params.set("page", page);
  window.location.search = params.toString();
}

// View complaint details
async function viewComplaint(id) {
  const modal = document.getElementById("complaintModal");
  const modalBody = document.getElementById("modalBody");

  // Show modal with loading state
  modal.classList.add("active");
  modalBody.innerHTML = `
        <div style="text-align: center; padding: 40px;">
            <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: var(--primary-color);"></i>
            <p style="margin-top: 16px; color: var(--text-secondary);">Loading complaint details...</p>
        </div>
    `;

  try {
    const response = await fetch(
      `${STAFF_URL}/complaints/api.php?action=get&id=${id}`
    );
    const data = await response.json();

    if (data.success) {
      renderComplaintDetails(data.complaint);
    } else {
      modalBody.innerHTML = `
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-exclamation-circle" style="font-size: 48px; color: var(--danger-color);"></i>
                    <p style="margin-top: 16px; color: var(--text-secondary);">${
                      data.error || "Failed to load complaint"
                    }</p>
                </div>
            `;
    }
  } catch (error) {
    console.error("Error loading complaint:", error);
    modalBody.innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-exclamation-circle" style="font-size: 48px; color: var(--danger-color);"></i>
                <p style="margin-top: 16px; color: var(--text-secondary);">Failed to load complaint details</p>
            </div>
        `;
  }
}

// Render complaint details
function renderComplaintDetails(complaint) {
  const modalBody = document.getElementById("modalBody");

  const statusOptions = [
    "NEW",
    "INVESTIGATING",
    "IN_PROGRESS",
    "RESOLVED",
    "CLOSED",
  ];
  const statusLabels = {
    NEW: "New",
    INVESTIGATING: "Investigating",
    IN_PROGRESS: "In Progress",
    RESOLVED: "Resolved",
    CLOSED: "Closed",
  };

  let html = `
        <div class="detail-section">
            <h3>Basic Information</h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Type</div>
                    <div class="detail-value">${escapeHtml(
                      complaint.type
                    )}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="status-badge ${complaint.status.toLowerCase()}">${escapeHtml(
    complaint.status
  )}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Reference Code</div>
                    <div class="detail-value"><strong>${escapeHtml(
                      complaint.reference_code
                    )}</strong></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Date Submitted</div>
                    <div class="detail-value">${formatDate(
                      complaint.created_at
                    )}</div>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h3>Reporter Information</h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Name</div>
                    <div class="detail-value">${
                      complaint.reporter_name || "Anonymous"
                    }</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Contact</div>
                    <div class="detail-value">${
                      complaint.contact || "Not provided"
                    }</div>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h3>Location</h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Municipality</div>
                    <div class="detail-value">${escapeHtml(
                      complaint.municipality
                    )}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Barangay</div>
                    <div class="detail-value">${escapeHtml(
                      complaint.barangay
                    )}</div>
                </div>
            </div>
            <div class="detail-item" style="margin-top: 12px;">
                <div class="detail-label">Address</div>
                <div class="detail-value">${escapeHtml(complaint.address)}</div>
            </div>
        </div>

        <div class="detail-section">
            <h3>Description</h3>
            <div class="detail-value">${escapeHtml(complaint.description)}</div>
        </div>
    `;

  if (complaint.photo_path) {
    // Construct full URL for the photo
    let photoUrl;
    if (complaint.photo_path.startsWith('http')) {
      // Already a full URL
      photoUrl = complaint.photo_path;
    } else if (complaint.photo_path.startsWith('assets/uploads/')) {
      // Complaint photos are stored in forms/complaints/public/assets/uploads/
      photoUrl = `${BASE_URL}/forms/complaints/public/${complaint.photo_path}`;
    } else if (complaint.photo_path.startsWith('uploads/')) {
      // General uploads folder
      photoUrl = `${BASE_URL}/${complaint.photo_path}`;
    } else {
      // Assume it's a relative path from root
      photoUrl = `${BASE_URL}/${complaint.photo_path}`;
    }
    
    html += `
            <div class="detail-section">
                <h3>Photo Evidence</h3>
                <img src="${escapeHtml(photoUrl)}" alt="Complaint photo" style="max-width: 100%; border-radius: 8px;" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22%3E%3Crect fill=%22%23f3f4f6%22 width=%22400%22 height=%22300%22/%3E%3Ctext fill=%22%239ca3af%22 font-family=%22sans-serif%22 font-size=%2218%22 dy=%2210.5%22 font-weight=%22bold%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22%3EImage not found%3C/text%3E%3C/svg%3E';">
            </div>
        `;
  }

  html += `
        <hr class="section-separator">

        <div class="detail-section">
            <h3>Update Status</h3>
            <div style="display: flex; gap: 12px; align-items: center;">
                <select id="statusUpdate" class="filter-select" style="flex: 1;">
                    ${statusOptions
                      .map(
                        (status) => `
                        <option value="${status}" ${
                          complaint.status === status ? "selected" : ""
                        }>
                            ${statusLabels[status]}
                        </option>
                    `
                      )
                      .join("")}
                </select>
                <button class="btn btn-primary" onclick="updateStatus(${
                  complaint.id
                })">
                    <i class="fas fa-save"></i> Update Status
                </button>
            </div>
        </div>

        <hr class="section-separator">

        <div class="detail-section">
            <h3>Dispatch Details</h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Dispatch to</div>
                    <input 
                        type="text" 
                        id="dispatchTo" 
                        class="filter-select" 
                        style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px;"
                        placeholder="Enter dispatcher name"
                        value="${escapeHtml(complaint.dispatch_to || '')}"
                    >
                </div>
                <div class="detail-item">
                    <div class="detail-label">Mode of Dispatch</div>
                    <select id="dispatchMode" class="filter-select" style="width: 100%;">
                        <option value="">Select mode</option>
                        <option value="Handcarried" ${complaint.dispatch_mode === 'Handcarried' ? 'selected' : ''}>Handcarried</option>
                        <option value="Radio/SMS/Chat/E-mail" ${complaint.dispatch_mode === 'Radio/SMS/Chat/E-mail' ? 'selected' : ''}>Radio/SMS/Chat/E-mail</option>
                        <option value="Others" ${complaint.dispatch_mode === 'Others' ? 'selected' : ''}>Others</option>
                    </select>
                </div>
            </div>
            <div class="detail-grid" style="margin-top: 12px;">
                <div class="detail-item">
                    <div class="detail-label">Assigned/Dispatch By</div>
                    <div class="detail-value">${complaint.dispatch_by_name || 'Not yet dispatched'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Date and Time of Dispatch</div>
                    <div class="detail-value">${complaint.dispatch_date ? formatDate(complaint.dispatch_date) : 'Not yet dispatched'}</div>
                </div>
            </div>
            <div class="detail-item" style="margin-top: 12px;">
                <div class="detail-label">Action taken by concerned personnel</div>
                <textarea 
                    id="actionTaken" 
                    rows="3" 
                    style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 14px;"
                    placeholder="Enter action taken..."
                >${escapeHtml(complaint.action_taken || '')}</textarea>
            </div>
            <div class="detail-grid" style="margin-top: 12px;">
                <div class="detail-item">
                    <div class="detail-label">Acknowledged by</div>
                    <input 
                        type="text" 
                        id="acknowledgedBy" 
                        class="filter-select" 
                        style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px;"
                        placeholder="Enter name"
                        value="${escapeHtml(complaint.acknowledged_by || '')}"
                    >
                </div>
                <div class="detail-item">
                    <div class="detail-label">Date Settled</div>
                    <input 
                        type="datetime-local" 
                        id="dateSettled" 
                        class="filter-select" 
                        style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px;"
                        value="${complaint.date_settled ? formatDateTimeLocal(complaint.date_settled) : ''}"
                    >
                </div>
            </div>
            <button class="btn btn-primary" onclick="updateDispatch(${complaint.id})" style="margin-top: 12px;">
                <i class="fas fa-save"></i> Update Dispatch Details
            </button>
        </div>

        <hr class="section-separator">

        <div class="detail-section">
            <h3>Activity Timeline</h3>
            <div class="timeline" id="timeline-${complaint.id}">
    `;

  if (complaint.comments && complaint.comments.length > 0) {
    // Reverse comments to show newest first
    const reversedComments = [...complaint.comments].reverse();
    
    reversedComments.forEach((comment, index) => {
      // Show first 2 items, hide the rest
      const isHidden = index >= 2 ? ' style="display: none;"' : '';
      const hiddenClass = index >= 2 ? ' timeline-item-hidden' : '';
      
      html += `
                <div class="timeline-item${hiddenClass}"${isHidden}>
                    <div class="timeline-content">
                        <div class="timeline-message">${escapeHtml(
                          comment.message
                        )}</div>
                        <div class="timeline-meta">
                            ${comment.user_name || "System"} • ${formatDate(
        comment.created_at
      )}
                        </div>
                    </div>
                </div>
            `;
    });
    
    // Add "Read more" button if there are more than 2 items
    if (complaint.comments.length > 2) {
      html += `
                <button 
                    class="btn btn-sm" 
                    onclick="toggleTimeline(${complaint.id})"
                    id="timeline-toggle-${complaint.id}"
                    style="margin-top: 12px; width: 100%; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color);"
                >
                    <i class="fas fa-chevron-down"></i> Read more (${complaint.comments.length - 2} more)
                </button>
            `;
    }
  } else {
    html += '<p style="color: var(--text-secondary);">No activity yet</p>';
  }

  html += `
            </div>
        </div>

        <div class="detail-section">
            <h3>Add Comment</h3>
            <textarea 
                id="commentText" 
                rows="3" 
                style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 14px;"
                placeholder="Add a note or comment..."
            ></textarea>
            <button class="btn btn-primary" onclick="addComment(${complaint.id})" style="margin-top: 12px;">
                <i class="fas fa-comment"></i> Add Comment
            </button>
        </div>
    `;

  modalBody.innerHTML = html;
}

// Update complaint status
async function updateStatus(id) {
  const status = document.getElementById("statusUpdate").value;

  try {
    const response = await fetch(
      `${STAFF_URL}/complaints/api.php`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "update_status",
          id: id,
          status: status,
        }),
      }
    );

    const data = await response.json();

    if (data.success) {
      alert("Status updated successfully!");
      window.location.reload();
    } else {
      alert("Error: " + (data.error || "Failed to update status"));
    }
  } catch (error) {
    console.error("Error updating status:", error);
    alert("Failed to update status");
  }
}

// Add comment
async function addComment(id) {
  const comment = document.getElementById("commentText").value.trim();

  if (!comment) {
    alert("Please enter a comment");
    return;
  }

  try {
    const response = await fetch(
      `${STAFF_URL}/complaints/api.php`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "add_comment",
          id: id,
          comment: comment,
        }),
      }
    );

    const data = await response.json();

    if (data.success) {
      alert("Comment added successfully!");
      viewComplaint(id); // Reload details
    } else {
      alert("Error: " + (data.error || "Failed to add comment"));
    }
  } catch (error) {
    console.error("Error adding comment:", error);
    alert("Failed to add comment");
  }
}

// Update dispatch details
async function updateDispatch(id) {
  const dispatchTo = document.getElementById("dispatchTo").value.trim();
  const dispatchMode = document.getElementById("dispatchMode").value;
  const actionTaken = document.getElementById("actionTaken").value.trim();
  const acknowledgedBy = document.getElementById("acknowledgedBy").value.trim();
  const dateSettled = document.getElementById("dateSettled").value;

  try {
    const response = await fetch(
      `${STAFF_URL}/complaints/api.php`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "update_dispatch",
          id: id,
          dispatch_to: dispatchTo,
          dispatch_mode: dispatchMode,
          action_taken: actionTaken,
          acknowledged_by: acknowledgedBy,
          date_settled: dateSettled || null,
        }),
      }
    );

    const data = await response.json();

    if (data.success) {
      alert("Dispatch details updated successfully!");
      viewComplaint(id); // Reload details
    } else {
      alert("Error: " + (data.error || "Failed to update dispatch details"));
    }
  } catch (error) {
    console.error("Error updating dispatch details:", error);
    alert("Failed to update dispatch details");
  }
}

// Close modal
function closeModal() {
  const modal = document.getElementById("complaintModal");
  modal.classList.remove("active");
}

// Toggle timeline visibility
function toggleTimeline(complaintId) {
  const hiddenItems = document.querySelectorAll('.timeline-item-hidden');
  const toggleBtn = document.getElementById(`timeline-toggle-${complaintId}`);
  
  if (!hiddenItems.length || !toggleBtn) return;
  
  const isExpanded = hiddenItems[0].style.display !== 'none';
  
  hiddenItems.forEach(item => {
    item.style.display = isExpanded ? 'none' : 'block';
  });
  
  if (isExpanded) {
    const hiddenCount = hiddenItems.length;
    toggleBtn.innerHTML = `<i class="fas fa-chevron-down"></i> Read more (${hiddenCount} more)`;
  } else {
    toggleBtn.innerHTML = `<i class="fas fa-chevron-up"></i> Show less`;
  }
}

// Close modal when clicking outside
document.addEventListener("click", function (event) {
  const modal = document.getElementById("complaintModal");
  if (event.target === modal) {
    closeModal();
  }
});

// Utility functions
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

function formatDateTimeLocal(dateString) {
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
}

// Auto-open modal if 'view' parameter is present in URL
document.addEventListener('DOMContentLoaded', function() {
  const urlParams = new URLSearchParams(window.location.search);
  const viewId = urlParams.get('view');
  
  if (viewId) {
    // Open the modal with the complaint ID
    viewComplaint(parseInt(viewId));
    
    // Remove the 'view' parameter from URL without reloading
    const newUrl = window.location.pathname + '?' + 
      Array.from(urlParams.entries())
        .filter(([key]) => key !== 'view')
        .map(([key, value]) => `${key}=${value}`)
        .join('&');
    window.history.replaceState({}, '', newUrl || window.location.pathname);
  }
});
