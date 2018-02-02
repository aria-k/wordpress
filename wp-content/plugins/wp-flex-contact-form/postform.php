<?php
	/*
	Version: 0.2
	*/

	/* Short and sweet wordpress includes*/
	define('WP_USE_THEMES', false);
	require('../../../wp-blog-header.php');
	
	/* $nonce = $_REQUEST['_wpnonce']; */

	 /* This postform.php can only be called with the correct nonce */
	 /*
	 if (! wp_verify_nonce($nonce, 'my-nonce') ) 
	 {
		// die('Failed: Security check'); 
	 }	
	 */
	
	/* Retrieve post and option data */	
	$name = stripslashes($_POST['name']);
	$sender = stripslashes($_POST['sender']);
	$msg = stripslashes($_POST['msg']);
	$website = stripslashes($_POST['website']);
	$receiver = stripslashes(get_option('fcf_email'));
	$subject = stripslashes(get_option('fcf_subject'));
	
	/* Check some data input */	
	if($name != "" && $sender != "" && $msg != "")
	{
		/* Build email */
		$headers = "MIME-Version: 1.0\n";
		$headers .= "From: $name <$sender>\n";
		$headers .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";

		$fullmsg = "$name wrote:\n";
		$fullmsg .= wordwrap($msg, 80, "\n") . "\n\n";
		$fullmsg .= "Website: " . $website . "\n\n";
//		$fullmsg .= "nonce: " . $nonce . "\n";
		
		/* Send email */
		if(mail($receiver, $subject, $fullmsg, $headers)) {
			print 'true';
		} else { 
			print 'false';
		}
	}
	else{
		echo "Failed";
	}
?>