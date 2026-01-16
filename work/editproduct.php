<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_edt";

$connection = new mysqli($servername, $username, $password, $database);
$id = "";
$name = "";
$cost = "";
$price = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["id"])) {
        header("location: /work/product.php");
        exit;
    }

    $id = $_GET["id"];

    $sql = "SELECT * FROM productmast WHERE Product_ID=$id";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: /work/product.php");
        exit;
    }

    $id = $row["Product_ID"];
    $name = $row["Product_Name"];
    $cost = $row["Product_Cost"];
    $price = $row["Product_Price"];
} else {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $cost = $_POST["cost"];
    $price = $_POST["price"];

    do {
        if (empty($id) || empty($name) || empty($cost) || empty($price)) {
            $errorMessage = "กรุณากรอกข้อมูลให้ครบ";
            break;
        }

        $sql = "UPDATE productmast  " .
            "SET Product_Name = '$name',Product_Cost = '$cost',Product_Price = '$price'" .
            "WHERE Product_ID = '$id'";
        $result = $connection->query($sql);

        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }

        $name = "";
        $cost = "";
        $price = "";

        $successMessage = "แก้ไขสินค้าสำเร็จ";
        header("location: /work/product.php");
        exit;
    } while (true);
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
    <div class="container my-5 d-flex justify-content-center">
        <div class="card shadow w-75">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">แก้ไขสินค้า</h4>
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
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">ชื่อสินค้า</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $name; ?>">

                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">ต้นทุน</label>
                        <input type="number" class="form-control" name="cost" value="<?php echo $cost; ?>">

                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">ราคาขาย</label>
                        <input type="number" class="form-control" name="price" value="<?php echo $price; ?>">

                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-50">บันทึก</button>
                        <a href="/work/product.php" class="btn btn-outline-danger w-50">ยกเลิก</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>

</html>