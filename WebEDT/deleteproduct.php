<?php

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "db_edt";
    $connection = new mysqli($servername, $username, $password, $database, 3307);

    $sql = "DELETE FROM productmast WHERE Product_ID = $id";
    $result = $connection->query($sql);

}
header("location: /WebEDT/product.php");
exit;
?>