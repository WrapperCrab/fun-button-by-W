<?php
/**
* Plugin name: Fun Button by W
*/

//hooks 'n stuff
register_activation_hook(__FILE__,'create_fun_button_table');
register_deactivation_hook(__FILE__,'delete_fun_button_data');
add_action('wp_enqueue_scripts','fun_button_onclick_init');
function fun_button_onclick_init(){
    //load the scripts needed for the plugin
    wp_register_script('fun-button-onclick-js',"https://www.mowinpeople.com/wp-content/plugins/fun-button-by-W/fun-button-onclick.js",array('jquery'));
    wp_enqueue_script('fun-button-onclick-js');
    wp_localize_script('fun-button-onclick-js','ajax_object',array('ajaxurl' => admin_url('admin-ajax.php')));
}

//let ajax call functions
//add_action(wp_ajax_(func called in ajax), func to call here);
add_action('wp_ajax_get_num_clicks','get_num_clicks_ajax');
add_action('wp_ajax_nopriv_get_num_clicks','get_num_clicks_ajax');
add_action('wp_ajax_get_num_user_clicks','get_num_user_clicks_ajax');
add_action('wp_ajax_nopriv_get_num_user_clicks','get_num_user_clicks_ajax');
add_action('wp_ajax_increase_num_clicks','increase_num_clicks_ajax');
add_action('wp_ajax_nopriv_increase_num_clicks','increase_num_clicks_ajax');
add_action('wp_ajax_increase_num_user_clicks','increase_num_user_clicks_ajax');
add_action('wp_ajax_nopriv_increase_num_user_clicks','increase_num_user_clicks_ajax');

add_shortcode('fun-button','show_fun_button');
add_shortcode('fun-button-user-clicks','show_fun_button_user_clicks');

function create_fun_button_table(){
    global $wpdb;
    $table_name = $wpdb->prefix.'FunButtonClicks';
    $charset_collate = $wpdb->get_charset_collate();
    //create table
    $sql = "CREATE TABLE {$table_name} (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        numClicks mediumint(9) NOT NULL,
        PRIMARY KEY (id)
    ) {$charset_collate}";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    //initialize numClicks
    initialize_fun_button_table();
}
function initialize_fun_button_table(){
    global $wpdb;
    $table_name = $wpdb->prefix.'FunButtonClicks';
    $wpdb->insert(
        $table_name,
        array('numClicks' => '0')
    );
}
function create_num_user_clicks(){
    //The user has logged in. Check if they have a saved num clicks value. If not, create one
    if (is_user_logged_in()){
        //This user is logged in
        $userID = get_current_user_id();
        if ($userID===0){
            return;
        }
        global $wpdb;
        $numUserClicks = get_user_meta($userID,"numClicks",true);//!!!Might fail if called twice quickly
        if ($numUserClicks===""){
            //the field does not exist! Create it
            add_user_meta($userID,"numClicks",0);
        }
    }
}

function get_num_clicks(){
    global $wpdb;
    $table_name = "{$wpdb->prefix}FunButtonClicks";
    $sql = "SELECT numClicks FROM {$table_name} WHERE id=1";
    $numClicks = $wpdb->get_var($sql);
    return $numClicks;
}
function get_num_clicks_ajax(){
    $numClicks = get_num_clicks();
    echo $numClicks;
    wp_die();
}

function increase_num_clicks_ajax(){
    $clicks = $_POST['clicks'];
    //update database
    if ($clicks!==0){
        global $wpdb;
        $table_name = "{$wpdb->prefix}FunButtonClicks";
        $sql = "UPDATE {$table_name} SET numClicks = numClicks + {$clicks}";
        $wpdb->query($sql);
    }
    //return the new value
    $numClicks = get_num_clicks();
    echo $numClicks;
    wp_die();
}

function get_num_user_clicks(){
    if (is_user_logged_in()){
        $userID = get_current_user_id();

        $numUserClicks = get_user_meta($userID,'numClicks',true);
        if ($numUserClicks===""){
            //This value does not exist in database
            create_num_user_clicks();
            return 0;
        }else{
            return $numUserClicks;
        }
    }else{
        return "You are not logged in";
    }
}
function get_num_user_clicks_ajax(){
    $numUserClicks = get_num_user_clicks();
    echo $numUserClicks;
    wp_die();
}
function increase_num_user_clicks_ajax(){
    $clicks = $_POST['clicks'];
    //returns the new number of clicks
    if (is_user_logged_in()){
        if ($clicks!==0){
            global $wpdb;
            $userID = get_current_user_id();
            $table_name = $wpdb->prefix . "usermeta";
            //Only update first such entry (in case of duplicates)
            $sql_get_id = "SELECT umeta_id FROM {$table_name} WHERE (user_id=%d AND meta_key='numClicks') ORDER BY umeta_id ASC LIMIT 1";
            $sql_increment_clicks = "UPDATE {$table_name} SET meta_value = meta_value+{$clicks} WHERE (umeta_id = %d)";
            $firstUserMetaID = $wpdb->get_var($wpdb->prepare($sql_get_id,$userID));
            $wpdb->query($wpdb->prepare($sql_increment_clicks,$firstUserMetaID));
        }
        //return the new value
        $numUserClicks = get_num_user_clicks();
        echo $numUserClicks;
        wp_die();
    }else{
        echo "You are not logged in";
        wp_die();
    }
}

function delete_fun_button_data(){
    // Turned off cuz I do not want this to be deleted
    // delete_fun_button_table();
    // delete_fun_button_user_clicks();
}
function delete_fun_button_table(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'FunButtonClicks';
    $sql = "DROP TABLE IF EXISTS {$table_name}";
    $wpdb->query($sql);
}
function delete_fun_button_user_clicks(){
    #delete all user meta data for numClicks
    $users = get_users();
    foreach($users as $user){
        delete_user_meta($user->ID,"numClicks");
    }
}

function show_fun_button(){
    ob_start();
    ?>
    <html>
    <body>
        <button id="fun-button" style="
        width:35vw;height:35vw;margin:0 auto;display:block;border-radius:400px;font-size:7vw;
        "><?php echo get_num_clicks();?></button>
    </body>
    </html>
    <?php
    return ob_get_clean();
}
function show_fun_button_user_clicks(){
    ob_start();
    ?>
    <html>
    <body>
        <h2 id="fun-button-user-clicks" style="
        text-align:center;display:block;font-size:4vw;
        "><?php echo "Your Clicks: ";?><?php echo get_num_user_clicks();?></h2>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

?>
