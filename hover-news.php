<?php
/*
 * Plugin Name: Hover News
 * Author: Kyle & Irving
 * Author URI: http://kyle-irving.co.uk/
 * Version: 1.0.0
 * Description: Display a list of posts in grid with custom styles and transitions
 * Class Name: KIHoverNews
 * PageLines: true
 * Section: true
 * Filter: component, format, dual-width
 * Loading: active
 */
if (!class_exists('KIHoverNews'))
    return;

include( plugin_dir_path(__FILE__) . '/assets/BFI_Thumb.php');

class KIHoverNews extends PageLinesSection {

   function __construct($settings = array()) {
        parent::__construct($settings);
        // add_action('init', array(&$this, 'init'));
    }

    // function init() {
    //     load_plugin_textdomain('hover_news', false, dirname(plugin_basename(__FILE__)) . '/languages');
    // }

    function section_scripts() {
        wp_enqueue_style('bootstrap-grid', $this->base_url . '/assets/bootstrap/grid.css', array(), NULL);

        // wp_enqueue_script( 'isotope', PL_JS . '/utils.isotope.min.js', array('jquery'), pl_get_cache_key(), true);
        wp_deregister_script('masonry');
        wp_register_script('masonry', $this->base_url . '/assets/masonry.pkgd.min.js', array('jquery'), '3.1.5', 1);
        wp_enqueue_script('masonry');
        
        // wp_enqueue_script( 'masonry', $this->base_url . '/assets/masonry.pkgd.min.js', array('jquery'), '3.1.5', 1);
        wp_enqueue_script('infinitescroll', $this->base_url . '/assets/script.infinitescroll.js', array( 'jquery'), PL_CORE_VERSION, true);
        
        wp_enqueue_style('hover-news', $this->base_url . '/css/hover-news.css', array(), NULL);
        wp_enqueue_script('hover-news', $this->base_url . '/js/hover-news.js', array('jquery'), NULL, true);
    }
   
    function section_head() {        
        $max_height = (int) $this->opt('max_height', array('default' => ''));
        $min_height = (int) $this->opt('min_height', array('default' => ''));
        $text_color = pl_hashify($this->opt('text_color', array('default' => '#ffffff')));
        $background_color = $this->hex2rgb($this->opt('background_color', array('default' => '#5bc0de')));
        $background_color_opacity = (int) $this->opt('background_color_opacity', array('default' => 10));
        
        $rgba = 'rgba('.$background_color['r'].', '.$background_color['g'].', '.$background_color['b'].', '.($background_color_opacity / 10).')';        

        $button_color = pl_hashify($this->opt('button_color', array('default' => '#ffffff')));
        $button_background_color = pl_hashify($this->opt('button_background_color', array('default' => '#428bca')));
        $extra_color = pl_hashify($this->opt('extra_color', array('default' => '#ffffff')));
        $id = sprintf('#hovernews-%s', $this->get_the_id());
        ?>
        <style type="text/css">
            <?php echo $id; ?> .hovernews-details {
                color: <?php echo $text_color; ?> !important;
            }
            <?php echo $id; ?> .hovernews-overlay {
                background-color: <?php echo $rgba; ?> !important;
            }
            <?php echo $id; ?> a.hovernews-more {
                background-color: <?php echo $button_background_color; ?> !important;
                color: <?php echo $button_color; ?> !important;
            }
            <?php echo $id; ?> .hovernews-info {
                color: <?php echo $extra_color; ?> !important;
            }
            <?php if ($max_height > 0): ?>
                <?php echo $id; ?> .hovernews-backdrop, .hovernews-backdrop img {
                    max-height: <?php echo $max_height; ?>px !important;
                }            
            <?php endif; ?>
            <?php if ($min_height > 0): ?>
                <?php echo $id; ?> .hovernews-backdrop, .hovernews-backdrop img {
                    min-height: <?php echo $min_height; ?>px !important;
                }            
            <?php endif; ?>
        </style>
        <?php
    }

    function section_opts() {

        $opts = array(
            array(
                'title' => __('Styling', 'hover_news'),
                'type' => 'multi',
                'col'   => 1,
                'opts' => array(
                    array(
                        'key' => 'hover_news_format',
                        'type' => 'select',
                        'opts' => array(
                            'grid' => array('name' => __('Grid', 'hover_news')),
                            'row_closed' => array('name' => __('Row Closed', 'hover_news')),
                        ),
                        'default' => 'grid',
                        'label' => __('Select Format', 'hover_news'),
                    ),
                    array(
                        'key'           => 'hover_news_thumbsize',
                        'type'          => 'select',
                        'default'       =>  'full',
                        'opts'          => $this->get_image_sizes(),
                        'label'         => __( 'Select attachment image source', 'hover_news' ),
                        'title'         => __( 'Attachment source', 'hover_news' ),
                    ),
                    array(
                        'key' => 'col',
                        'type' => 'select',
                        'label' => __('Number of Columns for Each Box (12 Col Grid)', 'hover_news'),
                        'default' => '4',
                        'opts' => array(
                            '3' => array('name' => '3'),
                            '4' => array('name' => '4'),
                            '6' => array('name' => '6'),
                        )
                    ),
                    array(
                        'key' => 'max_height',
                        'type' => 'text_small',
                        'label' => __('Max Height', 'hover_news'),
                        'help'      => __( 'The max height of Hover News in pixels. Default is <strong>500px</strong>.', 'hover_news' )
                    ),
                    array(
                        'key' => 'min_height',
                        'type' => 'text_small',
                        'label' => __('Min Height', 'hover_news'),
                        'help'      => __( 'The min height of Hover News in pixels. Default is <strong>190px</strong>.', 'hover_news' )
                    ),                   
                    array(
                        'key' => 'text_color',
                        'type' => 'color',
                        'label' => __('Text Color', 'hover_news'),
                        'default' => '#ffffff'
                    ),
                    array(
                        'key' => 'background_color',
                        'type' => 'color',
                        'label' => __('Background Color', 'hover_news'),
                        'default' => '#5bc0de'
                    ),
                    array(
                        'key' => 'background_color_opacity',
                        'type' => 'select',
                        'label' => __('Background Color Opacity', 'hover_news'),
                        'default' => 10,
                        'opts' => array(
                            0 => array('name' => __('0', 'hover_news')),
                            1 => array('name' => __('0.1', 'hover_news')),
                            2 => array('name' => __('0.2', 'hover_news')),
                            3 => array('name' => __('0.3', 'hover_news')),
                            4 => array('name' => __('0.4', 'hover_news')),
                            5 => array('name' => __('0.5', 'hover_news')),
                            6 => array('name' => __('0.6', 'hover_news')),
                            7 => array('name' => __('0.7', 'hover_news')),
                            8 => array('name' => __('0.8', 'hover_news')),
                            9 => array('name' => __('0.9', 'hover_news')),
                            10 => array('name' => __('1', 'hover_news')),
                        )
                    ),   
                    array(
                        'key' => 'button_text',
                        'type' => 'text',
                        'label' => __('Button Text', 'hover_news'),
                        'default' => 'Read more',
                    ),
                    array(
                        'key' => 'button_color',
                        'type' => 'color',
                        'label' => __('Button Text Color', 'hover_news'),
                        'default' => '#ffffff'
                    ),
                    array(
                        'key' => 'button_background_color',
                        'type' => 'color',
                        'label' => __('Button Background Color', 'hover_news'),
                        'default' => '#428bca'
                    ),
                    array(
                        'key' => 'extra_color',
                        'type' => 'color',
                        'label' => __('Extra Color (author/date)', 'hover_news'),
                        'default' => '#ffffff'
                    ),                     
                )
            ),
            array(
                'title' => __('Query Arguments', 'hover_news'),
                'type' => 'multi',
                'col'   => 2,
                'opts' => array(
                    array(
                        'key' => 'category',
                        'taxonomy_id'   => 'category',
                        'type'          => 'select_taxonomy',
                        'label' => __('Select Categories', 'hover_news'),
                        'default' => 0, 
                        'help' => "You can select to use only posts from a specific category, leave blank to use all posts. Default is to show all posts."                    
                    ),
                    array(
                        'key' => 'number_of_articles',
                        'type' => 'count_select',
                        'label' => __('Number of articles (total)', 'hover_news'),
                        'default' => 20,
                        'count_start' => 1,
                        'count_number' => 100
                    ),                    
                    array(
                        'key' => 'orderby',
                        'type' => 'select_same',
                        'label' => __('Order by', 'hover_news'),
                        'default' => 'date',
                        'opts' => array(
                            'ID',
                            'author',
                            'title',
                            'date',
                            'modified',
                            'rand',
                            'comment_count'
                        )
                    ),
                )
            ),
            array(
                'title' => __('Category Styling', 'hover_news'),
                'type' => 'multi',
                'col'   => 2,
                'opts' => array(
                    array(
                        'key' => 'category_position',
                        'type' => 'select',
                        'label' => __('Select Category Positon', 'hover_news'),
                        'default' => 'below',
                        'opts' => array(
                            'above' => array('name' => __('Above title', 'hover_news')),
                            'below' => array('name' => __('Below title', 'hover_news')),
                        )                        
                    ),
                    array(
                        'key' => 'category_display',
                        'type' => 'select',
                        'label' => __('Select Category Display', 'hover_news'),
                        'default' => 'text',
                        'opts' => array(
                            'text' => array('name' => __('Display as text', 'hover_news')),
                            'label' => array('name' => __('Display as label', 'hover_news')),
                        ) 
                    ),
                    array(
                        'key' => 'category_style_array',
                        'type' => 'accordion',
                        'help' => "Set the color of all categories or for each category. If using for each category add a new category style for each category",
                        'opts_cnt'  => 1,
                        'post_type' => __('Category Style', 'hover_news'), 
                        'opts' => array(
                            array(
                                'key' => 'category_slug',
                                'type' => 'select',
                                'label' => __('Select Categories', 'hover_news'),
                                'opts' => $this->get_categories_slug()                     
                            ),
                            array(
                                'key' => 'category_text_color',
                                'type' => 'color',
                                'label' => __('Category Text Color', 'hover_news'),
                                'default' => '#ffffff'
                            ),
                            array(
                                'key' => 'category_bg_color',
                                'type' => 'color',
                                'label' => __('Category Background Color', 'hover_news'),
                                'default' => '#428bca'
                            ),
                        ) 
                    ),                    
                )
            ),
            array(
                'title' => __('Extra Options', 'hover_news'),
                'type' => 'multi',
                'col'   => 3,
                'opts' => array(                    
                    array(
                        'key' => 'character_limit_of_excerpt',
                        'type' => 'select_same',
                        'label' => __('Character limit of excerpt', 'hover_news'),
                        'default' => 100,
                        'opts' => range(60, 200, 20)
                    ),                    
                    array(
                        'key' => 'is_hide_author',
                        'type' => 'check',
                        'label' => __('Hide author', 'hover_news')
                    ),
                    array(
                        'key' => 'is_hide_categories',
                        'type' => 'check',
                        'label' => __('Hide categories', 'hover_news')
                    ),
                    array(
                        'key' => 'is_hide_date',
                        'type' => 'check',
                        'label' => __('Hide date', 'hover_news')
                    ),
                    array(
                        'key'           => 'post_loading',
                        'type'          => 'select',
                        'default'       => 'ajax',
                        'opts' => array(
                            'infinite'      => array('name' => __( 'Use Infinite Scrolling', 'hover_news' ) ),
                            'ajax'          => array('name' => __( 'Use Load Posts Link (AJAX)', 'hover_news' ) ),
                        ),
                        'label'     => __( 'Post Loading Method', 'hover_news' ),
                        'title'         => __( 'Post Loading', 'hover_news' ),
                    ),                    
                )
            )
        );
        return $opts;
    }

    function section_template() {

        $category_style_array = $this->opt('category_style_array');
        if (count($category_style_array) == 1) {
            $cat_text_color_all = $category_style_array['item1']['category_text_color'];
            $cat_bg_color_all = $category_style_array['item1']['category_bg_color'];
        }
        $category_style_list = array();
        if($category_style_array) {
            foreach ($category_style_array as $value) {
                if(isset($value['category_slug']) && $value['category_slug'] != '') {
                    $category_style_list[$value['category_slug']]['category_text_color'] = $value['category_text_color'];
                    $category_style_list[$value['category_slug']]['category_bg_color'] = $value['category_bg_color'];
                }             
            }
        }     
        

        $format = $this->opt('hover_news_format', array('default' => 'grid'));     
        $col = (int) $this->opt('col', array('default' => 4));
        $button_text = $this->opt('button_text', array('default' => __('Read more', 'news_views')));

        
        $category = ($this->opt('category' )) ? $this->opt('category') : null;
        $number_of_articles = (int) $this->opt('number_of_articles', array('default' => 20));
        $orderby = $this->opt('orderby', array('default' => 'date'));     

        $character_limit_of_excerpt = (int) $this->opt('character_limit_of_excerpt', array('default' => 100));
        $is_hide_author = $this->opt('is_hide_author', array('default' => false));
        $is_hide_categories = $this->opt('is_hide_categories', array('default' => false));
        $is_hide_date = $this->opt('is_hide_date', array('default' => false));
        $loading = $this->opt('post_loading', array('default' => 'ajax'));

        $category_position = $this->opt('category_position', array('default' => 'below'));
        $category_display = $this->opt('category_display', array('default' => 'text'));

        $size_of_thumbnail = $this->opt('hover_news_thumbsize', array('default' => 'full'));

        $page = (isset($_GET['hovernews_page']) && $_GET['hovernews_page'] != 1) ? $_GET['hovernews_page'] : 1;

        $posts = $this->load_posts($page, $category, $orderby, $number_of_articles);
        $next_posts = $this->load_posts( $page + 1, $category, $orderby, $number_of_articles);

        $boxes = sprintf('<ul id="hovernews-%s" class="hovernews-container row clearfix" data-format="%s" data-loading="%s" data-url="%s" data-id="%s" data-col="%s">', $this->get_the_id(), $format, $loading, $this->base_url, $this->get_the_id(), $col);

        if ($posts->have_posts()):
            while ($posts->have_posts()):
                $posts->the_post();
                $post_id = get_the_ID();
                $post_url = get_permalink();
                $post_title = get_the_title();
                $excerpt = get_the_content();
                $categories = get_the_category($post_id);
                
                if ($format == 'grid') {
                    $boxes .= sprintf('<li class="col-md-%s grid-format hovernews-item">', $col);
                } else {
                    $boxes .= sprintf('<li class="col-md-%s hovernews-item">', $col);
                }                
                    $boxes .= '<div class="hovernews-overlay">';
                        $boxes .= '<div class="hovernews-details">';
                            if (!$is_hide_categories) {                                    
                                if (!empty($categories)) {
                                    $cats = sprintf('<div class="hovernews-cat display-%s">', $category_display);
                                    $count = 0;
                                    if(isset($cat_text_color_all)) {
                                        $catcolor = 'color: #'.$cat_text_color_all.';';    
                                    } else {
                                        $catcolor = 'color: #ffffff;';              
                                    }
                                    
                                    if($category_display == 'label') {
                                        if(isset($cat_bg_color_all)) {
                                            $catbgcolor = 'background-color: #'.$cat_bg_color_all.';';
                                        } else {
                                            $catbgcolor = 'background-color: #428bca;';    
                                        }
                                        
                                    } else {
                                        $catbgcolor = '';
                                    }                                  
                                    foreach ($categories as $category) {  
                                        if (isset($category_style_list[$category->slug])) {                                       
                                            $catcolor = sprintf('color: #%s;', $category_style_list[$category->slug]['category_text_color']);
                                            if($category_display == 'label') {
                                                $catbgcolor = sprintf('background-color: #%s;', $category_style_list[$category->slug]['category_bg_color']);    
                                            }                                            
                                        }
                                        $cats .= sprintf('<a href="%s" class="cat-%s" style="%s%s">%s</a>', get_category_link($category), $category->term_id, $catcolor, $catbgcolor, $category->name);
                                        $count++;                                        
                                    }
                                    $cats .= '</div>';    
                                }                                      
                            }

                            if($category_position == 'below') {
                                $boxes .= '<h4>'.$post_title.'</h4>';
                                $boxes .= $cats;    
                            } else {
                                $boxes .= $cats;
                                $boxes .= '<h4>'.$post_title.'</h4>';
                            }
                            
                            $boxes .= '<div class="hovernews-toggle" style="display: none;">';
                                $boxes .= sprintf('<p class="hovernews-excerpt">%s</p>', $this->get_summary($excerpt, $character_limit_of_excerpt));
                                $boxes .= sprintf('<p class="hovernews-info">%s %s</p>', $is_hide_date ? '' : '&boxh; '. get_the_date(), $is_hide_author ? '' : __('by', 'news_views') . ' ' . get_the_author());
                                $boxes .= '<a href="'.$post_url.'" target="_self" class="hovernews-more">'.$button_text.'</a>';
                            $boxes .= '</div>';
                        $boxes .= '</div>';    
                    $boxes .= '</div>';
                    
                    $boxes .= '<div class="hovernews-backdrop">';
                        if ( has_post_thumbnail() ) {
                            if ($format == 'grid') {
                                $image_url = wp_get_attachment_url( get_post_thumbnail_id($post_id) ); 
                                $boxes .= sprintf('<img src="%s" />', $this->get_thumbnail($image_url, $col)); 
                            } else {
                                $boxes .= get_the_post_thumbnail($post_id, $size_of_thumbnail);                                
                            }                            
                                                       
                        } else {
                            if ($format == 'grid') {
                                $boxes .= sprintf('<img src="%s" alt="no image added yet." />', $this->get_thumbnail(pl_default_image(), $col)); 
                            } else {
                                $boxes .= sprintf('<img src="%s" alt="no image added yet." />', pl_default_image()); 
                            }                           
                        }
                    $boxes .= '</div>';
                      
                $boxes .= '</li>';
            endwhile;            
        endif;        
        wp_reset_postdata();

        $u = add_query_arg('hovernews_page', $page + 1, pl_current_url());
        $fetchlink = sprintf('<a href="%s" class="">%s</a>', $u, __('Load More Posts', 'hover_news'));        

        
        if( !empty($next_posts->posts) ){

            $class = ( $this->opt('post_loading', $this->oset) == 'infinite' ) ? 'iscroll' : 'fetchpost';

            $display = ($class == 'iscroll') ? 'style="display: block"' : '';

            $next_url = sprintf('<div class="%s fetchlink clearfix" %s>', $class, $display);
            $next_url .= $fetchlink;
            $next_url .= '</div>';
        } else {
            $next_url = '';
        }
            
        $boxes .= '</ul>';
        $boxes .= $next_url;
        echo $boxes;
    }

    function get_summary($excerpt, $lenght = 100) {
        $excerpt = preg_replace(" (\[.*?\])", '', $excerpt);
        $excerpt = strip_shortcodes($excerpt);
        $excerpt = strip_tags($excerpt);
        $excerpt = substr($excerpt, 0, $lenght);
        $excerpt = substr($excerpt, 0, strripos($excerpt, " "));
        $excerpt = trim(preg_replace('/\s+/', ' ', $excerpt));

        return $excerpt;
    }

    function get_image_sizes() {
        global $_wp_additional_image_sizes;

        $sizes = array(
                'thumbnail' => array( 'name' => 'Thumbnail' ),
                'medium'=> array( 'name' => 'Medium' ),
                'large' => array( 'name' => 'Large' ),
                'full'  => array( 'name' => 'Full' )
                );
        if ( is_array( $_wp_additional_image_sizes ) && ! empty( $_wp_additional_image_sizes ) )
            foreach ( $_wp_additional_image_sizes as $size => $data )
                $sizes[] = array( 'name' => $size );

        return $sizes;
    }

    function hex2rgb( $colour ) {
        if ( $colour[0] == '#' ) {
                $colour = substr( $colour, 1 );
        }
        if ( strlen( $colour ) == 6 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
        } elseif ( strlen( $colour ) == 3 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
        } else {
                return false;
        }
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );
        return array( 'r' => $r, 'g' => $g, 'b' => $b );
    }

    function load_posts( $page = 1, $category = null, $orderby = null, $number = null){
        
        $query = array();
        $query['post_type'] = 'post';
        $query['paged'] = $page;

        if( isset($category) && !empty($category) )
            $query['category_name'] = $category;
            
        if( isset($orderby) && !empty($orderby) )
            $query['orderby'] = (string) $orderby;

        // Search page
        if( isset( $_GET['s'] ) && $_GET['s'] != '' )
            $query['s'] = $_GET['s'];
        
        if( isset($number) )
            $query['posts_per_page'] = $number;
        

        $q = new WP_Query($query);

    
        return $q;
    }

    function pl_current_url(){

        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        return substr($url,0,strpos($url, '?'));
    }

    function get_categories() {
        $categories = get_categories();
        $options = array('' => array('name' => '— SELECT —'));
        $options = array('0' => array('name' => '*Show All*'));

        foreach ($categories as $category) {
            $options[$category->term_id] = array('name' => $category->name);
        }
        return $options;
    }

    function get_categories_slug() {
        $categories = get_categories();
        $options = array('' => array('name' => '— SELECT —'));
        $options = array('0' => array('name' => '*Show All*'));

        foreach ($categories as $category) {
            $options[$category->slug] = array('name' => $category->name);
        }
        return $options;
    }

    function get_thumbnail($src, $col) {
        if ($src) {
            $args = array('width' => 0, 'height' => 0, 'crop' => true);
            switch ($col) {
                case 6:
                    $args['width'] = 510;
                    $args['height'] = 510;
                    break;
                case 4:
                    $args['width'] = 350;
                    $args['height'] = 350;
                    break;
                case 3:
                    $args['width'] = 245;
                    $args['height'] = 245;
                    break;
            }
            return bfi_thumb($src, $args);
        } else {
            return '';
        }
    }

}