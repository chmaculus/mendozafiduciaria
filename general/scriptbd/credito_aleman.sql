ALTER TABLE `fid_creditos` ADD `SISTEMA_CREDITO` INT( 1 ) NOT NULL DEFAULT '0' AFTER `TIPO_CREDITO`;

INSERT INTO `fiduciaria`.`fid_settings` (`variable` ,`valor` ,`descripcion`)
VALUES ('sistema_credito', '1', 'Define si trabaja con sistema francés');