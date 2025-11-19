<?php include_once 'connectdb.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $sqfeet = $_POST['sqfeet'];
    $product_type = $_POST['product_type'];
    $address = $_POST['address'];

    // Prepare the SQL insert query
    $sql = "INSERT INTO bookings (name, mobile, sqfeet, product_type, address) VALUES (:name, :mobile, :sqfeet, :product_type, :address)";
    $stmt = $pdo->prepare($sql);

    // Bind the values
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':mobile', $mobile);
    $stmt->bindParam(':sqfeet', $sqfeet);
    $stmt->bindParam(':product_type', $product_type);
    $stmt->bindParam(':address', $address);

    // Execute the query
    if ($stmt->execute()) {
        echo "Booking submitted successfully!";
    } else {
        echo "Error: Unable to submit booking.";
    }
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
    <meta name="content-language" content="EN" />

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
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
    </style>
    <style type="text/css">
        html.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown),
        body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) {
            overflow-y: hidden;
        }

        body.swal2-toast-shown.swal2-has-input>.swal2-container>.swal2-toast {
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
        }

        body.swal2-toast-shown.swal2-has-input>.swal2-container>.swal2-toast .swal2-icon {
            margin: 0 0 15px;
        }

        body.swal2-toast-shown.swal2-has-input>.swal2-container>.swal2-toast .swal2-buttonswrapper {
            -webkit-box-flex: 1;
            -ms-flex: 1;
            flex: 1;
            -ms-flex-item-align: stretch;
            align-self: stretch;
            -webkit-box-pack: end;
            -ms-flex-pack: end;
            justify-content: flex-end;
        }

        body.swal2-toast-shown.swal2-has-input>.swal2-container>.swal2-toast .swal2-loading {
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
        }

        body.swal2-toast-shown.swal2-has-input>.swal2-container>.swal2-toast .swal2-input {
            height: 32px;
            font-size: 14px;
            margin: 5px auto;
        }

        body.swal2-toast-shown>.swal2-container {
            position: fixed;
            background-color: transparent;
        }

        body.swal2-toast-shown>.swal2-container.swal2-shown {
            background-color: transparent;
        }

        body.swal2-toast-shown>.swal2-container.swal2-top {
            top: 0;
            left: 50%;
            bottom: auto;
            right: auto;
            -webkit-transform: translateX(-50%);
            transform: translateX(-50%);
        }

        body.swal2-toast-shown>.swal2-container.swal2-top-end,
        body.swal2-toast-shown>.swal2-container.swal2-top-right {
            top: 0;
            left: auto;
            bottom: auto;
            right: 0;
        }

        body.swal2-toast-shown>.swal2-container.swal2-top-start,
        body.swal2-toast-shown>.swal2-container.swal2-top-left {
            top: 0;
            left: 0;
            bottom: auto;
            right: auto;
        }

        body.swal2-toast-shown>.swal2-container.swal2-center-start,
        body.swal2-toast-shown>.swal2-container.swal2-center-left {
            top: 50%;
            left: 0;
            bottom: auto;
            right: auto;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
        }

        body.swal2-toast-shown>.swal2-container.swal2-center {
            top: 50%;
            left: 50%;
            bottom: auto;
            right: auto;
            -webkit-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
        }

        body.swal2-toast-shown>.swal2-container.swal2-center-end,
        body.swal2-toast-shown>.swal2-container.swal2-center-right {
            top: 50%;
            left: auto;
            bottom: auto;
            right: 0;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
        }

        body.swal2-toast-shown>.swal2-container.swal2-bottom-start,
        body.swal2-toast-shown>.swal2-container.swal2-bottom-left {
            top: auto;
            left: 0;
            bottom: 0;
            right: auto;
        }

        body.swal2-toast-shown>.swal2-container.swal2-bottom {
            top: auto;
            left: 50%;
            bottom: 0;
            right: auto;
            -webkit-transform: translateX(-50%);
            transform: translateX(-50%);
        }

        body.swal2-toast-shown>.swal2-container.swal2-bottom-end,
        body.swal2-toast-shown>.swal2-container.swal2-bottom-right {
            top: auto;
            left: auto;
            bottom: 0;
            right: 0;
        }

        body.swal2-iosfix {
            position: fixed;
            left: 0;
            right: 0;
        }

        body.swal2-no-backdrop>.swal2-shown {
            top: auto;
            bottom: auto;
            left: auto;
            right: auto;
            background-color: transparent;
        }

        body.swal2-no-backdrop>.swal2-shown>.swal2-modal {
            -webkit-box-shadow: 0 0 10px rgba(0, 0, 0, 0.4);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.4);
        }

        body.swal2-no-backdrop>.swal2-shown.swal2-top {
            top: 0;
            left: 50%;
            -webkit-transform: translateX(-50%);
            transform: translateX(-50%);
        }

        body.swal2-no-backdrop>.swal2-shown.swal2-top-start,
        body.swal2-no-backdrop>.swal2-shown.swal2-top-left {
            top: 0;
            left: 0;
        }

        body.swal2-no-backdrop>.swal2-shown.swal2-top-end,
        body.swal2-no-backdrop>.swal2-shown.swal2-top-right {
            top: 0;
            right: 0;
        }

        body.swal2-no-backdrop>.swal2-shown.swal2-center {
            top: 50%;
            left: 50%;
            -webkit-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
        }

        body.swal2-no-backdrop>.swal2-shown.swal2-center-start,
        body.swal2-no-backdrop>.swal2-shown.swal2-center-left {
            top: 50%;
            left: 0;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
        }

        body.swal2-no-backdrop>.swal2-shown.swal2-center-end,
        body.swal2-no-backdrop>.swal2-shown.swal2-center-right {
            top: 50%;
            right: 0;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
        }

        body.swal2-no-backdrop>.swal2-shown.swal2-bottom {
            bottom: 0;
            left: 50%;
            -webkit-transform: translateX(-50%);
            transform: translateX(-50%);
        }

        body.swal2-no-backdrop>.swal2-shown.swal2-bottom-start,
        body.swal2-no-backdrop>.swal2-shown.swal2-bottom-left {
            bottom: 0;
            left: 0;
        }

        body.swal2-no-backdrop>.swal2-shown.swal2-bottom-end,
        body.swal2-no-backdrop>.swal2-shown.swal2-bottom-right {
            bottom: 0;
            right: 0;
        }

        .swal2-container {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -ms-flex-direction: row;
            flex-direction: row;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            position: fixed;
            padding: 10px;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: transparent;
            z-index: 1060;
        }

        .swal2-container.swal2-top {
            -webkit-box-align: start;
            -ms-flex-align: start;
            align-items: flex-start;
        }

        .swal2-container.swal2-top-start,
        .swal2-container.swal2-top-left {
            -webkit-box-align: start;
            -ms-flex-align: start;
            align-items: flex-start;
            -webkit-box-pack: start;
            -ms-flex-pack: start;
            justify-content: flex-start;
        }

        .swal2-container.swal2-top-end,
        .swal2-container.swal2-top-right {
            -webkit-box-align: start;
            -ms-flex-align: start;
            align-items: flex-start;
            -webkit-box-pack: end;
            -ms-flex-pack: end;
            justify-content: flex-end;
        }

        .swal2-container.swal2-center {
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
        }

        .swal2-container.swal2-center-start,
        .swal2-container.swal2-center-left {
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: start;
            -ms-flex-pack: start;
            justify-content: flex-start;
        }

        .swal2-container.swal2-center-end,
        .swal2-container.swal2-center-right {
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: end;
            -ms-flex-pack: end;
            justify-content: flex-end;
        }

        .swal2-container.swal2-bottom {
            -webkit-box-align: end;
            -ms-flex-align: end;
            align-items: flex-end;
        }

        .swal2-container.swal2-bottom-start,
        .swal2-container.swal2-bottom-left {
            -webkit-box-align: end;
            -ms-flex-align: end;
            align-items: flex-end;
            -webkit-box-pack: start;
            -ms-flex-pack: start;
            justify-content: flex-start;
        }

        .swal2-container.swal2-bottom-end,
        .swal2-container.swal2-bottom-right {
            -webkit-box-align: end;
            -ms-flex-align: end;
            align-items: flex-end;
            -webkit-box-pack: end;
            -ms-flex-pack: end;
            justify-content: flex-end;
        }

        .swal2-container.swal2-grow-fullscreen>.swal2-modal {
            display: -webkit-box !important;
            display: -ms-flexbox !important;
            display: flex !important;
            -webkit-box-flex: 1;
            -ms-flex: 1;
            flex: 1;
            -ms-flex-item-align: stretch;
            align-self: stretch;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
        }

        .swal2-container.swal2-grow-row>.swal2-modal {
            display: -webkit-box !important;
            display: -ms-flexbox !important;
            display: flex !important;
            -webkit-box-flex: 1;
            -ms-flex: 1;
            flex: 1;
            -ms-flex-line-pack: center;
            align-content: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
        }

        .swal2-container.swal2-grow-column {
            -webkit-box-flex: 1;
            -ms-flex: 1;
            flex: 1;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
        }

        .swal2-container.swal2-grow-column.swal2-top,
        .swal2-container.swal2-grow-column.swal2-center,
        .swal2-container.swal2-grow-column.swal2-bottom {
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
        }

        .swal2-container.swal2-grow-column.swal2-top-start,
        .swal2-container.swal2-grow-column.swal2-center-start,
        .swal2-container.swal2-grow-column.swal2-bottom-start,
        .swal2-container.swal2-grow-column.swal2-top-left,
        .swal2-container.swal2-grow-column.swal2-center-left,
        .swal2-container.swal2-grow-column.swal2-bottom-left {
            -webkit-box-align: start;
            -ms-flex-align: start;
            align-items: flex-start;
        }

        .swal2-container.swal2-grow-column.swal2-top-end,
        .swal2-container.swal2-grow-column.swal2-center-end,
        .swal2-container.swal2-grow-column.swal2-bottom-end,
        .swal2-container.swal2-grow-column.swal2-top-right,
        .swal2-container.swal2-grow-column.swal2-center-right,
        .swal2-container.swal2-grow-column.swal2-bottom-right {
            -webkit-box-align: end;
            -ms-flex-align: end;
            align-items: flex-end;
        }

        .swal2-container.swal2-grow-column>.swal2-modal {
            display: -webkit-box !important;
            display: -ms-flexbox !important;
            display: flex !important;
            -webkit-box-flex: 1;
            -ms-flex: 1;
            flex: 1;
            -ms-flex-line-pack: center;
            align-content: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
        }

        .swal2-container:not(.swal2-top):not(.swal2-top-start):not(.swal2-top-end):not(.swal2-top-left):not(.swal2-top-right):not(.swal2-center-start):not(.swal2-center-end):not(.swal2-center-left):not(.swal2-center-right):not(.swal2-bottom):not(.swal2-bottom-start):not(.swal2-bottom-end):not(.swal2-bottom-left):not(.swal2-bottom-right)>.swal2-modal {
            margin: auto;
        }

        @media all and (-ms-high-contrast: none),
        (-ms-high-contrast: active) {
            .swal2-container .swal2-modal {
                margin: 0 !important;
            }
        }

        .swal2-container.swal2-fade {
            -webkit-transition: background-color .1s;
            transition: background-color .1s;
        }

        .swal2-container.swal2-shown {
            background-color: rgba(0, 0, 0, 0.4);
        }

        .swal2-popup {
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            background-color: #fff;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            border-radius: 5px;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            text-align: center;
            overflow-x: hidden;
            overflow-y: auto;
            display: none;
            position: relative;
            max-width: 100%;
        }

        .swal2-popup.swal2-toast {
            width: 300px;
            padding: 0 15px;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -ms-flex-direction: row;
            flex-direction: row;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            overflow-y: hidden;
            -webkit-box-shadow: 0 0 10px #d9d9d9;
            box-shadow: 0 0 10px #d9d9d9;
        }

        .swal2-popup.swal2-toast .swal2-title {
            max-width: 300px;
            font-size: 16px;
            text-align: left;
        }

        .swal2-popup.swal2-toast .swal2-content {
            font-size: 14px;
            text-align: left;
        }

        .swal2-popup.swal2-toast .swal2-icon {
            width: 32px;
            min-width: 32px;
            height: 32px;
            margin: 0 15px 0 0;
        }

        .swal2-popup.swal2-toast .swal2-icon.swal2-success .swal2-success-ring {
            width: 32px;
            height: 32px;
        }

        .swal2-popup.swal2-toast .swal2-icon.swal2-info,
        .swal2-popup.swal2-toast .swal2-icon.swal2-warning,
        .swal2-popup.swal2-toast .swal2-icon.swal2-question {
            font-size: 26px;
            line-height: 32px;
        }

        .swal2-popup.swal2-toast .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            top: 14px;
            width: 22px;
        }

        .swal2-popup.swal2-toast .swal2-icon.swal2-error [class^='swal2-x-mark-line'][class$='left'] {
            left: 5px;
        }

        .swal2-popup.swal2-toast .swal2-icon.swal2-error [class^='swal2-x-mark-line'][class$='right'] {
            right: 5px;
        }

        .swal2-popup.swal2-toast .swal2-buttonswrapper {
            margin: 0 0 0 5px;
        }

        .swal2-popup.swal2-toast .swal2-styled {
            margin: 0 0 0 5px;
            padding: 5px 10px;
        }

        .swal2-popup.swal2-toast .swal2-styled:focus {
            -webkit-box-shadow: 0 0 0 1px #fff, 0 0 0 2px rgba(50, 100, 150, 0.4);
            box-shadow: 0 0 0 1px #fff, 0 0 0 2px rgba(50, 100, 150, 0.4);
        }

        .swal2-popup.swal2-toast .swal2-validationerror {
            width: 100%;
            margin: 5px -20px;
        }

        .swal2-popup.swal2-toast .swal2-success {
            border-color: #a5dc86;
        }

        .swal2-popup.swal2-toast .swal2-success [class^='swal2-success-circular-line'] {
            border-radius: 50%;
            position: absolute;
            width: 32px;
            height: 64px;
            -webkit-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .swal2-popup.swal2-toast .swal2-success [class^='swal2-success-circular-line'][class$='left'] {
            border-radius: 64px 0 0 64px;
            top: -4px;
            left: -15px;
            -webkit-transform: rotate(-45deg);
            transform: rotate(-45deg);
            -webkit-transform-origin: 32px 32px;
            transform-origin: 32px 32px;
        }

        .swal2-popup.swal2-toast .swal2-success [class^='swal2-success-circular-line'][class$='right'] {
            border-radius: 0 64px 64px 0;
            top: -5px;
            left: 14px;
            -webkit-transform-origin: 0 32px;
            transform-origin: 0 32px;
        }

        .swal2-popup.swal2-toast .swal2-success .swal2-success-ring {
            width: 32px;
            height: 32px;
        }

        .swal2-popup.swal2-toast .swal2-success .swal2-success-fix {
            width: 7px;
            height: 90px;
            left: 28px;
            top: 8px;
        }

        .swal2-popup.swal2-toast .swal2-success [class^='swal2-success-line'] {
            height: 5px;
        }

        .swal2-popup.swal2-toast .swal2-success [class^='swal2-success-line'][class$='tip'] {
            width: 12px;
            left: 3px;
            top: 18px;
        }

        .swal2-popup.swal2-toast .swal2-success [class^='swal2-success-line'][class$='long'] {
            width: 22px;
            right: 3px;
            top: 15px;
        }

        .swal2-popup.swal2-toast .swal2-animate-success-line-tip {
            -webkit-animation: animate-toast-success-tip .75s;
            animation: animate-toast-success-tip .75s;
        }

        .swal2-popup.swal2-toast .swal2-animate-success-line-long {
            -webkit-animation: animate-toast-success-long .75s;
            animation: animate-toast-success-long .75s;
        }

        .swal2-popup:focus {
            outline: none;
        }

        .swal2-popup.swal2-loading {
            overflow-y: hidden;
        }

        .swal2-popup .swal2-title {
            color: #595959;
            font-size: 30px;
            text-align: center;
            font-weight: 600;
            text-transform: none;
            position: relative;
            margin: 0 0 .4em;
            padding: 0;
            display: block;
            word-wrap: break-word;
        }

        .swal2-popup .swal2-buttonswrapper {
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            margin-top: 15px;
        }

        .swal2-popup .swal2-buttonswrapper:not(.swal2-loading) .swal2-styled[disabled] {
            opacity: .4;
            cursor: no-drop;
        }

        .swal2-popup .swal2-buttonswrapper.swal2-loading .swal2-styled.swal2-confirm {
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            border: 4px solid transparent;
            border-color: transparent;
            width: 40px;
            height: 40px;
            padding: 0;
            margin: 7.5px;
            vertical-align: top;
            background-color: transparent !important;
            color: transparent;
            cursor: default;
            border-radius: 100%;
            -webkit-animation: rotate-loading 1.5s linear 0s infinite normal;
            animation: rotate-loading 1.5s linear 0s infinite normal;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .swal2-popup .swal2-buttonswrapper.swal2-loading .swal2-styled.swal2-cancel {
            margin-left: 30px;
            margin-right: 30px;
        }

        .swal2-popup .swal2-buttonswrapper.swal2-loading :not(.swal2-styled).swal2-confirm::after {
            display: inline-block;
            content: '';
            margin-left: 5px;
            vertical-align: -1px;
            height: 15px;
            width: 15px;
            border: 3px solid #999999;
            -webkit-box-shadow: 1px 1px 1px #fff;
            box-shadow: 1px 1px 1px #fff;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: rotate-loading 1.5s linear 0s infinite normal;
            animation: rotate-loading 1.5s linear 0s infinite normal;
        }

        .swal2-popup .swal2-styled {
            border: 0;
            border-radius: 3px;
            -webkit-box-shadow: none;
            box-shadow: none;
            color: #fff;
            cursor: pointer;
            font-size: 17px;
            font-weight: 500;
            margin: 15px 5px 0;
            padding: 10px 32px;
        }

        .swal2-popup .swal2-styled:focus {
            outline: none;
            -webkit-box-shadow: 0 0 0 2px #fff, 0 0 0 4px rgba(50, 100, 150, 0.4);
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px rgba(50, 100, 150, 0.4);
        }

        .swal2-popup .swal2-image {
            margin: 20px auto;
            max-width: 100%;
        }

        .swal2-popup .swal2-close {
            background: transparent;
            border: 0;
            margin: 0;
            padding: 0;
            width: 38px;
            height: 40px;
            font-size: 36px;
            line-height: 40px;
            font-family: serif;
            position: absolute;
            top: 5px;
            right: 8px;
            cursor: pointer;
            color: #cccccc;
            -webkit-transition: color .1s ease;
            transition: color .1s ease;
        }

        .swal2-popup .swal2-close:hover {
            color: #d55;
        }

        .swal2-popup>.swal2-input,
        .swal2-popup>.swal2-file,
        .swal2-popup>.swal2-textarea,
        .swal2-popup>.swal2-select,
        .swal2-popup>.swal2-radio,
        .swal2-popup>.swal2-checkbox {
            display: none;
        }

        .swal2-popup .swal2-content {
            font-size: 18px;
            text-align: center;
            font-weight: 300;
            position: relative;
            float: none;
            margin: 0;
            padding: 0;
            line-height: normal;
            color: #545454;
            word-wrap: break-word;
        }

        .swal2-popup .swal2-input,
        .swal2-popup .swal2-file,
        .swal2-popup .swal2-textarea,
        .swal2-popup .swal2-select,
        .swal2-popup .swal2-radio,
        .swal2-popup .swal2-checkbox {
            margin: 20px auto;
        }

        .swal2-popup .swal2-input,
        .swal2-popup .swal2-file,
        .swal2-popup .swal2-textarea {
            width: 100%;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            font-size: 18px;
            border-radius: 3px;
            border: 1px solid #d9d9d9;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.06);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.06);
            -webkit-transition: border-color .3s, -webkit-box-shadow .3s;
            transition: border-color .3s, -webkit-box-shadow .3s;
            transition: border-color .3s, box-shadow .3s;
            transition: border-color .3s, box-shadow .3s, -webkit-box-shadow .3s;
        }

        .swal2-popup .swal2-input.swal2-inputerror,
        .swal2-popup .swal2-file.swal2-inputerror,
        .swal2-popup .swal2-textarea.swal2-inputerror {
            border-color: #f27474 !important;
            -webkit-box-shadow: 0 0 2px #f27474 !important;
            box-shadow: 0 0 2px #f27474 !important;
        }

        .swal2-popup .swal2-input:focus,
        .swal2-popup .swal2-file:focus,
        .swal2-popup .swal2-textarea:focus {
            outline: none;
            border: 1px solid #b4dbed;
            -webkit-box-shadow: 0 0 3px #c4e6f5;
            box-shadow: 0 0 3px #c4e6f5;
        }

        .swal2-popup .swal2-input::-webkit-input-placeholder,
        .swal2-popup .swal2-file::-webkit-input-placeholder,
        .swal2-popup .swal2-textarea::-webkit-input-placeholder {
            color: #cccccc;
        }

        .swal2-popup .swal2-input:-ms-input-placeholder,
        .swal2-popup .swal2-file:-ms-input-placeholder,
        .swal2-popup .swal2-textarea:-ms-input-placeholder {
            color: #cccccc;
        }

        .swal2-popup .swal2-input::-ms-input-placeholder,
        .swal2-popup .swal2-file::-ms-input-placeholder,
        .swal2-popup .swal2-textarea::-ms-input-placeholder {
            color: #cccccc;
        }

        .swal2-popup .swal2-input::placeholder,
        .swal2-popup .swal2-file::placeholder,
        .swal2-popup .swal2-textarea::placeholder {
            color: #cccccc;
        }

        .swal2-popup .swal2-range input {
            float: left;
            width: 80%;
        }

        .swal2-popup .swal2-range output {
            float: right;
            width: 20%;
            font-size: 20px;
            font-weight: 600;
            text-align: center;
        }

        .swal2-popup .swal2-range input,
        .swal2-popup .swal2-range output {
            height: 43px;
            line-height: 43px;
            vertical-align: middle;
            margin: 20px auto;
            padding: 0;
        }

        .swal2-popup .swal2-input {
            height: 43px;
            padding: 0 12px;
        }

        .swal2-popup .swal2-input[type='number'] {
            max-width: 150px;
        }

        .swal2-popup .swal2-file {
            font-size: 20px;
        }

        .swal2-popup .swal2-textarea {
            height: 108px;
            padding: 12px;
        }

        .swal2-popup .swal2-select {
            color: #545454;
            font-size: inherit;
            padding: 5px 10px;
            min-width: 40%;
            max-width: 100%;
        }

        .swal2-popup .swal2-radio {
            border: 0;
        }

        .swal2-popup .swal2-radio label:not(:first-child) {
            margin-left: 20px;
        }

        .swal2-popup .swal2-radio input,
        .swal2-popup .swal2-radio span {
            vertical-align: middle;
        }

        .swal2-popup .swal2-radio input {
            margin: 0 3px 0 0;
        }

        .swal2-popup .swal2-checkbox {
            color: #545454;
        }

        .swal2-popup .swal2-checkbox input,
        .swal2-popup .swal2-checkbox span {
            vertical-align: middle;
        }

        .swal2-popup .swal2-validationerror {
            background-color: #f0f0f0;
            margin: 0 -20px;
            overflow: hidden;
            padding: 10px;
            color: gray;
            font-size: 16px;
            font-weight: 300;
            display: none;
        }

        .swal2-popup .swal2-validationerror::before {
            content: '!';
            display: inline-block;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #ea7d7d;
            color: #fff;
            line-height: 24px;
            text-align: center;
            margin-right: 10px;
        }

        @supports (-ms-accelerator: true) {
            .swal2-range input {
                width: 100% !important;
            }

            .swal2-range output {
                display: none;
            }
        }

        @media all and (-ms-high-contrast: none),
        (-ms-high-contrast: active) {
            .swal2-range input {
                width: 100% !important;
            }

            .swal2-range output {
                display: none;
            }
        }

        .swal2-icon {
            width: 80px;
            height: 80px;
            border: 4px solid transparent;
            border-radius: 50%;
            margin: 20px auto 30px;
            padding: 0;
            position: relative;
            -webkit-box-sizing: content-box;
            box-sizing: content-box;
            cursor: default;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .swal2-icon.swal2-error {
            border-color: #f27474;
        }

        .swal2-icon.swal2-error .swal2-x-mark {
            position: relative;
            display: block;
        }

        .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            position: absolute;
            height: 5px;
            width: 47px;
            background-color: #f27474;
            display: block;
            top: 37px;
            border-radius: 2px;
        }

        .swal2-icon.swal2-error [class^='swal2-x-mark-line'][class$='left'] {
            -webkit-transform: rotate(45deg);
            transform: rotate(45deg);
            left: 17px;
        }

        .swal2-icon.swal2-error [class^='swal2-x-mark-line'][class$='right'] {
            -webkit-transform: rotate(-45deg);
            transform: rotate(-45deg);
            right: 16px;
        }

        .swal2-icon.swal2-warning {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #f8bb86;
            border-color: #facea8;
            font-size: 60px;
            line-height: 80px;
            text-align: center;
        }

        .swal2-icon.swal2-info {
            font-family: 'Open Sans', sans-serif;
            color: #3fc3ee;
            border-color: #9de0f6;
            font-size: 60px;
            line-height: 80px;
            text-align: center;
        }

        .swal2-icon.swal2-question {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #87adbd;
            border-color: #c9dae1;
            font-size: 60px;
            line-height: 80px;
            text-align: center;
        }

        .swal2-icon.swal2-success {
            border-color: #a5dc86;
        }

        .swal2-icon.swal2-success [class^='swal2-success-circular-line'] {
            border-radius: 50%;
            position: absolute;
            width: 60px;
            height: 120px;
            -webkit-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .swal2-icon.swal2-success [class^='swal2-success-circular-line'][class$='left'] {
            border-radius: 120px 0 0 120px;
            top: -7px;
            left: -33px;
            -webkit-transform: rotate(-45deg);
            transform: rotate(-45deg);
            -webkit-transform-origin: 60px 60px;
            transform-origin: 60px 60px;
        }

        .swal2-icon.swal2-success [class^='swal2-success-circular-line'][class$='right'] {
            border-radius: 0 120px 120px 0;
            top: -11px;
            left: 30px;
            -webkit-transform: rotate(-45deg);
            transform: rotate(-45deg);
            -webkit-transform-origin: 0 60px;
            transform-origin: 0 60px;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            width: 80px;
            height: 80px;
            border: 4px solid rgba(165, 220, 134, 0.2);
            border-radius: 50%;
            -webkit-box-sizing: content-box;
            box-sizing: content-box;
            position: absolute;
            left: -4px;
            top: -4px;
            z-index: 2;
        }

        .swal2-icon.swal2-success .swal2-success-fix {
            width: 7px;
            height: 90px;
            position: absolute;
            left: 28px;
            top: 8px;
            z-index: 1;
            -webkit-transform: rotate(-45deg);
            transform: rotate(-45deg);
        }

        .swal2-icon.swal2-success [class^='swal2-success-line'] {
            height: 5px;
            background-color: #a5dc86;
            display: block;
            border-radius: 2px;
            position: absolute;
            z-index: 2;
        }

        .swal2-icon.swal2-success [class^='swal2-success-line'][class$='tip'] {
            width: 25px;
            left: 14px;
            top: 46px;
            -webkit-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .swal2-icon.swal2-success [class^='swal2-success-line'][class$='long'] {
            width: 47px;
            right: 8px;
            top: 38px;
            -webkit-transform: rotate(-45deg);
            transform: rotate(-45deg);
        }

        .swal2-progresssteps {
            font-weight: 600;
            margin: 0 0 20px;
            padding: 0;
        }

        .swal2-progresssteps li {
            display: inline-block;
            position: relative;
        }

        .swal2-progresssteps .swal2-progresscircle {
            background: #3085d6;
            border-radius: 2em;
            color: #fff;
            height: 2em;
            line-height: 2em;
            text-align: center;
            width: 2em;
            z-index: 20;
        }

        .swal2-progresssteps .swal2-progresscircle:first-child {
            margin-left: 0;
        }

        .swal2-progresssteps .swal2-progresscircle:last-child {
            margin-right: 0;
        }

        .swal2-progresssteps .swal2-progresscircle.swal2-activeprogressstep {
            background: #3085d6;
        }

        .swal2-progresssteps .swal2-progresscircle.swal2-activeprogressstep~.swal2-progresscircle {
            background: #add8e6;
        }

        .swal2-progresssteps .swal2-progresscircle.swal2-activeprogressstep~.swal2-progressline {
            background: #add8e6;
        }

        .swal2-progresssteps .swal2-progressline {
            background: #3085d6;
            height: .4em;
            margin: 0 -1px;
            z-index: 10;
        }

        [class^='swal2'] {
            -webkit-tap-highlight-color: transparent;
        }

        @-webkit-keyframes showSweetToast {
            0% {
                -webkit-transform: translateY(-10px) rotateZ(2deg);
                transform: translateY(-10px) rotateZ(2deg);
                opacity: 0;
            }

            33% {
                -webkit-transform: translateY(0) rotateZ(-2deg);
                transform: translateY(0) rotateZ(-2deg);
                opacity: .5;
            }

            66% {
                -webkit-transform: translateY(5px) rotateZ(2deg);
                transform: translateY(5px) rotateZ(2deg);
                opacity: .7;
            }

            100% {
                -webkit-transform: translateY(0) rotateZ(0);
                transform: translateY(0) rotateZ(0);
                opacity: 1;
            }
        }

        @keyframes showSweetToast {
            0% {
                -webkit-transform: translateY(-10px) rotateZ(2deg);
                transform: translateY(-10px) rotateZ(2deg);
                opacity: 0;
            }

            33% {
                -webkit-transform: translateY(0) rotateZ(-2deg);
                transform: translateY(0) rotateZ(-2deg);
                opacity: .5;
            }

            66% {
                -webkit-transform: translateY(5px) rotateZ(2deg);
                transform: translateY(5px) rotateZ(2deg);
                opacity: .7;
            }

            100% {
                -webkit-transform: translateY(0) rotateZ(0);
                transform: translateY(0) rotateZ(0);
                opacity: 1;
            }
        }

        @-webkit-keyframes hideSweetToast {
            0% {
                opacity: 1;
            }

            33% {
                opacity: .5;
            }

            100% {
                -webkit-transform: rotateZ(1deg);
                transform: rotateZ(1deg);
                opacity: 0;
            }
        }

        @keyframes hideSweetToast {
            0% {
                opacity: 1;
            }

            33% {
                opacity: .5;
            }

            100% {
                -webkit-transform: rotateZ(1deg);
                transform: rotateZ(1deg);
                opacity: 0;
            }
        }

        @-webkit-keyframes showSweetAlert {
            0% {
                -webkit-transform: scale(0.7);
                transform: scale(0.7);
            }

            45% {
                -webkit-transform: scale(1.05);
                transform: scale(1.05);
            }

            80% {
                -webkit-transform: scale(0.95);
                transform: scale(0.95);
            }

            100% {
                -webkit-transform: scale(1);
                transform: scale(1);
            }
        }

        @keyframes showSweetAlert {
            0% {
                -webkit-transform: scale(0.7);
                transform: scale(0.7);
            }

            45% {
                -webkit-transform: scale(1.05);
                transform: scale(1.05);
            }

            80% {
                -webkit-transform: scale(0.95);
                transform: scale(0.95);
            }

            100% {
                -webkit-transform: scale(1);
                transform: scale(1);
            }
        }

        @-webkit-keyframes hideSweetAlert {
            0% {
                -webkit-transform: scale(1);
                transform: scale(1);
                opacity: 1;
            }

            100% {
                -webkit-transform: scale(0.5);
                transform: scale(0.5);
                opacity: 0;
            }
        }

        @keyframes hideSweetAlert {
            0% {
                -webkit-transform: scale(1);
                transform: scale(1);
                opacity: 1;
            }

            100% {
                -webkit-transform: scale(0.5);
                transform: scale(0.5);
                opacity: 0;
            }
        }

        .swal2-show {
            -webkit-animation: showSweetAlert .3s;
            animation: showSweetAlert .3s;
        }

        .swal2-show.swal2-toast {
            -webkit-animation: showSweetToast .5s;
            animation: showSweetToast .5s;
        }

        .swal2-show.swal2-noanimation {
            -webkit-animation: none;
            animation: none;
        }

        .swal2-hide {
            -webkit-animation: hideSweetAlert .15s forwards;
            animation: hideSweetAlert .15s forwards;
        }

        .swal2-hide.swal2-toast {
            -webkit-animation: hideSweetToast .2s forwards;
            animation: hideSweetToast .2s forwards;
        }

        .swal2-hide.swal2-noanimation {
            -webkit-animation: none;
            animation: none;
        }

        [dir='rtl'] .swal2-close {
            left: 8px;
            right: auto;
        }

        @-webkit-keyframes animate-success-tip {
            0% {
                width: 0;
                left: 1px;
                top: 19px;
            }

            54% {
                width: 0;
                left: 1px;
                top: 19px;
            }

            70% {
                width: 50px;
                left: -8px;
                top: 37px;
            }

            84% {
                width: 17px;
                left: 21px;
                top: 48px;
            }

            100% {
                width: 25px;
                left: 14px;
                top: 45px;
            }
        }

        @keyframes animate-success-tip {
            0% {
                width: 0;
                left: 1px;
                top: 19px;
            }

            54% {
                width: 0;
                left: 1px;
                top: 19px;
            }

            70% {
                width: 50px;
                left: -8px;
                top: 37px;
            }

            84% {
                width: 17px;
                left: 21px;
                top: 48px;
            }

            100% {
                width: 25px;
                left: 14px;
                top: 45px;
            }
        }

        @-webkit-keyframes animate-success-long {
            0% {
                width: 0;
                right: 46px;
                top: 54px;
            }

            65% {
                width: 0;
                right: 46px;
                top: 54px;
            }

            84% {
                width: 55px;
                right: 0;
                top: 35px;
            }

            100% {
                width: 47px;
                right: 8px;
                top: 38px;
            }
        }

        @keyframes animate-success-long {
            0% {
                width: 0;
                right: 46px;
                top: 54px;
            }

            65% {
                width: 0;
                right: 46px;
                top: 54px;
            }

            84% {
                width: 55px;
                right: 0;
                top: 35px;
            }

            100% {
                width: 47px;
                right: 8px;
                top: 38px;
            }
        }

        @-webkit-keyframes animate-toast-success-tip {
            0% {
                width: 0;
                left: 1px;
                top: 9px;
            }

            54% {
                width: 0;
                left: 1px;
                top: 9px;
            }

            70% {
                width: 24px;
                left: -4px;
                top: 17px;
            }

            84% {
                width: 8px;
                left: 10px;
                top: 20px;
            }

            100% {
                width: 12px;
                left: 3px;
                top: 18px;
            }
        }

        @keyframes animate-toast-success-tip {
            0% {
                width: 0;
                left: 1px;
                top: 9px;
            }

            54% {
                width: 0;
                left: 1px;
                top: 9px;
            }

            70% {
                width: 24px;
                left: -4px;
                top: 17px;
            }

            84% {
                width: 8px;
                left: 10px;
                top: 20px;
            }

            100% {
                width: 12px;
                left: 3px;
                top: 18px;
            }
        }

        @-webkit-keyframes animate-toast-success-long {
            0% {
                width: 0;
                right: 22px;
                top: 26px;
            }

            65% {
                width: 0;
                right: 22px;
                top: 26px;
            }

            84% {
                width: 26px;
                right: 0;
                top: 15px;
            }

            100% {
                width: 22px;
                right: 3px;
                top: 15px;
            }
        }

        @keyframes animate-toast-success-long {
            0% {
                width: 0;
                right: 22px;
                top: 26px;
            }

            65% {
                width: 0;
                right: 22px;
                top: 26px;
            }

            84% {
                width: 26px;
                right: 0;
                top: 15px;
            }

            100% {
                width: 22px;
                right: 3px;
                top: 15px;
            }
        }

        @-webkit-keyframes rotatePlaceholder {
            0% {
                -webkit-transform: rotate(-45deg);
                transform: rotate(-45deg);
            }

            5% {
                -webkit-transform: rotate(-45deg);
                transform: rotate(-45deg);
            }

            12% {
                -webkit-transform: rotate(-405deg);
                transform: rotate(-405deg);
            }

            100% {
                -webkit-transform: rotate(-405deg);
                transform: rotate(-405deg);
            }
        }

        @keyframes rotatePlaceholder {
            0% {
                -webkit-transform: rotate(-45deg);
                transform: rotate(-45deg);
            }

            5% {
                -webkit-transform: rotate(-45deg);
                transform: rotate(-45deg);
            }

            12% {
                -webkit-transform: rotate(-405deg);
                transform: rotate(-405deg);
            }

            100% {
                -webkit-transform: rotate(-405deg);
                transform: rotate(-405deg);
            }
        }

        .swal2-animate-success-line-tip {
            -webkit-animation: animate-success-tip .75s;
            animation: animate-success-tip .75s;
        }

        .swal2-animate-success-line-long {
            -webkit-animation: animate-success-long .75s;
            animation: animate-success-long .75s;
        }

        .swal2-success.swal2-animate-success-icon .swal2-success-circular-line-right {
            -webkit-animation: rotatePlaceholder 4.25s ease-in;
            animation: rotatePlaceholder 4.25s ease-in;
        }

        @-webkit-keyframes animate-error-icon {
            0% {
                -webkit-transform: rotateX(100deg);
                transform: rotateX(100deg);
                opacity: 0;
            }

            100% {
                -webkit-transform: rotateX(0deg);
                transform: rotateX(0deg);
                opacity: 1;
            }
        }

        @keyframes animate-error-icon {
            0% {
                -webkit-transform: rotateX(100deg);
                transform: rotateX(100deg);
                opacity: 0;
            }

            100% {
                -webkit-transform: rotateX(0deg);
                transform: rotateX(0deg);
                opacity: 1;
            }
        }

        .swal2-animate-error-icon {
            -webkit-animation: animate-error-icon .5s;
            animation: animate-error-icon .5s;
        }

        @-webkit-keyframes animate-x-mark {
            0% {
                -webkit-transform: scale(0.4);
                transform: scale(0.4);
                margin-top: 26px;
                opacity: 0;
            }

            50% {
                -webkit-transform: scale(0.4);
                transform: scale(0.4);
                margin-top: 26px;
                opacity: 0;
            }

            80% {
                -webkit-transform: scale(1.15);
                transform: scale(1.15);
                margin-top: -6px;
            }

            100% {
                -webkit-transform: scale(1);
                transform: scale(1);
                margin-top: 0;
                opacity: 1;
            }
        }

        @keyframes animate-x-mark {
            0% {
                -webkit-transform: scale(0.4);
                transform: scale(0.4);
                margin-top: 26px;
                opacity: 0;
            }

            50% {
                -webkit-transform: scale(0.4);
                transform: scale(0.4);
                margin-top: 26px;
                opacity: 0;
            }

            80% {
                -webkit-transform: scale(1.15);
                transform: scale(1.15);
                margin-top: -6px;
            }

            100% {
                -webkit-transform: scale(1);
                transform: scale(1);
                margin-top: 0;
                opacity: 1;
            }
        }

        .swal2-animate-x-mark {
            -webkit-animation: animate-x-mark .5s;
            animation: animate-x-mark .5s;
        }

        @-webkit-keyframes rotate-loading {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @keyframes rotate-loading {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">

    <div class="aspNetHidden">
        <input type="hidden" name="__EVENTTARGET" id="__EVENTTARGET" value="">
        <input type="hidden" name="__EVENTARGUMENT" id="__EVENTARGUMENT" value="">
        <input type="hidden" name="__LASTFOCUS" id="__LASTFOCUS" value="">
        <input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="/wEPDwUJNzE0MTQwNTI2D2QWAmYPZBYCAgMPZBYCAgEPZBYCAgUPEA8WBh4NRGF0YVRleHRGaWVsZAUKc3F1YXJlZmVldB4ORGF0YVZhbHVlRmllbGQFCnNxdWFyZWZlZXQeC18hRGF0YUJvdW5kZ2QQFQUVU2VsZWN0IFByb1NxdWFyZSBGZWV0BDEzNTAEMTgwMAQyNzAwAzkwMBUFFVNlbGVjdCBQcm9TcXVhcmUgRmVldAQxMzUwBDE4MDAEMjcwMAM5MDAUKwMFZ2dnZ2cWAWZkZBFp5If422c9mvSId/bTHLdMBcwrfchxaDSi4DHS2XO6">
    </div>

    <script type="text/javascript">
        //<![CDATA[
        var theForm = document.forms['form1'];
        if (!theForm) {
            theForm = document.form1;
        }

        function __doPostBack(eventTarget, eventArgument) {
            if (!theForm.onsubmit || (theForm.onsubmit() != false)) {
                theForm.__EVENTTARGET.value = eventTarget;
                theForm.__EVENTARGUMENT.value = eventArgument;
                theForm.submit();
            }
        }
        //]]>
    </script>


    <div class="aspNetHidden">

        <input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="E9B45F86">
        <input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="/wEdAAu5eSmXsb+bc15Vtr12BQzz4v9WNMeDllTTGL3im9nVDOq4mPsNKbUA9lCR9pQtfeCiQ5fTSuQwdsRxYbuGWxcza80+VE81RqRctJJ2EuJ+JGw5bbP9K1Ooap0c+XFg/lEyUQcsQwJuv1QL7ejwn/+LY7olOpXsVaDMzyNS0s4iQfsbYLUSZaO/DROhIk8D+tX707C7PzzkS35zUQNvvogHjDu35o5CUn6umW4JNpE1p1YmNWyFraX6Wc+ZJTmGCT9ixb4BV7cPhfVMX8TP+Mh4">
    </div>
    <div>
        <div class="backtotop-wrap cursor-pointer">
            <svg class="backtotop-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;"></path>
            </svg>
        </div>
        <!-- Back to top end -->

        <!-- search area start -->
        <div class="vw-search-area">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="vw-search-form">
                            <div class="vw-search-close text-center mb-20">
                                <button class="vw-search-close-btn vw-search-close-btn"></button>
                            </div>

                            <div class="vw-search-input">
                                <input type="text" placeholder="Type your keyword &amp; hit the enter button...">
                                <button type="submit"><i class="icon-search"></i></button>
                            </div>
                            <div class="vw-search-category">
                                <!-- <span>Search by : </span>
                        <a href="#">Agency, </a>
                        <a href="#">Artist Portfolio, </a>
                        <a href="#">Branding, </a>
                        <a href="#">creative portfolio, </a>
                        <a href="#">Design </a> -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="body-overlay"></div>
        <!-- search area end -->

        <!-- Offcanvas area start -->
        <div class="fix">
            <div class="offcanvas__info">
                <div class="offcanvas__wrapper">
                    <div class="offcanvas__content">
                        <div class="offcanvas__top mb-40 d-flex justify-content-between align-items-center">
                            <div class="offcanvas__logo">
                                <a href="index.php" title="Home Page - Hari Homes Developers">
                                    <img src="image/harihomes1-logo.png" alt="Header Logo">
                                </a>
                            </div>
                            <div class="offcanvas__close">
                                <button>
                                    <i class="fal fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="offcanvas__search mb-25">
                            <p class="text-white">Hari Homes Developers is very humbly endeavoring to generate the sense of belonging and responsibility to propagate and deliver only good in the best possible manner.</p>
                        </div>
                        <div class="mobile-menu fix mb-40"></div>
                        <div class="offcanvas__contact mt-30 mb-20">
                            <h4>Contact Info</h4>
                            <ul>
                                <li class="d-flex align-items-center">
                                    <div class="offcanvas__contact-icon mr-15">
                                        <i class="fal fa-map-marker-alt"></i>
                                    </div>
                                    <div class="offcanvas__contact-text">
                                        <a target="_blank" href="#">Barheta Road Laheriasarai, Darbhanga, Bihar-846001</a>
                                    </div>
                                </li>
                                <li class="d-flex align-items-center">
                                    <div class="offcanvas__contact-icon mr-15">
                                        <i class="fal fa-phone"></i>
                                    </div>
                                    <div class="offcanvas__contact-text">
                                        <a href="tel:+917070521500">+917070521500</a>
                                    </div>
                                </li>
                                <li class="d-flex align-items-center">
                                    <div class="offcanvas__contact-icon mr-15">
                                        <i class="fal fa-envelope"></i>
                                    </div>
                                    <div class="offcanvas__contact-text">
                                        <a href="tel:+917070521500"><span class="mailto:Harihomes34@gmail.com">Harihomes34@gmail.com</span></a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="offcanvas__social">
                            <ul>
                                <li><a href="https://www.facebook.com/profile.php?id=61561482782305" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="https://www.instagram.com/harihomes9/" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                <li><a href="https://x.com/hariomsdevelop" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="https://www.youtube.com/@hariomsdeveloper" target="_blank"><i class="fab fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas__overlay"></div>
        <div class="offcanvas__overlay-white"></div>
        <!-- Offcanvas area start -->

        <!-- Header area start -->
        <header>
            <div class="container-fluid bg-color-1">
                <div class="header-top">
                    <div class="header-top-welcome-text">
                        <span class="welcome">Welcome to Hari Home Developers</span>
                    </div>
                    <div class="header-top-contact-info">
                        <span class="mail p-relative"><a href="mailto:Harihomes34@gmail.com">Harihomes34@gmail.com</a></span>
                        <span class="phone p-relative"><a href="tel:+917070521500">+917070521500</a></span>
                    </div>
                </div>
            </div>
            <div id="header-sticky" class="header-area header-style-one">
                <div class="container-fluid">
                    <div class="mega-menu-wrapper">
                        <div class="header-main">
                            <div class="header-left">
                                <div class="header-logo">
                                    <a href="index.php" title="Home Page - Hari Homes Developers">
                                        <img src="image/harihomes1-logo.png" alt="header logo">
                                    </a>
                                </div>
                                <div class="menu-with-search-wrapper">
                                    <div class="mean__menu-wrapper d-none d-lg-block">
                                        <div class="main-menu">
                                            <nav id="mobile-menu" style="display: block;">
                                                <ul>
                                                    <li class=" active">
                                                        <a href="index.php" title="Home Page - Hari Homes Developers">Home</a>
                                                    </li>
                                                    <li class="has-dropdown">
                                                        <a href="#">About</a>
                                                        <ul class="submenu">
                                                            <li><a href="about.php">About Company</a></li>
                                                            <li><a href="about_director.php">About Director</a></li>
                                                        </ul>
                                                    </li>

                                                    <li class="has-dropdown">
                                                        <a href="#">Media</a>
                                                        <ul class="submenu">
                                                            <li><a href="Gallery.php">Gallery</a></li>


                                                        </ul>
                                                    </li>
                                                    <li class="has-dropdown">
                                                        <a href="#">Map</a>
                                                        <ul class="submenu">
                                                            <li><a href="2D.php">Phase 1</a></li>
                                                            <li><a href="Phase2map.php">Phase 2</a></li>

                                                        </ul>
                                                    </li>

                                                    <li class="has-dropdown">
                                                        <a href="#">Projects</a>
                                                        <ul class="submenu">
                                                            <li><a href="running_projects.php">Running Projects</a></li>
                                                            <li><a href="completed_project.php">Completed Projects</a></li>
                                                            <li><a href="upcoming_projects.php">Upcoming Projects</a></li>
                                                        </ul>
                                                    </li>
                                                    <li>
                                                        <a href="blog.php">Blog</a>

                                                    </li>
                                                    <li>
                                                        <a href="New_Contact.php">Contact</a>
                                                    </li>

                                                    <li class="has-dropdown">
                                                        <a href="#">Login</a>
                                                        <ul class="submenu">
                                                            <li><a href="login.php">Associate</a></li>

                                                            <li><a href="Customer_Login.php">Customer</a></li>
                                                        </ul>
                                                    </li>

                                                    <?php
                                                    include 'connectdb.php';
                                                    // Check if there are notices
                                                    $stmt = $pdo->query("SELECT * FROM tbl_notices ORDER BY created_at DESC");
                                                    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    $hasNotices = !empty($notices); // True if there are notices
                                                    ?>

                                                    <?php if ($hasNotices): ?>
                                                        <a href="notice.php">
                                                            <div class="bell-container" id="bell">
                                                                <i class="bi bi-bell-fill" style="color: #ff9027;"></i>
                                                                <span class="bell-badge">!</span>
                                                            </div>
                                                        </a>
                                                    <?php endif; ?>

                                                </ul>
                                            </nav>
                                            <!-- for wp -->
                                            <div class="header__hamburger ml-50 d-none">
                                                <button type="button" class="hamburger-btn offcanvas-open-btn">
                                                    <span>01</span>
                                                    <span>01</span>
                                                    <span>01</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="header-right d-flex justify-content-end">
                                <div class="header-action  gap-5">
                                    <div class="header-link">
                                        <a class="primary-btn-1 btn-hover" href="Get_quote.php">
                                            Booking <i class="icon-arrow-double-right"></i>
                                            <span style="top: 147.172px; left: 108.5px;"></span>
                                        </a>
                                    </div>
                                </div>
                                <div class="header__hamburger my-auto">
                                    <div class="sidebar__toggle" style="background-color:white;padding:5px">
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


        <div class="container d-flex justify-content-center" style="margin-top:16rem;margin-bottom:1rem;">
            <div class="col-md-10">
                <div style="background: #fff; padding: 10px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                    <div style="background-color: #ebebeb;">
                        <div>
                            <h3 style="text-align: center;">Booking Form</h3>
                        </div>
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <b>Name</b>
                                    <input name="name" type="text" id="ContentPlaceHolder1_Txtname" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <b>Mobile No</b>
                                    <input name="mobile" type="text" id="ContentPlaceHolder1_Txtmobile" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <b>Square Feet</b>
                                    <select name="sqfeet" id="ContentPlaceHolder1_txtsqfeet" class="form-control mb-2" style="font-size: medium;height:5rem;">
                                        <option value="Select ProSquare Feet">Select ProSquare Feet</option>
                                        <option value="1350">1350</option>
                                        <option value="1800">1800</option>
                                        <option value="2700">2700</option>
                                        <option value="900">900</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <b>Product Name</b>
                                    <select name="product_type" id="ContentPlaceHolder1_txtType" class="form-control" style="font-size: medium;height:5rem;">
                                        <option value="1">One Time Registry</option>
                                        <option value="2">EMI Mode</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <b>Address</b>
                                    <textarea name="address" rows="2" cols="20" id="ContentPlaceHolder1_txtaddres" class="form-control" style="height:100px;"></textarea>
                                </div>
                            </div>
                            <div class="text-center">
                                <input type="submit" value="Submit" class="primary-btn-1 btn-hover">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <footer>
            <div class="footer-main bg-color-1 p-relative">
                <div class="shape" data-background="images/footer-bg.png" style="background-image: url(&quot;images/footer-bg.png&quot;);"></div>
                <div class="custom-container p-relative">
                    <div class="footer-top pt-65 pb-30">
                        <div class="footer-logo">
                            <a href="index.php" title="Home Page - Hari Homes Developers">
                                <img src="image/harihomes1-logo.png" width="25%" alt="">
                            </a>
                        </div>
                        <div class="footer-call">

                            <div class="info">
                                <span>Have Any Question ? Call</span>
                                <h4><a href="tel:+917070521500">+917070521500</a></h4>
                            </div>
                        </div>
                    </div>
                    <div class="footer-middle pt-50 pb-70">
                        <div class="row g-4">
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                                <div class="footer-widget-1">

                                    <ul class="company-info mt-30">
                                        <li class="phone-number"><a href="tel:+917070521500">+917070521500</a></li>
                                        <li class="email"><a href="mailto:Harihomes34@gmail.com">Harihomes34@gmail.com</a></li>
                                        <li class="address">Barheta Road Laheriasarai, Darbhanga, Bihar-846001</li>
                                    </ul>
                                    <div class="offcanvas__social">
                                        <ul>
                                            <li><a href="https://www.facebook.com/profile.php?id=61561482782305" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                            <li><a href="https://www.instagram.com/harihomes9/" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                            <li><a href="https://x.com/hariomsdevelop" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                            <li><a href="https://www.youtube.com/@hariomsdeveloper" target="_blank"><i class="fab fa-youtube"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                                <div class="footer-widget-2 pl-70 pr-70">
                                    <h5 class="footer-widget-title mb-30">Quick Link</h5>
                                    <ul class="footer-menu-links">
                                        <li><a href="about.php">About Us</a></li>
                                        <li><a href="blog.php">Blogs</a></li>
                                        <li><a href="running_projects.php">Projects</a></li>
                                        <li><a href="gallery.php">Gallery</a></li>
                                        <li><a href="contact.php">Contact Us</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                                <div class="footer-widget-3">
                                    <h5 class="footer-widget-title mb-25">News Letter</h5>
                                    <div class="subscribe-form p-relative">
                                        <form action="#">
                                            <input type="email" placeholder="Email Address" required="">
                                            <button type="submit"><i class="icon-arrow_carrot-right"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                                <div class="footer-widget-4">
                                    <h5 class="footer-widget-title mb-15">Gallery</h5>
                                    <div class="footer-gallery">
                                        <div class="image-area p-relative">
                                            <a class="popup-image" href="images/footer-1.jpg">
                                                <img src="images/footer-1.jpg" alt="">
                                                <i class="icon-plus"></i>
                                            </a>
                                        </div>
                                        <div class="image-area p-relative">
                                            <a class="popup-image" href="images/footer-2.jpg">
                                                <img src="images/footer-2.jpg" alt="">
                                                <i class="icon-plus"></i>
                                            </a>
                                        </div>
                                        <div class="image-area p-relative">
                                            <a class="popup-image" href="images/footer-3.jpg">
                                                <img src="images/footer-3.jpg" alt="">
                                                <i class="icon-plus"></i>
                                            </a>
                                        </div>
                                        <div class="image-area p-relative">
                                            <a class="popup-image" href="images/footer-4.jpg">
                                                <img src="images/footer-4.jpg" alt="">
                                                <i class="icon-plus"></i>
                                            </a>
                                        </div>
                                        <div class="image-area p-relative">
                                            <a class="popup-image" href="images/footer-5.jpg">
                                                <img src="images/footer-5.jpg" alt="">
                                                <i class="icon-plus"></i>
                                            </a>
                                        </div>
                                        <div class="image-area p-relative">
                                            <a class="popup-image" href="images/footer-6.jpg">
                                                <img src="images/footer-6.jpg" alt="">
                                                <i class="icon-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="footer-bottom bg-color-2 pt-15 pb-15 text-center">
                        <p class="copy-right m-0">© 2024 Hari Home Developers, All Rights Reserved Designed and developed by <a href="https://www.infoerasoftware.com/" target="_blank">Info Era Software Services Pvt. Ltd.</a></p>
                    </div>
                </div>
            </div>
        </footer>
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

        <script>
            function vibrateBell() {
                let bell = document.getElementById("bell");
                bell.classList.add("vibrating");

                setTimeout(() => {
                    bell.classList.remove("vibrating");
                }, 500); // Stop after 500ms
            }

            setInterval(vibrateBell, 2000); // Vibrate every 2 seconds
        </script>

    </div>


</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>