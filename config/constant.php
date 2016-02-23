<?php
define("IMP_IVA", 0.21);
define("PAGO_IVA_PUNITORIO", 1);
define("PAGO_IVA_MORATORIO", 2);
define("PAGO_IVA_COMPENSATORIO", 3);
define("PAGO_MORATORIO", 5);
define("PAGO_PUNITORIO", 4);
define("PAGO_COMPENSATORIO", 6);
define("PAGO_CAPITAL", 7);
define("PAGO_GASTOS", 8);
define("PAGO_GASTOS_ADM", 11);
define("PAGO_IVA_GASTOS_ADM", 12);
define("PAGO_ADELANTADO", 10);
define("PLAZO_SUBSIDIO_VENCIDO", 5);
define("NO_FECHA", 2147483647);


//devengamiento no definido, se calcula planchando la cuota segun su fecha de envio
define("TIPO_DEVENGAMIENTO_AUTO", 0);

//devengamiento de intereses solo hasta la fecha de calculo (int compensatorio a 0 despues de la fecha de calculo)
define("TIPO_DEVENGAMIENTO_DEVENGAR_A_FECHA", 1);

//contrario a la opcion anterior, se fuera el devengamiento de todas las cuotas calculadas
define("TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO", 2);

//solo se plancha la cuota actual sin importar si se ha enviado
define("TIPO_DEVENGAMIENTO_FORZAR_CUOTA_ACTUAL", 3);

define("TIPO_MICROCREDITO", 1);

define("EVENTO_ENVIO", 7);
define("EVENTO_GASTO", 5);
define("EVENTO_INICIAL", 0);
define("EVENTO_DESEMBOLSO", 1);
define("EVENTO_TASA", 2);
define("EVENTO_RECUPERO", 3);
define("EVENTO_VENCIMIENTO", 4000);
define("EVENTO_AJUSTE", 4);
define("EVENTO_INFORME", 5000);

define("ESTADO_CREDITO_NORMAL",1);
define("ESTADO_CREDITO_ELIMINADO",1);
define("ESTADO_CREDITO_CANCELADO",2);
define("ESTADO_CREDITO_VIGENTE",3);
define("ESTADO_CREDITO_CADUCADO",4);
define("ESTADO_CREDITO_PRORROGADO",5);
define("ESTADO_CREDITO_DESISTIDO",6);

define("TIPO_CREDITO_NORMAL", 0);
