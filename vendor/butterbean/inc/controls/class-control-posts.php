<?php
/**
 * Posts control class. This class is meant to provide lists of posts from a 
 * specific post type if any is set, standard posts otherwise.
 *
 * @package    ButterBean
 * @author     Charlie Merland <charlie@caercam.org>
 * @copyright  Copyright (c) 2015-2016, Justin Tadlock
 * @link       https://github.com/justintadlock/butterbean
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Posts control class.
 *
 * @since  1.0.0
 * @access public
 */
class ButterBean_Control_Posts extends ButterBean_Control {

	/**
	 * The type of control.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'posts';

	/**
	 * The post type to select posts from.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $post_type = 'post';

	/**
	 * Get the value for the setting.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $setting
	 * @return mixed
	 */
	public function get_value( $setting = 'default' ) {

		$value = parent::get_value( $setting );

		return intval( $value );
	}

	/**
	 * Adds custom data to the json array. This data is passed to the Underscore template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {
		parent::to_json();

		$posts = get_posts(
			array(
				'post_type'      => post_type_exists( $this->post_type ) ? $this->post_type : 'post',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'fields'         => array( 'ID', 'post_title' )
			)
		);

		$this->json['choices'] = array( array( 'value' => '', 'label' => '' ) );

		foreach ( $posts as $post )
			$this->json['choices'][] = array( 'value' => $post->ID, 'label' => $post->post_title );
	}
}
