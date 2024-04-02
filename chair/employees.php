<?php
$host = 'localhost';
$username = 'root';
$password = '1234';
$dbname = 'chairDB';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 직원 정보 조회
$employeeResult = $conn->query("SELECT * FROM EmployeeManagement");

// 출근 버튼 클릭 시
if (isset($_POST['work'])) {
    $employeeID = $_POST['employeeID'];
    
    // 월간 근무일수 증가
    $conn->query("UPDATE EmployeeManagement SET monthlyWorkDays = monthlyWorkDays + 1 WHERE employeeID = '$employeeID'");
    
    // 연간 근무일수 증가
    $conn->query("UPDATE EmployeeManagement SET annualWorkDays = annualWorkDays + 1 WHERE employeeID = '$employeeID'");
    
    // 배달 여부 업데이트
    $canDrive = $conn->query("SELECT canDrive FROM EmployeeManagement WHERE employeeID = '$employeeID'")->fetch_assoc()['canDrive'];
    if ($canDrive) {
        $conn->query("UPDATE EmployeeManagement SET delivery = '운행 가능' WHERE employeeID = '$employeeID'");
    } else {
        $conn->query("UPDATE EmployeeManagement SET delivery = '운행 불가' WHERE employeeID = '$employeeID'");
    }
}
    
    // 연간 근무일수 초기화 (매년 1월 1일에 실행)
    $currentDate = date('Y-m-d');
    $currentMonth = date('m');
    if ($currentMonth == '01' && substr($currentDate, -2) == '01') {
        $conn->query("UPDATE EmployeeManagement SET annualWorkDays = 0 WHERE employeeID = '$employeeID'");
    }
    
    // 월간 근무일수 초기화 (매월 1일에 실행)
    if (substr($currentDate, -2) == '01') {
        $conn->query("UPDATE EmployeeManagement SET monthlyWorkDays = 0 WHERE employeeID = '$employeeID'");
    }


// 휴가 버튼 클릭 시
if (isset($_POST['vacation'])) {
    $employeeID = $_POST['employeeID'];
    $remainingVacationDays = $conn->query("SELECT remainingVacationDays FROM EmployeeManagement WHERE employeeID = '$employeeID'")->fetch_assoc()['remainingVacationDays'];
    
    // 남은 휴가 일수 감소
    if ($remainingVacationDays > 0) {
        $remainingVacationDays--; // 휴가 사용 시 1일 감소
        $conn->query("UPDATE EmployeeManagement SET remainingVacationDays = $remainingVacationDays WHERE employeeID = '$employeeID'");
    }
    
    // 배달 여부 업데이트
    $conn->query("UPDATE EmployeeManagement SET delivery = '휴가' WHERE employeeID = '$employeeID'");
}

// 직원 추가
if (isset($_POST['addEmployee'])) {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $contactNumber = isset($_POST['contactNumber']) ? $_POST['contactNumber'] : '';
    $age = isset($_POST['age']) ? $_POST['age'] : 0;
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $birthdate = isset($_POST['birthdate']) ? $_POST['birthdate'] : '';
    $hireDate = isset($_POST['hireDate']) ? $_POST['hireDate'] : '';
    $yearsOfService = isset($_POST['yearsOfService']) ? $_POST['yearsOfService'] : 0;
    $position = isset($_POST['position']) ? $_POST['position'] : '';
    $department = isset($_POST['department']) ? $_POST['department'] : '';
    $delivery = (isset($_POST['delivery']) && $_POST['delivery'] == '운행 가능') ? '운행 가능' : '운행 불가';
    $canDrive = (isset($_POST['canDrive']) && $_POST['canDrive'] == 'Yes') ? 1 : 0;
    
    // 한 번에 하나의 쿼리를 실행
    $sql = "INSERT INTO EmployeeManagement (name, contactNumber, age, gender, birthdate, annualWorkDays, monthlyWorkDays, remainingVacationDays, hireDate, yearsOfService, position, department, delivery, canDrive) VALUES ('$name', '$contactNumber', $age, '$gender', '$birthdate', 0, 0, 0, '$hireDate', $yearsOfService, '$position', '$department', '$delivery', $canDrive)";
    
    if ($conn->query($sql) === TRUE) {
        echo "새 직원 추가 성공!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
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
    <h1>직원 정보 목록</h1>
    <table border="1">
        <!-- 테이블 헤더 -->
        <tr>
            <th>ID</th>
            <th>이름</th>
            <th>연락처 번호</th>
            <th>나이</th>
            <th>성별</th>
            <th>생년월일</th>
            <th>연간 근무일</th>
            <th>월간 근무일</th>
            <th>남은 휴가 일수</th>
            <th>입사 날짜</th>
            <th>근무 연수</th>
            <th>직위</th>
            <th>부서</th>
            <th>배달 여부</th>
            <th>운전 가능 여부</th>
            <th>출근</th>
            <th>휴가</th>
        </tr>

        <!-- 직원 정보 표시 -->
        <?php
        while ($row = $employeeResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row["employeeID"]."</td>";
            echo "<td>".$row["name"]."</td>";
            echo "<td>".$row["contactNumber"]."</td>";
            echo "<td>".$row["age"]."</td>";
            echo "<td>".$row["gender"]."</td>";
            echo "<td>".$row["birthdate"]."</td>";
            echo "<td>".$row["annualWorkDays"]."</td>";
            echo "<td>".$row["monthlyWorkDays"]."</td>";
            echo "<td>".$row["remainingVacationDays"]."</td>";
            echo "<td>".$row["hireDate"]."</td>";
            echo "<td>".$row["yearsOfService"]."</td>";
            echo "<td>".$row["position"]."</td>";
            echo "<td>".$row["department"]."</td>";
            echo "<td>".$row["delivery"]."</td>";
            echo "<td>".($row["canDrive"] ? "운행 가능" : "운행 불가")."</td>";
            echo '<td><form method="POST"><input type="hidden" name="employeeID" value="'.$row["employeeID"].'"><input type="hidden" name="remainingVacationDays" value="'.$row["remainingVacationDays"].'"><input type="submit" name="work" value="출근"></form></td>';
            echo '<td><form method="POST"><input type="hidden" name="employeeID" value="'.$row["employeeID"].'"><input type="submit" name="vacation" value="휴가"></form></td>';
            echo "</tr>";
        }
        ?>
    </table>

    <!-- 직원 추가 양식 -->
    <h2>새 직원 추가</h2>
    <form method="POST">
        <label for="name">이름:</label>
        <input type="text" name="name" required><br>
        <label for="contactNumber">연락처 번호:</label>
        <input type="text" name="contactNumber" required><br>
        <label for="age">나이:</label>
        <input type="number" name="age" required><br>
        <label for="gender">성별:</label>
        <input type="text" name="gender" required><br>
        <label for="birthdate">생년월일:</label>
        <input type="date" name="birthdate" required><br>
        <label for="hireDate">입사 날짜:</label>
        <input type="date" name="hireDate" required><br>
        <label for="position">직위:</label>
        <input type="text" name="position" required><br>
        <label for="department">부서:</label>
        <input type="text" name="department" required><br>
       <label for="canDrive">운전 가능 여부:</label>
        <input type="radio" name="canDrive" value="Yes" required> Yes
        <input type="radio" name="canDrive" value="No" required> No<br>
        <input type="submit" name="addEmployee" value="직원 추가">
    </form>
</body>
</html>