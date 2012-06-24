<?php

add_action( 'admin_menu', 'nginx_cache_expire_menu' );

function nginx_cache_expire_menu() {
	add_options_page( 'Nginx Cache Expire Options', 'Nginx Expire Cache', 'administrator', 'nginx-cache-expire-options', 'nginx_cache_expire_options' );

	add_action( 'admin_init', 'register_mysettings' );
}

function register_mysettings() {
	//register our settings
	register_setting( 'myoptions-group', 'nce_cache_dir' );
	register_setting( 'myoptions-group', 'nce_cache_level' );
}

function nginx_cache_expire_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
?>
<div class="wrap">
<h2>Nginx Cache Expire</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'myoptions-group' ); ?>
    <?php do_settings_fields( 'myoptions-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Nginx Cache Path</th>
        <td><input type="text" name="nce_cache_dir" value="<?php echo get_option('nce_cache_dir'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Nginx Cache Levels</th>
        <td><input type="text" name="nce_cache_level" value="<?php echo get_option('nce_cache_level'); ?>" /></td>
        </tr>
        
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
    </p>

</form>
</div>
<?php } ?>

