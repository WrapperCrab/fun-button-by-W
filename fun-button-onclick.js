var localClicks=0;//incremented at each click. Once time passes, this value is added to database

jQuery(document).ready(function($){
    if (jQuery("#fun-button").length>0){
        jQuery("#fun-button").click(function($){
            increment_local_clicks();
            // Update the visual right when the user clicks, update the database later
            js_increment_visual_clicks();
        });

        //send localClicks to database every 1 second
        window.setInterval(function(){
            var currentLocalClicks = localClicks
            js_increase_num_clicks(currentLocalClicks);
            js_increase_num_user_clicks(currentLocalClicks);
            localClicks-=currentLocalClicks;
        }, 1000);

        //update the fun button every 10 seconds
        window.setInterval(function(){
            js_update_fun_button();
            js_update_user_clicks();
        }, 10000);
    }
});

function increment_local_clicks(){
    localClicks++;
}
function js_increase_num_clicks(clicks){
    jQuery.ajax({
        type:"POST",
        url: ajax_object.ajaxurl,
        data:{
            action: 'increase_num_clicks',
            'clicks':clicks,
        },
        success:function(response){//response is what was echoed from action: function above
            // jQuery("#fun-button").html(response);
        }
    });
}
function js_increase_num_user_clicks(clicks){
    jQuery.ajax({
        type:"POST",
        url: ajax_object.ajaxurl,
        data:{
            action: 'increase_num_user_clicks',
            'clicks':clicks,
        },
        success:function(response){
            // var prefix = "Your Clicks: ";
            // var newText = prefix.concat(response.toString());
            // jQuery("#fun-button-user-clicks").html(newText);
        }
    });
}

function js_increment_visual_clicks(){
    // Update the visual number of clicks right as the user clicks. No ajax or sql needed so should be fast.
    var oldClicks = parseInt(jQuery("#fun-button").html());
    jQuery("#fun-button").html(oldClicks+1);

    var oldUserClicks = parseInt( jQuery("#fun-button-user-clicks").html().replace('Your Clicks: ', '') );

    if (isNaN(oldUserClicks)){
        return
    }else{
        jQuery("#fun-button-user-clicks").html("Your Clicks: " + (oldUserClicks+1).toString() );
    }
}

function js_update_fun_button(){
    jQuery.ajax({
        type:"POST",
        url: ajax_object.ajaxurl,
        data:{
            action: 'get_num_clicks'
        },
        success:function(response){
            jQuery("#fun-button").html(response);
        }
    });
}
function js_update_user_clicks(){
    jQuery.ajax({
        type:"POST",
        url: ajax_object.ajaxurl,
        data:{
            action: 'get_num_user_clicks'
        },
        success:function(response){
            var prefix = "Your Clicks: ";
            var newText = prefix.concat(response.toString());
            jQuery("#fun-button-user-clicks").html(newText);
        }
    });
}
