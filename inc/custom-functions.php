<?php

/*---------------------------
    BODY OPEN FUNCTION
----------------------------*/
if ( !function_exists( 'wp_body_open' ) ) {
    function wp_body_open() {
        do_action( 'wp_body_open' );
    }
}

/*----------------------------
    GET SVG LOGO
------------------------------*/
if ( !function_exists( 'itbin_get_logo_type_tag' ) ) {
    function itbin_get_logo_type_tag( $url='' ){
        if( $url == '' ){
            return '<img src="'.esc_url( $url ).'" alt="'.get_bloginfo( 'name' ).'">';
        }
        try{
            $url_basename = basename( $url ); 
            $svg_ext      = explode( '.',$url_basename )[1];

            if( $svg_ext != 'svg' ){
                return '<img src="'.esc_url( $url ).'" alt="'.get_bloginfo( 'name' ).'">';
            }
            $get_svg_file = wp_remote_get( $url );
            $svg_file     = wp_remote_retrieve_body( $get_svg_file );
            $find_string  = '<svg';
            $position     = strpos( $svg_file, $find_string );
            $new_svg_file = substr( $svg_file, $position );
            return $new_svg_file;
        }catch( \Exception $e ) {
            return '<img src="'.esc_url( $url ).'" alt="'.get_bloginfo( 'name' ).'">';
        }
    }
}


/*----------------------------
    LOGO WITH STICKY
------------------------------*/
if ( !function_exists( 'itbin_logo_with_sticky' ) ){
    function itbin_logo_with_sticky(){
        $default_logo = get_theme_mod( 'custom_logo' );
        $default_logo = wp_get_attachment_image_url( $default_logo, 'full');

        $logo        = itbin_get_option( 'logo' );
        $logo        = isset( $logo['url'] ) ? $logo['url'] : '';

        $sticky_logo = itbin_get_option( 'sticky_logo' );
        $sticky_logo = isset( $sticky_logo['url'] ) ? $sticky_logo['url'] : '';

        if ( '' == $default_logo && isset( $logo ) ) {
            $default_logo = $logo;
        }

        if ( '' == $sticky_logo && itbin_get_option( 'sticky_menu' ) == true ) {
            $sticky_logo = $default_logo;
        }

        /*---------------------------
            OVERWRITE PAGE LOGO
        ----------------------------*/
        $page_meta_array  = itbin_metabox_value('_itbin_page_metabox');
        $page_logo_switch = isset( $page_meta_array['overwrite_page_logo'] ) ? $page_meta_array['overwrite_page_logo'] : false;

        if( is_page() && '1' == $page_logo_switch ){            
            $page_default_logo = $page_meta_array['logo'];
            $page_sticky_logo  = $page_meta_array['sticky_logo'];
            $default_logo      = isset( $page_meta_array['logo']['url'] ) ? $page_meta_array['logo']['url'] : $default_logo;
            $sticky_logo       = isset( $page_meta_array['sticky_logo']['url'] ) ? $page_meta_array['sticky_logo']['url'] : $sticky_logo;

            if ( empty( $sticky_logo ) && itbin_get_option( 'sticky_menu' ) == true ) {
                $sticky_logo = $default_logo;
            }
        }
        
        ?>
        <?php if ( !empty( $default_logo ) &&  !empty( $sticky_logo ) ) : ?>
            <a href="<?php echo esc_url( home_url('/') ); ?>" class="custom-logo-link default-logo">
                <?php echo itbin_get_logo_type_tag( $default_logo ); ?>
            </a>
            <a href="<?php echo esc_url( home_url('/') ); ?>" class="custom-logo-link sticky-logo">
                <?php echo itbin_get_logo_type_tag( $sticky_logo ); ?>
            </a>
        <?php elseif( !empty( $default_logo ) && empty( $sticky_logo ) && itbin_get_option('sticky_menu') == false ): ?>
            <a href="<?php echo esc_url( home_url('/') ); ?>" class="custom-logo-link">
                <?php echo itbin_get_logo_type_tag( $default_logo ); ?>
            </a>
        <?php else: ?>
        <h3>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
            </a>
        </h3>
    <?php  endif;
    }
}

/*---------------------------
    DEFAULT LOGO
----------------------------*/
if ( !function_exists('itbin_default_logo') ) {
    function itbin_default_logo(){
        if ( has_custom_logo() ) :
            the_custom_logo('navbar-brand'); 
        else: ?>
            <h3>
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <?php echo esc_html( get_bloginfo('name') ); ?>
                </a>
            </h3>
        <?php
        endif;
    }
}



/*----------------------------
    PAGE TITLE
-----------------------------*/
if ( !function_exists('itbin_title') ) {
    function itbin_title(){ ?>
        <?php
            if ( is_page() ) {
                $page_meta_array = itbin_metabox_value('_itbin_page_metabox');
                $enable_title    = isset( $page_meta_array['enable_title'] ) ? $page_meta_array['enable_title'] : false;
                $custom_title    = isset( $page_meta_array['custom_title'] ) ? $page_meta_array['custom_title'] : '';
            }
            $itbin_blog_title = itbin_get_option( 'blog_page_title' );
        ?>
        <div class="barner-area white">
            <div class="barner-area-bg"></div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        
                        <?php if ( (is_home() && is_front_page() ) || is_page_template( 'blog-classic.php' ) ) : ?>

                            <div class="page-title">
                                <?php if( $itbin_blog_title == !'' ): ?>
                                    <h1><?php echo esc_html( $itbin_blog_title ); ?></h1>
                                <?php else: ?>
                                <h1>
                                    <?php esc_html_e('Blog Page','itbin'); ?>
                                </h1>
                                <?php endif; ?>

                                <?php if (get_bloginfo( 'description')) :?>
                                <p>
                                    <?php bloginfo( 'description' ); ?>
                                </p>
                                <?php endif; ?>
                            </div>

                        <?php elseif( is_page() ): ?>
                        
                            <div class="page-title">
                                <h1>
                                    <?php
                                        if ( $enable_title == true && !empty($custom_title) ) {
                                            echo esc_html( $custom_title );
                                        }else{
                                           wp_title( $sep = ' ');
                                        }
                                     ?>
                                </h1>
                            </div>

                            <?php if( '1' == itbin_get_option( 'show_page_breadcrumb', true ) ) : ?>
                            <div class="breadcumb">
                                <?php if (function_exists('itbin_breadcrumbs')) {
                                    itbin_breadcrumbs();
                                } ?>
                            </div>
                            <?php endif; ?>

                        <?php elseif( is_single() ): ?>

                            <div class="page-title">
                                <h1>
                                    <?php
                                        if ( 'portfolio' == get_post_type() ) {
                                            $title_text = itbin_get_option('portfolio_custom_title') ? itbin_get_option('portfolio_custom_title') : 'Work Details';
                                            if ( '1' == itbin_get_option('enable_portfolio_custom_title' ) && !empty( $title_text ) ) {
                                                echo esc_html( $title_text );
                                            }else{
                                                wp_title( $sep = ' ');
                                            }
                                        }else{
                                            $title_text = itbin_get_option('post_custom_title') ? itbin_get_option('post_custom_title') : 'News Details';
                                            if ( '1' == itbin_get_option('enable_post_custom_title' ) && !empty( $title_text ) ) {
                                                echo esc_html( $title_text );
                                            }else{
                                                wp_title( $sep = ' ');
                                            }
                                        }
                                    ?>
                                </h1>
                                
                                <?php if( '1' == itbin_get_option( 'show_post_breadcrumb', true ) ) : ?>
                                <div class="breadcumb">
                                    <?php if ( function_exists('itbin_breadcrumbs') ) {
                                        itbin_breadcrumbs();
                                    } ?>
                                </div>
                                <?php endif; ?>

                            </div>
                            <?php if ( '1' == itbin_get_option('enable_post_barner_top_meta' ) ) :?>
                            <div class="breadcumb">
                                <?php itbin_posted_on(); ?>
                            </div>
                            <?php endif; ?>

                            <?php if( get_post_type() === 'post' ) : ?>

                                <?php
                                    global $post;
                                    $author_id   = $post->post_author;
                                    $user_id     = get_current_user_id();
                                    $usermeta    = get_user_meta( $user_id,'itbin_profile_options',true );
                                    $designation = isset( $usermeta['designation'] ) ? $usermeta['designation'] : '';
                                ?>
                                <div class="single__post__author">
                                    <a class="author__thumbnail" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID', $author_id ) ) ); ?>">
                                        <img src="<?php echo esc_url( get_avatar_url( get_the_author_meta( 'email', $author_id ) ) ); ?>" alt="<?php the_title_attribute( array('echo' => true)); ?>">
                                    </a>
                                    <div class="signle__post__author__details">
                                    <a class="author__link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'display_name', $author_id ) ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></a>
                                    <p class="author__desig"><?php echo esc_html( $designation ); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php elseif( is_search() ): ?>

                            <div class="page-title">
                                <h1><?php esc_html_e( 'Search Results', 'itbin' ); ?></h1>
                                <p><?php  printf(__( 'Search Result For "%s"','itbin'), get_search_query() ) ?></p>
                            </div>
                            
                        <?php elseif(is_archive()): ?>
                            
                            <?php if ( isset($_GET['author_downloads'] ) && $_GET['author_downloads'] == 'true' ) :?>

                                <?php get_template_part( 'edd/author/author-download-top-meta' ); ?>
                                
                            <?php else: ?>

                                <div class="page-title">
                                    <h1>
                                        <?php the_archive_title(); ?>
                                    </h1>
                                </div>
                                <div class="breadcumb">
                                    <?php
                                        if (function_exists('itbin_breadcrumbs')) {
                                            itbin_breadcrumbs();
                                        }
                                    ?>
                                    <p>
                                        <?php the_archive_description(); ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>

                            <div class="page-title">
                                <h1>
                                    <?php wp_title( $sep = ' '); ?>
                                </h1>
                            </div>
                            <div class="breadcumb">
                                <p>
                                    <?php bloginfo( 'description' ); ?>
                                </p>
                            </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
            <?php
                if ( 'post' === get_post_type() && is_single() && function_exists( 'itbin_post_barner_multimeta' ) && '1' == itbin_get_option('enable_post_barner_bottom_meta' ) ) {
                    itbin_post_barner_multimeta();
                }
            ?>
        </div>
    <?php
    }
}


/*------------------------------
    COMMENT FORM FIELD
-------------------------------*/
if( ! function_exists('itbin_comment_form_default_fields') ){

    function itbin_comment_form_default_fields($fields){
        global $aria_req;
        $commenter     = wp_get_current_commenter();
        $req           = get_option( 'require_name_email' );
        $aria_req      = ($req ? " aria-required='true' " : '');
        $required_text = ' ';    
        $fields        =  array(
            'author'   => '<div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <input type="text" name="author" value="'.esc_attr( $commenter['comment_author'] ).'" '.$aria_req.' placeholder="'.esc_attr__( 'Your Name *', 'itbin' ).'">
                            </div>',
            'email'    => '<div class="col-sm-6">
                                <input type="email" name="email" value="'.esc_attr( $commenter['comment_author_email'] ).'" '.$aria_req.' placeholder="'.esc_attr__( 'Your Email *', 'itbin' ).'">
                            </div>
                        </div>
                    </div>',
            'url'      => '<div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <input type="url" name="url" value="'.esc_url( $commenter['comment_author_url'] ).'" '.$aria_req.' placeholder="'.esc_attr__( 'Your Website', 'itbin' ).'">
                                    </div>
                                </div>
                            </div>'
        );
        return $fields;
    }
}
add_filter('comment_form_default_fields', 'itbin_comment_form_default_fields');


/*-----------------------------------------
    OVERWRITE COMMENT FORM DEFAULT
-------------------------------------------*/
if( ! function_exists('itbin_comment_form_defaults') ){

    function itbin_comment_form_defaults( $defaults ) {
        global $aria_req;
        $defaults = array(
            'class_form'    => 'comment-form',
            'title_reply'   => esc_html__( 'Leave A Comment', 'itbin' ),
            'comment_field' => '<div class="form-group mb0">
                                    <textarea name="comment" placeholder="'.esc_attr__( 'Your Comment', 'itbin' ).'" '.$aria_req.' rows="10"></textarea>    
                                </div>',
            'comment_notes_before'  => '',
            'label_submit'  => esc_html__( 'Post Comment', 'itbin' ),
        );
        return $defaults;
    }    
}
add_filter( 'comment_form_defaults', 'itbin_comment_form_defaults' );


/*--------------------------
    POSTS PAGINATION
---------------------------*/
if ( !function_exists('itbin_pagination') ) {
    function itbin_pagination(){
        the_posts_pagination(array(
            'screen_reader_text' => ' ',
            'prev_text'          => '<i class="ti-arrow-left"></i>',
            'next_text'          => '<i class="ti-arrow-right"></i>',
            'type'               => 'list',
            'mid_size'           => 1,
        ));
    }
}

/*------------------------
    POSTS PAGINATION CUSTOM
-------------------------*/
if ( !function_exists('itbin_custom_pagination') ) {
    function itbin_custom_pagination( $query = false ){

        global $wp_query;
        if ($query) {
            $temp_query = $wp_query;
            $wp_query   = $query;
        }

        /*Return early if there's only one page.*/
        if ($GLOBALS['wp_query']->max_num_pages < 2) {
            return;
        }

        $big_data = 999999999;
        echo '<nav class="navigation pagination"><div class="nav-links">';
        echo paginate_links(array(
            'prev_text'          => '<i class="ti-arrow-left"></i>',
            'next_text'          => '<i class="ti-arrow-right"></i>',
            'screen_reader_text' => ' ',
            'mid_size'           => 1,
            'base'               => get_pagenum_link(1) . '%_%',
            'base'               => str_replace($big_data, '%#%', esc_url(get_pagenum_link($big_data))),
            'format'             => 'page/%#%',
            'current'            => max( 1, get_query_var('paged') ),
            'total'              => $wp_query->max_num_pages,
            'prev_next'          => true,
            'type'               => 'list',
        ));
        echo '</div></nav>';
    }
}

/*------------------------
    POSTS NAVIGATION
--------------------------*/
if ( !function_exists('itbin_navigation') ) {
    function itbin_navigation(){
        the_posts_navigation(array(
            'screen_reader_text' => ' ',        
            'prev_text'          => '<i class="ti ti-angle-double-left"></i> '.esc_html__( 'Older posts', 'itbin' ),
            'next_text'          => esc_html__( 'Newer posts', 'itbin' ).' <i class="ti ti-angle-double-right"></i>',
        )); 
    }
}

/*------------------------
    SINGLE POST NAVIGATION
--------------------------*/
if ( !function_exists('itbin_single_navigation') ) {
    function itbin_single_navigation(){
        the_post_navigation( array(
            'screen_reader_text' => ' ',  
            'prev_text'          => '<i class="ti ti-angle-double-left"></i> '.esc_html__( 'Prev Post', 'itbin' ),
            'next_text'          => esc_html__( 'Next Post', 'itbin' ).' <i class="ti ti-angle-double-right"></i>',
        ));
    }
}

/*----------------------
    SINGLE POST NAVIGATION
------------------------*/
if ( !function_exists('itbin_post_navigation') ) {
    function itbin_post_navigation(){
        global $post;
        $next_post = get_adjacent_post(false, '', false);
        $prev_post = get_adjacent_post(false, '', true);
        ?>
        <div class="single-post-navigation">

            <?php if( !empty($prev_post) ): ?>
            <div class="prev-post">
                <a href="<?php echo esc_url( get_permalink($prev_post->ID) ); ?>">
                    <div class="arrow-link">
                        <i class="fa fa-arrow-left"></i>
                    </div>
                    <div class="title-with-link">
                        <span><?php esc_html_e( 'Prev Post', 'itbin' ) ?></span>
                        <h3><?php echo esc_html( wp_trim_words( $prev_post->post_title, 4, '.' ) ); ?></h3>
                    </div>
                </a>
            </div>
            <?php endif; ?>

            <div class="single-post-navigation-center-grid">
                <a href="<?php echo esc_url( home_url('/') ) ?>"><i class="fa fa-th-large"></i></a>
            </div>

            <?php if( !empty($next_post) ): ?>
            <div class="next-post">
                <a href="<?php echo esc_url( get_permalink($next_post->ID) ); ?>">
                    <div class="title-with-link">
                        <span><?php esc_html_e( 'Next Post', 'itbin' ) ?></span>
                        <h3><?php echo esc_html( wp_trim_words( $next_post->post_title, 4, '.' ) ); ?></h3>
                    </div>
                    <div class="arrow-link">
                        <i class="fa fa-arrow-right"></i>
                    </div>
                </a>
            </div>
            <?php endif; ?>

        </div>
    <?php
    }
}

/*------------------------
    COMMENTS PAGINATION
-------------------------*/
if ( !function_exists('itbin_comments_pagination') ) {
    function itbin_comments_pagination(){
        the_comments_pagination(array(
            'screen_reader_text' => ' ',
            'prev_text'          => '<i class="ti-arrow-left"></i>',
            'next_text'          => '<i class="ti-arrow-right"></i>',
            'type'               => 'list',
            'mid_size'           => 1,
        ));
    }
}

/*------------------------
    COMMENTS NAVIGATION
-------------------------*/
if ( !function_exists('itbin_comments_navigation') ) {
    function itbin_comments_navigation(){
        the_comments_navigation(array(
            'screen_reader_text' => ' ',
            'prev_text'          => '<i class="ti ti-angle-double-left"></i> '.esc_html__( 'Older Comments', 'itbin' ),
            'next_text'          => esc_html__( 'Newer Comments', 'itbin' ).' <i class="ti ti-angle-double-right"></i>',
        ));
    }
}

/*----------------------------------
    SINGLE POST / PAGES LINK PAGES
------------------------------------*/
if ( !function_exists('itbin_link_pages') ) {
    function itbin_link_pages(){
        wp_link_pages( array(
            'before'           => '<div class="page-links post-pagination"><p>' . esc_html__( 'Pages:', 'itbin' ).'</p><ul><li>',
            'separator'        => '</li><li>',
            'after'            => '</li></ul></div>',
            'next_or_number'   => 'number',
            'nextpagelink'     => esc_html__( 'Next Page', 'itbin'),
            'previouspagelink' => esc_html__( 'Prev Page', 'itbin' ),
        ));
    }
}

/*----------------------------
    SEARCH FORM
------------------------------*/
if ( !function_exists('itbin_search_form') ) {
    function itbin_search_form(  $search_buttton=true, $is_button=true ) {
        ?>
        <div class="search-form">
            <form id="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="text" id="search" placeholder="<?php esc_attr_e('Search ...', 'itbin'); ?>" name="s">
                <?php if( $search_buttton == true ) : ?>
                    <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                <?php endif; ?>
            </form>
            <?php if( $is_button==true ) : ?>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="home_btn"> <?php esc_html_e('Back to home Page', 'itbin'); ?> </a>
            <?php endif; ?>
        </div>
        <?php
    }
}

/*-------------------------------------
    SEARCH PAGE SEARCH FORM
-------------------------------------*/
if ( !function_exists('itbin_search_page_search_form') ) {
    function itbin_search_page_search_form() {
        ?>
        <div class="search-form">            
            <form action="<?php echo esc_url(home_url('/')); ?>" method="get" _lpchecked="1">
                <input type="text" name="s" class="form-control search-field" id="search" placeholder="<?php esc_attr_e('Enter here your search query', 'itbin'); ?>" value="<?php echo get_search_query(); ?>">
                <button type="submit" class="search-submit search_btn"> <?php esc_html_e('Search', 'itbin') ?> </button>
            </form>
        </div>
        <?php
    }
}

/*------------------------------
    POST PASSWORD FORM
-------------------------------*/
if ( !function_exists('itbin_password_form') ) {
    function itbin_password_form($form) {
    global $post;
    $label  =   'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
    $form   =   '<form class="protected-post-form" action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
                    <span>'.esc_html__( "To view this protected post, enter the password below:", 'itbin' ).'</span>
                    <input name="post_password" id="' . $label . '" type="password"  placeholder="'.esc_attr__( "Enter Password", 'itbin' ).'">
                    <input type="submit" name="Submit" value="'.esc_attr__( "Submit",'itbin' ).'">
                </form>';
    return $form;
    }
}
add_filter( 'the_password_form', 'itbin_password_form' );


/*-------------------------------
    ADD CATEGORY NICENAMES IN BODY AND POST CLASS
--------------------------------*/
if ( !function_exists('itbin_post_class') ) {
   function itbin_post_class( $classes ) {
    
        global $post;
        if ( 'page' === get_post_type() ) {
            if(!has_post_thumbnail()) {
                $classes[] = 'no-post-thumbnail';
            }
        }

        if ( 'post' === get_post_type() ) {


            if ( is_page_template( 'blog-classic.php' ) ) {
                $classes[] = 'blog-classic';
            }

            if ( is_single() ) {
                $classes[] = 'single-post-item';
            }else{
                $classes[] = 'single-post-item mb40';
            }
        }
        return $classes;
    }
}
add_filter( 'post_class', 'itbin_post_class' );


/*-------------------------------
    DAY LINK TO ARCHIVE PAGE
---------------------------------*/
if ( !function_exists('itbin_day_link') ) {
    function itbin_day_link() {
        $archive_year   = get_the_time('Y');
        $archive_month  = get_the_time('m');
        $archive_day    = get_the_time('d');
        echo get_day_link( $archive_year, $archive_month, $archive_day);
    }
}

/*--------------------------------
    GET COMMENT COUNT TEXT
----------------------------------*/
if ( !function_exists('itbin_comment_count_text') ) {
    function itbin_comment_count_text($post_id) {
        $comments_number = get_comments_number($post_id);
        if($comments_number==0) {
            $comment_text = esc_html__('No comment', 'itbin');
        }elseif($comments_number == 1) {
            $comment_text = esc_html__('One comment', 'itbin');
        }elseif($comments_number > 1) {
            $comment_text = $comments_number.esc_html__(' Comments', 'itbin');
        }
        echo esc_html($comment_text);
    }
}

/*------------------------------------------
    GET POST TYPE ARRAY
--------------------------------------------*/
if ( !function_exists('itbin_get_post_array') ) {
    function itbin_get_post_array($post_type = 'elementor_library') {
        $query  = new WP_Query(
            array (
                'post_type'      => $post_type,
                'posts_per_page' => -1
            )
        );
        $posts_array = $query->posts;
        if( $posts_array ) {
            $post_title_array = wp_list_pluck( $posts_array, 'post_title', 'ID' );
        }else{
            $post_title_array['default'] = esc_html__( 'Default', 'itbin' );
        }
        return $post_title_array;
    }
}



/**
 * Remove schema attributes from custom logo html
 *
 * @param string $html
 * @return string
 */
function itbin_remove_custom_logo_schema_attr( $html ) {
    return str_replace( array( 'itemprop="url"', 'itemprop="logo"' ), '', $html );
}
add_filter( 'get_custom_logo', 'itbin_remove_custom_logo_schema_attr' );


/**
 * Remove schema attributes from oembed iframe html
 *
 * @param string $html
 * @return string
 */
function itbin_remove_oembed_schema_attr($return, $data, $url){
    if( is_object( $data ) ){
        $return = str_ireplace(
            array( 
                'frameborder="0"',
                'scrolling="no"',
                'frameborder="no"',
            ),
            '',
            $return
        );
    }
    return $return;
}
add_filter( 'oembed_dataparse', 'itbin_remove_oembed_schema_attr', 10, 3 );


/**
 * itbin_move_comment_field_to_bottom() Remove cookie field and move comment field bottom.
 * @param  $fields array()
 * @return return comment form fields
 */
function itbin_move_comment_field_to_bottom( $fields ) {
    $comment_field = $fields['comment'];
    unset( $fields['comment'] );
    unset( $fields['cookies'] );
    $fields['comment'] = $comment_field;
    return $fields;
}
add_filter( 'comment_form_fields', 'itbin_move_comment_field_to_bottom' );

function itbin_kses( $raw ) {
    $allowed_tags = array(
        'a' => array(
            'class'  => array(),
            'href'   => array(),
            'rel'    => array(),
            'title'  => array(),
            'target' => array(),
        ),
        'option' => array(
            'value' => array(),
        ),
        'abbr' => array(
            'title' => array(),
        ),
        'b'          => array(),
        'blockquote' => array(
            'cite' => array(),
        ),
        'cite' => array(
            'title' => array(),
        ),
        'code' => array(),
        'del'  => array(
            'datetime' => array(),
            'title'    => array(),
        ),
        'dd'  => array(),
        'div' => array(
            'class'  => array(),
            'title'  => array(),
            'style'  => array(),
        ),
        'dl' => array(),
        'dt' => array(),
        'em' => array(),
        'h1' => array(),
        'h2' => array(),
        'h3' => array(),
        'h4' => array(),
        'h5' => array(),
        'h6' => array(),
        'i'  => array(
            'class' => array(),
        ),
        'img' => array(
            'alt'    => array(),
            'class'  => array(),
            'height' => array(),
            'src'    => array(),
            'width'  => array(),
        ),
        'li' => array(
            'class' => array(),
        ),
        'ol' => array(
            'class' => array(),
        ),
        'p' => array(
            'class' => array(),
        ),
        'q' => array(
            'cite'   => array(),
            'title'  => array(),
        ),
        'span' => array(
            'class'  => array(),
            'title'  => array(),
            'style'  => array(),
        ),
        'iframe' => array(
            'width'       => array(),
            'height'      => array(),
            'scrolling'   => array(),
            'frameborder' => array(),
            'allow'       => array(),
            'src'         => array(),
        ),
        'strike'                         => array(),
        'br'                             => array(),
        'small'                          => array(),
        'strong'                         => array(),
        'data-wow-duration'              => array(),
        'data-wow-delay'                 => array(),
        'data-wallpaper-options'         => array(),
        'data-stellar-background-ratio'  => array(),
        'ul'                             => array(
            'class' => array(),
        ),
    );
    if ( function_exists( 'wp_kses' ) ) { // WP is here
        $allowed = wp_kses( $raw, $allowed_tags );
    } else {
        $allowed = $raw;
    }
    return $allowed;
}