<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>"><input class="search-sidebar" onblur="if (this.value=='') this.value='<?php _e( 'Search ...', 'convention'); ?>'" onfocus="if (this.value=='<?php _e( 'Search ...', 'convention'); ?>') this.value='';" value="<?php _e( 'Search ...', 'convention'); ?>" name="s" id="s"/></form>