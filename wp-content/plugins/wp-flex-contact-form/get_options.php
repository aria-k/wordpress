<?php

/*
Version: 0.2
*/

	/* Short and sweet */
	define('WP_USE_THEMES', false);
	require('../../../wp-blog-header.php');
	
	$post_param = stripslashes($_POST['param']);
	
	if( $post_param == "GET_XML_OPTIONS" )
	{	
		$xml = get_option('fcf_xml_options');
/*		$nonce = '<nonce>'.wp_create_nonce('my-nonce').'</nonce>';
		$xml = str_replace('<nonce></nonce>', $nonce, $xml);*/
		echo $xml;
	}
	
?>