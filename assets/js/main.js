// ============================================================
// GLOBAL UTILITIES
// ============================================================

function $(selector) {
    return document.querySelector(selector);
}

function $$(selector) {
    return document.querySelectorAll(selector);
}

function formatCurrency(amount) {
    return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
}

function showAlert(message, type = 'success') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    alert.style.position = 'fixed';
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.zIndex = '10000';
    document.body.appendChild(alert);

    setTimeout(() => {
        alert.remove();
    }, 3000);
}

function confirmDelete(message = 'Apakah Anda yakin ingin menghapus?') {
    return confirm(message);
}

// ============================================================
// MODAL FUNCTIONS
// ============================================================

function openModal(modalId) {
    const modal = $('#' + modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = $('#' + modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

function closeAllModals() {
    $$('.modal').forEach(modal => {
        modal.classList.remove('active');
    });
}

// Close modal on outside click
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});

// Close modal on ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeAllModals();
    }
});

// ============================================================
// AJAX HELPER
// ============================================================

async function apiRequest(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };

    if (data && method !== 'GET') {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(url, options);
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// ============================================================
// FORM VALIDATION
// ============================================================

function validateForm(formId) {
    const form = $('#' + formId);
    if (!form) return false;

    const inputs = form.querySelectorAll('[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'var(--danger)';
            isValid = false;
        } else {
            input.style.borderColor = 'var(--border)';
        }
    });

    return isValid;
}

// ============================================================
// TABLE FUNCTIONS
// ============================================================

function searchTable(inputId, tableId) {
    const input = $('#' + inputId);
    const table = $('#' + tableId);

    if (!input || !table) return;

    input.addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });
}

function sortTable(tableId, columnIndex) {
    const table = $('#' + tableId);
    if (!table) return;

    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    rows.sort((a, b) => {
        const aText = a.cells[columnIndex].textContent;
        const bText = b.cells[columnIndex].textContent;
        return aText.localeCompare(bText);
    });

    rows.forEach(row => tbody.appendChild(row));
}

// ============================================================
// CHART HELPER (Simple Canvas Charts)
// ============================================================

function drawBarChart(canvasId, data, labels) {
    const canvas = $('#' + canvasId);
    if (!canvas) {
        console.error('Canvas element with id ' + canvasId + ' not found');
        return;
    }

    if (!data || data.length === 0) {
        console.warn('No data provided for bar chart');
        return;
    }

    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    const barWidth = width / data.length;
    const maxValue = Math.max(...data, 1); // Prevent division by zero

    // Clear canvas
    ctx.clearRect(0, 0, width, height);

    // Draw bars
    data.forEach((value, index) => {
        const barHeight = (value / maxValue) * (height - 40);
        const x = index * barWidth;
        const y = height - barHeight - 20;

        // Bar
        ctx.fillStyle = '#2563eb';
        ctx.fillRect(x + 10, y, barWidth - 20, barHeight);

        // Label
        ctx.fillStyle = '#64748b';
        ctx.font = '12px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(labels[index], x + barWidth / 2, height - 5);

        // Value
        ctx.fillStyle = '#1e293b';
        ctx.fillText(value.toLocaleString(), x + barWidth / 2, y - 5);
    });
}

function drawLineChart(canvasId, data, labels) {
    const canvas = $('#' + canvasId);
    if (!canvas) {
        console.error('Canvas element with id ' + canvasId + ' not found');
        return;
    }

    if (!data || data.length === 0) {
        console.warn('No data provided for line chart');
        return;
    }

    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    const padding = 40;
    const maxValue = Math.max(...data, 1); // Prevent division by zero

    // Clear canvas
    ctx.clearRect(0, 0, width, height);

    // Calculate points
    const points = data.map((value, index) => {
        const x = padding + (index * (width - padding * 2) / (data.length - 1 || 1));
        const y = height - padding - (value / maxValue) * (height - padding * 2);
        return { x, y, value };
    });

    // Draw line
    ctx.strokeStyle = '#2563eb';
    ctx.lineWidth = 2;
    ctx.beginPath();
    points.forEach((point, index) => {
        if (index === 0) {
            ctx.moveTo(point.x, point.y);
        } else {
            ctx.lineTo(point.x, point.y);
        }
    });
    ctx.stroke();

    // Draw points
    points.forEach((point, index) => {
        ctx.fillStyle = '#2563eb';
        ctx.beginPath();
        ctx.arc(point.x, point.y, 4, 0, Math.PI * 2);
        ctx.fill();

        // Labels
        ctx.fillStyle = '#64748b';
        ctx.font = '12px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(labels[index], point.x, height - 20);
    });
}

// ============================================================
// DATE FORMATTING
// ============================================================

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleDateString('id-ID', options);
}

// ============================================================
// PRINT FUNCTION
// ============================================================

function printReport() {
    window.print();
}

// ============================================================
// MOBILE MENU TOGGLE
// ============================================================

function toggleMobileMenu() {
    const sidebar = $('.sidebar');
    const mainContent = $('.main-content');
    const hamburger = $('.hamburger-menu');
    
    if (sidebar) {
        sidebar.classList.toggle('hidden');
    }
    
    if (mainContent) {
        mainContent.classList.toggle('sidebar-hidden');
    }
    
    if (hamburger) {
        hamburger.classList.toggle('active');
    }
}

// ============================================================
// INITIALIZATION
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    // Set active menu item
    const currentPage = window.location.pathname.split('/').pop();
    $$('.sidebar-menu a').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });

    // Initialize tooltips (if needed)
    $$('[data-tooltip]').forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            tooltip.style.position = 'absolute';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        });

        element.addEventListener('mouseleave', function() {
            const tooltip = $('.tooltip');
            if (tooltip) tooltip.remove();
        });
    });
});

// ============================================================
// EXPORT TO PDF (Simple version - uses browser print)
// ============================================================

function exportToPDF() {
    window.print();
}

// ============================================================
// NUMBER INPUT VALIDATION
// ============================================================

function validateNumberInput(input) {
    input.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
}

// Initialize number inputs
document.addEventListener('DOMContentLoaded', function() {
    $$('input[type="number"]').forEach(input => {
        validateNumberInput(input);
    });
});
