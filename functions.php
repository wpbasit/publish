<?php
/**
 * Publish functions and definitions
 *
 * @package Publish
 * @since Publish 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since Publish 1.0
 */
if ( ! isset( $content_width ) )
	$content_width = 525; /* pixels */

if ( ! function_exists( 'publish_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since Publish 1.0
 */
function publish_setup() {

	/**
	 * Custom template tags for this theme.
	 */
	require( get_template_directory() . '/inc/template-tags.php' );

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 */
	load_theme_textdomain( 'publish', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable Custom Backgrounds
	 */
	add_theme_support( 'custom-background' );

	/**
	 * Enable editor style
	 */
	add_editor_style();

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'publish' ),
	) );

	/**
	 * Add support for the Aside Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'chat', 'image', 'video' ) );

	/**
	 * Custom headers support
	 * @since Publish 1.3
	 */
	add_theme_support( 'custom-header', array(
		'default-image' => publish_get_default_header_image(),
		'width' => 100,
		'height' => 100,
		'flex-width' => true,
		'flex-height' => true,
		'header-text' => false,
	) );

	/**
	 * Add support for infinite scroll
	 * @since Publish 1.2, Jetpack 2.0
	 */
	add_theme_support( 'infinite-scroll', array(
		'container' => 'content',
		'footer' => 'page',
	) );
}
endif; // publish_setup
add_action( 'after_setup_theme', 'publish_setup' );

/**
 * Register widgetized area and update sidebar with default widgets
 *
 * @since Publish 1.0
 */
function publish_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Sidebar', 'publish' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
}
add_action( 'widgets_init', 'publish_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function publish_scripts() {
	global $post;

	wp_enqueue_style( 'style', add_query_arg( 'v', 3, get_stylesheet_uri() ) );

	wp_enqueue_script( 'small-menu', get_template_directory_uri() . '/js/small-menu.js', array( 'jquery' ), '20120206', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image( $post->ID ) ) {
		wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
}
add_action( 'wp_enqueue_scripts', 'publish_scripts' );

/**
 * Footer credits, with support for infinite scroll.
 *
 * @since Publish 1.2
 */
function publish_footer_credits() {
	echo publish_get_footer_credits();
}
add_action( 'publish_credits', 'publish_footer_credits' );

function publish_get_footer_credits( $credits = '' ) {
	$credits = sprintf( __( 'Powered by %s', 'publish' ), '<a href="http://wordpress.org/" rel="generator">WordPress</a>' );
        $credits .= '<span class="sep"> | </span>';
        $credits .= sprintf( __( 'Theme: %1$s by %2$s.', 'publish' ), 'Publish', '<a href="http://kovshenin.com/" rel="designer">Konstantin Kovshenin</a>' );
	return $credits;
}
add_filter( 'infinite_scroll_credit', 'publish_get_footer_credits' );

/**
 * A default header image
 *
 * Use the admin email's gravatar as the default header image.
 *
 * @since Publish 1.3
 */
function publish_get_default_header_image() {
	$email = get_option( 'admin_email' );

	// Get default from Discussion Settings.
	$default = get_option( 'avatar_default', 'mystery' );
	if ( 'mystery' == $default )
		$default = 'mm';
	elseif ( 'gravatar_default' == $default )
		$default = '';

	$url = ( is_ssl() ) ? 'https://secure.gravatar.com' : 'http://gravatar.com';
	$url .= sprintf( '/avatar/%s/', md5( $email ) );
	$url = add_query_arg( 's', 100, $url );
	$url = add_query_arg( 'd', urlencode( $default ), $url ); // Mystery man default

	return esc_url_raw( $url );
}