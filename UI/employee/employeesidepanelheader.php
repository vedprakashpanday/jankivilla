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
                <a class="ti-power-off btn btn-warning" href="employeelogout.php"></a>
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
                <a class="nav-link franchiseSidebar franchiseSidebar2" href="index.php" style="background-color:#ff9027 ! important;">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>

             <?php if($_SESSION['designation']=='Telecaller'|| $_SESSION['designation']=='Senior Relationship Manager(SRM)'): ?>
              <li class="nav-item">
                <a class="nav-link franchiseSidebar franchiseSidebar2" href="interested_customer.php" style="color:#fff">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title">Interested Customer</span>
                </a>
            </li>
           
<?php endif; ?>

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
                        <!-- <li class="nav-item franchiseSidebar"><a class="nav-link" href="BankDetail.php">View/Edit Bank Details </a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="kycdetails.php">KYC Details </a></li>
                        <li class="nav-item franchiseSidebar"> <a class="nav-link" href="SponsorBy.php">Sponsored By</a></li> -->
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="changepassword.php">Change Password </a></li>
                    </ul>
                </div>
            </li>

       

            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#memm" aria-expanded="false" aria-controls="memm">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Salary</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="memm">
                    <ul class="nav flex-column sub-menu ">
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="view_advance.php">Your Advance</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="view_salary.php">Your Salary</a></li>
                        
                        
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
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="rewards.php">Cash Prize</a></li>
                        
                    </ul>
                </div>
            </li>


        </ul>

    </nav>
</div>


<style>
    @media print {
        .page-body-wrapper>*:not(.main-panel) {
            display: none !important;
        }
    }
</style>