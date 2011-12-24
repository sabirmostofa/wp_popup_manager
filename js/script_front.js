jQuery(document).ready(function($){
		
    
    $('#submit-wpvote').click(function(e){
        var thisText = $(this).text();
        var action = (thisText == 'Submit Rating' )? 'submit-wpvote':'update-wpvote';
        var selVal =$('#wp-rate-vote').val();
        e.preventDefault();
        $.ajax({
            type :  "post",
            url : wpvrSettings.ajaxurl,
            timeout : 5000,
            data : {
                'action' : action,
                'value': selVal,
                 'post_id':  wpvrSettings.post_id  
            },
            success :  function(data){
                   if(data == 'voted' )alert('You have already voted for this post');
                   if(data == 'nv')alert('Your Rating has been saved for this post');
                    if(data == 'updated')alert('Your Rating has been Updated for this post');
                    window.location.href=window.location.href;
                    
                    }
            })
        })






})
