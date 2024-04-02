
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>생산 관리</title>
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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// POST 데이터가 설정되어 있는지 확인합니다.
if(isset($_POST['productID'])) {
    $productID = $_POST['productID'];

    // 의자 재고 테이블에서 필요한 정보를 가져옵니다.
    // 여기에 필요한 필드를 추가하세요 (예: price, materials).
    $stmt = $conn->prepare("SELECT productID, name, stockAmount, price FROM ProductInventory WHERE productID = ?");
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $result = $stmt->get_result();

    // 쿼리 결과가 없으면 오류 메시지를 표시합니다.
    if ($result->num_rows == 0) {
        $message = "존재하지 않는 제품 ID입니다.";
    } else {
        $message = "";
        // 쿼리 결과를 출력합니다.
        $row = $result->fetch_assoc();
        echo "
            <table>
                <tr>
                    <th>제품 ID</th>
                    <td>{$row['productID']}</td>
                </tr>
                <tr>
                    <th>제품 이름</th>
                    <td>{$row['name']}</td>
                </tr>
                <tr>
                <th>재고 수량</th>
                <td>{$row['stockAmount']}</td>
            </tr>
            <tr>
            <th>가격</th>
            <td>{$row['price']}</td>
        </tr>
                            </table>
        ";
    }

    $stmt->close();
}

// 생산 버튼을 누르면 생산 수량, 재생 가능 수량, 폐기 수량을 입력할 수 있는 양식을 제공합니다.
if (isset($_POST['production']) && isset($_POST['quantity'])) {
    $productID = $_POST['productID'];
    $quantity = $_POST['quantity'];
    $renewable = $_POST['renewable'] ?? 0;
    $waste = $_POST['waste'] ?? 0;

    // 재고 수량을 업데이트합니다.
    $stmt = $conn->prepare("UPDATE ProductInventory SET stockAmount = stockAmount + ? WHERE productID = ?");
    $stmt->bind_param("ii", $quantity, $productID);
    $stmt->execute();

    // 생산 일지를 생성합니다.
    $stmt = $conn->prepare("INSERT INTO ChairInventoryLog (chairRefID, transactionType, transactionDate, quantity, note)
                            VALUES (?,  '생산', NOW(), ?, '')");
    $stmt->bind_param("ii", $productID, $quantity);
    $stmt->execute();

    // 재생 가능 수량과 폐기 수량을 업데이트합니다.
    if ($renewable > 0) {
        $stmt = $conn->prepare("UPDATE DefectiveProducts SET quantity = quantity + ? WHERE productRefID = ? AND status = '재생 가능'");
        $stmt->bind_param("ii", $renewable, $productID);
        $stmt->execute();
    }

    if ($waste > 0) {
        $stmt = $conn->prepare("UPDATE DefectiveProducts SET quantity = quantity + ? WHERE productRefID = ? AND status = '폐기'");
        $stmt->bind_param("ii", $waste, $productID);
        $stmt->execute();
    }

    // 생산이 완료되었음을 알립니다.
    echo "생산이 완료되었습니다.";

    $stmt->close();
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
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>
    <h2>의자 재고 조회</h2>
    <!-- 제품 조회 양식 -->
    <form method="post">
        <label for="productID">제품 ID:</label><br>
        <input type="number" id="productID" name="productID" required><br>
        <input type="submit" value="제품 조회">
        
    </form>

    <h2>의자 생산 관리</h2>
    <!-- 생산 관리 양식 -->
    <form method="post">
        <input type="hidden" name="production" value="1">
        <label for="productID">제품 ID:</label><br>
        <input type="number" id="productID" name="productID" required><br>

        <label for="quantity">생산 수량:</label><br>
        <input type="number" id="quantity" name="quantity" required><br>

        <label for="renewable">재생 가능 수량:</label><br>
        <input type="number" id="renewable" name="renewable"><br>

        <label for="waste">폐기 수량:</label><br>
        <input type="number" id="waste" name="waste"><br>

        <input type="submit" value="생산 결과 입력">
    </form>
</body>
</html>