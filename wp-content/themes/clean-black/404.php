<?php get_header(); ?>

<div id="content">

	<div id="homepage">
<div class="not-found"><h1>Page Not Found</h1></div>

<div class="mediumfont">The page you are looking for is currently unavailable or has been removed permanently.</div>
		
	<div class="mediumfont">Please try the following:
<ul>
<li>Use search form which is on the sidebar.</li>
<li>Try browsing by picking the "Topics" from Sidebar or Choose random posts below.</li>
<li>Go back to <a href="<?php echo home_url(); ?>">Homepage</a> and then start browsing.</li>
</ul>
	  <br/>
	  <p>HTTP 404 - Not Found</p></div>
<div class="headingred">Random Posts</div>  
<ol>
<?php
$args = array( 'numberposts' => 10, 'orderby' => 'rand');
$rand_posts = get_posts( $args );
foreach( $rand_posts as $post ) : ?>
	<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
<?php endforeach; ?>
</ol></div>
</div>
<?php get_template_part('sidebar','');?>

<!-- The main column ends  -->

<?php get_footer(); ?>