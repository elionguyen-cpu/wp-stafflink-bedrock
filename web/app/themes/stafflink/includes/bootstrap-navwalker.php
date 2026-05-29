<?php
/**
 * Bootstrap compatible nav walker.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Bootstrap_Navwalker' ) ) {
	class WP_Bootstrap_Navwalker extends Walker_Nav_Menu {
		/**
		 * Start submenu level.
		 *
		 * @param string $output Used to append markup.
		 * @param int    $depth  Menu depth.
		 * @param object $args   Menu args.
		 */
		public function start_lvl( &$output, $depth = 0, $args = null ) {
			$indent  = str_repeat( "\t", $depth );
			$output .= "\n$indent<ul class=\"dropdown-menu\">\n";
		}

		/**
		 * Start menu item.
		 *
		 * @param string  $output Used to append markup.
		 * @param WP_Post $item   Menu item.
		 * @param int     $depth  Menu depth.
		 * @param object  $args   Menu args.
		 * @param int     $id     Menu item ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$classes      = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes      = array_filter( $classes );
			$has_children = in_array( 'menu-item-has-children', $classes, true );

			if ( 0 === $depth ) {
				$classes[] = 'nav-item';
			}

			if ( $has_children ) {
				$classes[] = 'dropdown';
			}

			$class_names = implode( ' ', array_map( 'sanitize_html_class', $classes ) );
			$output     .= '<li class="' . esc_attr( $class_names ) . '">';

			$atts           = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target ) ? $item->target : '';
			$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
			$atts['href']   = ! empty( $item->url ) ? $item->url : '';
			$atts['class']  = 0 === $depth ? 'nav-link' : 'dropdown-item';

			if ( $has_children && 0 === $depth ) {
				$atts['class']         .= ' dropdown-toggle';
				$atts['role']           = 'button';
				$atts['data-bs-toggle'] = 'dropdown';
				$atts['aria-expanded']  = 'false';
			}

			$attributes = '';

			foreach ( $atts as $attr => $value ) {
				if ( '' === $value ) {
					continue;
				}

				$value       = 'href' === $attr ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}

			$title = apply_filters( 'the_title', $item->title, $item->ID );
			$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

			$item_output  = isset( $args->before ) ? $args->before : '';
			$item_output .= '<a' . $attributes . '>';
			$item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . $title . ( isset( $args->link_after ) ? $args->link_after : '' );
			$item_output .= '</a>';
			$item_output .= isset( $args->after ) ? $args->after : '';

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
}
