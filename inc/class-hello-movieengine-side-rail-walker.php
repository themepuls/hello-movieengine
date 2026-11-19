<?php
/**
 * Side rail navigation walker (OTT flyouts + mega panel).
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Walker for Style Two fixed left rail.
 */
class Hello_Movie_Engine_Side_Rail_Walker extends Walker_Nav_Menu {

	/**
	 * @var int Flyout group index for animation delay.
	 */
	protected $top_index = 0;

	/**
	 * @var string Parent title for flyout header.
	 */
	protected $fly_heading = '';

	/**
	 * @var bool Parent is mega menu.
	 */
	protected $fly_mega = false;

	/**
	 * @var int Mega column count.
	 */
	protected $fly_mega_cols = 3;

	/**
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 !== (int) $depth ) {
			$output .= '<ul class="hello-movieengine-side-rail__fly-sub">';
			return;
		}

		$cols = max( 2, min( 8, (int) $this->fly_mega_cols ) );
		$mega = $this->fly_mega ? ' hello-movieengine-side-rail__fly--mega' : '';
		$style = $this->fly_mega ? ' style="--hm-side-fly-cols:' . $cols . ';"' : '';

		$output .= '<div class="hello-movieengine-side-rail__fly' . $mega . '"' . $style . ' hidden>';
		if ( $this->fly_heading ) {
			$output .= '<div class="hello-movieengine-side-rail__fly-hd">' . esc_html( $this->fly_heading ) . '</div>';
		}
		$output .= '<ul class="hello-movieengine-side-rail__fly-list">';
	}

	/**
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 !== (int) $depth ) {
			$output .= '</ul>';
			return;
		}

		$output .= '</ul></div>';
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
		$is_active    = in_array( 'current-menu-item', $classes, true )
			|| in_array( 'current-menu-ancestor', $classes, true )
			|| in_array( 'current-menu-parent', $classes, true );

		if ( 0 === (int) $depth ) {
			$is_mega    = in_array( 'mega-menu', $classes, true );
			$mega_cols  = 3;
			foreach ( $classes as $cl ) {
				if ( preg_match( '/^mega-columns-(\d+)$/', $cl, $m ) ) {
					$mega_cols = (int) $m[1];
				}
			}

			$this->fly_heading   = $item->title;
			$this->fly_mega      = $is_mega;
			$this->fly_mega_cols = $mega_cols;

			$grp_cl = 'hello-movieengine-side-rail__grp';
			if ( $has_children ) {
				$grp_cl .= ' has-flyout';
			}
			if ( $is_mega ) {
				$grp_cl .= ' has-mega-flyout';
			}

			$output .= '<div class="' . esc_attr( $grp_cl ) . '" style="--i:' . (int) $this->top_index . ';">';
			++$this->top_index;

			$is_search = in_array( 'side-rail-search', $classes, true ) || '#hello-search' === $item->url;

			if ( $is_search ) {
				$output .= $this->render_top_button( $item, $classes, $is_active );
				return;
			}

			$output .= $this->render_top_link( $item, $classes, $is_active, $has_children );
			return;
		}

		$li_cl = 'hello-movieengine-side-rail__fly-li';
		if ( in_array( 'mega-heading', $classes, true ) ) {
			$li_cl .= ' is-mega-heading';
		}
		if ( $has_children ) {
			$li_cl .= ' has-children';
		}
		if ( (int) $depth >= 2 ) {
			$li_cl .= ' is-nested';
		}

		$output .= '<li class="' . esc_attr( $li_cl ) . '">';
		$output .= '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
	}

	/**
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( 0 === (int) $depth ) {
			$output .= '</div>';
			return;
		}
		$output .= '</li>';
	}

	/**
	 * @param WP_Post  $item         Item.
	 * @param string[] $classes      Classes.
	 * @param bool     $is_active    Active.
	 * @param bool     $has_children Has children.
	 * @return string
	 */
	protected function render_top_link( $item, $classes, $is_active, $has_children ) {
		$item_cl = 'hello-movieengine-side-rail__it';
		if ( $is_active ) {
			$item_cl .= ' is-active';
		}
		if ( $has_children ) {
			$item_cl .= ' has-children';
		}

		$atts = '';
		if ( $has_children ) {
			$atts .= ' aria-haspopup="true" aria-expanded="false"';
		}

		$html  = '<a href="' . esc_url( $item->url ) . '" class="' . esc_attr( $item_cl ) . '"' . $atts;
		if ( $is_active ) {
			$html .= ' aria-current="page"';
		}
		$html .= '>';
		$html .= '<span class="hello-movieengine-side-rail__bar" aria-hidden="true"></span>';
		$html .= '<span class="hello-movieengine-side-rail__icon" aria-hidden="true">';
		ob_start();
		hello_movieengine_render_menu_item_icon( $item );
		$html .= ob_get_clean();
		$html .= '</span>';
		$html .= '<b class="hello-movieengine-side-rail__label">' . esc_html( $item->title ) . '</b>';
		$html .= '</a>';

		return $html;
	}

	/**
	 * @param WP_Post  $item      Item.
	 * @param string[] $classes   Classes.
	 * @param bool     $is_active Active.
	 * @return string
	 */
	protected function render_top_button( $item, $classes, $is_active ) {
		$item_cl = 'hello-movieengine-side-rail__it hello-movieengine-side-rail__btn hello-movieengine-side-rail__btn--search';
		if ( $is_active ) {
			$item_cl .= ' is-active';
		}

		$html  = '<button type="button" class="' . esc_attr( $item_cl ) . '" data-type="search" data-url="' . esc_url( $item->url ) . '" aria-label="' . esc_attr( $item->title ) . '">';
		$html .= '<span class="hello-movieengine-side-rail__bar" aria-hidden="true"></span>';
		$html .= '<span class="hello-movieengine-side-rail__icon" aria-hidden="true">';
		ob_start();
		hello_movieengine_render_menu_item_icon( $item );
		$html .= ob_get_clean();
		$html .= '</span>';
		$html .= '<b class="hello-movieengine-side-rail__label">' . esc_html( $item->title ) . '</b>';
		$html .= '</button>';

		return $html;
	}

	/**
	 * Detect children for top-level items.
	 *
	 * @param WP_Post  $element Element.
	 * @param array    $children_map Children map.
	 * @param int      $max_depth Max depth.
	 * @param int      $depth Depth.
	 * @param stdClass $args Args.
	 * @param string   $output Output.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element ) {
			return;
		}

		$id_field = $this->db_fields['id'];
		$id       = $element->$id_field;

		if ( isset( $args[0] ) && is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[ $id ] );
		}

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}