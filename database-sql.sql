-- Create database and tables - you need to run this SQL in phpMyAdmin or another MySQL client
CREATE DATABASE coin_clicker;

USE coin_clicker;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL UNIQUE,
    username VARCHAR(255) NOT NULL,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    coins INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX idx_user_id ON users(user_id);
CREATE INDEX idx_coins ON users(coins);
