<style>
    .brand-logo {
        background: #fff !important;
    }

    .brand-logo-mini {
        background: #fff !important;
    }
</style>

<!-- Header -->
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row ">

    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center ">
        <a class="navbar-brand brand-logo" href="dashboard.php"><img id="Img" src="../../image/harihomes1-logo.png" class="mr-2" /></a>
        <a class="navbar-brand brand-logo-mini" href="dashboard.php"><img id="Img" src="../../image/harihomes1-logo.png" class="mr-2" /></a>
    </div>

    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end franchise_nav_menu">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>

        <ul class="navbar-nav navbar-nav-right">
            <span id="ct7">22-03-2024 10:38:36 AM</span>



            <li class="nav-item nav-profile dropdown">
                <a class="ti-power-off btn btn-warning" href="adminlogout.php" onclick="return confirm('Are you sure you want to logout?')">

                </a>

            </li>


        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>
    </div>
</nav>

<!-- SidePanel-->

<div class="franchise_nav_menu">
    <nav class="sidebar sidebar-offcanvas p-0" id="sidebar">
        <ul class="nav">
            <li class="nav-item active">
                <a class="nav-link franchiseSidebar franchiseSidebar2" href="dashboard.php" style="background-color:#ff9027 ! important;">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#salem" aria-expanded="false" aria-controls="salem">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Sale Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="salem">
                    <ul class="nav flex-column sub-menu ">

                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Saleinvoice.php"><span class="title"> Sale Invoice</span></a></li>
                        <!-- <li class="nav-item franchiseSidebar"><a class="nav-link" href="NewInvoice.php"><span class="title">New Sale Invoice</span></a></li> -->
                        <!-- <li class="nav-item franchiseSidebar"><a class="nav-link" href="DeleteInvoice.php"><span class="title">Delete Sale Invoice</span></a></li> -->

                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="IncomeDetails.php"><span class="title">Income Details</span></a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#comsl" aria-expanded="false" aria-controls="comsl">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Comission Slab</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="comsl">
                    <ul class="nav flex-column sub-menu ">
                        <!-- <li class="nav-item franchiseSidebar"><a class="nav-link" href="AdminCommissionSlab.php">Set Admin Comission</a></li> -->
                        <!-- <li class="nav-item franchiseSidebar"><a class="nav-link" href="ComissionSlab.php">Set Member Comission</a></li> -->
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="CommissionMember.php">Set All Member Comission</a></li>


                    </ul>
                </div>
            </li>

            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#genelo" aria-expanded="false" aria-controls="genelo">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Genealogy</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="genelo">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Label_1admin.php" title="Sales Executive">Sales Executive (S.E.)</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Label_02admin.php" title="Senior Sales Executive">Senior Sales Executive (S.S.E.)</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Label_03admin.php" title="Assistant Marketing Officer">Assistant Marketing Officer (A.M.O.)</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Label_04admin.php" title="Marketing Officer">Marketing Officer (M.O.)</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Label_05admin.php" title="Assistant Marketing Manager">Assistant Marketing Manager (A.M.M.)</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Label_06admin.php" title="Marketing Manager">Marketing Manager (M.M.)</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Label_07admin.php" title="Chief Marketing Manager">Chief Marketing Manager (C.M.M.)</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Label_08admin.php" title="Assistant General Manager">Assistant General Manager (A.G.M.)</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Label_09admin.php" title="Deputy General Manager">Deputy General Manager (D.G.M.)</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Label_10admin.php" title="General Manager">General Manager (G.M.)</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Label_11admin.php" title="Marketing Director">Marketing Director (M.D.)</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Label_12admin.php" title="Founder Member">Founder Member (F.M.)</a></li>
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
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="membernominee.php">Update Nominee</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Editprofile.php"> Update Member</a></li>
                        <!-- <li class="nav-item franchiseSidebar"><a class="nav-link" href="FindDistributor.php">Find Member</a></li> -->
                    </ul>
                </div>
            </li>

            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#cusman" aria-expanded="false" aria-controls="cusman">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Customer Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="cusman">
                    <ul class="nav flex-column sub-menu ">

                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="CustomerREgistration.php">Customer Registration</a></li>


                    </ul>
                </div>
            </li>

            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#closing" aria-expanded="false" aria-controls="closing">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Closing</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="closing">
                    <ul class="nav flex-column sub-menu ">
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="Dailyclosing.php">Calculate Closing</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="monthlyclosing.php">Closing Report</a></li>
                    </ul>
                </div>
            </li>
            <!-- <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#pmana" aria-expanded="false" aria-controls="pmana">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Product Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="pmana">
                    <ul class="nav flex-column sub-menu ">
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="AddNewProduct.php">Add New Product</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="setlaunchingoffer.php">Launching Offer</a></li>
                    </ul>
                </div>
            </li> -->
            <!-- <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#down" aria-expanded="false" aria-controls="down">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Down-Line</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="down">
                    <ul class="nav flex-column sub-menu ">

                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="DirectDistrubuter.php">Direct Member</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="LeftDistributer.php">Left Member</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="RightDistributer.php">Right Member</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="MemberAmountcomm.php">Member Comission</a></li>
                    </ul>
                </div>
            </li> -->
            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#payment" aria-expanded="false" aria-controls="payment">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Payment</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="payment">
                    <ul class="nav flex-column sub-menu ">
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="NewEmi_Payment.php">Re Payment</a></li>
                        <!-- <li class="nav-item franchiseSidebar "><a class="nav-link" href="Member_Commission.php">Member Commission</a> </li> -->
                    </ul>
                </div>
            </li>
            <!-- <li class="nav-item ">
                <a class="nav-link franchiseSidebar" href="FundRequest.php">
                    <i class="icon-grid menu-icon"></i>

                    <span class="menu-title" style="color:white">Fund Request</span>
                    <i class="menu-arrow"></i>
                </a>

            </li> -->

            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#report" aria-expanded="false" aria-controls="report">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Report</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="report">
                    <ul class="nav flex-column sub-menu ">


                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="MemberDetails.php">List of Member </a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="memberkyc.php">Member KYC</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="RptCustomerDetails.php">List of Customer </a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="ListofInvoice.php">Sale Invoice </a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="emireport.php">New EMI Report</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="emicalculate.php">Old EMI Report</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="membersalereport.php">Associate Sale Report</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="percentplot.php">25% Rpt</a></li>
                        <!-- <li class="nav-item franchiseSidebar"><a class="nav-link" href="rptdifferenceincome.php">Difference Income</a></li> -->
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="rptbookedplot.php">Booked Plot </a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="rptblankplot.php">Available Plot </a></li>
                        <!-- <li class="nav-item franchiseSidebar"><a class="nav-link" href="CustomerDueSummary.php">Customer Dues </a></li> -->
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="OneTimeRegisteryDues.php">One Time Registry Dues </a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="EmiRegisteryDues.php">EMI Dues </a></li>
                        <!-- <li class="nav-item franchiseSidebar"><a class="nav-link" href="RptBooking.php">Booking Form Rpt </a></li> -->
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="RptContact.php">Contact Form Rpt </a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#mypro" aria-expanded="false" aria-controls="mypro">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">My Profile</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="mypro">
                    <ul class="nav flex-column sub-menu ">

                        <!-- <li class="nav-item franchiseSidebar"><a class="nav-link" href="welcomeletter.php">Welcome Letter</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="personaldetails.php">Personal Profile</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="BankDetail.php">Bank Details</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="kycdetails.php">Update KYC </a></li> -->
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="changepassword.php">Change Password</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#web" aria-expanded="false" aria-controls="web">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Website</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="web">
                    <ul class="nav flex-column sub-menu ">
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="updatenotice.php">Update Notice</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="updategallery.php">Update Gallery</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="addproduct.php">Add Plot</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="landowner.php">Land Owner</a></li>
                    </ul>
                </div>

            </li>


            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#reward" aria-expanded="false" aria-controls="web">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Rewards</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="reward">
                    <ul class="nav flex-column sub-menu ">
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="rewards.php">Rewards</a></li>
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="spotcommission.php">Spot Commission</a></li>
                    </ul>
                </div>
            </li>


            <li class="nav-item ">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#cash" aria-expanded="false" aria-controls="web">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Cash Received</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="cash">
                    <ul class="nav flex-column sub-menu ">
                        <li class="nav-item franchiseSidebar"><a class="nav-link" href="cashreceived.php">Cash Received Records</a></li>

                    </ul>
                </div>
            </li>

            <!-- <li class="nav-item">
                <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#employee" aria-expanded="false" aria-controls="employee">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title" style="color:white">Employee</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="employee">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item franchiseSidebar">
                            <a class="nav-link" href="add_employee.php">
                                <i class="nav-item franchiseSidebar"></i> Add Employee
                            </a>
                        </li>
                    </ul>
                </div>
            </li> -->

        </ul>
    </nav>
</div>

<script>
    function confirmLogout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'logout.php';
        }
    }
</script>

<script>
    let protectionEnabled = true;
    let hasUnsavedChanges = false;

    function beforeUnloadHandler(e) {
        if (protectionEnabled) {
            e.preventDefault();
            e.returnValue = '';
            return '';
        }
    }

    // Enable protection by default
    window.addEventListener('beforeunload', beforeUnloadHandler);

    function enableProtection() {
        protectionEnabled = true;
        document.getElementById('status').className = 'status enabled';
        document.getElementById('status').innerHTML = '✅ Close Protection: ENABLED';
    }

    function disableProtection() {
        protectionEnabled = false;
        document.getElementById('status').className = 'status disabled';
        document.getElementById('status').innerHTML = '❌ Close Protection: DISABLED';
    }

    function simulateUnsavedChanges() {
        hasUnsavedChanges = true;
        alert('Simulated unsaved changes! Now the close protection will be more relevant.');
    }

    function simulateSave() {
        hasUnsavedChanges = false;
        alert('Data saved! Close protection remains active for demo purposes.');
    }

    // Advanced example: only protect when there are unsaved changes
    function advancedBeforeUnloadHandler(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = '';
            return '';
        }
    }
</script>