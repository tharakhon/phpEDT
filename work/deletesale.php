<?php
if (isset($_GET["id"])) {

    $sale_id = (int) $_GET["id"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "db_edt";
    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $sql = "SELECT Sale_Product_ID, Sale_Num
            FROM salemast
            WHERE Sale_ID = $sale_id";
    $sale = $connection->query($sql)->fetch_assoc();

    if (!$sale) {
        header("Location: /work/index.php?error=not_found");
        exit;
    }

    $product_id = (int) $sale['Sale_Product_ID'];
    $sale_num = (int) $sale['Sale_Num'];

    $connection->begin_transaction();

    try {

        $sql = "UPDATE productmast
                SET Product_Stock = Product_Stock + $sale_num
                WHERE Product_ID = $product_id";
        $connection->query($sql);
        if ($connection->affected_rows !== 1) {
            throw new Exception();
        }

        $sql = "DELETE FROM salemast WHERE Sale_ID = $sale_id";
        $connection->query($sql);
        if ($connection->affected_rows !== 1) {
            throw new Exception();
        }
        $connection->commit();

        header("Location: /work/index.php?success=delete_sale");
        exit;

    } catch (Exception $e) {
        $connection->rollback();
        header("Location: /work/index.php?error=delete_fail");
        exit;
    }
}

header("Location: /work/index.php");
exit;
?>