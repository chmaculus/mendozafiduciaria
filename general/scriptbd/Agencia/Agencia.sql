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

