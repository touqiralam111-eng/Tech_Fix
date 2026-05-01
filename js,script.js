// jsscript.js - Complete Professional Version
class TechFixApp {
    constructor() {
        this.init();
        this.initializeServiceWorker();
    }

    init() {
        this.initializeEventListeners();
        this.initializeComponents();
        this.initializeAnalytics();
        this.checkAuthentication();
        this.initializeRealTimeUpdates();
    }

    initializeEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.handleFormValidation();
            this.handlePasswordToggle();
            this.handleMobileMenu();
            this.initializeCharts();
            this.initializeFileUpload();
            this.initializeAJAXForms();
            this.initializeSmoothScrolling();
        });
    }

    initializeComponents() {
        this.initializeTooltips();
        this.initializeLoadingStates();
        this.initializeNotifications();
        this.initializeSessionTimer();
    }

    // Form Validation with Real-time Feedback
    handleFormValidation() {
        const forms = document.querySelectorAll('form');

        forms.forEach(form => {
            // Real-time validation
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });

                input.addEventListener('input', () => {
                    this.clearFieldError(input);
                });
            });

            // Form submission validation
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    this.showNotification('Please correct the errors before submitting.', 'error');
                } else {
                    this.showLoadingState(form);
                }
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('[required]');

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        // Custom validation for specific forms
        if (form.id === 'registerForm') {
            isValid = this.validateRegistration(form) && isValid;
        } else if (form.id === 'contactForm') {
            isValid = this.validateContact(form) && isValid;
        }

        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.getAttribute('name');
        let isValid = true;

        this.clearFieldError(field);

        // Required field validation
        if (field.hasAttribute('required') && !value) {
            this.showFieldError(field, 'This field is required');
            isValid = false;
        }

        // Email validation
        else if (field.type === 'email' && value) {
            if (!this.isValidEmail(value)) {
                this.showFieldError(field, 'Please enter a valid email address');
                isValid = false;
            }
        }

        // Password validation
        else if (field.type === 'password' && value) {
            if (value.length < 6) {
                this.showFieldError(field, 'Password must be at least 6 characters');
                isValid = false;
            } else if (!this.isStrongPassword(value)) {
                this.showFieldError(field, 'Password should include uppercase, lowercase, and numbers');
                isValid = false;
            }
        }

        // Phone validation
        else if (fieldName === 'phone' && value) {
            if (!this.isValidPhone(value)) {
                this.showFieldError(field, 'Please enter a valid phone number');
                isValid = false;
            }
        }

        // Confirm password validation
        else if (fieldName === 'confirm_password' && value) {
            const password = document.querySelector('[name="password"]').value;
            if (value !== password) {
                this.showFieldError(field, 'Passwords do not match');
                isValid = false;
            }
        }

        if (isValid) {
            field.classList.add('valid');
        }

        return isValid;
    }

    validateRegistration(form) {
        const username = form.querySelector('[name="username"]').value;
        const terms = form.querySelector('[name="terms"]');

        let isValid = true;

        if (username.length < 3) {
            this.showFieldError(form.querySelector('[name="username"]'), 'Username must be at least 3 characters');
            isValid = false;
        }

        if (terms && !terms.checked) {
            this.showNotification('Please accept the terms and conditions', 'error');
            isValid = false;
        }

        return isValid;
    }

    validateContact(form) {
        const message = form.querySelector('[name="message"]').value;
        let isValid = true;

        if (message.length < 10) {
            this.showFieldError(form.querySelector('[name="message"]'), 'Message must be at least 10 characters');
            isValid = false;
        }

        return isValid;
    }

    // Utility Functions
    isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    isValidPhone(phone) {
        const re = /^[\+]?[1-9][\d]{0,15}$/;
        return re.test(phone.replace(/[\s\-\(\)]/g, ''));
    }

    isStrongPassword(password) {
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/.test(password);
    }

    showFieldError(field, message) {
        field.classList.add('error');

        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.style.cssText = `
            color: #f72585;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            font-weight: 600;
        `;
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;

        field.parentNode.appendChild(errorDiv);
    }

    clearFieldError(field) {
        field.classList.remove('error', 'valid');
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    }

    // Password Toggle
    handlePasswordToggle() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.password-toggle')) {
                const button = e.target.closest('.password-toggle');
                const input = button.previousElementSibling;
                const icon = button.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            }
        });
    }

    // Mobile Menu
    handleMobileMenu() {
        const menuToggle = document.createElement('button');
        menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
        menuToggle.className = 'menu-toggle';
        menuToggle.style.cssText = `
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
        `;

        const nav = document.querySelector('.nav-links');
        if (nav) {
            nav.parentNode.insertBefore(menuToggle, nav);

            menuToggle.addEventListener('click', () => {
                nav.classList.toggle('active');
                menuToggle.classList.toggle('active');
            });

            // Media query for mobile
            if (window.matchMedia('(max-width: 768px)').matches) {
                menuToggle.style.display = 'block';
                nav.style.display = 'none';

                menuToggle.addEventListener('click', () => {
                    if (nav.style.display === 'flex') {
                        nav.style.display = 'none';
                    } else {
                        nav.style.display = 'flex';
                    }
                });
            }
        }
    }

    // AJAX Form Handling
    initializeAJAXForms() {
        const ajaxForms = document.querySelectorAll('form[data-ajax]');

        ajaxForms.forEach(form => {
            form.addEventListener('submit', async(e) => {
                e.preventDefault();

                if (this.validateForm(form)) {
                    try {
                        const formData = new FormData(form);
                        const response = await this.makeAJAXRequest(form.action, form.method, formData);

                        if (response.success) {
                            this.showNotification(response.message, 'success');
                            if (response.redirect) {
                                setTimeout(() => {
                                    window.location.href = response.redirect;
                                }, 2000);
                            } else {
                                form.reset();
                            }
                        } else {
                            this.showNotification(response.message, 'error');
                        }
                    } catch (error) {
                        this.showNotification('An error occurred. Please try again.', 'error');
                        console.error('AJAX Error:', error);
                    }
                }
            });
        });
    }

    async makeAJAXRequest(url, method = 'POST', data = null) {
        const options = {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (data instanceof FormData) {
            options.body = data;
        } else if (data) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            return await response.json();
        } catch (error) {
            throw new Error(`Request failed: ${error.message}`);
        }
    }

    // File Upload Handling
    initializeFileUpload() {
        const fileInputs = document.querySelectorAll('input[type="file"]');

        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    this.validateFile(file, input);
                }
            });
        });
    }

    validateFile(file, input) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];

        if (file.size > maxSize) {
            this.showNotification('File size must be less than 5MB', 'error');
            input.value = '';
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            this.showNotification('Please select a valid file type (JPEG, PNG, GIF, PDF)', 'error');
            input.value = '';
            return;
        }

        this.showNotification('File selected successfully', 'success');
    }

    // Notifications System
    initializeNotifications() {
        this.checkNotifications();

        // Check for new notifications every 30 seconds
        setInterval(() => {
            if (!document.hidden) {
                this.checkNotifications();
            }
        }, 30000);
    }

    async checkNotifications() {
        if (!this.isLoggedIn()) return;

        try {
            const response = await this.makeAJAXRequest('get_notifications.php');
            if (response.notifications && response.notifications.length > 0) {
                response.notifications.forEach(notification => {
                    this.showNotification(notification.message, notification.type);
                });
            }
        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => {
            if (notification.parentElement) {
                notification.remove();
            }
        });

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: white;
            color: #333;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-left: 4px solid var(--${type});
            z-index: 10000;
            max-width: 400px;
            animation: slideInRight 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        `;

        const icons = {
            'info': 'info-circle',
            'success': 'check-circle',
            'warning': 'exclamation-triangle',
            'error': 'exclamation-circle'
        };

        notification.innerHTML = `
            <i class="fas fa-${icons[type]}" style="color: var(--${type});"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" style="
                background: none;
                border: none;
                margin-left: 1rem;
                cursor: pointer;
                color: #666;
            ">
                <i class="fas fa-times"></i>
            </button>
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Session Management
    initializeSessionTimer() {
        if (!this.isLoggedIn()) return;

        let warningShown = false;
        const checkSession = () => {
            const loginTime = parseInt(sessionStorage.getItem('loginTime') || localStorage.getItem('loginTime'));
            const currentTime = Date.now();
            const sessionDuration = currentTime - loginTime;
            const sessionLimit = 30 * 60 * 1000; // 30 minutes

            if (sessionDuration > sessionLimit - (5 * 60 * 1000) && !warningShown) {
                this.showNotification('Your session will expire in 5 minutes', 'warning');
                warningShown = true;
            }

            if (sessionDuration > sessionLimit) {
                this.showNotification('Session expired. Please login again.', 'error');
                setTimeout(() => {
                    window.location.href = 'logout.php';
                }, 2000);
            }
        };

        setInterval(checkSession, 60000); // Check every minute
    }

    // Analytics
    initializeAnalytics() {
        this.trackPageView();
        this.trackUserBehavior();
    }

    trackPageView() {
        const data = {
            page: window.location.pathname,
            referrer: document.referrer,
            timestamp: new Date().toISOString()
        };

        // Send to analytics endpoint
        this.makeAJAXRequest('track_analytics.php', 'POST', data)
            .catch(error => console.error('Analytics error:', error));
    }

    trackUserBehavior() {
        // Track clicks, form interactions, etc.
        document.addEventListener('click', (e) => {
            const target = e.target;
            if (target.matches('a, button, input[type="submit"]')) {
                const action = target.textContent || target.value || target.getAttribute('href');
                const data = {
                    action: action,
                    element: target.tagName,
                    timestamp: new Date().toISOString()
                };

                this.makeAJAXRequest('track_behavior.php', 'POST', data)
                    .catch(error => console.error('Behavior tracking error:', error));
            }
        });
    }

    // Real-time Updates
    initializeRealTimeUpdates() {
        if (this.isLoggedIn()) {
            // Simulate real-time updates (in real app, use WebSockets)
            setInterval(() => {
                this.updateUserStatus();
            }, 30000);
        }
    }

    async updateUserStatus() {
        try {
            const response = await this.makeAJAXRequest('get_user_updates.php');
            if (response.updates) {
                // Update UI with new data
                this.updateDashboardStats(response.stats);
            }
        } catch (error) {
            console.error('Update error:', error);
        }
    }

    updateDashboardStats(stats) {
        // Update statistics on the page
        Object.keys(stats).forEach(stat => {
            const element = document.querySelector(`[data-stat="${stat}"]`);
            if (element) {
                this.animateValue(element, parseInt(element.textContent), stats[stat], 1000);
            }
        });
    }

    animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            element.textContent = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    // Service Worker for PWA
    initializeServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('SW registered: ', registration);
                })
                .catch(registrationError => {
                    console.log('SW registration failed: ', registrationError);
                });
        }
    }

    // Utility Methods
    isLoggedIn() {
        return document.body.classList.contains('logged-in') ||
            localStorage.getItem('isLoggedIn') === 'true';
    }

    showLoadingState(form) {
        const button = form.querySelector('button[type="submit"]');
        if (button) {
            const originalText = button.innerHTML;
            button.innerHTML = '<div class="loading"></div> Processing...';
            button.disabled = true;

            // Revert after 5 seconds if still processing
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 5000);
        }
    }

    initializeSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    initializeTooltips() {
        // Initialize tooltips if library is available
        if (typeof tippy !== 'undefined') {
            tippy('[data-tippy-content]', {
                placement: 'top',
                animation: 'scale',
                duration: 200,
            });
        }
    }

    initializeCharts() {
        // Initialize charts if Chart.js is available
        if (typeof Chart !== 'undefined') {
            this.initializeAnalyticsCharts();
        }
    }

    initializeAnalyticsCharts() {
        const ctx = document.getElementById('analyticsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Service Requests',
                        data: [12, 19, 3, 5, 2, 3],
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }
    }

    // Cookie Management
    setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
    }

    getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    deleteCookie(name) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    }
}

// Initialize the application
const techFixApp = new TechFixApp();

// Global utility functions
function formatPhoneNumber(phone) {
    return phone.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
}

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

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .form-control.error {
        border-color: #f72585;
        background: rgba(247, 37, 133, 0.05);
    }
    
    .form-control.valid {
        border-color: #4cc9f0;
        background: rgba(76, 201, 240, 0.05);
    }
    
    .password-toggle {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
    }
    
    @media (max-width: 768px) {
        .menu-toggle {
            display: block !important;
        }
        
        .nav-links {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            flex-direction: column;
            padding: 1rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .nav-links.active {
            display: flex;
        }
    }
`;
document.head.appendChild(style);