# BUSCAR CLIENTES REPETIDOS

SELECT ID, RAZON_SOCIAL, CUIT FROM `fid_clientes` WHERE REPLACE(REPLACE(RAZON_SOCIAL, ' ', ''), ',', '') IN 
(SELECT REPLACE(REPLACE(RAZON_SOCIAL, ' ', ''), ',', '') FROM `fid_clientes`  GROUP BY REPLACE(REPLACE(RAZON_SOCIAL, ' ', ''), ',', '') HAVING COUNT(ID) > 1)
ORDER BY RAZON_SOCIAL ASC INTO OUTFILE 'C:\\xampp\\mysql\\bin\\clientes.txt'