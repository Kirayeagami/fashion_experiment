// Header behavior
const header = document.querySelector('header');
let timeoutId;
const hideDelay = 2000; // 2 seconds

// Function to show header
function showHeader() {
    header.classList.remove('header-hidden');
    resetTimeout();
}

// Function to hide header
function hideHeader() {
    header.classList.add('header-hidden');
}

// Reset timeout when there's activity
function resetTimeout() {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(hideHeader, hideDelay);
}

// Show header on mouse enter
header.addEventListener('mouseenter', showHeader);

// Hide header on mouse leave
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

// Show header when there's user interaction
document.addEventListener('mousemove', showHeader);
document.addEventListener('keydown', showHeader);
document.addEventListener('click', showHeader);

// Dark mode functionality
const darkModeToggle = document.querySelector('.dark-mode-toggle');
const body = document.body;

// Check for saved theme
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
    body.classList.add('dark-mode');
}

// Toggle dark mode
darkModeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    
    // Save theme preference
    if (body.classList.contains('dark-mode')) {
        localStorage.setItem('theme', 'dark');
    } else {
        localStorage.setItem('theme', 'light');
    }
});