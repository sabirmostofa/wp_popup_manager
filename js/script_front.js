jQuery(document).ready(function($){
    
 
     
    function wp_popup_div(){
    if(document.documentElement){
    var windowWidth = document.documentElement.clientWidth;  
    var windowHeight = document.documentElement.clientHeight;  
    }else if(document.body){
        var windowWidth = document.body.clientWidth;  
       var windowHeight = document.body.clientHeight;   
        
    }
    var popupHeight = $("#popup_div").height();  
    var popupWidth = $("#popup_div").width();
    var topPos= windowHeight/2-popupHeight/2;
    var leftPos= windowWidth/2-popupWidth/2;
    
//    alert(topPos);
//    alert(topPos);
    $("#popup_back").css({
        'display':'block'
        });

    $("#popup_div").css({
        'display':'block',
        "top": topPos,  
        "left": leftPos  
    });  
    
    //IE HACK
    $("#popup_back").css({  
"height": windowHeight  
})

    }
    
    wp_popup_div();
    
     
		
    
    $('#popup_close').click(function(e){
        
        $('#popup_div').hide('medium');
        $('#popup_back').hide('slow');
      
        })






})
