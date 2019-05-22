CREATE TABLE IF NOT EXISTS `#__jatoms_showcases`
(
    `id`          INT(11)                                                NOT NULL AUTO_INCREMENT,
    `title`       VARCHAR(255)                                           NOT NULL DEFAULT '',
    `alias`       VARCHAR(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
    `key`         VARCHAR(100)                                           NOT NULL DEFAULT '',
    `description` MEDIUMTEXT                                             NOT NULL,
    `images`      MEDIUMTEXT                                             NOT NULL,
    `state`       TINYINT(3)                                             NOT NULL DEFAULT 0,
    `params`      TEXT                                                   NOT NULL,
    `ordering`    INT(11)                                                NOT NULL DEFAULT 0,
    PRIMARY KEY `id` (`id`),
    KEY `idx_alias` (`alias`(100)),
    KEY `idx_key` (`key`(100)),
    KEY `idx_state` (`state`),
    KEY `idx_ordering` (`ordering`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci
    AUTO_INCREMENT = 0;