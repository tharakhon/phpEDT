<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_edt";
$connection = new mysqli($servername, $username, $password, $database);

$id = "";
$product_id = "";
$product_name = "";
$num = "";
$getname = "";
$price = "";
$num_new = "";
$num_old = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (!isset($_GET["id"])) {
        header("location: /work/index.php");
        exit;
    }

    $id = (int) $_GET["id"];

    $sql = "SELECT s.*, p.Product_Name 
            FROM salemast s
            LEFT JOIN productmast p 
                ON s.Sale_Product_ID = p.Product_ID
            WHERE s.Sale_ID = $id";

    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: /work/index.php");
        exit;
    }

    $product_id = $row["Sale_Product_ID"];
    $product_name = $row["Product_Name"];
    $num = $row["Sale_Num"];
    $getname = $row["Sale_Name"];
    $price = $row["Sale_Price"];

} else {

    $id = (int) $_POST["id"];
    $num_new = (double) $_POST["num"];
    $price = (double) $_POST["price"];
    $getname = $connection->real_escape_string($_POST["getname"]);

    $sql = "SELECT Sale_Product_ID, Sale_Num 
            FROM salemast 
            WHERE Sale_ID = $id";
    $old = $connection->query($sql)->fetch_assoc();

    if (!$old) {
        $errorMessage = "ไม่พบข้อมูลเดิม";
    } else {

        $product_id = (int) $old["Sale_Product_ID"];
        $num_old = (double) $old["Sale_Num"];
        $diff = $num_new - $num_old;

        $connection->begin_transaction();

        try {
            $sql = "UPDATE productmast
                    SET Product_Stock = Product_Stock + ($diff)
                    WHERE Product_ID = $product_id";
            $connection->query($sql);

            $sql = "UPDATE salemast
                    SET Sale_Num = $num_new,
                        Sale_Price = $price * $num_new,
                        Sale_Name = '$getname'
                    WHERE Sale_ID = $id";
            $connection->query($sql);

            $id = "";
            $product_id = "";
            $product_name = "";
            $num = "";
            $price = "";
            $getname = "";
            $num_new = "";
            $num_old = "";

            $connection->commit();
            $successMessage = "แก้ไขเอกสารขายสำเร็จ";
            header("Location: /work/index.php");
            exit;
        } catch (Exception $e) {
            $connection->rollback();
            $errorMessage = "เกิดข้อผิดพลาด";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <title>Document</title>
</head>

<body>
    <div class="container my-5">

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">แก้ไขเอกสารขาย</h5>
            </div>

            <div class="card-body">

                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong><?= $errorMessage ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="id" value="<?= $id ?>">

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">ชื่อสินค้า</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" value="<?= $product_name ?>" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">จำนวนขาย</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="num" value="<?= $num ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">ขายให้</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" value="<?= $getname ?>" disabled>
                            <input type="hidden" name="getname" value="<?= $getname ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">ราคา</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="price" value="<?= $price ?>" required>
                        </div>
                    </div>

                    <?php if (!empty($successMessage)): ?>
                        <div class="row mb-3">
                            <div class="offset-sm-3 col-sm-6">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong><?= $successMessage ?></strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row mb-3">
                        <div class="offset-sm-3 col-sm-3 d-grid">
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                        <div class="col-sm-3 d-grid">
                            <a class="btn btn-outline-danger" href="/work/index.php">ยกเลิก</a>
                        </div>
                    </div>

                </form>

            </div>
        </div>

    </div>
</body>


</html>