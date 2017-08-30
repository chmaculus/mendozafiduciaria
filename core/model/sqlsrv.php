<?php

$connectionInfo = array(
    "UID" => $_config['database']['usersql'],
    "PWD" => $_config['database']['passwordsql'],
    "Database" => $_config['database']['databasesql']
    );

if (isset($connsql)){
    sqlsrv_close($connsql);
}
$connsql = sqlsrv_connect($_config["database"]["serversql"], $connectionInfo);


if ($connsql === false) {
    echo "Unable to connect.</br>";
    print_array(sqlsrv_errors());
    die();
}

class sqldatasql {

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
    private $_res_transaction = array();

    public function __construct($connsql) {
        $this->_conn = $connsql;
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
    }

    public function no_result() {
        $this->_result = false;
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

        $registros = sqlsrv_query($this->_conn, $sql);


        $result = array();

        $i = 0;
        if ($this->_result) {
            if ($registros !== false) {
                while (($reg = sqlsrv_fetch_array($registros, SQLSRV_FETCH_ASSOC)) != false) {
                    $result [] = $reg;
                }
            }
        }

        $this->reset();
        sqlsrv_free_stmt($registros);
        return $result;
    }

    public function query($sql, $ret = true) {
        $this->_sql = $sql;
        $_SESSION ['ParSQL'] = $this->_sql;
        if ($this->_show)
            echo $this->_sql;
        if ($ret) {
            $_SESSION ['ParSQL'] = $sql;
            timequery();
            $registros = sqlsrv_query($this->_conn, $sql);
            $time = timequery();
            $this->_stack_transaction($registros, $time);

            $result = array();
            $i = 0;
            while (($reg = sqlsrv_fetch_array($registros)) != false) {
                $result [$i++] = $reg;
            }
            return $result;
        } else {
            $_SESSION ['ParSQL'] = $sql;
            timequery();
            $res = sqlsrv_query($this->_conn, $sql);
            $time = timequery();
            $this->_stack_transaction($res, $time);
            if ($res)
                sqlsrv_free_stmt($res);
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
        $res = sqlsrv_query($this->_conn, $sql);
        $this->_stack_transaction($res);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    public function insert($tabla, $data) {
        $id = $this->save($tabla, $data, 0);
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
            $edits .= $key . "='" . $this->prepare($val) . "', ";
        }
        $edits = trim($edits, ", ");

        $this->_sql = "UPDATE " . $tabla . " SET " . $edits . " WHERE " . $this->_where;
        if ($this->_show)
            echo $this->_sql;

        $_SESSION ['ParSQL'] = $this->_sql;
        $res = sqlsrv_query($this->_conn, $this->_sql);
        $this->_stack_transaction($res);

        $this->reset();
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    public function save($tabla, $data, $tipo = 0) {

        $rtn = false;
        if ($tipo == 0) {
            $cols = "";
            $vals = "";
            foreach ($data as $key => $val) {
                $cols .= $key . ", ";
                $vals .= "'" . $this->prepare($val) . "', ";
            }

            $cols = trim($cols, ", ");
            $vals = trim($vals, ", ");
            $sql = "INSERT INTO " . $tabla . " (" . $cols . ") VALUES (" . $vals . ")";
        } else {
            $edits = "";
            $i = 0;
            $id_key = "";
            $id_val = "";
            foreach ($data as $key => $val) {
                if ($i == 0) {
                    $id_key = $key;
                    $id_val = $this->prepare($val);
                } else {
                    $edits .= $key . "='" . $this->prepare($val) . " ', ";
                }
                $i++;
            }
            $edits = trim($edits, ", ");

            $sql = "UPDATE " . $tabla . " SET " . $edits . " WHERE " . $id_key . " = " . $id_val;

            $rtn = true;
        }
        $this->_sql = $sql;
        $_SESSION ['ParSQL'] = $this->_sql;

        if ($this->_show)
            echo $this->_sql;
        $res = sqlsrv_query($this->_conn, $this->_sql);

        $this->_stack_transaction($res);

        if (!$res) {
            $rtn = false;
        } else {
            $rtn = true;//mysqli_insert_id($this->_conn);
        }

        return $rtn;
    }

    public function last_query() {
        return $this->_sql;
    }

    public function _stack_transaction($res, $time = 0) {
        if ($res === false) {
            $error = sqlsrv_errors($this->_conn);
            $e = new Exception ();
            $trace = $e->getTrace();
            $last_call = $trace [1];

            $FILE = isset($_SESSION ['FILE']) ? $_SESSION ['FILE'] : (isset($_SESSION ['FILE_SCRIPT']) ? $_SESSION ['FILE_SCRIPT'] : '');
            $err_string = "Archivo: " . $FILE . "\nFuncion: " . $trace [2] ['function'] . "\nLinea: " . $trace [1] ['line'] . "\nError : " . $error . "\n[ERR-700]Sql: " . $this->_sql;

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
        $pattern = "/'/"; 
        $replacement = "''"; 
        $output = preg_replace($pattern, $replacement, $val);
        
        return $output ;
    }

}


$_dbsql = new sqldatasql ( $connsql );
$global ['_dbsql'] = $_dbsql;
?>