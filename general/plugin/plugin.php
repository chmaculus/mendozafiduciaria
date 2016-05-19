<?php

function get_plugin(&$_plug){
    
    $_plug['calendar']["css"] = array("calendar.css");
    $_plug['calendar']["js"] = array("calendar.js");

    $_plug['quicksearch']["css"] = array();
    $_plug['quicksearch']["js"] = array("quicksearch.js");

    $_plug['jalerts']["css"] = array("jalert.css");
    $_plug['jalerts']["js"] = array("jquery.jalert.js");
    
    $_plug['chosen']["css"] = array("chosen.css");
    $_plug['chosen']["js"] = array("chosen.jquery.min.js");

    $_plug['datatables']["css"] = array("demo_page.css","demo_table.css","media/css/TableTools.css");
    $_plug['datatables']["js"] = array("jquery.dataTables.min.js","dataTables.rowreordering.js","media/js/ZeroClipboard.js","media/js/TableTools.js");
    
    $_plug['validation']["css"] = array("validationEngine.jquery.css");
    $_plug['validation']["js"] = array("jquery.validationEngine.js","jquery.validationEngine-es.js");
    
    $_plug['fancybox']["css"] = array("jquery.fancybox.css");
    $_plug['fancybox']["js"] = array("jquery.fancybox.js");
    
    $_plug['jqgrid']["css"] = array("jqwidgets/styles/jqx.base.css","jqwidgets/styles/jqx.energyblue.css");
    $_plug['jqgrid']["js"] = array("jqwidgets/jqxcore.js","jqwidgets/jqxdata.js","jqwidgets/jqxbuttons.js","jqwidgets/jqxscrollbar.js","jqwidgets/jqxmenu.js","jqwidgets/jqxgrid.js","jqwidgets/jqxgrid.grouping.js","jqwidgets/jqxgrid.selection.js","scripts/gettheme.js","jqwidgets/jqxnumberinput.js","jqwidgets/globalization/globalize.js","jqwidgets/jqxgrid.columnsresize.js","jqwidgets/jqxgrid.edit.js","jqwidgets/jqxmaskedinput.js","jqwidgets/jqxlistbox.js","jqwidgets/jqxdropdownlist.js","jqwidgets/jqxgrid.filter.js","jqwidgets/jqxlistbox.js","jqwidgets/jqxgrid.aggregates.js","jqwidgets/jqxcheckbox.js","jqwidgets/jqxdata.export.js","jqwidgets/jqxgrid.export.js","jqwidgets/jqxpanel.js","jqwidgets/jqxtree.js","jqwidgets/jqxtooltip.js","jqwidgets/jqxgrid.sort.js");
    //$_plug['jqgrid']["js"]['backend/administracion/permisos'] = array("jqwidgets/jqxcore.js","jqwidgets/jqxdata.js","jqwidgets/jqxbuttons.js","jqwidgets/jqxscrollbar.js","jqwidgets/jqxmenu.js","jqwidgets/jqxgrid.js","jqwidgets/jqxgrid.grouping.js","jqwidgets/jqxgrid.selection.js","scripts/gettheme.js","jqwidgets/jqxnumberinput.js","jqwidgets/globalization/globalize.js","jqwidgets/jqxgrid.columnsresize.js","jqwidgets/jqxgrid.edit.js","jqwidgets/jqxmaskedinput.js","jqwidgets/jqxlistbox.js","jqwidgets/jqxdropdownlist.js","jqwidgets/jqxgrid.filter.js","jqwidgets/jqxlistbox.js","jqwidgets/jqxgrid.aggregates.js","jqwidgets/jqxcheckbox.js","jqwidgets/jqxtree.js");
    //$_plug['jqgrid']["js"]['creditos/cuotas'] = array("jqwidgets/jqxcore.js","jqwidgets/jqxtabs.js");

    $_plug['multiselect']["css"] = array("multiselect.css");
    $_plug['multiselect']["js"] = array("multiselect.js");
    

    $_plug['numeric']["css"] = array("numeric.css");
    $_plug['numeric']["js"] = array("jquery.numeric.js");
    
    $_plug['jmenu']["css"] = array("css/jmenu.css");
    $_plug['jmenu']["js"] = array("js/jMenu.jquery.js");
	
    $_plug['table2excel']["css"] = array();
    $_plug['table2excel']["js"] = array("jquery.table2excel.js");
    
}

?>