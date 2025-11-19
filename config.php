<?php

try {

    $pdo = new PDO('mysql:host=localhost;dbname=harihomes', 'root', '');
    //echo'Connection Successfull'; 

} catch (PDOException $f) {

    echo $f->getMessage();
}
