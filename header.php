<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amitabh Builders & Developers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f8f9fa;
            line-height: 1.6;
        }

        .offcanvas__logo {
            background-color: #fff;
        }

        .header-logo img {
            background-color: #fff;
        }

        .bell-container {
            position: relative;
            display: inline-block;
            font-size: 25px;
            cursor: pointer;
        }

        .bell-badge {
            position: absolute;
            top: 0;
            right: 0px;
            background: red;
            color: white;
            font-size: 11px;
            font-weight: bold;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            text-align: center;
            line-height: 15px;
        }

        /* Shake animation */
        @keyframes vibrate {
            0% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(-10deg);
            }

            50% {
                transform: rotate(10deg);
            }

            75% {
                transform: rotate(-10deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        .vibrating {
            animation: vibrate 0.3s ease-in-out infinite;
        }


        /* Booking Button Styles */
        .booking-btn {
            display: inline-block;
            padding: 12px 25px;
            /* background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); */
            background-color: #ff6600;
            color: white;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0 4px 15px rgba(106, 17, 203, 0.4);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            border: none;
            outline: none;
            margin-left: 15px;
        }

        .booking-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(106, 17, 203, 0.6);
            /* background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%); */
            background-color: #ff6600;
        }

        .booking-btn:active {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(106, 17, 203, 0.4);
        }

        .booking-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .booking-btn:hover::after {
            left: 100%;
        }

        .sidebar__toggle {
            background-color: white;
            padding: 5px;
            border-radius: 5px;
            margin-left: 15px;
        }

        .bar-icon {
            display: block;
        }

        /* Modal Styles - Improved Responsiveness */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            padding: 20px;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .booking-modal {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            transform: translateY(-30px);
            transition: transform 0.4s ease;
            position: relative;
        }

        .modal-overlay.active .booking-modal {
            transform: translateY(0);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 30px 20px;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            background: white;
            border-radius: 12px 12px 0 0;
            z-index: 10;
        }

        .modal-title {
            font-size: 26px;
            font-weight: 700;
            color: #1a365d;
            margin: 0;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #777;
            transition: color 0.3s;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close-modal:hover {
            color: #333;
            background-color: #f5f5f5;
        }

        .modal-body {
            padding: 0 30px 30px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
            font-size: 16px;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: #fcfcfc;
        }

        .form-control:focus {
            border-color: #6a11cb;
            outline: none;
            box-shadow: 0 0 0 3px rgba(106, 17, 203, 0.15);
            background-color: white;
        }

        textarea.form-control {
            min-height: 110px;
            resize: vertical;
            line-height: 1.5;
        }

        .submit-btn {
            display: block;
            width: 100%;
            padding: 16px;
            /* background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); */
            background-color: #ff6600;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 15px;
            letter-spacing: 0.5px;
        }

        .submit-btn:hover {
            /* background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%); */
            background-color: #ff6600;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(106, 17, 203, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .success-message {
            display: none;
            text-align: center;
            padding: 40px 30px;
            background: #f0fff4;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 4px solid #38a169;
        }

        .success-message.active {
            display: block;
        }

        .success-icon {
            font-size: 48px;
            color: #38a169;
            margin-bottom: 15px;
        }

        .success-message h3 {
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 22px;
        }

        .success-message p {
            color: #4a5568;
            font-size: 16px;
        }


        .error-message {
            text-align: center;
            padding: 20px;
            background-color: #ffebee;
            border: 1px solid #f44336;
            border-radius: 4px;
            color: #c62828;
            margin-top: 15px;
        }

        .error-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .error-message h3 {
            margin-bottom: 10px;
        }

        .error-message ul {
            text-align: left;
            margin-top: 10px;
            padding-left: 20px;
        }

        /* Responsive Design */
        @media (max-width: 991px) {
            .menu-with-search-wrapper {
                display: none;
            }

            .header-logo {
                margin-right: 20px;
            }
        }

        @media (max-width: 768px) {
            .modal-header {
                padding: 20px 25px 15px;
            }

            .modal-body {
                padding: 0 25px 25px;
            }

            .modal-title {
                font-size: 22px;
            }

            .form-group {
                margin-bottom: 18px;
            }

            .form-control {
                padding: 12px 16px;
            }
        }

        @media (max-width: 576px) {
            .modal-overlay {
                padding: 15px;
                align-items: flex-start;
            }

            .booking-modal {
                max-height: 95vh;
                margin-top: 20px;
            }

            .modal-header {
                padding: 18px 20px 14px;
            }

            .modal-body {
                padding: 0 20px 20px;
            }

            .modal-title {
                font-size: 20px;
            }

            .close-modal {
                font-size: 24px;
                width: 36px;
                height: 36px;
            }

            .form-label {
                font-size: 15px;
            }

            .form-control {
                padding: 11px 14px;
                font-size: 15px;
            }

            .submit-btn {
                padding: 14px;
                font-size: 16px;
            }

            .success-message {
                padding: 30px 20px;
            }

            .success-icon {
                font-size: 40px;
            }

            .success-message h3 {
                font-size: 20px;
            }

            .header-top {
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }

            .header-top-contact-info span {
                margin-left: 10px;
                margin-right: 10px;
            }

            .header-action {
                flex-direction: column;
                gap: 10px;
                width: 100%;
            }

            .login-btn,
            .booking-btn {
                width: 100%;
                margin-left: 0;
                text-align: center;
            }
        }

        @media (max-width: 400px) {
            .modal-overlay {
                padding: 10px;
            }

            .modal-header {
                padding: 15px 18px 12px;
            }

            .modal-body {
                padding: 0 18px 18px;
            }

            .modal-title {
                font-size: 18px;
            }
        }

        /* For very small screens */
        @media (max-height: 600px) {
            .booking-modal {
                max-height: 95vh;
            }

            .modal-body {
                padding-bottom: 20px;
            }

            .form-group {
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>
    <!-- Header area start -->
    <header>
        <div class="container-fluid bg-color-1">
            <div class="header-top">
                <div class="header-top-welcome-text">
                    <span class="welcome">Welcome to Amitabh Builders & Developers</span>
                </div>
                <div class="header-top-contact-info">
                    <span class="mail p-relative"><a href="mailto:" title="Email Id - Amitabh Builders & Developers">abdeveloperspl@gmail.com</a></span>
                    <span class="phone p-relative"><a href="tel:+919031079721" title="Contact Number - Amitabh Builders & Developers">+919031079721</a></span>
                </div>
            </div>
        </div>
        <div id="header-sticky" class="header-area header-style-one sticky">
            <div class="container-fluid">
                <div class="mega-menu-wrapper">
                    <div class="header-main">
                        <div class="header-left">
                            <div class="header-logo">
                                <a href="index.php" title="Home Page - Amitabh Builders & Developers">
                                    <img src="image/harihomes1-logo.png" title="Cheap and Best Property Dealers near Darbhanga" alt="Hari Homes Logo, residential sites near prime locations darbhanga">
                                </a>
                            </div>
                            <div class="menu-with-search-wrapper">
                                <div class="mean__menu-wrapper d-none d-lg-block">
                                    <div class="main-menu">
                                        <nav id="mobile-menu" style="display: block;">
                                            <ul>
                                                <li class=" active">
                                                    <a href="index.php" title="Home Page - Amitabh Builders & Developers">Home</a>
                                                </li>
                                                <li class="has-dropdown">
                                                    <a href="#">About</a>
                                                    <ul class="submenu">
                                                        <li><a href="about.php" title="About Company - Amitabh Builders & Developers">About Company</a></li>
                                                        <li><a href="about_director.php" title="About Dirictor - Amitabh Builders & Developers">About Director</a></li>
                                                    </ul>
                                                </li>

                                                <li class="has-dropdown">
                                                    <a href="#">Media</a>
                                                    <ul class="submenu">
                                                        <li><a href="gallery.php" title="Galler - Amitabh Builders & Developers">Gallery</a></li>
                                                    </ul>
                                                </li>
                                                <li class="has-dropdown">
                                                    <a href="#">Map</a>
                                                    <ul class="submenu">
                                                        <li><a href="2D.php" title="Phase 1 - Amitabh Builders & Developers | Amitabh Builders & Developers">Phase 1</a></li>
                                                        <!-- <li><a href="Phase2map.php" title="Phase 2 - Amitabh Builders & Developers | Amitabh Builders & Developers">Phase 2</a></li> -->
                                                    </ul>
                                                </li>

                                                <li class="has-dropdown">
                                                    <a href="#">Projects</a>
                                                    <ul class="submenu">
                                                        <li><a href="running_projects.php" title="Running Projects - Amitabh Builders & Developers | Amitabh Builders & Developers">Running Projects</a></li>
                                                        <li><a href="completed_project.php" title="Completed Projects - Amitabh Builders & Developers | Amitabh Builders & Developers">Completed Projects</a></li>
                                                        <li><a href="upcoming_projects.php" title="Upcoming Projects - Amitabh Builders & Developers | Amitabh Builders & Developers">Upcoming Projects</a></li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    <a href="blog.php" title="Blog page - Amitabh Builders & Developers | Amitabh Builders & Developers">Blog</a>
                                                </li>
                                                <li>
                                                    <a href="New_Contact.php" title="Contact Us - Amitabh Builders & Developers | Amitabh Builders & Developers">Contact</a>
                                                </li>

                                                <li class="has-dropdown">
                                                    <a href="#">Login</a>
                                                    <ul class="submenu">
                                                        <li><a href="login.php" title="Login - Hari Homes | Amitabh Builders & Developers">Associate</a></li>
                                                        <li><a href="employee.php" title="Login - Hari Homes | Amitabh Builders & Developers">Employee</a></li>
                                                        <li><a href="Customer_Login.php" title="Customer Login - Amitabh Builders & Developers | Amitabh Builders & Developers">Customer</a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="header-right d-flex justify-content-end">
                            <div class="header-action gap-5">
                                <!-- Login Button -->
                                <a href="#" class="login-btn">Login</a>

                                <!-- Booking Button -->
                                <button class="booking-btn" id="openBookingModal">
                                    Book Now
                                </button>
                            </div>
                            <div class="header__hamburger my-auto">
                                <div class="sidebar__toggle">
                                    <a class="bar-icon" href="javascript:void(0)">
                                        <svg color="white" width="30" height="24" viewBox="0 0 30 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3.3125 4.59375C4.1927 4.59375 4.90625 3.8802 4.90625 3C4.90625 2.1198 4.1927 1.40625 3.3125 1.40625C2.4323 1.40625 1.71875 2.1198 1.71875 3C1.71875 3.8802 2.4323 4.59375 3.3125 4.59375Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M15 4.59375C15.8802 4.59375 16.5938 3.8802 16.5938 3C16.5938 2.1198 15.8802 1.40625 15 1.40625C14.1198 1.40625 13.4062 2.1198 13.4062 3C13.4062 3.8802 14.1198 4.59375 15 4.59375Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M26.6875 4.59375C27.5677 4.59375 28.2812 3.8802 28.2812 3C28.2812 2.1198 27.5677 1.40625 26.6875 1.40625C25.8073 1.40625 25.0938 2.1198 25.0938 3C25.0938 3.8802 25.8073 4.59375 26.6875 4.59375Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M3.3125 13.7812C4.1927 13.7812 4.90625 13.0677 4.90625 12.1875C4.90625 11.3073 4.1927 10.5938 3.3125 10.5938C2.4323 10.5938 1.71875 11.3073 1.71875 12.1875C1.71875 13.0677 2.4323 13.7812 3.3125 13.7812Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M15 13.7812C15.8802 13.7812 16.5938 13.0677 16.5938 12.1875C16.5938 11.3073 15.8802 10.5938 15 10.5938C14.1198 10.5938 13.4062 11.3073 13.4062 12.1875C13.4062 13.0677 14.1198 13.7812 15 13.7812Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M26.6875 13.7812C27.5677 13.7812 28.2812 13.0677 28.2812 12.1875C28.2812 11.3073 27.5677 10.5938 26.6875 10.5938C25.8073 10.5938 25.0938 11.3073 25.0938 12.1875C25.0938 13.0677 25.8073 13.7812 26.6875 13.7812Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M3.3125 22.9688C4.1927 22.9688 4.90625 22.2552 4.90625 21.375C4.90625 20.4948 4.1927 19.7812 3.3125 19.7812C2.4323 19.7812 1.71875 20.4948 1.71875 21.375C1.71875 22.2552 2.4323 22.9688 3.3125 22.9688Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M15 22.9688C15.8802 22.9688 16.5938 22.2552 16.5938 21.375C16.5938 20.4948 15.8802 19.7812 15 19.7812C14.1198 19.7812 13.4062 20.4948 13.4062 21.375C13.4062 22.2552 14.1198 22.9688 15 22.9688Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M26.6875 22.9688C27.5677 22.9688 28.2812 22.2552 28.2812 21.375C28.2812 20.4948 27.5677 19.7812 26.6875 19.7812C25.8073 19.7812 25.0938 20.4948 25.0938 21.375C25.0938 22.2552 25.8073 22.9688 26.6875 22.9688Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Booking Modal -->
    <div class="modal-overlay" id="bookingModal">
        <div class="booking-modal">
            <div class="modal-header">
                <h2 class="modal-title">Book Your Property</h2>
                <button class="close-modal" id="closeBookingModal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="bookingForm">
                    <div class="form-group">
                        <label class="form-label" for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="full_name" class="form-control" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="mobileNo">Mobile Number</label>
                        <input type="tel" id="mobileNo" name="mobile_no" class="form-control" placeholder="Enter your mobile number" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Address</label>
                        <textarea id="address" name="address" class="form-control" placeholder="Enter your complete address" required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="message">Message (Optional)</label>
                        <textarea id="message" name="message" class="form-control" placeholder="Any additional information or queries"></textarea>
                    </div>

                    <button type="submit" class="submit-btn">Submit Booking</button>
                </form>

                <div class="success-message" id="successMessage" style="display: none;">
                    <div class="success-icon">✓</div>
                    <h3>Booking Submitted Successfully!</h3>
                    <p>Thank you for your interest. Our team will contact you shortly.</p>
                </div>

                <div class="error-message" id="errorMessage" style="display: none;">
                    <div class="error-icon">✗</div>
                    <h3 id="errorTitle">Error</h3>
                    <p id="errorText"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        const openModalBtn = document.getElementById('openBookingModal');
        const closeModalBtn = document.getElementById('closeBookingModal');
        const modalOverlay = document.getElementById('bookingModal');
        const bookingForm = document.getElementById('bookingForm');
        const successMessage = document.getElementById('successMessage');

        // Open modal
        openModalBtn.addEventListener('click', () => {
            modalOverlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        });

        // Close modal
        closeModalBtn.addEventListener('click', () => {
            modalOverlay.classList.remove('active');
            document.body.style.overflow = 'auto'; // Restore scrolling
            resetForm();
        });

        // Close modal when clicking outside
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                modalOverlay.classList.remove('active');
                document.body.style.overflow = 'auto';
                resetForm();
            }
        });

        // Form submission - SINGLE HANDLER
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form data
            const formData = new FormData(this);

            // Show loading state
            const submitBtn = this.querySelector('.submit-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Submitting...';
            submitBtn.disabled = true;

            // Hide any previous messages
            document.getElementById('successMessage').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'none';

            // Send AJAX request
            fetch('submit_booking.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);

                    if (data.success) {
                        // Show success message
                        document.getElementById('bookingForm').style.display = 'none';
                        document.getElementById('successMessage').style.display = 'block';

                        // Auto close modal after 3 seconds
                        setTimeout(() => {
                            modalOverlay.classList.remove('active');
                            document.body.style.overflow = 'auto';
                            resetForm();
                        }, 3000);

                    } else {
                        // Show error message
                        let errorHtml = data.message;
                        if (data.errors) {
                            errorHtml += '<ul>';
                            data.errors.forEach(error => {
                                errorHtml += `<li>${error}</li>`;
                            });
                            errorHtml += '</ul>';
                        }

                        document.getElementById('errorText').innerHTML = errorHtml;
                        document.getElementById('errorMessage').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('errorText').textContent = 'An error occurred. Please try again.';
                    document.getElementById('errorMessage').style.display = 'block';
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
        });

        // Function to reset form
        function resetForm() {
            document.getElementById('bookingForm').style.display = 'block';
            document.getElementById('successMessage').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('bookingForm').reset();
        }

        // Bell vibration functionality (if bell exists)
        function vibrateBell() {
            let bell = document.getElementById("bell");
            if (bell) {
                bell.classList.add("vibrating");

                setTimeout(() => {
                    bell.classList.remove("vibrating");
                }, 500); // Stop after 500ms
            }
        }

        // Only run if bell element exists
        if (document.getElementById("bell")) {
            setInterval(vibrateBell, 2000); // Vibrate every 2 seconds
        }
    </script>
</body>

</html>