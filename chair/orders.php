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

// 발송 시작 처리
if (isset($_POST['startDispatch'])) {
    $productRefID = $_POST['productRefID'];
    $companyRefID = $_POST['companyRefID'];
    $quantity = $_POST['quantity'];
    $orderDate = $_POST['orderDate'];
    $deliveryDate = $_POST['deliveryDate'];
    $saleAmount = $_POST['saleAmount'];
    $vehicleRefID = $_POST['vehicleRefID'];
    $destination = $_POST['destination'];
    $driver1RefID = $_POST['driver1RefID'];
    $driver2RefID = $_POST['driver2RefID'];

    $insertSql = "INSERT INTO DeliveryRecords (productRefID, companyRefID, quantity, orderDate, deliveryDate, saleAmount, vehicleRefID, destination, driver1RefID, driver2RefID) VALUES ('$productRefID', '$companyRefID', '$quantity', '$orderDate', '$deliveryDate', '$saleAmount', '$vehicleRefID', '$destination', '$driver1RefID', '$driver2RefID')";
    $conn->query($insertSql);
}

// 배송 완료 처리
if (isset($_POST['completeDelivery'])) {
    $recordID = $_POST['recordID'];

    $updateSql = "UPDATE DeliveryRecords SET receivedDate = NOW() WHERE recordID = $recordID";
    $conn->query($updateSql);
}

// 주문 데이터 조회
$sql = "SELECT * FROM DeliveryRecords";
$result = $conn->query($sql);

// 추가 테이블 조회
$employeesSql = "SELECT employeeID, name, delivery FROM EmployeeManagement";
$employeesResult = $conn->query($employeesSql);

// DeliveryCompanies 테이블 조회
$companiesSql = "SELECT * FROM DeliveryCompanies";
$companiesResult = $conn->query($companiesSql);

// DeliveryVehicles 테이블 조회
$vehiclesSql = "SELECT * FROM DeliveryVehicles";
$vehiclesResult = $conn->query($vehiclesSql);

// ProductInventory 테이블 조회
$inventorySql = "SELECT * FROM ProductInventory";
$inventoryResult = $conn->query($inventorySql);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <title>주문 관리</title>
</head>
<body>
    <h2>주문 관리</h2>

    <!-- 발송 시작 폼 -->
    <form action="" method="post">
        제품 ID: <input type="text" name="productRefID">
        회사 ID: <input type="text" name="companyRefID">
        수량: <input type="text" name="quantity">
        주문 날짜: <input type="date" name="orderDate">
        배송 날짜: <input type="date" name="deliveryDate">
        판매 금액: <input type="text" name="saleAmount">
        차량 참조 ID: <input type="text" name="vehicleRefID">
        목적지: <input type="text" name="destination">
        운전사 1 ID: <input type="text" name="driver1RefID">
        운전사 2 ID: <input type="text" name="driver2RefID">
        <input type="submit" name="startDispatch" value="발송 시작">
    </form>

    <?php
    if ($result->num_rows > 0) {
        echo "<table border='1'><tr><th>기록 ID</th><th>제품 ID</th><th>회사 ID</th><th>수량</th><th>주문 날짜</th><th>배송 날짜</th><th>수령 날짜</th><th>판매 금액</th><th>차량 참조 ID</th><th>목적지</th><th>운전사 1 ID</th><th>운전사 2 ID</th><th>배송 완료</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["recordID"]. "</td><td>" . $row["productRefID"]. "</td><td>" . $row["companyRefID"]. "</td><td>" . $row["quantity"]. "</td><td>" . $row["orderDate"]. "</td><td>" . $row["deliveryDate"]. "</td><td>" . $row["receivedDate"]. "</td><td>" . $row["saleAmount"]. "</td><td>" . $row["vehicleRefID"]. "</td><td>" . $row["destination"]. "</td><td>" . $row["driver1RefID"]. "</td><td>" . $row["driver2RefID"]. "</td>";
            echo "<td><form action='' method='post'><input type='hidden' name='recordID' value='" . $row["recordID"] . "'><input type='submit' name='completeDelivery' value='배송 완료'></form></td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 results";
    }
    $conn->close();
    ?>
     <h3>사원 정보</h3>
    <?php if ($employeesResult->num_rows > 0): ?>
        <table border='1'>
            <tr>
                <th>사원 ID</th>
                <th>이름</th>
                <th>배달 여부</th>
            </tr>
            <?php while($row = $employeesResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["employeeID"]; ?></td>
                    <td><?php echo $row["name"]; ?></td>
                    <td><?php echo $row["delivery"]; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>사원 정보가 없습니다.</p>
    <?php endif; ?>
        <!-- 납품업체 데이터 표시 -->
        <h3>납품업체 데이터</h3>
    <?php
    if ($companiesResult->num_rows > 0) {
        echo "<table border='1'><tr><th>ID</th><th>이름</th><th>연락처</th><th>위치</th><th>예상 배송 시간</th></tr>";
        while($row = $companiesResult->fetch_assoc()) {
            echo "<tr><td>" . $row["companyID"]. "</td><td>" . $row["name"]. "</td><td>" . $row["contactInfo"]. "</td><td>" . $row["location"]. "</td><td>" . $row["estimatedDeliveryTime"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "납품업체 정보가 없습니다.";
    }
    ?>

    <!-- 배송 차량 데이터 표시 -->
    <h3>배송 차량 데이터</h3>
    <?php
    if ($vehiclesResult->num_rows > 0) {
        echo "<table border='1'><tr><th>ID</th><th>유형</th><th>상태</th><th>번호판</th><th>모델 연도</th><th>유지보수 간격</th></tr>";
        while($row = $vehiclesResult->fetch_assoc()) {
            echo "<tr><td>" . $row["vehicleID"]. "</td><td>" . $row["type"]. "</td><td>" . $row["status"]. "</td><td>" . $row["licensePlate"]. "</td><td>" . $row["yearModel"]. "</td><td>" . $row["maintenanceInterval"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "배송 차량 정보가 없습니다.";
    }
    ?>

    <!-- 제품 재고 데이터 표시 -->
    <h3>제품 재고 데이터</h3>
    <?php
    if ($inventoryResult->num_rows > 0) {
        echo "<table border='1'><tr><th>ID</th><th>이름</th><th>재고 수량</th><th>발송된 수량</th><th>가격</th></tr>";
        while($row = $inventoryResult->fetch_assoc()) {
            echo "<tr><td>" . $row["productID"]. "</td><td>" . $row["name"]. "</td><td>" . $row["stockAmount"]. "</td><td>" . $row["dispatchedAmount"]. "</td><td>" . $row["price"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "제품 재고 정보가 없습니다.";
    }
    ?>
</body>
</html>
