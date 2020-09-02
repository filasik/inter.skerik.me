<?php
include "inc/core/config.php";
global $database;
if (isset($_POST['id'])) {
    $database->query("update zalozky set archivovano=1 where id='$_POST[id]'");
    echo 1;
}
exit;