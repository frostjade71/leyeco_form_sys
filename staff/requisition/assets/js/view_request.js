/**
 * View Request JavaScript
 * Handles approval actions and inline editing
 */

// Show custom confirmation modal
function showConfirmModal(title, message, icon, onConfirm) {
    const modal = document.getElementById('confirmModal');
    const titleEl = document.getElementById('confirmTitle');
    const messageEl = document.getElementById('confirmMessage');
    const iconEl = document.getElementById('confirmIcon');
    const confirmBtn = document.getElementById('confirmOk');
    const cancelBtn = document.getElementById('confirmCancel');
    
    titleEl.textContent = title;
    messageEl.textContent = message;
    iconEl.textContent = icon;
    
    modal.style.display = 'flex';
    
    // Remove old event listeners
    const newConfirmBtn = confirmBtn.cloneNode(true);
    const newCancelBtn = cancelBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
    
    // Add new event listeners
    newConfirmBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        onConfirm();
    });
    
    newCancelBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });
    
    // Close on background click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
}

// Handle approval action
async function handleApproval(action) {
    const form = document.getElementById('approvalForm');
    const formData = new FormData(form);
    
    const data = {
        requisition_id: formData.get('requisition_id'),
        approval_level: formData.get('approval_level'),
        action: action,
        remarks: formData.get('remarks')
    };
    
    const title = action === 'approved' ? 'Approve Request' : 'Reject Request';
    const message = action === 'approved' 
        ? 'Are you sure you want to approve this requisition request?' 
        : 'Are you sure you want to reject this requisition request?';
    const icon = action === 'approved' ? '✅' : '❌';
    
    showConfirmModal(title, message, icon, async () => {
        try {
            const response = await fetch(`${window.location.origin}/staff/requisition/api/process_approval.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Reload to show updated status
                window.location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Approval error:', error);
            alert('An error occurred. Please try again.');
        }
    });
}

// Inline editing for inventory fields
document.addEventListener('DOMContentLoaded', function() {
    const editableCells = document.querySelectorAll('.editable-cell');
    
    editableCells.forEach(cell => {
        cell.addEventListener('click', function() {
            // Don't edit if already editing
            if (this.classList.contains('editing')) return;
            
            const currentValue = this.dataset.value || '';
            const field = this.dataset.field;
            const itemId = this.closest('tr').dataset.itemId;
            
            // Store original content
            const originalContent = this.innerHTML;
            
            // Check if this is the remarks field (for dropdown)
            if (field === 'remarks') {
                // Create dropdown for remarks
                const select = document.createElement('select');
                select.className = 'inventory-input';
                let saved = false;
                
                // Add options
                const options = ['', 'Reviewing', 'Incomplete', 'Verified'];
                options.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt;
                    option.textContent = opt || '-';
                    if (opt === currentValue) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
                
                // Replace content with select
                this.innerHTML = '';
                this.appendChild(select);
                this.classList.add('editing');
                
                // Focus
                select.focus();
                
                // Save on change
                select.addEventListener('change', async function() {
                    if (!saved) {
                        saved = true;
                        await saveInventoryValue(cell, itemId, field, select.value, originalContent);
                    }
                });
                
                // Save on blur (only if not already saved)
                select.addEventListener('blur', async function() {
                    if (!saved) {
                        saved = true;
                        await saveInventoryValue(cell, itemId, field, select.value, originalContent);
                    }
                });
                
                // Cancel on Escape
                select.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        cell.innerHTML = originalContent;
                        cell.classList.remove('editing');
                    }
                });
            } else {
                // Create input for other fields
                const input = document.createElement('input');
                input.type = 'text';

                // Special handling for Balance to Purchase
                if (field === 'balance_for_purchase') {
                    // Check initial value - if it has letters, don't add ₱
                    let cleanVal = currentValue.replace(/₱/g, '').trim();
                    const hasLetters = /[a-zA-Z]/.test(cleanVal);
                    
                    if (!hasLetters && cleanVal) {
                        // Ensure purely numeric has ₱
                        cleanVal = cleanVal.replace(/[^\d.]/g, '');
                        input.value = cleanVal ? '₱' + cleanVal : '';
                    } else {
                        input.value = cleanVal;
                    }
                    
                    input.addEventListener('input', function() {
                        let val = this.value;
                        
                        // Check if input contains any letters
                        if (/[a-zA-Z]/.test(val)) {
                            // If user types letters, treat as text - remove any ₱ sign
                            this.value = val.replace(/₱/g, '');
                        } else {
                            // If only numbers/dots/symbols, treat as currency
                            let cleanVal = val.replace(/[^\d.]/g, '');
                            
                            // Ensure only one decimal point
                            const parts = cleanVal.split('.');
                            if (parts.length > 2) {
                                cleanVal = parts[0] + '.' + parts.slice(1).join('');
                            }
                            
                            // Add currency sign if we have a number
                            this.value = cleanVal ? '₱' + cleanVal : '';
                        }
                    });
                } else {
                    input.value = currentValue;
                }

                input.className = 'inventory-input';
                input.placeholder = field === 'balance_for_purchase' ? '₱0.00' : 'Enter value...';
                
                // Replace content with input
                this.innerHTML = '';
                this.appendChild(input);
                this.classList.add('editing');
                
                // Focus and select
                input.focus();
                input.select();
                
                // Save on blur
                input.addEventListener('blur', async function() {
                    await saveInventoryValue(cell, itemId, field, input.value, originalContent);
                });
                
                // Save on Enter, cancel on Escape
                input.addEventListener('keydown', async function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        await saveInventoryValue(cell, itemId, field, input.value, originalContent);
                    } else if (e.key === 'Escape') {
                        cell.innerHTML = originalContent;
                        cell.classList.remove('editing');
                    }
                });
            }
        });
    });
});

// Save inventory value
async function saveInventoryValue(cell, itemId, field, newValue, originalContent) {
    const oldValue = cell.dataset.value || '';
    
    // Trim whitespace
    newValue = newValue.trim();
    
    // If value hasn't changed, just restore
    if (newValue === oldValue) {
        cell.innerHTML = originalContent;
        cell.classList.remove('editing');
        return;
    }
    
    try {
        const response = await fetch(`${window.location.origin}/staff/requisition/api/update_inventory.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                item_id: itemId,
                field: field,
                value: newValue
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update the cell
            cell.dataset.value = newValue;
            const displayValue = newValue || (field === 'remarks' ? '-' : 'N/A');
            cell.innerHTML = `${displayValue} <span class="edit-icon">✏️</span>`;
            cell.classList.remove('editing');
            
            // alert(result.message || 'Updated successfully');
        } else {
            alert('Error: ' + result.message);
            cell.innerHTML = originalContent;
            cell.classList.remove('editing');
        }
    } catch (error) {
        console.error('Update error:', error);
        alert('An error occurred while updating');
        cell.innerHTML = originalContent;
        cell.classList.remove('editing');
    }
}
