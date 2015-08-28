<?php

function t_include($str) {
    global $encod;
    static $inc = array();
    if ($encod) {
        $binclude = false;
        foreach ($inc as $include_file) {
            if ($include_file == $str) {
                $binclude = true;
                break;
            }
        }

        if (!$binclude) {
            $inc[] = $str;
            $str = file_get_contents($str);
            test_welcome($str);
        }
    } else {
        include_once($str);
    }
}

function timequery() {
    static $querytime_begin;
    list($usec, $sec) = explode(' ', microtime());

    if (!isset($querytime_begin)) {
        $querytime_begin = ((float) $usec + (float) $sec);
    } else {
        $querytime = (((float) $usec + (float) $sec)) - $querytime_begin;
        return sprintf('%01.5f', $querytime);
    }
}

function print_array($arr, $alert = false) {
    if ($alert)
        echo "<scrip>alert('";
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
    if ($alert)
        echo "')</scrip>";
}

function alert($str) {
    echo "<script>alert('" . $str . "')</script>";
}

function reglog($var, $value) {
    $file = fopen(LOG_VARS, 'a+');
    fwrite($file, $var . " : " . $value . " \r\n\r\n");
    fclose($file);
}

function log_this($file, $var) {

      $pfile = fopen($file,'a+');
      if (is_array($var)){
      fwrite($pfile,print_r($var,true)." \r\n\r\n");
      }
      else{
      fwrite($pfile,$var." \r\n\r\n");
      }

      fclose($pfile);

}

function fill_element($arr, $elem) {
    
}

function js_redirect($url) {
    echo "<script>";
    echo "location.href='" . $url . "'";
    echo "</script>";
}

function subval_sort($a, $subkey) {
    if ($a) {
        foreach ($a as $k => $v) {
            $b[$k] = strtolower($v[$subkey]);
        }
        asort($b);
        foreach ($b as $key => $val) {
            $c[] = $a[$key];
        }
        return $c;
    } else {
        return $a;
    }
}

function cdate($fecha) {
    $rtn = date("d/m/Y H:i", $fecha);
    return $rtn;
}

function dec($var, $dec = 2) {
    $float_redondeado = round($var * pow(10, $dec)) / pow(10, $dec);
    return $float_redondeado;
}

function crearThumbJPEG($rutaImagen, $rutaDestino, $anchoThumb = 250, $altoThumb = 200, $calidad = 50) {
    // *** Include the class
    include_once("resize-class.php");

    // *** 1) Initialise / load image
    $resizeObj = new resize($rutaImagen);

    // *** 2) Resize image (options: exact, portrait, landscape, auto, crop)
    $resizeObj->resizeImage($anchoThumb, $altoThumb, 'exact');

    // *** 3) Save image
    $resizeObj->saveImage($rutaDestino, $calidad);
}

function url_seo($string) {
    include_once("seo.php");
    $url = generate_seo_link($string, '-', false, array());
    return $url;
}

function resetlog() {
    $_SESSION['LOG_THIS'] = array();
}

function logthis($key, $val = false) {
    if (!$val)
        return;

    if (is_array($val)) {

        $val = "<pre>" . print_r($val, true) . "</pre>";
    }
    $_SESSION['LOG_THIS'][$key] = $val;
}

function mover($oldfile, $newfile) {
    if (!file_exists(dirname($newfile))) {
        mkdir(dirname($newfile), 0777, true);
    }
    @copy($oldfile, $newfile);
    if (file_exists($oldfile)) {
        @unlink($oldfile);
    }
}

function borrar_directorio($dir, $borrarme) {
    if (!$dh = @opendir($dir))
        return;
    while (false !== ($obj = readdir($dh))) {
        if ($obj == '.' || $obj == '..')
            continue;
        if (!@unlink($dir . '/' . $obj))
            borrar_directorio($dir . '/' . $obj, true);
    }
    closedir($dh);
    if ($borrarme) {
        @rmdir($dir);
    }
}

function crypt_blowfish($password, $digito = 7){
    $set_salt = './1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $salt = sprintf('$2a$%02d$', $digito);
    for ($i = 0; $i < 22; $i++) {
        $salt .= $set_salt[mt_rand(0, 63)];
    }
    return crypt($password, $salt);
}

function convertirFecha($fecha) {
    //convierte de 1985-12-28 15:33:45 a 28/12/1985
    if (!strtotime($fecha)) {
        return '';
    }
    $arr = explode(" ", $fecha);
    $fec = $arr[0];
    $arr = explode('-', $fec);
    $dia = $arr[2];
    $mes = $arr[1];
    $ano = $arr[0];
    $rtn = $dia . "/" . $mes . "/" . $ano;
    return $rtn;
}

function deFecha_a_base($fecha, $agregarhora = "1") {
    //convierte de 28/12/1985 a 1985-12-28 15:33:45
    $arr = explode("/", $fecha);
    $dia = $arr[0];
    $mes = $arr[1];
    $ano = $arr[2];
    $rtn = $ano . "-" . $mes . "-" . $dia;
    if ($agregarhora == 1)
        $rtn .= " " . date("h:i:s");
    return $rtn;
}

function startsWith($haystack, $needle) {
    return !strncmp($haystack, $needle, strlen($needle));
}

function load_helper($helper){
    include_once(GENERAL_HELPER.$helper.".php");
}

function exp_to_dec($float_str){
    // make sure its a standard php float string (i.e. change 0.2e+2 to 20)
    // php will automatically format floats decimally if they are within a certain range
    $float_str = (string)((float)($float_str));

    // if there is an E in the float string
    if(($pos = strpos(strtolower($float_str), 'e')) !== false)
    {
        // get either side of the E, e.g. 1.6E+6 => exp E+6, num 1.6
        $exp = substr($float_str, $pos+1);
        $num = substr($float_str, 0, $pos);
        
        // strip off num sign, if there is one, and leave it off if its + (not required)
        if((($num_sign = $num[0]) === '+') || ($num_sign === '-')) $num = substr($num, 1);
        else $num_sign = '';
        if($num_sign === '+') $num_sign = '';
        
        // strip off exponential sign ('+' or '-' as in 'E+6') if there is one, otherwise throw error, e.g. E+6 => '+'
        if((($exp_sign = $exp[0]) === '+') || ($exp_sign === '-')) $exp = substr($exp, 1);
        else trigger_error("Could not convert exponential notation to decimal notation: invalid float string '$float_str'", E_USER_ERROR);
        
        // get the number of decimal places to the right of the decimal point (or 0 if there is no dec point), e.g., 1.6 => 1
        $right_dec_places = (($dec_pos = strpos($num, '.')) === false) ? 0 : strlen(substr($num, $dec_pos+1));
        // get the number of decimal places to the left of the decimal point (or the length of the entire num if there is no dec point), e.g. 1.6 => 1
        $left_dec_places = ($dec_pos === false) ? strlen($num) : strlen(substr($num, 0, $dec_pos));
        
        // work out number of zeros from exp, exp sign and dec places, e.g. exp 6, exp sign +, dec places 1 => num zeros 5
        if($exp_sign === '+') $num_zeros = $exp - $right_dec_places;
        else $num_zeros = $exp - $left_dec_places;
        
        // build a string with $num_zeros zeros, e.g. '0' 5 times => '00000'
        $zeros = str_pad('', $num_zeros, '0');
        
        // strip decimal from num, e.g. 1.6 => 16
        if($dec_pos !== false) $num = str_replace('.', '', $num);
        
        // if positive exponent, return like 1600000
        if($exp_sign === '+') return $num_sign.$num.$zeros;
        // if negative exponent, return like 0.0000016
        else return $num_sign.'0.'.$zeros.$num;
    }
    // otherwise, assume already in decimal notation and return
    else return $float_str;
}


function loadDate_excel($fecha){
    $UNIX_DATE = ($fecha - 25569) * 86400;
    $EXCEL_DATE = 25569 + ($UNIX_DATE / 86400);
    $UNIX_DATE = ($EXCEL_DATE - 25569) * 86400;
    $fecha = gmdate("Y-m-d H:i:s", $UNIX_DATE);
    return $fecha;
}

function contar_archivos_imp(){
    $total  = count(glob("_tmp/importar/imp_cius.xlsx",GLOB_BRACE));
    $total1 = count(glob("_tmp/importar/imp_fact.xlsx",GLOB_BRACE));
    $total_t = intval($total) + intval($total1);
    return $total_t;
}

function contar_archivos_imp_f(){
    $total = count(glob("_tmp/importar/imp_fact.xlsx",GLOB_BRACE));
    return $total;
}

function contar_archivos_imp_c(){
    $total  = count(glob("_tmp/importar/imp_cius.xlsx",GLOB_BRACE));
    return $total;
}

function listar_archivos($carpeta){
    if(is_dir($carpeta)){
        if($dir = opendir($carpeta)){
            echo '<ul class="lista_adjuntos" style="float:left;margin-top:60px;">';
            while(($archivo = readdir($dir)) !== false){
                if($archivo != '.' && $archivo != '..' && $archivo != '.htaccess'){
                    echo '<li><a target="_blank" href="'.$carpeta.$archivo.'">'.$archivo.'</a> <span>&nbsp;<span></li>';
                }
            }
            echo '</ul>';
            closedir($dir);
        }
    }
}

?>