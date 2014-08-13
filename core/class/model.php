<?php

class model {
    public $_db;
    public $_soap;
    
    
    public function __construct(&$global){
        
        if (isset($global['_db']))
            $this->_db = $global['_db'];
        else{
            $this->_db = "";
        } 
        
        if (isset($global['_dbsql']))
            $this->_dbsql = $global['_dbsql'];
        else{
            $this->_dbsql = "";
        } 

        if (isset($global['_soap']))
            $this->_soap = $global['_soap'];
        else{
            $this->_soap = "";
        }
    }
    public function last_query(){
        $this->_db->last_query();
    }
}
?>