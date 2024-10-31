<?php
/**
 * Description of plumba
 *
 * @author plumba
 */
class Plumba_Main {


	function __construct() {

		//Set hook init to this class init function
		add_action( 'init', array( &$this, 'init' ) );

		$this->add_metaboxes();

		//override if deleting plumba comments
		add_action( 'delete_comment', array( &$this, 'delete_comment' ) );


	}

	//Initial all public functions at startup
	function init() {

		$this->add_postypes();

		// Main jQuery
		$src = WP_PLUGIN_URL . '/plumba/js/editor.js';
		wp_deregister_script( 'plumba_editor' );
		wp_register_script( 'plumba_editor', $src );
		wp_enqueue_script( 'plumba_editor' );


	}

	//Adds custom posttypes to WP
	function add_postypes() {

		//Adding the common plumba posttype to WP
		$result = register_post_type(
			'plumba_qa',
			array(
				'label' => 'Plumba',
				'description' => 'Stores Questions and Answers to WordPress as Custom Post Type',
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => array( 'slug' => '' ),
				'query_var' => true,
				'exclude_from_search' => true,
				'supports' => array( 'title' ),
			'labels' => array(
				'name'               => 'Plumba',
				'singular_name'      => 'Plumba',
				'menu_name'          => 'Plumba',
				'add_new'            => 'Add Plumba',
				'add_new_item'       => 'Add New Plumba',
				'edit'               => 'Edit',
				'edit_item'          => 'Edit Plumba',
				'new_item'           => 'New Plumba',
				'view'               => 'View Plumba',
				'view_item'          => 'View Plumba',
				'search_items'       => 'Search Plumba',
				'not_found'          => 'No Plumba Found',
				'not_found_in_trash' => 'No Plumba Found in Trash',
				'parent'             => 'Parent Plumba',
				)
			)
		);

		if ( is_wp_error( $result ) ) return false;

		if ( ! is_object( $result ) ) return false;

		return true;
	}

	//Adds metaboxes to specific post type
	function add_metaboxes() {
		$nada = new Plumba_Metabox();
		return $nada;
	}

	//When deleting spam, copy to new so that data is preserved!
	function delete_comment( $comment_id ) {

		if ( ! isset( $comment_id ) ) return 0;

		if ( ! $comment = get_comment( $comment_id ) )
			return $comment_id;

		if ( $comment->comment_type == 'plumba_vote' ) {
			Plumba_Comments::create_vote_comment( $comment->comment_post_ID, $comment->comment_content );
		}

		return $comment_id;

	}

}

?>
