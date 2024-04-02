<?php
$host = 'localhost';
$username = 'root';
$password = '1234';
$dbname = 'chairDB';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['action']) && isset($_POST['defectiveID'])) {
    $defectiveID = $_POST['defectiveID'];
    $action = $_POST['action'];

    if ($action == 'reproduce') {
        // ProductInventory 테이블에서 해당 제품의 수량을 가져옴
        $getQuantitySql = "SELECT quantity, productRefID FROM DefectiveProducts WHERE defectiveID = $defectiveID";
        $quantityResult = $conn->query($getQuantitySql);

        if ($quantityResult->num_rows > 0) {
            $row = $quantityResult->fetch_assoc();
            $quantity = $row["quantity"];
            $productRefID = $row["productRefID"];

            // ChairInventoryLog에 '재생'으로 로그 추가
            $insertLogSql = "INSERT INTO ChairInventoryLog (chairRefID, transactionType, transactionDate, quantity, note) VALUES ($productRefID, '재생', NOW(), $quantity, '불량 제품 재생 처리')";
            $conn->query($insertLogSql);

            // ProductInventory 테이블에서 수량 추가
            $updateInventorySql = "UPDATE ProductInventory SET stockAmount = stockAmount + $quantity WHERE productID = $productRefID";
            $conn->query($updateInventorySql);
        }
    } elseif ($action == 'discard') {
        // ProductInventory 테이블에서 해당 제품의 수량과 가격을 가져옴
        $getInfoSql = "SELECT quantity, price, productRefID FROM DefectiveProducts dp JOIN ProductInventory pi ON dp.productRefID = pi.productID WHERE defectiveID = $defectiveID";
        $infoResult = $conn->query($getInfoSql);

        if ($infoResult->num_rows > 0) {
            $row = $infoResult->fetch_assoc();
            $quantity = $row["quantity"];
            $pricePerUnit = 10000; // 1개 제품당 만원
            $totalPrice = $quantity * $pricePerUnit;
            $productRefID = $row["productRefID"];

            // FinancialRecords에 '폐기'로 기록
            $insertFinancialSql = "INSERT INTO FinancialRecords (recordDate, recordType, transactionCategory, description, amount, paymentMethod, transactionStatus, productRefID) VALUES (CURDATE(), '지출', '불량 제품 폐기', '불량 제품 폐기 처리', $totalPrice, '현금', '완료', $productRefID)";
            $conn->query($insertFinancialSql);
        }
    }

    // DefectiveProducts에서 제품 삭제
    $deleteSql = "DELETE FROM DefectiveProducts WHERE defectiveID = $defectiveID";
    $conn->query($deleteSql);
}

// 데이터 재조회
$DefectiveResult = $conn->query("SELECT dp.defectiveID, dp.productRefID, dp.quantity, dp.status, pi.name as productName
                                 FROM DefectiveProducts dp
                                 JOIN ProductInventory pi ON dp.productRefID = pi.productID");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>불량 관리</title>
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
    <h1>불량 제품 정보 목록</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>제품 참조 ID</th>
            <th>제품 이름</th>
            <th>수량</th>
            <th>상태</th>
            <th>동작</th>
        </tr>
        <?php
        while ($row = $DefectiveResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row["defectiveID"]."</td>";
            echo "<td>".$row["productRefID"]."</td>";
            echo "<td>".$row["productName"]."</td>";
            echo "<td>".$row["quantity"]."</td>";
            echo "<td>".$row["status"]."</td>";
            echo "<td>";
            if ($row["status"] == '수리 가능') {
                echo "<form method='post'><input type='hidden' name='defectiveID' value='".$row["defectiveID"]."'><input type='submit' name='action' value='reproduce'></form>";
            } elseif ($row["status"] == '폐기') {
                echo "<form method='post'><input type='hidden' name='defectiveID' value='".$row["defectiveID"]."'><input type='submit' name='action' value='discard'></form>";
            }
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>