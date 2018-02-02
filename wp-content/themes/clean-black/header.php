<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<?php if(cleanblack_get_options("favicon")) { ?><link rel="Shortcut Icon" href="<?php echo cleanblack_get_options("favicon");?>" type="image/x-icon" /><?php } ?>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="wrap">
<div id="header">
  <?php if(get_header_image()) { ?>
  <a href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php header_image(); ?>" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="" /></a>
  <?php } else { ?>
  <h2><a href="<?php echo esc_url(home_url('/')); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h2>
  <p><?php bloginfo('description'); ?></p><?php } ?>
  <div style="clear:both;"></div>  
</div>

<div id="navbarfull"><div id="navbar"><ul id="nav">
   <?php wp_nav_menu( array('theme_location' => 'primary')); ?>		
  </ul></div></div><div style="clear:both;"></div>