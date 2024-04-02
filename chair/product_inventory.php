<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>원자재 회사 관리</title>
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1>의자 제조 회사</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="main.php">홈으로</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <?php
session_start(); // 세션 시작
$host = 'localhost';
$username = 'root';
$password = '1234';
$dbname = 'chairDB';

// 데이터베이스 연결
$conn = new mysqli($host, $username, $password, $dbname);

// 연결 오류 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Suppliers 테이블 조회
$sql = "SELECT * FROM Suppliers";
$result = $conn->query($sql);

// ProductInventory 테이블 조회
$inventorySql = "SELECT * FROM ProductInventory";
$inventoryResult = $conn->query($inventorySql);

// rawmaterialsinventory 테이블 조회
$inventorySql = "SELECT * FROM rawmaterialsinventory";
$rawmaterialsinventory = $conn->query($inventorySql);

// Materials_IncomingAndConsumptionRecords 테이블 조회
$inventorySql = "SELECT * FROM Materials_IncomingAndConsumptionRecords";
$Materials_IncomingAndConsumptionRecords = $conn->query($inventorySql);


// 조회 기능
if (isset($_POST['search'])) {
    $materialID = $_POST['materialID'];
    $searchSql = "SELECT name, balance FROM rawmaterialsinventory WHERE materialID = $materialID";
    $searchResult = $conn->query($searchSql);
    if ($searchResult->num_rows > 0) {
        $searchRow = $searchResult->fetch_assoc();
        $name = $searchRow['name'];
        $balance = $searchRow['balance'];
    }
}

// 주문 기능
if (isset($_POST['order'])) {
    $materialID = $_POST['materialID'];
    $orderQuantity = $_POST['orderQuantity']; // 주문 수량 입력 필드 변경
    // 주문 내역을 세션에 저장
    $_SESSION['orders'][$materialID] = [
        'materialID' => $materialID,
        'name' => $_POST['name'],
        'orderQuantity' => $orderQuantity
    ];
}

// 수령 기능
if (isset($_POST['receive'])) {
    $materialID = $_POST['materialID'];
    if (isset($_SESSION['orders'][$materialID])) {
        $order = $_SESSION['orders'][$materialID];
        $orderQuantity = $order['orderQuantity'];

        // 원자재 단가 조회
        $priceSql = "SELECT price FROM rawmaterialsinventory WHERE materialID = $materialID";
        $priceResult = $conn->query($priceSql);
        if ($priceResult->num_rows > 0) {
            $priceRow = $priceResult->fetch_assoc();
            $unitPrice = $priceRow['price'];

            // 금액 계산
            $cost = $orderQuantity * $unitPrice;

            // 현재 날짜
            $currentDate = date('Y-m-d');

            // Materials_IncomingAndConsumptionRecords에 기록
            $insertSql = "INSERT INTO Materials_IncomingAndConsumptionRecords (materialRefID, incomingDate, incomingQuantity, cost) VALUES ($materialID, '$currentDate', $orderQuantity, $cost)";
            $conn->query($insertSql);

            // rawmaterialsinventory의 잔량 증가
            $updateInventorySql = "UPDATE rawmaterialsinventory SET balance = balance + $orderQuantity WHERE materialID = $materialID";
            $conn->query($updateInventorySql);

            // 세션에서 해당 주문 삭제
            unset($_SESSION['orders'][$materialID]);
        }
    }
    // 주문 내역 초기화
    $_SESSION['orders'] = [];
}
?>

<body>
<h2>제품 재고 관리</h2>
    <form action="" method="post">
        ID: <input type="text" name="materialID" value="<?php echo $materialID ?? ''; ?>"><br>
        원자재 이름: <input type="text" name="name" value="<?php echo $name ?? ''; ?>"><br>
        잔량: <input type="text" name="balance" value="<?php echo $balance ?? ''; ?>"><br>
        주문 수량: <input type="text" name="orderQuantity"><br>
        <input type="submit" name="search" value="조회">
        <input type="submit" name="order" value="주문">
    </form>
        <!-- 주문 내역 출력 -->
<h2>주문 내역</h2>
<!-- 주문 내역 출력 및 수령 버튼 -->
<h2>자재 현황</h2>
<?php if (!empty($_SESSION['orders'])): ?>
    <table border="1">
        <tr><th>ID</th><th>원자재 이름</th><th>주문 수량</th><th>동작</th></tr>
        <?php foreach ($_SESSION['orders'] as $order): ?>
            <tr>
                <td><?php echo $order['materialID']; ?></td>
                <td><?php echo $order['name']; ?></td>
                <td><?php echo $order['orderQuantity']; ?></td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="materialID" value="<?php echo $order['materialID']; ?>">
                        <input type="submit" name="receive" value="수령">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

    <?php
    if ($rawmaterialsinventory->num_rows > 0) {
        echo "<table border='1'><tr><th>ID</th><th>원자재 이름</th><th>들어온 수량</th><th>소비된 수량</th><th>잔량</th><th>가격</th><th>공급업체 참조 ID</th></tr>";
        while($row = $rawmaterialsinventory->fetch_assoc()) {
            echo "<tr><td>" . $row["materialID"]. "</td><td>" . $row["name"]. "</td><td>" . $row["incomingAmount"]. "</td><td>" . $row["consumedAmount"]. "</td><td>" . $row["balance"]. "</td><td>" . $row["price"]. "</td><td>" . $row["supplierRefID"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 rawmaterialsinventory";
    }
    ?>

<?php
    if ($result->num_rows > 0) {
        echo "<table border='1'><tr><th>ID</th><th>이름</th><th>연락처 정보</th><th>주소</th><th>단가</th><th>최소 주문량</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["supplierID"]. "</td><td>" . $row["name"]. "</td><td>" . $row["contactInfo"]. "</td><td>" . $row["address"]. "</td><td>" . $row["unitPrice"]. "</td><td>" . $row["minimumOrderAmount"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 results";
    }
    $conn->close();
    ?>

    
    <?php
    $host = 'localhost';
    $username = 'root';
    $password = '1234';
    $dbname = 'chairDB';
    
    // 데이터베이스 연결
    $conn = new mysqli($host, $username, $password, $dbname);
    
    // 연결 오류 확인
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $perPage = 3; // 한 페이지에 표시할 레코드 수
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; // 현재 페이지 번호
    $offset = ($currentPage - 1) * $perPage; // 계산된 OFFSET 값

    // 전체 레코드 수를 가져오는 쿼리
    $totalQuery = "SELECT COUNT(*) as total FROM Materials_IncomingAndConsumptionRecords";
    $totalResult = $conn->query($totalQuery);

    if ($totalResult === FALSE) {
        echo "Error in totalQuery: " . $conn->error;
    } else {
        $totalRow = $totalResult->fetch_assoc();
        $totalRecords = $totalRow['total'];

        // 페이지당 레코드를 가져오는 쿼리 (예: 역순 정렬)
        $query = "SELECT * FROM Materials_IncomingAndConsumptionRecords ORDER BY recordID DESC LIMIT $perPage OFFSET $offset";
        $result = $conn->query($query);

        if ($result === FALSE) {
            echo "Error in main query: " . $conn->error;
        } else {
            if ($result->num_rows > 0) {
                echo "<table border='1'><tr><th>ID</th><th>원자재 참조 ID</th><th>입고 날짜</th><th>입고된 원자재의 수량</th><th>소모된 원자재의 수량</th><th>원자재의 총 비용(입고시)</th><th>소모 날짜</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["recordID"]. "</td><td>" . $row["materialRefID"]. "</td><td>" . $row["incomingDate"]. "</td><td>" . $row["incomingQuantity"]. "</td><td>" . $row["consumedQuantity"]. "</td><td>" . $row["cost"]. "</td><td>" . $row["consumptionDate"]. "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "0 Materials_IncomingAndConsumptionRecords";
            }

            // 페이징 링크 생성
            $totalPages = ceil($totalRecords / $perPage);
            for ($i = 1; $i <= $totalPages; $i++) {
            echo "<a href='product_inventory.php?page=".$i."'>".$i."</a> ";
            }
        }
    }

    $conn->close();
?>
</body>
</html>