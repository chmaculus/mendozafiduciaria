<?php
class main_model extends model{
    
    function get_update_rol($id_rol){
        if (!$id_rol) return array();
        $this->_db->where("ID= '". $id_rol  ."'");
        $rtn = $this->_db->get_tabla("fid_roles");
        return $rtn?$rtn[0]["UPDATED"]:-1;
    }
    
    function update_rol_update($id_rol,$valor){
        if (!$id_rol) return array();
        $rtn = $this->_db->update("fid_roles", array("UPDATED"=>$valor),"ID= '". $id_rol  ."'");
        return $rtn;
    }
    
    function get_login($user, $pass){
        $this->_db->where("CodVen = ".$user." AND ClaVen = ".$pass);
        $rtn = $this->_db->get_tabla("vendedores");
        return $rtn;
    } 
    
    function cambiar_clave($user, $oldpass, $newpass){

        $this->_db->where("ID = ".$user." AND PWD = '".$oldpass."'");
        
        $old = $this->_db->get_tabla("BIENES_VENDEDORES");
        
        //file_put_contents('pppp.log',  $this->_db->last_query());
        
        if ($old){
            //update
            return $this->_db->update("BIENES_VENDEDORES", array("PWD"=>$newpass), "ID = ".$user );
        }else{
            return false;
        }
    }
    
    function get_datatable($fields, $index_id = 'id', $option = true){
        
        if ($this->_tablamod=='')
            return false;
        
                
        $aColumns = $fields;
	$sIndexColumn = $index_id;
	$sTable = $this->_tablamod;
                
	       
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
            $this->_db->limit($_GET['iDisplayStart'], $_GET['iDisplayLength'] );
	}

        
	if ( isset( $_GET['iSortCol_0'] )  )
	{
            $order_arr = array();
		for ( $i=0 ; $i < intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
                            $order_arr[] = $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".$_GET['sSortDir_'.$i];
			}
		}
            
                $this->_db->order_by(implode(",", $order_arr));		
	}
	
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
		$sWhere_arr = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere_arr[] = "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%'";
		}
                $this->_db->where("(".implode(" OR ",$sWhere_arr) .")");
	}
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
                        $this->_db->where("`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ");
                        
		}
	}
        //log_this('xxxx.log',print_r($aColumns,1));
    
        $this->prev_consulta($aColumns);
        $rResult = $this->_db->get_tabla($sTable);
        $this->_db->select("SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."`");
        

	$sQuery = "SELECT FOUND_ROWS() as contar";
        $rResultFilterTotal = $this->_db->query( $sQuery );
        $iFilteredTotal = $rResultFilterTotal[0]["contar"];

	$sQuery = "
		SELECT COUNT(`".$sIndexColumn."`) as contar1
		FROM   $sTable
	";
       
        $rResultTotal = $this->_db->query( $sQuery );
        $iTotal = $rResultTotal[0]['contar1'] ;
        
	$output = array(
                "sEcho" => 1,
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);

        foreach( $rResult as $aRow ){
            $row = array();
            $keys = array_keys($aRow);
            
            
            for ( $i=0 ; $i<count($keys) ; $i++ ){
                    if (  $keys[$i]  == "opciones" ){
                        if ($option){
                            $row[] = '<a data-id="'.$aRow[ $keys[$i] ].'" class="button-a red btnEditar tip-top" title="Editar" href="javascript:void(0);"><span class="edits icon-white"></span></a>' . ' '
                                    . '<a data-id="'.$aRow[ $keys[$i] ].'" class="button-a red btnBorrar tip-top" title="Borrar" href="javascript:void(0);"><span class="del icon-white"></span></a>';
                        }
                    }

                    else if ( $aRow[ $keys[$i]] != ' ' ){
                        $row[] = $aRow[ $keys[$i] ];

                    }
            }
            $output['aaData'][] = $row;
            
        }
        
	return $output;
        
    }
    function prev_consulta($rows){
        //log_this('xxxx.log',print_r($aColumns,1));
        $this->_db->select(implode(",",$rows).", ID as opciones");
    }
    
    function get_describe($tabla){
        $desc = $this->_db->query('describe '.$tabla);
        return $desc;
    }
    
    function get_tipos_operatoria($where=""){
        $this->_db->select("ID,TIPO");
        $rtn = $this->_db->get_tabla("fid_operacion_tipo",$where);
        return $rtn;
    }
    
    function get_rolesp($where=""){
        $this->_db->select("ID,DENOMINACION");
        $rtn = $this->_db->get_tabla("fid_roles",$where);
        return $rtn;
    }
    
    function get_areas(){
        
        $this->_db->select("ID,DENOMINACION");
        $rtn = $this->_db->get_tabla("fid_xareas");
        return $rtn;
    }
    
    function add_tipos_operatoria(){
        $arr_ins = array(
            "TIPO"=>'Nuevo Registro',
            "ESTADO"=>'1',
        );
        $id = $this->_db->insert("fid_operacion_tipo",$arr_ins);
        
        $this->_db->select('ID,TIPO');
        $rtn = $this->_db->get_tabla("fid_operacion_tipo",'ID='.$id);
        return $rtn;
    }
    
    function add_rolesp(){
        $arr_ins = array(
            "DENOMINACION"=>'Nuevo Registro',
        );
        $id = $this->_db->insert("fid_roles",$arr_ins);
        
        $this->_db->select('ID,DENOMINACION');
        $rtn = $this->_db->get_tabla("fid_roles",'ID='.$id);
        return $rtn;
    }
    
    function add_areas(){
        $arr_ins = array(
            "DENOMINACION"=>'Nuevo Registro',
        );
        $id = $this->_db->insert("fid_xareas",$arr_ins);
        
        $this->_db->select('ID,DENOMINACION');
        $rtn = $this->_db->get_tabla("fid_xareas",'ID='.$id);
        return $rtn;
    }
    
    
    function update_tipos_operatoria( $id, $tipo ){
        $arr_edit = array(
                        "ID"=>$id,
                        "TIPO"=>$tipo
                    );
        
        $rtn = $this->_db->update("fid_operacion_tipo",$arr_edit,"ID='". $id . "'");
        return $rtn;
    }
    
    function update_rolesp( $id, $denominacion ){
        $arr_edit = array(
                        "DENOMINACION"=>$denominacion
                    );
        $rtn = $this->_db->update("fid_roles",$arr_edit,"ID='". $id . "'");
        return $rtn;
    }
    
    function update_areasu( $id, $denominacion ){
        $arr_edit = array(
                        "DENOMINACION"=>$denominacion
                    );
        $rtn = $this->_db->update("fid_xareas",$arr_edit,"ID='". $id . "'");
        return $rtn;
    }
    
    function delete_tipos_operatoria($id){
        $rtn = $this->_db->delete("fid_operacion_tipo","ID='". $id . "'");
        return $rtn;
    }
    
    function delete_rolesp($id){
        $rtn = $this->_db->delete("fid_roles","ID='". $id . "'");
        return $rtn;
    }
    
    function delete_areasu($id){
        $rtn = $this->_db->delete("fid_xareas","ID='". $id . "'");
        return $rtn;
    }
    
    function get_checklist($where=""){
        $rtn = $this->_db->get_tabla("fid_checklist",$where);
        return $rtn;
    }
    
    function update_checklist( $id, $id_operatoria, $nombre ){
        $arr_edit = array("NOMBRE"=>$nombre);
        
        $rtn = $this->_db->update("fid_checklist",$arr_edit,"ID='". $id . "'");
        return $rtn;
    }
    
    function add_checklist(){
        $arr_ins = array(
            "NOMBRE"=>'Nuevo Registro',
            "ESTADO"=>'1',
        );
        $id = $this->_db->insert("fid_checklist",$arr_ins);
        
        //$this->_db->select('ID,TIPO');
        $rtn = $this->_db->get_tabla("fid_checklist",'ID='.$id);
        return $rtn;
    }
    
    function delete_checklist($id){
        $rtn = $this->_db->delete("fid_checklist","ID='". $id . "'");
        return $rtn;
    }
    
    function get_operatorias(){
        $this->_db->select("ot.TIPO as OTTIPO, o.*");
        $this->_db->join("fid_operacion_tipo ot","ot.ID=o.ID_TIPO_OPERATORIA");
        $rtn = $this->_db->get_tabla("fid_operatorias o");
        return $rtn;
    }
    
    function get_dependencia($tabla, $campo, $valor){
        $this->_db->select('count(*) as cont');
        $rtn = $this->_db->get_tabla( $tabla, $campo. '='.$valor);
        return $rtn;
    }
    
    function get_fideicomisos(){
        $this->_db->order_by("NOMBRE","ASC");
        $rtn = $this->_db->get_tabla("fid_fideicomiso");
        return $rtn;
    }
    
    function get_ultimoId($tabla){
        $this->_db->select("MAX(ID) AS ID");
        $rtn = $this->_db->get_tabla($tabla);
        if ($rtn)
            $rtn = $rtn[0]["ID"]+1;
        return $rtn;
    }
    
    function get_clientes(){
        $this->_db->order_by("RAZON_SOCIAL","ASC");
        $rtn = $this->_db->get_tabla("fid_clientes");
        return $rtn;
    }
    
    function get_escribanos(){
        /*
        SELECT e.NOMBRE FROM fid_entidades e
        inner join fid_entidadestipo et ON e.ID=et.ID_ENTIDAD
        where et.ID_TIPO=23
        */
        
        $this->_db->select("e.ID,e.NOMBRE");
        $this->_db->join("fid_entidadestipo et","e.ID=et.ID_ENTIDAD",'inner');
        $this->_db->order_by("NOMBRE","ASC");
        $rtn = $this->_db->get_tabla("fid_entidades e","et.ID_TIPO=23"); // 23 = entidades
        return $rtn;
    }
    
    function get_tasadores(){
        $this->_db->select("e.ID,e.NOMBRE");
        $this->_db->join("fid_entidadestipo et","e.ID=et.ID_ENTIDAD",'inner');
        $this->_db->order_by("NOMBRE","ASC");
        $rtn = $this->_db->get_tabla("fid_entidades e","et.ID_TIPO=22"); // 22 = tasadores
        return $rtn;
    }
    
    function get_menu_padres(){
        $rtn = $this->_db->get_tabla("fid_menu","ESPADRE='1'","ID");
        return $rtn;        
    }
    
    function get_menu_hijos($idpadre){
        $rtn = $this->_db->get_tabla("fid_menu","ESPADRE='0' AND PADRE='".$idpadre."'","ID");
        //log_this( 'aaaaa.log', $this->_db->last_query() );
        return $rtn;        
    }
    
    function get_menu_arbol(){
        $this->_db->select("ID AS id, IF(PADRE=0,'-1',PADRE) AS parentid, NOMBRE AS text, ID AS value");
        $rtn = $this->_db->get_tabla("fid_menu");
        return $rtn;
    }
    
    function get_etapas(){
        $this->_db->select("ID AS id, 99 as parentid, NOMBRE AS text, ID AS value");
        $this->_db->order_by("ID","ASC");
        $rtn = $this->_db->get_tabla("fid_etapas");
        return $rtn;
    }
    
    
    function actualizar_notas_activo_cero($id_nota){
        $arr_edit = array(
                            "ACTIVO"=>"0",
                    );
        $rtn = $this->_db->update("fid_traza",$arr_edit,"NOTA='". $id_nota . "'");
        return $rtn;
    }
    
    function guardar_traza_nota( $arr){
        $rtn = $this->_db->insert('fid_traza', $arr );
        return $rtn;
    }
    
    function cambiar_estado_antigua_traza_nota($id_traza){
        $arr_edit = array( "LEIDO"=>"0");
        $rtn = $this->_db->update("fid_traza",$arr_edit,"ID='". $id_traza . "'");
        return $rtn;
    }
    
    function actualizar_nota($id_nota,$arr_ed){
        $rtn = $this->_db->update("fid_nota_req",$arr_ed,"ID='". $id_nota . "'");
        return $rtn;
    }
    
}
?>