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

