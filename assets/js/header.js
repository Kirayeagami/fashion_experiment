/**
 * Header behavior script
 * Handles auto-hide/show of header based on user interaction and scroll
 * Also manages dark mode toggle and saves preference in localStorage
 */

const header = document.querySelector('header');
let timeoutId;
const hideDelay = 2000; // 2 seconds

/**
 * Show the header and reset hide timeout
 */
function showHeader() {
    header.classList.remove('header-hidden');
    resetTimeout();
}

/**
 * Hide the header
 */
function hideHeader() {
    header.classList.add('header-hidden');
}

/**
 * Reset the timeout to hide header after inactivity
 */
function resetTimeout() {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(hideHeader, hideDelay);
}

// Show header on mouse enter
header.addEventListener('mouseenter', showHeader);

// Hide header on mouse leave after delay
header.addEventListener('mouseleave', () => {
    timeoutId = setTimeout(hideHeader, hideDelay);
});

// Show header on scroll up
let lastScroll = 0;
window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll <= 0) {
        showHeader();
        return;
    }

    if (currentScroll < lastScroll) {
        // Scrolling up
        showHeader();
    }

    lastScroll = currentScroll;
});

// Show header on user interaction (mouse move, key press, click)
document.addEventListener('mousemove', showHeader);
document.addEventListener('keydown', showHeader);
document.addEventListener('click', showHeader);

// Dark mode toggle functionality
const darkModeToggle = document.querySelector('.dark-mode-toggle');
const body = document.body;

// Apply saved theme from localStorage
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
    body.classList.add('dark-mode');
}

// Toggle dark mode on button click and save preference
darkModeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    
    if (body.classList.contains('dark-mode')) {
        localStorage.setItem('theme', 'dark');
    } else {
        localStorage.setItem('theme', 'light');
    }
});
