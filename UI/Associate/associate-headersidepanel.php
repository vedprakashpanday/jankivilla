<!-- header -->
<style>
    .brand-logo {
        background: #fff !important;
    }

    .brand-logo-mini {
        background: #fff !important;
    }
</style>
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row ">

    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center ">
        <a class="navbar-brand brand-logo" href="Default.php"><img id="Img" class="mr-2" src="../../image/harihomes1-logo.png"></a>
        <a class="navbar-brand brand-logo-mini" href="Default.php"><img id="Image1" src="../../image/harihomes1-logo.png"></a>
    </div>

    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-between franchise_nav_menu">

        <!-- Left Side: Toggler Button -->
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>

        <!-- Center: Welcome Message -->
        <div class="mx-auto text-center">
            <span class="welcome-text text-white">Welcome, <?= $_SESSION['sponsor_name']; ?></span>
        </div>

        <!-- Right Side: Date & Logout -->
        <ul class="navbar-nav navbar-nav-right d-flex align-items-center">
            <span id="ct7" class="mr-3">18-02-2025 11:40:38 AM</span>

            <li class="nav-item nav-profile dropdown">
                <a class="ti-power-off btn btn-warning" href="associatelogout.php"></a>
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
    </style>
</nav>
<!-- sidepanel -->

<div class="franchise_nav_menu">
    <nav class="sidebar sidebar-offcanvas p-0" id="sidebar">
        <ul class="nav">
            <li class="nav-item active">
                <a class="nav-link franchiseSidebar franchiseSidebar2" href="Default.php" style="background-color:#ff9027 ! important;">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>

            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#exam" aria-expanded="false" aria-controls="exam">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">My Profile</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="exam">
                    <ul class="nav flex-column sub-menu ">

                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="welcomeletter.php">Welcome Letter </a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="personaldetails.php">View/Edit Personal</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="BankDetail.php">View/Edit Bank Details </a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="kycdetails.php">KYC Details </a></li>
                        <li class="nav-item franchiseSidebar"> <a class="nav-link" href="SponsorBy.php">Sponsored By</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="changepassword.php">Change Password </a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#Geneo" aria-expanded="false" aria-controls="Geneo">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Genealogy</span>
                    <i class="menu-arrow"></i>
                </a>
                <?php
                // --- 1. Get logged-in member ID ---
                $memberid = $_SESSION['sponsor_id'] ?? null;

                if (!$memberid) {
                    die("Error: Member not logged in.");
                }

                // --- 2. Fetch member's designation using PDO ---
                try {
                    $stmt = $pdo->prepare("SELECT designation FROM tbl_regist WHERE mem_sid = ?");
                    $stmt->execute([$memberid]);
                    $member = $stmt->fetch();
                } catch (Exception $e) {
                    die("DB Error: " . $e->getMessage());
                }

                if (!$member) {
                    die("Member not found.");
                }

                $current_designation = trim($member['designation']);

                // --- 3. Map designation → level number ---
                $designation_to_level = [
                    'Sales Executive (S.E.)'                  => 1,
                    'Senior Sales Executive (S.S.E.)'         => 2,
                    'Assistant Marketing Officer (A.M.O.)'    => 3,
                    'Marketing Officer (M.O.)'                => 4,
                    'Assistant Marketing Manager (A.M.M.)'   => 5,
                    'Marketing Manager (M.M.)'               => 6,
                    'Chief Marketing Manager (C.M.M.)'       => 7,
                    'Assistant General Manager (A.G.M.)'     => 8,
                    'Deputy General Manager (D.G.M.)'        => 9,
                    'General Manager (G.M.)'                 => 10,
                    'Marketing Director (M.D.)'              => 11,
                    'Founder Member (F.M.)'                  => 12,
                ];

                $member_level = $designation_to_level[$current_designation] ?? 0;

                if ($member_level === 0) {
                    die("Invalid designation.");
                }

                // --- 4. Define all levels ---
                $levels = [
                    1  => ['file' => 'Level_1.php',   'title' => 'Sales Executive (S.E.)'],
                    2  => ['file' => 'Level_02.php',  'title' => 'Senior Sales Executive (S.S.E.)'],
                    3  => ['file' => 'Lavel_03.php',  'title' => 'Assistant Marketing Officer (A.M.O.)'],
                    4  => ['file' => 'Lavel_04.php',  'title' => 'Marketing Officer (M.O.)'],
                    5  => ['file' => 'Lavel-5.php',   'title' => 'Assistant Marketing Manager (A.M.M.)'],
                    6  => ['file' => 'Lavel_06.php',  'title' => 'Marketing Manager (M.M.)'],
                    7  => ['file' => 'Lavel_07.php',  'title' => 'Chief Marketing Manager (C.M.M.)'],
                    8  => ['file' => 'Lavel_08.php',  'title' => 'Assistant General Manager (A.G.M.)'],
                    9  => ['file' => 'Lavel_09.php',  'title' => 'Deputy General Manager (D.G.M.)'],
                    10 => ['file' => 'Lavel_10.php',  'title' => 'General Manager (G.M.)'],
                    11 => ['file' => 'Level_11.php',  'title' => 'Marketing Director (M.D.)'],
                    12 => ['file' => 'Level_12.php',  'title' => 'Founder Member (F.M.)'],
                ];
                ?>

                <!-- =============================================== -->
                <!-- Sidebar: Show ONLY LOWER levels (not self) -->
                <!-- =============================================== -->
                <div class="collapse" id="Geneo">
                    <ul class="nav flex-column sub-menu">
                        <!-- Always show Direct Member -->
                        <li class="nav-item franchiseSidebar">
                            <a class="nav-link" href="DirectDistrubuter.php">Direct Member</a>
                        </li>

                        <!-- Show levels from 1 to (member_level - 1) -->
                        <?php for ($lvl = 1; $lvl < $member_level; $lvl++): ?>
                            <?php if (isset($levels[$lvl])): ?>
                                <li class="nav-item franchiseSidebar">
                                    <a class="nav-link" href="<?= htmlspecialchars($levels[$lvl]['file']) ?>">
                                        <?= htmlspecialchars($levels[$lvl]['title']) ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </ul>
                </div>
            </li>

            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#memm" aria-expanded="false" aria-controls="memm">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Member Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="memm">
                    <ul class="nav flex-column sub-menu ">
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="DistributerJoining.php">New Member Registration</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="educationaldata.php">Educational Qualification</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="experiencedata.php">Add Experience</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="bankdata.php">Add Bank</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="addnomineedata.php">Add Nominee</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Editprofile.php"> Update Member</a></li>
                        <!-- <li class="nav-item franchiseSidebar"><a class="nav-link" href="FindDistributor.php">Find Member</a></li> -->
                    </ul>
                </div>
            </li>


            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#myico" aria-expanded="false" aria-controls="myico">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">My Income</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="myico">
                    <ul class="nav flex-column sub-menu ">

                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="DirectIncome.php">Self Income</a></li>
                        <!-- <li class="nav-item franchiseSidebar"><a class="nav-link" href="Team-Income.php">Team Income</a></li> -->
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="teampurchaseplotcommission.php">Team Income</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Total-Incomereport.php">Total Income</a></li>

                    </ul>
                </div>
            </li>



            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#incom" aria-expanded="false" aria-controls="incom">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Plot Booking</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="incom">
                    <ul class="nav flex-column sub-menu ">




                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Self-Booking-plot.php">Self Booking</a> </li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="teampurchaseplot.php">Team Booking</a> </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#rpt11" aria-expanded="false" aria-controls="rpt11">
                    <p style="color:yellow"> <i class="icon-grid menu-icon"></i></p>
                    <span class="menu-title" style="color:white">Report</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="rpt11">
                    <ul class="nav flex-column sub-menu ">
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="newbusinessreport.php"> One Time Report </a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="EmiSummary.php">EMI Report </a></li>

                    </ul>
                </div>
            </li>


            <li class="nav-item">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#plot" aria-expanded="false" aria-controls="plot">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Plot Status</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="plot">
                    <ul class="nav flex-column sub-menu ">
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="plotstatus.php">Plot Status</a> </li>
                    </ul>
                </div>
            </li>


            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#rpt15" aria-expanded="false" aria-controls="rpt15">
                    <p style="color:yellow"> <i class="icon-grid menu-icon"></i></p>
                    <span class="menu-title" style="color:white">Closing Report</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="rpt15">
                    <ul class="nav flex-column sub-menu ">
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="monthlyclosereport.php">Closing Report</a></li>
                    </ul>
                </div>
            </li>


            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#rpt16" aria-expanded="false" aria-controls="rpt16">
                    <p style="color:yellow"> <i class="icon-grid menu-icon"></i></p>
                    <span class="menu-title" style="color:white">Reward Report</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="rpt16">
                    <ul class="nav flex-column sub-menu ">
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="rewardreport.php">Reward Report</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="spotcommissionreport.php">Spot Commission Report</a></li>

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