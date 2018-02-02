<?php
/**
 * The header template file.
 * @package PaperCuts
 * @since PaperCuts 1.0.0
*/
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<?php global $papercuts_options_db; ?>
  <meta charset="<?php bloginfo( 'charset' ); ?>" /> 
  <meta name="viewport" content="width=device-width, minimumscale=1.0, maximum-scale=1.0" />  
  <title><?php wp_title( '|', true, 'right' ); ?></title>  
  <!--[if lt IE 9]>
	<script src="<?php echo esc_url(get_template_directory_uri()); ?>/js/html5.js"></script>
	<![endif]-->
<?php if ($papercuts_options_db['papercuts_favicon_url'] != ''){ ?>
	<link rel="shortcut icon" href="<?php echo esc_url($papercuts_options_db['papercuts_favicon_url']); ?>" />
<?php } ?>
<?php wp_head(); ?>  
</head>


 
<body <?php body_class(); ?> id="wrapper">

<?php if ( has_nav_menu( 'top-navigation' ) || $papercuts_options_db['papercuts_header_facebook_link'] != '' || $papercuts_options_db['papercuts_header_twitter_link'] != '' || $papercuts_options_db['papercuts_header_google_link'] != '' || $papercuts_options_db['papercuts_header_rss_link'] != '' ) {  ?>

 
<div id="top-navigation-wrapper">
  <div class="top-navigation">
<?php if ( has_nav_menu( 'top-navigation' ) ) { wp_nav_menu( array( 'menu_id'=>'top-nav', 'theme_location'=>'top-navigation' ) ); } ?>
      
    <div class="header-icons">
<?php if ($papercuts_options_db['papercuts_header_facebook_link'] != ''){ ?>
      <a class="social-icon facebook-icon" href="<?php echo esc_url($papercuts_options_db['papercuts_header_facebook_link']); ?>"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/icon-facebook.png" alt="Facebook" /></a>
<?php } ?>
<?php if ($papercuts_options_db['papercuts_header_twitter_link'] != ''){ ?>
      <a class="social-icon twitter-icon" href="<?php echo esc_url($papercuts_options_db['papercuts_header_twitter_link']); ?>"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/icon-twitter.png" alt="Twitter" /></a>
<?php } ?>
<?php if ($papercuts_options_db['papercuts_header_google_link'] != ''){ ?>
      <a class="social-icon google-icon" href="<?php echo esc_url($papercuts_options_db['papercuts_header_google_link']); ?>"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/icon-google.png" alt="Google +" /></a>
<?php } ?>
<?php if ($papercuts_options_db['papercuts_header_rss_link'] != ''){ ?>
      <a class="social-icon rss-icon" href="<?php echo esc_url($papercuts_options_db['papercuts_header_rss_link']); ?>"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/icon-rss.png" alt="RSS" /></a>
<?php } ?>



    </div>
  </div>
</div>
<?php } ?>

<header id="wrapper-header">  
  <div id="header">
  <div class="header-content-wrapper">
    <div class="header-content">
<?php if ( $papercuts_options_db['papercuts_logo_url'] == '' ) { ?>
      <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></p>
<?php if ( $papercuts_options_db['papercuts_display_site_description'] != 'Hide' ) { ?>
      <p class="site-description"><?php bloginfo( 'description' ); ?></p>
<?php } ?>
<?php } else { ?>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img class="header-logo" src="<?php echo esc_url($papercuts_options_db['papercuts_logo_url']); ?>" alt="<?php bloginfo( 'name' ); ?>" /></a>
<?php } ?>
<?php if ( $papercuts_options_db['papercuts_display_search_form'] != 'Hide' ) { ?>
<?php get_search_form(); ?>
<?php } ?>
    </div>
  </div>
  
<?php   //has_nav_menu( 'main-navigation' ) ?>

<?php //echo 1111; 
	  //echo "<br/>";
	  
	  //$get_fields = get_option( 'custom_profile_field' );
	  //$get_fields = unserialize( $get_fields);
	  //var_dump($get_fields);
	  
	 /* foreach($get_fields as $get_fiel)
      {
	  		print"<br/>";
			foreach($get_fiel as $get_f)
			{
				print"\n<tr><td>".$get_f."</td></tr>";
				//print"<br/>";
			}
      }*/
	  
	  //echo json_encode($get_fields);
	  //echo "<br/>";	
	  
	 //$current_user = current_user_can( 'manage_network_users' );
	 //is_user_logged_in();
	 
     //echo "current_user ="; //echo current_user_can( 'manage_network_users' );
	 //echo current_user_can( 'manage_options' );
	 
	 //echo "<br/>";
     
     //echo "is_user_logged_in ="; //echo current_user_can( 'manage_network_users' );
	 //echo is_user_logged_in();
	 
	 //echo "<br/>";
     
	 //$is_network_admin = is_network_admin();
	 
	// echo "is_network_admin()=";
	 //echo $is_network_admin;
	 
	 //echo "<br/>";
	 //$is_user_admin = is_user_admin();
	 
	 //echo "is_user_admin()=";
	 //echo $is_user_admin();
     //echo "<br/>";
     
     //wp_set_current_user(  );  //- вот мы сразу становимся не зарегистрированным пользователем
     //echo "wp_get_current_user=";
     //$get_current_user = wp_get_current_user();
     //var_dump($get_current_user);
     //echo $get_current_user;
     //echo "<br/>";
     //echo "data=";
     //echo $data;
  
  
 $is_user_logged_in = is_user_logged_in();     
?>


<?php if ( $is_user_logged_in ) { ?>
  <!--<div class="menu-box-wrapper">-->
    <div class="menu-box">
	<?php //echo "<br/> ";
			//echo 123456;
			//echo "<br/> ";
						 ?>
<?php wp_nav_menu( array( 'menu_id'=>'nav', 'theme_location'=>'main-navigation' ) ); ?>
    </div>
  <!--</div>-->
<?php } ?>
  </div> <!-- end of header -->
</header> <!-- end of wrapper-header -->

<div id="container">
  <div id="main-content">
  <div id="content">
  
<?php if ( is_home() || is_front_page() ) { ?>
<?php if ( get_header_image() != '' ) { ?>
  <div class="header-image-wrapper"><div class="header-image"><img src="<?php header_image(); ?>" alt="<?php bloginfo( 'name' ); ?>" /></div></div>
<?php } ?> 
<?php } else { ?>
<?php if ( get_header_image() != '' && $papercuts_options_db['papercuts_display_header_image'] == 'Everywhere' ) { ?>
    <div class="header-image-wrapper"><div class="header-image"><img src="<?php header_image(); ?>" alt="<?php bloginfo( 'name' ); ?>" /></div></div>
<?php } ?>
<?php } ?> 