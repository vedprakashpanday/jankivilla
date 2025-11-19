<?php
try {
    $pdo = new PDO("\x6d\171\x73\x71\x6c\x3a\x68\x6f\163\x74\x3d\x6c\x6f\x63\x61\154\x68\x6f\163\x74\73\x64\x62\156\141\155\145\x3d\150\141\x72\151\x68\157\155\145\x73", "\162\157\x6f\164", "\x49\x6e\146\x6f\145\162\141\100\62\60\62\65");
} catch (PDOException $f) {
    echo $f->getMessage();
}
