<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 2.0
 */

namespace radiustheme\ClassiList;

class TGM_Config {
	
	public $prefix;
	public $path;

	public function __construct() {
		$this->prefix = Constants::$theme_prefix;
		$this->path   = Constants::$theme_plugins_dir;

		add_action( 'tgmpa_register', array( $this, 'register_required_plugins' ) );
	}

	public function register_required_plugins(){
		$plugins = array(
			// Bundled
			array(
				'name'         => 'ClassiList Core',
				'slug'         => 'classilist-core',
				'source'       => 'classilist-core.1.15.zip',
				'required'     =>  true,
				'version'      => '1.15'
			),
			array(
				'name'         => 'RT Framework',
				'slug'         => 'rt-framework',
				'source'       => 'rt-framework.zip',
				'required'     =>  true,
				'version'      => '2.9.1'
			),
			array(
				'name'         => 'Classified Listing Pro',
				'slug'         => 'classified-listing-pro',
				'source'       => 'classified-listing-pro.3.2.0.zip',
				'required'     =>  true,
				'version'      => '3.2.0'
			),
			array(
				'name'         => 'Classified Listing Store',
				'slug'         => 'classified-listing-store',
				'source'       => 'classified-listing-store.2.1.0.zip',
				'required'     =>  false,
				'version'      => '2.1.0'
			),
            array(
                'name'         => 'Review Schema Pro',
                'slug'         => 'review-schema-pro',
                'source'       => 'review-schema-pro.1.1.8.zip',
                'required'     =>  false,
                'version'      => '1.1.8'
            ),
            
			// Repository
            array(
                'name'     => 'Classified Listing',
                'slug'     => 'classified-listing',
                'required' => true,
            ),
			array(
				'name'     => 'Classified Listing Toolkits',
				'slug'     => 'classified-listing-toolkits',
				'required' => true,
			),
			array(
				'name'     => 'Redux Framework',
				'slug'     => 'redux-framework',
				'required' => true,
			),
            array(
                'name'     => 'Review Schema',
                'slug'     => 'review-schema',
                'required' => false,
            ),
			array(
				'name'     => 'Elementor Page Builder',
				'slug'     => 'elementor',
				'required' => true,
			),
			array(
				'name'     => 'Contact Form 7',
				'slug'     => 'contact-form-7',
				'required' => false,
			),
			array(
				'name'      => esc_html__('One Click Demo Import','cldoctor'),
				'slug'      => 'one-click-demo-import',
				'required'  => false,
			),
		);

		$config = array(
			'id'           => $this->prefix,            // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => $this->path,              // Default absolute path to bundled plugins.
			'menu'         => $this->prefix . '-install-plugins', // Menu slug.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => true,                    // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
		);

		tgmpa( $plugins, $config );
	}
}

new TGM_Config;