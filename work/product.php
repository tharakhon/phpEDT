<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <title>Document</title>
</head>

<body>
    <div class="container my-5">

        <?php
        $message = "";
        $type = "";

        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'product_used') {
                $message = "❌ ลบสินค้าไม่ได้ เนื่องจากมีประวัติรับหรือขายแล้ว";
                $type = "warning";
            } elseif ($_GET['error'] == 'system_error') {
                $message = "❌ เกิดข้อผิดพลาดของระบบ";
                $type = "danger";
            }
        }

        if (isset($_GET['success'])) {
            if ($_GET['success'] == 'delete_product') {
                $message = "✅ ลบสินค้าเรียบร้อยแล้ว";
                $type = "success";
            }
        }
        ?>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $type ?> alert-dismissible fade show">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-3">
            <div class="col">
                <a class="btn btn-primary" href="/work/addproduct.php">เพิ่มสินค้า</a>
            </div>
        </div>

        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "db_edt";

        $connection = new mysqli($servername, $username, $password, $database);
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        $limit = 5;
        $page = isset($_GET['page']) ? max((int) $_GET['page'], 1) : 1;
        $offset = ($page - 1) * $limit;

        $total = $connection->query("SELECT COUNT(*) AS total FROM productmast")
            ->fetch_assoc()['total'];
        $total_pages = ceil($total / $limit);

        $sql = "SELECT * FROM productmast
        ORDER BY Product_ID DESC
        LIMIT $limit OFFSET $offset";
        $result = $connection->query($sql);
        ?>

        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>รหัสสินค้า</th>
                    <th>ชื่อสินค้า</th>
                    <th>ต้นทุนสินค้า</th>
                    <th>ราคาขาย</th>
                    <th>จำนวนคงเหลือ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['Product_ID'] ?></td>
                        <td><?= $row['Product_Name'] ?></td>
                        <td><?= $row['Product_Cost'] ?></td>
                        <td><?= $row['Product_Price'] ?></td>
                        <td><?= $row['Product_Stock'] ?></td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="editproduct.php?id=<?= $row['Product_ID'] ?>">Edit</a>
                            <a class="btn btn-danger btn-sm" href="deleteproduct.php?id=<?= $row['Product_ID'] ?>"
                                onclick="return confirm('ยืนยันการลบสินค้า?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php
        $range = 2;
        $start = max(1, $page - $range);
        $end = min($total_pages, $page + $range);
        ?>

        <nav class="d-flex justify-content-center">
            <ul class="pagination">

                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>
               <?= isset($_GET['success']) ? '&success=' . $_GET['success'] : '' ?>
               <?= isset($_GET['error']) ? '&error=' . $_GET['error'] : '' ?>">
                        &laquo;
                    </a>
                </li>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>
                   <?= isset($_GET['success']) ? '&success=' . $_GET['success'] : '' ?>
                   <?= isset($_GET['error']) ? '&error=' . $_GET['error'] : '' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>
               <?= isset($_GET['success']) ? '&success=' . $_GET['success'] : '' ?>
               <?= isset($_GET['error']) ? '&error=' . $_GET['error'] : '' ?>">
                        &raquo;
                    </a>
                </li>

            </ul>
        </nav>


    </div>

    <script>
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) new bootstrap.Alert(alert).close();
        }, 3500);
    </script>

</body>

</html>