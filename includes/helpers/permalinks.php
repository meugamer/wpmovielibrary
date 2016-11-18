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
function get_movie_adult_url( $content, $is_adult ) {

	if ( true === $is_adult ) {
		$value = 'yes';
		$title = __( 'Adults-only movies', 'wpmovielibrary' );
	} else {
		$value = 'no';
		$title = __( 'All-audience movies', 'wpmovielibrary' );
	}

	$url = generate_movie_meta_url( 'adult', $value );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $title ) . '">' . esc_html( $content ) . '</a>';

	/**
	 * Filter single adult restriction permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    string     $content Default text.
	 * @param    boolean    $is_adult Adult restriction?
	 */
	return apply_filters( 'wpmoly/filter/permalink/adult', $permalink, $content, $is_adult );
}

function get_movie_author_url( $author, $options = array() ) {

	return $permalink = '';
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
	 * Filter single adult restriction permalink.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink Permalink HTML output.
	 * @param    int        $certification Movie certification.
	 * @param    arrat      $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/adult', $permalink, $certification, $options );
}

function get_movie_composer_url( $composer, $options = array() ) {

	return $permalink = '';
}

function get_movie_country_url( $country, $options = array() ) {

	return $permalink = '';
}

function get_movie_format_url( $format, $options = array() ) {

	return $permalink = '';
}

function get_movie_homepage_url( $homepage, $options = array() ) {

	return $permalink = '';
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

function get_movie_language_url( $language, $options = array() ) {

	return $permalink = '';
}

function get_movie_local_release_date_url( $local_release_date, $options = array() ) {

	return $permalink = '';
}

function get_movie_media_url( $media, $options = array() ) {

	return $permalink = '';
}

function get_movie_release_date_url( $release_date, $options = array() ) {

	return $permalink = '';
}

function get_movie_photography_url( $photography, $options = array() ) {

	return $permalink = '';
}

function get_movie_producer_url( $producer, $options = array() ) {

	return $permalink = '';
}

function get_movie_production_url( $company, $options = array() ) {

	return $permalink = '';
}

function get_movie_rating_url( $rating, $options = array() ) {

	return $permalink = '';
}

function get_movie_spoken_languages_url( $language, $options = array() ) {

	return $permalink = '';
}

function get_movie_status_url( $status, $options = array() ) {

	return $permalink = '';
}

function get_movie_subtitles_url( $subtitle, $options = array() ) {

	return $permalink = '';
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

function get_movie_writer_url( $writer, $options = array() ) {

	return $permalink = '';
}



































// /**
//  * Build a permalink for adult restrictions.
//  * 
//  * Uses \wpmoly\Permalink() to generate custom URLs for adult
//  * restrictions.
//  * 
//  * @since    3.0
//  * 
//  * @param    string     $text Movie adult restriction text.
//  * @param    boolean    $is_adult Adult restriction?
//  * 
//  * @return   string
//  */
// function get_movie_adult_url( $text, $is_adult = false ) {
// 
// 	if ( empty( $text ) ) {
// 		return $text;
// 	}
// 
// 	if ( true === $is_adult ) {
// 		$content = 'yes';
// 		$attr_title = __( 'Adults-only movies', 'wpmovielibrary' );
// 	} else {
// 		$content = 'no';
// 		$attr_title = __( 'All-audience movies', 'wpmovielibrary' );
// 	};
// 
// 	$permalink = new Permalink;
// 	$permalink->setID( 'adult' );
// 	$permalink->setContent( $content );
// 	$permalink->setTitle( $text );
// 	$permalink->setTitleAttr( $attr_title );
// 
// 	/**
// 	 * Filter single adult restriction permalink.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    string     $permalink Permalink HTML output.
// 	 * @param    object     $permalink_object Permalink instance.
// 	 * @param    string     $text Default text.
// 	 * @param    boolean    $is_adult Adult restriction?
// 	 */
// 	return apply_filters( 'wpmoly/filter/permalink/adult', $permalink->toHTML(), $permalink, $text, $is_adult );
// }
// 
// /**
//  * Build a permalink for authors.
//  * 
//  * Uses \wpmoly\Permalink() to generate custom URLs for authors.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $author Movie author.
//  * 
//  * @return   string
//  */
// function get_movie_author_url( $author ) {
// 
// 	if ( empty( $author ) ) {
// 		return $author;
// 	}
// 
// 	$permalink = new Permalink;
// 	$permalink->setID( 'author' );
// 	$permalink->setContent( sanitize_title_with_dashes( $author ) );
// 	$permalink->setTitle( $author );
// 	$permalink->setTitleAttr( sprintf( __( 'Movies from author %s', 'wpmovielibrary' ), $author ) );
// 
// 	/**
// 	 * Filter single author permalink.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    string    $permalink Permalink HTML output.
// 	 * @param    object    $permalink_object Permalink instance.
// 	 * @param    string    $author Default text.
// 	 */
// 	return apply_filters( 'wpmoly/filter/permalink/author', $permalink->toHTML(), $permalink, $author );
// }
// 
// /**
//  * Build a permalink for certifications.
//  * 
//  * Uses \wpmoly\Permalink() to generate custom URLs for certifications.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $certification Movie certification.
//  * 
//  * @return   string
//  */
// function get_movie_certification_url( $certification ) {
// 
// 	if ( empty( $certification ) ) {
// 		return $certification;
// 	}
// 
// 	$permalink = new Permalink;
// 	$permalink->setID( 'certification' );
// 	$permalink->setContent( sanitize_title_with_dashes( $certification ) );
// 	$permalink->setTitle( $certification );
// 	$permalink->setTitleAttr( sprintf( __( '%s rated movies', 'wpmovielibrary' ), $certification ) );
// 
// 	/**
// 	 * Filter single certification permalink.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    string    $permalink Permalink HTML output.
// 	 * @param    object    $permalink_object Permalink instance.
// 	 * @param    string    $certification Default text.
// 	 */
// 	return apply_filters( 'wpmoly/filter/permalink/certification', $permalink->toHTML(), $permalink, $certification );
// }
// 
// /**
//  * Build a permalink for composers.
//  * 
//  * Uses \wpmoly\Permalink() to generate custom URLs for composers.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $composer Movie composer.
//  * 
//  * @return   string
//  */
// function get_movie_composer_url( $composer ) {
// 
// 	if ( empty( $composer ) ) {
// 		return $composer;
// 	}
// 
// 	$permalink = new Permalink;
// 	$permalink->setID( 'composer' );
// 	$permalink->setContent( sanitize_title_with_dashes( $composer ) );
// 	$permalink->setTitle( $composer );
// 	$permalink->setTitleAttr( sprintf( __( 'Movies from composer %s', 'wpmovielibrary' ), $composer ) );
// 
// 	/**
// 	 * Filter single composer permalink.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    string    $permalink Permalink HTML output.
// 	 * @param    object    $permalink_object Permalink instance.
// 	 * @param    string    $composer Default text.
// 	 */
// 	return apply_filters( 'wpmoly/filter/permalink/composer', $permalink->toHTML(), $permalink, $composer );
// }
// 
// /**
//  * Build a permalink for countries.
//  * 
//  * Uses \wpmoly\Permalink() to generate custom URLs for countries.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $country Formatted country.
//  * @param    object    $country_data Country instance.
//  * @param    object    $format Country format.
//  * 
//  * @return   string
//  */
// function get_movie_country_url( $country, $country_data, $format ) {
// 
// 	if ( 'flag' == $format || empty( $country ) ) {
// 		return $country;
// 	}
// 
// 	if ( $country_data instanceof \wpmoly\Helpers\Country ) {
// 
// 		$permalink = new Permalink;
// 		$permalink->setID( 'country' );
// 		$permalink->setContent( $country_data->code );
// 		$permalink->setTitle( $country );
// 		$permalink->setTitleAttr( sprintf( __( 'Movies produced in %s', 'wpmovielibrary' ), $country_data->localized_name ) );
// 
// 		/**
// 		 * Filter single country permalink.
// 		 * 
// 		 * @since    3.0
// 		 * 
// 		 * @param    string    $permalink Permalink HTML output.
// 		 * @param    object    $permalink_object Permalink instance.
// 		 * @param    string    $country Default text.
// 		 */
// 		return apply_filters( 'wpmoly/filter/permalink/country', $permalink->toHTML(), $permalink, $country );
// 	}
// 
// 	return $country;
// }
// 
// /**
//  * Build a permalink for formats.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $format Format value.
//  * @param    array     $options Permalink options.
//  * 
//  * @return   string
//  */
// function get_movie_format_url( $format, $options = array() ) {
// 
// 	return get_movie_detail_url( 'format', $format, $options );
// }
// 
// /**
//  * Build a permalink for homepages.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $format Formatted value.
//  * @param    array     $options Permalink options.
//  * 
//  * @return   string
//  */
// function get_movie_homepage_url( $homepage, $options = array() ) {
// 
// 	if ( empty( $homepage ) ) {
// 
// 		/**
// 		 * Filter empty permalink value.
// 		 * 
// 		 * @param    string    $value .
// 		 */
// 		return apply_filters( 'wpmoly/filter/permalink/empty/homepage', $homepage );
// 	}
// 
// 	$url = sprintf( '<a href="%1$s" title="%2$s">%1$s</a>', esc_url( trailingslashit( $homepage ) ), __( 'Official Website', 'wpmovielibrary' ) );
// 
// 	/**
// 	 * Filter a movie homepage URL.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    string    $url Homepage URL.
// 	 * @param    string    $homepage Homepage value.
// 	 */
// 	return apply_filters( "wpmoly/filter/permalink/homepage/url", $url, $homepage );
// }
// 
// /**
//  * Build a permalink for languages.
//  * 
//  * Uses \wpmoly\Permalink() to generate custom URLs for languages.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $language Formatted language.
//  * @param    array     $options Permalink options.
//  * 
//  * @return   string
//  */
// function get_movie_language_url( $language, $options = array() ) {
// 
// 	$options = wp_parse_args( (array) $options, array(
// 		'content'  => '',
// 		'title'    => '',
// 		'variant'  => ''
// 	) );
// 
// 	$variant = 'language';
// 	if ( ! empty( $options['variant'] ) ) {
// 		$variant = $options['variant'];
// 	}
// 
// 	$url = generate_movie_meta_url( $variant, $language );
// 
// 	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';
// 
// 	/**
// 	 * Filter detail permalink.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    string    $permalink Permalink HTML output.
// 	 * @param    string    $detail Detail type.
// 	 * @param    string    $value Detail value.
// 	 * @param    array     $options Permalink options.
// 	 */
// 	return apply_filters( "wpmoly/filter/permalink/language", $permalink, $language, $options );
// }
// 
// /**
//  * Build a permalink for media.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $media Media value.
//  * @param    array     $options Permalink options.
//  * 
//  * @return   string
//  */
// function get_movie_media_url( $media, $options = array() ) {
// 
// 	return get_movie_detail_url( 'media', $media, $options );
// }
// 
// /**
//  * Build a permalink for directors of photography.
//  * 
//  * Uses \wpmoly\Permalink() to generate custom URLs for directors of
//  * photography.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $company Movie director of photography.
//  * 
//  * @return   string
//  */
// function get_movie_photographer_url( $photographer ) {
// 
// 	if ( empty( $photographer ) ) {
// 		return $photographer;
// 	}
// 
// 	$permalink = new Permalink;
// 	$permalink->setID( 'photography' );
// 	$permalink->setContent( sanitize_title_with_dashes( $photographer ) );
// 	$permalink->setTitle( $photographer );
// 	$permalink->setTitleAttr( sprintf( __( 'Movies from Director of Photography %s', 'wpmovielibrary' ), $photographer ) );
// 
// 	/**
// 	 * Filter single photographer permalink.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    string    $permalink Permalink HTML output.
// 	 * @param    object    $permalink_object Permalink instance.
// 	 * @param    string    $photographer Default text.
// 	 */
// 	return apply_filters( 'wpmoly/filter/permalink/photographer', $permalink->toHTML(), $permalink, $photographer );
// }
// 
// /**
//  * Build a permalink for producers.
//  * 
//  * Uses \wpmoly\Permalink() to generate custom URLs for producer.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $producer Movie producer
//  * 
//  * @return   string
//  */
// function get_movie_producer_url( $producer ) {
// 
// 	if ( empty( $producer ) ) {
// 		return $producer;
// 	}
// 
// 	$permalink = new Permalink;
// 	$permalink->setID( 'producer' );
// 	$permalink->setContent( sanitize_title_with_dashes( $producer ) );
// 	$permalink->setTitle( $producer );
// 	$permalink->setTitleAttr( sprintf( __( 'Movies produced by %s', 'wpmovielibrary' ), $producer ) );
// 
// 	/**
// 	 * Filter single producer permalink.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    string    $permalink Permalink HTML output.
// 	 * @param    object    $permalink_object Permalink instance.
// 	 * @param    string    $producer Default text.
// 	 */
// 	return apply_filters( 'wpmoly/filter/permalink/producer', $permalink->toHTML(), $permalink, $producer );
// }
// 
// /**
//  * Build a permalink for production companies.
//  * 
//  * Uses \wpmoly\Permalink() to generate custom URLs for production companies.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $company Movie production company
//  * 
//  * @return   string
//  */
// function get_movie_production_url( $company ) {
// 
// 	if ( empty( $company ) ) {
// 		return $company;
// 	}
// 
// 	$permalink = new Permalink;
// 	$permalink->setID( 'production' );
// 	$permalink->setContent( sanitize_title_with_dashes( $company ) );
// 	$permalink->setTitle( $company );
// 	$permalink->setTitleAttr( sprintf( __( 'Movies produced by %s', 'wpmovielibrary' ), $company ) );
// 
// 	/**
// 	 * Filter single production permalink.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    string    $permalink Permalink HTML output.
// 	 * @param    object    $permalink_object Permalink instance.
// 	 * @param    string    $company Default text.
// 	 */
// 	return apply_filters( 'wpmoly/filter/permalink/production', $permalink->toHTML(), $permalink, $company );
// }
// 
// /**
//  * Build a permalink for ratings.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $rating Rating value.
//  * @param    array     $options Permalink options.
//  * 
//  * @return   string
//  */
// function get_movie_rating_url( $rating, $options = array() ) {
// 
// 	$options = wp_parse_args( (array) $options, array(
// 		'content' => '',
// 		'title'   => ''
// 	) );
// 
// 	$url = generate_movie_meta_url( 'rating', floatval( $rating ) );
// 
// 	$content = wp_kses( $options['content'], array(
// 		'span' => array(
// 			'title' => array(),
// 			'class' => array(),
// 			'id'    => array()
// 		)
// 	) );
// 
// 	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . $content . '</a>';
// 
// 	/**
// 	 * Filter detail permalink.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    string    $permalink Permalink HTML output.
// 	 * @param    string    $detail Detail type.
// 	 * @param    string    $value Detail value.
// 	 * @param    array     $options Permalink options.
// 	 */
// 	return apply_filters( "wpmoly/filter/permalink/rating", $permalink, $rating, $options );
// }
// 
// /**
//  * Build a permalink for dates.
//  * 
//  * Uses \wpmoly\Permalink() to generate custom URLs for release dates and local
//  * release dates.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $date Filtered date.
//  * @param    array     $raw_date Unfiltered date
//  * @param    array     $date_parts Date parts, if need be
//  * @param    string    $date_format Date format
//  * @param    int       $timestamp Date UNIX Timestamp
//  * @param    string    $variant Local release date variant
//  * 
//  * @return   string
//  */
// function get_movie_release_date_url( $date, $raw_date = array(), $date_parts = array(), $date_format = '', $timestamp = '', $variant = '' ) {
// 
// 	if ( empty( $raw_date ) ) {
// 		return $date;
// 	}
// 
// 	$variant = (string) $variant;
// 	if ( 'local_' != $variant ) {
// 		$variant = '';
// 	}
// 
// 	if ( ! empty( $variant ) ) {
// 		$id = 'local-release-date';
// 		$attr_title = __( 'Movies localy released in %s', 'wpmovielibrary' );
// 	} else {
// 		$id = 'release-date';
// 		$attr_title = __( 'Movies released in %s', 'wpmovielibrary' );
// 	}
// 
// 	$permalink = new Permalink;
// 	switch ( $date_format ) {
// 		case 'Y':
// 			$permalink->setID( $id );
// 			$permalink->setContent( date_i18n( $date_format, $timestamp ) );
// 			$permalink->setTitle( $date );
// 			$permalink->setTitleAttr( sprintf( $attr_title, date_i18n( $date_format, $timestamp ) ) );
// 			break;
// 		case 'j F':
// 			$permalink->setID( $id );
// 			$permalink->setContent( date_i18n( 'Y-m', $timestamp ) );
// 			$permalink->setTitle( $date );
// 			$permalink->setTitleAttr( sprintf( $attr_title, date_i18n( $date_format, $timestamp ) ) );
// 			break;
// 		case 'j F Y':
// 			$month = get_movie_release_date_url( $date_parts[0], $raw_date, $date_parts, $date_format = 'j F', $timestamp, $variant );
// 			$year  = get_movie_release_date_url( $date_parts[1], $raw_date, $date_parts, $date_format = 'Y',   $timestamp, $variant );
// 			$permalink = $month . ' ' . $year;
// 			break;
// 		default:
// 			break;
// 	}
// 
// 	if ( ! $permalink instanceof Permalink ) {
// 		return $permalink;
// 	}
// 
// 	/**
// 	 * Filter date permalink.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    string    $permalink Permalink HTML output.
// 	 * @param    object    $permalink_object Permalink instance.
// 	 * @param    string    $date Default text.
// 	 */
// 	return apply_filters( 'wpmoly/filter/permalink/date', $permalink->toHTML(), $permalink, $date );
// }
// 
// /**
//  * Build a permalink for statuses.
//  * 
//  * Uses \wpmoly\Permalink() to generate custom URLs for statuses.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $status Status value.
//  * @param    array     $options Permalink options.
//  * 
//  * @return   string
//  */
// function get_movie_status_url( $status, $options = array() ) {
// 
// 	return get_movie_detail_url( 'status', $status, $options );
// }
// 
// /**
//  * Build a permalink for subtitles.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $status Subtitles value.
//  * @param    array     $options Permalink options.
//  * 
//  * @return   string
//  */
// function get_movie_subtitles_url( $subtitles, $options = array() ) {
// 
// 	return get_movie_detail_url( 'subtitles', $subtitles, $options );
// }
// 
// /**
//  * Build a permalink for writers.
//  * 
//  * Uses \wpmoly\Permalink() to generate custom URLs for writers.
//  * 
//  * @since    3.0
//  * 
//  * @param    string    $writer Movie writer.
//  * 
//  * @return   string
//  */
// function get_movie_writer_url( $writer ) {
// 
// 	if ( empty( $writer ) ) {
// 		return $writer;
// 	}
// 
// 	$permalink = new Permalink;
// 	$permalink->setID( 'writer' );
// 	$permalink->setContent( sanitize_title_with_dashes( $writer ) );
// 	$permalink->setTitle( $writer );
// 	$permalink->setTitleAttr( sprintf( __( 'Movies from writer %s', 'wpmovielibrary' ), $writer ) );
// 
// 	/**
// 	 * Filter single writer permalink.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    string    $permalink Permalink HTML output.
// 	 * @param    object    $permalink_object Permalink instance.
// 	 * @param    string    $writer Default text.
// 	 */
// 	return apply_filters( 'wpmoly/filter/permalink/writer', $permalink->toHTML(), $permalink, $writer );
// }
