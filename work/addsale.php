<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_edt";

$connection = new mysqli($servername, $username, $password, $database);

$name = "";
$num = "";
$product_id = "";
$price = "";

$errorMessage = "";
$successMessage = "";

$products = [];
$sql = "SELECT Product_ID, Product_Name FROM productmast";
$result = $connection->query($sql);
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $num = $_POST['num'];
    $price = $_POST['price'];

    if (empty($product_id) || empty($num) || empty($name) || empty($price)) {
        $errorMessage = "กรุณากรอกข้อมูลให้ครบ";
    } else {

        $product_id = (int) $product_id;
        $num = (int) $num;
        $price = (float) $price;
        $name = $connection->real_escape_string($name);

        $connection->begin_transaction();

        try {
            $sql = "UPDATE productmast
                    SET Product_Stock = Product_Stock - $num
                    WHERE Product_ID = $product_id
                      AND Product_Stock >= $num";
            $connection->query($sql);

            if ($connection->affected_rows == 0) {
                throw new Exception("สินค้าใน stock ไม่เพียงพอ");
            }

            $sql = "INSERT INTO salemast 
                    (Sale_Product_ID, Sale_Num, Sale_Name, Sale_Date, Sale_Price)
                    VALUES 
                    ($product_id, $num, '$name', NOW(), $price * $num)";
            $connection->query($sql);

            $name = "";
            $product_id = "";
            $num = "";
            $price = "";

            $connection->commit();
            $successMessage = "ขายสินค้าสำเร็จ";

        } catch (Exception $e) {
            $connection->rollback();
            $errorMessage = $e->getMessage();
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <title>Document</title>
</head>
<style>
    .select2-container--default .select2-results__option {
        background-color: #fff;
        color: #212529;
    }

    .select2-results__option--highlighted {
        background-color: #0d6efd !important;
        color: #fff;
    }

    .select2-dropdown {
        border: 1px solid #ced4da;
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        outline: none !important;
        box-shadow: none !important;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, .25);
    }
</style>

<body>
    <div class="container my-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">ขายสินค้า</h5>
            </div>

            <div class="card-body">

                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong><?= $errorMessage ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="post">

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">ชื่อสินค้า</label>
                        <div class="col-sm-6">
                            <select class="form-select" id="product" name="product_id">
                                <option value="">-- เลือกสินค้า --</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['Product_ID']; ?>">
                                        <?= $p['Product_Name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">จำนวนที่ขาย</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="num" value="<?= $num ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">ขายให้ใคร</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="name" value="<?= $name ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">ราคา</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="price" value="<?= $price ?>">
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
    <script>
        $(document).ready(function () {
            $('#product').select2({
                placeholder: "พิมพ์ค้นหาสินค้า",
                width: '100%'
            });
        });
    </script>

</body>

</html>