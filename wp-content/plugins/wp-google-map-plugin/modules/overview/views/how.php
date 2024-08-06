<?php
/**
 * Plugin Overviews.
 * @package Maps
 * @author Flipper Code <flippercode>
 **/

?>
<?php 
$form  = new WPGMP_Template();
echo wp_kses_post( $form->show_header() );

$allowed_html = array( 'a' => array( 'href' => array(), 'class' => array(), 'target' => array() ) );

?>

<div class="flippercode-ui">
<div class="fc-main">
<div class="fc-container">
 <div class="fc-divider">

 <div class="fc-back fc-docs ">
 <div class="fc-12">
            <h4 class="fc-title-blue"><?php esc_html_e('How to Create your First Map?','wp-google-map-plugin');?>  </h4>
              <div class="wpgmp-overview">
                <p>
                <?php 

                $install_sample_data_url = '<a target="_blank" href="' . esc_url( admin_url( 'admin.php?page=wpgmp_manage_tools' ) ) . '">' . esc_html__( 'installing our Sample Data', 'wp-google-map-plugin' ) . '</a>';
                
                $install_sample_instruction = sprintf( 
                    esc_html__( "Get rolling fast by %s. It sets up everything for you - just add your Google Maps API key and you are good to go!", 'wp-google-map-plugin' ), 
                    wp_kses( $install_sample_data_url, $allowed_html ) 
                );
                echo wp_kses_post( $install_sample_instruction );

                ?>
               </p>
                <ol>

                    <li><?php 

                    $api_key_link = '<a href="' . esc_url( 'https://www.wpmapspro.com/docs/how-to-create-an-api-key/' ) . '" class="wpgmp_map_key_missing" target="_blank">' . esc_html__( 'Google Map API Key', 'wp-google-map-plugin' ) . '</a>';
                    $plugin_setting = '<a href="' . esc_url( admin_url( 'admin.php?page=wpgmp_manage_settings' ) ) . '" target="_blank">' . esc_html__( 'Settings', 'wp-google-map-plugin' ) . '</a>';

                    

                    $output_string = sprintf(
                        esc_html__( 'First create a %s. Then go to %s page and insert your Google Maps API Key and save.', 'wp-google-map-plugin' ),
                        wp_kses( $api_key_link, $allowed_html ),
                        wp_kses( $plugin_setting, $allowed_html )
                    );

                    echo wp_kses_post( $output_string );

                    ?>
                        
                    </li>
                    
                    <li><?php

                    $add_location = '<a href="' . esc_url( admin_url( 'admin.php?page=wpgmp_form_location' ) ) . '" target="_blank">' . esc_html__( 'Add Location', 'wp-google-map-plugin' ) . '</a>';

                    $output_string = sprintf(
                        esc_html__( 'Create a location by using %s page.', 'wp-google-map-plugin' ),
                        wp_kses( $add_location, $allowed_html )
                    );

                    echo wp_kses_post( $output_string );
            
                     ?>
                    </li>
                    
                    <li>

                    <?php 

                    $addmap = '<a href="' . esc_url( admin_url( 'admin.php?page=wpgmp_form_map' ) ) . '" target="_blank">' . esc_html__( 'Add Map', 'wp-google-map-plugin' ) . '</a>';

                    $output_string = sprintf(
                        esc_html__( 'Go to %s page, assign locations to the map and setup other details as per your requirements. Save the map.', 'wp-google-map-plugin' ),
                        wp_kses( $addmap, $allowed_html )
                    );

                    echo wp_kses_post( $output_string );

                     ?>
                    </li>
                                                                                
                </ol>
            </div>
            
            <h4 class="fc-title-blue"><?php esc_html_e('How to Display Map in Frontend?','wp-google-map-plugin'); ?>  </h4>
              <div class="wpgmp-overview">
                        
                    <p>
                        <?php 

                        $manage_map = '<a href="' . esc_url( admin_url( 'admin.php?page=wpgmp_manage_map' ) ) . '" target="_blank">' . esc_html__( 'Manage Map', 'wp-google-map-plugin' ) . '</a>';

                        $output_string = sprintf(
                            esc_html__( 'Go to %s and copy the shortcode then paste it to any page/post where you want to display map.', 'wp-google-map-plugin' ),
                            wp_kses( $manage_map, $allowed_html )
                        );

                        echo wp_kses_post( $output_string );
                        
                        ?>

                     
                    </p>
                    
              </div>
        <h4 class="fc-title-blue"><?php esc_html_e('How to Create Marker Category?','wp-google-map-plugin'); ?>  </h4>
                <div class="wpgmp-overview">
                        
                    <p>
                        <?php 
                            $add_marker_Category = '<a href="' . esc_url( admin_url( 'admin.php?page=wpgmp_form_group_map' ) ) . '" target="_blank">' . esc_html__( 'Add Marker Category', 'wp-google-map-plugin' ) . '</a>';

                            $output_string = sprintf(
                                esc_html__( 'Creating marker categories & assigning those to locations helps grouping the markers on the map by their icon image. Markers having the same marker category will display the same marker icon. Go to %s, specify marker category title and assign a marker icon. Once you have created some marker categories, these categories can be assigned to the location on "Add Locations" page.', 'wp-google-map-plugin' ),
                                wp_kses( $add_marker_Category, $allowed_html )
                            );

                            echo wp_kses_post( $output_string );
                        ?>

                   </p>
                </div> 


        <h4 class="fc-title-blue"> <?php esc_html_e('Google Map API Troubleshooting','wp-google-map-plugin'); ?>  </h4>
        <div class="wpgmp-overview">
        <p> <?php esc_html_e('If your google maps is not working. Make sure you have checked following things.','wp-google-map-plugin'); ?></p>
        <ul>
        <li> <?php esc_html_e('1. Make sure you have assigned locations to your map.','wp-google-map-plugin');?></li>
        <li> <?php esc_html_e('2. You must have google maps api key.','wp-google-map-plugin');?></li>
        <li> <?php esc_html_e('3. Check HTTP referrers. It must be https://yourwebsiteurl.com or *yourwebsiteurl.com/*','wp-google-map-plugin');?> 
        </li>
        </ul>
        
        <?php $referrer_image = WPGMP_IMAGES.'referrer.png';  ?>
        <p><img src="<?php echo esc_url($referrer_image); ?>"> </p>

        <p>
            <?php
           
            $support_ticket = '<a href="' . esc_url( 'https://www.wpmapspro.com/support/' ) . '" target="_blank">' . esc_html__( 'support ticket', 'wp-google-map-plugin' ) . '</a>';

            $output_string = sprintf(
                esc_html__( "If you need any assistance or if you still see any issue, feel free to create a %s and we'd be happy to help you asap.", 'wp-google-map-plugin' ),
                wp_kses( $support_ticket, $allowed_html )
            );

            echo wp_kses_post( $output_string );

            echo '<br><br>';    
          
            $premium_plugin = '<a target="_blank" href="' . esc_url( 'https://www.wpmapspro.com/?utm_source=wordpress&utm_medium=liteversion&utm_campaign=freemium&utm_id=freemium' ) . '">' . esc_html__( 'WP Maps Pro', 'wp-google-map-plugin' ) . '</a>';

            $output_string = sprintf(
                esc_html__( "If you are looking for even more features, please have a look on the pro version: %s. It's the number #1 selling (13k+ happy customers), most loved & trusted advanced Google Maps plugin for WordPress. We are continuously adding more features to it based on the suggestions of esteemed customers / users like you. With the pro version, you can set up Google Maps with very advanced features in just a few seconds. Also, both our free and pro version plugins can be customized to achieve specific requirements.", 'wp-google-map-plugin' ),
                wp_kses( $premium_plugin, $allowed_html )
            );

            echo wp_kses_post( $output_string );
            ?>
        </p>

        </div>          
    </div>
</div></div>
</div>
</div></div>
