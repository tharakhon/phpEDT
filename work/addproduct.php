<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_edt";

$connection = new mysqli($servername, $username, $password, $database);

$name = "";
$cost = "";
$price = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST["name"]);
    $cost = $_POST["cost"];
    $price = $_POST["price"];

    do {
        if (empty($name) || empty($cost) || empty($price)) {
            $errorMessage = "กรุณากรอกข้อมูลให้ครบ";
            break;
        }

        $name_safe = $connection->real_escape_string($name);

        $checkSql = "SELECT Product_ID FROM productmast WHERE Product_Name = '$name_safe'";
        $checkResult = $connection->query($checkSql);

        if ($checkResult->num_rows > 0) {
            $errorMessage = "มีสินค้านี้อยู่ในระบบแล้ว";
            break;
        }

        $sql = "INSERT INTO productmast 
                (Product_Name, Product_Cost, Product_Price, Product_Stock) 
                VALUES 
                ('$name_safe', '$cost', '$price', 0)";

        if (!$connection->query($sql)) {
            $errorMessage = "เกิดข้อผิดพลาด: " . $connection->error;
            break;
        }

        $name = "";
        $cost = "";
        $price = "";

        $successMessage = "เพิ่มสินค้าสำเร็จ";
        header("location: /work/product.php");
        exit;

    } while (false);
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
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">เพิ่มสินค้า</h4>
                    </div>

                    <div class="card-body">

                        <?php if (!empty($errorMessage)): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong><?= $errorMessage ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">ชื่อสินค้า</label>
                                <input type="text" class="form-control" name="name" value="<?= $name ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ต้นทุน</label>
                                <input type="number" class="form-control" name="cost" value="<?= $cost ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ราคาขาย</label>
                                <input type="number" class="form-control" name="price" value="<?= $price ?>">
                            </div>

                            <?php if (!empty($successMessage)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong><?= $successMessage ?></strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-50">บันทึก</button>
                                <a href="/work/product.php" class="btn btn-outline-danger w-50">ยกเลิก</a>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>