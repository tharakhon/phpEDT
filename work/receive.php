<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_edt";
$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$filter_date = isset($_GET['receive_date']) ? $_GET['receive_date'] : '';
$where_receive = "";
if (!empty($filter_date)) {
    $where_receive = "WHERE DATE(g.Get_Date) = '$filter_date'";
}

$limit_receive = 5;
$page_receive = isset($_GET['page_receive']) ? (int) $_GET['page_receive'] : 1;
$page_receive = max($page_receive, 1);
$offset_receive = ($page_receive - 1) * $limit_receive;

$total_receive = $connection->query("
    SELECT COUNT(*) AS total 
    FROM getmast g
    $where_receive
")->fetch_assoc()['total'];

$total_pages_receive = ceil($total_receive / $limit_receive);
?>
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
            if ($_GET['error'] == 'stock_not_enough') {
                $message = "ลบไม่ได้! จำนวนสินค้าในคลังไม่เพียงพอ";
                $type = "danger";
            } elseif ($_GET['error'] == 'not_found') {
                $message = "ไม่พบข้อมูลเอกสาร";
                $type = "warning";
            } elseif ($_GET['error'] == 'system_error') {
                $message = "เกิดข้อผิดพลาดของระบบ";
                $type = "danger";
            }
        }

        if (isset($_GET['success']) && $_GET['success'] == 'delete_receive') {
            $message = "ลบเอกสารรับสินค้าเรียบร้อยแล้ว";
            $type = "success";
        }
        ?>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <a class="btn btn-primary" href="/work/addreceive.php">รับสินค้าเพิ่ม</a>
        </div>

        <h2>รายการรับสินค้า</h2>
        <form method="get" class="row g-3 mb-3 align-items-end">
            <div class="col-auto">
                <label class="form-label">เลือกวันที่รับสินค้า</label>
                <input type="date" name="receive_date" class="form-control"
                    value="<?= htmlspecialchars($filter_date) ?>">
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-success">ค้นหา</button>
                <a href="receive.php" class="btn btn-secondary">ล้างค่า</a>
            </div>
        </form>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>รหัสรับสินค้า</th>
                    <th>วันที่รับสินค้า</th>
                    <th>ชื่อสินค้า</th>
                    <th>รับจากใคร</th>
                    <th>จำนวน</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT g.*, p.Product_Name
        FROM getmast g
        LEFT JOIN productmast p ON g.Get_Product_ID = p.Product_ID
        $where_receive
        ORDER BY g.Get_ID DESC
        LIMIT $limit_receive OFFSET $offset_receive";

                $result = $connection->query($sql);

                if ($result->num_rows === 0) {
                    echo "
                    <tr>
                        <td colspan='6' class='text-center text-warning'>
                            ไม่พบข้อมูล
                        </td>
                    </tr>";
                } else {
                    while ($row = $result->fetch_assoc()) {
                        echo "
                        <tr>
                            <td>{$row['Get_ID']}</td>
                            <td>{$row['Get_Date']}</td>
                            <td>{$row['Product_Name']}</td>
                            <td>{$row['Get_Name']}</td>
                            <td>{$row['Get_Num']}</td>
                            <td>
                                <a class='btn btn-primary btn-sm' href='editreceive.php?id={$row['Get_ID']}'>Edit</a>
                                <a class='btn btn-danger btn-sm' href='deletereceive.php?id={$row['Get_ID']}'>Delete</a>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </tbody>
        </table>

        <?php
        $range_receive = 2;
        $start_receive = max(1, $page_receive - $range_receive);
        $end_receive = min($total_pages_receive, $page_receive + $range_receive);
        ?>

        <?php if ($total_pages_receive > 1): ?>
            <nav class="d-flex justify-content-center">
                <ul class="pagination">

                    <li class="page-item <?= ($page_receive <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page_receive=<?= $page_receive - 1 ?>&receive_date=<?= $filter_date ?>">
                            &laquo;
                        </a>
                    </li>

                    <?php for ($i = $start_receive; $i <= $end_receive; $i++): ?>
                        <li class="page-item <?= ($i == $page_receive) ? 'active' : '' ?>">
                            <a class="page-link" href="?page_receive=<?= $i ?>&receive_date=<?= $filter_date ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= ($page_receive >= $total_pages_receive) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page_receive=<?= $page_receive + 1 ?>&receive_date=<?= $filter_date ?>">
                            &raquo;
                        </a>
                    </li>

                </ul>
            </nav>
        <?php endif; ?>

    </div>

    <script>
        setTimeout(() => {
            const alertEl = document.querySelector('.alert');
            if (alertEl) {
                const bsAlert = new bootstrap.Alert(alertEl);
                bsAlert.close();
            }
        }, 3500);
    </script>

</body>

</html>