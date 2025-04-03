document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
        });
    }

    // Tab functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');

            // Remove active class from all buttons and contents
            tabBtns.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Modal functionality for gallery
    const galleryItems = document.querySelectorAll('.gallery-item');
    const modal = document.getElementById('car-details-modal');
    const closeModal = document.querySelector('.close-modal');

    if (galleryItems.length && modal) {
        galleryItems.forEach(item => {
            item.addEventListener('click', function() {
                const carId = this.getAttribute('data-car-id');
                window.location.href = `gallery.php?car_id=${carId}`;
            });
        });

        if (closeModal) {
            closeModal.addEventListener('click', function() {
                window.location.href = 'gallery.php';
            });
        }

        // Show modal if there's a car ID in the URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('car_id')) {
            modal.style.display = 'block';
        }
    }

    // Admin car form toggle
    const addCarBtn = document.getElementById('add-car-btn');
    const carFormContainer = document.getElementById('car-form-container');
    const cancelEditBtn = document.getElementById('cancel-edit');

    if (addCarBtn && carFormContainer) {
        addCarBtn.addEventListener('click', function() {
            carFormContainer.classList.add('active');
            window.scrollTo({
                top: carFormContainer.offsetTop - 20,
                behavior: 'smooth'
            });
        });
    }

    if (cancelEditBtn && carFormContainer) {
        cancelEditBtn.addEventListener('click', function() {
            carFormContainer.classList.remove('active');
            window.location.href = 'cars.php';
        });
    }

    // Form validation for signup
    const signupForm = document.getElementById('signup-form');
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const username = document.getElementById('username').value;

            // Validate username
            if (!/^[a-zA-Z0-9_]+$/.test(username)) {
                alert('Username can only contain letters, numbers and underscores');
                e.preventDefault();
                return;
            }

            // Validate password strength
            if (password.length < 8) {
                alert('Password must be at least 8 characters long');
                e.preventDefault();
                return;
            }

            if (!/[A-Z]/.test(password)) {
                alert('Password must contain at least one uppercase letter');
                e.preventDefault();
                return;
            }

            if (!/[a-z]/.test(password)) {
                alert('Password must contain at least one lowercase letter');
                e.preventDefault();
                return;
            }

            if (!/[0-9]/.test(password)) {
                alert('Password must contain at least one number');
                e.preventDefault();
                return;
            }

            if (!/[^A-Za-z0-9]/.test(password)) {
                alert('Password must contain at least one special character');
                e.preventDefault();
                return;
            }

            // Check if passwords match
            if (password !== confirmPassword) {
                alert('Passwords do not match');
                e.preventDefault();
                return;
            }
        });
    }

    // Phone number formatting
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    // Date input min value setting
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        if (!input.min) {
            input.min = new Date().toISOString().split('T')[0];
        }
    });

    // Profile picture preview
    const profilePictureInput = document.getElementById('profile_picture');
    if (profilePictureInput) {
        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.querySelector('.profile-picture img');
                    if (preview) {
                        preview.src = event.target.result;
                    } else {
                        const defaultPreview = document.querySelector('.default-picture');
                        if (defaultPreview) {
                            defaultPreview.innerHTML = `<img src="${event.target.result}" alt="Profile Preview">`;
                        }
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});