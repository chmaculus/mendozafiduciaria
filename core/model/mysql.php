<?php

ini_set('display_errors', 'off');
$conn = mysqli_connect($_config ['database'] ['server'], $_config ['database'] ['user'], $_config ['database'] ['password'], $_config ['database'] ['database']);

if (!$conn) {
    echo "Unable to connect.</br>";
    print_array(mysqli_error($conn));
    die();
}
mysqli_set_charset($conn, 'utf8');

class sqldata {

    private $_having;
    private $_where;
    private $_select;
    private $_join;
    private $_sql;
    private $_limit;
    private $_group;
    private $_conn;
    private $_result = true;
    private $_showret = false;
    private $_key_index = false;
    private $_res_transaction = array();

    public function __construct($conn) {
        $this->_conn = $conn;
        $this->reset();
    }

    public function init() {

        $this->_res_transaction = array();
    }

    public function commit() {
        
    }

    public function reset() {
        $this->_where = array();
        $this->_select = " * ";
        $this->_join = "";
        $this->_limit = "";
        $this->_having = "";
        $this->_group = "";
        $this->_show = false;
        $this->_showret = false;
        $this->_orderby = "";
        $this->_result = true;
        $this->_key_index = false;
    }

    public function no_result() {
        $this->_result = false;
    }
    
    public function set_key($key){
        $this->_key_index = $key;
    }

    public function group_by($group) {
        $this->_group = $group;
        return $this;
    }

    public function order_by($order, $ord = "asc") {
        $this->_orderby = " ORDER By " . $order . " " . $ord;
        return $this;
    }

    public function where($where) {
        $this->_where [] = $where;
        return $this;
    }

    public function having($having) {
        $this->_having [] = $having;
        return $this;
    }

    public function limit($count, $offset) {
        $this->_limit = " LIMIT " . $count . ", " . $offset;
        return $this;
    }

    public function select($select = ' * ') {
        $this->_select = $select;
        return $this;
    }

    public function join($tabla, $on, $tipo = "") {
        $this->_join .= " " . $tipo . " JOIN " . $tabla . " ON ( " . $on . " ) ";
        return $this;
    }

    public function show_sql($sql) {
        if ($this->_showret) {
            return $sql;
        } else {
            echo $sql;
        }
    }

    public function get_tabla($tab, $where = false, $order = false) {
        
        if ($order)
            $order = "ORDER BY " . $order;
        else if ($this->_orderby)
            $order = $this->_orderby;

        if ($where)
            $where = "WHERE " . $where;
        else if ($this->_where)
            $where = "WHERE " . implode(" AND ", $this->_where);

        if ($this->_group)
            $this->_group = " GROUP BY " . $this->_group;

        if ($this->_having)
            $this->_having = " HAVING " . implode(" AND ", $this->_having);

        $sql = "SELECT " . $this->_select . " FROM $tab " . $this->_join . " " . $where . " " . $this->_group . " " . $this->_having . " " . $order . " " . $this->_limit;

        $this->_sql = $sql;
        if ($this->_show)
            $this->show_sql($this->_sql);
        $_SESSION ['ParSQL'] = $this->_sql;
        
        $this->log_this("get_tabla", $sql);

        // timequery();
        $registros = mysqli_query($this->_conn, $sql);
        // $time = timequery();

        $this->_stack_transaction($registros);

        $result = array();

        $i = 0;
        if ($this->_result) {
            if ($registros !== false) {
                if (!$this->_key_index){
                    while (($reg = mysqli_fetch_assoc($registros)) != false) {
                        $result[] = $reg;
                    }
                }else{
                    while (($reg = mysqli_fetch_assoc($registros)) != false) {
                        $result[$reg[$this->_key_index]] = $reg;
                    }
                }
            }
        }

        $this->reset();
        mysqli_free_result($registros);
        return $result;
    }
    
    function get_row($tab, $where = false, $order = false){
        $result = $this->get_tabla($tab, $where, $order);;
        if ($result){
            return $result[0];
        }
        else{
            return array();
        }
    }

    public function query($sql, $ret = true) {
        $this->log_this("query", $sql);
        $this->_sql = $sql;
        $_SESSION ['ParSQL'] = $this->_sql;
        if ($this->_show)
            echo $this->_sql;
        if ($ret) {
            $_SESSION ['ParSQL'] = $sql;
            timequery();
            $registros = mysqli_query($this->_conn, $sql);
            $time = timequery();
            $this->_stack_transaction($registros, $time);

            $result = array();
            $i = 0;
            while (($reg = mysqli_fetch_assoc($registros)) != false) {
                $result [$i++] = $reg;
            }
            return $result;
        } else {
            $_SESSION ['ParSQL'] = $sql;
            timequery();
            $res = mysqli_query($this->_conn, $sql);
            $time = timequery();
            $this->_stack_transaction($res, $time);
            if ($res)
                mysqli_free_result($res);
        }
    }

    public function delete($tabla, $where = false) {
        if ($where)
            $where = " WHERE " . $where;

        $sql = "delete from " . $tabla . $where;
        $_SESSION ['ParSQL'] = $sql;

        $this->_sql = $sql;
        if ($this->_show)
            echo $this->_sql;

        timequery();
        $res = mysqli_query($this->_conn, $sql);
        $this->log_this("delete", $sql);
        $this->_stack_transaction($res);
    }

    public function insert($tabla, $data, $not = array()) {
        $id = $this->save($tabla, $data, $not);
        return $id;
    }

    public function show($ret = false) {
        $this->_show = true;
        $this->_showret = $ret;
    }

    public function update($tabla, $data, $where = false) {
        if ($where)
            $this->_where = $where;

        $edits = "";
        foreach ($data as $key => $val) {
            $val = trim($val);
            if (strpos($val, "]") !== false) {

                $val = str_replace(array("[", "]"), "", $val);

                $val = $val . ", ";
            } else {

                $val = "'" . $val . "'";
            }
            $edits .= $key . "=" . $val . ", ";
        }
        $edits = trim($edits, ", ");

        $this->_sql = "UPDATE " . $tabla . " SET " . $edits . " WHERE " . $this->_where;
        $this->log_this("insert", $this->_sql);
        if ($this->_show)
            echo $this->_sql;

        $_SESSION ['ParSQL'] = $this->_sql;
        $res = mysqli_query($this->_conn, $this->_sql);
        $this->_stack_transaction($res);

        $this->reset();
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    public function save($tabla, $data, $not = array()) {

        $rtn = false;
        $cols = "";
        $vals = "";

        
        if (isset($data[0])){
            $first = $data[0];
            $keys_col = array_keys($first);
            $rows = array();
            foreach($data as $row){

                $val_arr = array();
                foreach($keys_col as $key_k){
                    $tmp = $row[$key_k];

                        $tmp= $this->prepare($tmp);

                    if (strpos($tmp , "]") !== false) {
                        $tmp = trim(trim($tmp , "["), "]");
                        $val_arr[]= $tmp ;
                    } else {
                        $val_arr[]= "'" . $tmp . "' ";
                    }

                }
                $rows[] = "(".implode(",",$val_arr).")";
            }

            $cols = implode(",",$keys_col);
            $vals = implode(",", $rows);
            $sql = "INSERT INTO " . $tabla . " (" . $cols . ") VALUES ". $vals ;
            //echo $sql;

        }
        else{
            foreach ($data as $key => $val) {
                $cols .= $key . ", ";
                $val = $this->prepare($val);

                if (strpos($val, "]") !== false) {
                    $val = trim(trim($val, "["), "]");
                    $vals .= $val . ", ";
                } else {
                    $vals .= "'" . $val . "', ";
                }
            }
            $cols = trim($cols, ", ");
            $vals = trim($vals, ", ");
            $sql = "INSERT INTO " . $tabla . " (" . $cols . ") VALUES (" . $vals . ")";
            //echo $sql;                
        }

        $this->_sql = $sql;
        $this->log_this("insert", $sql);

        if ($this->_show)
            echo $this->_sql;

        $res = mysqli_query($this->_conn, $this->_sql);

        $this->_stack_transaction($res);
        if (!$res) {
            $rtn = false;
        } else {
            $rtn = mysqli_insert_id($this->_conn);
        }
        return $rtn;
    }

    public function show_columns($table) {
        $this->_sql = $sql = "SHOW COLUMNS FROM " . $table;
        $res = mysqli_query($this->_conn, $this->_sql);
        $result = array();
        if ($res !== false) {
            while (($reg = mysqli_fetch_assoc($res)) != false) {
                $result [] = $reg;
            }
        }
        return $result;
    }

    public function last_query() {
        return $this->_sql;
    }

    public function _stack_transaction($res, $time = 0) {
        if ($res === false) {
            $error = mysqli_error($this->_conn);
            $e = new Exception ();
            $trace = $e->getTrace();
            $last_call = $trace [1];

            $archivo = isset($_SESSION ['FILE']) ? $_SESSION ['FILE'] : 'archivo-no-definido';
            $err_string = "Archivo: " . $archivo . "\nFuncion: " . $trace [2] ['function'] . "\nLinea: " . $trace [1] ['line'] . "\nError : " . $error . "\n[ERR-700]Sql: " . $this->_sql;

            include_once ("core/view/err_controller.php");
            view_error(str_replace("\n", "<br/>", $err_string));
            $error_trace = $err_string;
            write_error_log($error_trace);
            die();
            // get_caller();
        } else {
            
        }
    }

    public function prepare($val) {

        $result = mysqli_real_escape_string($this->_conn, $val);

        return $result;
    }
    
    function log_this($accion, $var) {
        $dir = dirname(__FILE__) . "/logs/";
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $file = $dir . $accion . '_' . date('Y-m-d') . ".log";
        try {
            if ($pfile = @fopen($file,'a+')) {
                if (is_array($var)) {
                    fwrite($pfile, print_r($var, TRUE) . "\n");
                } else {
                    fwrite($pfile, $var . "\n");
                }
                fclose($pfile);
            }
        
        } catch (Exception $ex) {
            //print_r($ex);die;
        }

        

    }

}

function set_new_connection($server, $user, $password, $db){
    $conn = mysqli_connect($server, $user, $password, $db);   
    $_db = new sqldata($conn);
    return $_db;
}

$_db = new sqldata($conn);
$global ['_db'] = $_db;
?>