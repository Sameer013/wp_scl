<?php
/**
 * Partners
 *
 * This is the template for the content of Partners
 *
 * @package Theme Palace
 * @subpackage Academic Pro
 * @since 1.0
 */
if ( ! function_exists( 'academic_pro_add_partner' ) ) :
  /**
   * Add Partners
   *
   *@since Academic Pro 1.0
   */
  function academic_pro_add_partner() {

    // Check if partner is enabled on frontpage
    $partner_enable = apply_filters( 'academic_pro_section_status', true, 'partner_enable' );
    if ( true !== $partner_enable ) {
      return false;
    }
    // Get Partners details
    $section_details = array();
    $section_details = apply_filters( 'academic_pro_filter_partner_details', $section_details );

    if ( empty( $section_details ) ) {
      return;
    }

    // Render Partners now.
    academic_pro_render_partner( $section_details );
  }
endif;
add_action( 'academic_pro_footer_content', 'academic_pro_add_partner', 10 );


if ( ! function_exists( 'academic_pro_get_partner_details' ) ) :
  /**
   * Partners details.
   *
   * @since Academic Pro 1.0
   * @param array $input Partners details.
   */
  function academic_pro_get_partner_details( $input ) {
    $options = academic_pro_get_theme_options();
    // Partners Type
    $partner_type    = $options['partner_type'];
    $no_of_partner   = $options['no_of_partner'];
    $content = array();
    switch ( $partner_type ) {
        case 'demo':
            for ( $i = 1; $i <= 7; $i++ ) { 
                $content[$i]['img_array'][0]   = get_template_directory_uri() . '/assets/uploads/tp-logo.jpg';
                $content[$i]['url']         = '#';
                $content[$i]['alt']         = 'academic-pro';
            }
        break;

        case 'custom':
            for ( $i = 1; $i <= $no_of_partner; $i++ ) { 
                $content[$i]['img_array'][0]   = ! empty( $options['partner_image_'. $i] ) ? $options['partner_image_'. $i] : '';
                $content[$i]['url']         = ! empty( $options['partner_link_'. $i] ) ? $options['partner_link_'. $i] : '';
                $content[$i]['alt']         = ! empty( $options['partner_alt_'. $i] ) ? $options['partner_alt_'. $i] : '';
            }
        break;

        case 'page':
            $ids = array();

            for ( $i = 1; $i <= $no_of_partner; $i++ ) {
                $id = null;
                if ( isset( $options[ 'partner_page_'.$i ] ) ) {
                    $id = $options[ 'partner_page_'.$i ];
                }
                if ( ! empty( $id ) ) {
                    $ids[] = absint( $id );
                }
            }

            // Bail if no valid pages are selected.
            if ( empty( $ids ) ) {
                return $input;
            }
            $args = array(
                'no_found_rows'  => true,
                'post_type'      => 'page',
                'post__in'       => $ids,
                'orderby'        => 'post__in',
                'posts_per_page' => $no_of_partner,
            );
            $posts = get_posts( $args );
            $i = 1;
            foreach ( $posts as $post ) {
                $page_id = $post->ID;
                if ( has_post_thumbnail( $page_id ) ) {
                    $img_array = wp_get_attachment_image_src( get_post_thumbnail_id( $page_id ), 'large' );
                } else {
                    $img_array[0] =  get_template_directory_uri().'/assets/uploads/no-featured-image-450x338.png';
                }
                $content[$i]['url']       = get_the_permalink( $page_id );
                $content[$i]['alt']       = get_the_title( $page_id );

                if ( isset( $img_array ) ) {
                  $content[$i]['img_array'] = $img_array;
                }
                $i++;
            }
        break;

        default:
        break;
    }

    if ( ! empty( $content ) ) {
      $input = $content;
    }
    return $input;
  }
endif;
// Partners content details.
add_filter( 'academic_pro_filter_partner_details', 'academic_pro_get_partner_details' );


if ( ! function_exists( 'academic_pro_render_partner' ) ) :
  /**
   * Start Partners section 
   *
   * @return string Partners content
   * @since Academic Pro 1.0
   *
   */
   function academic_pro_render_partner( $content_details = array() ) {
        $options = academic_pro_get_theme_options();  
        $partner_title      = ! empty( $options['partner_title'] ) ? $options['partner_title'] : '';
        $partner_sub_title  = ! empty( $options['partner_sub_title'] ) ? $options['partner_sub_title'] : '';
        $partner_layout     = ! empty( $options['partner_layout'] ) ? $options['partner_layout'] : 6;
        $partner_dragable   = ( $options['partner_dragable'] == true ) ? 'true' : 'false';
        $partner_autoplay   = ( $options['partner_autoplay'] == true ) ? 'true' : 'false';
        ?>  
        <section id="our-partners" class="page-section slider os-animation">
            <div class="container">
                <header class="entry-header">
                        <?php if ( ! empty( $partner_title ) ) : ?>
                            <h2 class="entry-title"><?php echo esc_html( $partner_title ); ?></h2>  
                        <?php endif; 
                        if ( ! empty( $partner_sub_title ) ) : ?>
                            <h6 class="entry-subtitle"><?php echo esc_html( $partner_sub_title ); ?></h6>
                        <?php endif; ?>
                </header><!-- end .entry-header -->

                <div class="entry-content regular" data-slick='{"slidesToShow": <?php echo absint( $partner_layout ); ?>, "slidesToScroll": 1, "infinite": true, "speed": 800, "dots": false, "arrows":true, "draggable": <?php echo esc_attr( $partner_dragable ); ?>, "autoplay": <?php echo esc_attr( $partner_autoplay ); ?> }'>
                    <?php foreach( $content_details as $content ) : 
                        if( ! empty( $content['img_array'][0] ) ) : ?>
                            <div class="slider-item">
                                <a href="<?php echo esc_url( $content['url'] ); ?>"><img src="<?php echo esc_url( $content['img_array'][0] ); ?>" alt="<?php echo esc_attr( $content['alt'] ); ?>"></a>
                            </div><!-- end .slider-item -->
                        <?php endif;
                    endforeach; ?>
                </div><!-- end .entry-content -->
            </div><!-- end .container -->
        </section><!-- end #our-partners -->
    <?php 
    }
endif;