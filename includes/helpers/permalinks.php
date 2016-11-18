<?php
/**
 * The file that defines the plugin formatting functions.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/helpers
 */

use \wpmoly\Helpers\Permalink;

/**
 * Generate basic movie meta permalink.
 * 
 * @since    3.0
 * 
 * @param    string    $meta Meta name.
 * @param    string    $value Meta value.
 * 
 * @return   string
 */
function generate_movie_meta_url( $meta, $value ) {

	$meta = sanitize_key( $meta );
	$meta = str_replace( '_', '-', $meta );

	$value = sanitize_title_with_dashes( $value );

	$url = get_movie_archive_link() . $meta . '/' . $value;

	/**
	 * Filter a movie meta URL.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $url Meta URL.
	 * @param    string    $meta Meta key.
	 * @param    string    $value Meta value.
	 */
	return apply_filters( "wpmoly/filter/permalink/{$meta}/{$value}/url", trailingslashit( $url ), $meta, $value );
}

/**
 * Build a permalink for details.
 * 
 * @since    3.0
 * 
 * @param    string          $detail Detail type.
 * @param    string|array    $value Detail value.
 * @param    array           $options Permalink options.
 * 
 * @return   string
 */
function get_movie_detail_url( $detail, $value, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => ''
	) );

	$url = generate_movie_meta_url( $detail, $value );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter detail permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $permalink Permalink HTML output.
	 * @param    string    $detail Detail type.
	 * @param    string    $value Detail value.
	 * @param    array     $options Permalink options.
	 */
	return apply_filters( "wpmoly/filter/permalink/{$detail}", $permalink, $detail, $value, $options );
}

/**
 * Build a permalink for adult restrictions.
 * 
 * @since    3.0
 * 
 * @param    string     $content Movie adult restriction text.
 * @param    boolean    $is_adult Adult restriction?
 * 
 * @return   string
 */
function get_movie_adult_url( $adult, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content'  => '',
		'title'    => '',
		'is_adult' => false
	) );

	$adult = (string) $adult;

	if ( true === $options['is_adult'] ) {
		$value = 'yes';
		if ( empty( $options['content'] ) ) {
			$options['content'] = __( 'Yes', 'wpmovielibrary' );
		}
		if ( empty( $options['title'] ) ) {
			$options['title'] = __( 'Adults-only movies', 'wpmovielibrary' );
		}
	} else {
		$value = 'no';
		if ( empty( $options['content'] ) ) {
			$options['content'] = __( 'No', 'wpmovielibrary' );
		}
		if ( empty( $options['title'] ) ) {
			$options['title'] = __( 'All-audience movies', 'wpmovielibrary' );
		}
	}

	$url = generate_movie_meta_url( 'adult', $value );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter adult restriction permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    string     $content Default text.
	 * @param    boolean    $is_adult Adult restriction?
	 */
	return apply_filters( 'wpmoly/filter/permalink/adult', $permalink, $options['content'], $options['is_adult'] );
}

/**
 * Build a permalink for author.
 * 
 * @since    3.0
 * 
 * @param    string    $author Movie author.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_author_url( $author, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => ''
	) );

	$author = (string) $author;

	if ( empty( $options['content'] ) ) {
		$options['content'] = $author;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = sprintf( __( 'Movies from author %s', 'wpmovielibrary' ), $author );
	}

	$url = generate_movie_meta_url( 'author', $author );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter author permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    string     $author Movie author.
	 * @param    array      $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/author', $permalink, $author, $options );
}

/**
 * Build a permalink for certifications.
 * 
 * @since    3.0
 * 
 * @param    string    $certification Movie certification.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_certification_url( $certification, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => ''
	) );

	$certification = (string) $certification;

	if ( empty( $options['content'] ) ) {
		$options['content'] = $certification;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = sprintf( __( '“%s” rated movies', 'wpmovielibrary' ), $certification );
	}

	$url = generate_movie_meta_url( 'certification', $certification );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter certification permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    int        $certification Movie certification.
	 * @param    array      $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/certification', $permalink, $certification, $options );
}

/**
 * Build a permalink for original music composers.
 * 
 * @since    3.0
 * 
 * @param    string    $composer Movie original music composer.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_composer_url( $composer, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => ''
	) );

	$composer = (string) $composer;

	if ( empty( $options['content'] ) ) {
		$options['content'] = $composer;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = sprintf( __( 'Movies from original music composer %s', 'wpmovielibrary' ), $composer );
	}

	$url = generate_movie_meta_url( 'composer', $composer );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter composer permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    string     $composer Movie composer.
	 * @param    array      $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/composer', $permalink, $composer, $options );
}

/**
 * Build a permalink for production countries.
 * 
 * @since    3.0
 * 
 * @param    string    $country Movie country.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_country_url( $country, $options = array() ) {

	if ( ! $country instanceof \wpmoly\Helpers\Country ) {
		return $country;
	}

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => ''
	) );

	if ( empty( $options['content'] ) ) {
		$options['content'] = $country->localized_name;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = sprintf( __( 'Movies produced in: %s (%s)', 'wpmovielibrary' ), $country->localized_name, $country->standard_name );
	}

	$url = generate_movie_meta_url( 'production-country', $country->code );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter single country permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $permalink Permalink HTML output.
	 * @param    string    $country Movie production country object.
	 * @param    array     $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/country', $permalink, $country, $options );
}

function get_movie_format_url( $format, $options = array() ) {

	return $permalink = '';
}

/**
 * Build a permalink for movie homepages.
 * 
 * @since    3.0
 * 
 * @param    string    $country Movie homepage.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_homepage_url( $homepage, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => ''
	) );

	$homepage = (string) $homepage;

	if ( empty( $options['content'] ) ) {
		$options['content'] = str_replace( array( 'http://', 'https://' ), '', untrailingslashit( $homepage ) );
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = __( 'Official movie website', 'wpmovielibrary' );
	}

	$permalink = '<a href="' . esc_url( $homepage ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter movie homepage permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    int        $homepage Movie homepage.
	 * @param    arrat      $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/homepage', $permalink, $homepage, $options );
}

/**
 * Build an external link for IMDb IDs.
 * 
 * @since    3.0
 * 
 * @param    string     $content Movie IMDb ID.
 * @param    boolean    $options Permalink options.
 * 
 * @return   string
 */
function get_movie_imdb_id_url( $imdb_id, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => ''
	) );

	$imdb_id = (string) $imdb_id;

	if ( empty( $options['content'] ) ) {
		$options['content'] = $imdb_id;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = __( 'Movie page on IMDb.com', 'wpmovielibrary' );
	}

	$url = sprintf( 'https://www.imdb.com/title/%s/', $imdb_id );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter movie IMDb ID permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    int        $imdb_id Movie IMDb ID.
	 * @param    arrat      $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/imdb_id', $permalink, $imdb_id, $options );
}

/**
 * Build a permalink for languages.
 * 
 * @since    3.0
 * 
 * @param    string    $language Movie language.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_language_url( $language, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content'  => '',
		'title'    => '',
		'language' => '',
		'variant'  => 'spoken_languages'
	) );

	if ( ! $options['language'] instanceof \wpmoly\Helpers\Language ) {
		return $language;
	}

	$language_object = $options['language'];

	if ( empty( $options['content'] ) ) {
		$options['content'] = $language;
	}

	if ( empty( $options['title'] ) ) {
		if ( 'language' == $options['variant'] ) {
			$options['title'] = sprintf( __( '%s-dubbed movies', 'wpmovielibrary' ), $language_object->localized_name );
		} elseif ( 'subtitles' == $options['variant'] ) {
			$options['title'] = sprintf( __( '%s-subtitled movies', 'wpmovielibrary' ), $language_object->localized_name );
		} else {
			$options['title'] = sprintf( __( '%s-speaking movies', 'wpmovielibrary' ), $language_object->localized_name );
		}
	}

	$url = generate_movie_meta_url( $options['variant'], $language_object->code );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter single language permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $permalink Permalink HTML output.
	 * @param    string    $language Movie language object.
	 * @param    array     $options Formatting options.
	 */
	return apply_filters( "wpmoly/filter/permalink/{$options['variant']}", $permalink, $language_object, $options );

	return $permalink = '';
}

/**
 * Build a permalink for local release dates.
 * 
 * Alias for get_movie_date_url().
 * 
 * @since    3.0
 * 
 * @param    string    $local_release_date Movie release date.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_local_release_date_url( $local_release_date, $options = array() ) {

	$options = (array) $options;
	$options['variant'] = 'local_';

	return get_movie_date_url( $local_release_date, $options );
}

function get_movie_media_url( $media, $options = array() ) {

// 	$options = wp_parse_args( (array) $options, array(
// 		'content' => '',
// 		'title'   => ''
// 	) );
// 
// 	$ = (string) $;
// 
// 	if ( empty( $options['content'] ) ) {
// 		$options['content'] = $;
// 	}
// 
// 	if ( empty( $options['title'] ) ) {
// 		$options['title'] = sprintf( __( 'Movies  %s', 'wpmovielibrary' ), $ );
// 	}
// 
// 	$url = generate_movie_meta_url( '', $ );
// 
// 	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';
// 
// 	/**
// 	 * Filter  permalink.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    string     $permalink Permalink HTML output.
// 	 * @param    string     $ Movie director of .
// 	 * @param    array      $options Formatting options.
// 	 */
// 	return apply_filters( 'wpmoly/filter/permalink/', $permalink, $, $options );

	return $permalink = '';
}

/**
 * Build a permalink for release dates.
 * 
 * Alias for get_movie_date_url().
 * 
 * @since    3.0
 * 
 * @param    string    $release_date Movie release date.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_release_date_url( $release_date, $options = array() ) {

	return get_movie_date_url( $release_date, $options );
}

/**
 * Build a permalink for dates.
 * 
 * A bunch of different, basic formats are supported. US/UK -formatted dates will
 * link to monthly archives while French-formatted dates will be splited to link
 * to monthly and yearly archives.
 * 
 * Support a 'local_' variant for local release dates.
 * 
 * @since    3.0
 * 
 * @param    string    $release_date Movie release date.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_date_url( $date, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'format'  => '',
		'variant' => ''
	) );

	$date = (string) $date;
	$timestamp = strtotime( $date );

	if ( empty( $options['content'] ) ) {
		$options['content'] = $date;
	}

	switch ( $options['format'] ) {
		case 'Y':
			if ( 'local_' == $options['variant'] ) {
				$options['title'] = sprintf( __( 'Movies locally released in %s', 'wpmovielibrary' ), date_i18n( 'Y', $timestamp ) );
				$url = generate_movie_meta_url( 'local-release-date', date( 'Y', $timestamp ) );
			} else {
				$options['title'] = sprintf( __( 'Movies released in %s', 'wpmovielibrary' ), date_i18n( 'Y', $timestamp ) );
				$url = generate_movie_meta_url( 'release-date', date( 'Y', $timestamp ) );
			}

			$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';
			break;
		case 'j F Y':
			$permalink = array();

			$month = date_i18n( 'j F', $timestamp );
			if ( 'local_' == $options['variant'] ) {
				$options['title'] = sprintf( __( 'Movies locally released in %s', 'wpmovielibrary' ), $month );
				$url = generate_movie_meta_url( 'local-release-date', date( 'Y-m', $timestamp ) );
			} else {
				$options['title'] = sprintf( __( 'Movies released in %s', 'wpmovielibrary' ), $month );
				$url = generate_movie_meta_url( 'release-date', date( 'Y-m', $timestamp ) );
			}
			$permalink[] = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $month ) . '</a>';

			$year = date_i18n( 'Y', $timestamp );
			if ( 'local_' == $options['variant'] ) {
				$options['title'] = sprintf( __( 'Movies locally released in %s', 'wpmovielibrary' ), $year );
				$url = generate_movie_meta_url( 'local-release-date', date( 'Y', $timestamp ) );
			} else {
				$options['title'] = sprintf( __( 'Movies released in %s', 'wpmovielibrary' ), $year );
				$url = generate_movie_meta_url( 'release-date', date( 'Y', $timestamp ) );
			}
			$permalink[] = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $year ) . '</a>';

			$permalink = implode( '&nbsp;', $permalink );
			break;
		case 'Y-m-d':
		case 'm/d/Y':
		case 'd/m/Y':
		default:
			if ( 'local_' == $options['variant'] ) {
				$options['title'] = sprintf( __( 'Movies locally released in %s', 'wpmovielibrary' ), date_i18n( 'F Y', $timestamp ) );
				$url = generate_movie_meta_url( 'local-release-date', date( 'Y-m-d', $timestamp ) );
			} else {
				$options['title'] = sprintf( __( 'Movies released in %s', 'wpmovielibrary' ), date_i18n( 'F Y', $timestamp ) );
				$url = generate_movie_meta_url( 'release-date', date( 'Y-m-d', $timestamp ) );
			}

			$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';
			break;
	}

	/**
	 * Filter release date permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    string     $date Movie date.
	 * @param    array      $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/release_date', $permalink, $date, $options );
}

/**
 * Build a permalink for directors of photography.
 * 
 * @since    3.0
 * 
 * @param    string    $photographer Movie director of photography.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_photography_url( $photographer, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => ''
	) );

	$photographer = (string) $photographer;

	if ( empty( $options['content'] ) ) {
		$options['content'] = $photographer;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = sprintf( __( 'Movies from director of photography %s', 'wpmovielibrary' ), $photographer );
	}

	$url = generate_movie_meta_url( 'photography', $photographer );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter  permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    string     $photographer Movie director of photography.
	 * @param    array      $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/photography', $permalink, $photographer, $options );
}

/**
 * Build a permalink for producers.
 * 
 * @since    3.0
 * 
 * @param    string    $producer Movie producer.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_producer_url( $producer, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => ''
	) );

	$producer = (string) $producer;

	if ( empty( $options['content'] ) ) {
		$options['content'] = $producer;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = sprintf( _x( 'Movies produced by %s', 'producer', 'wpmovielibrary' ), $producer );
	}

	$url = generate_movie_meta_url( 'producer', $producer );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter  permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    string     $producer Movie director of producer.
	 * @param    array      $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/producer', $permalink, $producer, $options );
}

/**
 * Build a permalink for production companies.
 * 
 * @since    3.0
 * 
 * @param    string    $company Movie production company.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_production_url( $company, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => ''
	) );

	$company = (string) $company;

	if ( empty( $options['content'] ) ) {
		$options['content'] = $company;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = sprintf( _x( 'Movies produced by %s', 'production company', 'wpmovielibrary' ), $company );
	}

	$url = generate_movie_meta_url( 'company', $company );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter  permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    string     $ Movie director of .
	 * @param    array      $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/company', $permalink, $company, $options );
}

function get_movie_rating_url( $rating, $options = array() ) {

	return $permalink = '';
}

/**
 * Build a permalink for spoken languages.
 * 
 * @since    3.0
 * 
 * @param    string    $spoken_languages Movie spoken languages.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_spoken_languages_url( $spoken_languages, $options = array() ) {

	$options = (array) $options;
	$options['variant'] = 'spoken_languages';

	return get_movie_language_url( $spoken_languages, $options );
}

function get_movie_status_url( $status, $options = array() ) {

	return $permalink = '';
}

/**
 * Build a permalink for subtitles.
 * 
 * @since    3.0
 * 
 * @param    string    $subtitles Movie subtitles.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_subtitles_url( $subtitles, $options = array() ) {

	$options = (array) $options;
	$options['variant'] = 'subtitles';

	return get_movie_language_url( $subtitles, $options );
}

/**
 * Build an external link for TMDb IDs.
 * 
 * @since    3.0
 * 
 * @param    string     $content Movie TMDb ID.
 * @param    boolean    $options Permalink options.
 * 
 * @return   string
 */
function get_movie_tmdb_id_url( $tmdb_id, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => ''
	) );

	$tmdb_id = absint( $tmdb_id );

	if ( empty( $options['content'] ) ) {
		$options['content'] = $tmdb_id;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = __( 'Movie page on TheMovieDB.org', 'wpmovielibrary' );
	}

	$url = sprintf( 'https://www.themoviedb.org/movie/%d', $tmdb_id );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter movie TMDb ID permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    int        $tmdb_id Movie TMDb ID.
	 * @param    arrat      $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/tmdb_id', $permalink, $tmdb_id, $options );
}

/**
 * Build a permalink for writers.
 * 
 * @since    3.0
 * 
 * @param    string    $writer Movie writer.
 * @param    array     $options Permalink options.
 * 
 * @return   string
 */
function get_movie_writer_url( $writer, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => ''
	) );

	$writer = (string) $writer;

	if ( empty( $options['content'] ) ) {
		$options['content'] = $writer;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = sprintf( __( 'Movies from writer %s', 'wpmovielibrary' ), $writer );
	}

	$url = generate_movie_meta_url( 'writer', $writer );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter writer permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    string     $writer Movie writer.
	 * @param    array      $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/writer', $permalink, $writer, $options );
}
