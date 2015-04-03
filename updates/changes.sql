# 03.12.2014 - 3.9
CREATE TABLE IF NOT EXISTS `tm_user_auth_attempts` (
  `ip` varchar(25) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attempts` smallint(3) NOT NULL DEFAULT '0',
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
UPDATE `themagescms` SET `value` = '3.9' WHERE `themagescms`.`setting` = 'version';

# 03.12.2014 - 3.8
ALTER TABLE `tm_admins` ADD `editRedirect` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `themagescms` DROP INDEX `setting`;
ALTER TABLE `themagescms` ADD UNIQUE(`setting`);
UPDATE `themagescms` SET `value` = '3.8' WHERE `themagescms`.`setting` = 'version';
ALTER TABLE `tm_api_request` CHANGE `response_data` `response_data` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

# 09.09.2014
ALTER TABLE  `tm_admins` ADD  `custom_access` TEXT NULL AFTER  `level`;

# 19.06.2014
ALTER TABLE  `tm_links` ADD  `logged_in` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `tm_links` DROP INDEX  `value`;
ALTER TABLE  `tm_pages` ADD  `logged_in` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `value`;

# specific change, depeds on languages available on website
# ALTER TABLE  `tm_pages` CHANGE  `russian`  `text_russian` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
# CHANGE  `english`  `text_english` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

# 10.06.2014 - admins update, changing default lang from "us" to "en"
ALTER TABLE  `tm_admins` CHANGE  `language`  `language` CHAR( 2 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'en'

# 28.05.2014 - settings update
ALTER TABLE  `tm_settings` ADD  `field` VARCHAR( 99 ) NULL ,
ADD  `type` ENUM(  'text',  'checkbox', 'level' ) NOT NULL DEFAULT  'text',
ADD  `position` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';