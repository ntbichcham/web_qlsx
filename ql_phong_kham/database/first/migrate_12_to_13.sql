/*
  NANG CAP DATABASE CU 12 BANG -> 13 BANG
  Chay tren database qlphongkhamnhakhoa da co 12 bang.
  Khong xoa du lieu cu.
*/
USE qlphongkhamnhakhoa;

-- 1. BENH_NHAN co the chua co tai khoan.
ALTER TABLE BENH_NHAN MODIFY COLUMN MaND INT NULL;

-- 2. Them vai tro NHAN_VIEN vao rang buoc CHECK cu (neu co).
SET @old_role_check = (
  SELECT CONSTRAINT_NAME
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'TAI_KHOAN_NGUOI_DUNG'
    AND CONSTRAINT_TYPE = 'CHECK'
  LIMIT 1
);
SET @drop_role_check_sql = IF(
  @old_role_check IS NULL,
  'SELECT ''Khong co CHECK cu can xoa''',
  CONCAT('ALTER TABLE TAI_KHOAN_NGUOI_DUNG DROP CONSTRAINT `', @old_role_check, '`')
);
PREPARE stmt FROM @drop_role_check_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE TAI_KHOAN_NGUOI_DUNG
  ADD CONSTRAINT ck_tknd_vaitro_v2
  CHECK (VaiTro IN ('BENH_NHAN','BAC_SI','NHAN_VIEN','QUAN_TRI_VIEN'));

-- 3. Tao bang NHAN_VIEN neu chua ton tai.
CREATE TABLE IF NOT EXISTS NHAN_VIEN (
  MaNV INT AUTO_INCREMENT PRIMARY KEY,
  MaND INT NOT NULL UNIQUE,
  HoTen VARCHAR(100) NOT NULL,
  SDT VARCHAR(20) NULL,
  Email VARCHAR(100) NULL,
  ChucVu VARCHAR(100) NULL,
  TrangThai VARCHAR(20) NOT NULL DEFAULT 'HOAT_DONG',
  CONSTRAINT fk_nhan_vien_tai_khoan FOREIGN KEY (MaND)
    REFERENCES TAI_KHOAN_NGUOI_DUNG(MaND)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Them cot MaNVTao neu chua co.
SET @has_mnvt = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'LICH_HEN_KHAM'
    AND COLUMN_NAME = 'MaNVTao'
);
SET @add_mnvt_sql = IF(
  @has_mnvt = 0,
  'ALTER TABLE LICH_HEN_KHAM ADD COLUMN MaNVTao INT NULL AFTER MaDV, ADD INDEX ix_lhk_mnvt (MaNVTao)',
  'SELECT ''Cot MaNVTao da ton tai'''
);
PREPARE stmt FROM @add_mnvt_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5. Them FK MaNVTao neu chua co.
SET @has_mnvt_fk = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'LICH_HEN_KHAM'
    AND CONSTRAINT_NAME = 'fk_lhk_nhan_vien'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @add_mnvt_fk_sql = IF(
  @has_mnvt_fk = 0,
  'ALTER TABLE LICH_HEN_KHAM ADD CONSTRAINT fk_lhk_nhan_vien FOREIGN KEY (MaNVTao) REFERENCES NHAN_VIEN(MaNV)',
  'SELECT ''FK MaNVTao da ton tai'''
);
PREPARE stmt FROM @add_mnvt_fk_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 6. Kiem tra ket qua nang cap.
SELECT TABLE_NAME
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE'
ORDER BY TABLE_NAME;

SHOW COLUMNS FROM BENH_NHAN LIKE 'MaND';
SHOW COLUMNS FROM LICH_HEN_KHAM LIKE 'MaNVTao';
SHOW CREATE TABLE NHAN_VIEN;
