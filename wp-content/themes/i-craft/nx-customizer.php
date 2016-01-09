<?php


function icraft_customizer_config() {
	

    $url  = get_stylesheet_directory_uri() . '/inc/kirki/';
	
    /**
     * If you need to include Kirki in your theme,
     * then you may want to consider adding the translations here
     * using your textdomain.
     * 
     * If you're using Kirki as a plugin then you can remove these.
     */

    $strings = array(
        'background-color' => __( 'Background Color', 'i-craft' ),
        'background-image' => __( 'Background Image', 'i-craft' ),
        'no-repeat' => __( 'No Repeat', 'i-craft' ),
        'repeat-all' => __( 'Repeat All', 'i-craft' ),
        'repeat-x' => __( 'Repeat Horizontally', 'i-craft' ),
        'repeat-y' => __( 'Repeat Vertically', 'i-craft' ),
        'inherit' => __( 'Inherit', 'i-craft' ),
        'background-repeat' => __( 'Background Repeat', 'i-craft' ),
        'cover' => __( 'Cover', 'i-craft' ),
        'contain' => __( 'Contain', 'i-craft' ),
        'background-size' => __( 'Background Size', 'i-craft' ),
        'fixed' => __( 'Fixed', 'i-craft' ),
        'scroll' => __( 'Scroll', 'i-craft' ),
        'background-attachment' => __( 'Background Attachment', 'i-craft' ),
        'left-top' => __( 'Left Top', 'i-craft' ),
        'left-center' => __( 'Left Center', 'i-craft' ),
        'left-bottom' => __( 'Left Bottom', 'i-craft' ),
        'right-top' => __( 'Right Top', 'i-craft' ),
        'right-center' => __( 'Right Center', 'i-craft' ),
        'right-bottom' => __( 'Right Bottom', 'i-craft' ),
        'center-top' => __( 'Center Top', 'i-craft' ),
        'center-center' => __( 'Center Center', 'i-craft' ),
        'center-bottom' => __( 'Center Bottom', 'i-craft' ),
        'background-position' => __( 'Background Position', 'i-craft' ),
        'background-opacity' => __( 'Background Opacity', 'i-craft' ),
        'ON' => __( 'ON', 'i-craft' ),
        'OFF' => __( 'OFF', 'i-craft' ),
        'all' => __( 'All', 'i-craft' ),
        'cyrillic' => __( 'Cyrillic', 'i-craft' ),
        'cyrillic-ext' => __( 'Cyrillic Extended', 'i-craft' ),
        'devanagari' => __( 'Devanagari', 'i-craft' ),
        'greek' => __( 'Greek', 'i-craft' ),
        'greek-ext' => __( 'Greek Extended', 'i-craft' ),
        'khmer' => __( 'Khmer', 'i-craft' ),
        'latin' => __( 'Latin', 'i-craft' ),
        'latin-ext' => __( 'Latin Extended', 'i-craft' ),
        'vietnamese' => __( 'Vietnamese', 'i-craft' ),
        'serif' => _x( 'Serif', 'font style', 'i-craft' ),
        'sans-serif' => _x( 'Sans Serif', 'font style', 'i-craft' ),
        'monospace' => _x( 'Monospace', 'font style', 'i-craft' ),
    );	

	$args = array(
  
        // Change the logo image. (URL) Point this to the path of the logo file in your theme directory
                // The developer recommends an image size of about 250 x 250
        'logo_image'   => get_template_directory_uri() . '/images/logo.png',
  
        // The color of active menu items, help bullets etc.
        'color_active' => '#95c837',
		
		// Color used on slider controls and image selects
		'color_accent' => '#e7e7e7',
		
		// The generic background color
		//'color_back' => '#f7f7f7',
	
        // Color used for secondary elements and desable/inactive controls
        'color_light'  => '#e7e7e7',
  
        // Color used for button-set controls and other elements
        'color_select' => '#34495e',
		 
        
        // For the parameter here, use the handle of your stylesheet you use in wp_enqueue
        'stylesheet_id' => 'customize-styles', 
		
        // Only use this if you are bundling the plugin with your theme (see above)
        'url_path'     => get_template_directory_uri() . '/inc/kirki/',

        'textdomain'   => 'i-craft',
		
        'i18n'         => $strings,		
		
		
	);
	
	
	return $args;
}
add_filter( 'kirki/config', 'icraft_customizer_config' );


/**
 * Create the customizer panels and sections
 */
add_action( 'customize_register', 'icraft_add_panels_and_sections' ); 
function icraft_add_panels_and_sections( $wp_customize ) {
	
	/*
	* Add panels
	*/
	
	$wp_customize->add_panel( 'slider', array(
		'priority'    => 140,
		'title'       => __( 'Slider', 'i-craft' ),
		'description' => __( 'Slides details', 'i-craft' ),
	) );	

    /**
     * Add Sections
     */
    $wp_customize->add_section('basic', array(
        'title'    => __('Basic Settings', 'i-craft'),
        'description' => '',
        'priority' => 130,
    ));
	
    $wp_customize->add_section('layout', array(
        'title'    => __('Layout Options', 'i-craft'),
        'description' => '',
        'priority' => 130,
    ));	
	
    $wp_customize->add_section('social', array(
        'title'    => __('Social Links', 'i-craft'),
        'description' => __('Insert full URL of your social link including http:// replacing #', 'i-craft'),
        'priority' => 130,
    ));		
	
    $wp_customize->add_section('blogpage', array(
        'title'    => __('Default Blog Page', 'i-craft'),
        'description' => '',
        'priority' => 150,
    ));	
	
	// slider sections
	
	$wp_customize->add_section('slidersettings', array(
        'title'    => __('Slide Settings', 'i-craft'),
        'description' => '',
        'panel' => 'slider',		
        'priority' => 140,
    ));		
	
	$wp_customize->add_section('slide1', array(
        'title'    => __('Slide 1', 'i-craft'),
        'description' => '',
        'panel' => 'slider',		
        'priority' => 140,
    ));	
	$wp_customize->add_section('slide2', array(
        'title'    => __('Slide 2', 'i-craft'),
        'description' => '',
        'panel' => 'slider',		
        'priority' => 140,
    ));	
	$wp_customize->add_section('slide3', array(
        'title'    => __('Slide 3', 'i-craft'),
        'description' => '',
        'panel' => 'slider',		
        'priority' => 140,
    ));	
	$wp_customize->add_section('slide4', array(
        'title'    => __('Slide 4', 'i-craft'),
        'description' => '',
        'panel' => 'slider',		
        'priority' => 140,
    ));	
	
	// WooCommerce Settings
    $wp_customize->add_section('woocomm', array(
        'title'    => __('WooCommerce', 'i-craft'),
        'description' => '',
        'priority' => 150,
    ));		
	
	// promo sections
	
	$wp_customize->add_section('nxpromo', array(
        'title'    => __('More About i-craft', 'i-craft'),
        'description' => '',
        'priority' => 170,
    ));				
	
}


function icraft_custom_setting( $controls ) {
	
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'top_phone',
        'label'    => __( 'Phone Number', 'i-craft' ),
        'section'  => 'basic',
        'default'  => of_get_option('top_bar_phone', '1-000-123-4567'),		
        'priority' => 1,
		'description' => __( 'Phone number that appears on top bar.', 'i-craft' ),
    );
	
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'top_email',
        'label'    => __( 'Email Address', 'i-craft' ),
        'section'  => 'basic',
        'default'  => sanitize_email(of_get_option('top_bar_email', 'email@i-create.com')),
        'priority' => 1,
		'description' => __( 'Email Id that appears on top bar.', 'i-craft' ),		
    );
	
	$controls[] = array(
		'type'        => 'upload',
		'setting'     => 'logo',
		'label'       => __( 'Site header logo', 'i-craft' ),
		'description' => __( 'Width 280px, height 72px max. Upload logo for header', 'i-craft' ),
        'section'  => 'basic',
        'default'  => of_get_option('itrans_logo_image', get_template_directory_uri() . '/images/logo.png'),		
		'priority'    => 1,
	);	
	
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'banner_text',
        'label'    => __( 'Banner Text', 'i-craft' ),
        'section'  => 'basic',
        'default'  => of_get_option('itrans_slogan', 'Banner Text Here'),
        'priority' => 1,
		'description' => __( 'if you are using a logo and want your site title or slogan to appear on the header banner', 'i-craft' ),		
    );	
	
	$controls[] = array(
		'type'        => 'color',
		'setting'     => 'primary_color',
		'label'       => __( 'Primary Color', 'i-craft' ),
		'description' => __( 'Choose your theme color', 'i-craft' ),
		'section'     => 'layout',
		'default'     => of_get_option('itrans_primary_color', '#dd3333'),
		'priority'    => 1,
	);	
	
	$controls[] = array(
		'type'        => 'radio-image',
		'setting'     => 'blog_layout',
		'label'       => __( 'Blog Posts Layout', 'i-craft' ),
		'description' => __( '(Choose blog posts layout (one column/two column)', 'i-craft' ),
		'section'     => 'layout',
		'default'     => of_get_option('itrans_blog_layout', 'onecol'),
		'priority'    => 2,
		'choices'     => array(
			'onecol' => get_template_directory_uri() . '/images/onecol.png',
			'twocol' => get_template_directory_uri() . '/images/twocol.png',
		),
	);
	
	$controls[] = array(
		'type'        => 'switch',
		'setting'     => 'full_content',
		'label'       => __( 'Show Full Content', 'i-craft' ),
		'description' => __( 'Show full content on blog pages', 'i-craft' ),
		'section'     => 'layout',
		'default'     => of_get_option('full_content', 0),		
		'priority'    => 3,
	);		
	
	$controls[] = array(
		'type'        => 'switch',
		'setting'     => 'wide_layout',
		'label'       => __( 'Wide layout', 'i-craft' ),
		'description' => __( 'Check to have wide layou', 'i-craft' ),
		'section'     => 'layout',
		'default'     => of_get_option('boxed_type', 1),			
		'priority'    => 4,
	);
	
	$controls[] = array(
		'type'        => 'switch',
		'setting'     => 'sidebar_side',
		'label'       => __( 'Main Sidebar on left (default sidebar appears on right)', 'i-craft' ),
		'description' => __( 'move the main sidebar position to left', 'i-craft' ),
		'section'     => 'layout',
		'default'     => of_get_option('sidebar_side', 0),			
		'priority'    => 4,
	);	
	
	$controls[] = array(
		'type'        => 'textarea',
		'setting'     => 'itrans_extra_style',
		'label'       => __( 'Additional style', 'i-craft' ),
		'description' => __( 'add extra style(CSS) codes here', 'i-craft' ),
		'section'     => 'layout',
		'default'     => '',
		'default'     => of_get_option('itrans_extra_style', ''),		
		'priority'    => 10,
	);	
	
	/*
	$controls[] = array(
		'type'        => 'color',
		'setting'     => 'site_bg_color',
		'label'       => __( 'Background Color (Boxed Layout)', 'i-craft' ),
		'description' => __( 'Choose your background color', 'i-craft' ),
		'section'     => 'layout',
		'default'     => '#FFFFFF',
		'priority'    => 1,
	);
	*/	
	

	
	// social links
	
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_social_facebook',
        'label'    => __( 'Facebook', 'i-craft' ),
		'description' => __( 'Empty the field to remove the icon', 'i-craft' ),		
        'section'  => 'social',
		'default'  => of_get_option('itrans_social_facebook', '#'),		
        'priority' => 1,
    );	
	
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_social_twitter',
        'label'    => __( 'Twitter', 'i-craft' ),
		'description' => __( 'Empty the field to remove the icon', 'i-craft' ),			
        'section'  => 'social',
		'default'  => of_get_option('itrans_social_twitter', '#'),	
        'priority' => 1,
    );
	
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_social_flickr',
        'label'    => __( 'Flickr', 'i-craft' ),
		'description' => __( 'Empty the field to remove the icon', 'i-craft' ),			
        'section'  => 'social',
		'default'  => of_get_option('itrans_social_flickr', '#'),	
        'priority' => 1,
    );	
	
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_social_feed',
        'label'    => __( 'RSS', 'i-craft' ),
		'description' => __( 'Empty the field to remove the icon', 'i-craft' ),			
        'section'  => 'social',
		'default'  => of_get_option('itrans_social_feed', '#'),	
        'priority' => 1,
    );	
	
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_social_instagram',
        'label'    => __( 'Instagram', 'i-craft' ),
		'description' => __( 'Empty the field to remove the icon', 'i-craft' ),			
        'section'  => 'social',
		'default'  => of_get_option('itrans_social_instagram', '#'),	
        'priority' => 1,
    );	
	
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_social_googleplus',
        'label'    => __( 'Google Plus', 'i-craft' ),
		'description' => __( 'Empty the field to remove the icon', 'i-craft' ),			
        'section'  => 'social',
		'default'  => of_get_option('itrans_social_googleplus', '#'),	
        'priority' => 1,
    );	
	
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_social_youtube',
        'label'    => __( 'YouTube', 'i-craft' ),
		'description' => __( 'Empty the field to remove the icon', 'i-craft' ),			
        'section'  => 'social',
		'default'  => of_get_option('itrans_social_youtube', '#'),	
        'priority' => 1,
    );	
	
	// Slider

	$controls[] = array(
		'type'        => 'slider',
		'setting'     => 'itrans_sliderspeed',
		'label'       => __( 'Slide Duration', 'i-craft' ),
		'description' => __( 'Slide visibility in second', 'i-craft' ),
		'section'     => 'slidersettings',
		'default'     => 6,
		'priority'    => 1,
		'choices'     => array(
			'min'  => 1,
			'max'  => 30,
			'step' => 1
		),
	);
	
	
	// Slide1
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_slide1_title',
        'label'    => __( 'Slide1 Title', 'i-craft' ),
        'section'  => 'slide1',
		'default'  => of_get_option('itrans_slide1_title', 'i-craft, Exclusive WooCommerce Features'),			
        'priority' => 1,
    );
	$controls[] = array(
		'type'        => 'textarea',
		'setting'     => 'itrans_slide1_desc',
		'label'       => __( 'Slide1 Description', 'i-craft' ),
		'section'     => 'slide1',
		'default'  => of_get_option('itrans_slide1_desc', 'To start setting up i-craft go to Appearance &gt; Customize. Make sure you have installed recommended plugin &rdquo;TemplatesNext Toolkit&rdquo; by going appearance &gt; install plugin.'),			
		'priority'    => 10,
	);
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_slide1_linktext',
        'label'    => __( 'Slide1 Link text', 'i-craft' ),
        'section'  => 'slide1',
		'default'  => of_get_option('itrans_slide1_linktext', 'Know More'),		
        'priority' => 1,
    );
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_slide1_linkurl',
        'label'    => __( 'Slide1 Link URL', 'i-craft' ),
        'section'  => 'slide1',
		'default'  => of_get_option('itrans_slide1_linkurl', 'http://templatesnext.org/icraft/'),		
        'priority' => 1,
    );
	$controls[] = array(
		'type'        => 'upload',
		'setting'     => 'itrans_slide1_image',
		'label'       => __( 'Slide1 Image', 'i-craft' ),
        'section'  	  => 'slide1',
		'default'  => of_get_option('itrans_slide1_image', get_template_directory_uri() . '/images/slide1.jpg'),
		//'default'  => of_get_option('itrans_slide1_image'),			
		'priority'    => 1,
	);							
	
	
	// Slide2
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_slide2_title',
        'label'    => __( 'Slide2 Title', 'i-craft' ),
        'section'  => 'slide2',
		'default'  => of_get_option('itrans_slide2_title', 'Live Edit With Customizer'),		
        'priority' => 1,
    );
	$controls[] = array(
		'type'        => 'textarea',
		'setting'     => 'itrans_slide2_desc',
		'label'       => __( 'Slide2 Description', 'i-craft' ),
		'section'     => 'slide2',
		'default'  => of_get_option('itrans_slide2_desc', 'Setup your theme from Appearance &gt; Customize , boxed/wide layout, unlimited color, custom background, blog layout, social links, additiona css styling, phone number and email id, etc.'),		
		'priority'    => 10,
	);
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_slide2_linktext',
        'label'    => __( 'Slide2 Link text', 'i-craft' ),
        'section'  => 'slide2',
		'default'  => of_get_option('itrans_slide2_linktext', 'Know More'),		
        'priority' => 1,
    );
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_slide2_linkurl',
        'label'    => __( 'Slide2 Link URL', 'i-craft' ),
        'section'  => 'slide2',
		'default'  => of_get_option('itrans_slide2_linkurl', 'https://wordpress.org/'),		
        'priority' => 1,
    );
	$controls[] = array(
		'type'        => 'upload',
		'setting'     => 'itrans_slide2_image',
		'label'       => __( 'Slide2 Image', 'i-craft' ),
        'section'  	  => 'slide2',
		'default'  => of_get_option('itrans_slide2_image', get_template_directory_uri() . '/images/slide2.jpg'),
		//'default'  => of_get_option('itrans_slide2_image'),					
		'priority'    => 1,
	);							
		
		
	// Slide3
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_slide3_title',
        'label'    => __( 'Slide3 Title', 'i-craft' ),
        'section'  => 'slide3',
		'default'  => of_get_option('itrans_slide3_title', 'Portfolio, Testimonial, Services...'),		
        'priority' => 1,
    );
	$controls[] = array(
		'type'        => 'textarea',
		'setting'     => 'itrans_slide3_desc',
		'label'       => __( 'Slide3 Description', 'i-craft' ),
		'section'     => 'slide3',
		'default'  => of_get_option('itrans_slide3_desc', 'Once you install and activate the plugin &rdquo; TemplatesNext Toolkit &rdquo; Use the [tx] button on your editor to create the columns, services, portfolios, testimonials and custom sliders.'),		
		'priority'    => 10,
	);
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_slide3_linktext',
        'label'    => __( 'Slide3 Link text', 'i-craft' ),
        'section'  => 'slide3',
		'default'  => of_get_option('itrans_slide3_linktext', 'Know More'),			
        'priority' => 1,
    );
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_slide3_linkurl',
        'label'    => __( 'Slide3 Link URL', 'i-craft' ),
        'section'  => 'slide3',
		'default'  => of_get_option('itrans_slide3_linkurl', 'https://wordpress.org/'),		
        'priority' => 1,
    );
	$controls[] = array(
		'type'        => 'upload',
		'setting'     => 'itrans_slide3_image',
		'label'       => __( 'Slide3 Image', 'i-craft' ),
        'section'  	  => 'slide3',
		'default'  => of_get_option('itrans_slide3_image', get_template_directory_uri() . '/images/slide3.jpg'),
		//'default'  => of_get_option('itrans_slide3_image'),					
		'priority'    => 1,
	);							
	
	
	// Slide2
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_slide4_title',
        'label'    => __( 'Slide4 Title', 'i-craft' ),
        'section'  => 'slide4',
		'default'  => of_get_option('itrans_slide4_title', 'Customize Your pages'),		
        'priority' => 1,
    );
	$controls[] = array(
		'type'        => 'textarea',
		'setting'     => 'itrans_slide4_desc',
		'label'       => __( 'Slide4 Description', 'i-craft' ),
		'section'     => 'slide4',
		'default'  => of_get_option('itrans_slide4_desc', 'Customize your pages with page options (meta). Use default theme slider or itrans slider or any 3rd party slider on any page'),		
		'priority'    => 10,
	);
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_slide4_linktext',
        'label'    => __( 'Slide4 Link text', 'i-craft' ),
        'section'  => 'slide4',
		'default'  => of_get_option('itrans_slide4_linktext', 'Know More'),		
        'priority' => 1,
    );
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'itrans_slide4_linkurl',
        'label'    => __( 'Slide4 Link URL', 'i-craft' ),
        'section'  => 'slide4',
		'default'  => of_get_option('itrans_slide4_linkurl', 'https://wordpress.org/'),		
        'priority' => 1,
    );
	$controls[] = array(
		'type'        => 'upload',
		'setting'     => 'itrans_slide4_image',
		'label'       => __( 'Slide4 Image', 'i-craft' ),
        'section'  	  => 'slide4',
		'default'  => of_get_option('itrans_slide4_image', get_template_directory_uri() . '/images/slide4.jpg'),
		//'default'  => of_get_option('itrans_slide4_image'),			
		'priority'    => 1,
	);
	
	// Blog page setting
	
	$controls[] = array(
		'type'        => 'switch',
		'setting'     => 'slider_stat',
		'label'       => __( 'Hide i-craft Slider', 'i-craft' ),
		'description' => __( 'Turn Off or On to hide/show default i-craft slider', 'i-craft' ),
		'section'     => 'blogpage',
		'default'  => of_get_option('hide_front_slider', ''),		
		'priority'    => 1,
	);
	
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'other_front_slider',
        'label'    => __( 'Other Slider Shortcode', 'i-craft' ),
        'section'  => 'blogpage',
		'default'  => of_get_option('other_front_slider', ''),		
        'priority' => 1,
		'description' => __( 'Enter a 3rd party slider shortcode, ex. meta slider, smart slider 2, wow slider, etc.', 'i-craft' ),		
    );	
	
	// WooCommerce Settings
	
	// Blog page setting
	
	$controls[] = array(
		'type'        => 'switch',
		'setting'     => 'hide_login',
		'label'       => __( 'Hide Topnav Login', 'i-craft' ),
		'description' => __( 'Hide login menu item from top nav', 'i-craft' ),
		'section'     => 'woocomm',
		'default'  => of_get_option('hide_login', ''),		
		'priority'    => 1,
	);
	
	$controls[] = array(
		'type'        => 'switch',
		'setting'     => 'hide_cart',
		'label'       => __( 'Hide Topnav Cart', 'i-craft' ),
		'description' => __( 'Hide cart from top nav', 'i-craft' ),
		'section'     => 'woocomm',
		'default'  => of_get_option('hide_cart', ''),		
		'priority'    => 1,
	);
	
	$controls[] = array(
		'type'        => 'switch',
		'setting'     => 'normal_search',
		'label'       => __( 'Turn On Normal Search', 'i-craft' ),
		'description' => __( 'Product only search will be turned off.', 'i-craft' ),
		'section'     => 'woocomm',
		'default'  => of_get_option('normal_search', ''),		
		'priority'    => 1,
	);			
	
	/*
    $controls[] = array(
        'type'     => 'text',
        'setting'  => 'blogslide_scode',
        'label'    => __( 'Other Slider Shortcode', 'i-craft' ),
        'section'  => 'blogpage',
        'default'  => '',
		'description' => __( 'Enter a 3rd party slider shortcode, ex. meta slider, smart slider 2, wow slider, etc.', 'i-craft' ),
        'priority' => 2,
    );
	

	
	
	// Off
	$controls[] = array(
		'type'        => 'toggle',
		'setting'     => 'toggle_demo',
		'label'       => __( 'This is the label', 'i-craft' ),
		'description' => __( 'This is the control description', 'i-craft' ),
		'section'     => 'blogpage',
		'default'     => 1,
		'priority'    => 10,
	);	
	
	*/
	// promos
	$controls[] = array(
		'type'        => 'custom',
		'settings'    => 'custom_demo',
		'label' => __( 'TemplatesNext Promo', 'i-craft' ),
		'section'     => 'nxpromo',
		'default'	  => '<div class="promo-box">
        <div class="promo-2">
        	<div class="promo-wrap">
            	<a href="http://templatesnext.org/icraft/" target="_blank">i-craft Demo</a>
                <a href="https://www.facebook.com/templatesnext" target="_blank">Facebook</a> 
                <a href="http://templatesnext.org/ispirit/landing/forums/" target="_blank">Support</a>                                 
                <!-- <a href="http://templatesnext.org/icraft/docs">Documentation</a> -->
                <a href="http://templatesnext.org/ispirit/landing/" target="_blank">Go Premium</a>                
                <div class="donate">                
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="M2HN47K2MQHAN">
                    <table>
                    <tr><td><input type="hidden" name="on0" value="If you like my work, you can buy me">If you like my work, you can buy me</td></tr><tr><td><select name="os0">
                        <option value="a cup of coffee">1 cup of coffee $10.00 USD</option>
                        <option value="2 cup of coffee">2 cup of coffee $20.00 USD</option>
                        <option value="3 cup of coffee">3 cup of coffee $30.00 USD</option>
                    </select></td></tr>
                    </table>
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
                    </form>
                </div>                                                                          
            </div>
        </div>
		</div>',
		'priority' => 10,
	);	
	
    return $controls;
}
add_filter( 'kirki/controls', 'icraft_custom_setting' );







