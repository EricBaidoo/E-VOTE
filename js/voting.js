// Voting System JavaScript Functions

// Validate vote form before submission
document.addEventListener('DOMContentLoaded', function() {
    const voteForm = document.getElementById('voteForm');

    function highlightSelection(container) {
        container.querySelectorAll('.candidate-option').forEach(function(option) {
            const input = option.querySelector('input');
            if (input && input.checked) {
                option.classList.add('bg-light', 'border-primary');
            } else {
                option.classList.remove('bg-light', 'border-primary');
            }
        });
    }

    function enforceMaxSelections(container) {
        const max = parseInt(container.getAttribute('data-max-select') || '1', 10);
        const checkboxes = container.querySelectorAll('input[type="checkbox"]');
        const checked = Array.from(checkboxes).filter(cb => cb.checked);
        if (checked.length > max) {
            // uncheck the last one
            const last = checked.pop();
            last.checked = false;
        }
    }

    if (voteForm) {
        // Submit validation per position based on max-select
        voteForm.addEventListener('submit', function(e) {
            const groups = document.querySelectorAll('.position-group');
            let valid = true;
            groups.forEach(function(group) {
                const max = parseInt(group.getAttribute('data-max-select') || '1', 10);
                const radios = group.querySelectorAll('input[type="radio"]');
                const checkboxes = group.querySelectorAll('input[type="checkbox"]');
                if (radios.length > 0) {
                    const selected = group.querySelector('input[type="radio"]:checked');
                    if (!selected) valid = false;
                } else if (checkboxes.length > 0) {
                    const checked = Array.from(checkboxes).filter(cb => cb.checked);
                    if (checked.length !== max) valid = false;
                }
            });
            if (!valid) {
                e.preventDefault();
                alert('Please complete selections for all positions (respecting limits).');
                return false;
            }

            const confirmCheckbox = document.getElementById('confirmVote');
            if (!confirmCheckbox || !confirmCheckbox.checked) {
                e.preventDefault();
                alert('Please confirm your vote!');
                return false;
            }
        });

        // Change handlers
        document.querySelectorAll('.position-group').forEach(function(group) {
            group.addEventListener('change', function(e) {
                if (e.target.matches('input[type="checkbox"]')) {
                    enforceMaxSelections(group);
                }
                highlightSelection(group);
            });
        });
    }
});

// Format currency
function formatCurrency(value) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
}

// Show notification
function showNotification(message, type = 'info') {
    const alertClass = `alert-${type}`;
    const alertHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('main') || document.body;
    const alertElement = document.createElement('div');
    alertElement.innerHTML = alertHTML;
    container.insertBefore(alertElement.firstElementChild, container.firstChild);
}

// Confirm action
function confirmAction(message) {
    return confirm(message);
}

// Toggle password visibility
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

// Format date
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Validate email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showNotification('Copied to clipboard!', 'success');
    }).catch(function(err) {
        showNotification('Failed to copy!', 'danger');
    });
}

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Initialize Bootstrap popovers
document.addEventListener('DOMContentLoaded', function() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});
