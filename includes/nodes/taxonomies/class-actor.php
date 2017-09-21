<?php
/**
 * Define the Actor Taxonomy class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package WPMovieLibrary
 */

namespace wpmoly\nodes\taxonomies;

/**
 * Actors are terms from the 'actor' taxonomy.
 *
 * @since 3.0.0
 * @package WPMovieLibrary
 * @author Charlie Merland <charlie@caercam.org>
 *
 * @property    string     $name Actor name.
 * @property    int        $person_id Actor related Person ID.
 */
class Actor extends Taxonomy {

	/**
	 * Taxonomy name.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $taxonomy = 'actor';

	/**
	 * Simple accessor for Actor thumbnail.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $variant Image variant.
	 * @param string $size Image size.
	 *
	 * @return Image
	 */
	public function get_thumbnail( $variant = '', $size = 'thumb' ) {

		$custom_picture = $this->get_custom_picture( $size );
		if ( ! empty( $custom_picture ) ) {
			$this->picture = $custom_picture;
			return $this->picture;
		}

		if ( empty( $variant ) ) {
			$variant = $this->get( 'picture' );
		}

		/**
		 * Filter default actor picture
		 *
		 * @since 3.0.0
		 *
		 * @param string $picture
		 */
		$variants = apply_filters( 'wpmoly/filter/default/actor/picture/variants', array( 'neutral', 'female', 'male' ) );
		if ( ! in_array( $variant, $variants ) ) {
			$variant = 'neutral';
		}

		/**
		 * Filter default actor picture
		 *
		 * @since 3.0.0
		 *
		 * @param string $picture
		 */
		$sizes = apply_filters( 'wpmoly/filter/default/actor/picture/sizes', array( 'original', 'full', 'medium', 'small', 'thumb', 'thumbnail', 'tiny' ) );
		if ( ! in_array( $size, $sizes ) ) {
			$size = 'medium';
		}

		if ( 'original' !== $size ) {
			$size = '-' . $size;
		}

		/**
		 * Filter default actor picture
		 *
		 * @since 3.0.0
		 *
		 * @param string $picture
		 */
		$picture = apply_filters( 'wpmoly/filter/default/actor/picture', WPMOLY_URL . "public/assets/img/actor-{$variant}{$size}.png" );

		$this->picture = $picture;

		return $this->picture;
	}

	/**
	 * Retrieve the Actor custom thumbnail, if any.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $size Image size.
	 *
	 * @return string
	 */
	public function get_custom_thumbnail( $size = 'thumb' ) {

		$picture = $this->get( 'custom_picture' );
		if ( empty( $picture ) ) {
			return $picture;
		}

		$picture = wp_get_attachment_image_src( $picture, $size );
		if ( empty( $picture[0] ) ) {
			return '';
		}

		return $picture[0];
	}

}
