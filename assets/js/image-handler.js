document.addEventListener('DOMContentLoaded', () => {
    const productImage = document.querySelector('.product-image img');
    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    document.body.appendChild(overlay);

    // Handle image click for zoom
    productImage.addEventListener('click', () => {
        if (productImage.classList.contains('zoomed')) {
            // Zoom out
            productImage.classList.remove('zoomed');
            overlay.style.display = 'none';
        } else {
            // Zoom in
            productImage.classList.add('zoomed');
            overlay.style.display = 'block';
        }
    });

    // Close zoom when clicking outside
    overlay.addEventListener('click', () => {
        productImage.classList.remove('zoomed');
        overlay.style.display = 'none';
    });

    // Handle escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && productImage.classList.contains('zoomed')) {
            productImage.classList.remove('zoomed');
            overlay.style.display = 'none';
        }
    });
});
