-- ============================================
-- School Lost and Found - Database Schema
-- MySQL 8.0+
-- ============================================

CREATE DATABASE IF NOT EXISTS `lost_and_found_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `lost_and_found_db`;

-- ============================================
-- Users Table
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(50)     NOT NULL,
  `email`      VARCHAR(100)    NOT NULL,
  `password`   VARCHAR(255)    NOT NULL,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Items Table
-- ============================================
CREATE TABLE IF NOT EXISTS `items` (
  `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED    NOT NULL,
  `title`       VARCHAR(150)    NOT NULL,
  `description` TEXT            NOT NULL,
  `category`    VARCHAR(50)     NOT NULL DEFAULT 'Other',
  `type`        ENUM('lost','found') NOT NULL,
  `location`    VARCHAR(200)    NOT NULL,
  `image_path`  VARCHAR(255)    DEFAULT NULL,
  `status`      ENUM('active','resolved') NOT NULL DEFAULT 'active',
  `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_items_user` (`user_id`),
  KEY `idx_items_type` (`type`),
  KEY `idx_items_status` (`status`),
  KEY `idx_items_category` (`category`),
  FULLTEXT KEY `ft_items_search` (`title`, `description`),
  CONSTRAINT `fk_items_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Messages Table
-- ============================================
CREATE TABLE IF NOT EXISTS `messages` (
  `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `item_id`     INT UNSIGNED    NOT NULL,
  `sender_id`   INT UNSIGNED    NOT NULL,
  `receiver_id` INT UNSIGNED    NOT NULL,
  `content`     TEXT            NOT NULL,
  `sent_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_messages_item` (`item_id`),
  KEY `idx_messages_sender` (`sender_id`),
  KEY `idx_messages_receiver` (`receiver_id`),
  CONSTRAINT `fk_messages_item`
    FOREIGN KEY (`item_id`) REFERENCES `items` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_messages_sender`
    FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_messages_receiver`
    FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
