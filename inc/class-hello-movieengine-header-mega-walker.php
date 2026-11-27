<?php
/**
 * Header primary nav walker — mega menu + banner / movies blocks (Style One & Two).
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Walker for header .hello-movieengine-nav-menu.
 */
class Hello_Movie_Engine_Header_Mega_Walker extends Walker_Nav_Menu {

	/**
	 * @var string Parent mega title (banner eyebrow fallback).
	 */
	protected $parent_heading = '';

	/**
	 * @var bool Parent has mega-menu class.
	 */
	protected $parent_mega = false;

	/**
	 * @var int Mega column count.
	 */
	protected $parent_mega_cols = 3;

	/**
	 * @var string Mega panel width (CSS length).
	 */
	protected $parent_mega_width = '860px';

	/**
	 * @var bool Rich layout (banner / movies).
	 */
	protected $parent_mega_rich = false;

	/**
	 * @var bool Rich panel banner column is open.
	 */
	protected $rich_banner_open = false;

	/**
	 * @var bool Rich panel main is open.
	 */
	protected $rich_main_open = false;

	/**
	 * @var bool Rich column list is open.
	 */
	protected $rich_cols_open = false;

	/**
	 * @var bool Rich movies region is open.
	 */
	protected $rich_movies_open = false;

	/**
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 === (int) $depth ) {
			$ul_class = 'sub-menu';
			if ( $this->parent_mega_rich ) {
				$ul_class .= ' hello-movieengine-header-mega--rich';
			}
			$cols = max( 2, min( 8, (int) $this->parent_mega_cols ) );
			$style = $this->parent_mega_rich
				? ' style="--hm-side-fly-cols:' . (int) $cols . ';--hm-mega-w:' . esc_attr( $this->parent_mega_width ) . ';"'
				: '';
			$output .= '<ul class="' . esc_attr( $ul_class ) . '"' . $style . '>';
			if ( $this->parent_mega && $this->parent_mega_rich ) {
				$this->rich_banner_open = false;
				$this->rich_main_open    = false;
				$this->rich_cols_open    = false;
				$this->rich_movies_open  = false;
				$output                 .= '<li class="hello-movieengine-header-mega-rich-host"><div class="hello-movieengine-mega-panel">';
			}
			return;
		}

		if ( 1 === (int) $depth && $this->parent_mega && $this->parent_mega_rich ) {
			$output .= '<ul class="sub-menu hello-movieengine-header-mega__sub">';
			return;
		}

		$output .= '<ul class="sub-menu">';
	}

	/**
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 === (int) $depth && $this->parent_mega && $this->parent_mega_rich ) {
			$output .= $this->close_rich_regions();
			$output .= '</div></li>';
		}
		$output .= '</ul>';
	}

	/**
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     Item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes = array_filter( $classes );

		$has_children = ! empty( $args->has_children ) || in_array( 'menu-item-has-children', $classes, true );

		if ( 0 === (int) $depth ) {
			$this->parent_heading    = $item->title;
			$this->parent_mega       = in_array( 'mega-menu', $classes, true );
			$this->parent_mega_cols  = 3;
			$this->parent_mega_width = function_exists( 'hello_movieengine_get_mega_width_css' )
				? hello_movieengine_get_mega_width_css( $item->ID )
				: '860px';
			foreach ( $classes as $cl ) {
				if ( preg_match( '/^mega-columns-(\d+)$/', $cl, $m ) ) {
					$this->parent_mega_cols = (int) $m[1];
				}
			}
			if ( $this->parent_mega && function_exists( 'hello_movieengine_mega_allowed_columns' ) ) {
				$meta_cols = absint( get_post_meta( $item->ID, HELLO_MOVIEENGINE_MEGA_COLUMNS, true ) );
				if ( in_array( $meta_cols, hello_movieengine_mega_allowed_columns(), true ) ) {
					$this->parent_mega_cols = $meta_cols;
				}
			}
		}

		$layout = function_exists( 'hello_movieengine_get_mega_block_layout' )
			? hello_movieengine_get_mega_block_layout( $item->ID )
			: 'default';

		if ( 1 === (int) $depth && $this->parent_mega && $this->parent_mega_rich && 'banner' === $layout ) {
			$output .= $this->open_rich_banner();
			ob_start();
			hello_movieengine_render_mega_banner_block( $item, $this->parent_heading );
			$output .= ob_get_clean();
			return;
		}

		if ( 1 === (int) $depth && $this->parent_mega && $this->parent_mega_rich && 'movies' === $layout ) {
			$output .= $this->open_rich_movies();
			ob_start();
			hello_movieengine_render_mega_movies_block( $item );
			$output .= ob_get_clean();
			return;
		}

		if ( 1 === (int) $depth && $this->parent_mega && $this->parent_mega_rich && ! in_array( $layout, array( 'banner', 'movies' ), true ) ) {
			$output .= $this->open_rich_cols();
			$classes[] = 'hello-movieengine-header-mega__col';
			if ( $has_children || in_array( 'mega-heading', $classes, true ) ) {
				$classes[] = 'hello-movieengine-header-mega__col--titled';
			}
		}

		$class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= '<li' . $class_names . '>';

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$is_col_title = 1 === (int) $depth
			&& $this->parent_mega
			&& $this->parent_mega_rich
			&& ! in_array( $layout, array( 'banner', 'movies' ), true )
			&& ( $has_children || in_array( 'mega-heading', $classes, true ) );

		if ( $is_col_title ) {
			$output .= '<span class="hello-movieengine-header-mega__col-title">';
			$output .= '<span class="hello-movieengine-header-mega__col-title-icon" aria-hidden="true">';
			if ( function_exists( 'hello_movieengine_render_menu_item_icon' ) ) {
				ob_start();
				hello_movieengine_render_menu_item_icon( $item );
				$icon_html = ob_get_clean();
				$output   .= $icon_html ? $icon_html : '<span class="hello-movieengine-header-mega__col-title-dot"></span>';
			} else {
				$output .= '<span class="hello-movieengine-header-mega__col-title-dot"></span>';
			}
			$output .= '</span>';
			$output .= '<span class="hello-movieengine-header-mega__col-title-text">' . esc_html( $title ) . '</span>';
			$output .= '</span>';
			return;
		}

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$output .= '<a' . $attributes . '>';
		$output .= esc_html( $title );
		$output .= '</a>';
	}

	/**
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$layout = function_exists( 'hello_movieengine_get_mega_block_layout' )
			? hello_movieengine_get_mega_block_layout( $item->ID )
			: 'default';

		if ( 1 === (int) $depth && $this->parent_mega && $this->parent_mega_rich && in_array( $layout, array( 'banner', 'movies' ), true ) ) {
			return;
		}

		$output .= '</li>';
	}

	/**
	 * @return string
	 */
	protected function close_rich_regions() {
		$html = '';
		if ( $this->rich_cols_open ) {
			$html                 .= '</ul>';
			$this->rich_cols_open  = false;
		}
		if ( $this->rich_movies_open ) {
			$html                   .= '</div>';
			$this->rich_movies_open  = false;
		}
		if ( $this->rich_main_open ) {
			$html                 .= '</div>';
			$this->rich_main_open  = false;
		}
		if ( $this->rich_banner_open ) {
			$html                  .= '</div>';
			$this->rich_banner_open  = false;
		}
		return $html;
	}

	/**
	 * @return string
	 */
	protected function open_rich_banner() {
		$html = '';
		if ( $this->rich_cols_open ) {
			$html                .= '</ul>';
			$this->rich_cols_open = false;
		}
		if ( $this->rich_movies_open ) {
			$html                  .= '</div>';
			$this->rich_movies_open = false;
		}
		if ( $this->rich_main_open ) {
			$html                .= '</div>';
			$this->rich_main_open = false;
		}
		if ( ! $this->rich_banner_open ) {
			$html                  .= '<div class="hello-movieengine-mega-panel__banner">';
			$this->rich_banner_open = true;
		}
		return $html;
	}

	/**
	 * @return string
	 */
	protected function open_rich_main() {
		$html = '';
		if ( $this->rich_banner_open ) {
			$html                   .= '</div>';
			$this->rich_banner_open   = false;
		}
		if ( ! $this->rich_main_open ) {
			$html                .= '<div class="hello-movieengine-mega-panel__main">';
			$this->rich_main_open = true;
		}
		return $html;
	}

	/**
	 * @return string
	 */
	protected function open_rich_cols() {
		$html = $this->open_rich_main();
		if ( $this->rich_movies_open ) {
			$html                  .= '</div>';
			$this->rich_movies_open = false;
		}
		if ( ! $this->rich_cols_open ) {
			$html                .= '<ul class="hello-movieengine-mega-panel__cols hello-movieengine-header-mega__cols">';
			$this->rich_cols_open = true;
		}
		return $html;
	}

	/**
	 * @return string
	 */
	protected function open_rich_movies() {
		$html = $this->open_rich_main();
		if ( $this->rich_cols_open ) {
			$html                .= '</ul>';
			$this->rich_cols_open = false;
		}
		if ( ! $this->rich_movies_open ) {
			$html                  .= '<div class="hello-movieengine-mega-panel__movies">';
			$this->rich_movies_open = true;
		}
		return $html;
	}

	/**
	 * @param WP_Post  $element          Element.
	 * @param array    $children_elements Children.
	 * @param int      $max_depth        Max depth.
	 * @param int      $depth            Depth.
	 * @param stdClass $args             Args.
	 * @param string   $output           Output.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element ) {
			return;
		}

		$id_field = $this->db_fields['id'];
		$id       = $element->$id_field;

		if ( 1 === (int) $depth && function_exists( 'hello_movieengine_get_mega_block_layout' ) ) {
			$layout = hello_movieengine_get_mega_block_layout( $id );
			if ( function_exists( 'hello_movieengine_mega_is_rich_layout' ) && hello_movieengine_mega_is_rich_layout( $layout ) ) {
				unset( $children_elements[ $id ] );
			}
		}

		if ( isset( $args[0] ) && is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[ $id ] );
		}

		if ( 0 === (int) $depth ) {
			$this->parent_mega_rich = false;
		}

		if ( 0 === (int) $depth && ! empty( $children_elements[ $id ] ) ) {
			foreach ( $children_elements[ $id ] as $child ) {
				$child_layout = function_exists( 'hello_movieengine_get_mega_block_layout' )
					? hello_movieengine_get_mega_block_layout( $child->ID )
					: 'default';
				if ( function_exists( 'hello_movieengine_mega_is_rich_layout' ) && hello_movieengine_mega_is_rich_layout( $child_layout ) ) {
					$this->parent_mega_rich = true;
					break;
				}
			}
		}

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}