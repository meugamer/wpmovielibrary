<?php
/**
 * The file that defines the plugin debug functions.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 */

/**
 * Dump rewrite rules debug info.
 * 
 * @since    3.0
 * 
 * @return   void
 */
function debug_rewrite_rules() {

	global $wp, $template;

	echo "\r\n<!-- Request: " . ( empty( $wp->request ) ? '−' : esc_html( $wp->request ) ) . " -->";
	echo "\r\n<!-- Matched Rewrite Rule: " . ( empty( $wp->matched_rule ) ? '−' : esc_html( $wp->matched_rule ) ) . " -->";
	echo "\r\n<!-- Matched Rewrite Query: " . ( empty( $wp->matched_query ) ? '−' : esc_html( $wp->matched_query ) ) . " -->";
	echo "\r\n<!-- Loaded Template: " . basename( $template ) . " -->";
	echo "\r\n";
}

/**
 * Friendly looking print_r.
 * 
 * @since    3.0
 * 
 * @param    mixed      $data Data to dump.
 * 
 * @return   string
 */
function printr() {

	$printr = new PrintR;
	if ( func_num_args() ) {
		foreach ( func_get_args() as $item ) {
			$printr->add( $item );
		}
	}

	return $printr;
}

/**
 * Friendly looking print_r class.
 * 
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/helpers
 * @author     Charlie Merland <charlie@caercam.org>
 */
class PrintR {

	/**
	 * Data to dump.
	 * 
	 * @var    array
	 */
	protected $items = array();

	/**
	 * Add new items to data list.
	 * 
	 * @since    3.0
	 * 
	 * @param    mixed    $item
	 * 
	 * @return   void
	 */
	public function add( $item ) {

		$this->items[] = $item;
	}

	/**
	 * Output data.
	 * 
	 * @since    3.0
	 * 
	 * @param    boolean    $silent Should we wrap output with HTML comments?
	 * 
	 * @return   void
	 */
	public function toString( $silent = false ) {

		$string = array();
		foreach ( $this->items as $item ) {
			$string[] = print_r( $item, true );
		}

		$string = join( "\r\n", $string );
		if ( true == $silent ) {
			$string = "<!-- {$string} -->\r\n";
		}

		echo $string;
	}

	/**
	 * Output data inside a <PRE> markup.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function toHTML() {

		$string = $this->toString();

		echo $string;"\r\n<pre>{$string}</pre>\r\n";
	}
}
