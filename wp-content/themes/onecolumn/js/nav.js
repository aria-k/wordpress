/*
 * Source: http://maxfoundry.com/blog/responsive-mobile-navigation-in-wordpress/
 * Copyright author: John Hartley
 * Author URI: http://maxfoundry.com/blog/author/johnbhartley 
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

jQuery(document).ready(function() {
            
            // build <select> dropdown
            jQuery("<select />").appendTo("div.nav-head");

            //Create deafult option "Go to..."
            jQuery("<option />", {
                "selected": "selected",
                "value": "",
                "text": "Menu..."  
            }).appendTo(".nav-head select");

            //Populate
            jQuery(".nav-head ul li a").each(function() {
                var el = jQuery(this);
                    if(el.parents('.nav-head ul ul').length) {
                        // if there are ul in li
                        jQuery('<option />', {
                        'value': el.attr('href'),
                        'text':  '- ' + el.text()
                    }).appendTo('.nav-head select');
                    } else { // if no ul in li
                        jQuery('<option />', {
                        'value': el.attr('href'),
                        'text': el.text()
                    }).appendTo('.nav-head select');
                }
            });

            //make links work 
            jQuery(".nav-head select").change(function() {
                window.location = jQuery(this).find("option:selected").val();
            });
    });