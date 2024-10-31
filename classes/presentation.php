<?php
/**
 * This class serves with functions for the presentation outside wp-admin
 *
 * @author plumba
 */
class Plumba_Presentation {

	function __construct() {

		//Add public js functions
		add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts' ), 20 );

		add_action( 'wp_head', array( &$this, 'wp_head' ) );

		add_filter( 'the_content', array( &$this, 'override_content' ) );

	}


	//Add client scripts!
	function wp_enqueue_scripts() {

		wp_enqueue_script( 'jquery' );

		$twitter_bootstrap = get_option( 'plumba_use_bootstrap', 1 );

		if ( $twitter_bootstrap ) {
			$src = WP_PLUGIN_URL . '/plumba/bootstrap/js/bootstrap.min.js';
			wp_deregister_script( 'bootstrap_js' );
			wp_register_script( 'bootstrap_js', $src );
			wp_enqueue_script( 'bootstrap_js' );

			$src = WP_PLUGIN_URL . '/plumba/bootstrap/css/bootstrap.min.css';
			wp_register_style( 'bootstrap_css', $src );
			wp_enqueue_style( 'bootstrap_css' );
		}

		// Main jQuery
		$src = WP_PLUGIN_URL . '/plumba/js/public.js';
		wp_deregister_script( 'plumba_public' );
		wp_register_script( 'plumba_public', $src );
		wp_enqueue_script( 'plumba_public' );
		wp_localize_script( 'plumba_public', 'ajaxurl', admin_url( 'admin-ajax.php' ) );

		$src = WP_PLUGIN_URL . '/plumba/css/public.css';
		wp_register_style( 'plumba_public_css', $src );
		wp_enqueue_style( 'plumba_public_css' );

	}

	/**
	 * @param int  $post_id
	 * @param null $style
	 * @param int  $bars
	 * @param int  $thanks
	 *
	 * @return bool|string
	 */
	function display( $post_id = 0, $style = null, $bars = 1, $thanks = 1 ) {

		$result = '';

		if ( ! is_numeric( $post_id ) && $post_id > 0 ) return false;

		$comments = new Plumba_Comments( $post_id );

		if ( is_array( $comments->questions ) && sizeof( $comments->questions ) > 0 ) {
			$result .= '<div id="plumba_vote_result_' . $post_id . '"></div>';

			foreach ( $comments->questions as $key => $question ) {
				$result .= "\n\r\n\r";
				$result .= '<!--plumba presentation item-->';
				$result .= "\n\r";
				$result .= '<div class="plumba_presentation_item">';

				if ( $style == 'radio' ) {
					$result .= '<input type="radio" onclick="plumba_vote(' . $post_id . ',' . $key . ',\'' . wp_create_nonce( 'plumba-vote-' + $post_id ) . '\',' . $thanks . '); return false;" value="' . $question . '" class="btn btn-large btn-' . $comments->colors[$key] . '" name="plumba_presentation_question" />';
					$result .= ' <span class="badge badge-' . $comments->colors[$key] . '" id="plumba_question_badge_' . $post_id . '_' . $key . '">0</span> ' . __( 'votes', 'plumba' );
				} else {
					$result .= '<input type="button" onclick="plumba_vote(' . $post_id . ',' . $key . ',\'' . wp_create_nonce( 'plumba-vote' + $post_id ) . '\',' . $thanks . '); return false;" value="' . $question . '" class="btn btn-large btn-' . $comments->colors[$key] . '" name="plumba_presentation_question" />';
					$result .= ' <span class="badge badge-' . $comments->colors[$key] . '" id="plumba_question_badge_' . $post_id . '_' . $key . '">0</span> ' . __( 'votes', 'plumba' );
				}

				if ( $bars ) {
					$result .= '<div class="progress plumba_progress_bar"><div id="plumba_question_bar_' . $post_id . '_' . $key . '" class="bar bar-' . $comments->colors[$key] . '" style="width: 0%;"></div>&nbsp;<span id="plumba_question_percent_' . $post_id . '_' . $key . '">0%</span></div>';
				} else {
					$result .= ' - <span id="plumba_question_percent_' . $post_id . '_' . $key . '">0%</span>';
				}

				$result .= '</div>';

				$result .= "\n\r";
				$result .= '<!--/plumba presentation item-->';
			}
		}

		$interval = get_option( 'plumba_interval', 3000 );
		$result  .= '<script type="text/javascript"> plumba_stats(' . $post_id . ',"' . wp_create_nonce( 'plumba-stats' ) . '"); ';

		if ( $interval > 0 ) {
			$result .= 'setInterval(\'plumba_stats(' . $post_id . ',"' . wp_create_nonce( 'plumba-stats' ) . '");\',' . $interval . '); ';
		}

		$result .= '</script>';

		return $result;

	}

	//Action when creating wp head
	function wp_head() {
		//Hide the comments if single page and plumba page
		if ( is_single() && get_post_type() == 'plumba_qa' ) echo '<style> #comments { display:none; } </style>';
	}

	//Overrides content output and collects a new one from the presentation functions
	function override_content( $content ) {
		global $post;
		//Only if this is the right post type
		if ( $post->post_type == 'plumba_qa' ) {
				$presentation = new Plumba_Presentation();
			     $content .= $presentation->display( $post->ID );
		}
		return $content;
	}

}
