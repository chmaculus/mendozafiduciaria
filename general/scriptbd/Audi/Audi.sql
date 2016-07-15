/*
*05/07
*Se crea tabla auditoria para guardar los cambios de estados en las facturas o pagos
*/
CREATE TABLE  `fid_audi_fact` (
`ID_AUDI` INT NULL AUTO_INCREMENT PRIMARY KEY ,
`ID_USUARIO` INT NOT NULL ,
`ACCION` VARCHAR( 600 ) NOT NULL ,
`FECHA_ACCION` DATETIME NOT NULL
) ENGINE = INNODB;
/*
*06/07
*Se crea una tabla solamente para definit los estados de los pagos en las cuotas para identificarlos en la tabla fid_audi_fact
*/
CREATE TABLE `fid_cu_pagos_estados` (
`ID` INT NOT NULL ,
`NOMBRE` VARCHAR( 64 ) NOT NULL
) ENGINE = INNODB;
/*Los insert que van a llenar la tabla con los estados de los pagos*/
INSERT INTO fid_cu_pagos_estados VALUES (0,"No Enviada")
INSERT INTO fid_cu_pagos_estados VALUES (1,"Pendiente")
INSERT INTO fid_cu_pagos_estados VALUES (2,"Pagada")
/*
*15/07
*/
ALTER TABLE  `fid_audi_fact` ADD  `SECTOR` VARCHAR( 64 ) NOT NULL AFTER  `ACCION`