-- 'chairDB' 데이터베이스를 생성하고 UTF-8 문자 인코딩을 사용합니다.
CREATE DATABASE chairDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 'chairDB' 데이터베이스를 사용합니다.
USE chairDB;

-- Suppliers 테이블 생성: 공급업체의 기본 정보를 저장
CREATE TABLE Suppliers (
    supplierID INT AUTO_INCREMENT PRIMARY KEY, -- 공급업체의 고유 ID
    name VARCHAR(100), -- 공급업체 이름
    contactInfo VARCHAR(100), -- 공급업체 연락처 정보
    address VARCHAR(100), -- 공급업체 주소
    unitPrice DECIMAL(15, 2), -- 제품 단가
    minimumOrderAmount DECIMAL(15, 2) -- 최소 주문 금액
);

-- DeliveryCompanies 테이블 생성: 배송 회사의 기본 정보를 저장
CREATE TABLE DeliveryCompanies (
    companyID INT AUTO_INCREMENT PRIMARY KEY, -- 배송 회사의 고유 ID
    name VARCHAR(100), -- 회사 이름
    contactInfo VARCHAR(100), -- 연락처 정보
    location VARCHAR(100), -- 위치
    estimatedDeliveryTime INT -- 예상 배송 시간
);

-- DeliveryVehicles 테이블 생성: 배송 차량 정보 저장
CREATE TABLE DeliveryVehicles (
    vehicleID INT AUTO_INCREMENT PRIMARY KEY, -- 차량의 고유 ID
    type VARCHAR(50), -- 차량 유형
    status VARCHAR(50), -- 차량 상태
    licensePlate VARCHAR(20), -- 번호판
    yearModel YEAR, -- 모델 연도
    maintenanceInterval INT -- 유지보수 간격
);

-- ProductInventory 테이블 생성: 제품 재고 관리
CREATE TABLE ProductInventory (
    productID INT AUTO_INCREMENT PRIMARY KEY, -- 제품의 고유 ID
    name VARCHAR(50), -- 제품 이름
    stockAmount INT, -- 재고 수량
    dispatchedAmount INT, -- 발송된 수량
    price DECIMAL(15, 2) -- 가격
);

-- EmployeeManagement 테이블 생성: 직원 관리
CREATE TABLE EmployeeManagement (
    employeeID INT AUTO_INCREMENT PRIMARY KEY, -- 직원의 고유 ID
    name VARCHAR(100), -- 이름
    contactNumber VARCHAR(15), -- 연락처 번호
    age INT, -- 나이
    gender CHAR(1), -- 성별
    birthdate DATE, -- 생년월일
    annualWorkDays INT DEFAULT 0, -- 연간 근무일
    monthlyWorkDays INT DEFAULT 0, -- 월간 근무일
    remainingVacationDays INT DEFAULT 20, -- 남은 휴가 일수
    hireDate DATE, -- 입사 날짜
    yearsOfService INT, -- 근무 연수
    position VARCHAR(50), -- 직위
    department VARCHAR(50), -- 부서
    delivery VARCHAR(100), -- 배달여부
    canDrive BOOLEAN DEFAULT FALSE -- 운전 가능 여부
);

-- RawMaterialsInventory 테이블 수정: 원자재 재고 관리
CREATE TABLE RawMaterialsInventory (
    materialID INT AUTO_INCREMENT PRIMARY KEY, -- 원자재의 고유 ID
    name VARCHAR(50), -- 원자재 이름
    incomingAmount INT, -- 들어온 수량
    consumedAmount INT, -- 소비된 수량
    balance INT, -- 잔량
    price DECIMAL(15, 2), -- 가격
    supplierRefID INT, -- 공급업체 참조 ID
    FOREIGN KEY (supplierRefID) REFERENCES Suppliers(supplierID) -- 외래 키 제약 조건
);




-- DefectiveProducts 테이블 생성: 불량 제품 정보 관리
CREATE TABLE DefectiveProducts (
    defectiveID INT AUTO_INCREMENT PRIMARY KEY, -- 불량 제품의 고유 ID
    productRefID INT, -- 제품 참조 ID
    quantity INT, -- 수량
    status VARCHAR(50), -- 상태
    FOREIGN KEY (productRefID) REFERENCES ProductInventory(productID) -- 외래 키 제약 조건
);


-- EmployeeSalary 테이블 생성: 직원 급여 정보 관리
CREATE TABLE EmployeeSalary (
    employeeSalaryID INT AUTO_INCREMENT PRIMARY KEY, -- 급여 기록의 고유 ID
    employeeRefID INT, -- 직원 참조 ID
    baseSalary DECIMAL(15, 2), -- 기본 급여
    bonus DECIMAL(15, 2), -- 보너스
    taxDeduction DECIMAL(15, 2), -- 세금 공제
    totalSalary DECIMAL(15, 2), -- 총 급여
    salaryDate DATE, -- 급여 날짜
    FOREIGN KEY (employeeRefID) REFERENCES EmployeeManagement(employeeID) -- 외래 키 제약 조건
);

-- Materials_IncomingAndConsumptionRecords 테이블 수정: 원자재 입고 및 소모 기록 관리
CREATE TABLE Materials_IncomingAndConsumptionRecords (
    recordID INT AUTO_INCREMENT PRIMARY KEY,  -- 기록의 고유 ID
    materialRefID INT,  -- 원자재 참조 ID
    incomingDate DATE,  -- 입고 날짜
    incomingQuantity INT,  -- 입고된 원자재의 수량
    consumedQuantity INT DEFAULT 0,  -- 소모된 원자재의 수량
    cost DECIMAL(15, 2),  -- 원자재의 총 비용 (입고 시)
    consumptionDate DATE,  -- 소모 날짜
    FOREIGN KEY (materialRefID) REFERENCES RawMaterialsInventory(materialID)
    -- 주석: 이 테이블은 원자재의 입고 및 소모 기록을 관리합니다.
);

-- MaintenanceRecords 테이블: 장비 정비 및 유지 보수 기록 관리
CREATE TABLE Truck_MaintenanceRecords (
    maintenanceRecordID INT AUTO_INCREMENT PRIMARY KEY,
    equipmentRefID INT,
    maintenanceDate DATE,
    description TEXT,
    cost DECIMAL(10, 2),
    FOREIGN KEY (equipmentRefID) REFERENCES DeliveryVehicles(vehicleID)
    -- 주석: MaintenanceRecords 테이블은 장비의 정비 및 유지보수 기록을 관리합니다.
);

-- ChairMaterials 테이블 수정: 의자 제작에 사용되는 재료 비용 관리
CREATE TABLE ChairMaterials (
    chairMaterialID INT AUTO_INCREMENT PRIMARY KEY,
    chairRefID INT,
    materialRefID INT,
    materialName VARCHAR(50),
    quantityRequired INT,
    pricePerUnit DECIMAL(15, 2),
    FOREIGN KEY (chairRefID) REFERENCES ProductInventory(productID),
    FOREIGN KEY (materialRefID) REFERENCES RawMaterialsInventory(materialID)
    -- 주석: ChairMaterials 테이블은 의자 제작에 사용되는 재료의 비용을 관리합니다.
);




-- ChairInventoryLog 테이블 수정: 의자 재고 관리 일지
CREATE TABLE ChairInventoryLog (
    logID INT AUTO_INCREMENT PRIMARY KEY, -- 재고 관리 일지의 고유 ID
    chairRefID INT, -- 참조되는 의자 ID
    transactionType VARCHAR(50), -- 거래 유형 (예: 생산, 납품, 재생, 폐기)
    transactionDate DATETIME, -- 거래 날짜 및 시간
    quantity INT, -- 거래 수량
    note TEXT, -- 거래에 대한 추가적인 노트나 설명
    FOREIGN KEY (chairRefID) REFERENCES ProductInventory(productID) -- 외래 키 제약 조건 (의자 재고)
);

-- DeliveryRecords 테이블 수정: 배송 기록 저장 및 트럭 출발 정보 통합
CREATE TABLE DeliveryRecords (
    recordID INT AUTO_INCREMENT PRIMARY KEY, -- 기록의 고유 ID
    productRefID INT, -- 제품 참조 ID
    companyRefID INT, -- 회사 참조 ID
    quantity INT, -- 수량
    orderDate DATE, -- 주문 날짜
    deliveryDate DATE, -- 배송 날짜
    receivedDate DATE, -- 수령 날짜
    saleAmount DECIMAL(15, 2), -- 판매 금액
    vehicleRefID INT, -- 차량 참조 ID (트럭 출발 정보에서 통합)
    destination VARCHAR(100), -- 목적지 (트럭 출발 정보에서 통합)
    driver1RefID INT, -- 1번 운전사 참조 ID (트럭 출발 정보에서 통합)
    driver2RefID INT, -- 2번 운전사 참조 ID (트럭 출발 정보에서 통합)
    FOREIGN KEY (productRefID) REFERENCES ProductInventory(productID),
    FOREIGN KEY (companyRefID) REFERENCES DeliveryCompanies(companyID),
    FOREIGN KEY (vehicleRefID) REFERENCES DeliveryVehicles(vehicleID),
    FOREIGN KEY (driver1RefID) REFERENCES EmployeeManagement(employeeID),
    FOREIGN KEY (driver2RefID) REFERENCES EmployeeManagement(employeeID)
);

CREATE TABLE FinancialRecords (
    recordID INT AUTO_INCREMENT PRIMARY KEY, -- 금융 기록의 고유 ID
    recordDate DATE, -- 거래 날짜
    recordType VARCHAR(50), -- 거래 유형 (예: 수입, 지출, 급여, 구매 등)
    transactionCategory VARCHAR(50), -- 거래 범주 (예: 원자재, 제품, 급여 등)
    description TEXT, -- 거래 설명
    amount DECIMAL(15, 2), -- 금액
    paymentMethod VARCHAR(50), -- 결제 방식 (예: 현금, 카드, 전자 송금 등)
    transactionStatus VARCHAR(50), -- 거래 상태 (예: 완료, 보류, 취소 등)

    -- 다양한 테이블과의 관계를 나타내는 외래 키
    employeeRefID INT, -- 직원 참조 ID (급여 및 기타 직원 관련 거래)
    vehicleRefID INT, -- 차량 참조 ID (차량 관련 거래)
    productRefID INT, -- 제품 참조 ID (제품 판매 또는 구매 관련 거래)
    materialRefID INT, -- 원자재 참조 ID (원자재 구매 관련 거래)
    orderRefID INT, -- 주문 참조 ID (제품 주문 관련 거래)

    -- 각 외래 키에 대한 외래 키 제약 조건
    FOREIGN KEY (employeeRefID) REFERENCES EmployeeManagement(employeeID),
    FOREIGN KEY (vehicleRefID) REFERENCES DeliveryVehicles(vehicleID),
    FOREIGN KEY (productRefID) REFERENCES ProductInventory(productID),
    FOREIGN KEY (materialRefID) REFERENCES RawMaterialsInventory(materialID),
    FOREIGN KEY (orderRefID) REFERENCES DeliveryRecords(recordID)
    
    -- 주석: FinancialRecords 테이블은 모든 유형의 금융 거래 기록을 관리합니다.
);