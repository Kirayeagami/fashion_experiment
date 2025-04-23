document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('dark-mode-toggle-btn');
    const body = document.body;

    // Load saved mode from localStorage
    const savedMode = localStorage.getItem('mode');
    console.log('Saved mode:', savedMode);
    if (savedMode) {
        body.classList.add(savedMode);
        console.log('Applied mode class:', savedMode);
    } else {
        // Default to bright mode
        body.classList.add('bright-mode');
        console.log('Applied default mode: bright-mode');
    }

    toggleButton.addEventListener('click', function () {
        if (body.classList.contains('dark-mode')) {
            body.classList.remove('dark-mode');
            body.classList.add('bright-mode');
            localStorage.setItem('mode', 'bright-mode');
            console.log('Switched to bright-mode');
        } else {
            body.classList.remove('bright-mode');
            body.classList.add('dark-mode');
            localStorage.setItem('mode', 'dark-mode');
            console.log('Switched to dark-mode');
        }
    });
});
