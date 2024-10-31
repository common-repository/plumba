<?php

/**
 * Data operations for storing votes to custom post type plumba_qa
 */
class Plumba_Comments {

	public $first_vote;
	public $latest_vote;
	public $questions;
	public $answers;
	public $colors;
	public $percent;
	public $total;
	public $buttons;

	function __construct( $post_id ) {

		//arguments to query
		$args = array(
			'post_id' => $post_id,
			'status' => 'spam',
		);

		//comments!
		$comments = get_comments( $args );

		$this->first_vote  = date( 'Y-m-d H:i:s' );
		$this->latest_vote = '1969-03-13 17:35';
		$this->questions   = get_post_meta( $post_id, 'plumba_questions', true );
		$this->colors      = get_post_meta( $post_id, 'plumba_colors', true );
		$this->answers     = array();
		$this->percent     = array();

		if ( $this->questions ) {
			for ( $i = 0; $i < sizeof( $this->questions ); $i ++ ) {
				$this->answers[$i] = 0;
			}
		}

		$this->total = sizeof( $comments );

		foreach ( $comments as $comment ) {
			if ( $this->first_vote > $comment->comment_date )
				$this->first_vote = $comment->comment_date;

			if ( $this->latest_vote < $comment->comment_date )
				$this->latest_vote = $comment->comment_date;

			$key                 = (int) $comment->comment_content;
			$current_value       = isset( $this->answers[$key] ) ? $this->answers[$key] : 0;
			$this->answers[$key] = $current_value + 1;
		}

		foreach ( $this->answers as $key => $answer ) {
			if ( $this->total > 0 ) {
				$this->percent[$key] = round( ( ( $answer / $this->total ) * 100 ) );
			} else {
				$this->percent[$key] = 0;
			}
		}

	}

	/**
	 * @return array
	 */
	static function option_colors() {
		return array(
			'primary'   => __( 'Blue', 'plumba' ),
			'success'   => __( 'Green', 'plumba' ),
			'warning'   => __( 'Yellow', 'plumba' ),
			'important' => __( 'Red', 'plumba' ),
			'info'      => __( 'Cyan', 'plumba' ),
			'inverse'   => __( 'Black', 'plumba' )
		);
	}

	/**
	 * @param null $color_key
	 */
	static function wp_option_colors( $color_key = null ) {
		echo Plumba_Comments::get_option_colors( $color_key );
	}

	/**
	 * @param null $color_key
	 *
	 * @return string
	 */
	static function get_option_colors( $color_key = null ) {
		$result = '';
		foreach ( Plumba_Comments::option_colors() as $key => $color ) {
			$result .= '<option value="' . $key . '"';
			if ( $color_key == $key ) $result .= ' selected';
			$result .= '>' . $color . '</option>' . "\r\n";
		}
		return $result;
	}

	/**
	 * @return array
	 */
	static function option_styles() {
		return array(
			'standard'   => __( 'Buttons & Bars', 'plumba' ),
			'radio'   => __( 'Radio', 'plumba' )
		);
	}

	/**
	 * @param null $style_key
	 */
	static function wp_option_styles( $style_key = null ) {
		echo Plumba_Comments::get_option_styles( $style_key );
	}

	/**
	 * @param null $style_key
	 *
	 * @return string
	 */
	static function get_option_styles( $style_key = null ) {
		$result = '';
		foreach ( Plumba_Comments::option_styles() as $key => $style ) {
			$result .= '<option value="' . $key . '"';
			if ( $style_key == $key ) $result .= ' selected';
			$result .= '>' . $style . '</option>' . "\r\n";
		}
		return $result;
	}

	/**
	 * @param $post_id
	 * @param $key
	 */
	static function create_vote_comment( $post_id, $key ) {
		$time       = date( 'Y-m-d H:i:s' );
		$data       = array(
			'comment_post_ID'    => $post_id,
			'comment_author'     => 'Plumba Vote',
			'comment_author_url' => 'http://',
			'comment_content'    => $key,
			'comment_type'       => 'plumba_vote',
			'comment_parent'     => 0,
			'user_id'            => 1,
			'comment_date'       => $time,
			'comment_approved'   => 1,
			'status'             => 'spam',
		);
		$comment_id = wp_insert_comment( $data );
		wp_spam_comment( $comment_id );
	}


}
