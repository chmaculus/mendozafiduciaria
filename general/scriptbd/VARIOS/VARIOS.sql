/****** Script para agregar “limite” en la tabla entidades******/
ALTER TABLE `fid_entidades` ADD `LIMITE` FLOAT( 11 ) NOT NULL;
/****** Script para agregar “ID_PROVINCIA” en la tabla entidades******/
ALTER TABLE  `fid_entidades` ADD  `ID_PROVINCIA` INT( 11 ) NOT NULL;
/****** Script para crear tabla fid_factura_depositarios******/
CREATE TABLE `fid_factura_depositarios` (
`ID` INT( 11 ) NOT NULL ,
`NUMERO_FACTURA` INT( 11 ) NOT NULL ,
`ID_BODEGA` INT( 11 ) NOT NULL ,
`KGRS` FLOAT( 15 ) NOT NULL ,
`LITROS` FLOAT( 15 ) NOT NULL
) ENGINE = INNODB;
/****** Agregar LIMITE_LITROS a fid_entidades ******/
ALTER TABLE `fid_entidades` ADD `LIMITE_LITROS` FLOAT( 11 ) NOT NULL;
/****** CAMBIAR ID_BODEGA  de entero a varchar fid_cu_factura ******/
ALTER TABLE `fid_cu_factura` CHANGE `ID_BODEGA` `ID_BODEGA` VARCHAR( 50 ) NULL DEFAULT NULL;
/****** Agregar LITROS  a fid_cu_factura ******/
ALTER TABLE `fid_cu_factura` ADD `LITROS` VARCHAR( 20 ) NOT NULL;
/****** Agregar PORC_IVA  a fid_cu_factura  asi almacenamos el porcentaje para despues mostrarlo ******/
ALTER TABLE `fid_cu_factura` ADD `PORC_IVA` VARCHAR( 20 ) NOT NULL
/****** 16/03  ******/
/****** Crear  la tabla fid_cu_analisis para la carga de factura del proceso de compra vino. ******/
CREATE TABLE `fid_cu_analisis` (
`ID` INT( 11 ) NOT NULL ,
`ID_FACTURA` INT( 11 ) NOT NULL ,
`CUIT` VARCHAR( 64 ) NOT NULL ,
`ID_CLIENTE` INT( 11 ) NOT NULL ,
`NUMERO` VARCHAR( 45 ) NOT NULL ,
`LITROS` VARCHAR( 20 ) NOT NULL ,
`AZUCAR` VARCHAR( 20 ) NOT NULL ,
`VERIFICADO` TINYINT( 4 ) NOT NULL ,
`VARCHAR` VARCHAR( 20 ) NOT NULL ,
`TIPO` TINYINT( 4 ) NOT NULL
) ENGINE = INNODB;
