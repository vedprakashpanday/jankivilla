<?php
include_once 'connectdb.php';

// Fetch plot details including payment information from the database
$query = "
    SELECT DISTINCT
        p.ProductName,
        p.Status,
        ca.payamount,
        ca.net_amount
    FROM products p
    LEFT JOIN tbl_customeramount ca
        ON p.ProductName = ca.productname
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$plots = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create an array to map ProductName to Status and payment details
$plotStatus = [];
foreach ($plots as $plot) {
    $plotStatus[$plot['ProductName']] = [
        'status' => $plot['Status'],
        'payamount' => floatval($plot['payamount']) ?: 0,
        'net_amount' => floatval($plot['net_amount']) ?: 0
    ];
}

// Function to get background color based on payment percentage
function getBackgroundColor($plotName, $plotStatus)
{
    if (!isset($plotStatus[$plotName])) {
        return 'LightGreen'; // Default for plots not found
    }

    $status = $plotStatus[$plotName]['status'];
    $payAmount = $plotStatus[$plotName]['payamount'];
    $netAmount = $plotStatus[$plotName]['net_amount'];

    // If plot is not booked, return LightGreen
    if ($status !== 'booked') {
        return 'LightGreen';
    }

    // Calculate payment percentage
    $percentage = ($netAmount > 0) ? ($payAmount / $netAmount * 100) : 0;

    // Determine color based on percentage
    $color = 'gray';
    if ($netAmount > 0) {
        if ($percentage >= 0 && $percentage <= 10) {
            $color = 'gray';
        } elseif ($percentage > 10 && $percentage <= 30) {
            $color = 'red';
        } elseif ($percentage > 30 && $percentage <= 70) {
            $color = 'orange';
        } elseif ($percentage > 70 && $percentage <= 100) {
            $color = 'green';
        }
    }

    return $color;
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>
        Hari Home Developers
    </title>
    <meta name="description">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Place favicon.ico in the root directory -->
    <link rel="shortcut icon" type="image/x-icon" href="icon/harihomes1-fevicon.png">
    <!-- CSS here -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/meanmenu.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/swiper.min.css">
    <link rel="stylesheet" href="css/slick.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/fontawesome-pro.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/spacing.css">
    <link rel="stylesheet" href="css/sweetalert2.min.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        td {
            padding: 5px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            border-radius: unset;
            cursor: pointer;
            width: 105px;
            border: 1px solid #000;
        }

        .vertical-road {
            writing-mode: vertical-lr;
            padding: 12px;
            background-color: black;
            color: yellow;
            text-align: center;
            font-size: 11px;
            width: 95px;
            height: 78;
            position: relative;
            /* Required for absolute positioning */
        }

        .vertical-road::after {
            content: "";
            position: absolute;
            top: 0;
            left: 46%;
            width: 2px;
            height: 100%;
            background: repeating-linear-gradient(to bottom, white 0px, Black 5px, transparent 5px, transparent 10px);
            transform: translateX(-50%);
        }

        .road {
            background-color: black;
            color: yellow;
            padding-top: 21px;
            text-align: center;
            margin: 1px 0;
            font-size: 12px;
            width: 100%;
            position: relative;
        }

        .road::after {
            content: "";
            position: absolute;
            top: 50%;
            /* Centers the line vertically */
            left: 0;
            width: 100%;
            height: 2px;
            /* Line thickness */
            background: repeating-linear-gradient(to right, Black 0px, white 5px, transparent 5px, transparent 10px);
            /* Creates a horizontal dotted effect */
            transform: translateY(-50%);
        }

        .road1 {
            background-color: black;
            color: yellow;
            padding-top: 21px;
            text-align: center;
            margin: 1px 0;
            font-size: 23px;
            width: 100%;
            position: relative;
        }

        .road1::after {
            content: "";
            position: absolute;
            top: 50%;
            /* Centers the line vertically */
            left: 0;
            width: 100%;
            height: 2px;
            /* Line thickness */
            /* background: repeating-linear-gradient(to right, Black 0px, Black 5px, transparent 5px, transparent 10px); */
            /* Creates a horizontal dotted effect */
            /* transform: translateY(-50%); */
        }


        .row {
            --bs-gutter-x: -0.5rem;
        }

        .vertical-road1 {
            writing-mode: vertical-lr;
            padding: 17px;
            background-color: black;
            color: yellow;
            text-align: center;
            font-size: 13px;
            width: 96px;
            height: 106px;
            position: relative;
            /* Creates a vertical dotted effect */


            /* Required for absolute positioning */
        }



        .vertical-road1::after {
            content: "";
            position: absolute;
            top: 0;
            left: 45%;
            /* Centers the line */
            width: 2px;
            /* Line thickness */
            height: 100%;
            background: repeating-linear-gradient(to bottom, white 0px, white 5px, transparent 5px, transparent 10px);
            /* Creates a vertical dotted effect */
            transform: translateX(-50%);
        }

        .vertical-road2-container {
            display: flex;
            align-items: center;


            /* Align text and image vertically */
        }

        .vertical-road2 {
            writing-mode: vertical-lr;
            padding: 27px;
            background-color: black;
            color: yellow;
            text-align: center;
            font-size: 16px;
            width: 59px;
            height: 1100px;
            position: relative;

            /*margin-right: 10px;*/
            /* Space between text and image */
        }



        .vertical-road2::after {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            width: 2px;
            height: 100%;
            background: repeating-linear-gradient(to bottom, white 0px, white 5px, transparent 5px, transparent 10px);
            transform: translateX(-50%);
        }


        .vertical-road2-img {
            height: auto;
            max-width: 150px;
            /* Adjust image width */
            height: 1033px;
        }




        @media (max-width: 768px) {
            .row>* {
                flex-shrink: 0;
                width: auto;
                max-width: none;
                padding-right: calc(var(--bs-gutter-x) * .0);
                padding-left: calc(var(--bs-gutter-x) * .0);
                margin-top: var(--bs-gutter-y);
            }

            td {
                padding: 5px;
                font-size: 14px;
                font-weight: bold;
                text-align: center;
                border-radius: 5px;
                cursor: pointer;
                width: 80px;
                border: 1px solid #000;
            }

            img {
                height: 82%;
                width: 100%;
            }

            .nnn {
                height: 323px;
                width: 256px;
            }

            .road1 {
                width: auto;
            }

            .area {
                padding-right: 487px
            }
        }

        /* Wrapper to ensure all containers scroll together */
        .wrapper {
            display: flex;
            /* Align containers in a row */
            overflow-x: auto;
            /* Enable horizontal scrolling */
            overflow-y: hidden;
            /* Prevent vertical scrolling */
            width: 100vw;
            /* Full viewport width */
            Black-space: nowrap;
            /* Prevent line breaks */
            scrollbar-arrow-color: ActiveCaption;

        }

        /* Style for each container */
        .container {
            min-width: 1200px;
            /* Set a fixed width for each container */
            overflow-x: visible;
            /* Ensure individual scrolling */
            Black-space: nowrap;
            /* Prevent text wrapping */
        }

        /* Shared Scrollbar Style */
        .wrapper::-webkit-scrollbar,
        .container::-webkit-scrollbar {
            height: 10px;
            /* Set height for horizontal scrollbar */
        }

        .wrapper::-webkit-scrollbar-thumb,
        .container::-webkit-scrollbar-thumb {
            background: #888;
            /* Scrollbar color */
            border-radius: 5px;
        }

        .wrapper::-webkit-scrollbar-thumb:hover,
        .container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>



</head>

<body data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">
    <form method="post" action="./Get_quote.php" id="form1">




        <div
            style="background: #fff; padding: 10px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">

            <img src="image/map/mapheader.PNG" class="img-fluid">

            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-1  d-none d-md-block" style="overflow:hidden;  height:1800px;">
                        <div class="vertical-road2-container">
                            <div class="vertical-road2">NH-57 (NEW NH-27)</div>

                            <img src="image/map/vtree.PNG" class="vertical-road2-img img-fuild" style="height: 1100px;">
                        </div>
                        <div class="vertical-road2-container">
                            <div class="vertical-road2">NH-57 (NEW NH-27)</div>

                            <img src="image/map/maprightimage.jpeg" class="vertical-road2-img" style="height: 1100px;">
                        </div>

                    </div>

                    <div class="col-md-11">
                        <div class="row">
                            <div class="col-md-8">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD84" style="color:Black;background-color:<?php echo getBackgroundColor('D-84', $plotStatus); ?>;">D-84</td>
                                            <td id="ContentPlaceHolder1_LabD55" style="color:Black;background-color:<?php echo getBackgroundColor('D-55', $plotStatus); ?>;">D-55</td>
                                            <td id="ContentPlaceHolder1_LabD52" style="color:Black;background-color:<?php echo getBackgroundColor('D-52', $plotStatus); ?>;">D-52</td>
                                            <td id="ContentPlaceHolder1_LabC116" style="color:Black;background-color:<?php echo getBackgroundColor('C-116', $plotStatus); ?>;">C-116</td>
                                            <td id="ContentPlaceHolder1_LabC87" style="color:Black;background-color:<?php echo getBackgroundColor('C-87', $plotStatus); ?>;">C-87</td>
                                            <td id="ContentPlaceHolder1_LabC58" style="color:Black;background-color:<?php echo getBackgroundColor('C-58', $plotStatus); ?>;">C-58</td>
                                            <td id="ContentPlaceHolder1_LabB87" style="color:Black;background-color:<?php echo getBackgroundColor('B-87', $plotStatus); ?>;">B-87</td>
                                            <td id="ContentPlaceHolder1_LabB58" style="color:Black;background-color:<?php echo getBackgroundColor('B-58', $plotStatus); ?>;">B-58</td>
                                            <td id="ContentPlaceHolder1_LabA58" style="color:Black;background-color:<?php echo getBackgroundColor('A-58', $plotStatus); ?>;">A-58</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-1">
                                <div class="vertical-road"></div>
                            </div>
                            <div class="col-md-3">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA29" style="color:Black;background-color:<?php echo getBackgroundColor('A-29', $plotStatus); ?>;font-weight:bold;">A-29</td>
                                            <td id="ContentPlaceHolder1_LabB29" style="color:Black;background-color:<?php echo getBackgroundColor('B-29', $plotStatus); ?>;">B-29</td>
                                            <td id="ContentPlaceHolder1_LabC29" style="color:Black;background-color:<?php echo getBackgroundColor('C-29', $plotStatus); ?>;">C-29</td>
                                            <td id="ContentPlaceHolder1_LabD23" style="color:Black;background-color:<?php echo getBackgroundColor('D-23', $plotStatus); ?>;">D-23</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD83" style="color:Black;background-color:<?php echo getBackgroundColor('D-83', $plotStatus); ?>;">D-83</td>
                                            <td id="ContentPlaceHolder1_LabD54" style="color:Black;background-color:<?php echo getBackgroundColor('D-54', $plotStatus); ?>;">D-54</td>
                                            <td id="ContentPlaceHolder1_LabD51" style="color:Black;background-color:<?php echo getBackgroundColor('D-51', $plotStatus); ?>;">D-51</td>
                                            <td id="ContentPlaceHolder1_LabC115" style="color:Black;background-color:<?php echo getBackgroundColor('C-115', $plotStatus); ?>;">C-115</td>
                                            <td id="ContentPlaceHolder1_LabC86" style="color:Black;background-color:<?php echo getBackgroundColor('C-86', $plotStatus); ?>;">C-86</td>
                                            <td id="ContentPlaceHolder1_LabC57" style="color:Black;background-color:<?php echo getBackgroundColor('C-57', $plotStatus); ?>;">C-57</td>
                                            <td id="ContentPlaceHolder1_LabB86" style="color:Black;background-color:<?php echo getBackgroundColor('B-86', $plotStatus); ?>;">B-86</td>
                                            <td id="ContentPlaceHolder1_LabB57" style="color:Black;background-color:<?php echo getBackgroundColor('B-57', $plotStatus); ?>;">B-57</td>
                                            <td id="ContentPlaceHolder1_LabA57" style="color:Black;background-color:<?php echo getBackgroundColor('A-57', $plotStatus); ?>;">A-57</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD82" style="color:Black;background-color:<?php echo getBackgroundColor('D-82', $plotStatus); ?>;">D-82</td>
                                            <td id="ContentPlaceHolder1_LabD53" style="color:Black;background-color:<?php echo getBackgroundColor('D-53', $plotStatus); ?>;">D-53</td>
                                            <td id="ContentPlaceHolder1_LabD50" style="color:Black;background-color:<?php echo getBackgroundColor('D-50', $plotStatus); ?>;">D-50</td>
                                            <td id="ContentPlaceHolder1_LabC114" style="color:Black;background-color:<?php echo getBackgroundColor('C-114', $plotStatus); ?>;">C-114</td>
                                            <td id="ContentPlaceHolder1_LabC85" style="color:Black;background-color:<?php echo getBackgroundColor('C-85', $plotStatus); ?>;">C-85</td>
                                            <td id="ContentPlaceHolder1_LabC56" style="color:Black;background-color:<?php echo getBackgroundColor('C-56', $plotStatus); ?>;">C-56</td>
                                            <td id="ContentPlaceHolder1_LabB85" style="color:Black;background-color:<?php echo getBackgroundColor('B-85', $plotStatus); ?>;">B-85</td>
                                            <td id="ContentPlaceHolder1_LabB56" style="color:Black;background-color:<?php echo getBackgroundColor('B-56', $plotStatus); ?>;">B-56</td>
                                            <td id="ContentPlaceHolder1_LabA56" style="color:Black;background-color:<?php echo getBackgroundColor('A-56', $plotStatus); ?>;font-weight:bold;">A-56</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-1">
                                <div class="vertical-road1">30 Feet Road</div>
                            </div>
                            <div class="col-md-3">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA28" style="color:Black;background-color:<?php echo getBackgroundColor('A-28', $plotStatus); ?>;font-weight:bold;">A-28</td>
                                            <td id="ContentPlaceHolder1_LabB28" style="color:Black;background-color:<?php echo getBackgroundColor('B-28', $plotStatus); ?>;">B-28</td>
                                            <td id="ContentPlaceHolder1_LabC28" style="color:Black;background-color:<?php echo getBackgroundColor('C-28', $plotStatus); ?>;">C-28</td>
                                            <td id="ContentPlaceHolder1_LabD22" style="color:Black;background-color:<?php echo getBackgroundColor('D-22', $plotStatus); ?>;">D-22</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA27" style="color:Black;background-color:<?php echo getBackgroundColor('A-27', $plotStatus); ?>;font-weight:bold;">A-27</td>
                                            <td id="ContentPlaceHolder1_LabB27" style="color:Black;background-color:<?php echo getBackgroundColor('B-27', $plotStatus); ?>;">B-27</td>
                                            <td id="ContentPlaceHolder1_LabC27" style="color:Black;background-color:<?php echo getBackgroundColor('C-27', $plotStatus); ?>;">C-27</td>
                                            <td id="ContentPlaceHolder1_LabD21" style="color:Black;background-color:<?php echo getBackgroundColor('D-21', $plotStatus); ?>;">D-21</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-7">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD81" style="color:Black;background-color:<?php echo getBackgroundColor('D-81', $plotStatus); ?>;">D-81</td>
                                            <td id="ContentPlaceHolder1_LabD49" style="color:Black;background-color:<?php echo getBackgroundColor('D-49', $plotStatus); ?>;">D-49</td>
                                            <td id="ContentPlaceHolder1_LabC113" style="color:Black;background-color:<?php echo getBackgroundColor('C-113', $plotStatus); ?>;">C-113</td>
                                            <td id="ContentPlaceHolder1_LabC84" style="color:Black;background-color:<?php echo getBackgroundColor('C-84', $plotStatus); ?>;">C-84</td>
                                            <td id="ContentPlaceHolder1_LabC55" style="color:Black;background-color:<?php echo getBackgroundColor('C-55', $plotStatus); ?>;">C-55</td>
                                            <td id="ContentPlaceHolder1_LabB84" style="color:Black;background-color:<?php echo getBackgroundColor('B-84', $plotStatus); ?>;">B-84</td>
                                            <td id="ContentPlaceHolder1_LabB55" style="color:Black;background-color:<?php echo getBackgroundColor('B-55', $plotStatus); ?>;">B-55</td>
                                            <td id="ContentPlaceHolder1_LabA55" style="color:Black;background-color:<?php echo getBackgroundColor('A-55', $plotStatus); ?>;">A-55</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD80" style="color:Black;background-color:<?php echo getBackgroundColor('D-80', $plotStatus); ?>;">D-80</td>
                                            <td id="ContentPlaceHolder1_LabD48" style="color:Black;background-color:<?php echo getBackgroundColor('D-48', $plotStatus); ?>;">D-48</td>
                                            <td id="ContentPlaceHolder1_LabC112" style="color:Black;background-color:<?php echo getBackgroundColor('C-112', $plotStatus); ?>;">C-112</td>
                                            <td id="ContentPlaceHolder1_LabC83" style="color:Black;background-color:<?php echo getBackgroundColor('C-83', $plotStatus); ?>;">C-83</td>
                                            <td id="ContentPlaceHolder1_LabC54" style="color:Black;background-color:<?php echo getBackgroundColor('C-54', $plotStatus); ?>;">C-54</td>
                                            <td id="ContentPlaceHolder1_LabB83" style="color:Black;background-color:<?php echo getBackgroundColor('B-83', $plotStatus); ?>;">B-83</td>
                                            <td id="ContentPlaceHolder1_LabB54" style="color:Black;background-color:<?php echo getBackgroundColor('B-54', $plotStatus); ?>;">B-54</td>
                                            <td id="ContentPlaceHolder1_LabA54" style="color:Black;background-color:<?php echo getBackgroundColor('A-54', $plotStatus); ?>;">A-54</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD79" style="color:Black;background-color:<?php echo getBackgroundColor('D-79', $plotStatus); ?>;">D-79</td>
                                            <td id="ContentPlaceHolder1_LabD47" style="color:Black;background-color:<?php echo getBackgroundColor('D-47', $plotStatus); ?>;">D-47</td>
                                            <td id="ContentPlaceHolder1_LabC111" style="color:Black;background-color:<?php echo getBackgroundColor('C-111', $plotStatus); ?>;">C-111</td>
                                            <td id="ContentPlaceHolder1_LabC82" style="color:Black;background-color:<?php echo getBackgroundColor('C-82', $plotStatus); ?>;">C-82</td>
                                            <td id="ContentPlaceHolder1_LabC53" style="color:Black;background-color:<?php echo getBackgroundColor('C-53', $plotStatus); ?>;">C-53</td>
                                            <td id="ContentPlaceHolder1_LabB82" style="color:Black;background-color:<?php echo getBackgroundColor('B-82', $plotStatus); ?>;">B-82</td>
                                            <td id="ContentPlaceHolder1_LabB53" style="color:Black;background-color:<?php echo getBackgroundColor('B-53', $plotStatus); ?>;">B-53</td>
                                            <td id="ContentPlaceHolder1_LabA53" style="color:Black;background-color:<?php echo getBackgroundColor('A-53', $plotStatus); ?>;font-weight:bold;">A-53</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD78" style="color:Black;background-color:<?php echo getBackgroundColor('D-78', $plotStatus); ?>;">D-78</td>
                                            <td id="ContentPlaceHolder1_LabD46" style="color:Black;background-color:<?php echo getBackgroundColor('D-46', $plotStatus); ?>;">D-46</td>
                                            <td id="ContentPlaceHolder1_LabC110" style="color:Black;background-color:<?php echo getBackgroundColor('C-110', $plotStatus); ?>;font-weight:bold;">C-110</td>
                                            <td id="ContentPlaceHolder1_LabC81" style="color:Black;background-color:<?php echo getBackgroundColor('C-81', $plotStatus); ?>;">C-81</td>
                                            <td id="ContentPlaceHolder1_LabC52" style="color:Black;background-color:<?php echo getBackgroundColor('C-52', $plotStatus); ?>;">C-52</td>
                                            <td id="ContentPlaceHolder1_LabB81" style="color:Black;background-color:<?php echo getBackgroundColor('B-81', $plotStatus); ?>;">B-81</td>
                                            <td id="ContentPlaceHolder1_LabB52" style="color:Black;background-color:<?php echo getBackgroundColor('B-52', $plotStatus); ?>;">B-52</td>
                                            <td id="ContentPlaceHolder1_LabA52" style="color:Black;background-color:<?php echo getBackgroundColor('A-52', $plotStatus); ?>;">A-52</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD77" style="color:Black;background-color:<?php echo getBackgroundColor('D-77', $plotStatus); ?>;">D-77</td>
                                            <td id="ContentPlaceHolder1_LabD45" style="color:Black;background-color:<?php echo getBackgroundColor('D-45', $plotStatus); ?>;">D-45</td>
                                            <td id="ContentPlaceHolder1_LabC109" style="color:Black;background-color:<?php echo getBackgroundColor('C-109', $plotStatus); ?>;">C-109</td>
                                            <td id="ContentPlaceHolder1_LabC80" style="color:Black;background-color:<?php echo getBackgroundColor('C-80', $plotStatus); ?>;">C-80</td>
                                            <td id="ContentPlaceHolder1_LabC51" style="color:Black;background-color:<?php echo getBackgroundColor('C-51', $plotStatus); ?>;">C-51</td>
                                            <td id="ContentPlaceHolder1_LabB80" style="color:Black;background-color:<?php echo getBackgroundColor('B-80', $plotStatus); ?>;">B-80</td>
                                            <td id="ContentPlaceHolder1_LabB51" style="color:Black;background-color:<?php echo getBackgroundColor('B-51', $plotStatus); ?>;">B-51</td>
                                            <td id="ContentPlaceHolder1_LabA51" style="color:Black;background-color:<?php echo getBackgroundColor('A-51', $plotStatus); ?>;">A-51</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD76" style="color:Black;background-color:<?php echo getBackgroundColor('D-76', $plotStatus); ?>;">D-76</td>
                                            <td id="ContentPlaceHolder1_LabD44" style="color:Black;background-color:<?php echo getBackgroundColor('D-44', $plotStatus); ?>;">D-44</td>
                                            <td id="ContentPlaceHolder1_LabC108" style="color:Black;background-color:<?php echo getBackgroundColor('C-108', $plotStatus); ?>;">C-108</td>
                                            <td id="ContentPlaceHolder1_LabC79" style="color:Black;background-color:<?php echo getBackgroundColor('C-79', $plotStatus); ?>;">C-79</td>
                                            <td id="ContentPlaceHolder1_LabC50" style="color:Black;background-color:<?php echo getBackgroundColor('C-50', $plotStatus); ?>;">C-50</td>
                                            <td id="ContentPlaceHolder1_LabB79" style="color:Black;background-color:<?php echo getBackgroundColor('B-79', $plotStatus); ?>;">B-79</td>
                                            <td id="ContentPlaceHolder1_LabB50" style="color:Black;background-color:<?php echo getBackgroundColor('B-50', $plotStatus); ?>;">B-50</td>
                                            <td id="ContentPlaceHolder1_LabA50" style="color:Black;background-color:<?php echo getBackgroundColor('A-50', $plotStatus); ?>;">A-50</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-3 nnn">
                                <img src="image/map/2.png" style="height:269px; width:300px">
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-2">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA26" style="color:Black;background-color:<?php echo getBackgroundColor('A-26', $plotStatus); ?>;font-weight:bold;">A-26</td>
                                            <td id="ContentPlaceHolder1_LabB26" style="color:Black;background-color:<?php echo getBackgroundColor('B-26', $plotStatus); ?>;">B-26</td>
                                            <td id="ContentPlaceHolder1_LabC26" style="color:Black;background-color:<?php echo getBackgroundColor('C-26', $plotStatus); ?>;">C-26</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA25" style="color:Black;background-color:<?php echo getBackgroundColor('A-25', $plotStatus); ?>;font-weight:bold;">A-25</td>
                                            <td id="ContentPlaceHolder1_LabB25" style="color:Black;background-color:<?php echo getBackgroundColor('B-25', $plotStatus); ?>;">B-25</td>
                                            <td id="ContentPlaceHolder1_LabC25" style="color:Black;background-color:<?php echo getBackgroundColor('C-25', $plotStatus); ?>;">C-25</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA24" style="color:Black;background-color:<?php echo getBackgroundColor('A-24', $plotStatus); ?>;font-weight:bold;">A-24</td>
                                            <td id="ContentPlaceHolder1_LabB24" style="color:Black;background-color:<?php echo getBackgroundColor('B-24', $plotStatus); ?>;">B-24</td>
                                            <td id="ContentPlaceHolder1_LabC24" style="color:Black;background-color:<?php echo getBackgroundColor('C-24', $plotStatus); ?>;">C-24</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA23" style="color:Black;background-color:<?php echo getBackgroundColor('A-23', $plotStatus); ?>;font-weight:bold;">A-23</td>
                                            <td id="ContentPlaceHolder1_LabB23" style="color:Black;background-color:<?php echo getBackgroundColor('B-23', $plotStatus); ?>;">B-23</td>
                                            <td id="ContentPlaceHolder1_LabC23" style="color:Black;background-color:<?php echo getBackgroundColor('C-23', $plotStatus); ?>;">C-23</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA22" style="color:Black;background-color:<?php echo getBackgroundColor('A-22', $plotStatus); ?>;font-weight:bold;">A-22</td>
                                            <td id="ContentPlaceHolder1_LabB22" style="color:Black;background-color:<?php echo getBackgroundColor('B-22', $plotStatus); ?>;">B-22</td>
                                            <td id="ContentPlaceHolder1_LabC22" style="color:Black;background-color:<?php echo getBackgroundColor('C-22', $plotStatus); ?>;">C-22</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA21" style="color:Black;background-color:<?php echo getBackgroundColor('A-21', $plotStatus); ?>;font-weight:bold;">A-21</td>
                                            <td id="ContentPlaceHolder1_LabB21" style="color:Black;background-color:<?php echo getBackgroundColor('B-21', $plotStatus); ?>;">B-21</td>
                                            <td id="ContentPlaceHolder1_LabC21" style="color:Black;background-color:<?php echo getBackgroundColor('C-21', $plotStatus); ?>;">C-21</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD104" style="color:Black;background-color:<?php echo getBackgroundColor('D-104', $plotStatus); ?>;">D-104</td>
                                            <td id="ContentPlaceHolder1_LabD75" style="color:Black;background-color:<?php echo getBackgroundColor('D-75', $plotStatus); ?>;">D-75</td>
                                            <td id="ContentPlaceHolder1_LabD43" style="color:Black;background-color:<?php echo getBackgroundColor('D-43', $plotStatus); ?>;">D-43</td>
                                            <td id="ContentPlaceHolder1_LabC107" style="color:Black;background-color:<?php echo getBackgroundColor('C-107', $plotStatus); ?>;">C-107</td>
                                            <td id="ContentPlaceHolder1_LabC78" style="color:Black;background-color:<?php echo getBackgroundColor('C-78', $plotStatus); ?>;">C-78</td>
                                            <td id="ContentPlaceHolder1_LabC49" style="color:Black;background-color:<?php echo getBackgroundColor('C-49', $plotStatus); ?>;">C-49</td>
                                            <td id="ContentPlaceHolder1_LabB78" style="color:Black;background-color:<?php echo getBackgroundColor('B-78', $plotStatus); ?>;">B-78</td>
                                            <td id="ContentPlaceHolder1_LabB49" style="color:Black;background-color:<?php echo getBackgroundColor('B-49', $plotStatus); ?>;">B-49</td>
                                            <td id="ContentPlaceHolder1_LabA49" style="color:Black;background-color:<?php echo getBackgroundColor('A-49', $plotStatus); ?>;">A-49</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD103" style="color:Black;background-color:<?php echo getBackgroundColor('D-103', $plotStatus); ?>;">D-103</td>
                                            <td id="ContentPlaceHolder1_LabD74" style="color:Black;background-color:<?php echo getBackgroundColor('D-74', $plotStatus); ?>;">D-74</td>
                                            <td id="ContentPlaceHolder1_LabD42" style="color:Black;background-color:<?php echo getBackgroundColor('D-42', $plotStatus); ?>;">D-42</td>
                                            <td id="ContentPlaceHolder1_LabC106" style="color:Black;background-color:<?php echo getBackgroundColor('C-106', $plotStatus); ?>;">C-106</td>
                                            <td id="ContentPlaceHolder1_LabC77" style="color:Black;background-color:<?php echo getBackgroundColor('C-77', $plotStatus); ?>;">C-77</td>
                                            <td id="ContentPlaceHolder1_LabC48" style="color:Black;background-color:<?php echo getBackgroundColor('C-48', $plotStatus); ?>;">C-48</td>
                                            <td id="ContentPlaceHolder1_LabB77" style="color:Black;background-color:<?php echo getBackgroundColor('B-77', $plotStatus); ?>;">B-77</td>
                                            <td id="ContentPlaceHolder1_LabB48" style="color:Black;background-color:<?php echo getBackgroundColor('B-48', $plotStatus); ?>;">B-48</td>
                                            <td id="ContentPlaceHolder1_LabA48" style="color:Black;background-color:<?php echo getBackgroundColor('A-48', $plotStatus); ?>;">A-48</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-1">
                                <div class="vertical-road1"></div>
                            </div>
                            <div class="col-md-3">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA20" style="color:Black;background-color:<?php echo getBackgroundColor('A-20', $plotStatus); ?>;font-weight:bold;">A-20</td>
                                            <td id="ContentPlaceHolder1_LabB20" style="color:Black;background-color:<?php echo getBackgroundColor('B-20', $plotStatus); ?>;font-weight:bold;">B-20</td>
                                            <td id="ContentPlaceHolder1_LabC20" style="color:Black;background-color:<?php echo getBackgroundColor('C-20', $plotStatus); ?>;">C-20</td>
                                            <td id="ContentPlaceHolder1_LabD20" style="color:Black;background-color:<?php echo getBackgroundColor('D-20', $plotStatus); ?>;">D-20</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA19" style="color:Black;background-color:<?php echo getBackgroundColor('A-19', $plotStatus); ?>;font-weight:bold;">A-19</td>
                                            <td id="ContentPlaceHolder1_LabB19" style="color:Black;background-color:<?php echo getBackgroundColor('B-19', $plotStatus); ?>;">B-19</td>
                                            <td id="ContentPlaceHolder1_LabC19" style="color:Black;background-color:<?php echo getBackgroundColor('C-19', $plotStatus); ?>;">C-19</td>
                                            <td id="ContentPlaceHolder1_LabD19" style="color:Black;background-color:<?php echo getBackgroundColor('D-19', $plotStatus); ?>;">D-19</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD102" style="color:Black;background-color:<?php echo getBackgroundColor('D-102', $plotStatus); ?>;">D-102</td>
                                            <td id="ContentPlaceHolder1_LabD73" style="color:Black;background-color:<?php echo getBackgroundColor('D-73', $plotStatus); ?>;">D-73</td>
                                            <td id="ContentPlaceHolder1_LabD41" style="color:Black;background-color:<?php echo getBackgroundColor('D-41', $plotStatus); ?>;">D-41</td>
                                            <td id="ContentPlaceHolder1_LabC105" style="color:Black;background-color:<?php echo getBackgroundColor('C-105', $plotStatus); ?>;">C-105</td>
                                            <td id="ContentPlaceHolder1_LabC76" style="color:Black;background-color:<?php echo getBackgroundColor('C-76', $plotStatus); ?>;">C-76</td>
                                            <td id="ContentPlaceHolder1_LabC47" style="color:Black;background-color:<?php echo getBackgroundColor('C-47', $plotStatus); ?>;">C-47</td>
                                            <td id="ContentPlaceHolder1_LabB76" style="color:Black;background-color:<?php echo getBackgroundColor('B-76', $plotStatus); ?>;">B-76</td>
                                            <td id="ContentPlaceHolder1_LabB47" style="color:Black;background-color:<?php echo getBackgroundColor('B-47', $plotStatus); ?>;">B-47</td>
                                            <td id="ContentPlaceHolder1_LabA47" style="color:Black;background-color:<?php echo getBackgroundColor('A-47', $plotStatus); ?>;">A-47</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD101" style="color:Black;background-color:<?php echo getBackgroundColor('D-101', $plotStatus); ?>;">D-101</td>
                                            <td id="ContentPlaceHolder1_LabD72" style="color:Black;background-color:<?php echo getBackgroundColor('D-72', $plotStatus); ?>;">D-72</td>
                                            <td id="ContentPlaceHolder1_LabD40" style="color:Black;background-color:<?php echo getBackgroundColor('D-40', $plotStatus); ?>;">D-40</td>
                                            <td id="ContentPlaceHolder1_LabC104" style="color:Black;background-color:<?php echo getBackgroundColor('C-104', $plotStatus); ?>;">C-104</td>
                                            <td id="ContentPlaceHolder1_LabC75" style="color:Black;background-color:<?php echo getBackgroundColor('C-75', $plotStatus); ?>;">C-75</td>
                                            <td id="ContentPlaceHolder1_LabC46" style="color:Black;background-color:<?php echo getBackgroundColor('C-46', $plotStatus); ?>;">C-46</td>
                                            <td id="ContentPlaceHolder1_LabB75" style="color:Black;background-color:<?php echo getBackgroundColor('B-75', $plotStatus); ?>;">B-75</td>
                                            <td id="ContentPlaceHolder1_LabB46" style="color:Black;background-color:<?php echo getBackgroundColor('B-46', $plotStatus); ?>;">B-46</td>
                                            <td id="ContentPlaceHolder1_LabA46" style="color:Black;background-color:<?php echo getBackgroundColor('A-46', $plotStatus); ?>;">A-46</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-1">
                                <div class="vertical-road1">30 Feet Road</div>
                            </div>
                            <div class="col-md-3">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA18" style="color:Black;background-color:<?php echo getBackgroundColor('A-18', $plotStatus); ?>;font-weight:bold;">A-18</td>
                                            <td id="ContentPlaceHolder1_LabB18" style="color:Black;background-color:<?php echo getBackgroundColor('B-18', $plotStatus); ?>;">B-18</td>
                                            <td id="ContentPlaceHolder1_LabC18" style="color:Black;background-color:<?php echo getBackgroundColor('C-18', $plotStatus); ?>;">C-18</td>
                                            <td id="ContentPlaceHolder1_LabD18" style="color:Black;background-color:<?php echo getBackgroundColor('D-18', $plotStatus); ?>;">D-18</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA17" style="color:Black;background-color:<?php echo getBackgroundColor('A-17', $plotStatus); ?>;font-weight:bold;">A-17</td>
                                            <td id="ContentPlaceHolder1_LabB17" style="color:Black;background-color:<?php echo getBackgroundColor('B-17', $plotStatus); ?>;">B-17</td>
                                            <td id="ContentPlaceHolder1_LabC17" style="color:Black;background-color:<?php echo getBackgroundColor('C-17', $plotStatus); ?>;">C-17</td>
                                            <td id="ContentPlaceHolder1_LabD17" style="color:Black;background-color:<?php echo getBackgroundColor('D-17', $plotStatus); ?>;">D-17</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD100" style="color:Black;background-color:<?php echo getBackgroundColor('D-100', $plotStatus); ?>;">D-100</td>
                                            <td id="ContentPlaceHolder1_LabD71" style="color:Black;background-color:<?php echo getBackgroundColor('D-71', $plotStatus); ?>;">D-71</td>
                                            <td id="ContentPlaceHolder1_LabD39" style="color:Black;background-color:<?php echo getBackgroundColor('D-39', $plotStatus); ?>;">D-39</td>
                                            <td id="ContentPlaceHolder1_LabC103" style="color:Black;background-color:<?php echo getBackgroundColor('C-103', $plotStatus); ?>;">C-103</td>
                                            <td id="ContentPlaceHolder1_LabC74" style="color:Black;background-color:<?php echo getBackgroundColor('C-74', $plotStatus); ?>;">C-74</td>
                                            <td id="ContentPlaceHolder1_LabC45" style="color:Black;background-color:<?php echo getBackgroundColor('C-45', $plotStatus); ?>;">C-45</td>
                                            <td id="ContentPlaceHolder1_LabB74" style="color:Black;background-color:<?php echo getBackgroundColor('B-74', $plotStatus); ?>;">B-74</td>
                                            <td id="ContentPlaceHolder1_LabB45" style="color:Black;background-color:<?php echo getBackgroundColor('B-45', $plotStatus); ?>;">B-45</td>
                                            <td id="ContentPlaceHolder1_LabA45" style="color:Black;background-color:<?php echo getBackgroundColor('A-45', $plotStatus); ?>;">A-45</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD99" style="color:Black;background-color:<?php echo getBackgroundColor('D-99', $plotStatus); ?>;">D-99</td>
                                            <td id="ContentPlaceHolder1_LabD70" style="color:Black;background-color:<?php echo getBackgroundColor('D-70', $plotStatus); ?>;">D-70</td>
                                            <td id="ContentPlaceHolder1_LabD38" style="color:Black;background-color:<?php echo getBackgroundColor('D-38', $plotStatus); ?>;">D-38</td>
                                            <td id="ContentPlaceHolder1_LabC102" style="color:Black;background-color:<?php echo getBackgroundColor('C-102', $plotStatus); ?>;">C-102</td>
                                            <td id="ContentPlaceHolder1_LabC73" style="color:Black;background-color:<?php echo getBackgroundColor('C-73', $plotStatus); ?>;">C-73</td>
                                            <td id="ContentPlaceHolder1_LabC44" style="color:Black;background-color:<?php echo getBackgroundColor('C-44', $plotStatus); ?>;">C-44</td>
                                            <td id="ContentPlaceHolder1_LabB73" style="color:Black;background-color:<?php echo getBackgroundColor('B-73', $plotStatus); ?>;">B-73</td>
                                            <td id="ContentPlaceHolder1_LabB44" style="color:Black;background-color:<?php echo getBackgroundColor('B-44', $plotStatus); ?>;">B-44</td>
                                            <td id="ContentPlaceHolder1_LabA44" style="color:Black;background-color:<?php echo getBackgroundColor('A-44', $plotStatus); ?>;">A-44</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-1">
                                <div class="vertical-road1"></div>
                            </div>
                            <div class="col-md-3">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA16" style="color:Black;background-color:<?php echo getBackgroundColor('A-16', $plotStatus); ?>;font-weight:bold;">A-16</td>
                                            <td id="ContentPlaceHolder1_LabB16" style="color:Black;background-color:<?php echo getBackgroundColor('B-16', $plotStatus); ?>;">B-16</td>
                                            <td id="ContentPlaceHolder1_LabC16" style="color:Black;background-color:<?php echo getBackgroundColor('C-16', $plotStatus); ?>;">C-16</td>
                                            <td id="ContentPlaceHolder1_LabD16" style="color:Black;background-color:<?php echo getBackgroundColor('D-16', $plotStatus); ?>;">D-16</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA15" style="color:Black;background-color:<?php echo getBackgroundColor('A-15', $plotStatus); ?>;font-weight:bold;">A-15</td>
                                            <td id="ContentPlaceHolder1_LabB15" style="color:Black;background-color:<?php echo getBackgroundColor('B-15', $plotStatus); ?>;">B-15</td>
                                            <td id="ContentPlaceHolder1_LabC15" style="color:Black;background-color:<?php echo getBackgroundColor('C-15', $plotStatus); ?>;">C-15</td>
                                            <td id="ContentPlaceHolder1_LabD15" style="color:Black;background-color:<?php echo getBackgroundColor('D-15', $plotStatus); ?>;">D-15</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD98" style="color:Black;background-color:<?php echo getBackgroundColor('D-98', $plotStatus); ?>;">D-98</td>
                                            <td id="ContentPlaceHolder1_LabD69" style="color:Black;background-color:<?php echo getBackgroundColor('D-69', $plotStatus); ?>;">D-69</td>
                                            <td id="ContentPlaceHolder1_LabD37" style="color:Black;background-color:<?php echo getBackgroundColor('D-37', $plotStatus); ?>;">D-37</td>
                                            <td id="ContentPlaceHolder1_LabC101" style="color:Black;background-color:<?php echo getBackgroundColor('C-101', $plotStatus); ?>;font-weight:bold;">C-101</td>
                                            <td id="ContentPlaceHolder1_LabC72" style="color:Black;background-color:<?php echo getBackgroundColor('C-72', $plotStatus); ?>;">C-72</td>
                                            <td id="ContentPlaceHolder1_LabC43" style="color:Black;background-color:<?php echo getBackgroundColor('C-43', $plotStatus); ?>;">C-43</td>
                                            <td id="ContentPlaceHolder1_LabB72" style="color:Black;background-color:<?php echo getBackgroundColor('B-72', $plotStatus); ?>;">B-72</td>
                                            <td id="ContentPlaceHolder1_LabB43" style="color:Black;background-color:<?php echo getBackgroundColor('B-43', $plotStatus); ?>;">B-43</td>
                                            <td id="ContentPlaceHolder1_LabA43" style="color:Black;background-color:<?php echo getBackgroundColor('A-43', $plotStatus); ?>;">A-43</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD97" style="color:Black;background-color:<?php echo getBackgroundColor('D-97', $plotStatus); ?>;">D-97</td>
                                            <td id="ContentPlaceHolder1_LabD68" style="color:Black;background-color:<?php echo getBackgroundColor('D-68', $plotStatus); ?>;">D-68</td>
                                            <td id="ContentPlaceHolder1_LabD36" style="color:Black;background-color:<?php echo getBackgroundColor('D-36', $plotStatus); ?>;">D-36</td>
                                            <td id="ContentPlaceHolder1_LabC100" style="color:Black;background-color:<?php echo getBackgroundColor('C-100', $plotStatus); ?>;font-weight:bold;">C-100</td>
                                            <td id="ContentPlaceHolder1_LabC71" style="color:Black;background-color:<?php echo getBackgroundColor('C-71', $plotStatus); ?>;">C-71</td>
                                            <td id="ContentPlaceHolder1_LabC42" style="color:Black;background-color:<?php echo getBackgroundColor('C-42', $plotStatus); ?>;">C-42</td>
                                            <td id="ContentPlaceHolder1_LabB71" style="color:Black;background-color:<?php echo getBackgroundColor('B-71', $plotStatus); ?>;">B-71</td>
                                            <td id="ContentPlaceHolder1_LabB42" style="color:Black;background-color:<?php echo getBackgroundColor('B-42', $plotStatus); ?>;">B-42</td>
                                            <td id="ContentPlaceHolder1_LabA42" style="color:Black;background-color:<?php echo getBackgroundColor('A-42', $plotStatus); ?>;">A-42</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-1">
                                <div class="vertical-road1"></div>
                            </div>
                            <div class="col-md-3">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA14" style="color:Black;background-color:<?php echo getBackgroundColor('A-14', $plotStatus); ?>;font-weight:bold;">A-14</td>
                                            <td id="ContentPlaceHolder1_LabB14" style="color:Black;background-color:<?php echo getBackgroundColor('B-14', $plotStatus); ?>;">B-14</td>
                                            <td id="ContentPlaceHolder1_LabC14" style="color:Black;background-color:<?php echo getBackgroundColor('C-14', $plotStatus); ?>;">C-14</td>
                                            <td id="ContentPlaceHolder1_LabD14" style="color:Black;background-color:<?php echo getBackgroundColor('D-14', $plotStatus); ?>;">D-14</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA13" style="color:Black;background-color:<?php echo getBackgroundColor('A-13', $plotStatus); ?>;font-weight:bold;">A-13</td>
                                            <td id="ContentPlaceHolder1_LabB13" style="color:Black;background-color:<?php echo getBackgroundColor('B-13', $plotStatus); ?>;">B-13</td>
                                            <td id="ContentPlaceHolder1_LabC13" style="color:Black;background-color:<?php echo getBackgroundColor('C-13', $plotStatus); ?>;">C-13</td>
                                            <td id="ContentPlaceHolder1_LabD13" style="color:Black;background-color:<?php echo getBackgroundColor('D-13', $plotStatus); ?>;">D-13</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD96" style="color:Black;background-color:<?php echo getBackgroundColor('D-96', $plotStatus); ?>;">D-96</td>
                                            <td id="ContentPlaceHolder1_LabD67" style="color:Black;background-color:<?php echo getBackgroundColor('D-67', $plotStatus); ?>;">D-67</td>
                                            <td id="ContentPlaceHolder1_LabD35" style="color:Black;background-color:<?php echo getBackgroundColor('D-35', $plotStatus); ?>;">D-35</td>
                                            <td id="ContentPlaceHolder1_LabC99" style="color:Black;background-color:<?php echo getBackgroundColor('C-99', $plotStatus); ?>;">C-99</td>
                                            <td id="ContentPlaceHolder1_LabC70" style="color:Black;background-color:<?php echo getBackgroundColor('C-70', $plotStatus); ?>;">C-70</td>
                                            <td id="ContentPlaceHolder1_LabC41" style="color:Black;background-color:<?php echo getBackgroundColor('C-41', $plotStatus); ?>;">C-41</td>
                                            <td id="ContentPlaceHolder1_LabB70" style="color:Black;background-color:<?php echo getBackgroundColor('B-70', $plotStatus); ?>;">B-70</td>
                                            <td id="ContentPlaceHolder1_LabB41" style="color:Black;background-color:<?php echo getBackgroundColor('B-41', $plotStatus); ?>;">B-41</td>
                                            <td id="ContentPlaceHolder1_LabA41" style="color:Black;background-color:<?php echo getBackgroundColor('A-41', $plotStatus); ?>;font-weight:bold;">A-41</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD95" style="color:Black;background-color:<?php echo getBackgroundColor('D-95', $plotStatus); ?>;">D-95</td>
                                            <td id="ContentPlaceHolder1_LabD66" style="color:Black;background-color:<?php echo getBackgroundColor('D-66', $plotStatus); ?>;">D-66</td>
                                            <td id="ContentPlaceHolder1_LabD34" style="color:Black;background-color:<?php echo getBackgroundColor('D-34', $plotStatus); ?>;">D-34</td>
                                            <td id="ContentPlaceHolder1_LabC98" style="color:Black;background-color:<?php echo getBackgroundColor('C-98', $plotStatus); ?>;">C-98</td>
                                            <td id="ContentPlaceHolder1_LabC69" style="color:Black;background-color:<?php echo getBackgroundColor('C-69', $plotStatus); ?>;">C-69</td>
                                            <td id="ContentPlaceHolder1_LabC40" style="color:Black;background-color:<?php echo getBackgroundColor('C-40', $plotStatus); ?>;">C-40</td>
                                            <td id="ContentPlaceHolder1_LabB69" style="color:Black;background-color:<?php echo getBackgroundColor('B-69', $plotStatus); ?>;">B-69</td>
                                            <td id="ContentPlaceHolder1_LabB40" style="color:Black;background-color:<?php echo getBackgroundColor('B-40', $plotStatus); ?>;">B-40</td>
                                            <td id="ContentPlaceHolder1_LabA40" style="color:Black;background-color:<?php echo getBackgroundColor('A-40', $plotStatus); ?>;font-weight:bold;">A-40</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-1">
                                <div class="vertical-road1"></div>
                            </div>
                            <div class="col-md-3">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA12" style="color:Black;background-color:<?php echo getBackgroundColor('A-12', $plotStatus); ?>;font-weight:bold;">A-12</td>
                                            <td id="ContentPlaceHolder1_LabB12" style="color:Black;background-color:<?php echo getBackgroundColor('B-12', $plotStatus); ?>;">B-12</td>
                                            <td id="ContentPlaceHolder1_LabC12" style="color:Black;background-color:<?php echo getBackgroundColor('C-12', $plotStatus); ?>;font-weight:bold;">C-12</td>
                                            <td id="ContentPlaceHolder1_LabD12" style="color:Black;background-color:<?php echo getBackgroundColor('D-12', $plotStatus); ?>;">D-12</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA11" style="color:Black;background-color:<?php echo getBackgroundColor('A-11', $plotStatus); ?>;font-weight:bold;">A-11</td>
                                            <td id="ContentPlaceHolder1_LabB11" style="color:Black;background-color:<?php echo getBackgroundColor('B-11', $plotStatus); ?>;">B-11</td>
                                            <td id="ContentPlaceHolder1_LabC11" style="color:Black;background-color:<?php echo getBackgroundColor('C-11', $plotStatus); ?>;">C-11</td>
                                            <td id="ContentPlaceHolder1_LabD11" style="color:Black;background-color:<?php echo getBackgroundColor('D-11', $plotStatus); ?>;">D-11</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD94" style="color:Black;background-color:<?php echo getBackgroundColor('D-94', $plotStatus); ?>;">D-94</td>
                                            <td id="ContentPlaceHolder1_LabD65" style="color:Black;background-color:<?php echo getBackgroundColor('D-65', $plotStatus); ?>;">D-65</td>
                                            <td id="ContentPlaceHolder1_LabD33" style="color:Black;background-color:<?php echo getBackgroundColor('D-33', $plotStatus); ?>;">D-33</td>
                                            <td id="ContentPlaceHolder1_LabC97" style="color:Black;background-color:<?php echo getBackgroundColor('C-97', $plotStatus); ?>;">C-97</td>
                                            <td id="ContentPlaceHolder1_LabC68" style="color:Black;background-color:<?php echo getBackgroundColor('C-68', $plotStatus); ?>;">C-68</td>
                                            <td id="ContentPlaceHolder1_LabC39" style="color:Black;background-color:<?php echo getBackgroundColor('C-39', $plotStatus); ?>;">C-39</td>
                                            <td id="ContentPlaceHolder1_LabB68" style="color:Black;background-color:<?php echo getBackgroundColor('B-68', $plotStatus); ?>;">B-68</td>
                                            <td id="ContentPlaceHolder1_LabB39" style="color:Black;background-color:<?php echo getBackgroundColor('B-39', $plotStatus); ?>;">B-39</td>
                                            <td id="ContentPlaceHolder1_LabA39" style="color:Black;background-color:<?php echo getBackgroundColor('A-39', $plotStatus); ?>;font-weight:bold;">A-39</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD93" style="color:Black;background-color:<?php echo getBackgroundColor('D-93', $plotStatus); ?>;">D-93</td>
                                            <td id="ContentPlaceHolder1_LabD64" style="color:Black;background-color:<?php echo getBackgroundColor('D-64', $plotStatus); ?>;">D-64</td>
                                            <td id="ContentPlaceHolder1_LabD32" style="color:Black;background-color:<?php echo getBackgroundColor('D-32', $plotStatus); ?>;">D-32</td>
                                            <td id="ContentPlaceHolder1_LabC96" style="color:Black;background-color:<?php echo getBackgroundColor('C-96', $plotStatus); ?>;">C-96</td>
                                            <td id="ContentPlaceHolder1_LabC67" style="color:Black;background-color:<?php echo getBackgroundColor('C-67', $plotStatus); ?>;">C-67</td>
                                            <td id="ContentPlaceHolder1_LabC38" style="color:Black;background-color:<?php echo getBackgroundColor('C-38', $plotStatus); ?>;">C-38</td>
                                            <td id="ContentPlaceHolder1_LabB67" style="color:Black;background-color:<?php echo getBackgroundColor('B-67', $plotStatus); ?>;">B-67</td>
                                            <td id="ContentPlaceHolder1_LabB38" style="color:Black;background-color:<?php echo getBackgroundColor('B-38', $plotStatus); ?>;">B-38</td>
                                            <td id="ContentPlaceHolder1_LabA38" style="color:Black;background-color:<?php echo getBackgroundColor('A-38', $plotStatus); ?>;font-weight:bold;">A-38</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-1">
                                <div class="vertical-road1"></div>
                            </div>
                            <div class="col-md-3">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA10" style="color:Black;background-color:<?php echo getBackgroundColor('A-10', $plotStatus); ?>;font-weight:bold;">A-10</td>
                                            <td id="ContentPlaceHolder1_LabB10" style="color:Black;background-color:<?php echo getBackgroundColor('B-10', $plotStatus); ?>;">B-10</td>
                                            <td id="ContentPlaceHolder1_LabC10" style="color:Black;background-color:<?php echo getBackgroundColor('C-10', $plotStatus); ?>;font-weight:bold;">C-10</td>
                                            <td id="ContentPlaceHolder1_LabD10" style="color:Black;background-color:<?php echo getBackgroundColor('D-10', $plotStatus); ?>;">D-10</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA9" style="color:Black;background-color:<?php echo getBackgroundColor('A-9', $plotStatus); ?>;">A-9</td>
                                            <td id="ContentPlaceHolder1_LabB9" style="color:Black;background-color:<?php echo getBackgroundColor('B-9', $plotStatus); ?>;">B-9</td>
                                            <td id="ContentPlaceHolder1_LabC9" style="color:Black;background-color:<?php echo getBackgroundColor('C-9', $plotStatus); ?>;">C-9</td>
                                            <td id="ContentPlaceHolder1_LabD9" style="color:Black;background-color:<?php echo getBackgroundColor('D-9', $plotStatus); ?>;">D-9</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD92" style="color:Black;background-color:<?php echo getBackgroundColor('D-92', $plotStatus); ?>;">D-92</td>
                                            <td id="ContentPlaceHolder1_LabD63" style="color:Black;background-color:<?php echo getBackgroundColor('D-63', $plotStatus); ?>;">D-63</td>
                                            <td id="ContentPlaceHolder1_LabD31" style="color:Black;background-color:<?php echo getBackgroundColor('D-31', $plotStatus); ?>;">D-31</td>
                                            <td id="ContentPlaceHolder1_LabC95" style="color:Black;background-color:<?php echo getBackgroundColor('C-95', $plotStatus); ?>;">C-95</td>
                                            <td id="ContentPlaceHolder1_LabC66" style="color:Black;background-color:<?php echo getBackgroundColor('C-66', $plotStatus); ?>;">C-66</td>
                                            <td id="ContentPlaceHolder1_LabC37" style="color:Black;background-color:<?php echo getBackgroundColor('C-37', $plotStatus); ?>;">C-37</td>
                                            <td id="ContentPlaceHolder1_LabB66" style="color:Black;background-color:<?php echo getBackgroundColor('B-66', $plotStatus); ?>;">B-66</td>
                                            <td id="ContentPlaceHolder1_LabB37" style="color:Black;background-color:<?php echo getBackgroundColor('B-37', $plotStatus); ?>;">B-37</td>
                                            <td id="ContentPlaceHolder1_LabA37" style="color:Black;background-color:<?php echo getBackgroundColor('A-37', $plotStatus); ?>;font-weight:bold;">A-37</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD91" style="color:Black;background-color:<?php echo getBackgroundColor('D-91', $plotStatus); ?>;">D-91</td>
                                            <td id="ContentPlaceHolder1_LabD62" style="color:Black;background-color:<?php echo getBackgroundColor('D-62', $plotStatus); ?>;">D-62</td>
                                            <td id="ContentPlaceHolder1_LabD30" style="color:Black;background-color:<?php echo getBackgroundColor('D-30', $plotStatus); ?>;">D-30</td>
                                            <td id="ContentPlaceHolder1_LabC94" style="color:Black;background-color:<?php echo getBackgroundColor('C-94', $plotStatus); ?>;">C-94</td>
                                            <td id="ContentPlaceHolder1_LabC65" style="color:Black;background-color:<?php echo getBackgroundColor('C-65', $plotStatus); ?>;">C-65</td>
                                            <td id="ContentPlaceHolder1_LabC36" style="color:Black;background-color:<?php echo getBackgroundColor('C-36', $plotStatus); ?>;">C-36</td>
                                            <td id="ContentPlaceHolder1_LabB65" style="color:Black;background-color:<?php echo getBackgroundColor('B-65', $plotStatus); ?>;">B-65</td>
                                            <td id="ContentPlaceHolder1_LabB36" style="color:Black;background-color:<?php echo getBackgroundColor('B-36', $plotStatus); ?>;">B-36</td>
                                            <td id="ContentPlaceHolder1_LabA36" style="color:Black;background-color:<?php echo getBackgroundColor('A-36', $plotStatus); ?>;font-weight:bold;">A-36</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-1">
                                <div class="vertical-road1">30 Feet Road</div>
                            </div>
                            <div class="col-md-3">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA8" style="color:Black;background-color:<?php echo getBackgroundColor('A-8', $plotStatus); ?>;">A-8</td>
                                            <td id="ContentPlaceHolder1_LabB8" style="color:Black;background-color:<?php echo getBackgroundColor('B-8', $plotStatus); ?>;">B-8</td>
                                            <td id="ContentPlaceHolder1_LabC8" style="color:Black;background-color:<?php echo getBackgroundColor('C-8', $plotStatus); ?>;">C-8</td>
                                            <td id="ContentPlaceHolder1_LabD8" style="color:Black;background-color:<?php echo getBackgroundColor('D-8', $plotStatus); ?>;">D-8</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA7" style="color:Black;background-color:<?php echo getBackgroundColor('A-7', $plotStatus); ?>;">A-7</td>
                                            <td id="ContentPlaceHolder1_LabB7" style="color:Black;background-color:<?php echo getBackgroundColor('B-7', $plotStatus); ?>;">B-7</td>
                                            <td id="ContentPlaceHolder1_LabC7" style="color:Black;background-color:<?php echo getBackgroundColor('C-7', $plotStatus); ?>;">C-7</td>
                                            <td id="ContentPlaceHolder1_LabD7" style="color:Black;background-color:<?php echo getBackgroundColor('D-7', $plotStatus); ?>;">D-7</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD90" style="color:Black;background-color:<?php echo getBackgroundColor('D-90', $plotStatus); ?>;">D-90</td>
                                            <td id="ContentPlaceHolder1_LabD61" style="color:Black;background-color:<?php echo getBackgroundColor('D-61', $plotStatus); ?>;">D-61</td>
                                            <td id="ContentPlaceHolder1_LabD29" style="color:Black;background-color:<?php echo getBackgroundColor('D-29', $plotStatus); ?>;">D-29</td>
                                            <td id="ContentPlaceHolder1_LabC93" style="color:Black;background-color:<?php echo getBackgroundColor('C-93', $plotStatus); ?>;">C-93</td>
                                            <td id="ContentPlaceHolder1_LabC64" style="color:Black;background-color:<?php echo getBackgroundColor('C-64', $plotStatus); ?>;">C-64</td>
                                            <td id="ContentPlaceHolder1_LabC35" style="color:Black;background-color:<?php echo getBackgroundColor('C-35', $plotStatus); ?>;">C-35</td>
                                            <td id="ContentPlaceHolder1_LabB64" style="color:Black;background-color:<?php echo getBackgroundColor('B-64', $plotStatus); ?>;">B-64</td>
                                            <td id="ContentPlaceHolder1_LabB35" style="color:Black;background-color:<?php echo getBackgroundColor('B-35', $plotStatus); ?>;">B-35</td>
                                            <td id="ContentPlaceHolder1_LabA35" style="color:Black;background-color:<?php echo getBackgroundColor('A-35', $plotStatus); ?>;font-weight:bold;">A-35</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD89" style="color:Black;background-color:<?php echo getBackgroundColor('D-89', $plotStatus); ?>;">D-89</td>
                                            <td id="ContentPlaceHolder1_LabD60" style="color:Black;background-color:<?php echo getBackgroundColor('D-60', $plotStatus); ?>;">D-60</td>
                                            <td id="ContentPlaceHolder1_LabD28" style="color:Black;background-color:<?php echo getBackgroundColor('D-28', $plotStatus); ?>;">D-28</td>
                                            <td id="ContentPlaceHolder1_LabC92" style="color:Black;background-color:<?php echo getBackgroundColor('C-92', $plotStatus); ?>;">C-92</td>
                                            <td id="ContentPlaceHolder1_LabC63" style="color:Black;background-color:<?php echo getBackgroundColor('C-63', $plotStatus); ?>;">C-63</td>
                                            <td id="ContentPlaceHolder1_LabC34" style="color:Black;background-color:<?php echo getBackgroundColor('C-34', $plotStatus); ?>;">C-34</td>
                                            <td id="ContentPlaceHolder1_LabB63" style="color:Black;background-color:<?php echo getBackgroundColor('B-63', $plotStatus); ?>;">B-63</td>
                                            <td id="ContentPlaceHolder1_LabB34" style="color:Black;background-color:<?php echo getBackgroundColor('B-34', $plotStatus); ?>;font-weight:bold;">B-34</td>
                                            <td id="ContentPlaceHolder1_LabA34" style="color:Black;background-color:<?php echo getBackgroundColor('A-34', $plotStatus); ?>;font-weight:bold;">A-34</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-1">
                                <div class="vertical-road1"></div>
                            </div>
                            <div class="col-md-3">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA6" style="color:Black;background-color:<?php echo getBackgroundColor('A-6', $plotStatus); ?>;">A-6</td>
                                            <td id="ContentPlaceHolder1_LabB6" style="color:Black;background-color:<?php echo getBackgroundColor('B-6', $plotStatus); ?>;">B-6</td>
                                            <td id="ContentPlaceHolder1_LabC6" style="color:Black;background-color:<?php echo getBackgroundColor('C-6', $plotStatus); ?>;">C-6</td>
                                            <td id="ContentPlaceHolder1_LabD6" style="color:Black;background-color:<?php echo getBackgroundColor('D-6', $plotStatus); ?>;">D-6</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA5" style="color:Black;background-color:<?php echo getBackgroundColor('A-5', $plotStatus); ?>;">A-5</td>
                                            <td id="ContentPlaceHolder1_LabB5" style="color:Black;background-color:<?php echo getBackgroundColor('B-5', $plotStatus); ?>;">B-5</td>
                                            <td id="ContentPlaceHolder1_LabC5" style="color:Black;background-color:<?php echo getBackgroundColor('C-5', $plotStatus); ?>;">C-5</td>
                                            <td id="ContentPlaceHolder1_LabD5" style="color:Black;background-color:<?php echo getBackgroundColor('D-5', $plotStatus); ?>;font-weight:bold;">D-5</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD88" style="color:Black;background-color:<?php echo getBackgroundColor('D-88', $plotStatus); ?>;">D-88</td>
                                            <td id="ContentPlaceHolder1_LabD59" style="color:Black;background-color:<?php echo getBackgroundColor('D-59', $plotStatus); ?>;">D-59</td>
                                            <td id="ContentPlaceHolder1_LabD27" style="color:Black;background-color:<?php echo getBackgroundColor('D-27', $plotStatus); ?>;">D-27</td>
                                            <td id="ContentPlaceHolder1_LabC91" style="color:Black;background-color:<?php echo getBackgroundColor('C-91', $plotStatus); ?>;">C-91</td>
                                            <td id="ContentPlaceHolder1_LabC62" style="color:Black;background-color:<?php echo getBackgroundColor('C-62', $plotStatus); ?>;">C-62</td>
                                            <td id="ContentPlaceHolder1_LabC33" style="color:Black;background-color:<?php echo getBackgroundColor('C-33', $plotStatus); ?>;">C-33</td>
                                            <td id="ContentPlaceHolder1_LabB62" style="color:Black;background-color:<?php echo getBackgroundColor('B-62', $plotStatus); ?>;">B-62</td>
                                            <td id="ContentPlaceHolder1_LabB33" style="color:Black;background-color:<?php echo getBackgroundColor('B-33', $plotStatus); ?>;">B-33</td>
                                            <td id="ContentPlaceHolder1_LabA33" style="color:Black;background-color:<?php echo getBackgroundColor('A-33', $plotStatus); ?>;font-weight:bold;">A-33</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD87" style="color:Black;background-color:<?php echo getBackgroundColor('D-87', $plotStatus); ?>;">D-87</td>
                                            <td id="ContentPlaceHolder1_LabD58" style="color:Black;background-color:<?php echo getBackgroundColor('D-58', $plotStatus); ?>;">D-58</td>
                                            <td id="ContentPlaceHolder1_LabD26" style="color:Black;background-color:<?php echo getBackgroundColor('D-26', $plotStatus); ?>;">D-26</td>
                                            <td id="ContentPlaceHolder1_LabC90" style="color:Black;background-color:<?php echo getBackgroundColor('C-90', $plotStatus); ?>;">C-90</td>
                                            <td id="ContentPlaceHolder1_LabC61" style="color:Black;background-color:<?php echo getBackgroundColor('C-61', $plotStatus); ?>;">C-61</td>
                                            <td id="ContentPlaceHolder1_LabC32" style="color:Black;background-color:<?php echo getBackgroundColor('C-32', $plotStatus); ?>;">C-32</td>
                                            <td id="ContentPlaceHolder1_LabB61" style="color:Black;background-color:<?php echo getBackgroundColor('B-61', $plotStatus); ?>;">B-61</td>
                                            <td id="ContentPlaceHolder1_LabB32" style="color:Black;background-color:<?php echo getBackgroundColor('B-32', $plotStatus); ?>;">B-32</td>
                                            <td id="ContentPlaceHolder1_LabA32" style="color:Black;background-color:<?php echo getBackgroundColor('A-32', $plotStatus); ?>;font-weight:bold;">A-32</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-1">
                                <div class="vertical-road1"></div>
                            </div>
                            <div class="col-md-3">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA4" style="color:Black;background-color:<?php echo getBackgroundColor('A-4', $plotStatus); ?>;font-weight:bold;">A-4</td>
                                            <td id="ContentPlaceHolder1_LabB4" style="color:Black;background-color:<?php echo getBackgroundColor('B-4', $plotStatus); ?>;">B-4</td>
                                            <td id="ContentPlaceHolder1_LabC4" style="color:Black;background-color:<?php echo getBackgroundColor('C-4', $plotStatus); ?>;">C-4</td>
                                            <td id="ContentPlaceHolder1_LabD4" style="color:Black;background-color:<?php echo getBackgroundColor('D-4', $plotStatus); ?>;">D-4</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA3" style="color:Black;background-color:<?php echo getBackgroundColor('A-3', $plotStatus); ?>;font-weight:bold;">A-3</td>
                                            <td id="ContentPlaceHolder1_LabB3" style="color:Black;background-color:<?php echo getBackgroundColor('B-3', $plotStatus); ?>;">B-3</td>
                                            <td id="ContentPlaceHolder1_LabC3" style="color:Black;background-color:<?php echo getBackgroundColor('C-3', $plotStatus); ?>;font-weight:bold;">C-3</td>
                                            <td id="ContentPlaceHolder1_LabD3" style="color:Black;background-color:<?php echo getBackgroundColor('D-3', $plotStatus); ?>;">D-3</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD86" style="color:Black;background-color:<?php echo getBackgroundColor('D-86', $plotStatus); ?>;">D-86</td>
                                            <td id="ContentPlaceHolder1_LabD57" style="color:Black;background-color:<?php echo getBackgroundColor('D-57', $plotStatus); ?>;">D-57</td>
                                            <td id="ContentPlaceHolder1_LabD25" style="color:Black;background-color:<?php echo getBackgroundColor('D-25', $plotStatus); ?>;">D-25</td>
                                            <td id="ContentPlaceHolder1_LabC89" style="color:Black;background-color:<?php echo getBackgroundColor('C-89', $plotStatus); ?>;font-weight:bold;">C-89</td>
                                            <td id="ContentPlaceHolder1_LabC60" style="color:Black;background-color:<?php echo getBackgroundColor('C-60', $plotStatus); ?>;font-weight:bold;">C-60</td>
                                            <td id="ContentPlaceHolder1_LabC31" style="color:Black;background-color:<?php echo getBackgroundColor('C-31', $plotStatus); ?>;font-weight:bold;">C-31</td>
                                            <td id="ContentPlaceHolder1_LabB60" style="color:Black;background-color:<?php echo getBackgroundColor('B-60', $plotStatus); ?>;">B-60</td>
                                            <td id="ContentPlaceHolder1_LabB31" style="color:Black;background-color:<?php echo getBackgroundColor('B-31', $plotStatus); ?>;">B-31</td>
                                            <td id="ContentPlaceHolder1_LabA31" style="color:Black;background-color:<?php echo getBackgroundColor('A-31', $plotStatus); ?>;font-weight:bold;">A-31</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabD85" style="color:Black;background-color:<?php echo getBackgroundColor('D-85', $plotStatus); ?>;">D-85</td>
                                            <td id="ContentPlaceHolder1_LabD56" style="color:Black;background-color:<?php echo getBackgroundColor('D-56', $plotStatus); ?>;">D-56</td>
                                            <td id="ContentPlaceHolder1_LabD24" style="color:Black;background-color:<?php echo getBackgroundColor('D-24', $plotStatus); ?>;">D-24</td>
                                            <td id="ContentPlaceHolder1_LabC88" style="color:Black;background-color:<?php echo getBackgroundColor('C-88', $plotStatus); ?>;font-weight:bold;">C-88</td>
                                            <td id="ContentPlaceHolder1_LabC59" style="color:Black;background-color:<?php echo getBackgroundColor('C-59', $plotStatus); ?>;font-weight:bold;">C-59</td>
                                            <td id="ContentPlaceHolder1_LabC30" style="color:Black;background-color:<?php echo getBackgroundColor('C-30', $plotStatus); ?>;font-weight:bold;">C-30</td>
                                            <td id="ContentPlaceHolder1_LabB59" style="color:Black;background-color:<?php echo getBackgroundColor('B-59', $plotStatus); ?>;font-weight:bold;">B-59</td>
                                            <td id="ContentPlaceHolder1_LabB30" style="color:Black;background-color:<?php echo getBackgroundColor('B-30', $plotStatus); ?>;font-weight:bold;">B-30</td>
                                            <td id="ContentPlaceHolder1_LabA30" style="color:Black;background-color:<?php echo getBackgroundColor('A-30', $plotStatus); ?>;font-weight:bold;">A-30</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                            <div class="col-md-1">
                                <div class="vertical-road1"></div>
                            </div>
                            <div class="col-md-3">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA2" style="color:Black;background-color:<?php echo getBackgroundColor('A-2', $plotStatus); ?>;font-weight:bold;">A-2</td>
                                            <td id="ContentPlaceHolder1_LabB2" style="color:Black;background-color:<?php echo getBackgroundColor('B-2', $plotStatus); ?>;font-weight:bold;">B-2</td>
                                            <td id="ContentPlaceHolder1_LabC2" style="color:Black;background-color:<?php echo getBackgroundColor('C-2', $plotStatus); ?>;">C-2</td>
                                            <td id="ContentPlaceHolder1_LabD2" style="color:Black;background-color:<?php echo getBackgroundColor('D-2', $plotStatus); ?>;">D-2</td>
                                        </tr>
                                        <tr>
                                            <td id="ContentPlaceHolder1_LabA1" style="color:Black;background-color:<?php echo getBackgroundColor('A-1', $plotStatus); ?>;font-weight:bold;">A-1</td>
                                            <td id="ContentPlaceHolder1_LabB1" style="color:Black;background-color:<?php echo getBackgroundColor('B-1', $plotStatus); ?>;">B-1</td>
                                            <td id="ContentPlaceHolder1_LabC1" style="color:Black;background-color:<?php echo getBackgroundColor('C-1', $plotStatus); ?>;font-weight:bold;">C-1</td>
                                            <td id="ContentPlaceHolder1_LabD1" style="color:Black;background-color:<?php echo getBackgroundColor('D-1', $plotStatus); ?>;">D-1</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="road">20 Feet Road</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <p class="area"
                                    style="height:150px;text-align:center;font-size:30px;font-weight:700;padding-top:90px">
                                    Commercial Area</p>



                            </div>
                            <div class="col-md-1">
                                <div class="vertical-road1" style=" height:201px">30 Feet Road</div>

                            </div>
                            <div class="col-md-3">
                                <p
                                    style="height:150px;text-align:center;font-size:30px;font-weight:700; padding-top:90px">
                                    Commercial Area</p>




                            </div>
                        </div>
                        <div class="road1">Darbhanga Sitamarhi Road</div>

                    </div>
                    <img src="image/map/mapffff.PNG">

                </div>
            </div>



            <script src="js/jquery-3.6.0.min.js"></script>
            <script src="js/waypoints.min.js"></script>
            <script src="js/bootstrap.bundle.min.js"></script>
            <script src="js/meanmenu.min.js"></script>
            <script src="js/swiper.min.js"></script>
            <script src="js/slick.min.js"></script>
            <script src="js/magnific-popup.min.js"></script>
            <script src="js/counterup.js"></script>
            <script src="js/wow.js"></script>
            <script src="js/TweenMax.min.js"></script>
            <script src="js/sweetalert2.all.min.js"></script>
            <script src="js/email.min.js"></script>
            <script>
                emailjs.init('user_4JNFd46byV1DFNJopV4hK')
            </script>
            <script src="js/email-validation.js"></script>
            <script src="js/main.js"></script>
        </div>
    </form>



</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>