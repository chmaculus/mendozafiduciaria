/*27/06 
* Insert para agregar el boton de agencia
*/
INSERT INTO fid_menu VALUES(39,'Agencia',0,11,'backend/carpeta/agencia',1)

/*
* Insert para agregar agencia a la tabla permisos
*/
INSERT INTO fid_permisos VALUES (21,'Procesos','Agencia',39)

/*
* Se agrega el campo retencion a la tabla clientes
*/
ALTER TABLE  `fid_clientes` ADD  `RETENCION` FLOAT NOT NULL

/*28/06
* Se agrega el campo mayorista a la tabla clientes
*/
ALTER TABLE  `fid_clientes` ADD  `MAYORISTA` INT NOT NULL

/*29/06
* Se agrega el campo limite por si es mayorista a la tabla clientes
*/
ALTER TABLE  `fid_clientes` ADD  `LIMITE_M` FLOAT NOT NULL

/*
* Se agrega el campo RETENCION_PORC para guardar en la factura el porcentaje de retencion que se le hace  por si despues se modifica la retencion
*del cliente
*/
ALTER TABLE  `fid_cu_factura` ADD  `RETENCION_PORC` FLOAT NOT NULL

/*
* Se agrega el campo TIPO para poder diferenciar los distintos pagos
* 0 => UVA
* 1 => VINO
* 2 => AGENCIA
*/
ALTER TABLE  `fid_cu_pagos` ADD  `TIPO` INT NOT NULL

/*
* 27/07/2016
* Se agrega campo para guardar la fecha de desembols / vencimiento en agencia
*/
ALTER TABLE  `fid_cu_factura` ADD  `FECHAVTO_DESEMB` DATETIME NULL

/*
* 28/07/2016
* Se cambia el tipo de campo por el formato en que se necesitan ingresar las fechas
* Verificar si es necesario usarlo, se vuelve a datetime
*/
-- ALTER TABLE  `fid_cu_factura` CHANGE  `FECHAVTO_DESEMB`  `FECHAVTO_DESEMB` VARCHAR( 64 ) NULL DEFAULT NULL

/*
* 01/08/2016
* Se agrega un campo mas por que si es minorista se le debe colocar un rango de fechas
*/
ALTER TABLE  `fid_cu_factura` ADD  `FECHAVTO_DESEMB2` DATETIME NULL

/*
* Se agrega el campo para guardar el numero de desembolso
*/
ALTER TABLE  `fid_cu_factura` ADD  `NRO_DESEMBOLSO` VARCHAR( 64 ) NOT NULL