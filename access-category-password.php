<?php
/*
Plugin Name: Access Category Password
Text Domain: acpwd
Plugin URI: https://wordpress.org/plugins/access-category-password/
Description: Protects posts in categories by setting a unique Password for all restricted categories.
Author: Jojaba 
Version: 1.1
Author URI: http://perso.jojaba.fr/
*/

/**
 * Language init
 */
function acpwd_lang_init() {
 load_plugin_textdomain( 'acpwd', false, basename(dirname(__FILE__)) );
}
add_action('plugins_loaded', 'acpwd_lang_init');

/* ******************************* */
/* Backend of the plugin (options) */
/* ******************************* */

add_action( 'admin_menu', 'acpwd_options_add_page' );
/**
 * Load up the options page
 */
if( !function_exists('acpwd_options_add_page'))  {
	function acpwd_options_add_page() {
		add_options_page( 
			__( 'Access Category Password', 'acpwd' ), // Title for the page
			__( 'Access Category Password', 'acpwd' ), //  Page name in admin menu
			'manage_options', //  Minimum role required to see the page
			'acpwd_options_page', // unique identifier
			'acpwd_options_do_page'  // name of function to display the page
		);
		add_action( 'admin_init', 'acpwd_options_settings' );	
	}
}
/**
 * Create the options page
 */

if( !function_exists('acpwd_options_do_page'))  {
	function acpwd_options_do_page() { ?>

<div class="wrap">

        <h2><?php _e( 'Access Category Password Options', 'acpwd' ) ?></h2>  
        
        <?php 
        /*** To debug, here we can print the plugin options **/
        /* 
        echo '<pre>';
        $options = get_option( 'acpwd_settings_options' );
        print_r($options); 
        echo '</pre>';
        */
         ?>
        
        <form method="post" action="options.php">
        		<?php settings_fields( 'acpwd_settings_options' ); ?>
		  	<?php do_settings_sections('acpwd_setting_section'); ?>
		  	<p><input class="button-primary"  name="Submit" type="submit" value="<?php esc_attr_e(__('Save Changes','acpwd')); ?>" /></p>		
        </form>
        
</div>

<?php
	} // end acpwd_options_do_page
}

/**
 * Init plugin options to white list our options
 */
if( !function_exists('acpwd_options_settings'))  {
	function acpwd_options_settings(){
		/* Register acpwd settings. */
		register_setting( 
			'acpwd_settings_options',  //$option_group , A settings group name. Must exist prior to the register_setting call. This must match what's called in settings_fields()
			'acpwd_settings_options', // $option_name The name of an option to sanitize and save.
			'acpwd_options_validate' // $sanitize_callback  A callback function that sanitizes the option's value.
        );

		/** Add a section **/
		add_settings_section(
			'acpwd_option_main', //  section name unique ID
			'&nbsp;', // Title or name of the section (to be output on the page), you can leave nbsp here if not wished to display
			'acpwd_option_section_text',  // callback to display the content of the section itself
			'acpwd_setting_section' // The page name. This needs to match the text we gave to the do_settings_sections function call 
        );

		/** Register each option **/
		add_settings_field(
			'password',  //$id a unique id for the field 
			__( 'The password', 'acpwd' ), // the title for the field 
			'acpwd_func_password',  // the function callback, to display the input box 
			'acpwd_setting_section',  // the page name that this is attached to (same as the do_settings_sections function call).  
			'acpwd_option_main' // the id of the settings section that this goes into (same as the first argument to add_settings_section). 
        ); 
		
		add_settings_field(
			'impacted_categories', 
			__( 'Impacted categories', 'acpwd' ), 
			'acpwd_func_impacted_categories', 
			'acpwd_setting_section',  
			'acpwd_option_main' 
        ); 
        
             
        add_settings_field(
			'info_message',
			__( 'Info message', 'acpwd' ),
			'acpwd_func_info_message',
			'acpwd_setting_section',
			'acpwd_option_main'
        );
        
        add_settings_field(
			'error_message',
			__( 'Error message', 'acpwd' ),
			'acpwd_func_error_message',
			'acpwd_setting_section',
			'acpwd_option_main'
        );
        
         add_settings_field(
			'feed_name',
			__( 'Feed name', 'acpwd' ),
			'acpwd_func_feed_name',
			'acpwd_setting_section',
			'acpwd_option_main'
        );
        
        add_settings_field(
			'feed_desc_text',
			__( 'The feed item description text', 'acpwd' ),
			'acpwd_func_feed_desc_text',
			'acpwd_setting_section',
			'acpwd_option_main'
        );
    }
}

/** the theme section output**/
if( !function_exists('acpwd_option_section_text'))  {
	function acpwd_option_section_text(){
	echo '<p>'.__( 'Here you can set the options of Access Category Password plugin. Set a password, check the categories with restricted access (the posts in these categories will require a password authentication) and define the info and error messages displayed on frontend page. Finally, give a name (slug) to your new feed and the default item description for restricted posts.', 'acpwd' ).'</p>';
	}
}

/** The password field **/
if( !function_exists('acpwd_func_password'))  {
	function acpwd_func_password() {
		 /* Get the option value from the database. */
		$options = get_option( 'acpwd_settings_options' );
		$password = ($options['password'] != '') ? '' : 'acpwdpass' ;
		/* Echo the field. */ ?>
		<label for="paswword" > <?php _e( 'Password', 'acpwd' ); ?></label>
		<input type="password" id="limit_true" name="acpwd_settings_options[password]" value="<?php echo $password ?>" />
		<p class="description">
		    <?php _e( 'You can type a word or un sentence, no restriction. If not set, the default password is <strong>acpwdpass</strong>.', 'acpwd' ); ?>
        </p>
    <?php }
}

/** The Impacted categories Checkboxes **/
if( !function_exists('acpwd_func_impacted_categories'))  {
	function acpwd_func_impacted_categories(){
	/* Get the option value from the database. */
		$options = get_option( 'acpwd_settings_options' );
		$impacted_categories =  (is_array($options['impacted_categories'])) ? $options['impacted_categories'] : array();
		/* Echo the field. */ ?>
		<div id="impacted_categories">
		<?php
		$cats = get_categories(array('hide_empty' => 0));
        foreach( $cats as $cat ) { ?>
            <input type="checkbox" name="acpwd_settings_options[impacted_categories][]" value="<?php echo $cat->cat_ID ?>"<?php if (in_array($cat->cat_ID, $impacted_categories)) echo ' checked'; ?> /> <?php echo $cat->cat_name ?><br>
        <?php } ?>
		<p class="description">
		    <?php _e( 'Check the categories that you want to have password restricted post access.', 'acpwd' ); ?>
        </p>
        </div>
	<?php }
}


/** The info field */
if( !function_exists('acpwd_func_info_message'))  {
	function acpwd_func_info_message(){
	/* Get the option value from the database. */
		$options = get_option( 'acpwd_settings_options' );
		$info_option = ($options['info_message'] != '') ? $options['info_message'] : __('This content has restricted access, please type the password below and get access.', 'acpwd');
		/* Echo the field. */ ?>
		<textarea style="width: 50%; height: 100px;" id="info_message" name="acpwd_settings_options[info_message]"><?php echo esc_attr($info_option); ?></textarea>
		<p class="description">
		    <?php _e( 'The message displayed before the password form replacing the content (and the excerpt) of the article (HTML formating with allowed tags).', 'acpwd' ); ?><br>
		    <?php echo '<strong>'.__('Allowed tags:', 'acpwd').'</strong> '. allowed_tags() ?>
        </p>
	<?php }
}

/** The error mesage **/
if( !function_exists('acpwd_func_error_message'))  {
	function acpwd_func_error_message(){
	/* Get the option value from the database. */
		$options = get_option( 'acpwd_settings_options' );
		$error_message = ($options['error_message'] != '') ? $options['error_message'] : __('Sorry, but this is the wrong password.', 'acpwd');	
		/* Echo the field. */ ?>
		<input style="width: 95%;" type="text" id="message_error_option" name="acpwd_settings_options[error_message]" value="<?php echo esc_attr($error_message); ?>" />
		<p class="description">
		    <?php _e( 'The message that will display if the user typed the wrong password (HTML formating with allowed tags).', 'acpwd' ) ?><br>
		    <?php echo '<strong>'.__('Allowed tags:', 'acpwd').'</strong> '. allowed_tags() ?>
        </p>
    <?php }
}

/** The new feed name **/
if( !function_exists('acpwd_func_feed_name'))  {
	function acpwd_func_feed_name(){
	/* Get the option value from the database. */
		$options = get_option( 'acpwd_settings_options' );
		$feed_name_option = ($options['feed_name'] != '') ? $options['feed_name'] : __('acpwd-feed', 'acpwd');	
		/* Echo the field. */ ?>
		<input type="text" id="feed_name" name="acpwd_settings_options[feed_name]" value="<?php echo esc_attr($feed_name_option); ?>" />
		<p class="description">
		    <?php printf(__( 'The regular automatic generated WordPress Feeds are removed by this plugin and replaced by a custom feed taking in account the post access restriction. Here you can set the name of this new feed. Please use only lowercase letters (a-z), digits (0-9), dashes (-), and no spaces for this name. If you defined your permalinks to have nice URL in the <a href="options-permalink.php">Permalinks option page</a> you can get the feed on <code>%1$s</code> or <code>%2$s</code> (Don\'t forget to flush the rewrite rules after setting this new name by <a href="options-permalink.php">visiting this Permalinks option page</a>). Alternatively, this feed will be available on <code>%3$s</code>.', 'acpwd' ), home_url( '/' ).$feed_name_option, home_url( '/' ).'feed/'.$feed_name_option, home_url( '/' ).'?feed='.$feed_name_option); ?>
        </p>
    <?php }
}

/** The feed item description text **/
if( !function_exists('acpwd_func_feed_desc_text'))  {
	function acpwd_func_feed_desc_text(){
	/* Get the option value from the database. */
		$options = get_option( 'acpwd_settings_options' );
		$feed_desc_text_option = ($options['feed_desc_text'] != '') ? $options['feed_desc_text'] : __('Access to this post restricted, please go to the website to read it.', 'acpwd');	
		/* Echo the field. */ ?>
		<input type="text" style="width: 95%;" id="feed_desc_text" name="acpwd_settings_options[feed_desc_text]" value="<?php echo stripslashes($feed_desc_text_option); ?>" />
		<p class="description">
		    <?php _e( 'The feed item descriptions that belong to access restricted posts will be replaced by this sentence (HTML formating not allowed).', 'acpwd' ) ?>
        </p>
    <?php }
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
if( !function_exists('acpwd_options_validate'))  {
	function acpwd_options_validate( $input ) {
	$options = get_option( 'acpwd_settings_options' );
	
    /** Password crypting */
    if ($input['password'] != '')
	    $options['password'] = crypt($input['password'], $input['password']);
	
	/** Impacted Categories	validation **/
	$options['impacted_categories'] = $input['impacted_categories'];
		
	/** clean info field, HTML allowed for the format */
	$options['info_message'] = wp_filter_kses( $input['info_message'] );
	
	/** clean error message field, HTML allowed for the format */
	$options['error_message'] = wp_filter_kses( $input['error_message'] );
	
    /** validate the feed slug (name) */
	$options['feed_name'] = sanitize_title_with_dashes( $input['feed_name'] );
	
	/** clean feed desc text HTML not allowed */
	$options['feed_desc_text'] = wp_filter_nohtml_kses( $input['feed_desc_text'] );

	return $options;	
	}
}

/* ******************************* */
/* Frontend of the plugin          */
/* ******************************* */

/* Validation of the password */
function acpwd_session_check() {
	// Launching session if not launched
	if(!session_id()) session_start();
	// $_SESSION['acpwd_session'] = 0;
	// Checking password
	$acpwd_options = get_option('acpwd_settings_options');
    if(isset($_POST['pass']) && crypt($_POST['pass'], $_POST['pass']) == $acpwd_options['password'])
        $_SESSION['acpwd_session'] = 1;
    elseif (isset($_POST['pass']) && crypt($_POST['pass'], $_POST['pass']) != $acpwd_options['password']) {
    	$_POST['msg'] = ($acpwd_options['error_message'] != '') ? '<p style="color: darkred;">'.$acpwd_options['error_message'].'</p>' : '<p style="color: darkred;">'.__('Sorry, but this is the wrong password.', 'acpwd').'</p>';
    	$_SESSION['acpwd_session'] = 0;
    }
}
add_action('init', 'acpwd_session_check', 1);

/* Displaying the password form */
function acpwd_frontend_changes($content) {
    $acpwd_options = get_option('acpwd_settings_options');
	if ( in_category($acpwd_options['impacted_categories']) ) {
        if ($_SESSION['acpwd_session'] == 1) {
            $content = $content;
        } else { 
            $content = ($options['info_message'] != '') ? '<p>'.$options['info_message'].'</p>' : '<p>'.__('This content has restricted access, please type the password below and get access.', 'acpwd').'</p>';
            $content .= '
            <form name="login" action="'.$_SERVER['REQUEST_URI'].'" method="post"> 
                <input type="password" name="pass"> <input type="submit" value="'.__('Get access', 'acpwd').'"> 
            </form>'; 
            if (isset ($_POST['msg'])) $content .= $_POST['msg'];
        }
    }
	return $content;
}
add_filter( 'the_content', 'acpwd_frontend_changes' );
add_filter( 'get_the_excerpt', 'acpwd_frontend_changes' );

/* Remove automatic links to feeds */
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);

/* Disable WP generated feed */
remove_action( 'do_feed_rdf', 'do_feed_rdf', 10, 1 );
remove_action( 'do_feed_rss', 'do_feed_rss', 10, 1 );
remove_action( 'do_feed_rss2', 'do_feed_rss2', 10, 1 );
remove_action( 'do_feed_atom', 'do_feed_atom', 10, 1 );

/* Creating the new customized feed */
add_action('init', 'acpwd_feed_init');
function acpwd_feed_init(){
    $options = get_option( 'acpwd_settings_options' );
    $feed_name_option = ($options['feed_name'] != '') ? $options['feed_name'] : __('acpwd-feed', 'acpwd');	
    add_feed($feed_name_option, 'acpwd_feed_template');
}

function acpwd_feed_template() {
    $acpwd_dir = 'wp-content/plugins/access-category-password/';
    load_template( $acpwd_dir . 'acpwd_feed_template.php');
}
?>