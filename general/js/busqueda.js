/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    

    function _jscrollshow(selector){
        
        var pane = $(selector);
        $(selector).jScrollPane({showArrows: true , arrowScrollOnHover: false, stickToBottom: true});

        $(selector+' .jspArrowUp, '+selector+' .jspArrowDown').bind('mouseenter mouseleave', function() {
          $(this).toggleClass('over');
        });
        
        $_actual_scroll = pane.data('jsp');

        var rtn = $_actual_scroll;   

        return  rtn;
    }
    
    function _jscrollshow_set(selector,arrows){
        
        var pane = $(selector);
        $(selector).jScrollPane({showArrows: arrows , arrowScrollOnHover: false, stickToBottom: true});

        $(selector+' .jspArrowUp, '+selector+' .jspArrowDown').bind('mouseenter mouseleave', function() {
          $(this).toggleClass('over');
        });
        
        $_actual_scroll = pane.data('jsp');

        var rtn = $_actual_scroll;   

        return  rtn;
    }
    
    
    
    function _jscrollremove(selector){
        var elem = $(selector);
        elem.removeClass("jspScrollable").removeAttr("style").removeAttr("tabindex").removeData("jsp");
        elem.unbind('.jsp');
        
        if (elem.find(".jspPane").length > 0){
            var _inner = elem.find(".jspPane").html();
            $(selector).html(_inner);
        }

    }
    