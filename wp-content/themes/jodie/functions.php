<?php // https://github.com/retlehs/roots/wiki

if (!defined('__DIR__')) define('__DIR__', dirname(__FILE__));

require_once locate_template('/inc/roots-activation.php');  // activation
require_once locate_template('/inc/roots-options.php');     // theme options
require_once locate_template('/inc/roots-cleanup.php');     // cleanup
require_once locate_template('/inc/roots-scripts.php');     // modified scripts output
require_once locate_template('/inc/roots-htaccess.php');    // rewrites for assets, h5bp htaccess
require_once locate_template('/inc/roots-hooks.php');       // hooks
require_once locate_template('/inc/roots-actions.php');     // actions
require_once locate_template('/inc/roots-widgets.php');     // widgets
require_once locate_template('/inc/roots-custom.php');      // custom functions

$roots_options = roots_get_theme_options();

// set the maximum 'Large' image width to the maximum grid width
// http://wordpress.stackexchange.com/q/11766
if (!isset($content_width)) {
  global $roots_options;
  $roots_css_framework = $roots_options['css_framework'];
  switch ($roots_css_framework) {
    case 'blueprint':   $content_width = 950;   break;
    case '960gs_12':    $content_width = 940;   break;
    case '960gs_16':    $content_width = 940;   break;
    case '960gs_24':    $content_width = 940;   break;
    case '1140':        $content_width = 1140;  break;
    case 'adapt':       $content_width = 940;   break;
    case 'bootstrap':   $content_width = 940;   break;
    case 'foundation':  $content_width = 980;   break;
    default:            $content_width = 950;   break;
  }
}

function roots_setup() {
  load_theme_textdomain('roots', get_template_directory() . '/lang');

  // tell the TinyMCE editor to use editor-style.css
  // if you have issues with getting the editor to show your changes then
  // use this instead: add_editor_style('editor-style.css?' . time());
  add_editor_style('editor-style.css');

  // http://codex.wordpress.org/Post_Thumbnails
  add_theme_support('post-thumbnails');
  // set_post_thumbnail_size(150, 150, false);

  // http://codex.wordpress.org/Post_Formats
  // add_theme_support('post-formats', array('aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat'));

  // http://codex.wordpress.org/Function_Reference/add_custom_image_header
  if (!defined('HEADER_TEXTCOLOR')) { define('HEADER_TEXTCOLOR', ''); }
  if (!defined('NO_HEADER_TEXT')) { define('NO_HEADER_TEXT', true); }
  if (!defined('HEADER_IMAGE')) { define('HEADER_IMAGE', get_template_directory_uri() . '/img/logo.png'); }
  if (!defined('HEADER_IMAGE_WIDTH')) { define('HEADER_IMAGE_WIDTH', 500); }
  if (!defined('HEADER_IMAGE_HEIGHT')) { define('HEADER_IMAGE_HEIGHT', 306); }

  function roots_custom_image_header_site() { }
  function roots_custom_image_header_admin() { ?>
    <style type="text/css">
      .appearance_page_custom-header #headimg { min-height: 0; }
    </style>
  <?php }
  add_custom_image_header('roots_custom_image_header_site', 'roots_custom_image_header_admin');

  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus(array(
    'primary_navigation' => __('Primary Navigation', 'roots'),
    'utility_navigation' => __('Utility Navigation', 'roots')
  ));
}

add_action('after_setup_theme', 'roots_setup');

// http://codex.wordpress.org/Function_Reference/register_sidebar
function roots_register_sidebars() {
  //$sidebars = array('Sidebar', 'Menu', 'Footer');

 // foreach($sidebars as $sidebar) {
    register_sidebar(
      array(
        'id'=> 'roots-' . strtolower($sidebar),
        'name' => __('Sidebar', 'roots'),
        'description' => __('Sidebar', 'roots'),
        'before_widget' => '<article id="%1$s" class="widget %2$s"><div class="container">',
        'after_widget' => '</div></article>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
      )
    );
	    register_sidebar(
      array(
        'id'=> 'fumbalina-dropdown-menu',
        'name' => __('gallery_menu', 'fumbalina'),
        'description' => __('Menu', 'fumb'),
        'before_widget' => '<article id="%1$s" class="widget %2$s"><div id="gallery_menu" class="fumbalina_gallery">',
        'after_widget' => '</div></article>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
      )
    );
//  }
}

add_action('widgets_init', 'roots_register_sidebars');

// return post entry meta information
function roots_entry_meta() {
  echo '<time class="updated" datetime="'. get_the_time('c') .'" pubdate>'. sprintf(__('Posted on %s at %s.', 'roots'), get_the_date(), get_the_time()) .'</time>';
  echo '<p class="byline author vcard">'. __('Written by', 'roots') .' <a href="'. get_author_posts_url(get_the_author_meta('id')) .'" rel="author" class="fn">'. get_the_author() .'</a></p>';
}

function loadFancyBox(){
	wp_deregister_script('fancybox');
	wp_register_script ('fancybox', get_template_directory_uri().'/js/fancybox/fancybox.js');
	wp_enqueue_script('fancybox');
	}
add_action('wp_enqueue_scripts', 'loadFancyBox');
function loadEasing(){
	wp_deregister_script('easing');
	wp_register_script ('easing', get_template_directory_uri().'/js/fancybox/easing.js');
	wp_enqueue_script('easing');
	}
add_action('wp_enqueue_scripts', 'loadEasing');
function loadFancyBoxCss(){
	wp_deregister_style ('fancybox');
	wp_register_style ('fancybox', get_template_directory_uri().'/css/fancybox/style.css');
	wp_enqueue_style ('fancybox');
	}
add_action('wp_enqueue_style', 'loadFancyBoxCss');

//deactivate WordPress function
remove_shortcode('gallery', 'gallery_shortcode');
//activate own function
add_shortcode('gallery', 'wpe_gallery_shortcode');
//the own renamed function

function wpe_gallery_shortcode($attr) {
	global $post;

	static $instance = 0;
	$instance++;

	// Allow plugins/themes to override the default gallery template.
	$output = apply_filters('post_gallery', '', $attr);
	if ( $output != '' )
		return $output;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => ''
		//$attachment->post_title 
	), $attr));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = $gallery_div = '';
	if ( apply_filters( 'use_default_gallery_style', true ) )
		$gallery_style = "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
		</style>
		<!-- see gallery_shortcode() in wp-includes/media.php -->";
	$size_class = sanitize_html_class( $size );
	$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";	
	
	$output = apply_filters('gallery_style', "
    <div id='$selector' class='gallery galleryid-{$id}'>");
$i = 0;
foreach ( $attachments as $id => $attachment ) {
    /*$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);*/
	$link = '<a href="'.wp_get_attachment_url($id).'" class="gallery" rel="'.$selector.'" title="'.$attachment->post_title.'">'.get_attachment_icon($id).'</a>';
   
	$output .= "<{$itemtag} class='gallery-item col-{$columns}'>";
    $output .= "
        <{$icontag} class='gallery-icon'>
            $link
        </{$icontag}>";
    if ( $captiontag && trim($attachment->post_excerpt) ) {
        $output .= "
            <{$captiontag} class='gallery-caption'>
            " . wptexturize($attachment->post_excerpt) . "
            </{$captiontag}>";
    }
    $output .= "</{$itemtag}>";
    if ( $columns > 0 && ++$i % $columns == 0 )
        $output .= '<br />';
}
$output .= "</div>\n";
return $output;
	
	
	
	
	
	
	
	

	
}









?>