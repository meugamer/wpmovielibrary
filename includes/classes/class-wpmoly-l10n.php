<?php
/**
 * WPMovieLibrary L10n Class extension.
 * 
 * This class implement some of the translation processes the plugin offers.
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_L10n' ) ) :

	class WPMOLY_L10n extends WPMOLY_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    2.0
		 */
		public function register_hook_callbacks() {

			add_filter( 'wpmoly_filter_rewrites', __CLASS__ . '::filter_rewrites', 10, 1 );
			add_filter( 'wpmoly_filter_value_rewrites', __CLASS__ . '::filter_value_rewrites', 10, 3 );
		}

		/**
		 * Get rewrites list
		 * 
		 * @since    2.0
		 * 
		 * @return   array     Translated rewrites
		 */
		public static function get_l10n_rewrite() {

			$l10n_rewrite = get_option( 'wpmoly_l10n_rewrite' );
			if ( false === $l10n_rewrite )
				$l10n_rewrite = self::set_l10n_rewrite();

			return $l10n_rewrite;
		}

		/**
		 * Generate a list of possible translated rewrites
		 * 
		 * @since    2.0
		 * 
		 * @return   array     Translated rewrites
		 */
		public static function set_l10n_rewrite() {

			$translate = wpmoly_o( 'rewrite-enable' );
			$locale    = substr( get_locale(), 0, 2 );

			$l10n_rewrite = array();

			$details   = WPMOLY_Settings::get_supported_movie_details();
			$meta      = WPMOLY_Settings::get_supported_movie_meta();
			$countries = WPMOLY_Settings::get_supported_countries();
			$languages = WPMOLY_Settings::get_available_languages();
			$languages = $languages['standard'];

			foreach ( $details as $slug => $detail ) {

				if ( $translate )
					$key = array_pop( $detail['rewrite'] );
				else
					$key = key( $detail['rewrite'] );

				$l10n_rewrite[ sanitize_title( $key ) ] = $slug;

				if ( ! isset( $detail['options'] ) )
					continue;

				foreach ( $detail['options'] as $_slug => $option ) {

					if ( $translate ) {
						if ( false !== strstr( 'rating', $slug ) ) {
							$key = $_slug;
						} else {
							$key = sanitize_title( $option );
						}
					} elseif ( in_array( $slug, array( 'status', 'media', 'rating' ) ) || false !== strstr( $slug, 'rating' ) ) {
						$key = $_slug;
					} else {
						$key = sanitize_title( $option );
					}

					$l10n_rewrite[ $key ] = $_slug;
				}
			}

			foreach ( $meta as $slug => $m ) {

				if ( ! is_null( $m['rewrite'] ) ) {

					if ( $translate )
						$key = array_pop( $m['rewrite'] );
					else
						$key = key( $m['rewrite'] );

					$l10n_rewrite[ $key ] = $slug;
				}
			}

			foreach ( $countries as $code => $country ) {

				if ( $translate )
					$key = __( $country, 'wpmovielibrary-iso' );
				else
					$key = $country;

				$l10n_rewrite[ sanitize_title( $key ) ] = $code;
			}

			foreach ( $languages as $code => $language ) {

				if ( $translate )
					$key = __( $language, 'wpmovielibrary-iso' );
				else
					$key = $language;

				$l10n_rewrite[ sanitize_title( $key ) ] = $code;
			}
			

			/**
			 * Filter the rewrites list
			 *
			 * @since    2.0
			 *
			 * @param    array    $l10n_rewrite Existing rewrites
			 */
			$l10n_rewrite = apply_filters( 'wpmoly_filter_l10n_rewrite', $l10n_rewrite );

			self::delete_l10n_rewrite();
			add_option( 'wpmoly_l10n_rewrite', $l10n_rewrite );

			return $l10n_rewrite;
		}

		/**
		 * Delete cached rewrites list
		 * 
		 * @since    2.0
		 * 
		 * @return   boolean    Deletion status
		 */
		public static function delete_l10n_rewrite() {

			$delete = delete_option( 'wpmoly_l10n_rewrite' );

			return $delete;
		}

		/**
		 * Get rewrite rules list
		 * 
		 * @since    2.0
		 * 
		 * @return   array     Translated rewrite rules
		 */
		public static function get_l10n_rewrite_rules() {

			$l10n_rewrite_rules = get_option( 'wpmoly_l10n_rewrite_rules' );
			if ( false === $l10n_rewrite_rules )
				$l10n_rewrite_rules = self::set_l10n_rewrite_rules();

			return $l10n_rewrite_rules;
		}

		/**
		 * Generate a list of possible translated rewrite rules
		 * 
		 * Rewrite rules are more limited than rewrites as we only need
		 * to adapt structures.
		 * 
		 * @since    2.0
		 * 
		 * @return   array     Translated rewrite rules
		 */
		public static function set_l10n_rewrite_rules() {

			$l10n_rules = array();

			$translate  = wpmoly_o( 'rewrite-enable' );
			$movies     = wpmoly_o( 'rewrite-movie' );
			$collection = wpmoly_o( 'rewrite-collection' );
			$genre      = wpmoly_o( 'rewrite-genre' );
			$actor      = wpmoly_o( 'rewrite-actor' );

			$l10n_rules['movies'] = ( $translate && '' != $movies ? $movies : 'movies' );
			$l10n_rules['collection'] = ( $translate && '' != $collection ? $collection : 'collection' );
			$l10n_rules['genre'] = ( $translate && '' != $genre ? $genre : 'genre' );
			$l10n_rules['actor'] = ( $translate && '' != $actor ? $actor : 'actor' );

			$l10n_rules['list'] = ( $translate ? __( 'list', 'wpmovielibrary' ) : 'list' );
			$l10n_rules['grid'] = ( $translate ? __( 'grid', 'wpmovielibrary' ) : 'grid' );
			$l10n_rules['archives'] = ( $translate ? __( 'archives', 'wpmovielibrary' ) : 'archives' );

			$details = WPMOLY_Settings::get_supported_movie_details();
			$meta    = WPMOLY_Settings::get_supported_movie_meta();

			foreach ( $details as $slug => $detail ) {
				if ( $translate )
					$l10n_rules['detail'][ $slug ] = array_pop( $detail['rewrite'] );
				else
					$l10n_rules['detail'][ $slug ] = key( $detail['rewrite'] );
			}

			foreach ( $meta as $slug => $m ) {
				if ( ! is_null( $m['rewrite'] ) ) {
					if ( $translate )
						$l10n_rules['meta'][ $slug ] = array_pop( $m['rewrite'] );
					else
						$l10n_rules['meta'][ $slug ] = key( $m['rewrite'] );
				}
			}

			/**
			 * Filter the rewrite rules list
			 *
			 * @since    2.0
			 *
			 * @param    array    $l10n_rules Existing rewrite rules
			 */
			$l10n_rules = apply_filters( 'wpmoly_filter_l10n_rewrite_rules', $l10n_rules );

			self::delete_l10n_rewrite_rules();
			add_option( 'wpmoly_l10n_rewrite_rules', $l10n_rules );

			return $l10n_rules;
		}

		/**
		 * Delete cached rewrite rules list
		 * 
		 * @since    2.0
		 * 
		 * @return   boolean    Deletion status
		 */
		public static function delete_l10n_rewrite_rules() {

			$delete = delete_option( 'wpmoly_rewrite_rules' );

			return $delete;
		}

		/**
		 * Simple filter for rewrites to get rid of %xx%xx-like accented
		 * letters in URLs.
		 *
		 * @since    2.0
		 * 
		 * @param    string    $rewrite
		 *
		 * @return   string    Filtered $rewrite
		*/
		public static function filter_rewrites( $rewrite ) {

			if ( 1 == strpos( $rewrite, '.' ) )
				return $rewrite;

			$rewrite = remove_accents( $rewrite );
			$rewrite = sanitize_title_with_dashes( $rewrite );

			return $rewrite;
		}

		public static function translate_rewrite( $value ) {

			$rewrites = self::get_l10n_rewrite();
			$value = self::filter_rewrites( $value );

			$_value = array_search( $value, $rewrites );
			if ( false !== $_value )
				$value = $_value;

			return $value;
		}

		/**
		 * Filter a value to match a translation, if any.
		 * 
		 * @since    2.1.1
		 * 
		 * @param    string    $value Value to translate back to original
		 * 
		 * @return   string    Un-rewrite value if any, original value else
		 */
		public static function untranslate_rewrite( $value ) {

			$rewrites = self::get_l10n_rewrite();
			$value = self::filter_rewrites( $value );

			if ( ! isset( $rewrites[ $value ] ) )
				return $value;

			return $rewrites[ $value ];
		}

		public static function get_country_standard_name( $country ) {

			$countries = WPMOLY_Settings::get_supported_countries();

			if ( 2 == strlen( $country ) )
				$code = strtoupper( $country );
			else
				$code = array_search( strtoupper( $country ), $countries );

			if ( false !== $code )
				$country = $countries[ $code ];

			return $country;
		}

		public static function get_country_code( $country ) {

			$countries = WPMOLY_Settings::get_supported_countries();

			$code = array_search( $country, $countries );
			if ( false !== $code )
				return $code;

			return null;
		}

		public static function get_language_standard_name( $language ) {

			$languages = WPMOLY_Settings::get_available_languages();

			if ( 2 == strlen( $language ) )
				$code = strtolower( $language );
			else
				$code = array_search( $language, $languages['native'] );

			if ( false !== $code )
				$language = $languages['standard'][ $code ];

			return $language;
		}

		public static function get_language_native_name( $language ) {

			$languages = WPMOLY_Settings::get_available_languages();

			if ( 2 == strlen( $language ) )
				$code = strtolower( $language );
			else
				$code = array_search( $language, $languages['native'] );

			if ( false !== $code )
				$language = $languages['native'][ $code ];

			return $language;
		}

		public static function filter_translation_key( $key ) {

			if ( 'production_countries' == $key )
				$key = 'countries';
			elseif ( 'spoken_languages' == $key )
				$key = 'languages';
			else
				$key = false;

			return $key;
		}

		/**
		 * Localization for scripts
		 * 
		 * Adds a translation object to the plugin's JavaScript object
		 * containing localized texts.
		 * 
		 * @since    1.0
		 * 
		 * @return   array    Localization array
		 */
		public static function localize_script() {

			$localize = array();
			$localize['language'] = wpmoly_o( 'api-language' );

			$lang = array(
				'dashboard' => array(
					'' => ''
				),
				'media' => array(
					'backdrops' => array(
						'added'		=> __( 'Images added!', 'wpmovielibrary' ),
						'available'	=> __( '%d images available for download', 'wpmovielibrary' ),
						'from'		=> __( 'Image from', 'wpmovielibrary' ),
						'uploaded'	=> __( 'Images uploaded!', 'wpmovielibrary' ),
						'import'	=> __( 'Import Images', 'wpmovielibrary' ),
						'title'		=> __( 'Import Images for "%s"', 'wpmovielibrary' ),
						'default_title'	=> __( 'Import Images', 'wpmovielibrary' ),
						'uploading'	=> __( 'Images uploading...', 'wpmovielibrary' ),
						'save'		=> __( 'Saving Images...', 'wpmovielibrary' ),
						'load'		=> __( 'Load Images', 'wpmovielibrary' ),
						'loading'	=> __( 'Loading Images...', 'wpmovielibrary' )
					),
					'posters' => array(
						'added'		=> __( 'Posters added!', 'wpmovielibrary' ),
						'available'	=> __( '%d posters available for download', 'wpmovielibrary' ),
						'for'		=> __( 'Poster for', 'wpmovielibrary' ),
						'uploaded'	=> __( 'Posters uploaded!', 'wpmovielibrary' ),
						'import'	=> __( 'Import Posters', 'wpmovielibrary' ),
						'title'		=> __( 'Select a poster for "%s"', 'wpmovielibrary' ),
						'default_title'	=> __( 'Select a poster', 'wpmovielibrary' ),
						'uploading'	=> __( 'Posters uploading...', 'wpmovielibrary' ),
						'featuring'	=> __( 'Setting featured image…', 'wpmovielibrary' ),
						'poster'	=> __( 'Poster', 'wpmovielibrary' ),
						'save'		=> __( 'Saving Posters...', 'wpmovielibrary' ),
						'load'		=> __( 'Load Posters', 'wpmovielibrary' ),
						'loading'	=> __( 'Loading Posters...', 'wpmovielibrary' )
					),
					'backdrop' => array(
						'uploading'	=> __( 'Image uploading...', 'wpmovielibrary' ),
						'available'	=> __( 'One image available for download', 'wpmovielibrary' )
					),
					'poster' => array(
						'uploading'	=> __( 'Poster uploading...', 'wpmovielibrary' ),
						'available'	=> __( 'One poster available for download', 'wpmovielibrary' )
					),
				),
				'misc' => array(
					'api_connected'		=> __( 'API connected', 'wpmovielibrary' ),
					'available'		=> __( 'Available', 'wpmovielibrary' ),
					'delete_attachment'	=> __( 'You’re about to delete this attachment. Are you sure you want to do that?', 'wpmovielibrary' ),
					'done'			=> __( 'Done!', 'wpmovielibrary' ),
					'load_more'		=> __( 'Load More', 'wpmovielibrary' ),
					'oops'			=> __( 'Oops… Did something went wrong?', 'wpmovielibrary' ),
					'see_less'		=> __( 'see no more', 'wpmovielibrary' ),
					'see_more'		=> __( 'see more', 'wpmovielibrary' ),
					'selected'		=> _n( 'selected', 'selected', 0, 'wpmovielibrary' ),
					'updated'		=> __( 'updated successfully', 'wpmovielibrary' ),
					'used'			=> __( 'Used', 'wpmovielibrary' ),
					'updating'		=> __( 'updating movies...', 'wpmovielibrary' ),
					'x_selected'		=> _n( 'selected', 'selected', 2, 'wpmovielibrary' ),
					'publish'		=> __( 'Published' ),
					'schedule'		=> __( 'Schedule' ),
					'private'		=> __( 'Private' ),
					'public'		=> __( 'Public' ),
					'password'		=> __( 'Password Protected' ),
					'published'		=> __( 'Published' ),
				),
				'movies' => array(
					'confirm_empty'		=> __( 'You’re about to delete all metadata related to this movie. Are you sure you want to do that?', 'wpmovielibrary' ),
					'deleted_movie'		=> __( 'One movie successfully deleted.', 'wpmovielibrary' ),
					'deleted_movies'	=> __( '%s movies successfully deleted.', 'wpmovielibrary' ),
					'dequeued_movie'	=> __( 'One movie removed from the queue.', 'wpmovielibrary' ),
					'dequeued_movies'	=> __( '%s movies removed from the queue.', 'wpmovielibrary' ),
					'enqueued_movie'	=> __( 'One movie added to the queue.', 'wpmovielibrary' ),
					'enqueued_movies'	=> __( '%s movies added to the queue.', 'wpmovielibrary' ),
					'imported'		=> __( 'Imported', 'wpmovielibrary' ),
					'imported_movie'	=> __( 'One movie successfully imported!', 'wpmovielibrary' ),
					'imported_movies'	=> __( '%s movies successfully imported!', 'wpmovielibrary' ),
					'in_progress'		=> __( 'Progressing', 'wpmovielibrary' ),
					'loading'		=> __( 'Loading...', 'wpmovielibrary' ),
					'missing_meta'		=> __( 'No metadata could be found, please import metadata before queuing.', 'wpmovielibrary' ),
					'movie'			=> __( 'Movie', 'wpmovielibrary' ),
					'movie_updated'		=> _n( 'movie updated', 'movies updated', 0, 'wpmovielibrary' ),
					'movies_updated'	=> _n( 'movie updated', 'movies updated', 2, 'wpmovielibrary' ),
					'multiple_results'	=> __( '%d results', 'wpmovielibrary' ),
					'not_updated'		=> __( 'not updated', 'wpmovielibrary' ),
					'saved'			=> __( 'Saved!', 'wpmovielibrary' ),
					'searching'		=> __( 'Searching...', 'wpmovielibrary' )
				),
				'languages' => array(
					'all' => array( 'code' => '', 'text' => __( 'All languages', 'wpmovielibrary' ), 'priority' => 10 )
				)
				/*'' => array(
					'' => ''
				),*/
			);

			$languages = WPMOLY_Settings::get_supported_languages();
			asort( $languages );
			foreach ( $languages as $code => $name )
				$lang['languages'][ $code ] = array( 'code' => $code, 'text' => $name );

			$localize = array_merge( $localize, $lang );

			return $localize;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    2.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {

			self::delete_l10n_rewrite();
			self::delete_l10n_rewrite_rules();
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    2.0
		 */
		public function deactivate() {

			self::delete_l10n_rewrite();
			self::delete_l10n_rewrite_rules();
		}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    2.0
		 */
		public static function uninstall() {

			self::delete_l10n_rewrite();
			self::delete_l10n_rewrite_rules();
		}

		/**
		 * Initializes variables
		 *
		 * @since    2.0
		 */
		public function init() {}

	}

endif;
