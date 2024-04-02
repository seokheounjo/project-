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
    <h1>Financial Records</h1>
    <?php
$host = 'localhost';
$username = 'root';
$password = '1234';
$dbname = 'chairDB';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


    // FinancialRecords 테이블 데이터 가져오기
    $sql = "SELECT * FROM FinancialRecords";
    $result = $conn->query($sql);

    // 데이터 출력
    if ($result->num_rows > 0) {
        echo '<table>';
        echo '<tr>';
        echo '<th>recordID</th>';
        echo '<th>recordDate</th>';
        echo '<th>recordType</th>';
        echo '<th>transactionCategory</th>';
        echo '<th>description</th>';
        echo '<th>amount</th>';
        echo '<th>paymentMethod</th>';
        echo '<th>transactionStatus</th>';
        echo '</tr>';

        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row["recordID"] . '</td>';
            echo '<td>' . $row["recordDate"] . '</td>';
            echo '<td>' . $row["recordType"] . '</td>';
            echo '<td>' . $row["transactionCategory"] . '</td>';
            echo '<td>' . $row["description"] . '</td>';
            echo '<td>' . $row["amount"] . '</td>';
            echo '<td>' . $row["paymentMethod"] . '</td>';
            echo '<td>' . $row["transactionStatus"] . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo "데이터가 없습니다.";
    }

    // 연결 종료
    $conn->close();
    ?>
</body>
</html>