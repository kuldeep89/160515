<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooSlider Sliders Class
 *
 * All functionality pertaining to the slideshows in WooSlider.
 *
 * @package WordPress
 * @subpackage WooSlider
 * @category Core
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - __construct()
 * - add()
 * - get_slides()
 * - render()
 * - slideshow_type_attachments()
 * - slideshow_type_posts()
 * - slideshow_type_slides()
 * - apply_default_filters_slides()
 * - add_wistia_support()
 */
class WooSlider_Sliders {
	public $token;
	public $sliders;

	/**
	 * Constructor.
	 * @since  1.0.0
	 */
	public function __construct () {
		add_action( 'init', array( $this, 'apply_default_filters_slides' ) );
		add_action( 'init', array( $this, 'add_wistia_support' ) );
	} // End __construct()

	/**
	 * Add a slider to be kept track of on the current page.
	 * @since  1.0.0
	 * @param  array $slides 	An array of items to act as slides.
	 * @param  array $settings   Arguments pertaining to this specific slider.
	 * @param  array $args  		Optional arguments to be passed to the slider.
	 */
	public function add ( $slides, $settings, $args = array() ) {
		if ( isset( $settings['id'] ) && ! in_array( $settings['id'], array_keys( (array)$this->sliders ) ) ) {
			$this->sliders[(string)$settings['id']] = array( 'slides' => $slides, 'args' => $settings, 'extra' => $args );
		}
	} // End add()

	/**
	 * Get the slides pertaining to a specified slider.
	 * @since  1.0.0
	 * @param  int $id   The ID of the slider in question.
	 * @param  array  $args Optional arguments pertaining to this slider.
	 * @return array       An array of slides pertaining to the specified slider.
	 */
	public function get_slides ( $type, $args = array(), $settings = array() ) {
		$slides = array();
		$supported_types = WooSlider_Utils::get_slider_types();

		if ( in_array( $type, array_keys( $supported_types ) ) ) {
			if ( method_exists( $this, 'slideshow_type_' . esc_attr( $type ) ) ) {
				$slides = call_user_func( array( $this, 'slideshow_type_' . esc_attr( $type ) ), $args, $settings );
			} else {
				if ( isset( $supported_types[$type]['callback'] ) && $supported_types[$type]['callback'] != 'method' ) {
					if ( is_callable( $supported_types[$type]['callback'] ) ) {
						$slides = call_user_func( $supported_types[$type]['callback'], $args, $settings );
					}
				}
			}
		}

		return (array) apply_filters( 'wooslider_get_slides', $slides, $type, $args, $settings );
	} // End get_slides()

	/**
	 * Render the slides into appropriate HTML.
	 * @since  1.0.0
	 * @param  array $slides 	The slides to render.
	 * @return string         	The rendered HTML.
	 */
	public function render ( $slides ) {
		$html = '';

		if ( ! is_array( $slides ) ) $slides = (array)$slides;

		if ( is_array( $slides ) && count( $slides ) ) {
			$slide_count = 1;
			foreach ( $slides as $k => $v ) {

				if ( isset( $v['content'] ) ) {
					$atts = '';
					if ( isset( $v['attributes'] ) && is_array( $v['attributes'] ) && ( count( $v['attributes'] ) > 0 ) ) {
						foreach ( $v['attributes'] as $i => $j ) {
							$atts .= ' ' . esc_attr( strtolower( $i ) ) . '="' . esc_attr( $j ) . '"';
						}
					}
					if($slide_count > 2 ) {
						$atts .=' data-wooslidercontent="' . esc_attr($v['content']) . '"';
						$html .= '<li class="slide"' . $atts . '></li>' . "\n";

					} else {
						$html .= '<li class="slide"' . $atts . '>' . "\n" . $v['content'] . '</li>' . "\n";
					}
				}
				$slide_count++;
			}
		}

		return $html;
	} // End render()

	/**
	 * Render the carousel into appropriate HTML.
	 * @since  1.0.0
	 * @param  array $slides 	The slides to render.
	 * @return string         	The rendered HTML.
	 */
	public function render_carousel ( $slides ) {
		$html = '';

		if ( ! is_array( $slides ) ) $slides = (array)$slides;

		if ( is_array( $slides ) && count( $slides ) ) {
			foreach ( $slides as $k => $v ) {
				$atts = '';
				if ( isset( $v['attributes'] ) && is_array( $v['attributes'] ) && ( count( $v['attributes'] ) > 0 ) ) {
					foreach ( $v['attributes'] as $i => $j ) {
						$atts .=  esc_attr( $j );
					}
					$html .= '<li class="slide">' . "\n". '<img src=' . $atts . '></li>' . "\n";
				}
			}
		}

		return $html;
	} // End render()

	/**
	 * Get the slides for the "attachments" slideshow type.
	 * @since  1.0.0
	 * @param  array $args Array of arguments to determine which slides to return.
	 * @return array       An array of slides to render for the slideshow.
	 */
	private function slideshow_type_attachments ( $args = array(), $settings = array() ) {
		global $post;
		$slides = array();

		$defaults = array(
						'limit' => '5',
						'id' => $post->ID,
						'size' => 'large',
						'thumbnails' => '',
						'orderby' => 'menu_order',
						'order' => 'ASC'
						);

		$args = wp_parse_args( $args, $defaults );

		$query_args = array( 'post_type' => 'attachment', 'post_mime_type' => 'image', 'post_parent' => intval( $args['id'] ), 'numberposts' => intval( $args['limit'] ), 'orderby' => sanitize_key( $args['orderby'] ), 'order' => sanitize_key( $args['order'] ) );

		if( $settings['randomize'] == true ) {
			$query_args['orderby'] = 'rand';
		}

		$attachments = get_posts( $query_args );

		if ( ! is_wp_error( $attachments ) && ( count( $attachments ) > 0 ) ) {
			foreach ( $attachments as $k => $v ) {
				$data = array( 'content' => wp_get_attachment_image( $v->ID, esc_attr( $args['size'] ) ) );
				if ( 'true' == $args['thumbnails'] || 1 == $args['thumbnails'] || 2 == $args['thumbnails'] || 'carousel' == $args['thumbnails'] || 'thumbnails' == $args['thumbnails'] ) {
					$thumb_url = wp_get_attachment_thumb_url( $v->ID );
					if ( ! is_bool( $thumb_url ) ) {
						$data['attributes'] = array( 'data-thumb' => $thumb_url );
					} else {
						$data['attributes'] = array( 'data-thumb' => esc_url( WooSlider_Utils::get_placeholder_image() ) );
					}
				}
				$slides[] = $data;
			}
		}

		return $slides;
	} // End slideshow_type_attachments()

	/**
	 * Get the slides for the "posts" slideshow type.
	 * @since  1.0.0
	 * @param  array $args Array of arguments to determine which slides to return.
	 * @return array       An array of slides to render for the slideshow.
	 */
	private function slideshow_type_posts ( $args = array(), $settings = array() ) {
		global $post;
		$slides = array();

		$defaults = array(
						'limit' => '5',
						'category' => '',
						'tag' => '',
						'layout' => 'text-left',
						'size' => 'large',
						'link_title' => '',
						'overlay' => 'none', // none, full or natural
						'display_excerpt' => 'true'
						);

		$args = wp_parse_args( $args, $defaults );

		// Determine and validate the layout type.
		$supported_layouts = WooSlider_Utils::get_posts_layout_types();
		if ( ! in_array( $args['layout'], array_keys( $supported_layouts ) ) ) { $args['layout'] = $defaults['layout']; }

		// Determine and validate the overlay setting.
		if ( ! in_array( $args['overlay'], array( 'none', 'full', 'natural' ) ) ) { $args['overlay'] = $defaults['overlay']; }

		$query_args = array( 'post_type' => 'post', 'numberposts' => intval( $args['limit'] ) );

		if ( $args['category'] != '' ) {
			$query_args['category_name'] = esc_attr( $args['category'] );
		}

		if ( $args['tag'] != '' ) {
			$query_args['tag'] = esc_attr( str_replace( ',', '+', $args['tag'] ) );
		}

		if( $settings['randomize'] == true ) {
			$query_args['orderby'] = 'rand';
		}

		$posts = get_posts( $query_args );

		if ( ! is_wp_error( $posts ) && ( count( $posts ) > 0 ) ) {
			// Setup the CSS class.
			$class = 'layout-' . esc_attr( $args['layout'] ) . ' overlay-' . esc_attr( $args['overlay'] );

			foreach ( $posts as $k => $post ) {
				setup_postdata( $post );
				$image = get_the_post_thumbnail( get_the_ID(), $args['size'] );

				// Allow plugins/themes to filter here.
				$excerpt = '';
				if ( ( $args['display_excerpt'] == 'true' || $args['display_excerpt'] == 1 ) ) { $excerpt = wpautop( get_the_excerpt() ); }

				$title = get_the_title( get_the_ID() );
				if ( $args['link_title'] == 'true' || $args['link_title'] == 1 ) {
					$title = '<a href="' . get_permalink( $post ) . '">' . $title . '</a>';
					$image = '<a href="' . get_permalink( $post ) . '">' . $image . '</a>';
				}
				$content = $image . '<div class="slide-excerpt"><h2 class="slide-title">' . $title . '</h2>' . $excerpt . '</div>';
				if ( $args['layout'] == 'text-top' ) {
					$content = '<div class="slide-excerpt"><h2 class="slide-title">' . $title . '</h2>' . $excerpt . '</div>' . $image;
				}

				$layed_out_content = apply_filters( 'wooslider_posts_layout_html', $content, $args, $post );

				$content = '<div class="' . esc_attr( $class ) . '">' . $layed_out_content . '</div>';
				$data = array( 'content' => $content );

				if ( 'true' == $args['thumbnails'] || 1 == $args['thumbnails'] || 2 == $args['thumbnails'] || 'carousel' == $args['thumbnails'] || 'thumbnails' == $args['thumbnails']) {
				//if ( isset( $args['thumbnails'] ) && ( 'true' == $args['thumbnails'] || 1 == $args['thumbnails'] ) ) {
					$thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'medium' );
					if ( ! is_bool( $thumb_url ) && isset( $thumb_url[0] ) ) {
						$data['attributes'] = array( 'data-thumb' => esc_url( $thumb_url[0] ) );
					} else {
						$data['attributes'] = array( 'data-thumb' => esc_url( WooSlider_Utils::get_placeholder_image() ) );
					}
				}

				$slides[] = $data;
			}
			wp_reset_postdata();
		}

		return $slides;
	} // End slideshow_type_posts()

	/**
	 * Get the slides for the "slides" slideshow type.
	 * @since  1.0.0
	 * @param  array $args Array of arguments to determine which slides to return.
	 * @return array       An array of slides to render for the slideshow.
	 */
	private function slideshow_type_slides ( $args = array(), $settings = array() ) {
		global $post;
		$slides = array();

		$has_video = array(
						'youtube' => false,
						'vimeo' => false,
						'wistia' => false
						);

		$defaults = array(
						'limit' => '5',
						'slide_page' => '',
						'carousel' => '',
						'thumbnails' => '',
						'layout' => 'text-left',
						'imageslide' => 'false',
						//'size' => 'large',
						'display_title' => '',
						'overlay' => 'none', // none, full or natural
						'display_content' => 'true',
						'order' => 'DESC',
						'order_by' => 'date'
						);

		$args = wp_parse_args( $args, $defaults );

		$query_args = array( 'post_type' => 'slide', 'numberposts' => intval( $args['limit']), 'orderby' => $args['order_by'], 'order' => $args['order']  );

		if( true == $settings['randomize'] ) {
			$query_args['orderby'] = 'rand';
		}

		if ( '' != $args['slide_page'] ) {
			$cats_split = explode( ',', $args['slide_page'] );
			$query_args['tax_query'] = array( 
				array(
					'taxonomy' => 'slide-page',
					'field' => 'slug',
					'terms' => $cats_split
				)
			);
		}

		$posts = get_posts( $query_args );

		if ( ! is_wp_error( $posts ) && ( count( $posts ) > 0 ) ) {
			foreach ( $posts as $k => $post ) {

				setup_postdata( $post );

				$post_meta = get_post_custom( get_the_ID() );
			    foreach( $post_meta as $meta=>$value ) {
			        if( '_oembed' == substr( trim( $meta ) , 0 , 7 ) ) {

			        	if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $value[0], $match)) {
				   			$has_video['youtube'] = true;
				   			add_filter( 'wooslider_callback_start_' . $settings['id'], array( 'WooSlider_Frontend', 'wooslider_youtube_start' ) );
				   			add_filter( 'wooslider_callback_before_' . $settings['id'], array( 'WooSlider_Frontend', 'wooslider_youtube_before' ) );
				   		}
				   		if ( preg_match( '#(?:https?:\/\/(?:[\w]+\.)*vimeo\.com(?:[\/\w]*\/videos?)?\/([0-9]+)[^\s]*)#i', $value[0], $match ) ){
			         		$has_video['vimeo'] = true;
			         		add_filter( 'wooslider_callback_start_' . $settings['id'], array( 'WooSlider_Frontend', 'wooslider_vimeo_start' ) );
			         		add_filter( 'wooslider_callback_before_' . $settings['id'], array( 'WooSlider_Frontend', 'wooslider_vimeo_before' ) );
			         	}
			        	if ( preg_match( '%(?:https?:\/\/(?:.+)?(?:wistia.com|wi.st|wistia.net)\/(?:medias|embed)?\/)(.*)%i', $value[0], $match ) ){
			         		$has_video['wistia'] = true;
			         		add_filter( 'wooslider_callback_before_' . $settings['id'], array( 'WooSlider_Frontend', 'wooslider_wistia_before' ) );
			         	}
			        }
			    }

			    if ( isset( $args['carousel'] ) && 'true' == $args['carousel'] ) {
			    	$image = get_the_post_thumbnail( get_the_ID() );
					if ( 'true' == $args['link_slide'] || 1 == $args['link_slide'] ) {
						$wooslider_url = get_post_meta( get_the_ID(), '_wooslider_url', true );
						$image = '<a href="' . esc_url( $wooslider_url ) . '">' . $image . '</a>';
					}

					$data = array( 'content' => '<div class="slide-content">' . "\n" . apply_filters( 'wooslider_slide_carousel_slides', $image, $args ) . "\n" . '</div>' . "\n" );

			    } else if ( isset( $args['imageslide'] ) && 'true' == $args['imageslide'] || 1 == $args['imageslide'] ) {

					// Determine and validate the layout type.
					$supported_layouts = WooSlider_Utils::get_posts_layout_types();
					if ( ! in_array( $args['layout'], array_keys( $supported_layouts ) ) ) { $args['layout'] = $defaults['layout']; }

					// Determine and validate the overlay setting.
					if ( ! in_array( $args['overlay'], array( 'none', 'full', 'natural' ) ) ) { $args['overlay'] = $defaults['overlay']; }
					if ( ( $args['display_content'] == 'true' || $args['display_content'] == 1 ) || ( $args['display_title'] == 'true' || $args['display_title'] == 1 ) ) {

						$class = 'layout-' . esc_attr( $args['layout'] ) . ' overlay-' . esc_attr( $args['overlay'] );
					}

					$image = get_the_post_thumbnail( get_the_ID() );
					if ( 'true' == $args['link_slide'] || 1 == $args['link_slide'] ) {
						$wooslider_url = get_post_meta( get_the_ID(), '_wooslider_url', true );
						$image = '<a href="' . esc_url( $wooslider_url ) . '">' . $image . '</a>';
					}

					$title = '';
					if ( 'true' == $args['display_title'] || 1 == $args['display_title'] ) {
						$title = get_the_title( get_the_ID() );
						if ( 'true' == $args['link_slide'] || 1 == $args['link_slide'] ) {
							$title = '<a href="' . esc_url( $wooslider_url ) . '">' . $title . '</a>';
						}
						$title = '<h2 class="slide-title">' . $title . '</h2>';
					}

					$content = '';
					if ( ( 'true' == $args['display_content'] || 1 == $args['display_content'] ) ) {
						$content = wpautop( get_the_excerpt() );

					}

					if ( ( 'true' == $args['display_content'] || 1 == $args['display_content'] ) || ( 'true' == $args['display_title'] || 1 == $args['display_title'] ) ) {
						$content = '<div class="slide-excerpt">' . $title . $content . '</div>';
						if ( $args['layout'] == 'text-top' ) {
							$content = $content . $image;
						} else {
							$content = $image . $content;
						}
					} else {
						$content = $image;
					}

					$layed_out_content = apply_filters( 'wooslider_slides_layout_html', $content, $args, $post );

					$content = '<div class="slide-content ' . esc_attr( $class ) . '">' . $layed_out_content . '</div>';

					$data = array( 'content' => $content );

				} else {
					$content = get_the_content();

				    $data = array( 'content' => '<div class="slide-content">' . "\n" . apply_filters( 'wooslider_slide_content_slides', $content, $args ) . "\n" . '</div>' . "\n" );
				}

				if ( 'true' == $args['thumbnails'] || 1 == $args['thumbnails'] || 2 == $args['thumbnails'] || 'carousel' == $args['thumbnails'] || 'thumbnails' == $args['thumbnails']) {
					$thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'medium' );
					if ( ! is_bool( $thumb_url ) && isset( $thumb_url[0] ) ) {
						$data['attributes'] = array( 'data-thumb' => esc_url( $thumb_url[0] ) );
					} else {
						$data['attributes'] = array( 'data-thumb' => esc_url( WooSlider_Utils::get_placeholder_image() ) );
					}
				}
				$data['video'] = $has_video;

				$slides[] = $data;
			}
			wp_reset_postdata();
		}

		return $slides;
	} // End slideshow_type_slides()

	/**
	 * Add default filters to the content of the "slides" slideshow type's slides.
	 * @access  public
	 * @since   1.0.2
	 * @return  void
	 */
	public function apply_default_filters_slides () {
		add_filter( 'wooslider_slide_content_slides', 'wptexturize', 1 );
		add_filter( 'wooslider_slide_content_slides', 'convert_smilies', 1 );
		add_filter( 'wooslider_slide_content_slides', 'convert_chars', 1 );
		add_filter( 'wooslider_slide_content_slides', 'wpautop', 1 );
		add_filter( 'wooslider_slide_content_slides', 'shortcode_unautop', 1 );
		add_filter( 'wooslider_slide_content_slides', 'prepend_attachment', 1 );

		// Take note of the priority settings for the following filters.
		add_filter( 'wooslider_slide_content_slides', 'wp_kses_post', 2 );

		if ( get_option( 'embed_autourls', true ) ) {
			global $wp_embed;
			add_filter( 'wooslider_slide_content_slides', array( $wp_embed, 'run_shortcode' ), 3 );
		}

		add_filter( 'wooslider_slide_content_slides', 'do_shortcode', 4 );

	} // End apply_default_filters_slides()

	/**
	 * Add Wistia as an oembed provider.
	 * @access  public
	 * @since   2.0.0
	 * @return  void
	 */
	public function add_wistia_support () {
		wp_oembed_add_provider( '/https?:\/\/(.+)?(wistia.com|wi.st)\/(medias|embed)\/.*/', 'http://fast.wistia.com/oembed', true );
	} // End add_wistia_support()
} // End Class
?>