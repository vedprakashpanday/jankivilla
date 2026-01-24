<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '512M');
session_start();

require __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

include_once "connectdb.php";

$id = $_GET['landid'] ?? null;
if (!$id) die("Invalid Land ID");

$stmt = $pdo->prepare("SELECT * FROM land_owner_payments WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) die("Record not found");

/* PDF MODE CHECK */
$isPdf = isset($_GET['pdf']) && $_GET['pdf'] == 1;

/* START BUFFER */
ob_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Land Owner Details</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            background: #d9d7b8;
            font-family: "Times New Roman", serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .container-page {
            max-width: 210mm;
            margin: auto;
            background: #d9d7b8;
            padding: 10px 15px;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .header h4 {
            margin: 0;
            font-weight: bold;
        }

        .header small {
            font-size: 12px;
        }

        /* Section title */
        .section-title {
            display: inline-block;
            border: 2px solid #000;
            padding: 2px 14px;
            font-weight: bold;
            margin: 8px auto;
        }

        /* Form table */
        .form-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        
        }

        .form-table td {
            padding: 10px 4px;
            vertical-align: bottom;
        }

        /* label + line */
        .label {
            white-space: nowrap;
            width: 100px;
        }

        .line {
            display: inline-block;
            width: 100%;
            border-bottom: 1px solid #000;
            height: 20px;
        }

        /* Main land table */
        .land-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .land-table th,
        .land-table td {
            border: 1px solid #000;
            height: 32px;
            text-align: center;
            font-size: 14px;
        }

        .print-btn {
            position: fixed;
            top: 10px;
            right: 15px;
        }

      @media print {
    .action-buttons {
        display: none !important;
    }
}

/* Hide buttons in PDF mode */
<?php if ($isPdf): ?>
.action-buttons {
    display: none !important;
}
<?php endif; ?>

        

    </style>
</head>

<body>

    <div class="action-buttons">
    <button class="btn btn-dark print-btn" onclick="window.print()">Print</button>

    <a href="?landid=<?= $id ?>&pdf=1"
       class="btn btn-dark print-btn"
       style="top:50px">
       Download PDF
    </a>
</div>

    <div class="container-page">

        <!-- HEADER -->
        <div class="header">
            <h4>AMITABH BUILDERS & DEVELOPERS PVT. LTD.</h4>
            <small>
                CIN No.: U43298BR2024PTC07212<br>
                1st Floor, Pappu Yadav Building, South of NH-57, Kakargahati,<br>
                Adarsh Chowk, Darbhanga (Bihar) – 846007
            </small>
        </div>

        <div class="text-center">
            <div class="section-title">LAND OWNER DETAILS</div>
        </div>

        <!-- EXACT FORM STRUCTURE -->
        <table class="form-table">
            <?php
            $id = $_GET['landid'];

            $stmt = $pdo->prepare("SELECT * FROM land_owner_payments WHERE id = :id");
            $stmt->execute(['id' => $id]);

            $record = $stmt->fetch(PDO::FETCH_ASSOC);


            function jsonToText1($json, $type = 'old')
            {
                if (!empty($json)) {
                    $data = json_decode($json, true);
                    if (!is_array($data)) return '';

                    if ($type === 'old' && !empty($data['old_khesra_no'])) {
                        return htmlspecialchars($data['old_khesra_no']);
                    }

                    if ($type === 'new' && !empty($data['new_khesra_no'])) {
                        return htmlspecialchars($data['new_khesra_no']);
                    }

                    if ($type === 'new' && !empty($data['new_khata'])) {
                        return htmlspecialchars($data['new_khata']);
                    }

                    if ($type === 'old' && !empty($data['old_khata'])) {
                        return htmlspecialchars($data['old_khata']);
                    }
                }
                return '';
            }

            function jsonToText2($json, $type = '')
{
    if (empty($json) || empty($type)) {
        return '';
    }

    $data = json_decode($json, true);

    if (!is_array($data)) {
        return '';
    }

    // direct key access
    if (isset($data[$type]) && $data[$type] !== '') {
        return htmlspecialchars($data[$type]);
    }

    return '';
}

            ?>



            <tr>
                <td class="label">State</td>
                <td><span class="line"><?= $record['lo_state'] ?></span></td>
                <td class="label">District</td>
                <td><span class="line"><?= $record['lo_district'] ?></span></td>
                <td class="label">Thana No.</td>
                <td><span class="line"><?= $record['thana_no'] ?></span></td>
            </tr>
            <tr>
                <td class="label">Block</td>
                <td><span class="line"><?= $record['lo_block'] ?></span></td>
                <td class="label">Panchayat</td>
                <td><span class="line"><?= $record['lo_panchayat'] ?></span></td>
                <td class="label">Village</td>
                <td><span class="line"><?= $record['lo_village'] ?></span></td>
            </tr>
            <tr>
                <td class="label">Mauje Name</td>
                <td colspan="5"><span class="line"><?= $record['mauze_name'] ?></span></td>
            </tr>
            <tr>
                <td class="label">Land Owner Name</td>
                <td colspan="5"><span class="line"><?= $record['land_owner_name'] ?></span></td>
            </tr>
            <tr>
                <td class="label">S/o, W/o, D/o</td>
                <td colspan="2"><span class="line"><?= $record['relation_name'] ?></span></td>
                <td class="label">Date of Birth</td>
                <td colspan="2"><span class="line"><?= $record['lo_dob'] ?></span></td>
            </tr>
            <tr>
                <td class="label">Communication Address</td>
                <td colspan="5"><span class="line"><?= $record['address'] ?></span></td>
            </tr>
            <tr>
                <td class="label">Aadhar No.</td>
                <td colspan="2"><span class="line"><?= $record['lo_aadhar'] ?></span></td>
                <td class="label">PAN No.</td>
                <td colspan="2"><span class="line"><?= $record['lo_pan'] ?></span></td>
            </tr>
            <tr>
                <td class="label">Mobile No. (1)</td>
                <td colspan="2"><span class="line"><?= $record['mobile1'] ?></span></td>
                <td class="label">Mobile No. (2)</td>
                <td colspan="2"><span class="line"><?= $record['mobile2'] ?></span></td>
            </tr>
            <tr>
                <td class="label">Agent Name</td>
                <td colspan="2"><span class="line"><?= $record['nom_name'] ?></span></td>
                <td class="label">Agent Mobile</td>
                <td colspan="2"><span class="line"><?= $record['nom_mobile'] ?></span></td>
            </tr>
            <tr>
                <td class="label">Agent Address</td>
                <td colspan="5"><span class="line"><?= $record['nom_address'] ?></span></td>
            </tr>
            <tr>
                <td class="label">Agent Commission / Katha</td>
                <td colspan="2"><span class="line"><?= $record['nom_pin'] ?></span></td>
                <td class="label">Total Commission</td>
                <td colspan="2"><span class="line"><?= $record['nom_state'] ?></span></td>
            </tr>
            <tr>
                <td class="label">Rate / Katha</td>
                <td colspan="2"><span class="line"><?= $record['rate_per_katha'] ?></span></td>
                <td class="label">Total Land Value</td>
                <td colspan="2"><span class="line"><?= $record['total_land_value'] ?></span></td>
            </tr>

            <!-- ONLY ADDITION -->
            <tr>
                <td class="label"><strong>Jamabandi No.</strong></td>
                <td colspan="5"><span class="line"><?= $record['jamabandi'] ?></span></td>
            </tr>
        </table>

        <!-- LOWER TABLE (UNCHANGED STRUCTURE) -->
        <table class="land-table">
            <tr>
                <th colspan="2">Khata No.</th>
                <th colspan="2">Khesra No.</th>
                <th colspan="4">Rakwa</th>
                <th rowspan="2">Decimal</th>
            </tr>
            <tr>
                <th>Old</th>
                <th>New</th>
                <th>Old</th>
                <th>New</th>
                <th>B</th>
                <th>K</th>
                <th>D</th>
                <th>K</th>
            </tr>
            <tr>
                <td><?= jsonToText1($record['khata'], 'old') ?? 'null' ?></td>
                <td><?= jsonToText1($record['khata'], 'new') ?? 'null' ?></td>
                <td><?= jsonToText1($record['khesra_no'], 'old') ?? 'null' ?></td>
                <td><?= jsonToText1($record['khesra_no'], 'new') ?? 'null' ?></td>
                <td><?= jsonToText2($record['rakuwa'], 'bigha') ?? 'null' ?></td>
                <td><?= jsonToText2($record['rakuwa'], 'kattha') ?? 'null' ?></td>
                <td><?= jsonToText2($record['rakuwa'], 'dhoor') ?? 'null' ?></td>
                <td><?= jsonToText2($record['rakuwa'], 'kanma') ?? 'null' ?></td>
                <td><?= jsonToText2($record['rakuwa'], 'dismil') ?? 'null' ?></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>

    </div>
</body>

</html>

<?php
$html = ob_get_clean();

if ($isPdf) {
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Times-Roman');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dompdf->stream(
        "Land_Owner_Details_$id.pdf",
        ["Attachment" => true] // force download
    );
    exit;
}

echo $html;
?>