// Custom Tailwind Configuration
tailwind.config = {
    theme: {
        extend: {
            colors: {
                background: '#ffffff',
                foreground: '#030213',
                card: '#ffffff',
                'card-foreground': '#030213',
                primary: '#030213',
                'primary-foreground': '#ffffff',
                secondary: '#f3f3f5',
                'secondary-foreground': '#030213',
                muted: '#ececf0',
                'muted-foreground': '#717182',
                accent: '#e9ebef',
                'accent-foreground': '#030213',
                border: 'rgba(0, 0, 0, 0.1)',
                input: 'transparent',
                'input-background': '#f3f3f5',
                ring: '#717182',
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-up': 'slideUp 0.5s ease-out',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
            }
        }
    }
};

// Lucide icons initialization
lucide.createIcons();

// Simple Login/Register/Logout logic
let isLoggedIn = false;
let currentUser = null;

function showLoginState() {
    const accountText = document.getElementById('account-text');
    const guestMenu = document.getElementById('guest-menu');
    const userMenu = document.getElementById('user-menu');

    if (isLoggedIn) {
        if (accountText) {
            accountText.textContent = currentUser;
        }
        if (guestMenu) {
            guestMenu.style.display = 'none';
        }
        if (userMenu) {
            userMenu.style.display = 'block';
        }
    } else {
        if (accountText) {
            accountText.textContent = 'Account';
        }
        if (guestMenu) {
            guestMenu.style.display = 'block';
        }
        if (userMenu) {
            userMenu.style.display = 'none';
        }
    }
}

// Modal functions
function showLoginForm() {
    document.getElementById('login-modal').style.display = 'flex';
}

function showRegisterForm() {
    document.getElementById('register-modal').style.display = 'flex';
}

function showLoginFormFromRegister() {
    document.getElementById('register-modal').style.display = 'none';
    showLoginForm();
}

function showRegisterFormFromLogin() {
    document.getElementById('login-modal').style.display = 'none';
    showRegisterForm();
}

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    const loginModal = document.getElementById('login-modal');
    const registerModal = document.getElementById('register-modal');

    if (event.target === loginModal) {
        loginModal.style.display = 'none';
    }
    if (event.target === registerModal) {
        registerModal.style.display = 'none';
    }
});

// Form submission handling
document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const email = document.getElementById('login-email').value;
    // In a real application, you would handle login with a server.
    // This is a simple mock.
    if (email) {
        isLoggedIn = true;
        currentUser = email.split('@')[0];
        showLoginState();
        document.getElementById('login-modal').style.display = 'none';

        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up';
        notification.textContent = `Welcome back, ${currentUser}!`;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});

document.getElementById('register-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const username = document.getElementById('register-username').value;
    const email = document.getElementById('register-email').value;
    // In a real application, you would handle registration with a server.
    // This is a simple mock.
    if (username && email) {
        isLoggedIn = true;
        currentUser = username;
        showLoginState();
        document.getElementById('register-modal').style.display = 'none';

        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up';
        notification.textContent = `Account created! Welcome, ${currentUser}!`;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});

function logoutUser() {
    if (confirm('Are you sure you want to logout?')) {
        isLoggedIn = false;
        currentUser = null;
        showLoginState();
        
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-orange-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up';
        notification.textContent = 'Successfully logged out!';
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Scroll to recipes section
function scrollToRecipes() {
    const recipesSection = document.getElementById('recipes-section');
    if (recipesSection) {
        recipesSection.scrollIntoView({
            behavior: 'smooth'
        });
    }
}

// Initialize the page with guest state
document.addEventListener('DOMContentLoaded', function() {
    showLoginState();
});