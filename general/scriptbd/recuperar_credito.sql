DROP FUNCTION IF EXISTS recuperarCredito;

DELIMITER //

CREATE FUNCTION recuperarCredito (id_cred INT)
    RETURNS VARCHAR(50)

    BEGIN
        DECLARE s VARCHAR(50);

        IF (SELECT COUNT(*) FROM fiduciaria.fid_creditos WHERE ID=id_cred) > 0 THEN 
            SET s = CONCAT('No se puede copiar, ya existe el crédito ID = ', id_cred);
        ELSE
            IF (SELECT COUNT(*) FROM fiduciaria_20170603_1000.fid_creditos WHERE ID=id_cred) = 0 THEN 
                SET s = CONCAT('No se encontró el crédito ID = ', id_cred);
            ELSE
                INSERT INTO fiduciaria.fid_creditos (SELECT * FROM fiduciaria_20170603_1000.fid_creditos WHERE ID=id_cred);
                INSERT INTO fiduciaria.fid_creditos_cuotas (SELECT * FROM fiduciaria_20170603_1000.fid_creditos_cuotas WHERE ID_CREDITO=id_cred);
                INSERT INTO fiduciaria.fid_creditos_bancos_cobros (SELECT * FROM fiduciaria_20170603_1000.fid_creditos_bancos_cobros WHERE ID_CREDITO=id_cred);
                INSERT INTO fiduciaria.fid_creditos_desembolsos (SELECT * FROM fiduciaria_20170603_1000.fid_creditos_desembolsos WHERE ID_CREDITO=id_cred);
                INSERT INTO fiduciaria.fid_creditos_cambiotasas (SELECT * FROM fiduciaria_20170603_1000.fid_creditos_cambiotasas WHERE ID_CREDITO=id_cred);
                INSERT INTO fiduciaria.fid_creditos_gastos (SELECT * FROM fiduciaria_20170603_1000.fid_creditos_gastos WHERE ID_CREDITO=id_cred);
                INSERT INTO fiduciaria.fid_creditos_eventos (SELECT * FROM fiduciaria_20170603_1000.fid_creditos_eventos WHERE ID_CREDITO=id_cred);
                INSERT INTO fiduciaria.fid_creditos_extra (SELECT * FROM fiduciaria_20170603_1000.fid_creditos_extra WHERE CREDITO_ID=id_cred);
                INSERT INTO fiduciaria.fid_creditos_version (SELECT * FROM fiduciaria_20170603_1000.fid_creditos_version WHERE ID_CREDITO_VERSION=id_cred);

                SET s = CONCAT('Se copió el crédito ID = ', id_cred);
            END IF;
        END IF;

        RETURN s;
    END //

DELIMITER ;



--select recuperarCredito(2004);fiduciaria_20170603_1000  -   fid_creditos_cuotas