<?php
/*
	Plugin Name: WP Flex Contact Form
	Plugin URI: http://www.flex-blog.com/components/wp-flex-contact-form/
	Description: WP Flex Contact Form is a contact form in Flash for users to contact you.
	Author: Flex-Blog.com (Arjan Nieuwenhuizen, Roelof Albers)
	Author URI: http://www.flex-blog.com
	Version: 0.2
*/

/*  Copyright 2009  Roelof Albers  (email : roelof@flex-blog.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$WPFlexContactForm = new WPFlexContactForm();
$WPFlexContactForm->initialize_fcf();

class WPFlexContactForm
{		
	function initialize_fcf() {
		add_action('admin_menu', array($this,'fcf_add_options_page'));
		add_filter('the_content', array($this, 'fcf_callback'), 99);
		add_action('wp_head', array(&$this, 'fcf_add_script_to_header'), 10);
		
		//add this swfobject.js to the header (this is used for showing the swf correctly in all browsers)
		wp_enqueue_script( 'swfobject', plugins_url(plugin_basename( dirname( __FILE__ )).'/js/swfobject.js'), array(), '2.1' );
	}

	function fcf_callback( $content ) {
		global $fcf_strings;
		
		/* Run the input check. */		
		if(false === strpos($content, '[fcf]')) {
			return $content;
		}
		
		$form = array();	
		$siteurl = plugins_url('/wp-flex-contact-form/WPFlexContactForm.swf');
		
		$form[] = ' <div id="flexcontactform" align="center">';
		$form[] = ' <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="WPFlexContactForm" class="flashmovie" width="450" height="400">';
		$form[] = '	<param name="movie" value="'.$siteurl.'" />';
		$form[] = '	<!--[if !IE]>-->';
		$form[] = '	<object	type="application/x-shockwave-flash" data="'.$siteurl.'" name="WPFlexContactForm" width="450" height="400">';
		$form[] = '	<!--<![endif]-->';			
		$form[] = '<p><a href="http://adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>';
		$form[] = '	<!--[if !IE]>-->';
		$form[] = '	</object>';
		$form[] = '	<!--<![endif]-->';
		$form[] = '</object>';
		//<!--Please show this link or make a donation to support our plugin. Thick the checkbox on the options page to show this url.-->
		$fcf_sponsor_link = stripslashes(get_option('fcf_sponsor_link'));
		if($fcf_sponsor_link) {
			$form[] = '<p><font size="1"><a href="http://www.flex-blog.com/components/wp-flex-contact-form/" target="_blank">Wordpress Flex Contact Form</a></font></p>';
		}
		else {
		//<!-- This text will be shown if you do not check the checkbox to support our plugin. -->
			$form[] = '<p>Powered by Flex-Blog.com</p>';
		}
		$form[] = '</div>';
		
		$form1 = join("\n", $form); 
		return str_replace('[fcf]', $form1, $content);
	}
	
	function fcf_add_script_to_header() {
		//to make the swf object is only registered within the correct page
		$current_page = "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
		
		if( $current_page == get_option('fcf_form_url')) {
	
			// Otherwise build out the script.
			$out = array();	
			
			$out[]		= '';
			$out[]		= '<script type="text/javascript" charset="utf-8">';
			$out[]		= '';
			$out[]		= '	(function(){';
			$out[]		= '		try {';		
			$out[]		= '			// Registering Statically Published SWFs';
			$out[]		= '			swfobject.registerObject("WPFlexContactForm","9.0.124");';
			$out[]		= '		} catch(e) {}';
			$out[]		= '	}())';
			$out[]		= '</script>';

			$script = join("\n", $out);
			echo $script;
		}
	}
	
	function fcf_add_options_page() {
		add_options_page(__('Flex Contact Form Options', 'fcf'), __('Flex Contact Form', 'fcf'), 'manage_options', 'wp-flex-contact-form/options-fcf.php');
	}
}
?>