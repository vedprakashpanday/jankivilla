<?php

?>
<!-- header -->
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row ">

    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center ">
        <a class="navbar-brand brand-logo" href="dashboard.php"><img id="Img" class="mr-2" src="../../image/harihomes1-logo.png"></a>
        <a class="navbar-brand brand-logo-mini" href="dashboard.php"><img id="Image1" src="../../image/harihomes1-logo.png"></a>
    </div>

    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-between franchise_nav_menu">

        <!-- Left Side: Toggler Button -->
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>

        <div class="mx-auto text-center">
            <span class="welcome-text text-white">Welcome, <?= $_SESSION['sponsor_name']; ?></span>
        </div>

        <!-- Right Side: Date & Logout -->
        <ul class="navbar-nav navbar-nav-right d-flex align-items-center">
            <span id="ct7" class="mr-3">18-02-2025 11:40:38 AM</span>

            <li class="nav-item nav-profile dropdown">
                <a class="ti-power-off btn btn-warning" href="accountlogout.php"></a>
            </li>
        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>

    </div>
    <style>
        .welcome-text {
            font-size: 16px;
            font-weight: bold;
        }

        .sidebar-list .nav-item {
            margin-bottom: 15px;
        }

        .nav-item a {
            font-size: 1.1rem;
        }

        .sidebar-list .sidebar-heading {
            color: #ffffff;
            font-size: 1rem;
            /* bigger than default */
            font-weight: 600;
            padding-left: 1rem;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .sidebar-list .nav-link {
            color: #f0f0f0;
            font-size: 0.95rem;
            padding: 0.4rem 1.5rem;
            display: block;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar-list .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 4px;
        }
    </style>
</nav>
<!-- sidepanel -->

<div class="franchise_nav_menu">
    <nav class="sidebar sidebar-offcanvas p-0" id="sidebar">
        <ul class="nav">
            <li class="nav-item    active">
                <a class="nav-link franchiseSidebar franchiseSidebar2" href="dashboard.php" style="background-color:#ff9027 ! important;">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>

            <!-- Account Menu -->
            <li class="nav-item">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#accountMenu" aria-expanded="false" aria-controls="accountMenu">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Account</span>
                    <i class="menu-arrow"></i>
                </a>

                <div class="collapse" id="accountMenu">
                    <ul class="nav flex-column sub-menu sidebar-list">

                        <!-- Invoices & Sales -->
                        <li class="nav-item">
                            <h6 class="sidebar-heading">Invoices & Sales</h6>
                            <a class="nav-link" href="sell_invoice_details.php">Sale Invoice Details</a>
                        </li>

                        <!-- Expenses & Payments -->
                        <li class="nav-item">
                            <h6 class="sidebar-heading">Expenses & Payments</h6>
                            <a class="nav-link" href="director_acc.php" onclick="return false;">Director Account</a>
                            <a class="nav-link" href="directordebitlist.php" onclick="return false;">Director Debit Statements</a>
                            <a class="nav-link" href="daily_expenses.php">Office Expenses</a>
                            <a class="nav-link" href="payment_records.php">Payment Records</a>
                        </li>

                        <!-- Transactions -->
                        <li class="nav-item">
                            <h6 class="sidebar-heading">Transactions</h6>
                            <a class="nav-link" href="credittransaction_recording.php">Credit</a>
                            <a class="nav-link" href="debittransaction_recording.php">Debit</a>
                            <a class="nav-link" href="balance_trading.php">View Ledger</a>
                        </li>

                        <!-- Account Management -->
                        <li class="nav-item">
                            <h6 class="sidebar-heading">Account Management</h6>
                            <a class="nav-link" href="opening_balance.php" onclick="return true;">Opening Balance('Disabled from <br>02-07-2025')</a>
                        </li>

                    </ul>
                </div>
            </li>


            <!-- Sale Management Menu -->
            <li class="nav-item">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#salem" aria-expanded="false" aria-controls="salem">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Sale Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="salem">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="Saleinvoice.php">Sale Invoice</a></li>
                        <li class="nav-item"><a class="nav-link" href="ListofInvoice.php">Sale Invoice List</a></li>
                        <li class="nav-item"><a class="nav-link" href="NewEmi_Payment.php">Re Payment</a></li>
                    </ul>
                </div>
            </li>


        </ul>

    </nav>
</div>

<!-- <script>
    // Original content backup
    let originalContent = null;
    let isBlocked = false;

    // Block access function
    function blockAccess() {
        if (!isBlocked) {
            // Save original content only once
            if (originalContent === null) {
                originalContent = document.body.innerHTML;
            }
            document.body.innerHTML = 'Developer area: This content is restricted and intended for development purposes only.';
            isBlocked = true;
        }
    }

    // Restore access function
    function restoreAccess() {
        if (isBlocked && originalContent !== null) {
            document.body.innerHTML = originalContent;
            isBlocked = false;
        }
    }

    // Enhanced DevTools detection
    function detectDevTools() {
        // Calculate differences with lower thresholds
        const widthDiff = window.outerWidth - window.innerWidth;
        const heightDiff = window.outerHeight - window.innerHeight;
        const widthThreshold = widthDiff > 160; // Reduced threshold
        const heightThreshold = heightDiff > 160;

        // Desktop platform check for emulation
        const isDesktopPlatform = ['Win32', 'MacIntel', 'Linux x86_64'].includes(navigator.platform);
        const isMobileViewport = window.innerWidth <= 1024 || [320, 375, 390, 412, 414, 428, 480, 768, 800, 1024].includes(window.innerWidth);
        const possibleEmulation = isDesktopPlatform && isMobileViewport;

        // UserAgent-Platform mismatch detection
        const hasMobileUA = /Android|iPhone|iPad/i.test(navigator.userAgent);
        const hasEmulationIndicators = hasMobileUA && isDesktopPlatform;

        // Media query checks
        const mqInconsistency = window.matchMedia("(hover: hover)").matches && hasMobileUA;

        // Orientation mismatch
        const orientationMismatch = typeof window.orientation === 'undefined' &&
            (window.innerHeight > window.innerWidth && !window.matchMedia("(orientation: portrait)").matches);

        // Combined detection logic
        const devToolsDetected = widthThreshold || heightThreshold || possibleEmulation ||
            hasEmulationIndicators || mqInconsistency || orientationMismatch;

        if (devToolsDetected) {
            blockAccess();
            return true;
        } else {
            restoreAccess();
            return false;
        }
    }

    // Initialize after DOM is fully loaded
    window.addEventListener('load', function() {
        // Save original content immediately
        originalContent = document.body.innerHTML;

        // Initial detection without delay
        detectDevTools();

        // Start monitoring
        startMonitoring();
    });

    function startMonitoring() {
        // Prevent keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            const key = event.keyCode;
            const ctrl = event.ctrlKey || event.metaKey;
            if (key === 123 || // F12
                (ctrl && event.shiftKey && (key === 73 || key === 74 || key === 77)) || // Ctrl+Shift+I/J/M
                (ctrl && key === 85)) { // Ctrl+U
                event.preventDefault();
                blockAccess();
            }
        });

        // Prevent right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Visibility change detection
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') detectDevTools();
        });

        // Continuous monitoring
        const detectionInterval = setInterval(detectDevTools, 300);
        window.addEventListener('resize', () => detectDevTools());
        window.addEventListener('orientationchange', detectDevTools);

        // Observe DOM changes
        new MutationObserver(detectDevTools).observe(document.body, {
            attributes: true,
            attributeFilter: ['style', 'class']
        });
    }
</script> -->

<style>
    @media print {
        .page-body-wrapper>*:not(.main-panel) {
            display: none !important;
        }
    }
</style>