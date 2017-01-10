CREATE TABLE IF NOT EXISTS `fid_operatoria_cambiotasas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_OPERATORIA` int(11) NOT NULL,
  `FECHA` int(11) NOT NULL,
  `COMPENSATORIO` double NOT NULL,
  `SUBSIDIO` double NOT NULL,
  `MORATORIO` double NOT NULL,
  `PUNITORIO` double NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_OPERATORIA` (`ID_OPERATORIA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


ALTER TABLE `fid_creditos` ADD `POSTULANTES_NOMBRES` TINYTEXT NOT NULL AFTER `POSTULANTES` ,
ADD `POSTULANTES_CUIT` VARCHAR( 45 ) NOT NULL AFTER `POSTULANTES_NOMBRES` ;


ALTER TABLE `fid_creditos` ADD `IVA` FLOAT NOT NULL AFTER `T_GASTOS_MIN` ;
UPDATE `fid_creditos` c JOIN `fid_operatorias` o ON c.ID_OPERATORIA = o.ID SET c.IVA=o.IVA;


INSERT INTO `fid_menu` (`ID`, `NOMBRE`,`ESPADRE`, `PADRE`, `URL`, `ESTADO`) VALUES (NULL , 'Cobranzas de Créditos', '0', '23', 'creditos/front/cobranzas', '1');
INSERT INTO `fid_permisos` (`ID`, `MODULO`, `PERMISO`, `CODIGO`) VALUES (NULL, 'Creditos', 'Cobranzas de Crédito', '40');
INSERT INTO `fid_roles_menu` (`ID_ROL`, `ID_MENU`) VALUES ('20', '40');


/*cobranzas*/
CREATE TABLE `fid_creditos_cobranzas` (
`ID_HO` BIGINT NOT NULL,
`ID_CREDITO` INT NOT NULL ,
`CUOTAS_RESTANTES` INT NOT NULL ,
`NRO_CUOTA` INT NOT NULL ,
`CAPITAL_CUOTA` FLOAT NOT NULL ,
`INT_COMPENSATORIO` FLOAT NOT NULL ,
`INT_COMPENSATORIO_IVA` FLOAT NOT NULL ,
`FECHA` DATETIME NOT NULL
) ENGINE = InnoDB;

ALTER TABLE `fid_creditos_cobranzas` ADD UNIQUE (
`ID_CREDITO` ,
`CUOTAS_RESTANTES`
);

ALTER TABLE `fid_clientes` ADD `ID_CLIENTE_HO` INT NOT NULL AFTER `ID` 