<?php
$host = 'localhost';
$username = 'root';
$password = '1234';
$dbname = 'chairDB';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// 유지보수 기록 추가
if (isset($_POST['addMaintenance'])) {
    $vehicleID = $_POST['vehicleID'];
    $maintenanceDate = $_POST['maintenanceDate'];
    $description = $_POST['description'];
    $cost = $_POST['cost'];

    // 새 유지보수 기록 추가
    $insertSql = "INSERT INTO Truck_MaintenanceRecords (equipmentRefID, maintenanceDate, description, cost) VALUES ($vehicleID, '$maintenanceDate', '$description', $cost)";
    $conn->query($insertSql);
}

// DeliveryVehicles 테이블 데이터 조회
$vehicleSql = "SELECT * FROM DeliveryVehicles";
$vehicleResult = $conn->query($vehicleSql);

// MaintenanceRecords 테이블 데이터 조회 (선택적)
$maintenanceRecordsResult = null;
if (isset($_GET['showMaintenance']) && isset($_GET['vehicleID'])) {
    $vehicleID = $_GET['vehicleID'];
    $maintenanceSql = "SELECT * FROM Truck_MaintenanceRecords WHERE equipmentRefID = $vehicleID";
    $maintenanceRecordsResult = $conn->query($maintenanceSql);
}
// 유지보수 기록 업데이트
if (isset($_POST['updateMaintenance'])) {
    $vehicleID = $_POST['vehicleID'];
    $maintenanceDate = $_POST['maintenanceDate'];
    $description = $_POST['description'];
    $cost = $_POST['cost'];

    // 해당 트럭 ID의 최신 유지보수 기록 ID 가져오기
    $getLatestRecordSql = "SELECT maintenanceRecordID FROM Truck_MaintenanceRecords WHERE equipmentRefID = $vehicleID ORDER BY maintenanceDate DESC LIMIT 1";
    $latestRecordResult = $conn->query($getLatestRecordSql);
    if ($latestRecordResult->num_rows > 0) {
        $latestRecordRow = $latestRecordResult->fetch_assoc();
        $maintenanceRecordID = $latestRecordRow['maintenanceRecordID'];

        // 유지보수 기록 업데이트
        $updateSql = "UPDATE Truck_MaintenanceRecords SET maintenanceDate='$maintenanceDate', description='$description', cost=$cost WHERE maintenanceRecordID=$maintenanceRecordID";
        $conn->query($updateSql);
    }
}

// 유지보수 기록 조회
$maintenanceSql = "SELECT * FROM Truck_MaintenanceRecords";
$maintenanceResult = $conn->query($maintenanceSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>배송 차량 관리</title>
    <!-- 스타일시트 및 기타 메타 태그 -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- 헤더 및 내비게이션 -->
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

    <h2>배송 차량 관리</h2>
    <?php
    if ($vehicleResult->num_rows > 0) {
        echo "<table border='1'><tr><th>ID</th><th>차량 유형</th><th>상태</th><th>번호판</th><th>모델 연도</th><th>유지보수 간격</th><th>유지 보수 기록</th></tr>";
        while ($row = $vehicleResult->fetch_assoc()) {
            echo "<tr><td>".$row["vehicleID"]."</td><td>".$row["type"]."</td><td>".$row["status"]."</td><td>".$row["licensePlate"]."</td><td>".$row["yearModel"]."</td><td>".$row["maintenanceInterval"]."</td>";
            echo "<td><a href='?showMaintenance=true&vehicleID=".$row["vehicleID"]."'>유지 보수 기록</a></td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 차량 정보";
    }

    // 유지 보수 기록 표시
    if ($maintenanceRecordsResult && $maintenanceRecordsResult->num_rows > 0) {
        echo "<h3>유지 보수 기록</h3>";
        echo "<table border='1'><tr><th>ID</th><th>유지 보수 날짜</th><th>설명</th><th>비용</th></tr>";
        while ($record = $maintenanceRecordsResult->fetch_assoc()) {
            echo "<tr><td>".$record["maintenanceRecordID"]."</td><td>".$record["maintenanceDate"]."</td><td>".$record["description"]."</td><td>".$record["cost"]."</td></tr>";
        }
        echo "</table>";
    } elseif (isset($_GET['showMaintenance'])) {
        echo "<p>해당 차량에 대한 유지 보수 기록이 없습니다.</p>";
    }
    ?>
   
<!-- 유지보수 기록 업데이트 폼 -->
<!-- 유지보수 기록 추가 폼 -->
<form action="" method="post">
    트럭 ID: <input type="text" name="vehicleID"><br>
    유지보수 날짜: <input type="date" name="maintenanceDate"><br>
    설명: <textarea name="description"></textarea><br>
    비용: <input type="text" name="cost"><br>
    <input type="submit" name="addMaintenance" value="추가">
</form>

    <?php
    if ($maintenanceResult->num_rows > 0) {
        echo "<table border='1'><tr><th>ID</th><th>유지보수 날짜</th><th>설명</th><th>비용</th><th>차량ID</th></tr>";
        while ($row = $maintenanceResult->fetch_assoc()) {
            echo "<tr><td>".$row["maintenanceRecordID"]."</td><td>".$row["maintenanceDate"]."</td><td>".$row["description"]."</td><td>".$row["cost"]."</td><td>".$row["equipmentRefID"]."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "유지보수 기록이 없습니다.";
    }
    ?>

    <!-- 나머지 HTML 및 스크립트 -->
</body>
</html>