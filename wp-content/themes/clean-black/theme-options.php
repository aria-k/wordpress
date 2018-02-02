<?php
class ThemeOptionsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_themeoptions_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_themeoptions_page()
    {
        // This page will be under "Settings"
        add_theme_page(
            'Clean Black Theme Setting', 
            'Theme Options', 
            'manage_options', 
            'cleanblack_admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'cleanblack_options' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Clean Black Theme Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'cleanblack_option_group' );   
                do_settings_sections( 'cleanblack_admin' );
                submit_button(); 
                ?>
            </form>
            <p>Thank you for using Clean Black. You can send feedback via <a target="_blank" href="http://cwlk.org">cwlk.org</a></p>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'cleanblack_option_group', // Option group
            'cleanblack_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        //Favicon section
        add_settings_section(
            'setting_section_id', // ID
            'Favicon Icon Setting', // Title
            array( $this, 'print_section_info' ), // Callback
            'cleanblack_admin' // Page
        );  
        add_settings_field(
            'favicon', 
            'URL : ', 
            array( $this, 'favicon_callback' ), 
            'cleanblack_admin', 
            'setting_section_id'
        );

        //Show/Hide Post thumbnail
        add_settings_section(
            'setting_section_id2', // ID
            'Show or Hide Post Thumbnail', // Title
            array( $this, 'print_section_info2' ), // Callback
            'cleanblack_admin' // Page
        );

        add_settings_field(
            'postthumb', 
            'Show/Hide', 
            array( $this, 'postthumb_callback' ), 
            'cleanblack_admin', 
            'setting_section_id2'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['favicon'] ) )
            $new_input['favicon'] = sanitize_text_field( $input['favicon'] );

        if( isset( $input['postthumb'] ) )
            $new_input['postthumb'] = sanitize_key( $input['postthumb'] );
        return $new_input;
    }

    /** 
     * Favicon Section
     */
    public function print_section_info()
    {
        print 'Type or Paste the full url of your favicon icon including(http://).';
    }

    public function favicon_callback()
    {
        printf(
            '<input size="80" type="text" id="favicon" name="cleanblack_options[favicon]" value="%s" />',
            isset( $this->options['favicon'] ) ? esc_attr( $this->options['favicon']) : ''
        );
    }

    /** 
     * Show/Hide Post Thumbnail
     */
    public function print_section_info2()
    {
        print 'Either select to show or hide post thumbnail.';
    }
    public function postthumb_callback()
    {
        printf('<label><input type="radio" name="cleanblack_options[postthumb]" value="1" %s /> Show</label><br />
                <label><input type="radio" name="cleanblack_options[postthumb]" value="0" %s /> Hide</label>',
                ($this->options['postthumb'] )==1 ? 'checked="checked"' : '',
                ($this->options['postthumb'] )==0 ? 'checked="checked"' : ''
            );
    }

}

if( is_admin() )
    $my_settings_page = new ThemeOptionsPage();
