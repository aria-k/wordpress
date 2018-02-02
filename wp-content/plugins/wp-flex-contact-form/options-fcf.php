<?php
/*
Author: Roelof Albers
Author URI: http://www.flex-blog.com
Description: Options Page for Flex Contact Form
Version: 0.2
*/

// yes this function will fail if attempting to open this options page from outside the admin panel..

//Only an admin can change these settings..
if (is_admin())
{
	load_plugin_textdomain('fcf', $path = plugins_url('/wp-flex-contact-form/'));
	$location = get_option('siteurl') . '/wp-admin/admin.php?page=wp-flex-contact-form/options-fcf.php'; // Form Action URI
	$fcf_plugin_url = plugins_url('/wp-flex-contact-form/jscolor/jscolor.js');

	/*Lets add some default options if they don't exist*/
	add_option('fcf_email', 'you@example.com', '');
	add_option('fcf_subject', 'email from my website', '');
	add_option('fcf_form_title', 'my contact form', '');
	add_option('fcf_form_url', 'http://yourwebsite.com/contact/', '');
	add_option('fcf_background_color', 'FFFFFF', '');
	add_option('fcf_success_msg', 'message successfully sent', '');
	add_option('fcf_error_msg', 'error while sending message', '');
	add_option('fcf_name_label_text', 'name:', '');
	add_option('fcf_email_label_text', 'e-mail address:', '');
	add_option('fcf_website_label_text', 'website:', '');
	add_option('fcf_message_label_text', 'message:', '');
	add_option('fcf_send_button_text', 'send', '');
	add_option('fcf_name_error_msg', 'please fill in your name', '');
	add_option('fcf_email_error_msg', 'please fill in your e-mail address', '');
	add_option('fcf_message_error_msg', 'please fill in your message', '');
	add_option('fcf_sponsor_link', '', '');
	add_option('fcf_xml_options', '<formoptions></formoptions>', '');
	
	/*check form submission and update options*/
	if (isset ($_POST['stage']) && ( 'process' == $_POST['stage']) ) {
		update_option('fcf_email', $_POST['fcf_email']);
		update_option('fcf_subject', $_POST['fcf_subject']);
		update_option('fcf_form_title', $_POST['fcf_form_title']);
		update_option('fcf_background_color', $_POST['fcf_background_color']);
		update_option('fcf_sponsor_link', $_POST['fcf_sponsor_link']);
		update_option('fcf_form_url', $_POST['fcf_form_url']);
		update_option('fcf_success_msg', $_POST['fcf_success_msg']);
		update_option('fcf_error_msg', $_POST['fcf_error_msg']);
		update_option('fcf_name_label_text', $_POST['fcf_name_label_text']);
		update_option('fcf_email_label_text', $_POST['fcf_email_label_text']);
		update_option('fcf_website_label_text', $_POST['fcf_website_label_text']);
		update_option('fcf_message_label_text', $_POST['fcf_message_label_text']);
		update_option('fcf_send_button_text', $_POST['fcf_send_button_text']);		
		update_option('fcf_name_error_msg', $_POST['fcf_name_error_msg']);
		update_option('fcf_email_error_msg', $_POST['fcf_email_error_msg']);
		update_option('fcf_message_error_msg', $_POST['fcf_message_error_msg']);
		
		$xml = "<formoptions>";
		$xml .= "<fcf_form_title>" . $_POST['fcf_form_title'] . "</fcf_form_title>";
		$xml .= "<fcf_background_color>" . "0x" . $_POST['fcf_background_color'] . "</fcf_background_color>";
		$xml .= "<fcf_success_msg>" . $_POST['fcf_success_msg'] . "</fcf_success_msg>";
		$xml .= "<fcf_error_msg>" . $_POST['fcf_error_msg'] . "</fcf_error_msg>";
		$xml .= "<fcf_name_label_text>" . $_POST['fcf_name_label_text'] . "</fcf_name_label_text>";
		$xml .= "<fcf_email_label_text>" . $_POST['fcf_email_label_text'] . "</fcf_email_label_text>";
		$xml .= "<fcf_website_label_text>" . $_POST['fcf_website_label_text'] . "</fcf_website_label_text>";
		$xml .= "<fcf_message_label_text>" . $_POST['fcf_message_label_text'] . "</fcf_message_label_text>";
		$xml .= "<fcf_send_button_text>" . $_POST['fcf_send_button_text'] . "</fcf_send_button_text>";
		
		$xml .= "<fcf_name_error_msg>" . $_POST['fcf_name_error_msg'] . "</fcf_name_error_msg>";
		$xml .= "<fcf_email_error_msg>" . $_POST['fcf_email_error_msg'] . "</fcf_email_error_msg>";
		$xml .= "<fcf_message_error_msg>" . $_POST['fcf_message_error_msg'] . "</fcf_message_error_msg>";
		
		$xml .= "</formoptions>";

		update_option('fcf_xml_options', $xml);
	}

	/*Get options for form fields*/
	$fcf_email = stripslashes(get_option('fcf_email'));
	$fcf_subject = stripslashes(get_option('fcf_subject'));
	$fcf_form_title = stripslashes(get_option('fcf_form_title'));
	$fcf_form_url = stripslashes(get_option('fcf_form_url'));
	$fcf_sponsor_link = stripslashes(get_option('fcf_sponsor_link'));
	$fcf_error_msg = stripslashes(get_option('fcf_error_msg'));
	$fcf_success_msg = stripslashes(get_option('fcf_success_msg'));
	$fcf_name_label_text = stripslashes(get_option('fcf_name_label_text'));
	$fcf_email_label_text = stripslashes(get_option('fcf_email_label_text'));
	$fcf_website_label_text = stripslashes(get_option('fcf_website_label_text'));
	$fcf_message_label_text = stripslashes(get_option('fcf_message_label_text'));
	$fcf_send_button_text = stripslashes(get_option('fcf_send_button_text'));	
	$fcf_name_error_msg = stripslashes(get_option('fcf_name_error_msg'));
	$fcf_email_error_msg = stripslashes(get_option('fcf_email_error_msg'));
	$fcf_message_error_msg = stripslashes(get_option('fcf_message_error_msg'));
	
	if($fcf_sponsor_link) {
		$fcf_sponsor_link = ' checked="checked"';
	}
	else {
		$fcf_sponsor_link = '';
	}
	$fcf_background_color = stripslashes(get_option('fcf_background_color'));
	
	?>

	<script type="text/javascript" src="<?php echo $fcf_plugin_url; ?>"></script>

	<div class="wrap">
	  <h2><?php _e('Flex Contact Form Options', 'fcf') ?></h2>
	  <hr>
	  <h3><?php _e('Email Options', 'fcf') ?></h3>
	  <form name="form1" method="post" action="<?php echo $location ?>&amp;updated=true">
		<input type="hidden" name="stage" value="process" />

		<table width="100%" cellpadding="5" class="form-table">
			<tr valign="top">
				<th scope="row"><label for="fcf_form_title"><?php _e('Form Title', 'fcf') ?>:</label></th>
				<td><input name="fcf_form_title" id="fcf_form_title" type="text" size="50" value="<?php echo $fcf_form_title; ?>"/>
				<span class="description"><?php _e('This will be the title of the form.', 'fcf') ?></span>
				<br />
			 </tr>
			<tr valign="top">
				<th scope="row"><label for="fcf_email"><?php _e('E-mail Address', 'fcf') ?>:</label></th>
				<td><input name="fcf_email" type="text" id="fcf_email" value="<?php echo $fcf_email; ?>" size="50" />
				<span class="description"><?php _e('This will be the receiving e-mail address.', 'fcf') ?></span>
				<br />
				</td>
		  </tr>
		  <tr valign="top">
			<th scope="row"><label for="fcf_subject"><?php _e('Subject', 'fcf') ?>:</label></th>
			<td><input name="fcf_subject" type="text" id="fcf_subject" value="<?php echo $fcf_subject; ?>" size="50" />
				<span class="description"><?php _e('This will be the subject of the email.', 'fcf') ?></span>
				<br />
			</td>
		  </tr>
		  <tr valign="top">
			<th scope="row"><label for="fcf_form_url"><?php _e('Contact Form URL', 'fcf') ?>:</label></th>
			<td><input name="fcf_form_url" type="text" id="fcf_form_url" value="<?php echo $fcf_form_url; ?>" size="50" />
				<span class="description"><?php _e('This should be the url where the contact form will be shown. Not required, but for cleaner coding.', 'fcf') ?></span>
				<br />
			</td>
		  </tr>	  
		  <tr valign="top">
			<th scope="row"><label for="fcf_background_color"><?php _e('Backgroundcolor', 'fcf') ?>:</label></th>
			<td><input class="color" name="fcf_background_color" type="text" id="fcf_background_color" value="<?php echo $fcf_background_color; ?>" size="6" />
				<span class="description"><?php _e('This will be the background color of the contact form. Default/empty = white. Do NOT use a #.', 'fcf') ?></span>
				<br />
			</td>
		  </tr>				  
		  </table>
		  <hr>
		  <h3><?php _e('Message Options', 'fcf') ?></h3>
		  <table width="100%" cellpadding="5" class="form-table">		  
		  <!-- Sending Success Message -->
		  <tr valign="top">
			<th scope="row"><label for="fcf_success_msg"><?php _e('Result: Success Message', 'fcf') ?>:</label></th>
			<td><input name="fcf_success_msg" type="text" id="fcf_success_msg" value="<?php echo $fcf_success_msg; ?>" size="50" />
				<span class="description"><?php _e('This message will be shown if the email is successfully sent.', 'fcf') ?></span>
				<br />
			</td>
		  </tr>		  
		  <!-- Sending Error Message -->
		  <tr valign="top">
			<th scope="row"><label for="fcf_error_msg"><?php _e('Result: Error Message', 'fcf') ?>:</label></th>
			<td><input type="text" name="fcf_error_msg" type="text" id="fcf_error_msg" value="<?php echo $fcf_error_msg; ?>" size="50" />
				<span class="description"><?php _e('This message will be shown if the email could not be sent.', 'fcf') ?></span>
				<br />
			</td>
		  </tr>
		  <!-- Name Error Message -->
		  <tr valign="top">
			<th scope="row"><label for="fcf_name_error_msg"><?php _e('Name Error Message', 'fcf') ?>:</label></th>
			<td><input type="text" name="fcf_name_error_msg" type="text" id="fcf_name_error_msg" value="<?php echo $fcf_name_error_msg; ?>" size="50" />
				<span class="description"><?php _e('This message will be shown if the name field is empty', 'fcf') ?></span>
				<br />
			</td>
		  </tr>
		  <!-- Email Error Message -->
		  <tr valign="top">
			<th scope="row"><label for="fcf_email_error_msg"><?php _e('Email Error Message', 'fcf') ?>:</label></th>
			<td><input type="text" name="fcf_email_error_msg" type="text" id="fcf_email_error_msg" value="<?php echo $fcf_email_error_msg; ?>" size="50" />
				<span class="description"><?php _e('This message will be shown if the email field is incorrect or empty', 'fcf') ?></span>
				<br />
			</td>
		  </tr>
		  <!-- Message Error Message -->
		  <tr valign="top">
			<th scope="row"><label for="fcf_message_error_msg"><?php _e('Message Error Message', 'fcf') ?>:</label></th>
			<td><input type="text" name="fcf_message_error_msg" type="text" id="fcf_message_error_msg" value="<?php echo $fcf_message_error_msg; ?>" size="50" />
				<span class="description"><?php _e('This message will be shown if the message is empty', 'fcf') ?></span>
				<br />
			</td>
		  </tr>
	  
		  </table>
			<hr>
			<h3><?php _e('Language Options', 'fcf') ?></h3>		  
		  <table width="100%" cellpadding="5" class="form-table">		  
		  <!-- Name -->
		  <tr valign="top">
			<th scope="row"><label for="fcf_name_label_text"><?php _e('Name Label Text', 'fcf') ?>:</label></th>
			<td><input type="text" id="fcf_name_label_text" name="fcf_name_label_text" value="<?php echo $fcf_name_label_text; ?>" size="50" />
				<span class="description"><?php _e('This will be the label text shown for the name field. (correct display up to 16 characters.)', 'fcf') ?></span>
				<br />
			</td>
		  </tr>			
		  <!-- Email -->	
		  <tr valign="top">
			<th scope="row"><label for="fcf_email_label_text"><?php _e('Email Label Text', 'fcf') ?>:</label></th>
			<td><input type="text" id="fcf_email_label_text" name="fcf_email_label_text" value="<?php echo $fcf_email_label_text; ?>" size="50" />
				<span class="description"><?php _e('This will be the label text shown for the email field. (correct display up to 16 characters.)', 'fcf') ?></span>
				<br />
			</td>
		  </tr>				  
		  <!-- Website -->	
		  <tr valign="top">
			<th scope="row"><label for="fcf_website_label_text"><?php _e('Website Label Text', 'fcf') ?>:</label></th>
			<td><input type="text" id="fcf_website_label_text" name="fcf_website_label_text" value="<?php echo $fcf_website_label_text; ?>" size="50" />
				<span class="description"><?php _e('This will be the label text shown for the website field. (correct display up to 16 characters.)', 'fcf') ?></span>
				<br />
			</td>
		  </tr>	
		  <!-- Message -->	
		  <tr valign="top">
			<th scope="row"><label for="fcf_message_label_text"><?php _e('Message Label Text', 'fcf') ?>:</label></th>
			<td><input type="text" id="fcf_message_label_text" name="fcf_message_label_text" value="<?php echo $fcf_message_label_text; ?>" size="50" />
				<span class="description"><?php _e('This will be the label text shown for the message field. (correct display up to 16 characters.)', 'fcf') ?></span>
				<br />
			</td>
		  </tr>
		  <!-- Send Button -->	
		  <tr valign="top">
			<th scope="row"><label for="fcf_send_button_text"><?php _e('Message Label Text', 'fcf') ?>:</label></th>
			<td><input type="text" id="fcf_send_button_text" name="fcf_send_button_text" value="<?php echo $fcf_send_button_text; ?>" size="50" />
				<span class="description"><?php _e('This will be the button label for the send button.', 'fcf') ?></span>
				<br />
			</td>
		  </tr>		  
		  </table>
			<hr>
				  <h3><?php _e('Support this plugin', 'fcf') ?></h3>	  
		  <table width="100%" cellpadding="5" class="form-table">			  

		  <tr valign="top">
			<th scope="row" vertical-align="center"><label for="fcf_sponsor_link"><?php _e('Support this plugin?', 'fcf') ?>:</label></th>
			<td><input type="checkbox" id="fcf_sponsor_link" name="fcf_sponsor_link" value="1" <?php echo $fcf_sponsor_link; ?>" />
			<?php echo " or <a href='http://www.flex-blog.com'>Donate</a>"; ?>
			</form>
				<span class="description"><?php _e('A sponsorlink will appear below the contact form when you tick this checkbox. We really appreciate a backlink or a donation for this plugin.', 'fcf') ?></span>
				<br />
			</td>
		  </tr>		  
		  </table>

		<p class="submit">
		  <input type="submit" name="Submit" value="<?php _e('Update Options', 'fcf') ?> &raquo;" />
		</p>
	  </form>
	  
	</div>
<?php
}
?>