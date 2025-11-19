<?php
try {
    $pdo = new PDO("\x6d\171\163\x71\154\72\x68\157\x73\x74\75\154\x6f\x63\x61\154\x68\x6f\163\x74\x3b\x64\x62\156\x61\x6d\145\75\x6a\141\156\x6b\x69\166\151\154\x6c\x61", "\x72\x6f\x6f\164", "\x49\156\146\157\145\162\x61\x40\x32\60\x32\65");
} catch (PDOException $f) {
    echo $f->getMessage();
}
