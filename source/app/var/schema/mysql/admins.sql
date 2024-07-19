DROP TABLE IF EXISTS `admins`;

CREATE TABLE `admins`
(
    `id`          INTEGER UNSIGNED AUTO_INCREMENT NOT NULL,
    `name`        VARCHAR(100)                    NOT NULL COMMENT '名前',
    `username`    VARCHAR(100)                    NOT NULL COMMENT 'ユーザー名',
    `password`    VARCHAR(255)                    NOT NULL COMMENT 'パスワード',
    `create_date` DATETIME                        NOT NULL,
    `update_date` DATETIME                        NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理者';
