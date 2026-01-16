<?php
if (isset($_GET["id"])) {

    $id = (int) $_GET["id"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "db_edt";
    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // ดึงข้อมูลเอกสารรับ + stock
    $sql = "SELECT g.Get_Product_ID, g.Get_Num, p.Product_Stock
            FROM getmast g
            JOIN productmast p ON g.Get_Product_ID = p.Product_ID
            WHERE g.Get_ID = $id";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("Location: /work/receive.php?error=not_found");
        exit;
    }

    $product_id = (int) $row['Get_Product_ID'];
    $num = (int) $row['Get_Num'];
    $stock = (int) $row['Product_Stock'];

    if ($stock - $num < 0) {
        header("Location: /work/receive.php?error=stock_not_enough");
        exit;
    }

    $connection->begin_transaction();

    try {
        $sql = "UPDATE productmast
                SET Product_Stock = Product_Stock - $num
                WHERE Product_ID = $product_id";
        $connection->query($sql);

        $sql = "DELETE FROM getmast WHERE Get_ID = $id";
        $connection->query($sql);

        $connection->commit();

        header("Location: /work/receive.php?success=delete_receive");
        exit;

    } catch (Exception $e) {
        $connection->rollback();
        header("Location: /work/receive.php?error=system_error");
        exit;
    }
}

header("Location: /work/receive.php");
exit;
?>