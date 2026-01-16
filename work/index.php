<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_edt";
$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
  die("Connection failed: " . $connection->connect_error);
}
$filter_type = isset($_GET['filter']) ? $_GET['filter'] : '';
if ($filter_type === 'best' || $filter_type === 'never') {
  $filter_date = '';
  $where_sale = '';
}

$filter_date = isset($_GET['sale_date']) ? $_GET['sale_date'] : '';
$where_sale = "";

if (!empty($filter_date)) {
  $safe_date = $connection->real_escape_string($filter_date);
  $where_sale = "WHERE DATE(s.Sale_Date) = '$safe_date'";
}

$limit_sale = 5;
$page_sale = max(1, (int) ($_GET['page_sale'] ?? 1));
$offset_sale = ($page_sale - 1) * $limit_sale;
$limit_sale = (int) $limit_sale;
$offset_sale = (int) $offset_sale;


if ($filter_type === 'best') {

  $sql_count = "
    SELECT COUNT(*) AS total
    FROM (
    SELECT s.Sale_Product_ID
    FROM salemast s
    GROUP BY s.Sale_Product_ID
    ) t
  ";


} elseif ($filter_type === 'never') {

  $sql_count = "
    SELECT COUNT(*) AS total
    FROM productmast p
    LEFT JOIN salemast s ON p.Product_ID = s.Sale_Product_ID
    WHERE s.Sale_ID IS NULL
  ";

} else {

  $sql_count = "
    SELECT COUNT(*) AS total
    FROM salemast s
    $where_sale
  ";

}

$total_sale = $connection->query($sql_count)->fetch_assoc()['total'];
$total_pages_sale = ceil($total_sale / $limit_sale);

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

      } elseif ($_GET['error'] == 'delete_sale_fail') {
        $message = "ลบเอกสารขายไม่สำเร็จ";
        $type = "danger";
      }
    }

    if (isset($_GET['success'])) {
      if ($_GET['success'] == 'delete_sale') {
        $message = "ลบเอกสารขายสินค้าเรียบร้อยแล้ว";
        $type = "success";
      }
    }
    ?>

    <?php if (!empty($message)): ?>
      <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="mb-4">
      <a class="btn btn-primary" href="/work/product.php">ข้อมูลสินค้า</a>
      <a class="btn btn-primary" href="/work/receive.php">รับสินค้า</a>
      <a class="btn btn-primary" href="/work/addsale.php">ขายสินค้า</a>
    </div>
    <h2 class="mt-5">รายการขายสินค้า</h2>
    <?php
    $filter_date = isset($_GET['sale_date']) ? $_GET['sale_date'] : '';
    ?>

    <form method="get" class="row g-3 mb-3 align-items-end">

      <div class="col-auto">
        <label class="form-label">เลือกวันที่ขาย</label>
        <input type="date" name="sale_date" class="form-control" value="<?= htmlspecialchars($filter_date) ?>">
      </div>

      <div class="col-auto">
        <button type="submit" class="btn btn-success">ค้นหา</button>
        <a href="index.php" class="btn btn-secondary">ล้างค่า</a>
      </div>
      <div class="mb-3">
        <a href="index.php?filter=best" class="btn btn-warning">
          สินค้าขายดีที่สุด
        </a>
        <a href="index.php?filter=never" class="btn btn-secondary">
          สินค้าที่ไม่เคยขาย
        </a>
        <a href="index.php" class="btn btn-outline-dark">
          แสดงทั้งหมด
        </a>
      </div>

    </form>

    <table class="table table-dark table-striped">
      <thead>
        <tr>
          <?php if ($filter_type === 'best'): ?>
            <th>รหัสสินค้า</th>
            <th>-</th>
            <th>ชื่อสินค้า</th>
            <th>-</th>
            <th>ยอดขาย (จำนวน)</th>
            <th>ยอดเงินรวม</th>
            <th></th>

          <?php elseif ($filter_type === 'never'): ?>
            <th>รหัสสินค้า</th>
            <th>-</th>
            <th>ชื่อสินค้า</th>
            <th colspan="4">สถานะ</th>

          <?php else: ?>
            <th>รหัสขายสินค้า</th>
            <th>วันที่ขายสินค้า</th>
            <th>ชื่อสินค้า</th>
            <th>ขายให้ใคร</th>
            <th>จำนวน</th>
            <th>ราคา</th>
            <th></th>
          <?php endif; ?>
        </tr>
      </thead>

      <tbody>
        <?php
        if ($filter_type === 'best') {

          $sql = "
            SELECT 
              p.Product_ID,
              p.Product_Name,
              SUM(s.Sale_Num) AS total_qty,
              SUM(s.Sale_Price) AS total_price
            FROM productmast p
            JOIN salemast s ON p.Product_ID = s.Sale_Product_ID
            GROUP BY p.Product_ID, p.Product_Name
            ORDER BY total_qty DESC
            LIMIT $limit_sale OFFSET $offset_sale
          ";

        } elseif ($filter_type === 'never') {

          $sql = "
            SELECT 
              p.Product_ID,
              p.Product_Name
            FROM productmast p
            LEFT JOIN salemast s ON p.Product_ID = s.Sale_Product_ID
            WHERE s.Sale_ID IS NULL
            LIMIT $limit_sale OFFSET $offset_sale
          ";

        } else {

          $sql = "
            SELECT s.*, p.Product_Name
            FROM salemast s
            LEFT JOIN productmast p ON s.Sale_Product_ID = p.Product_ID
            $where_sale
            ORDER BY s.Sale_ID DESC
            LIMIT $limit_sale OFFSET $offset_sale
          ";

        }


        $result = $connection->query($sql);

        if ($result->num_rows === 0) {

          echo "
            <tr>
              <td colspan='7' class='text-center text-warning'>
                ไม่พบข้อมูล
              </td>
            </tr>";

        } else {

          while ($row = $result->fetch_assoc()) {

            if ($filter_type === 'best') {

              echo "
                <tr>
                  <td>{$row['Product_ID']}</td>
                  <td>-</td>
                  <td>{$row['Product_Name']}</td>
                  <td>-</td>
                  <td>{$row['total_qty']}</td>
                  <td>{$row['total_price']}</td>
                  <td></td>
                </tr>";

            } elseif ($filter_type === 'never') {

              echo "
                <tr>
                  <td>{$row['Product_ID']}</td>
                  <td>-</td>
                  <td>{$row['Product_Name']}</td>
                  <td colspan='4' class='text-center text-danger'>
                    ยังไม่เคยถูกขาย
                  </td>
                </tr>";

            } else {
              echo "
                <tr>
                  <td>{$row['Sale_ID']}</td>
                  <td>{$row['Sale_Date']}</td>
                  <td>{$row['Product_Name']}</td>
                  <td>{$row['Sale_Name']}</td>
                  <td>{$row['Sale_Num']}</td>
                  <td>{$row['Sale_Price']}</td>
                  <td>
                    <a class='btn btn-primary btn-sm' href='editsale.php?id={$row['Sale_ID']}'>Edit</a>
                    <a class='btn btn-danger btn-sm' href='deletesale.php?id={$row['Sale_ID']}'>Delete</a>
                  </td>
                </tr>";

            }
          }
        }
        ?>
      </tbody>
    </table>
    <?php
    $range_sale = 2;
    $start_sale = max(1, $page_sale - $range_sale);
    $end_sale = min($total_pages_sale, $page_sale + $range_sale);
    ?>

    <?php if ($total_pages_sale > 1): ?>
      <nav class="d-flex justify-content-center">
        <ul class="pagination">

          <li class="page-item <?= ($page_sale <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page_sale=<?= $page_sale - 1 ?>
              <?= !empty($filter_date) ? '&sale_date=' . urlencode($filter_date) : '' ?>
              <?= !empty($filter_type) ? '&filter=' . $filter_type : '' ?>">
              &laquo;
            </a>

          </li>

          <?php for ($i = $start_sale; $i <= $end_sale; $i++): ?>
            <li class="page-item <?= ($i == $page_sale) ? 'active' : '' ?>">
              <a class="page-link" href="?page_sale=<?= $i ?>
                <?= !empty($filter_date) ? '&sale_date=' . urlencode($filter_date) : '' ?>
                <?= !empty($filter_type) ? '&filter=' . $filter_type : '' ?>">
                <?= $i ?>
              </a>

            </li>
          <?php endfor; ?>

          <li class="page-item <?= ($page_sale >= $total_pages_sale) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page_sale=<?= $page_sale + 1 ?>
                <?= !empty($filter_date) ? '&sale_date=' . urlencode($filter_date) : '' ?>
                <?= !empty($filter_type) ? '&filter=' . $filter_type : '' ?>">
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