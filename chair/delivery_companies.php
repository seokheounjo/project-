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
    $location = $_POST['location'];
    $estimatedDeliveryTime = $_POST['estimatedDeliveryTime'];

    $addSql = "INSERT INTO DeliveryCompanies (name, contactInfo, location, estimatedDeliveryTime) VALUES ('$name', '$contactInfo', '$location', '$estimatedDeliveryTime')";
    $conn->query($addSql);
}

// 데이터 수정
if (isset($_POST['update'])) {
    $companyID = $_POST['companyID'];
    $name = $_POST['name'];
    $contactInfo = $_POST['contactInfo'];
    $location = $_POST['location'];
    $estimatedDeliveryTime = $_POST['estimatedDeliveryTime'];

    $updateSql = "UPDATE DeliveryCompanies SET name='$name', contactInfo='$contactInfo', location='$location', estimatedDeliveryTime='$estimatedDeliveryTime' WHERE companyID=$companyID";
    $conn->query($updateSql);
}

// 데이터 삭제
if (isset($_POST['delete'])) {
    $companyID = $_POST['companyID'];

    $deleteSql = "DELETE FROM DeliveryCompanies WHERE companyID=$companyID";
    $conn->query($deleteSql);
}

// 배송 회사 데이터 조회
$sql = "SELECT * FROM DeliveryCompanies";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>배송 회사 관리</title>
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


    <!-- 헤더 및 네비게이션 바 -->

    <h2>배송 회사 관리</h2>
    <form action="" method="post">
        ID: <input type="text" name="companyID"><br>
        이름: <input type="text" name="name"><br>
        연락처 정보: <input type="text" name="contactInfo"><br>
        위치: <input type="text" name="location"><br>
        예상 배송 시간: <input type="text" name="estimatedDeliveryTime"><br>
        <input type="submit" name="add" value="추가">
        <input type="submit" name="update" value="수정">
        <input type="submit" name="delete" value="삭제">
    </form>

    <?php
    if ($result->num_rows > 0) {
        echo "<table border='1'><tr><th>ID</th><th>이름</th><th>연락처 정보</th><th>위치</th><th>예상 배송 시간</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["companyID"]. "</td><td>" . $row["name"]. "</td><td>" . $row["contactInfo"]. "</td><td>" . $row["location"]. "</td><td>" . $row["estimatedDeliveryTime"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 results";
    }
    $conn->close();
    ?>
</body>
</html>