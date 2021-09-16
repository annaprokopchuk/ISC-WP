<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
	$timber = new Timber\Timber();
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function( $template ) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);
	return;
}


if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page();
	
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends Timber\Site {
	/** Add timber support. */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'init', array( $this, 'register_menus' ) );

		add_action('wp_enqueue_scripts', array( $this,  'style_theme'));
		add_action('wp_enqueue_scripts', array( $this, 'scripts_theme'));
		add_filter( 'upload_mimes', array( $this, 'upload_disallow_types'));
		parent::__construct();
	}
	/** This is where you can register custom post types. */
	public function register_post_types() {
		register_post_type( 'employee',
			array(
				'labels' => array(
					'name' => __( 'Співробітники' ),
					'singular_name' => __( 'Співробітник' )
				),
				'public' => true,
				'has_archive' => true,
				'supports' => array('title', 'editor', 'thumbnail', 'revisions')
				// 'taxonomies' => array('category', 'post_tag')
			)
		);
	}

	public function upload_disallow_types( $mimes ) {
			unset( $mimes['asf|asx'] );
			unset( $mimes['wmv'] );
			unset( $mimes['wmx'] );
			unset( $mimes['wm'] );
			unset( $mimes['avi'] );
			unset( $mimes['divx'] );
			unset( $mimes['flv]'] );
			unset( $mimes['mov|qt'] );
			unset( $mimes['mpeg|mpg|mpe'] );
			unset( $mimes['mp4|m4v'] );
			unset( $mimes['ogv'] );
			unset( $mimes['webm'] );
			unset( $mimes['mkv'] );
			unset( $mimes['3gp|3gpp'] );
			unset( $mimes['3g2|3gp2'] );
			unset( $mimes['mp3|m4a|m4b'] );
			unset( $mimes['ra|ram'] );
			unset( $mimes['wav'] );
			unset( $mimes['ogg|oga'] );
			unset( $mimes['mid|midi'] );
			unset( $mimes['wma'] );
			unset( $mimes['wax'] );
			unset( $mimes['mka'] );
		return $mimes;
	}


	/** This is where you can register custom taxonomies. */
	public function register_taxonomies() {

	}

	public function style_theme(){
		wp_enqueue_style('style', get_stylesheet_uri());
		
		wp_enqueue_style( 'montserrat', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap');
		wp_enqueue_style( 'uikit', 'https://cdn.jsdelivr.net/npm/uikit@3.7.0/dist/css/uikit.min.css');
		wp_enqueue_style( 'main', get_template_directory_uri() . '/css/main.css');
	}
	
	public function scripts_theme() {
		wp_deregister_script('uikit');
		wp_register_script('uikit', 'https://cdn.jsdelivr.net/npm/uikit@3.7.0/dist/js/uikit.min.js');
		wp_enqueue_script('uikit');
	}

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['menu']  = new Timber\Menu("primary");
		$context['menu_lang']  = new Timber\Menu("switch_lang");
		$context['options'] = get_fields('options');
		$context['site']  = $this;
		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );

		
	}



	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	public function register_menus() {
		register_nav_menus(array(
			'primary' => __('Головне меню'),
			'switch_lang' => __('Меню мов')
		));
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
		$twig->addFilter( new Twig\TwigFilter( 'myfoo', array( $this, 'myfoo' ) ) );
		return $twig;
	}

}

new StarterSite();
