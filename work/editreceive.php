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

    $sql = "SELECT g.*, p.Product_Name 
            FROM getmast g
            LEFT JOIN productmast p 
                ON g.Get_Product_ID = p.Product_ID
            WHERE g.Get_ID = $id";

    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: /work/index.php");
        exit;
    }

    $product_id = $row["Get_Product_ID"];
    $product_name = $row["Product_Name"];
    $num = $row["Get_Num"];
    $getname = $row["Get_Name"];

} else {

    $id = (int) $_POST["id"];
    $num_new = (double) $_POST["num"];
    $getname = $connection->real_escape_string($_POST["getname"]);

    $sql = "SELECT Get_Product_ID, Get_Num 
            FROM getmast 
            WHERE Get_ID = $id";
    $old = $connection->query($sql)->fetch_assoc();

    if (!$old) {
        $errorMessage = "ไม่พบข้อมูลเดิม";
    } else {

        $product_id = (int) $old["Get_Product_ID"];
        $num_old = (double) $old["Get_Num"];
        $diff = $num_new - $num_old;

        $connection->begin_transaction();

        try {
            $sql = "UPDATE productmast
                    SET Product_Stock = Product_Stock + ($diff)
                    WHERE Product_ID = $product_id";
            $connection->query($sql);

            $sql = "UPDATE getmast
                    SET Get_Num = $num_new,
                        Get_Name = '$getname'
                    WHERE Get_ID = $id";
            $connection->query($sql);

            $id = "";
            $product_id = "";
            $product_name = "";
            $num = "";
            $getname = "";
            $num_new = "";
            $num_old = "";

            $connection->commit();
            $successMessage = "แก้ไขเอกสารรับสำเร็จ";
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
    <title>แก้ไขเอกสารรับสินค้า</title>
</head>

<body>
    <div class="container my-5 d-flex justify-content-center">
        <div class="card shadow w-75">

            <div class="card-header bg-success text-white">
                <h4 class="mb-0">แก้ไขเอกสารรับสินค้า</h4>
            </div>

            <div class="card-body">

                <?php
                if (!empty($errorMessage)) {
                    echo "
                    <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                        <strong>$errorMessage</strong>
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>
                    ";
                }
                ?>

                <form method="post">
                    <input type="hidden" name="id" value="<?= $id ?>">

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">ชื่อสินค้า</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="<?= $product_name ?>" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">จำนวนรับ</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="num" value="<?= $num ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">รับจากใคร</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="getname" value="<?= $getname ?>" required>
                        </div>
                    </div>

                    <?php
                    if (!empty($successMessage)) {
                        echo "
                        <div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <strong>$successMessage</strong>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                        ";
                    }
                    ?>

                    <div class="row mt-4">
                        <div class="col-sm-6 d-grid">
                            <button type="submit" class="btn btn-success">
                                บันทึก
                            </button>
                        </div>
                        <div class="col-sm-6 d-grid">
                            <a href="/work/receive.php" class="btn btn-outline-danger">
                                ยกเลิก
                            </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>


</html>