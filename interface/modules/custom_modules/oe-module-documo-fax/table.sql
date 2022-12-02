CREATE TABLE IF NOT EXISTS `documo_user`
(
    `id` INT NOT NULL PRIMARY KEY auto_increment,
    `account_user` TEXT NOT NULL,
    `fax_numbers` TEXT NULL,
    `webhook` TEXT NULL,
    `password` TEXT NULL
) ENGINE = InnoDB COMMENT = 'documo account users';

CREATE TABLE IF NOT EXISTS `documo_account`
(
    `id` INT NOT NULL PRIMARY KEY auto_increment,
    `account_info` TEXT NOT NULL
) ENGINE = InnoDB COMMENT = 'documo account information';

CREATE TABLE  IF NOT EXISTS `documo_fax_inbound`
(
    `id` INT NOT NULL PRIMARY KEY auto_increment,
    `date` DATETIME NOT NULL,
    `message_json` TEXT NOT NULL,
    `file_name` VARCHAR(50) NOT NULL
) ENGINE = InnoDB COMMENT = 'documo fax inbound';