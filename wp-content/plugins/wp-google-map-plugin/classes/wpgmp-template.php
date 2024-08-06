<?php
/**
 * Template class
 * @author Flipper Code<hello@flippercode.com>
 * @version 4.1.6
 * @package Maps
 */

if ( ! class_exists( 'WPGMP_Template' ) ) {

	/**
	 * Controller class to display views.
	 * @author: Flipper Code<hello@flippercode.com>
	 * @version: 4.1.6
	 * @package: Maps
	 */

	class WPGMP_Template extends FlipperCode_HTML_Markup{


		function __construct($options = array()) {
			
			$productOverview = array(
				'subscribe_mailing_list' => esc_html__( 'Subscribe to our mailing list', 'wp-google-map-plugin' ),
				'product_info_heading' => esc_html__( 'Product Information', 'wp-google-map-plugin' ),
				'product_info_desc' => esc_html__( 'For each of our plugins, we have created step by step detailed tutorials that helps you to get started quickly.', 'wp-google-map-plugin' ),
				'start_now' => esc_html__( 'START NOW', 'wp-google-map-plugin' ),
				'installed_version' => esc_html__( 'Installed version :', 'wp-google-map-plugin' ),
				'latest_version_available' => esc_html__( 'Latest Version Available : ', 'wp-google-map-plugin' ),
				'updates_available' => esc_html__( 'Update Available', 'wp-google-map-plugin' ),

				'subscribe_now' => array(
					'heading' => esc_html__( 'Subscribe Now', 'wp-google-map-plugin' ),
					'desc1' => esc_html__( 'Receive updates on our new product features and new products effortlessly.', 'wp-google-map-plugin' ),
					'desc2' => esc_html__( 'We will not share your email addresses in any case.', 'wp-google-map-plugin' ),
				),

				'product_support' => array(
					'heading' => esc_html__( 'Product Support', 'wp-google-map-plugin' ),
					'desc' => esc_html__( 'For our each product we have very well explained starting guide to get you started in matter of minutes.', 'wp-google-map-plugin' ),
					'click_here' => esc_html__( ' Click Here', 'wp-google-map-plugin' ),
					'desc2' => esc_html__( 'For our each product we have set up demo pages where you can see the plugin in working mode. You can see a working demo before making a purchase.', 'wp-google-map-plugin' ),
				),
				'create_support_ticket' => array(
                    'heading' => esc_html__( 'Create Support Ticket', 'wp-google-map-plugin' ),
                    'desc1' => esc_html__( 'If you have any question and need our help, click below button to create a support ticket and our support team will assist you asap.', 'wp-google-map-plugin' ),
                    'link' => array( 
						'label' => esc_html__( 'Create Ticket', 'wp-google-map-plugin' ),
						'url' => 'https://weplugins.com/support/'
					)
                ),

                'hire_wp_expert' => array(
                    'heading' => esc_html__( 'Hire Wordpress Expert', 'wp-google-map-plugin' ),
                    'desc' => esc_html__( 'Do you have a custom requirement which is missing in this plugin?', 'wp-google-map-plugin' ),
                    'desc1' => esc_html__( 'We can customize this plugin according to your needs. Click below button to send an quotation request.', 'wp-google-map-plugin' ),
                    'link' => array(
                                    
                        'label' => esc_html__( 'Request a quotation', 'wp-google-map-plugin' ),
                        'url' => 'https://weplugins.com/contact/'
					)
                ),
			);  
						
			$productInfo = array('productName' => esc_html__('WP MAPS','wp-google-map-plugin'), 
                        'productSlug' => 'wp-google-map-plugin',
                        'product_tag_line' => esc_html__('World\'s most advanced google map plugin','wp-google-map-plugin'),
                        'productTextDomain' => 'wp-google-map-plugin',
                        'productVersion' => WPGMP_VERSION,
                        'videoURL' => 'https://www.youtube.com/playlist?list=PLlCp-8jiD3p2PYJI1QCIvjhYALuRGBJ2A',
                        'docURL' => 'https://www.wpmapspro.com/tutorials/',
                        'demoURL' => 'https://www.wpmapspro.com/?utm_source=wordpress&utm_medium=liteversion&utm_campaign=freemium&utm_id=freemium',
                        'start_now_url' => admin_url('admin.php?page=wpgmp_how_overview'),
                        'productSaleURL' => 'https://www.wpmapspro.com/?utm_source=wordpress&utm_medium=liteversion&utm_campaign=freemium&utm_id=freemium',
                        'multisiteLicence' => 'https://www.wpmapspro.com/?utm_source=wordpress&utm_medium=liteversion&utm_campaign=freemium&utm_id=freemium',
                        'productOverview' => $productOverview,
   			 );
			$productInfo = array_merge($productInfo, $options);
			parent::__construct($productInfo);

		}

	}
	
}
