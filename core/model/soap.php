<?php


class soap{
   private $_soap;
   public function __construct($conn){
       $this->_soap = $conn;
   }
   
   public function send_func(){
      
      
        $argumentos = func_get_args();
        $res = array();

        if (count($argumentos)==1 )
            $res = $this->_soap->$argumentos[0]();
        else if (count($argumentos)>1){
            $func = array_shift($argumentos) ;
            for($x = 0 ; $x < count($argumentos) ; $x++){
                $argumentos[$x] =  "'".$argumentos[$x]."'";
            }
            
            eval( '$res = $this->_soap->'.$func.'('.implode(",",$argumentos).' ) ;');
        }
        
        return $res;
   }


}

$conn = array();
foreach($_config['soap'] as $key=>$val){
   
    $conn = new SoapClient(
            $_config['soap'][$key]['wsdl'],
            array(
                "soap_version"=>SOAP_1_2,
                "login"=>$_config['soap'][$key]['login'],
                "password"=>$_config['soap'][$key]['password'],
                "location"=>$_config['soap'][$key]['location'],
                "uri"=>$_config['soap'][$key]['uri']
                )
            );

    $_soap = new soap($conn);
    $global['_soap'][$key] = $_soap;
}

?>