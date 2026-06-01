<?php
/**
 * Breadcrumb template part.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_front_page() || is_home() ) {
	return;
}

$text_domain = defined( 'TEXT_DOMAIN' ) ? TEXT_DOMAIN : 'wp-stafflink';
$items       = array(
	array(
		'label' => __( 'Home', $text_domain ),
		'url'   => home_url( '/' ),
	),
);

if ( is_page() ) {
	$post_id   = get_queried_object_id();
	$ancestors = array_reverse( get_post_ancestors( $post_id ) );

	foreach ( $ancestors as $ancestor_id ) {
		$items[] = array(
			'label' => get_the_title( $ancestor_id ),
			'url'   => get_permalink( $ancestor_id ),
		);
	}

	$items[] = array(
		'label' => get_the_title( $post_id ),
	);
} elseif ( is_singular( 'post' ) ) {
	$posts_page_id = (int) get_option( 'page_for_posts' );

	if ( $posts_page_id ) {
		$items[] = array(
			'label' => get_the_title( $posts_page_id ),
			'url'   => get_permalink( $posts_page_id ),
		);
	}

	$items[] = array(
		'label' => get_the_title(),
	);
} elseif ( is_singular( POST_TYPE_JOB ) ) {
	$jobseekers_page = get_page_by_path( 'jobseekers' );

	$items[] = array(
		'label' => $jobseekers_page ? get_the_title( $jobseekers_page ) : __( 'Jobseekers', $text_domain ),
		'url'   => $jobseekers_page ? get_permalink( $jobseekers_page ) : home_url( '/jobseekers/' ),
	);

	$items[] = array(
		'label' => get_the_title(),
	);
} elseif ( is_singular() ) {
	$post_type        = get_post_type();
	$post_type_object = $post_type ? get_post_type_object( $post_type ) : null;
	$archive_link     = $post_type ? get_post_type_archive_link( $post_type ) : '';

	if ( $post_type_object && $archive_link ) {
		$items[] = array(
			'label' => $post_type_object->labels->name,
			'url'   => $archive_link,
		);
	}

	$items[] = array(
		'label' => get_the_title(),
	);
} elseif ( is_search() ) {
	$items[] = array(
		'label' => sprintf(
			/* translators: %s: Search keyword. */
			__( 'Search results for: %s', $text_domain ),
			get_search_query( false )
		),
	);
} elseif ( is_404() ) {
	$items[] = array(
		'label' => __( 'Page not found', $text_domain ),
	);
} elseif ( is_archive() ) {
	$items[] = array(
		'label' => wp_strip_all_tags( get_the_archive_title() ),
	);
}

if ( count( $items ) < 2 ) {
	return;
}

$html  = '<div class="breadcrumb-wrapper">';
$html .= '<div class="container">';
$html .= '<nav aria-label="' . esc_attr__( 'Breadcrumb', $text_domain ) . '">';
$html .= '<ol class="breadcrumb">';

$last_index = count( $items ) - 1;

foreach ( $items as $index => $item ) {
	$label = isset( $item['label'] ) ? $item['label'] : '';

	if ( '' === $label ) {
		continue;
	}

	if ( $index === $last_index ) {
		$html .= '<li class="breadcrumb-item active" aria-current="page">' . esc_html( $label ) . '</li>';
		continue;
	}

	$url = isset( $item['url'] ) ? $item['url'] : '';

	if ( $url ) {
		$html .= '<li class="breadcrumb-item"><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	} else {
		$html .= '<li class="breadcrumb-item">' . esc_html( $label ) . '</li>';
	}
}

$html .= '</ol>';
$html .= '</nav>';
$html .= '</div>';
$html .= '</div>';

echo $html;
