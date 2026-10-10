-- Import lewat phpMyAdmin: tab Import -> pilih file ini -> Go
CREATE DATABASE IF NOT EXISTS cahaya_computer CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE cahaya_computer;

CREATE TABLE IF NOT EXISTS bookings (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  nama       VARCHAR(80)  NOT NULL,
  layanan    VARCHAR(100) NOT NULL,
  perangkat  VARCHAR(100) NOT NULL DEFAULT '',
  catatan    TEXT NOT NULL,
  status     VARCHAR(20)  NOT NULL DEFAULT 'baru',
  ip         VARCHAR(45)  NOT NULL DEFAULT '',
  created_at DATETIME     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admins (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(30)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    DATETIME     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
