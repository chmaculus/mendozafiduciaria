/****** Script  para crear la tabla fid_operatoria_vino ******/
CREATE TABLE IF NOT EXISTS `fid_operatoria_vino` (
  `ID_OPERATORIA` int(11) NOT NULL,
  `FECHA_CRE` date NOT NULL,
  `FECHA_VEN` date NOT NULL,
  `NOMBRE_OPE` varchar(120) NOT NULL,
  `DESCRIPCION_OPE` varchar(120) NOT NULL,
  `ID_COORDINADOR_OPE` int(11) NOT NULL,
  `ID_JEFE_OPE` int(11) NOT NULL,
  `PERSONA` VARCHAR( 64 ) NOT NULL,
  `LTRS_MAX` float NOT NULL,
  `MAX_PESOS` float NOT NULL,
  `HECT_MAX` int(11) NOT NULL,
  `PRECIO_1` float NOT NULL,
  `PRECIO_2` float NOT NULL,
  `PRECIO_3` float NOT NULL,
  `PRECIO_4` float NOT NULL,
  `PRECIO_5` float NOT NULL,
  `PRECIO_6` float NOT NULL,
  `CHECKLIST_PERSONA` tinytext NOT NULL,
  `ESTADO_OP` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/****** Script  para crear la tabla fid_op_vino_proveedores ******/

CREATE TABLE IF NOT EXISTS `fid_op_vino_proveedores` (
  `ID_OPERATORIA` int(11) NOT NULL,
  `ID_PROVEEDOR` int(11) NOT NULL,
  `LIMITE_OPE` float NOT NULL,
  `LIM_OPE_HECT` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/****** Script  para crear la tabla fid_op_vino_bodegas ******/
CREATE TABLE IF NOT EXISTS `fid_op_vino_bodegas` (
  `ID_OPERATORIA` int(11) NOT NULL,
  `ID_BODEGA` int(11) NOT NULL,
  `LIMITE_OPE` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `fid_op_vino_checklist` (
  `ID` int(11) NOT NULL,
  `JURIDICA` tinyint(1) NOT NULL,
  `DESCRIPCION` varchar(250) NOT NULL,
  `ESTADO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/****** CREAR TABLA fid_op_vino_checklist E INSERTAR VALORES ******/
CREATE TABLE IF NOT EXISTS `fid_op_vino_checklist` (
  `ID` int(11) NOT NULL,
  `JURIDICA` tinyint(1) NOT NULL,
  `DESCRIPCION` varchar(250) NOT NULL,
  `ESTADO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `fid_op_vino_checklist` (`ID`, `JURIDICA`, `DESCRIPCION`, `ESTADO`) VALUES
(1, 1, 'Copia del contrato social, estatutos y demás documentos relativos a la capacidad jurídica de la empresa Postulante para suscribir el contrato de compraventa conforme a su objeto y a la representación de sus directores o administradores, debidamente a', 1),
(2, 1, 'Copia de Actas de Asamblea u órgano similar, donde surja la designación de los actuales directores, socios gerentes o representantes, y del acta de directorio u órgano similar de distribución de cargos, con las inscripciones de los administradores en', 1),
(3, 1, 'En caso de tener representante, copia del Poder que confiera representación a la persona que designe el Postulante para la presentación de la propuesta, con facultades expresas para asumir las obligaciones emergentes de la operatoria y constancia de ', 1),
(4, 1, 'Constancia de inscripción ante la A.F.I.P., como contribuyente de impuestos e incorporado al sistema previsional.', 1),
(5, 1, 'Constancia de inscripción en el Impuesto sobre los Ingresos Brutos ya sea como contribuyente local o comprendido en el régimen del Convenio Multilateral.', 1),
(6, 1, 'Constancia de Inscripcion en INV como titular de viñedo, bodega o elaborador.', 1),
(7, 1, 'En caso de ser elaborador, constancia de elaboración de vinos de los años 2014 y 2015, emitidas por el INV.', 1),
(8, 1, 'Constancia del CBU emitida por entidad bancaria (la cuenta en la cual serán efectuados los pagos, deberá ser de titularidad del vendedor).', 1),
(9, 1, 'Toda la documentación deberá estar firmada y con aclaración de firma en todas sus hojas por el Postulante (o en su caso, representante legal o apoderado).', 1),
(10, 0, 'Fotocopia del documento de identidad del Postulante.', 1),
(11, 0, 'En caso de tener representante, copia del Poder que confiera representación a la persona que designe el Postulante para la presentación de la propuesta, con facultades expresas para asumir las obligaciones emergentes de la operatoria y constancia de ', 1),
(12, 0, 'Constancia de inscripción ante la A.F.I.P., como contribuyente de impuestos e incorporado al sistema previsional.', 1),
(13, 0, 'Constancia de inscripción en el Impuesto sobre los Ingresos Brutos ya sea como contribuyente local o comprendido en el régimen del Convenio Multilateral.', 1),
(14, 0, 'Constancia de Inscripcion en INV como titular de viñedo, bodega o elaborador.', 1),
(15, 0, 'En caso de ser elaborador, constancia de elaboración de vinos de los años 2014 y 2015, emitidas por el INV.', 1),
(16, 0, 'En caso de ser contratista de viña, copia del contrato sellado ante la Subsecretaría de Trabajo, el cual permita constatar la cantidad de has. que se explotan.', 1),
(17, 0, 'Constancia del CBU emitida por entidad bancaria (la cuenta en la cual serán efectuados los pagos, deberá ser de titularidad del Postulante u oferente.', 1),
(18, 0, 'Toda la documentación deberá estar firmada y con aclaración de firma en todas sus hojas por el Postulante (o en su caso, representante legal o apoderado).', 1);

/*SCRIPT PARA TABLA QUE ALMACENA LOS USUARIOS QUE ACTIVAN EL TILDE DE CAMBIO DE TITULARIDAD EN FACTURAS*/

CREATE TABLE `fid_op_vino_cambio_tit` (
`ID_FACTURA` INT NOT NULL ,
`ID_USUARIO` INT NOT NULL ,
`FECHA` DATETIME NOT NULL ,
`CHECK_ESTADO` INT NOT NULL
) ENGINE = INNODB;

/* Script  para agregar campos a la tabla fid_cu_factura */

ALTER TABLE `fid_cu_factura` ADD `VINEDO` VARCHAR( 20 ) NOT NULL AFTER `ID_CLIENTE` ,
ADD `RUT` VARCHAR( 20 ) NOT NULL AFTER `VINEDO`,
ADD `FORMA_PAGO` INT( 1 ) NOT NULL AFTER `RUT`;

/*07/04/2016*/
/*Agrego campo para guardar los checklist en las facturas*/
ALTER TABLE  `fid_cu_factura` ADD  `CHECKLIST_PERSONA` TINYTEXT NOT NULL;

/*08/04/2016*/
/*Agrego campo en la tabla de las facturas para almacenar la orden de pago generada en el debo*/
ALTER TABLE  `fid_cu_factura` ADD  `ORDEN_PAGO` VARCHAR( 64 ) NOT NULL;



/*25/04/2016*/
/*Creo tabla para agregar los pagos con las cuotas y fechas de vencimiento*/
CREATE TABLE `fid_cu_pagos` (
`ESTADO_CUOTA` INT NOT NULL ,
`ID_FACTURA` INT NOT NULL ,
`NUM_CUOTA` FLOAT NOT NULL ,
`VALOR_CUOTA` FLOAT NOT NULL ,
`FECHA_VEN` DATE NOT NULL
) ENGINE = InnoDB;


/*28/04/2016*/
ALTER TABLE `fid_operatoria_vino` ADD `ID_FIDEICOMISO` INT NOT NULL AFTER `ID_OPERATORIA`;


/*28/04/2016*/
/*Campos que agrego en la base MICROSOFTSQL del debo para pasarle otros valores*/
 alter table solicitud_adm
  add NETO float not null default 0,
  IVA float not null default 0,
  CCU INT not null default 0,
  UCU INT not null default 0;

/*02/05/2016*/
/*Se agrega la columna orden de pago para guardar la orden de cada cuota*/
ALTER TABLE  `fid_cu_pagos` ADD  `ORDEN_PAGO` VARCHAR( 64 ) NOT NULL