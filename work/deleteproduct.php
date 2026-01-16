<?php
if (isset($_GET["id"])) {

    $product_id = (int) $_GET["id"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "db_edt";
    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $sql = "SELECT COUNT(*) AS total FROM getmast WHERE Get_Product_ID = $product_id";
    $get_count = $connection->query($sql)->fetch_assoc()['total'];

    $sql = "SELECT COUNT(*) AS total FROM salemast WHERE Sale_Product_ID = $product_id";
    $sale_count = $connection->query($sql)->fetch_assoc()['total'];

    if ($get_count > 0 || $sale_count > 0) {
        header("Location: /work/product.php?error=product_used");
        exit;
    }

    $sql = "DELETE FROM productmast WHERE Product_ID = $product_id";
    $result = $connection->query($sql);

    if ($result) {
        header("Location: /work/product.php?success=delete_product");
    } else {
        header("Location: /work/product.php?error=system_error");
    }
    exit;
}

header("Location: /work/product.php");
exit;
?>