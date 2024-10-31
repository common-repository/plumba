<?php
/**
 * Creates and handles metabox in editor post view.
 * Andreas Ek, 2012-10-01, created
 *
 */
class Plumba_Metabox {

	public function __construct() {
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
	}

	//Add metaboxes and attach save method
	public function admin_init() {
		add_meta_box( 'plumba_metabox_properties', __( 'Properties', 'plumba' ), array( &$this, 'show_properties' ), 'plumba_qa', 'normal', 'default' );
		add_meta_box( 'plumba_metabox_questions', __( 'Questions', 'plumba' ), array( &$this, 'show_questions' ), 'plumba_qa', 'normal', 'default' );
		add_meta_box( 'plumba_metabox_answers', __( 'Answers', 'plumba' ), array( &$this, 'show_answers' ), 'plumba_qa', 'normal', 'default' );

		// add a callback function to save any data a user enters in
		add_action( 'save_post', array( &$this, 'save_post' ) );
	}

	//Add client scripts!
	function admin_enqueue_scripts() {

		wp_enqueue_script( 'jquery' );

		// Main jQuery
		$src = WP_PLUGIN_URL . '/plumba/js/metabox.js';
		wp_deregister_script( 'plumba_metabox' );
		wp_register_script( 'plumba_metabox', $src );
		wp_enqueue_script( 'plumba_metabox' );
		wp_localize_script( 'plumba_metabox', 'plumba_option_colors', Plumba_Comments::get_option_colors( '' ) );
	}

	//Show the metabox in post edit
	public function show_questions() {
		global $post;
		$comments = new Plumba_Comments( $post->ID );
		?>
  <!--suppress ALL -->
  <div class="plumba_metabox_control">

      <div style="margin-bottom: 4px;">
          <input type="button" class="button-secondary" value="<?php _e( 'Add', 'plumba' ); ?>"
                 id="plumba_metabox_questions_add" />
          <input type="button" class="button-secondary" value="<?php _e( 'Remove', 'plumba' ); ?>"
                 id="plumba_metabox_questions_remove" />
          <input type="button" class="button-secondary" value="<?php _e( 'Reset', 'plumba' ); ?>"
                 id="plumba_metabox_questions_reset" />
      </div>

      <div id="plumba_metabox_questions_ui">

				<?php
		if ( $comments->questions ) {
			foreach ( $comments->questions as $key => $question ) {
				?>
					<div class="plumba_ui_div">
							<input type="text" class="text plumba_ui_dynamic" name="plumba_ui_dynamic[]"
										 value="<?php echo $question; ?>" />
							<select name="plumba_ui_color[]">
								<?php
								Plumba_Comments::wp_option_colors( $comments->colors[$key] );
								?>
							</select>
					</div>
				<?php
			}
		}
				?>

      </div>

  </div>
	<?php
		// create a custom nonce for submit verification later
		echo '<input type="hidden" name="plumba_metabox_noncename" value="' . wp_create_nonce( __FILE__ ) . '" />';

		return true;
	}

	//Show the metabox in post edit
	public function show_answers() {
		global $post;

		$comments = new Plumba_Comments( $post->ID );

		echo '<div class="plumba_metabox_control">';
		echo '<p><strong>Total votes:</strong> ' . $comments->total . '</p>';

		echo '<p><strong>First vote:</strong> ' . $comments->first_vote . '<br/>';
		echo '<strong>Latest vote:</strong> ' . $comments->latest_vote . '</p>';

		echo '<p>';
		if ( $comments->questions ) {
			foreach ( $comments->questions as $key => $question ) {
				if ( ! isset( $comments->answers[$key] ) )
					continue;
				echo '<strong>' . $question . ':</strong> ' . $comments->answers[$key] . ' votes, ' . $comments->percent[$key] . '%<br/>';
			}
		}
		echo '</p>';
		echo '</div>';

		return true;
	}

	//Show the metabox properties
	public function show_properties() {
		global $post;

		echo '<div class="plumba_metabox_control">';

		echo '<p><strong>' . __( 'Introduction text' , 'plumba' ) . ':</strong><br/>';
		echo '<textarea name="plumba_metabox_intro" rows="5" cols="40">';
		echo get_post_meta( $post->ID, 'plumba_intro', true );
		echo '</textarea>';
		echo '</p>';

		echo '</div>';

		return true;
	}

	//Save properties and qa to post
	function save_post( $post_id ) {
		global $post;

		if ( ! isset( $_POST['plumba_metabox_noncename'] ) )
			return false;

		// authentication checks
		// make sure data came from our meta box
		if ( ! wp_verify_nonce( $_POST['plumba_metabox_noncename'], __FILE__ ) )
			return $post_id;

		//Get intro text from the postback
		$intro = esc_attr( $_POST['plumba_metabox_intro'] );
		update_post_meta( $post->ID, 'plumba_intro', $intro );

		$store_questions = array();
		$questions       = $_POST['plumba_ui_dynamic'];

		foreach ( $questions as $question ) {
			if ( ! empty( $question ) )	$store_questions[] = esc_attr( $question );
		}

		update_post_meta( $post->ID, 'plumba_questions', $store_questions );

		$store_colors = array();
		$colors       = $_POST['plumba_ui_color'];
		if ( $colors ) {
			foreach ( $colors as $color ) {
				if ( ! empty( $color ) )
					$store_colors[] = esc_attr( $color );
			}
			update_post_meta( $post->ID, 'plumba_colors', $store_colors );
		}

		return $post_id;
	}


}

//end of class
?>
