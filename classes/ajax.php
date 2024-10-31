<?php
/**
 * This class service with functions for ajax actions and stats
 */
class Plumba_Ajax {

	function __construct() {
		//Add ajax actions
		add_action( 'wp_ajax_nopriv_plumba_vote', array( &$this, 'vote' ) );
		add_action( 'wp_ajax_plumba_vote', array( &$this, 'vote' ) );

		add_action( 'wp_ajax_nopriv_plumba_stats', array( &$this, 'stats' ) );
		add_action( 'wp_ajax_plumba_stats', array( &$this, 'stats' ) );
	}

	function vote() {

		$post_id = esc_attr( $_REQUEST['post_id'] );
		$key     = esc_attr( $_REQUEST['key'] );
		//$nonce   = esc_attr( $_REQUEST['nonce'] );

		//check nonce before continue
		//if ( ! wp_verify_nonce( $nonce, 'plumba-vote' ) ) wp_die( __( 'Busted!' , 'plumba' ) );

		if ( isset($_COOKIE['plumba-vote-'.$post_id]) ) wp_die( __( 'Thanks but double votes not allowed!' , 'plumba' ) );

		Plumba_Comments::create_vote_comment( $post_id, $key );

		$cookie_expire = get_option( 'plumba_cookie_expire', 1 );

		setcookie( 'plumba-vote-'.$post_id, 'true', time() + 60 * $cookie_expire );

		wp_die( __( 'Thank you!', 'plumba' ) );

	}

	function stats() {

		$post_id = esc_attr( $_REQUEST['post_id'] );
		//$nonce   = esc_attr( $_REQUEST['nonce'] );

		//check nonce before continue
		//if ( ! wp_verify_nonce( $nonce, 'plumba-stats' ) ) wp_die( 'Busted!' );

		$comments = new Plumba_Comments( $post_id );

		wp_die( json_encode( $comments ) );

	}


}