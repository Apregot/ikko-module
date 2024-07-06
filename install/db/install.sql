CREATE TABLE IF NOT EXISTS b_ikkomodule_order (
	ID bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
	NAME varchar(255) NOT NULL,
	DATE datetime NOT NULL
);

CREATE TABLE IF NOT EXISTS b_ikkomodule_product_complexity (
	ID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	NAME varchar(255) NOT NULL,
	SECONDS_TO_MAKE INT NOT NULL
);