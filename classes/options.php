<?php

class Plumba_Options {

	public function __construct() {
		//Add an options page
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}

	/** function/method
	 * Usage: hooking the plugin options/settings
	 * Arg(0): null
	 * Return: void
	 */
	public static function register() {
		register_setting( 'plumba_options', 'twitter_bootstrap' );
	}

	public function admin_menu() {
		// Create menu tab
		add_options_page( 'plumba_options', 'Plumba', 'manage_options', 'plumba_options', array( &$this, 'options_page' ) );
	}

	/** function/method
	 * Usage: show options/settings form page
	 * Arg(0): null
	 * Return: void
	 */
	public static function options_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'plumba' ) );
		}

		if ( isset( $_POST['submit'] ) ) {
			Plumba_Options::update_options();
		}

		$twitter_bootstrap = get_option( 'plumba_use_bootstrap', 1 );
		$interval          = get_option( 'plumba_interval', 3000 );
		$cookie_expire     = get_option( 'plumba_cookie_expire', 60 );

		?>
  <div class="wrap">
		<?php screen_icon(); ?>
      <form method="post" id="plumba_options_form" name="plumba_options_form">
          <h2>Plumba</h2>

          <p>
              <input name="twitter_bootstrap"
                     type="checkbox" <?php checked( $twitter_bootstrap, 1 ); ?> /> <?php _e( 'Include Twitter Bootstrap', 'plumba' ); ?>
              <br />
              <em><?php _e( 'If your theme doen\'t have Twitter Bootstrap included this plugin will attach missing files', 'plumba' ) ?></em>
          </p>

          <p>
						<?php _e( 'Ajax update interval in seconds', 'plumba' ); ?><br />
              <input class="text" name="interval" type="text" value="<?php echo $interval; ?>" /> <?php _e( '( 0 = disabled )' , 'plumba' ); ?>
          </p>

          <p>
						<?php _e( 'Add user cookie for x minutes', 'plumba' ); ?><br />
              <input class="text" name="cookie_expire" type="text" value="<?php echo $cookie_expire; ?>" /> <?php _e( '( 0 = no cookies )' , 'plumba' ); ?>
          </p>

          <p>
              <input type="submit" name="submit" value="<?php _e( 'Save settings', 'plumba' ); ?>"
                     class="button-primary" />
          </p>
      </form>
  </div>
	<?php

	}

	/**
	 * get page post and update options
	 */
	public static function update_options() {
		$twitter_bootstrap = isset( $_POST['twitter_bootstrap'] ) ? 1 : 0;
		update_option( 'plumba_use_bootstrap', $twitter_bootstrap );
		$interval = isset( $_POST['interval'] ) ? (int) esc_attr( $_POST['interval'] ) : 3000;
		update_option( 'plumba_interval', $interval );
		$cookie_expire = isset( $_POST['cookie_expire'] ) ? (int) esc_attr( $_POST['cookie_expire'] ) : 60;
		update_option( 'plumba_cookie_expire', $cookie_expire );
	}

}

?>
