--SELECT cambiarIdCredito(20220715478, 1240, 1999);

DROP FUNCTION IF EXISTS cambiarIdCredito;

DELIMITER //

CREATE FUNCTION cambiarIdCredito (cui VARCHAR(20), n INT, m INT)
    RETURNS VARCHAR(50)

    BEGIN
        DECLARE s VARCHAR(50);

        IF (SELECT COUNT(*) FROM fid_creditos WHERE ID=n) = 0
            THEN SET s = CONCAT('No existe el crédito ID = ', n);
        ELSE
            IF (SELECT COUNT(*) FROM fid_creditos WHERE ID=n AND POSTULANTES_CUIT=cui) = 0
                THEN SET s = CONCAT('Verificar el CUIT del crédito ID = ', n);
            ELSE
                IF (SELECT COUNT(*) FROM fid_creditos WHERE ID=m) != 0 
                    THEN SET s = CONCAT('No se puede mover ya que existe el crédito ID = ', m);
                ELSE
                    DELETE FROM fid_creditos_cuotas WHERE ID_CREDITO=m;
                    DELETE FROM fid_creditos_desembolsos WHERE ID_CREDITO=m;
                    DELETE FROM fid_creditos_cambiotasas WHERE ID_CREDITO=m;
                    DELETE FROM fid_creditos_gastos WHERE ID_CREDITO=m;
                    DELETE FROM fid_creditos_eventos WHERE ID_CREDITO=m;
                    DELETE FROM fid_creditos_extra WHERE CREDITO_ID=m;
                    DELETE FROM fid_creditos_pagos WHERE ID_CREDITO=m;
                    DELETE FROM fid_creditos_cambiotasas WHERE ID_CREDITO=m;
                    DELETE FROM fid_creditos_version WHERE ID_CREDITO_VERSION=m;

                    UPDATE fid_creditos SET ID=m WHERE ID=n;
                    UPDATE fid_creditos_cuotas SET ID_CREDITO=m WHERE ID_CREDITO=n;
                    UPDATE fid_creditos_desembolsos SET ID_CREDITO=m WHERE ID_CREDITO=n;
                    UPDATE fid_creditos_cambiotasas SET ID_CREDITO=m WHERE ID_CREDITO=n;
                    UPDATE fid_creditos_gastos SET ID_CREDITO=m WHERE ID_CREDITO=n;
                    UPDATE fid_creditos_eventos SET ID_CREDITO=m WHERE ID_CREDITO=n;
                    UPDATE fid_creditos_extra SET CREDITO_ID=m WHERE CREDITO_ID=n;
                    UPDATE fid_creditos_pagos SET ID_CREDITO=m WHERE ID_CREDITO=n;
                    UPDATE fid_creditos_version SET ID_CREDITO_VERSION=m WHERE ID_CREDITO_VERSION=n;
                    UPDATE fid_creditos SET ID_CADUCADO=m WHERE ID_CADUCADO=n;
                    SET s = CONCAT('Se movió el crédito ', n, ' a ', m);
                END IF;
            END IF;
        END IF;

        RETURN s;
    END //

DELIMITER ;
