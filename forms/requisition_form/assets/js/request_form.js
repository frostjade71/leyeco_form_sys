/**
 * Request Form JavaScript
 * Handles dynamic item addition and form submission
 */

let itemCounter = 0;

// Add item to table
function addItem() {
    itemCounter++;
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.id = `item-${itemCounter}`;
    
    row.innerHTML = `
        <td>
            <input type="number" 
                   name="items[${itemCounter}][quantity]" 
                   min="1" 
                   required 
                   class="form-control">
        </td>
        <td>
            <select name="items[${itemCounter}][unit]" required class="form-control">
                <option value="">Select</option>
                ${getUnitOptions()}
            </select>
        </td>
        <td>
            <input type="text" 
                   name="items[${itemCounter}][description]" 
                   required 
                   class="form-control"
                   placeholder="Item description">
        </td>
        <td>
            <button type="button" 
                    class="btn btn-danger btn-sm" 
                    onclick="removeItem(${itemCounter})">
                🗑️
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
}

// Get unit options
function getUnitOptions() {
    const units = ['pcs', 'kg', 'meters', 'liters', 'boxes', 'rolls', 'sets', 'pairs', 'units'];
    return units.map(unit => `<option value="${unit}">${unit}</option>`).join('');
}

// Remove item from table
function removeItem(id) {
    const row = document.getElementById(`item-${id}`);
    if (row) {
        row.remove();
    }
}

// Form submission
document.addEventListener('DOMContentLoaded', function() {
    // Add initial item
    addItem();
    
    // Add item button
    document.getElementById('addItemBtn').addEventListener('click', addItem);
    
    // Form submission
    const form = document.getElementById('requisitionForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate at least one item
        const items = document.querySelectorAll('#itemsTableBody tr');
        if (items.length === 0) {
            alert('Please add at least one item to the requisition.');
            return;
        }
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        
        try {
            // Prepare form data
            const formData = new FormData(form);
            
            // Submit form
            const submitUrl = (typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/forms/requisition_form/public/submit_request.php';
            const response = await fetch(submitUrl, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Show success modal
                document.getElementById('rfNumber').textContent = result.rf_number;
                document.getElementById('successModal').style.display = 'flex';
            } else {
                alert('Error: ' + result.message);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Request';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while submitting the request. Please try again.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Request';
        }
    });
});
