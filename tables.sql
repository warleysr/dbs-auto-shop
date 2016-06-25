CREATE TABLE IF NOT EXISTS `produtos` (
	`codigo`    INT            NOT NULL  AUTO_INCREMENT PRIMARY KEY,
	`nome`      VARCHAR(32)    NOT NULL,
	`preco`     DOUBLE         NOT NULL
);

CREATE TABLE IF NOT EXISTS `pendentes` (
	`nick`       VARCHAR(16)      NOT NULL,
	`produto`    INT              NOT NULL,
	`codigo`     VARCHAR(36)      NOT NULL PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS `estornados` (
	`nick`       VARCHAR(16)    NOT NULL,
	`produto`    INT            NOT NULL,
	`codigo`     VARCHAR(36)    NOT NULL PRIMARY KEY,
	`status`     INT(1)         NOT NULL
);

CREATE TABLE IF NOT EXISTS `log` (
	`nick`       VARCHAR(16)  NOT NULL,
	`produto`    INT          NOT NULL,
	`codigo`     VARCHAR(36)  NOT NULL PRIMARY KEY,
	`horario`    TIMESTAMP    NOT NULL
);