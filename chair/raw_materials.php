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

// 데이터 추가
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $contactInfo = $_POST['contactInfo'];
    $address = $_POST['address'];
    $unitPrice = $_POST['unitPrice'];
    $minimumOrderAmount = $_POST['minimumOrderAmount'];

    $addSql = "INSERT INTO Suppliers (name, contactInfo, address, unitPrice, minimumOrderAmount) VALUES ('$name', '$contactInfo', '$address', '$unitPrice', '$minimumOrderAmount')";
    $conn->query($addSql);
}

// 데이터 수정
if (isset($_POST['update'])) {
    $supplierID = $_POST['supplierID'];
    $name = $_POST['name'];
    $contactInfo = $_POST['contactInfo'];
    $address = $_POST['address'];
    $unitPrice = $_POST['unitPrice'];
    $minimumOrderAmount = $_POST['minimumOrderAmount'];

    $updateSql = "UPDATE Suppliers SET name='$name', contactInfo='$contactInfo', address='$address', unitPrice='$unitPrice', minimumOrderAmount='$minimumOrderAmount' WHERE supplierID=$supplierID";
    $conn->query($updateSql);
}

// 데이터 삭제
if (isset($_POST['delete'])) {
    $supplierID = $_POST['supplierID'];

    $deleteSql = "DELETE FROM Suppliers WHERE supplierID=$supplierID";
    $conn->query($deleteSql);
}

// 공급업체 데이터 조회
$sql = "SELECT * FROM Suppliers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>주문 관리</title>
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
<body>
    <h2>공급업체 관리</h2>

    <form action="" method="post">
        ID: <input type="text" name="supplierID"><br>
        이름: <input type="text" name="name"><br>
        연락처 정보: <input type="text" name="contactInfo"><br>
        주소: <input type="text" name="address"><br>
        단가: <input type="text" name="unitPrice"><br>
        최소 주문량: <input type="text" name="minimumOrderAmount"><br>
        <input type="submit" name="add" value="추가">
        <input type="submit" name="update" value="수정">
        <input type="submit" name="delete" value="삭제">
    </form>

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
</body>
</html>