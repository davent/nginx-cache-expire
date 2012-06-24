<?php

add_action( 'admin_menu', 'nginx_cache_expire_menu' );

function nginx_cache_expire_menu() {
	add_options_page( 'Nginx Cache Expire Options', 'Nginx Expire Cache', 'manage_options', 'nginx-cache-expire-options', 'nginx_cache_expire_options' );

	add_action( 'admin_init', 'register_mysettings' );
}

function register_mysettings() {
	//register our settings
	register_setting( 'myoptions-group', 'nce_cache_dir', 'sanatize_nce_cache_dir' );
	register_setting( 'myoptions-group', 'nce_cache_level', 'sanatize_nce_cache_level' );
	register_setting( 'myoptions-group', 'nce_triggers' );
}

function sanatize_nce_cache_dir($input) {

	if( !is_dir( $input )) {
	
		add_settings_error( 'nce_cache_dir', 'nce_cache_dir_err', 'The Cache Path supplied does not appear to exist.', 'error' );
		return get_option('nce_cache_dir');

	}
	
	if( !is_writable( $input )) {
	
		add_settings_error( 'nce_cache_dir', 'nce_cache_dir_err', 'You do not appear to have permission to modify file in the supplied Cache Path.', 'error' );
		return get_option('nce_cache_dir');

	}

	return $input;

}

function sanatize_nce_cache_level($input) {

	$pattern = '/^[12]((:[12])?){1,2}$/';
	if( preg_match( $pattern, $input ) > 0 ) {

		return $input;

	}
	
	add_settings_error( 'nce_cache_level', 'nce_cache_level_err', 'You do not appear to have supplied a valid Nginx Cache Level. Please supply a Cache Level in the format: n, n:n or n:n:n where n is equal to either \'1\' or \'2\', e.g. 1:2:1', 'error' );
	
	return get_option('nce_cache_level');

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
	<h3>Expiry options</h3>
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
	<h3>Expiry triggers</h3>
	<?php $nce_triggers = get_option( 'nce_triggers' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Publish Post</th>
        <td><input type="checkbox" name="nce_triggers[publish_post]" value="1"<?php checked( isset( $nce_triggers['publish_post'] ) ); ?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Edit Post</th>
        <td><input type="checkbox" name="nce_triggers[edit_post]" value="1"<?php checked( isset( $nce_triggers['edit_post'] ) ); ?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Delete Post</th>
        <td><input type="checkbox" name="nce_triggers[deleted_post]" value="1"<?php checked( isset( $nce_triggers['deleted_post'] ) ); ?> /></td>
        </tr>
	</table>
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
    </p>

</form>
</div>
<?php } ?>
