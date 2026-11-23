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
	 * @var string CSS width for rich mega panel.
	 */
	protected $fly_mega_width = '860px';

	/**
	 * @var bool Flyout uses banner / movie blocks.
	 */
	protected $fly_mega_rich = false;

	/**
	 * @var bool Rich panel banner column is open.
	 */
	protected $rich_banner_open = false;

	/**
	 * @var bool Rich panel main (menu + movies) is open.
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
		if ( 0 !== (int) $depth ) {
			$output .= '<ul class="hello-movieengine-side-rail__fly-sub">';
			return;
		}

		$cols  = max( 2, min( 8, (int) $this->fly_mega_cols ) );
		$mega  = $this->fly_mega ? ' hello-movieengine-side-rail__fly--mega' : '';
		$rich  = ( $this->fly_mega && $this->fly_mega_rich ) ? ' hello-movieengine-side-rail__fly--mega-rich' : '';
		$style = '';
		if ( $this->fly_mega ) {
			$width = $this->fly_mega_width ? $this->fly_mega_width : '860px';
			$style = ' style="--hm-side-fly-cols:' . $cols . ';--hm-mega-w:' . esc_attr( $width ) . ';"';
		}

		$output .= '<div class="hello-movieengine-side-rail__fly' . $mega . $rich . '"' . $style . ' hidden>';
		if ( $this->fly_heading && ! ( $this->fly_mega && $this->fly_mega_rich ) ) {
			$output .= '<div class="hello-movieengine-side-rail__fly-hd">' . esc_html( $this->fly_heading ) . '</div>';
		}

		if ( $this->fly_mega && $this->fly_mega_rich ) {
			$this->rich_banner_open  = false;
			$this->rich_main_open    = false;
			$this->rich_cols_open    = false;
			$this->rich_movies_open  = false;
			$output                 .= '<div class="hello-movieengine-mega-panel">';
			return;
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

		if ( $this->fly_mega && $this->fly_mega_rich ) {
			$output .= $this->close_rich_regions();
			$output .= '</div></div>';
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

			$this->fly_heading    = $item->title;
			$this->fly_mega       = $is_mega;
			$this->fly_mega_cols  = $mega_cols;
			$this->fly_mega_width = function_exists( 'hello_movieengine_get_mega_width_css' )
				? hello_movieengine_get_mega_width_css( $item->ID )
				: '860px';

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

		$layout = function_exists( 'hello_movieengine_get_mega_block_layout' )
			? hello_movieengine_get_mega_block_layout( $item->ID )
			: 'default';

		if ( 1 === (int) $depth && $this->fly_mega && $this->fly_mega_rich && 'banner' === $layout ) {
			$output .= $this->open_rich_banner();
			ob_start();
			hello_movieengine_render_mega_banner_block( $item, $this->fly_heading );
			$output .= ob_get_clean();
			return;
		}

		if ( 1 === (int) $depth && $this->fly_mega && $this->fly_mega_rich && 'movies' === $layout ) {
			$output .= $this->open_rich_movies();
			ob_start();
			hello_movieengine_render_mega_movies_block( $item );
			$output .= ob_get_clean();
			return;
		}

		if ( 1 === (int) $depth && $this->fly_mega && $this->fly_mega_rich ) {
			$output .= $this->open_rich_cols();
		}

		$li_cl = 'hello-movieengine-side-rail__fly-li';
		if ( $this->fly_mega && 1 === (int) $depth ) {
			$li_cl .= ' is-mega-col';
		}
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

		$is_col_title = $this->fly_mega
			&& 1 === (int) $depth
			&& ( $has_children || in_array( 'mega-heading', $classes, true ) );

		if ( $is_col_title ) {
			$output .= '<span class="hello-movieengine-side-rail__fly-col-title">';
			$output .= '<span class="hello-movieengine-side-rail__fly-col-title-icon" aria-hidden="true">';
			if ( function_exists( 'hello_movieengine_render_menu_item_icon' ) ) {
				ob_start();
				hello_movieengine_render_menu_item_icon( $item );
				$icon_html = ob_get_clean();
				$output   .= $icon_html ? $icon_html : '<span class="hello-movieengine-side-rail__fly-col-title-dot"></span>';
			} else {
				$output .= '<span class="hello-movieengine-side-rail__fly-col-title-dot"></span>';
			}
			$output .= '</span>';
			$output .= '<span class="hello-movieengine-side-rail__fly-col-title-text">' . esc_html( $item->title ) . '</span>';
			$output .= '</span>';
		} else {
			$output .= '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
		}
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

		$layout = function_exists( 'hello_movieengine_get_mega_block_layout' )
			? hello_movieengine_get_mega_block_layout( $item->ID )
			: 'default';

		if ( 1 === (int) $depth && $this->fly_mega && $this->fly_mega_rich && in_array( $layout, array( 'banner', 'movies' ), true ) ) {
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
			$html                   .= '</ul>';
			$this->rich_cols_open    = false;
		}
		if ( $this->rich_movies_open ) {
			$html                    .= '</div>';
			$this->rich_movies_open   = false;
		}
		if ( $this->rich_main_open ) {
			$html                 .= '</div>';
			$this->rich_main_open  = false;
		}
		if ( $this->rich_banner_open ) {
			$html                   .= '</div>';
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
			$html                    .= '<div class="hello-movieengine-mega-panel__banner">';
			$this->rich_banner_open   = true;
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
			$this->rich_banner_open  = false;
		}
		if ( ! $this->rich_main_open ) {
			$html                 .= '<div class="hello-movieengine-mega-panel__main">';
			$this->rich_main_open  = true;
		}
		return $html;
	}

	/**
	 * @return string
	 */
	protected function open_rich_cols() {
		$html  = $this->open_rich_main();
		if ( $this->rich_movies_open ) {
			$html                  .= '</div>';
			$this->rich_movies_open = false;
		}
		if ( ! $this->rich_cols_open ) {
			$html                .= '<ul class="hello-movieengine-mega-panel__cols hello-movieengine-side-rail__fly-list">';
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
			$this->fly_mega_rich = false;
		}

		if ( 0 === (int) $depth && ! empty( $children_elements[ $id ] ) ) {
			foreach ( $children_elements[ $id ] as $child ) {
				$child_layout = function_exists( 'hello_movieengine_get_mega_block_layout' )
					? hello_movieengine_get_mega_block_layout( $child->ID )
					: 'default';
				if ( function_exists( 'hello_movieengine_mega_is_rich_layout' ) && hello_movieengine_mega_is_rich_layout( $child_layout ) ) {
					$this->fly_mega_rich = true;
					break;
				}
			}
		}

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}