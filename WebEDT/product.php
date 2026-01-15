<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    <div class="container my-5">
        <div class="row">
            <div class="col">
                <a class="btn btn-primary" href="/WebEDT/addproduct.php" role="button">เพิ่มสินค้า</a>
            </div>

            <div class="mt-5">
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th scope="col">รหัสสินค้า</th>
                            <th scope="col">ชื่อสินค้า</th>
                            <th scope="col">ต้นทุนสินค้า</th>
                            <th scope="col">ราคาขาย</th>
                            <th scope="col">จำนวนคงเหลือ</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $database = "db_edt";

                        $connection = new mysqli($servername, $username, $password, $database, 3307);

                        if ($connection->connect_error) {
                            die("Connection failed: " . $connection->connect_error);
                        }

                        $sql = "SELECT * FROM productmast";
                        $result = $connection->query($sql);

                        if (!$result) {
                            die("Invalid query : " . $connection->error);
                        }

                        while ($row = $result->fetch_assoc()) {
                            echo "
                        <tr>
                            <td>$row[Product_ID]</td>
                            <td>$row[Product_Name]</td>
                            <td>$row[Product_Cost]</td>
                            <td>$row[Product_Price]</td>
                            <td>$row[Product_Stock]</td>
                            <td>
                                <a class='btn btn-primary btn-sm' href='/WebEDT/editproduct.php?id=$row[Product_ID]'>Edit</a>
                                <a class='btn btn-danger btn-sm' href='/WebEDT/deleteproduct.php?id=$row[Product_ID]'>Delete</a>
                            </td>
                        </tr>
                        ";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"></script>
</body>

</html>