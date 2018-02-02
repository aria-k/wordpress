<?php
/**
 * Headerdata of Theme options.
 * @package PaperCuts
 * @since PaperCuts 1.0.0
*/  

// additional js and css
if(	!is_admin()){
function papercuts_fonts_include () {
global $papercuts_options_db;
// Google Fonts
$bodyfont = $papercuts_options_db['papercuts_body_google_fonts'];
$headingfont = $papercuts_options_db['papercuts_headings_google_fonts'];
$descriptionfont = $papercuts_options_db['papercuts_description_google_fonts'];
$headlinefont = $papercuts_options_db['papercuts_headline_google_fonts'];
$headlineboxfont = $papercuts_options_db['papercuts_headline_box_google_fonts'];
$postentryfont = $papercuts_options_db['papercuts_postentry_google_fonts'];
$sidebarfont = $papercuts_options_db['papercuts_sidebar_google_fonts'];
$menufont = $papercuts_options_db['papercuts_menu_google_fonts'];
$topmenufont = $papercuts_options_db['papercuts_top_menu_google_fonts'];

$fonturl = "http://fonts.googleapis.com/css?family=";

$bodyfonturl = $fonturl.$bodyfont;
$headingfonturl = $fonturl.$headingfont;
$descriptionfonturl = $fonturl.$descriptionfont;
$headlinefonturl = $fonturl.$headlinefont;
$headlineboxfonturl = $fonturl.$headlineboxfont;
$postentryfonturl = $fonturl.$postentryfont;
$sidebarfonturl = $fonturl.$sidebarfont;
$menufonturl = $fonturl.$menufont;
$topmenufonturl = $fonturl.$topmenufont;
	// Google Fonts
     if ($bodyfont != 'default' && $bodyfont != ''){
      wp_enqueue_style('google-font1', $bodyfonturl); 
		 }
     if ($headingfont != 'default' && $headingfont != ''){
      wp_enqueue_style('google-font2', $headingfonturl);
		 }
     if ($descriptionfont != 'default' && $descriptionfont != ''){
      wp_enqueue_style('google-font3', $descriptionfonturl);
		 }
     if ($headlinefont != 'default' && $headlinefont != ''){
      wp_enqueue_style('google-font4', $headlinefonturl); 
		 }
     if ($postentryfont != 'default' && $postentryfont != ''){
      wp_enqueue_style('google-font5', $postentryfonturl); 
		 }
     if ($sidebarfont != 'default' && $sidebarfont != ''){
      wp_enqueue_style('google-font6', $sidebarfonturl);
		 }
     if ($menufont != 'default' && $menufont != ''){
      wp_enqueue_style('google-font8', $menufonturl);
		 }
     if ($topmenufont != 'default' && $topmenufont != ''){
      wp_enqueue_style('google-font9', $topmenufonturl);
		 }
     if ($headlineboxfont != 'default' && $headlineboxfont != ''){
      wp_enqueue_style('google-font10', $headlineboxfonturl); 
		 }
}
add_action( 'wp_enqueue_scripts', 'papercuts_fonts_include' );
}

// additional js and css
function papercuts_css_include () {
global $papercuts_options_db;
	if ($papercuts_options_db['papercuts_css'] == 'Turquoise (default)' ){
			wp_enqueue_style('papercuts-style', get_stylesheet_uri());
		}

		if ($papercuts_options_db['papercuts_css'] == 'Blue' ){
			wp_enqueue_style('style-blue', get_template_directory_uri().'/css/blue.css');
		}

		if ($papercuts_options_db['papercuts_css'] == 'Red' ){
			wp_enqueue_style('style-red', get_template_directory_uri().'/css/red.css');
		}
}
add_action( 'wp_enqueue_scripts', 'papercuts_css_include' );

// Display sidebar
function papercuts_display_sidebar() {
global $papercuts_options_db;
    $display_sidebar = $papercuts_options_db['papercuts_display_sidebar']; 
		if ($display_sidebar == 'Hide') { ?>
		<?php _e('#wrapper #container #main-content #content { width: 100%; }', 'papercuts'); ?>
<?php } 
}

// Display header Search Form - header content width
function papercuts_display_search_form() {
global $papercuts_options_db;
    $display_search_form = $papercuts_options_db['papercuts_display_search_form']; 
		if ($display_search_form == 'Hide') { ?>
		<?php _e('#wrapper #header .header-content .site-title, #wrapper #header .header-content .site-description, #wrapper #header .header-content .header-logo { max-width: 100%; }', 'papercuts'); ?>
<?php } 
}

// Display Meta Box on posts - post entries styling
function papercuts_display_meta_post_entry() {
global $papercuts_options_db;
    $display_meta_post_entry = $papercuts_options_db['papercuts_display_meta_post']; 
		if ($display_meta_post_entry == 'Hide') { ?>
		<?php _e('#wrapper #main-content .post-entry .attachment-post-thumbnail { margin-bottom: 17px; } #wrapper #main-content .post-entry .post-entry-headline {margin-bottom: 0;}', 'papercuts'); ?>
<?php } 
}

// FONTS
// Body font
function papercuts_get_body_font() {
global $papercuts_options_db;
    $bodyfont = $papercuts_options_db['papercuts_body_google_fonts']; 
    if ($bodyfont != 'default' && $bodyfont != '') { ?>
    <?php _e('html body, #wrapper blockquote, #wrapper q, #wrapper #container #comments .comment, #wrapper #container #comments .comment time, #wrapper #container #commentform .form-allowed-tags, #wrapper #container #commentform p, #wrapper input, #wrapper button, #wrapper select { font-family: "', 'papercuts'); ?><?php echo $bodyfont ?><?php _e('", Arial, Helvetica, sans-serif; }', 'papercuts'); ?>
<?php } 
}

// Site title font
function papercuts_get_headings_google_fonts() {
global $papercuts_options_db;
    $headingfont = $papercuts_options_db['papercuts_headings_google_fonts']; 
		if ($headingfont != 'default' && $headingfont != '') { ?>
		<?php _e('#wrapper #wrapper-header .site-title { font-family: "', 'papercuts'); ?><?php echo $headingfont ?><?php _e('", Arial, Helvetica, sans-serif; }', 'papercuts'); ?>
<?php } 
}

// Site description font
function papercuts_get_description_font() {
global $papercuts_options_db;
    $descriptionfont = $papercuts_options_db['papercuts_description_google_fonts'];
    if ($descriptionfont != 'default' && $descriptionfont != '') { ?>
    <?php _e('#wrapper #wrapper-header .site-description {font-family: "', 'papercuts'); ?><?php echo $descriptionfont ?><?php _e('", Arial, Helvetica, sans-serif; }', 'papercuts'); ?>
<?php } 
}

// Page/post headlines font
function papercuts_get_headlines_font() {
global $papercuts_options_db;
    $headlinefont = $papercuts_options_db['papercuts_headline_google_fonts'];
    if ($headlinefont != 'default' && $headlinefont != '') { ?>
		<?php _e('#wrapper h1, #wrapper h2, #wrapper h3, #wrapper h4, #wrapper h5, #wrapper h6, #wrapper #container .navigation .section-heading { font-family: "', 'papercuts'); ?><?php echo $headlinefont ?><?php _e('", Arial, Helvetica, sans-serif; }', 'papercuts'); ?>
<?php } 
}

// PaperCuts Posts-List Widgets headlines font
function papercuts_get_headline_box_google_fonts() {
global $papercuts_options_db;
    $headline_box_google_fonts = $papercuts_options_db['papercuts_headline_box_google_fonts']; 
		if ($headline_box_google_fonts != 'default' && $headline_box_google_fonts != '') { ?>
		<?php _e('#wrapper #container #main-content section .entry-headline { font-family: "', 'papercuts'); ?><?php echo $headline_box_google_fonts ?><?php _e('", Arial, Helvetica, sans-serif; }', 'papercuts'); ?>
<?php } 
}

// Post entry font
function papercuts_get_postentry_font() {
global $papercuts_options_db;
    $postentryfont = $papercuts_options_db['papercuts_postentry_google_fonts']; 
		if ($postentryfont != 'default' && $postentryfont != '') { ?>
		<?php _e('#wrapper #main-content .post-entry .post-entry-headline, #wrapper #main-content .slides li, #wrapper #main-content .home-list-posts ul li { font-family: "', 'papercuts'); ?><?php echo $postentryfont ?><?php _e('", Arial, Helvetica, sans-serif; }', 'papercuts'); ?>
<?php } 
}

// Sidebar and Footer widget headlines font
function papercuts_get_sidebar_widget_font() {
global $papercuts_options_db;
    $sidebarfont = $papercuts_options_db['papercuts_sidebar_google_fonts'];
    if ($sidebarfont != 'default' && $sidebarfont != '') { ?>
		<?php _e('#wrapper #container #sidebar .sidebar-widget .sidebar-headline, #wrapper #wrapper-footer #footer .footer-widget .footer-headline { font-family: "', 'papercuts'); ?><?php echo $sidebarfont ?><?php _e('", Arial, Helvetica, sans-serif; }', 'papercuts'); ?>
<?php } 
}

// Main Header menu font
function papercuts_get_menu_font() {
global $papercuts_options_db;
    $menufont = $papercuts_options_db['papercuts_menu_google_fonts']; 
		if ($menufont != 'default' && $menufont != '') { ?>
		<?php _e('#wrapper #wrapper-header .menu-box ul li { font-family: "', 'papercuts'); ?><?php echo $menufont ?><?php _e('", Arial, Helvetica, sans-serif; }', 'papercuts'); ?>
<?php } 
}

// Top Header menu font
function papercuts_get_top_menu_font() {
global $papercuts_options_db;
    $topmenufont = $papercuts_options_db['papercuts_top_menu_google_fonts']; 
		if ($topmenufont != 'default' && $topmenufont != '') { ?>
		<?php _e('#wrapper #top-navigation-wrapper .top-navigation ul li { font-family: "', 'papercuts'); ?><?php echo $topmenufont ?><?php _e('", Arial, Helvetica, sans-serif; }', 'papercuts'); ?>
<?php } 
}

// User defined CSS.
function papercuts_get_own_css() {
global $papercuts_options_db;
    $own_css = $papercuts_options_db['papercuts_own_css']; 
		if ($own_css != '') { ?>
		<?php echo esc_attr($own_css); ?>
<?php } 
}

// Display custom CSS.
function papercuts_custom_styles() { ?>
<?php echo ("<style type='text/css'>"); ?>
<?php papercuts_get_own_css(); ?>
<?php papercuts_display_sidebar(); ?>
<?php papercuts_display_search_form(); ?>
<?php papercuts_display_meta_post_entry(); ?>
<?php papercuts_get_body_font(); ?>
<?php papercuts_get_headings_google_fonts(); ?>
<?php papercuts_get_description_font(); ?>
<?php papercuts_get_headlines_font(); ?>
<?php papercuts_get_headline_box_google_fonts(); ?>
<?php papercuts_get_postentry_font(); ?>
<?php papercuts_get_sidebar_widget_font(); ?>
<?php papercuts_get_menu_font(); ?>
<?php papercuts_get_top_menu_font(); ?>
<?php echo ("</style>"); ?>
<?php
} 
add_action('wp_enqueue_scripts', 'papercuts_custom_styles');	?>