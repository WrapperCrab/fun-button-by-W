
var localClicks=0;//incremented at each click. Once time passes, this value is added to database

jQuery(document).ready(function($){
    jQuery("#fun-button").click(function($){
        increment_local_clicks();
    });
    //send localClicks to database every 1 second
    window.setInterval(function(){
        var currentLocalClicks = localClicks
        js_increase_num_clicks(currentLocalClicks);
        js_increase_num_user_clicks(currentLocalClicks);
        localClicks-=currentLocalClicks;
    }, 1000);
    //update the fun button every 1 second
    window.setInterval(function(){
        js_update_fun_button();
        js_update_user_clicks();
    }, 1000);
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
            jQuery("#fun-button").html(response);
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
            var prefix = "Your Clicks: ";
            var newText = prefix.concat(response.toString());
            jQuery("#fun-button-user-clicks").html(newText);
        }
    });
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
