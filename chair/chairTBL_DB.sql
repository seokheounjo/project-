
-- 공급업체 데이터 추가
INSERT INTO Suppliers (name, contactInfo, address, unitPrice, minimumOrderAmount) VALUES
('우드리버라인 주식회사', '123-456-7890', '서울특별시 강남구', 14000, 500),
('메탈웍스', '234-567-8901', '경기도 수원시', 8, 1000),
('AdhesiveSolutions LLC.', '010-9012-4567', '대구광역시 글루군', 2000, 150000),
('컬러풀 코팅', '345-678-9012', '인천광역시', 22000, 200);

-- 납품업체 데이터 추가
INSERT INTO DeliveryCompanies (name, contactInfo, location, estimatedDeliveryTime) VALUES
('송익 가구', '456-789-0123', '서울특별시', 240),
('삼익 가구', '567-890-1234', '부산광역시', 480),
('나무 가구', '678-901-2345', '대구광역시', 360),
('한빛 가구', '789-012-3456', '충청북도', 272),
('가구 도매', '890-123-4567', '경상남도', 224),
('가구 나라', '901-234-5678', '전라남도', 296);

-- 배송 차량 데이터 추가
INSERT INTO DeliveryVehicles (type, status, licensePlate, yearModel, maintenanceInterval) VALUES
('5톤 트럭', '사용 가능', '서울5가 1234', 2018, 6),
('5톤 트럭', '사용 가능', '서울5나 5678', 2019, 6),
('1톤 트럭', '사용 가능', '서울1가 9012', 2020, 6),
('1톤 트럭', '사용 가능', '서울1나 3456', 2021, 6),
('1톤 트럭', '사용 가능', '서울1다 7890', 2022, 6),
('5톤 트럭', '사용 가능', '인천5바 2345', 2021, 6);

-- 사장 1명 (내부)
INSERT INTO EmployeeManagement 
    (name, contactNumber, age, gender, birthdate, hireDate, yearsOfService, position, department, canDrive) 
VALUES
    ('Kim 사장', '010-1000-0001', 50, 'M', '1973-01-01', '2000-01-01', 23, '사장', '내부', FALSE);

-- 부장 1명 (현장)
INSERT INTO EmployeeManagement 
    (name, contactNumber, age, gender, birthdate, hireDate, yearsOfService, position, department, canDrive) 
VALUES
    ('Lee 현장부장', '010-2000-0002', 45, 'F', '1978-02-02', '2005-02-02', 18, '부장', '현장', TRUE);

-- 과장 2명 (현장)
INSERT INTO EmployeeManagement 
    (name, contactNumber, age, gender, birthdate, hireDate, yearsOfService, position, department, canDrive) 
VALUES
    ('Choi 과장1', '010-3000-0004', 40, 'M', '1983-04-04', '2010-04-04', 13, '과장', '현장', TRUE),
    ('Kang 과장2', '010-3000-0005', 38, 'F', '1985-05-05', '2012-05-05', 11, '과장', '현장', TRUE);

-- 대리 6명 (현장 4, 내부 2)
-- 현장 대리
INSERT INTO EmployeeManagement 
    (name, contactNumber, age, gender, birthdate, hireDate, yearsOfService, position, department, canDrive) 
VALUES
    ('Jang 대리1', '010-4000-0007', 32, 'F', '1991-07-07', '2018-07-07', 5, '대리', '현장', TRUE),
    ('Kim 대리2', '010-4000-0008', 30, 'M', '1993-08-08', '2019-08-08', 4, '대리', '현장', TRUE),
    ('Lee 대리3', '010-4000-0009', 28, 'F', '1995-09-09', '2020-09-09', 3, '대리', '현장', TRUE),
    ('Seo 대리4', '010-4000-0010', 26, 'M', '1997-10-10', '2021-10-10', 2, '대리', '현장', TRUE),
-- 내부 대리
    ('Han 대리5', '010-4000-0011', 33, 'F', '1990-11-11', '2017-11-11', 6, '대리', '내부', FALSE);

-- 사원 8명 (현장 6, 내부 2)
-- 현장 사원
INSERT INTO EmployeeManagement 
    (name, contactNumber, age, gender, birthdate, hireDate, yearsOfService, position, department, canDrive) 
VALUES
    ('Kwon 사원1', '010-5000-0013', 25, 'M', '1998-01-01', '2022-01-01', 1, '사원', '현장', TRUE),
    ('Oh 사원2', '010-5000-0014', 24, 'F', '1999-02-02', '2023-02-02', 0, '사원', '현장', TRUE),
    ('Ryu 사원3', '010-5000-0015', 23, 'M', '2000-03-03', '2023-03-03', 0, '사원', '현장', TRUE),
    ('Jung 사원4', '010-5000-0016', 22, 'F', '2001-04-04', '2023-04-04', 0, '사원', '현장', TRUE),
    ('Lim 사원5', '010-5000-0017', 26, 'M', '1997-05-05', '2021-05-05', 2, '사원', '현장', TRUE),
    ('Son 사원6', '010-5000-0018', 27, 'F', '1996-06-06', '2020-06-06', 3, '사원', '현장', TRUE),
-- 내부 사원
    ('Baek 사원7', '010-5000-0019', 28, 'M', '1995-07-07', '2019-07-07', 4, '사원', '내부', FALSE),
    ('Hwang 사원8', '010-5000-0020', 30, 'F', '1993-08-08', '2018-08-08', 5, '사원', '내부', FALSE);
    
    
-- 원자재 데이터 추가
INSERT INTO RawMaterialsInventory (name, incomingAmount, consumedAmount, balance, price, supplierRefID) VALUES
('참나무 목재', 1000, 200, 800, 15000, 1),
('강철 못', 5000, 1500, 3500, 10, 2),
('흰색 페인트', 300, 50, 250, 20000, 4),
('초록색 페인트', 300, 40, 260, 18000, 4),
('목재 접착제', 200, 20, 180, 5000, 3),
('바니시', 100, 10, 90, 10000, 3);

INSERT INTO productInventory (Name, stockAmount, dispatchedAmount, price) VALUES
('럭셔리 의자', 100, 20, 150000),
('스탠다드 의자', 200, 50, 75000),
('베이직 의자', 300, 80, 45000);



INSERT INTO defectiveProducts (productRefID, quantity, status)
VALUES
(1, 10, '수리 가능'),
(1, 5, '폐기');    




-- DeliveryRecords 테이블에 6개월간의 데이터 추가
INSERT INTO DeliveryRecords 
(productRefID, companyRefID, quantity, orderDate, deliveryDate, receivedDate, saleAmount, vehicleRefID, destination, driver1RefID, driver2RefID) 
VALUES
-- 1월 데이터
(1, 1, 100, '2024-01-08', '2024-01-10', '2024-01-11', 15000000, 1, '서울특별시', 1, 2),
(2, 2, 80, '2024-01-18', '2024-01-20', '2024-01-21', 12000000, 2, '경기도 수원시', 3, 4),

-- 2월 데이터
(1, 1, 110, '2024-02-09', '2024-02-11', '2024-02-12', 16500000, 1, '서울특별시', 1, 2),
(2, 2, 90, '2024-02-19', '2024-02-21', '2024-02-22', 13500000, 2, '경기도 수원시', 3, 4),

-- 3월 데이터
(1, 1, 95, '2024-03-13', '2024-03-15', '2024-03-16', 14250000, 1, '서울특별시', 1, 2),
(2, 2, 85, '2024-03-23', '2024-03-25', '2024-03-26', 12750000, 2, '경기도 수원시', 3, 4),

-- 4월 데이터
(1, 1, 120, '2024-04-10', '2024-04-12', '2024-04-13', 18000000, 1, '서울특별시', 1, 2),
(2, 2, 100, '2024-04-20', '2024-04-22', '2024-04-23', 15000000, 2, '경기도 수원시', 3, 4),

-- 5월 데이터
(1, 1, 105, '2024-05-08', '2024-05-10', '2024-05-11', 15750000, 1, '서울특별시', 1, 2),
(2, 2, 95, '2024-05-18', '2024-05-20', '2024-05-21', 14250000, 2, '경기도 수원시', 3, 4),

-- 6월 데이터
(1, 1, 130, '2024-06-13', '2024-06-15', '2024-06-16', 19500000, 1, '서울특별시', 1, 2),
(2, 2, 110, '2024-06-23', '2024-06-25', '2024-06-26', 16500000, 2, '경기도 수원시', 3, 4);



INSERT INTO ChairMaterials (chairRefID, materialRefID, materialName, quantityRequired, pricePerUnit) VALUES
(1, 1, '참나무 목재', 5, 15000), -- 고급 의자에 대한 원자재
(1, 2, '강철 못', 20, 500),
(1, 5, '목재 접착제', 1, 2000),
(1, 3, '흰색 페인트', 2, 10000),
(2, 1, '참나무 목재', 4, 15000), -- 중급 의자에 대한 원자재
(2, 2, '강철 못', 15, 500),
(2, 5, '목재 접착제', 1, 2000),
(2, 4, '초록색 페인트', 2, 9000),
(3, 1, '참나무 목재', 3, 15000), -- 저급 의자에 대한 원자재
(3, 2, '강철 못', 10, 500),
(3, 5, '목재 접착제', 1, 2000),
(3, 6, '바니시', 1, 8000);

INSERT INTO Materials_IncomingAndConsumptionRecords (
    materialRefID, 
    incomingDate, 
    incomingQuantity,
    consumedQuantity, 
    cost,
    consumptionDate
)
VALUES
    -- 참나무 목재 입고 (materialID = 1)
    (1, '2024-01-01', 1000, DEFAULT, 15000000, DEFAULT),
    (1, '2024-02-01', 1000, DEFAULT, 15000000, DEFAULT),
    (1, '2024-03-01', 1000, DEFAULT, 15000000, DEFAULT),
    (1, '2024-04-01', 1000, DEFAULT, 15000000, DEFAULT),
    (1, '2024-05-01', 1000, DEFAULT, 15000000, DEFAULT),
    (1, '2024-06-01', 1000, DEFAULT, 15000000, DEFAULT),

    -- 강철 못 입고 (materialID = 2)
    (2, '2024-01-01', 5000, DEFAULT, 50000, DEFAULT),
    (2, '2024-02-01', 5000, DEFAULT, 50000, DEFAULT),
    (2, '2024-03-01', 5000, DEFAULT, 50000, DEFAULT),
    (2, '2024-04-01', 5000, DEFAULT, 50000, DEFAULT),
    (2, '2024-05-01', 5000, DEFAULT, 50000, DEFAULT),
    (2, '2024-06-01', 5000, DEFAULT, 50000, DEFAULT),

    -- 흰색 페인트 입고 (materialID = 3)
    (3, '2024-01-01', 500, DEFAULT, 10000000, DEFAULT),
    (3, '2024-02-01', 500, DEFAULT, 10000000, DEFAULT),
    (3, '2024-03-01', 500, DEFAULT, 10000000, DEFAULT),
    (3, '2024-04-01', 500, DEFAULT, 10000000, DEFAULT),
    (3, '2024-05-01', 500, DEFAULT, 10000000, DEFAULT),
    (3, '2024-06-01', 500, DEFAULT, 10000000, DEFAULT),

    -- 초록색 페인트 입고 (materialID = 4)
    (4, '2024-01-01', 500, DEFAULT, 9000000, DEFAULT),
    (4, '2024-02-01', 500, DEFAULT, 9000000, DEFAULT),
    (4, '2024-03-01', 500, DEFAULT, 9000000, DEFAULT),
    (4, '2024-04-01', 500, DEFAULT, 9000000, DEFAULT),
    (4, '2024-05-01', 500, DEFAULT, 9000000, DEFAULT),
    (4, '2024-06-01', 500, DEFAULT, 9000000, DEFAULT),

    -- 목재 접착제 입고 (materialID = 5)
    (5, '2024-01-01', 200, DEFAULT, 1000000, DEFAULT),
    (5, '2024-02-01', 200, DEFAULT, 1000000, DEFAULT),
    (5, '2024-03-01', 200, DEFAULT, 1000000, DEFAULT),
    (5, '2024-04-01', 200, DEFAULT, 1000000, DEFAULT),
    (5, '2024-05-01', 200, DEFAULT, 1000000, DEFAULT),
    (5, '2024-06-01', 200, DEFAULT, 1000000, DEFAULT),

    -- 바니시 입고 (materialID = 6)
    (6, '2024-01-01', 100, DEFAULT, 10000000, DEFAULT),
    (6, '2024-02-01', 100, DEFAULT, 10000000, DEFAULT),
    (6, '2024-03-01', 100, DEFAULT, 10000000, DEFAULT),
    (6, '2024-04-01', 100, DEFAULT, 10000000, DEFAULT),
    (6, '2024-05-01', 100, DEFAULT, 10000000, DEFAULT),
    (6, '2024-06-01', 100, DEFAULT, 10000000, DEFAULT);
    
    INSERT INTO truck_MaintenanceRecords (equipmentRefID, maintenanceDate, description, cost)
VALUES
    (1, '2024-01-10', '엔진 정비 및 오일 교체', 50000),
    (1, '2024-02-15', '타이어 교체 및 정렬', 30000),
    (1, '2024-03-20', '브레이크 시스템 검사', 40000),
    (1, '2024-04-10', '에어컨 필터 교체', 20000),
    (1, '2024-05-15', '일반 점검 및 세차', 10000),
    (1, '2024-06-20', '배터리 교체', 60000),
    (2, '2024-01-10', '엔진 정비 및 오일 교체', 50000),
    (2, '2024-02-15', '타이어 교체 및 정렬', 30000),
    (2, '2024-03-20', '브레이크 시스템 검사', 40000),
    (2, '2024-04-10', '에어컨 필터 교체', 20000),
    (2, '2024-05-15', '일반 점검 및 세차', 10000),
    (2, '2024-06-20', '배터리 교체', 60000),
    (3, '2024-01-10', '엔진 정비 및 오일 교체', 50000),
    (3, '2024-02-15', '타이어 교체 및 정렬', 30000),
    (3, '2024-03-20', '브레이크 시스템 검사', 40000),
    (3, '2024-04-10', '에어컨 필터 교체', 20000),
    (3, '2024-05-15', '일반 점검 및 세차', 10000),
    (3, '2024-06-20', '배터리 교체', 60000);
    
INSERT INTO EmployeeSalary (employeeRefID, baseSalary, bonus, taxDeduction, totalSalary, salaryDate)
SELECT 
    e.employeeID, 
    CEIL(
        CASE 
            WHEN e.position = '사장' THEN 100000000 / 12
            WHEN e.position = '부장' THEN 80000000 / 12
            WHEN e.position = '과장' THEN 60000000 / 12
            WHEN e.position = '대리' THEN 40000000 / 12
            WHEN e.position = '사원' THEN 30000000 / 12
            ELSE 0
        END
    ) AS baseSalary,
    0 AS bonus,         -- 보너스는 예시로 0원
    0 AS taxDeduction,  -- 세금 공제는 예시로 0원
    CEIL(
        CASE 
            WHEN e.position = '사장' THEN 100000000 / 12
            WHEN e.position = '부장' THEN 80000000 / 12
            WHEN e.position = '과장' THEN 60000000 / 12
            WHEN e.position = '대리' THEN 40000000 / 12
            WHEN e.position = '사원' THEN 30000000 / 12
            ELSE 0
        END
    ) AS totalSalary,
    CURDATE() AS salaryDate  -- 급여 지급일은 오늘 날짜로 설정
FROM 
    EmployeeManagement e;
    
