-- Tạo Schema (Hỗ trợ SQL Server / PostgreSQL)
CREATE SCHEMA IF NOT EXISTS Dim;
CREATE SCHEMA IF NOT EXISTS Fact;

-- 1. Bảng Dim.Date
CREATE TABLE Dim.Date (
    DateKey INT PRIMARY KEY, -- Định dạng YYYYMMDD
    FullDate DATE NOT NULL,
    Day INT NOT NULL,
    Month INT NOT NULL,
    Quarter INT NOT NULL,
    Year INT NOT NULL,
    IsWeekend BOOLEAN NOT NULL
);

-- 2. Bảng Dim.Kiosk
CREATE TABLE Dim.Kiosk (
    KioskKey INT AUTO_INCREMENT PRIMARY KEY, -- Surrogate Key (SQL Server dùng IDENTITY(1,1), PostgreSQL dùng SERIAL)
    OriginalKioskID INT NOT NULL,            -- Natural Key từ hệ thống OLTP
    Code VARCHAR(50) NOT NULL,
    Zone VARCHAR(50) NOT NULL,
    Area DECIMAL(8,2) NOT NULL,
    BasePrice DECIMAL(15,2) NOT NULL
);

-- 3. Bảng Fact.Rental
CREATE TABLE Fact.Rental (
    RentalKey INT AUTO_INCREMENT PRIMARY KEY,
    DateKey INT NOT NULL,                    -- Foreign Key -> Dim.Date
    KioskKey INT NOT NULL,                   -- Foreign Key -> Dim.Kiosk
    OriginalRequestID INT NOT NULL,          -- Lưu ID của rental_requests để truy vết
    DurationMonths INT NOT NULL,             -- Đo lường (Measure)
    TotalRevenue DECIMAL(15,2) NOT NULL,     -- Đo lường = BasePrice * DurationMonths
    Status VARCHAR(50) NOT NULL,
    
    FOREIGN KEY (DateKey) REFERENCES Dim.Date(DateKey),
    FOREIGN KEY (KioskKey) REFERENCES Dim.Kiosk(KioskKey)
);
