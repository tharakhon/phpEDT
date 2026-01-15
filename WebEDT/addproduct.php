<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_edt";

$connection = new mysqli($servername, $username, $password, $database, 3307);

$name = "";
$cost = "";
$price = "";

$errorMessage = "";
$successMessage = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST["name"];
    $cost = $_POST["cost"];
    $price = $_POST["price"];

    do {
        if (empty($name) || empty($cost) || empty($price)) {
            $errorMessage = "กรุณากรอกข้อมูลให้ครบ";
            break;
        }

        $sql = "INSERT INTO productmast (Product_Name, Product_Cost, Product_Price, Product_Stock) " .
            "VALUES ('$name','$cost','$price','0')";
        $result = $connection->query($sql);

        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }

        $name = "";
        $cost = "";
        $price = "";

        $successMessage = "เพิ่มสินค้าสำเร็จ";
        header("location: /WebEDT/product.php");
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
        <h2>เพิ่มสินค้า</h2>

        <?php
        if (!empty($errorMessage)) {
            echo "
                <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                    <strong>$errorMessage</strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></buttoon>
                </div>
                ";
        }
        ?>
        <form method="post">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ชื่อสินค้า</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="name" value="<?php echo $name; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ต้นทุน</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="cost" value="<?php echo $cost; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ราคาขาย</label>
                <div class="col-sm-6">
                    <input type="" class="form-control" name="price" value="<?php echo $price; ?>">
                </div>
            </div>

            <?php
            if (!empty($successMessage)) {
                echo "
                <div class='row mb-3'>
                    <div class='offset-sm-3 col-sm-6'>
                        <div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <strong>$successMessage</strong>
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>
                    </div>
                </div>
                ";
            }
            ?>
            <div class="row mb-3 ">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="/WebEDT/product.php" role="button">ยกเลิก</a>
                </div>
            </div>
        </form>
    </div>

</body>

</html>