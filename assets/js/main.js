/**
 * SkillSwap Main JavaScript
 * Core functionality for the SkillSwap application
 */

// Theme Management
class ThemeManager {
    constructor() {
        this.init();
    }

    init() {
        // Load saved theme
        const savedTheme = localStorage.getItem('skillswap-theme');
        if (savedTheme === 'light') {
            document.body.classList.add('theme-light');
        }

        // Setup theme toggle
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => this.toggleTheme());
        }
    }

    toggleTheme() {
        document.body.classList.toggle('theme-light');
        const isLight = document.body.classList.contains('theme-light');
        localStorage.setItem('skillswap-theme', isLight ? 'light' : 'dark');
    }
}

// Form Validation
class FormValidator {
    static validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    static validatePassword(password) {
        return password.length >= 6;
    }

    static showError(element, message) {
        // Remove existing error
        const existingError = element.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }

        // Add new error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        element.parentNode.appendChild(errorDiv);
        element.classList.add('error');
    }

    static clearError(element) {
        const existingError = element.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        element.classList.remove('error');
    }
}

// Alert System
class AlertSystem {
    static show(message, type = 'info', duration = 5000) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-floating`;
        alert.innerHTML = `
            <span>${message}</span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        `;

        document.body.appendChild(alert);

        // Auto remove after duration
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, duration);
    }
}

// Smooth Scrolling
function smoothScroll(target) {
    const element = document.querySelector(target);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Password Toggle Functionality
function togglePassword(inputId = 'password') {
    const passwordInput = document.getElementById(inputId);
    const toggleButton = passwordInput.parentNode.querySelector('.password-toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <path d="M9 12a3 3 0 1 0 6 0 3 3 0 0 0-6 0"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
        `;
    } else {
        passwordInput.type = 'password';
        toggleButton.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        `;
    }
}

// Form Validation Functions (for backward compatibility)
function validateForm() {
    const username = document.querySelector('input[name="username"]').value;
    const password = document.querySelector('input[name="password"]').value;
    
    if (!username.trim()) {
        AlertSystem.show('Email must be filled out', 'error');
        return false;
    }
    
    if (!FormValidator.validateEmail(username)) {
        AlertSystem.show('Please enter a valid email address', 'error');
        return false;
    }
    
    if (!password.trim()) {
        AlertSystem.show('Password must be filled out', 'error');
        return false;
    }
    
    return true;
}

function validateSignupForm() {
    const name = document.querySelector('input[name="name"]').value;
    const email = document.querySelector('input[name="email"]').value;
    const password = document.querySelector('input[name="password"]').value;
    const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
    
    if (!name.trim()) {
        AlertSystem.show('Name must be filled out', 'error');
        return false;
    }
    
    if (!email.trim()) {
        AlertSystem.show('Email must be filled out', 'error');
        return false;
    }
    
    if (!FormValidator.validateEmail(email)) {
        AlertSystem.show('Please enter a valid email address', 'error');
        return false;
    }
    
    if (!password.trim()) {
        AlertSystem.show('Password must be filled out', 'error');
        return false;
    }
    
    if (!FormValidator.validatePassword(password)) {
        AlertSystem.show('Password must be at least 6 characters long', 'error');
        return false;
    }
    
    if (password !== confirmPassword) {
        AlertSystem.show('Passwords do not match', 'error');
        return false;
    }
    
    return true;
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme manager
    new ThemeManager();
    
    // Add floating animation to cards if they exist
    const cards = document.querySelectorAll('.floating-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.5}s`;
    });
    
    // Add smooth scrolling to anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            smoothScroll(this.getAttribute('href'));
        });
    });
    
    // Auto-hide alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(alert => {
        if (!alert.classList.contains('alert-permanent')) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }
    });
});

// Export for use in other scripts
window.SkillSwap = {
    ThemeManager,
    FormValidator,
    AlertSystem,
    smoothScroll,
    togglePassword,
    validateForm,
    validateSignupForm
};