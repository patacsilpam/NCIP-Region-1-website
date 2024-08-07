<?php
/*
  Plugin Name: Easy Social Like Box - Popup - Sidebar Widget
  Plugin URI: https://johnn.info/
  Description: Facebook Like Box enables you to display the facebook page likes in your website.
  Version: 4.5
  Author: Vinoj Cardoza
  Author URI: https://johnn.info/
  License: GPL2
 */

 
add_action('admin_init', 'cfblb_redirection');
add_action('admin_enqueue_scripts','cfblb_enq_scripts');
add_action('wp_enqueue_scripts', 'cfblb_enq_scripts');
add_action("plugins_loaded", "cardoza_fb_like_init");
add_action("admin_menu", "cardoza_fb_like_options");
add_action("wp_footer", "cardoza_fb_like_popup");
add_shortcode("cardoza_facebook_like_box", "cardoza_facebook_like_box_sc");
add_shortcode("cardoza_facebook_posts_like", "cardoza_facebook_posts_like_sc");
add_action( 'admin_enqueue_scripts', 'cardoza_facebook_posts_scripts' );
add_action( 'login_enqueue_scripts', 'cardoza_facebook_posts_scripts');
register_activation_hook( __FILE__, 'cfblb_activate' );





function cfblb_activate()
{
	update_option('cfblb_stream', "false");
	update_option('cfblb_header', "true");
	update_option('cfblb_small_header', "false");
	update_option('cfblb_show_faces', "true");
	update_option('cfblb_fb_url',"https://www.facebook.com/WordPress");
	update_option('cfblb_title',"Like us on Facebook");
	update_option('cfpl_enable',"no");
	add_option('cfblb_header_do_activation_redirect', true);
	update_option('cfblb_popup_enable_disable',"");
	update_option('cfblb_popup_title',"Like us on Facebook");
	update_option('cfblb_popup_fb_url',"https://www.facebook.com/WordPress");
	update_option('cfblb_popup_width',"400");
	update_option('cfblb_popup_height',"250");
	update_option('cfblb_popup_show_faces',"true");
	update_option('cfblb_popup_stream',"false");
	update_option('cfblb_popup_header',"true");
	update_option('cfblb_popup_small_header',"false");
	update_option('cfblb_popup_repeat_times',"3");

}

function cfblb_redirection()
{
	 if (get_option('cfblb_header_do_activation_redirect', false)) {
        delete_option('cfblb_header_do_activation_redirect');
        wp_redirect(admin_url('options-general.php?page=slug_for_fb_like_box'));
		exit;
    }

}

function cardoza_facebook_posts_scripts()
{
	
	if(isset($_GET['page']))
	{
		if($_GET['page']=="slug_for_fb_like_box")
		{
			wp_enqueue_script('admin_cs_cfblbjs', plugins_url('/admin_cardozafacebook.js', __FILE__), array('jquery'));
		}
	}	

	wp_enqueue_style('admin_cfblbcss', plugins_url('/admin_cardozafacebook.css', __FILE__));
}

function cfblb_enq_scripts() {
   	 wp_enqueue_style('cfblbcss', plugins_url('/cardozafacebook.css', __FILE__));
	 wp_enqueue_script('cfblbjs', plugins_url('/cardozafacebook.js', __FILE__), array('jquery'));
	 $option_value=cfblb_retrieve_options();
	if($option_value['popup_enable_disable']=="on")
	{
		wp_enqueue_style('cfblb_popup_css', plugins_url('/cardozafacebook_popup.css', __FILE__));
	}
		
}

//The following function will retrieve all the avaialable 
//options from the wordpress database

function cfblb_retrieve_options() {
    $opt_val = array(
        'title' => get_option('cfblb_title'),
        'fb_url' => get_option('cfblb_fb_url'),
        'fb_border_color' => get_option('cfblb_fb_border_color'),
        'fb_color' => get_option('cfblb_fb_border_color'),
        'width' => get_option('cfblb_width'),
        'height' => get_option('cfblb_height'),
        'show_faces' => get_option('cfblb_show_faces'),
        'stream' => get_option('cfblb_stream'),		
		'events' => get_option('cfblb_events'),	
		'message' => get_option('cfblb_message'),
        'header' => get_option('cfblb_header'),
		'small_header' => get_option('cfblb_small_header'),
		'lang' => get_option('cfblb_lang'),
		'popup_enable_disable' => get_option('cfblb_popup_enable_disable'),
		'popup_title' => get_option('cfblb_popup_title'),
        'popup_fb_url' => get_option('cfblb_popup_fb_url'),
        'popup_width' => get_option('cfblb_popup_width'),
        'popup_height' => get_option('cfblb_popup_height'),
        'popup_show_faces' => get_option('cfblb_popup_show_faces'),
        'popup_stream' => get_option('cfblb_popup_stream'),
        'popup_header' => get_option('cfblb_popup_header'),
		'popup_small_header' => get_option('cfblb_popup_small_header'),
		'popup_lang' => get_option('cfblb_popup_lang'),
		'popup_repeat_times' => get_option('cfblb_popup_repeat_times'),	
		'popup_show_on_home' => get_option('cfblb_popup_show_on_home'),	
		'popup_show_on_logged_in' => get_option('cfblb_popup_show_on_logged_in'),		
		'popup_show_on_not_logged_in' => get_option('cfblb_popup_show_on_not_logged_in'),
		'cfpl_enable' => get_option('cfpl_enable'),
		'show_button' => get_option('cfpl_show_button'),
		'layout' =>	get_option('cfpl_layout'),
		'show_faces' =>	get_option('cfpl_show_faces'),
		'verb' => get_option('cfpl_verb'),
		
    );
	
	
	
	
    return $opt_val;
}

function cardoza_fb_like_options() {
    add_options_page(
            __('FB Like Box'), __('FB Like Box'), 'manage_options', 'slug_for_fb_like_box', 'cardoza_fb_like_options_page');
}

	
function cardoza_fb_like_options_page() {
    $cfblb_options = array(
        'cfb_title' => 'cfblb_title',
        'cfb_fb_url' => 'cfblb_fb_url',
        'cfb_fb_border_color' => 'cfblb_fb_border_color',
        'cfb_width' => 'cfblb_width',
        'cfb_height' => 'cfblb_height',
        'cfb_show_faces' => 'cfblb_show_faces',
        'cfb_stream' => 'cfblb_stream',	
		'cfb_events' => 'cfblb_events',			
		'cfb_message' => 'cfblb_message',
        'cfb_header' => 'cfblb_header',
		'cfb_small_header'=>'cfblb_small_header',
		'cfb_lang'=>'cfblb_lang',
		'popup_enable_disable'=>'cfblb_popup_enable_disable',
		'popup_title' => 'cfblb_popup_title',
        'popup_fb_url' => 'cfblb_popup_fb_url',
        'popup_width' => 'cfblb_popup_width',
        'popup_height' => 'cfblb_popup_height',
        'popup_show_faces' => 'cfblb_popup_show_faces',
        'popup_stream' => 'cfblb_popup_stream',
        'popup_header' => 'cfblb_popup_header',
		'popup_small_header'=>'cfblb_popup_small_header',
		'popup_lang'=>'cfblb_popup_lang',
		'popup_repeat_times'=>'cfblb_popup_repeat_times',	
		'popup_show_on_home'=>'cfblb_popup_show_on_home',		
		'popup_show_on_logged_in'=>'cfblb_popup_show_on_logged_in',	
		'popup_show_on_not_logged_in'=>'cfblb_popup_show_on_not_logged_in'
    );

	
	
   
if(current_user_can('edit_posts'))	
{
    if (isset($_POST['frm_submit_post'])) {
		
		 if (isset( $_POST['cflb_noonce']) &&  wp_verify_nonce( $_POST['cflb_noonce'], 'cflb_verify')) 
		 {
	
			if ($_POST['cfpl_enable']){
				 update_option('cfpl_enable', sanitize_text_field($_POST['cfpl_enable']));
			}
			if ($_POST['show_button']){
				  update_option('cfpl_show_button', sanitize_text_field($_POST['show_button']));
			}
			if ($_POST['layout']){
				 update_option('cfpl_layout', sanitize_text_field($_POST['layout']));
			}
			if ($_POST['show_faces']){
				 update_option('cfpl_show_faces', sanitize_text_field($_POST['show_faces']));
			}
			if ($_POST['verb']){
				 update_option('cfpl_verb', sanitize_text_field($_POST['verb']));
			}
			
			
			?>
			<div id="message" class="updated fade"><p><strong><?php _e('Options saved.', 'facebooklikebox'); ?></strong></p></div>
			<?php
		}
		else
		{
			die("Sorry! Your Noonce didn't verify");
			
		}
		
		
	
		}
	
	if (isset($_POST['reset_cookie'])) {
		if (isset( $_POST['cflb_noonce']) &&  wp_verify_nonce( $_POST['cflb_noonce'], 'cflb_verify')) 
		{
			$seed = str_split('abcdefghijklmnopqrstuvwxyz');
			shuffle($seed); 
			$rand = '';
			foreach (array_rand($seed, 5) as $k) $rand .= $seed[$k];

			update_option('cffb_popup_cookie', 'popup_cookie_'.$rand);
		?>	
			<div id="message" class="updated fade"><p><strong><?php _e('Cookie Reset Saved	.', 'facebooklikebox'); ?></strong></p></div>
		<?php
		}
		else
		{
			die("Sorry! Your Noonce didn't verify");
		}


	}


    if (isset($_POST['frm_submit'])) {
	
	if (isset( $_POST['cflb_noonce']) &&  wp_verify_nonce( $_POST['cflb_noonce'], 'cflb_verify')) 
	{
        if (isset($_POST['frm_title'])){
           update_option($cfblb_options['cfb_title'], sanitize_text_field($_POST['frm_title']));
        }
        if (isset($_POST['frm_url'])){
           update_option($cfblb_options['cfb_fb_url'], sanitize_text_field($_POST['frm_url']));
        }
        if (isset($_POST['frm_border_color'])){
           update_option($cfblb_options['cfb_fb_border_color'], sanitize_text_field($_POST['frm_border_color']));
        }
        if (isset($_POST['frm_width'])){
            update_option($cfblb_options['cfb_width'],sanitize_text_field($_POST['frm_width']));
        }
        if (isset($_POST['frm_height'])){
            update_option($cfblb_options['cfb_height'], sanitize_text_field($_POST['frm_height']));
        }
        if (!empty($_POST['frm_show_faces'])){
            update_option($cfblb_options['cfb_show_faces'], sanitize_text_field($_POST['frm_show_faces']));
        }
        if (!empty($_POST['frm_stream'])){
            update_option($cfblb_options['cfb_stream'], sanitize_text_field($_POST['frm_stream']));  
		}
		else	
		{			
			update_option($cfblb_options['cfb_stream'], "");
		}
        if (!empty($_POST['frm_events'])){ 
			update_option($cfblb_options['cfb_events'], sanitize_text_field($_POST['frm_events'])); 
		}
		else	
		{	
			update_option($cfblb_options['cfb_events'], "");
		}	
        if (!empty($_POST['frm_message'])){  
			update_option($cfblb_options['cfb_message'], sanitize_text_field($_POST['frm_stream']));   
		}	
		else
		{		
			update_option($cfblb_options['cfb_message'], "");		
		}
        if (!empty($_POST['frm_header'])){
            update_option($cfblb_options['cfb_header'], sanitize_text_field($_POST['frm_header']));
        }
		if (!empty($_POST['frm_small_header'])){
            update_option($cfblb_options['cfb_small_header'], sanitize_text_field($_POST['frm_small_header']));
        }
		
		if (!empty($_POST['frm_lang'])){
            update_option($cfblb_options['cfb_lang'], sanitize_text_field($_POST['frm_lang']));
        }
		
		
        ?>
        <div id="message" class="updated fade"><p><strong><?php _e('Options saved.', 'facebooklikebox'); ?></strong></p></div>
        <?php
    }
	else
	{
		die("Sorry! Your Noonce didn't verify");
	}
	}
	
	
	if (isset($_POST['popup_frm_submit'])) {
	
	if (isset( $_POST['popup_cflb_noonce']) &&  wp_verify_nonce( $_POST['popup_cflb_noonce'], 'popup_cflb_verify')) 
	{
		
		if (isset($_POST['popup_enable_disable'])){
           update_option($cfblb_options['popup_enable_disable'], sanitize_text_field($_POST['popup_enable_disable']));		}		else		{		  update_option($cfblb_options['popup_enable_disable'], "");		}
		
        if (isset($_POST['popup_frm_title'])){
           update_option($cfblb_options['popup_title'], sanitize_text_field($_POST['popup_frm_title']));
        }
        if (isset($_POST['popup_frm_url'])){
           update_option($cfblb_options['popup_fb_url'], sanitize_text_field($_POST['popup_frm_url']));
        }
        if (isset($_POST['popup_frm_width'])){
            update_option($cfblb_options['popup_width'],sanitize_text_field($_POST['popup_frm_width']));
        }
        if (isset($_POST['popup_frm_height'])){
            update_option($cfblb_options['popup_height'], sanitize_text_field($_POST['popup_frm_height']));
        }
        if (!empty($_POST['popup_frm_show_faces'])){
            update_option($cfblb_options['popup_show_faces'], sanitize_text_field($_POST['popup_frm_show_faces']));
        }
        if (!empty($_POST['popup_frm_stream'])){
            update_option($cfblb_options['popup_stream'], sanitize_text_field($_POST['popup_frm_stream']));
        }
        if (!empty($_POST['popup_frm_header'])){
            update_option($cfblb_options['popup_header'], sanitize_text_field($_POST['popup_frm_header']));
        }
		if (!empty($_POST['popup_frm_small_header'])){
            update_option($cfblb_options['popup_small_header'], sanitize_text_field($_POST['popup_frm_small_header']));
        }
		
		if (!empty($_POST['popup_frm_lang'])){
            update_option($cfblb_options['popup_lang'], sanitize_text_field($_POST['popup_frm_lang']));
        }
		if (isset($_POST['popup_repeat_times'])){
            update_option($cfblb_options['popup_repeat_times'], sanitize_text_field($_POST['popup_repeat_times']));
        }		if (isset($_POST['popup_show_on_home'])){           update_option($cfblb_options['popup_show_on_home'], sanitize_text_field($_POST['popup_show_on_home']));		}		else		{		  update_option($cfblb_options['popup_show_on_home'], "");		}				if (isset($_POST['popup_show_on_logged_in'])){           update_option($cfblb_options['popup_show_on_logged_in'], sanitize_text_field($_POST['popup_show_on_logged_in']));		}		else		{		  update_option($cfblb_options['popup_show_on_logged_in'], "");		}		if (isset($_POST['popup_show_on_not_logged_in'])){           update_option($cfblb_options['popup_show_on_not_logged_in'], sanitize_text_field($_POST['popup_show_on_not_logged_in']));		}		else		{		  update_option($cfblb_options['popup_show_on_not_logged_in'], "");		}		
		
		
		$cffb_cookie=get_option('cffb_popup_cookie');
		
		if(empty($cffb_cookie))
		{
			$seed = str_split('abcdefghijklmnopqrstuvwxyz');
			shuffle($seed); 
			$rand = '';
			foreach (array_rand($seed, 5) as $k) $rand .= $seed[$k];
			
			update_option("cffb_popup_cookie","popup_cookie_".$rand);
			
		}
		
        ?>
        <div id="message" class="updated fade"><p><strong><?php _e('Popup Options saved.', 'facebooklikebox'); ?></strong></p></div>
        <?php
    }
	else
	{
		die("Sorry! Your Noonce didn't verify");
	}
	}


	
    $option_value = cfblb_retrieve_options();
	$cfpl_enable = $option_value['cfpl_enable'];
	$show_button = $option_value['show_button'];
	$layout = $option_value['layout'];
	$show_faces = $option_value['show_faces'];
	$verb = $option_value['verb'];

	
	
			
		
	
	
    ?>
	<div class="fb-container" id="poststuff">
    <div class="wrap_facebook">
        <h2><?php echo __("Facebook Like Box Options", "facebooklikebox"); ?></h2><br />
        <!-- Administration panel form -->
		<p class="update-nag" style="margin:0px 20px 10px 2px;">Go to Appearance -> Widgets.   Chose `Facebook Like Box` Widget and Add it to Sidebar / Footer / or any widget Area.</p>
		
        <form method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<div class="postbox">
			<h3 class="hndle">
			 <span>General Settings</span>
			</h3>
			<div class="inside">
            <table>
            
                <tr height="35">
                    <td width="150"><b><?php _e('Title', 'facebooklikebox'); ?>:</b></td>
                    <td><input type="text" name="frm_title" size="50" value="<?php echo esc_attr($option_value['title']); ?>"/>
                        &nbsp;<label id="cfbtitle"><b>?</b></label></td>
                </tr>
                <tr id="title_help"><td></td><td>(<?php _e('Title of the facebook like box', 'facebooklikebox'); ?>)</td></tr>
                <tr height="35">
                    <td width="150"><b><?php _e('Facebook Page URL:', 'facebooklikebox'); ?></b></td>
                    <td><input type="text" name="frm_url" size="50" value="<?php echo esc_attr($option_value['fb_url']); ?>"/>
                        &nbsp;<label id="cfbpage_url"><b>?</b></label>
                
				</tr>
                <tr id="page_url_help"><td></td><td>(<?php _e('Copy and paste your facebook page URL here - Example -> <a href="https://www.facebook.com/facebook" target="_blank">https://www.facebook.com/facebook</a>', 'facebooklikebox'); ?>)</td></tr>
                <tr height="35">
                    <td width="150"><b><?php _e("Border Color", 'facebooklikebox'); ?>:</b></td>
                    <td>#<input type="text" name="frm_border_color" value="<?php echo esc_attr($option_value['fb_border_color']); ?>"/>
                        &nbsp;<label id="cfbborder"><b>?</b></label></td>
                </tr>
                <tr id="border_help"><td></td><td>(<?php _e('Border Color of the facebook like box. HEX Code only - <a href="http://htmlcolorcodes.com/" target="_blank">http://htmlcolorcodes.com/</a>', 'facebooklikebox'); ?>)</td></tr>
                <tr height="35">
                    <td width="150"><b><?php _e('Width', 'facebooklikebox'); ?>:</b></td>
                    <td><input type="text" name="frm_width" value="<?php echo esc_attr($option_value['width']); ?>"/>px 
                        &nbsp;<label id="cfbwidth"><b>?</b></label></td>
                </tr>
                <tr id="width_help"><td></td><td>(<?php _e('Width of the facebook like box', 'facebooklikebox'); ?>)</td></tr>
                <tr height="35">
                    <td width="150"><b><?php _e('Height', 'facebooklikebox'); ?>:</b></td>
                    <td><input type="text" name="frm_height" value="<?php echo esc_attr($option_value['height']); ?>"/>px 
                        &nbsp;<label id="cfbheight"><b>?</b></label></td>
                </tr>
                <tr id="height_help"><td></td><td>(<?php _e('Height of the facebook like box', 'facebooklikebox'); ?>)</td></tr>
                <tr height="35">
                    <td width="150"><b><?php _e('Show Faces', 'facebooklikebox'); ?>:</b></td>
                    <td>
                        <select name="frm_show_faces" style="margin-left:0px;width:100px;">
                            <option value="true" <?php if ($option_value['show_faces'] == "true") echo "selected='selected'"; ?>><?php _e('Yes', 'facebooklikebox'); ?></option>
                            <option value="false" <?php if ($option_value['show_faces'] == "false") echo "selected='selected'"; ?>><?php _e('No', 'facebooklikebox'); ?></option>
                        </select>
                        &nbsp;<label id="cfbshow_faces"><b>?</b></label>
                    </td>
                </tr>
                <tr id="show_faces_help"><td></td><td>(<?php _e('Show few facebook user face photos who liked your page', 'facebooklikebox'); ?>)</td></tr>
                <tr height="35">
                    <td width="150"><b><?php _e('Tabs', 'facebooklikebox'); ?>:</b></td>
                    <td>							<label>Facebook Feed Stream(timeline):</label><input type="checkbox" <?php if ($option_value['stream'] == "true") echo "checked"; ?> value="true" name="frm_stream"/>&nbsp;&nbsp;						<label>Events:</label><input type="checkbox" <?php if ($option_value['events'] == "true") echo "checked";?> value="true" name="frm_events"/>&nbsp;&nbsp;						<label>Messages:</label><input type="checkbox" <?php if ($option_value['message'] == "true") echo "checked";?> value="true" name="frm_message"/>						<!--
                        <select name="frm_stream" style="margin-left:0px;width:100px;">
                            <option value="true" <?php if ($option_value['stream'] == "true") echo "selected='selected'"; ?>><?php _e('Yes', 'facebooklikebox'); ?></option>
                            <option value="false" <?php if ($option_value['stream'] == "false") echo "selected='selected'"; ?>><?php _e('No', 'facebooklikebox'); ?></option>
                        </select>-->
                        &nbsp;<label id="cfbstream"><b>?</b></label>
                    </td>
                </tr>
                <tr id="stream_help"><td></td><td>(<?php _e('Show your tabs for timeline,events and messages respectively', 'facebooklikebox'); ?>)</td></tr>
                <tr height="35">
                    <td width="168"><b><?php _e('Header Image:', 'facebooklikebox'); ?></b></td>
                    <td>
                        <select name="frm_header" style="margin-left:0px;width:100px;">
                            <option value="true" <?php if ($option_value['header'] == "true") echo "selected='selected'"; ?>><?php _e('Yes', 'facebooklikebox'); ?></option>
                            <option value="false" <?php if ($option_value['header'] == "false") echo "selected='selected'"; ?>><?php _e('No', 'facebooklikebox'); ?></option>
                        </select>
                        &nbsp;<label id="cfbheader"><b>?</b></label>
                    </td>
                </tr>
				<tr id="header_help"><td></td><td>(<?php _e('Show / Hide your facebook cover image', 'facebooklikebox'); ?>)</td></tr>
				 <tr height="35">
                    <td width="168"><b><?php _e('Small Header', 'facebooklikebox'); ?></b></td>
                    <td>
                        <select name="frm_small_header" style="margin-left:0px;width:100px;">
                            <option value="true" <?php if ($option_value['small_header'] == "true") echo "selected='selected'"; ?>><?php _e('Yes', 'facebooklikebox'); ?></option>
                            <option value="false" <?php if ($option_value['small_header'] == "false") echo "selected='selected'"; ?>><?php _e('No', 'facebooklikebox'); ?></option>
                        </select>
                        &nbsp;<label id="cfbsmheader"><b>?</b></label>
                    </td>
                </tr>
				<tr id="cfbsmheader_help"><td></td><td>(<?php _e('Show Small Header', 'facebooklikebox'); ?>)</td></tr>
				
								<tr id="header_help"><td></td><td>(<?php _e('Show / Hide your facebook cover image', 'facebooklikebox'); ?>)</td></tr>
				 <tr height="35">
                    <td width="168"><b><?php _e('Language', 'facebooklikebox'); ?></b></td>
                    <td>
					<?php $current_lang=get_locale(); 
						  
					$cfb_langs=array("$current_lang"=>"Default",'af_ZA'=>'Afrikaans','sq_AL'=>'Albanian','ar_AR'=>'Arabic','hy_AM'=>'Armenian','ay_BO'=>'Aymara','az_AZ'=>'Azeri','eu_ES'=>'Basque','be_BY'=>'Belarusian','bn_IN'=>'Bengali','bs_BA'=>'Bosnian','bg_BG'=>'Bulgarian','ca_ES'=>'Catalan','ck_US'=>'Cherokee','hr_HR'=>'Croatian','cs_CZ'=>'Czech','da_DK'=>'Danish','nl_NL'=>'Dutch','nl_BE'=>'Dutch (Belgi?)','en_PI'=>'English (Pirate)','en_GB'=>'English (UK)','en_UD'=>'English (Upside Down)','en_US'=>'English (US)','eo_EO'=>'Esperanto','et_EE'=>'Estonian','fo_FO'=>'Faroese','tl_PH'=>'Filipino','fi_FI'=>'Finnish','fb_FI'=>'Finnish (test)','fr_FR'=>'French (Canada)','gl_ES'=>'Galician','ka_GE'=>'Georgian','de_DE'=>'German','el_GR'=>'Greek','gn_PY'=>'Guaran?','gu_IN'=>'Gujarati','he_IL'=>'Hebrew','hi_IN'=>'Hindi','hu_HU'=>'Hungarian','is_IS'=>'Icelandic','id_ID'=>'Indonesian','ga_IE'=>'Irish','it_IT'=>'Italian','ja_JP'=>'Japanese','jv_ID'=>'Javanese','kn_IN'=>'Kannada','kk_KZ'=>'Kazakh','km_KH'=>'Khmer','tl_ST'=>'Klingon','ko_KR'=>'Korean','ku_TR'=>'Kurdish','la_VA'=>'Latin','lv_LV'=>'Latvian','fb_LT'=>'Leet Speak','li_NL'=>'Limburgish','lt_LT'=>'Lithuanian','mk_MK'=>'Macedonian','mg_MG'=>'Malagasy','ms_MY'=>'Malay','ml_IN'=>'Malayalam','mt_MT'=>'Maltese','mr_IN'=>'Marathi','mn_MN'=>'Mongolian','ne_NP'=>'Nepali','se_NO'=>'Northern S?mi','nb_NO'=>'Norwegian (bokmal)','nn_NO'=>'Norwegian (nynorsk)','ps_AF'=>'Pashto','fa_IR'=>'Persian','pl_PL'=>'Polish','pt_BR'=>'Portuguese (Brazil)','pt_PT'=>'Portuguese (Portugal)','pa_IN'=>'Punjabi','qu_PE'=>'Quechua','ro_RO'=>'Romanian','rm_CH'=>'Romansh','ru_RU'=>'Russian','sa_IN'=>'Sanskrit','sr_RS'=>'Serbian','zh_CN'=>'Simplified Chinese (China)','sl_SI'=>'Slovak','rm_CH'=>'Slovenian','so_SO'=>'Somali','es_LA'=>'Spanish','es_CL'=>'Spanish (Chile)','es_CO'=>'Spanish (Colombia)','es_MX'=>'Spanish (Mexico)','es_ES'=>'Spanish (Spain)','es_VE'=>'Spanish (Venezuela)','sw_KE'=>'Swahili','sv_SE'=>'Swedish','sy_SY'=>'Syriac','tg_TJ'=>'Tajik','ta_IN'=>'Tamil','tt_RU'=>'Tatar','te_IN'=>'Telugu','th_TH'=>'Thai','zh_HK'=>'Traditional Chinese (Hong Kong)','zh_TW'=>'Traditional Chinese (Taiwan)','tr_TR'=>'Turkish','uk_UA'=>'Ukrainian','ur_PK'=>'Urdu','uz_UZ'=>'Uzbek','vi_VN'=>'Vietnamese','cy_GB'=>'Welsh','xh_ZA'=>'Xhosa','yi_DE'=>'Yiddish','zu_ZA'=>'Zulu');		
					
					
					?>
                        <select name="frm_lang" style="margin-left:0px;width:100px;">
						
						<?php
							
							foreach($cfb_langs as $ln_c=>$ln)
							{
								echo '<option value="'.$ln_c.'" ';
									
								if($option_value['lang']==$ln_c)
								{
									echo "selected";
								}
								
								echo '>'.$ln.'</option>';
							}
					
					?>					
                        </select>
                        &nbsp;<label id="cfbsmlang"><b>?</b></label>
                    </td>
                </tr>
				<tr id="cfbsmlang_help"><td></td><td>(<?php _e('Set the language for Likebox', 'facebooklikebox'); ?>)</td></tr>
				
				
                
                <tr height="60"><td></td><td><input type="submit" name="frm_submit" value="<?php _e('Save', 'facebooklikebox'); ?>" class="button button-primary"/></td>
                </tr>
            </table>
			
			</div> <!-- End of .inside -->
			
   </div>
	<?php wp_nonce_field( 'cflb_verify', 'cflb_noonce'); ?>
        </form>
		<p class="update-nag" style="margin:0px 20px 10px 2px;">Recommandation: It is Adviced to set Height-value &gt; 210 for better look.</p>
		
		
		<form method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<div class="postbox">
			<h3 class="hndle">
			 <span>Facebook Posts Like Options</span>
			</h3>
			<div class="inside">
            <table>
                 <tr height="35">
                    <td width="150"><b><?php _e('Show like button for posts', 'facebooklikebox'); ?>:</b></td>
                     <td>
                        <select name="cfpl_enable" style="margin-left:0px;width:100px;">
                            <option value="yes" <?php if ($cfpl_enable == "yes") echo "selected='selected'"; ?>><?php _e('Yes', 'facebooklikebox'); ?></option>
                            <option value="no" <?php if ($cfpl_enable == "no") echo "selected='selected'"; ?>><?php _e('No', 'facebooklikebox'); ?></option>
                        </select>
                    </td>
                </tr>
				
				<tr height="35">
                    <td width="150"><b><?php _e('Show like button', 'facebooklikebox'); ?>:</b></td>
                     <td>
                         <select name="show_button" style="margin-left:0px;width:225px;">
                            <option value="before_post_content" <?php if ($show_button == "before_post_content") echo "selected='selected'"; ?>><?php _e('Before the post content', 'facebooklikebox'); ?></option>
                            <option value="after_post_content" <?php if ($show_button == "after_post_content") echo "selected='selected'"; ?>><?php _e('After the post content', 'facebooklikebox'); ?></option>
                            <option value="before_after_post_content" <?php if ($show_button == "before_after_post_content") echo "selected='selected'"; ?>><?php _e('Before and after the post content', 'facebooklikebox'); ?></option>
                        </select>
                    </td>
                </tr>
				<tr height="35">
                    <td width="150"><b><?php _e('Layout', 'facebooklikebox'); ?>:</b></td>
                     <td>
                         <select name="layout" style="margin-left:0px;width:100px;">
                            <option value="standard" <?php if ($layout == "standard") echo "selected='selected'"; ?>>standard</option>
                            <option value="button_count" <?php if ($layout == "button_count") echo "selected='selected'"; ?>>button_count</option>
                            <option value="box_count" <?php if ($layout == "box_count") echo "selected='selected'"; ?>>box_count</option>
                        </select>
                    </td>
                </tr>
				<tr height="35">
                    <td width="150"><b><?php _e('Show Faces', 'facebooklikebox'); ?>:</b></td>
                     <td>
                        <select name="show_faces" style="margin-left:0px;width:100px;">
                            <option value="true" <?php if ($show_faces == "true") echo "selected='selected'"; ?>><?php _e('Yes', 'facebooklikebox'); ?></option>
                            <option value="false" <?php if ($show_faces == "false") echo "selected='selected'"; ?>><?php _e('No', 'facebooklikebox'); ?></option>
                        </select>
                        &nbsp;(<?php _e('Select the option to show the faces', 'facebooklikebox'); ?>)
                    </td>
                </tr>
				<tr height="35">
                    <td width="150"><b><?php _e('Verb to display', 'facebooklikebox'); ?>:</b></td>
                     <td>
                         <select name="verb" style="margin-left:0px;width:100px;">
                            <option value="like" <?php if ($verb == "like") echo "selected='selected'"; ?>><?php _e('like', 'facebooklikebox'); ?></option>
                            <option value="recommend" <?php if ($verb == "recommend") echo "selected='selected'"; ?>><?php _e('recommend', 'facebooklikebox'); ?></option>
                        </select>
                    </td>
                </tr>
				
				<tr height="60"><td></td><td><input type="submit" name="frm_submit_post" value="<?php _e('Save', 'facebooklikebox'); ?>" class="button button-primary"/></td>
                </tr>
			</table>
			</div>
					
			</div>
        <?php wp_nonce_field( 'cflb_verify', 'cflb_noonce'); ?>
		</form>
		
		
		<form method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<div class="postbox">
			<h3 class="hndle">
			 <span>Auto Popup Settings</span>
			</h3>
			<div class="inside">
            <table>
				
				<tr height="35">
                    <td width="150"><b><?php _e('Enable/Disable Popup', 'facebooklikebox'); ?>:</b></td>
                    <td><input type="checkbox" name="popup_enable_disable"  <?php if($option_value['popup_enable_disable']=="on"){ echo "checked";}?>/>
                        &nbsp;<label id="popup_enable_disable"><b>?</b></label></td>
                </tr>
                <tr id="popup_enable_disable_help"><td></td><td>(<?php _e('Enable or Disable Popup', 'facebooklikebox'); ?>)</td></tr>
			
                <tr height="35">
                    <td width="150"><b><?php _e('Title', 'facebooklikebox'); ?>:</b></td>
                    <td><input type="text" name="popup_frm_title" size="50" value="<?php echo esc_attr($option_value['popup_title']); ?>" placeholder="Like us on Facebook"/>
                        &nbsp;<label id="popuptitle"><b>?</b></label></td>
                </tr>
                <tr id="popup_title_help"><td></td><td>(<?php _e('Title of the facebook like box', 'facebooklikebox'); ?>)</td></tr>
                <tr height="35">
                    <td width="150"><b><?php _e('Facebook Page URL:', 'facebooklikebox'); ?></b></td>
                    <td><input type="text" name="popup_frm_url" size="50" value="<?php echo esc_attr($option_value['popup_fb_url']); ?>" required/>
                        &nbsp;<label id="popuppage_url"><b>?</b></label>
                
				</tr>
                <tr id="popup_page_url_help"><td></td><td>(<?php _e('Copy and paste your facebook page URL here - Example -> <a href="https://www.facebook.com/facebook" target="_blank">https://www.facebook.com/facebook</a>', 'facebooklikebox'); ?>)</td></tr>

                <tr id="popup_border_help"><td></td><td>(<?php _e('Border Color of the facebook like box. HEX Code only - <a href="http://htmlcolorcodes.com/" target="_blank">http://htmlcolorcodes.com/</a>', 'facebooklikebox'); ?>)</td></tr>
                <tr height="35">
                    <td width="150"><b><?php _e('Width', 'facebooklikebox'); ?>:</b></td>
                    <td><input type="text" name="popup_frm_width" value="<?php echo esc_attr($option_value['popup_width']); ?>" placeholder="400" />px 
                        &nbsp;<label id="popupwidth"><b>?</b></label></td>
                </tr>
                <tr id="popup_width_help"><td></td><td>(<?php _e('Width of the facebook like box', 'facebooklikebox'); ?>)</td></tr>
                <tr height="35">
                    <td width="150"><b><?php _e('Height', 'facebooklikebox'); ?>:</b></td>
                    <td><input type="text" name="popup_frm_height" value="<?php echo esc_attr($option_value['popup_height']); ?>" placeholder="250"/>px 
                        &nbsp;<label id="popupheight"><b>?</b></label></td>
                </tr>
                <tr id="popup_height_help"><td></td><td>(<?php _e('Height of the facebook like box', 'facebooklikebox'); ?>)</td></tr>
                <tr height="35">
                    <td width="150"><b><?php _e('Show Faces', 'facebooklikebox'); ?>:</b></td>
                    <td>
                        <select name="popup_frm_show_faces" style="margin-left:0px;width:100px;">
                            <option value="true" <?php if ($option_value['popup_show_faces'] == "true") echo "selected='selected'"; ?>><?php _e('Yes', 'facebooklikebox'); ?></option>
                            <option value="false" <?php if ($option_value['popup_show_faces'] == "false") echo "selected='selected'"; ?>><?php _e('No', 'facebooklikebox'); ?></option>
                        </select>
                        &nbsp;<label id="popupshow_faces"><b>?</b></label>
                    </td>
                </tr>
                <tr id="popup_show_faces_help"><td></td><td>(<?php _e('Show few facebook user face photos who liked your page', 'facebooklikebox'); ?>)</td></tr>
                <tr height="35">
                    <td width="150"><b><?php _e('Facebook Feed Stream', 'facebooklikebox'); ?>:</b></td>
                    <td>
                        <select name="popup_frm_stream" style="margin-left:0px;width:100px;">
                            <option value="true" <?php if ($option_value['popup_stream'] == "true") echo "selected='selected'"; ?>><?php _e('Yes', 'facebooklikebox'); ?></option>
                            <option value="false" <?php if ($option_value['popup_stream'] == "false") echo "selected='selected'"; ?>><?php _e('No', 'facebooklikebox'); ?></option>
                        </select>
                        &nbsp;<label id="popupstream"><b>?</b></label>
                    </td>
                </tr>
                <tr id="popup_stream_help"><td></td><td>(<?php _e('Show your recet posts published on your facebook page', 'facebooklikebox'); ?>)</td></tr>
                <tr height="35">
                    <td width="168"><b><?php _e('Header Image', 'facebooklikebox'); ?></b></td>
                    <td>
                        <select name="popup_frm_header" style="margin-left:0px;width:100px;">
                            <option value="true" <?php if ($option_value['popup_header'] == "true") echo "selected='selected'"; ?>><?php _e('Yes', 'facebooklikebox'); ?></option>
                            <option value="false" <?php if ($option_value['popup_header'] == "false") echo "selected='selected'"; ?>><?php _e('No', 'facebooklikebox'); ?></option>
                        </select>
                        &nbsp;<label id="popupheader"><b>?</b></label>
                    </td>
                </tr>
				<tr id="popup_header_help"><td></td><td>(<?php _e('Show / Hide your facebook cover image', 'facebooklikebox'); ?>)</td></tr>
				 <tr height="35">
                    <td width="168"><b><?php _e('Small Header', 'facebooklikebox'); ?></b></td>
                    <td>
                        <select name="popup_frm_small_header" style="margin-left:0px;width:100px;">
                            <option value="true" <?php if ($option_value['popup_small_header'] == "true") echo "selected='selected'"; ?>><?php _e('Yes', 'facebooklikebox'); ?></option>
                            <option value="false" <?php if ($option_value['popup_small_header'] == "false") echo "selected='selected'"; ?>><?php _e('No', 'facebooklikebox'); ?></option>
                        </select>
                        &nbsp;<label id="popupsmheader"><b>?</b></label>
                    </td>
                </tr>
				<tr id="popup_smheader_help"><td></td><td>(<?php _e('Show Small Header', 'facebooklikebox'); ?>)</td></tr>
				
								<tr id="popup_header_help"><td></td><td>(<?php _e('Show / Hide your facebook cover image', 'facebooklikebox'); ?>)</td></tr>
				 <tr height="35">
                    <td width="168"><b><?php _e('Language', 'facebooklikebox'); ?></b></td>
                    <td>
					<?php $current_lang=get_locale(); 
						  
					$cfb_langs=array("$current_lang"=>"Default",'af_ZA'=>'Afrikaans','sq_AL'=>'Albanian','ar_AR'=>'Arabic','hy_AM'=>'Armenian','ay_BO'=>'Aymara','az_AZ'=>'Azeri','eu_ES'=>'Basque','be_BY'=>'Belarusian','bn_IN'=>'Bengali','bs_BA'=>'Bosnian','bg_BG'=>'Bulgarian','ca_ES'=>'Catalan','ck_US'=>'Cherokee','hr_HR'=>'Croatian','cs_CZ'=>'Czech','da_DK'=>'Danish','nl_NL'=>'Dutch','nl_BE'=>'Dutch (Belgi?)','en_PI'=>'English (Pirate)','en_GB'=>'English (UK)','en_UD'=>'English (Upside Down)','en_US'=>'English (US)','eo_EO'=>'Esperanto','et_EE'=>'Estonian','fo_FO'=>'Faroese','tl_PH'=>'Filipino','fi_FI'=>'Finnish','fb_FI'=>'Finnish (test)','fr_FR'=>'French (Canada)','gl_ES'=>'Galician','ka_GE'=>'Georgian','de_DE'=>'German','el_GR'=>'Greek','gn_PY'=>'Guaran?','gu_IN'=>'Gujarati','he_IL'=>'Hebrew','hi_IN'=>'Hindi','hu_HU'=>'Hungarian','is_IS'=>'Icelandic','id_ID'=>'Indonesian','ga_IE'=>'Irish','it_IT'=>'Italian','ja_JP'=>'Japanese','jv_ID'=>'Javanese','kn_IN'=>'Kannada','kk_KZ'=>'Kazakh','km_KH'=>'Khmer','tl_ST'=>'Klingon','ko_KR'=>'Korean','ku_TR'=>'Kurdish','la_VA'=>'Latin','lv_LV'=>'Latvian','fb_LT'=>'Leet Speak','li_NL'=>'Limburgish','lt_LT'=>'Lithuanian','mk_MK'=>'Macedonian','mg_MG'=>'Malagasy','ms_MY'=>'Malay','ml_IN'=>'Malayalam','mt_MT'=>'Maltese','mr_IN'=>'Marathi','mn_MN'=>'Mongolian','ne_NP'=>'Nepali','se_NO'=>'Northern S?mi','nb_NO'=>'Norwegian (bokmal)','nn_NO'=>'Norwegian (nynorsk)','ps_AF'=>'Pashto','fa_IR'=>'Persian','pl_PL'=>'Polish','pt_BR'=>'Portuguese (Brazil)','pt_PT'=>'Portuguese (Portugal)','pa_IN'=>'Punjabi','qu_PE'=>'Quechua','ro_RO'=>'Romanian','rm_CH'=>'Romansh','ru_RU'=>'Russian','sa_IN'=>'Sanskrit','sr_RS'=>'Serbian','zh_CN'=>'Simplified Chinese (China)','sl_SI'=>'Slovak','rm_CH'=>'Slovenian','so_SO'=>'Somali','es_LA'=>'Spanish','es_CL'=>'Spanish (Chile)','es_CO'=>'Spanish (Colombia)','es_MX'=>'Spanish (Mexico)','es_ES'=>'Spanish (Spain)','es_VE'=>'Spanish (Venezuela)','sw_KE'=>'Swahili','sv_SE'=>'Swedish','sy_SY'=>'Syriac','tg_TJ'=>'Tajik','ta_IN'=>'Tamil','tt_RU'=>'Tatar','te_IN'=>'Telugu','th_TH'=>'Thai','zh_HK'=>'Traditional Chinese (Hong Kong)','zh_TW'=>'Traditional Chinese (Taiwan)','tr_TR'=>'Turkish','uk_UA'=>'Ukrainian','ur_PK'=>'Urdu','uz_UZ'=>'Uzbek','vi_VN'=>'Vietnamese','cy_GB'=>'Welsh','xh_ZA'=>'Xhosa','yi_DE'=>'Yiddish','zu_ZA'=>'Zulu');		
					
					
					?>
                        <select name="popup_frm_lang" style="margin-left:0px;width:100px;">
						
						<?php
							
							foreach($cfb_langs as $ln_c=>$ln)
							{
								echo '<option value="'.$ln_c.'" ';
									
								if($option_value['popup_lang']==$ln_c)
								{
									echo "selected";
								}
								
								echo '>'.$ln.'</option>';
							}
					
					?>					
                        </select>
                        &nbsp;<label id="popupsmlang"><b>?</b></label>
                    </td>
                </tr>
				<tr id="popup_smlang_help"><td></td><td>(<?php _e('Set the language for Likebox', 'facebooklikebox'); ?>)</td></tr>
				
				
				<tr height="35">
                    <td width="168"><b><?php _e('Popup Repeat Time', 'facebooklikebox'); ?></b></td>
                    <td>
                        <select name="popup_repeat_times" style="margin-left:0px;width:100px;">
						 <?php
							
							for($i=0;$i<=30;$i++)
							{
								echo '<option value="'.$i.'"';
									if($i==$option_value['popup_repeat_times'])
										echo "selected";
								echo '>'.$i.'</option>';
							}
						  ?>
							
                        </select>
                     </td>
					 
					 
                </tr>
				<tr><td colspan="2"><p style="font-size:80%;font-style:italic;">(If you select 3.  Popup will be shown only 3 times maximum to a user. Please be advised, popups are bad for website-user-experience)</p></td></tr>
				</table>
				<h2>Popup Advanced Settings</h2>
				<table>
				<tr height="35">  
					<td width="200"><b><?php _e('Show on home page only', 'facebooklikebox'); ?>:</b></td>      
					<td><input type="checkbox" name="popup_show_on_home"  <?php if($option_value['popup_show_on_home']=="on"){ echo "checked";}?>/>                        &nbsp;<label id="popup_show_on_home"><b>?</b></label></td>
				</tr> 
				<tr id="popup_show_on_home_help"><td></td><td>(<?php _e('Display popup on home page only', 'facebooklikebox'); ?>)</td></tr>
				
				<tr height="35">
				<td width="200"><b><?php _e('Show for logged in visitors only', 'facebooklikebox'); ?>:</b></td>                    <td><input type="checkbox" name="popup_show_on_logged_in"  <?php if($option_value['popup_show_on_logged_in']=="on"){ echo "checked";}?>/>						&nbsp;<label id="popup_show_on_logged_in"><b>?</b></label></td>                </tr>	
				<tr id="popup_show_on_logged_in_help"><td></td><td>(<?php _e('Display Popup only if visitor logged in', 'facebooklikebox'); ?>)</td></tr>	
				<tr height="35">
				<td width="200"><b><?php _e('Show for not logged in visitors only', 'facebooklikebox'); ?>:</b></td>                    <td><input type="checkbox" name="popup_show_on_not_logged_in"  <?php if($option_value['popup_show_on_not_logged_in']=="on"){ echo "checked";}?>/>						&nbsp;<label id="popup_show_on_not_logged_in"><b>?</b></label></td>            </tr>								
				<tr id="popup_show_on_not_logged_in_help"><td></td><td>(<?php _e('Display Popup only if visitor not logged in', 'facebooklikebox'); ?>)</td></tr>				
                
                <tr height="60"><td></td><td><input type="submit" name="popup_frm_submit" value="<?php _e('Save', 'facebooklikebox'); ?>" class="button button-primary"/></td>
                </tr>																
            </table>
			
			</div> <!-- End of .inside -->
			
   </div>
	<?php wp_nonce_field( 'popup_cflb_verify', 'popup_cflb_noonce'); ?>
        </form>		
				
	<form method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="submit" name="reset_cookie" value="Reset Cookie-Repeat-Counter" class="button"/>
		<p style="font-size:80%;font-style:italic;">(This will reset the counter. Popup will be shown to users again for "Popup Repeat Times"  [set this option above)</p>
		 <?php wp_nonce_field( 'cflb_verify', 'cflb_noonce'); ?>
	</form>	
				
		

		<div class="postbox">
			<h3 class="hndle">
			 <span>Shortcode</span>
			</h3>
			<div class="inside">
            <table>
                 <tr height="35">
                   
                     <td>
						 <td colspan="2" ><b><?php _e('[cardoza_facebook_like_box width="300" height="250"]', 'facebooklikebox'); ?></b></td>
                    </td>
                </tr>
				 <tr height="35">
					<td width="150">
						<?php _e('Width Attribute', 'facebooklikebox'); ?>
					</td>
                     <td>
						 <td colspan="2" ><b><?php _e('Will set the width of facebook like box. if left empty contaier width will be used.', 'facebooklikebox'); ?></b></td>
                    </td>
                </tr>
				
				 <tr height="35">
					<td width="150">
						<?php _e('Height Attribute', 'facebooklikebox'); ?>
					</td>
                     <td>
						 <td colspan="2" ><b><?php _e('Will set the height of facebook like box. if left empty default height will applied.', 'facebooklikebox'); ?></b></td>
                    </td>
                </tr>
				
			
			</table>
			</div>
					
			</div>
    </div>
	<div class="fb-preview" style="background: white;border: 1px solid #fbfbfb;margin: 20px;"></div>
	
	</div>
    <?php
	}
	else
	{
		die("You don't have permission to access this page");
	}
}



function cardoza_facebook_like_box_sc($atts) {
    ob_start();
    $option_value = cfblb_retrieve_options();

    if (isset($atts['width']) && !empty($atts['width']))
        $option_value['width'] = $atts['width'];
    if (isset($atts['height']) && !empty($atts['height']))
        $option_value['height'] = $atts['height'];
	
    ?>
	<div class="fb-page" style="border:1px solid #<?php echo esc_attr($option_value['fb_border_color']); ?>;"
		<?php
			if(empty($option_value['width']))
			{ ?>	data-adapt-container-width="true";
		<?php 
			}
			else
			{
			?>	 data-width="<?php echo esc_attr($option_value['width']); ?>"
	 <?php } 
			$header="";
			if($option_value['header']=="true")
				$header=false;
			else
				$header=true;
	 ?>
	 
	 data-height="<?php echo esc_attr($option_value['height']); ?>"
     data-href="<?php echo esc_url($option_value['fb_url']); ?>"  
     data-small-header="<?php echo esc_attr($option_value['small_header']);?>"  
     data-hide-cover="<?php echo esc_attr($header);?>" 
	 data-show-facepile="<?php echo esc_attr($option_value['show_faces']);?>"  
	 
	 <?php
		if($option_value['stream']=="true")
		{
		?>
			data-tabs="timeline"
	<?php	}
	?>	
     data-show-posts="false"
	 >
		
</div>
<div id="fb-root"></div>

<script>
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>
   
    <?php
    $output_string = ob_get_contents();
    ob_end_clean();
    return $output_string;
}

function fb_like_button_for_post($content) {
	
	global $post;
	$option_value = cfblb_retrieve_options();
	
    $cfpl_enable = $option_value['cfpl_enable'];
    $show_button = $option_value['show_button'];
    $layout = $option_value['layout'];
    $show_faces = $option_value['show_faces'];
    $verb = $option_value['verb'];
	
	  

    

    if (is_single()) {
		
		  $fb_like_url = '//www.facebook.com/plugins/like.php?href=' . urlencode(get_permalink($post->ID)) .
                           '&layout=' . esc_attr($layout) . '&show_faces=' . esc_attr($show_faces) . '&width=450&action=' . esc_attr($verb);

            $fb_like_iframe = '<div class="facebook-like-box"><iframe src="' . esc_url($fb_like_url) . '" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:450px; height:60px;"></iframe></div>';
	
							
        if ($cfpl_enable == 'yes') {
            if ($show_button == 'before_post_content') {
                $content = $fb_like_iframe
                        . $content;
            }
            if ($show_button == 'after_post_content') {
                $content = $content . $fb_like_iframe;
            }
            if ($show_button == 'before_after_post_content') {
				
				
                $content = $fb_like_iframe
                        . $content .
                        $fb_like_iframe;
            }
        }
    }
    return $content;
}

add_filter('the_content', 'fb_like_button_for_post');

function cardoza_facebook_posts_like_sc($content) {
	
	$option_value = cfblb_retrieve_options();
    $cfpl_enable = $option_value['cfpl_enable'];
    $show_faces = $option_value['show_faces'];
    $verb = $option_value['verb'];
	
  
 
    
    

    if (is_single()) {
		$fb_like_url = '//www.facebook.com/plugins/like.php?href=' . urlencode(get_permalink($post->ID)) .
                           '&layout=' . esc_attr($layout) . '&show_faces=' . esc_attr($show_faces) . '&width=450&action=' . esc_attr($verb);
        $content = '<iframe  src="' . esc_url($fb_like_url) . '" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:450px; height:60px;"></iframe>'
                . $content;
    }
    echo  $content;
}

function cardoza_fb_like_popup()
{
	wp_reset_query(); 
	$option_value = cfblb_retrieve_options();		
	if(!is_front_page() && $option_value['popup_show_on_home']=="on")	  {		
		return;	  
	}
	else if(!is_user_logged_in() && $option_value['popup_show_on_logged_in']=="on")
	{	
		return;
	}
	else if(is_user_logged_in() && $option_value['popup_show_on_not_logged_in']=="on")
	{
		return;	  
	}				
    $cffb_cookie=get_option('cffb_popup_cookie');
	$popup_repeat_times=$option_value['popup_repeat_times'];
	$repeat_time=@$_COOKIE[$cffb_cookie]==''?0:$_COOKIE[$cffb_cookie];	if($option_value['popup_enable_disable']=="on" && ($repeat_time<$popup_repeat_times || $popup_repeat_times==0))	{
	
		if(empty($option_value['popup_width']))
		{
			$option_value['popup_width']=400;
		}
		
		if(empty($option_value['popup_height']))
		{
			$option_value['popup_height']=250;
		}		$cflb_container_pop_height=$option_value['popup_height'];		if($option_value['popup_stream']=="true")		{
			$cflb_container_pop_height=$cflb_container_pop_height+10;		}
?>

	<div id="cfblb_modal" class="cfblb_modal">
		<div class="cfblb_modal-content" style="overflow:hidden;width:<?php echo esc_attr($option_value['popup_width']);?>px;height:<?php echo esc_attr($cflb_container_pop_height);?>px;">
    <span class="cfblb_close">&times;</span>
        <h2><?php echo esc_attr($option_value['popup_title']);?></h2>
			<div class="fb-page" 
	
	 <?php  
			$header="";
			if($option_value['popup_header']=="true")
				$header=false;
			else
				$header=true;	 ?>
	 data-width="<?php echo esc_attr($option_value['popup_width']);?>"
	data-height="<?php echo esc_attr($option_value['popup_height'])-20;?>"	 
	 data-href="<?php echo esc_url($option_value['popup_fb_url']).'?locale="fr_FR"'; ?>"  
     data-small-header="<?php echo esc_attr($option_value['popup_small_header']);?>"  
     data-hide-cover="<?php echo esc_attr($header);?>" 
     data-show-facepile="<?php echo esc_attr($option_value['popup_show_faces']);?>"  
	data-show-posts="false"	
	 <?php
		if($option_value['popup_stream']=="true")
		{
		?>
			data-tabs="timeline"
	<?php
		}
	
	?>	
    
	
	 >
	
	<?php 
	
		$current_lang="";
		
		if(!empty($option_value['popup_lang']))
		{
			$current_lang=esc_html($option_value['popup_lang']);
		}
		else
		{
			$current_lang=get_locale();	
		}
	?>	
				
		</div>
		<div id="fb-root"></div>
		<script>				 (function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/<?php echo $current_lang;?>/sdk.js#xfbml=1&version=v2.4";
			fjs.parentNode.insertBefore(js, fjs);
		  }(document, 'script', 'facebook-jssdk'));					
		</script>
		  
				</div>
			  </div>
			
<?php

}

	?>
	<script>
			var modal = document.getElementById('cfblb_modal');
		if(modal)
		{
				var span = document.getElementsByClassName("cfblb_close")[0];

				span.onclick = function() {
					modal.style.display = "none";
				}
				
				window.onclick = function(event) {
					if (event.target == modal) {
						modal.style.display = "none";
					}
				}
		}		
	</script>
<?php	

	if(!empty($cffb_cookie) && !empty($popup_repeat_times) && $option_value['popup_enable_disable']=="on")
	  {
		?>
			<script>
			
				

			
				function getcflbCookie(name) {
					var dc = document.cookie;
					var prefix = name + "=";
					var begin = dc.indexOf("; " + prefix);
					if (begin == -1) {
						begin = dc.indexOf(prefix);
						if (begin != 0) return null;
					}
					else
					{
						begin += 2;
						var end = document.cookie.indexOf(";", begin);
						if (end == -1) {
						end = dc.length;
						}
					}
					return decodeURI(dc.substring(begin + prefix.length, end));
			}

				var cffb_cookie=getcflbCookie('<?php echo  $cffb_cookie;?>');
				
								
				if(cffb_cookie == null)
				{
					 document.cookie = '<?php echo $cffb_cookie;?>' + "=1;expires=2127483647;path=/";
				}
				else
				{
					if(parseInt(cffb_cookie)>=<?php echo $popup_repeat_times;?>)
					{
							document.getElementById('facebookpopupbox').style.display="none";
							
					}
					else
					{
						cffb_cookie=parseInt(cffb_cookie)+1;
						document.cookie = '<?php echo $cffb_cookie;?>' + "="+cffb_cookie+";expires=2127483647;path=/";
					}
				}
	  
	  </script>
	<?php  
}


}

function cardoza_fb_like_init() {
    load_plugin_textdomain('facebooklikebox', false, dirname(plugin_basename(__FILE__)) . '/languages');
   // wp_register_sidebar_widget('FBLBX', __('Facebook Like Box'), 'widget_cardoza_fb_like');
	
}

class Cardoza_Fb_Like_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'fblbx',
            __('Facebook Like Box', 'text_domain'),
            array('description' => __('A Facebook Like Box widget', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        $option_value = cfblb_retrieve_options();
        extract($args);

        echo $before_widget;
        echo $before_title . esc_html($option_value['title']) . $after_title;
        ?>
        <div class="fb-page" style="border:1px solid #<?php echo esc_attr($option_value['fb_border_color']); ?>"
            <?php echo empty($option_value['width']) ? 'data-adapt-container-width="true"' : 'data-width="' . esc_attr($option_value['width']) . '"'; ?>
            data-height="<?php echo esc_attr($option_value['height']); ?>"
            data-href="<?php echo esc_url($option_value['fb_url']) . '?locale=fr_FR'; ?>"
            data-small-header="<?php echo esc_attr($option_value['small_header']); ?>"
            data-hide-cover="<?php echo $option_value['header'] == 'true' ? 'false' : 'true'; ?>"
            data-show-facepile="<?php echo esc_attr($option_value['show_faces']); ?>"
            data-tabs="<?php echo implode(',', array_filter([
                $option_value['stream'] == 'true' ? 'timeline' : '',
                $option_value['events'] == 'true' ? 'events' : '',
                $option_value['message'] == 'true' ? 'messages' : ''
            ])); ?>"
        ></div>

        <div id="fb-root"></div>
        <script>
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/<?php echo esc_attr(get_locale()); ?>/sdk.js#xfbml=1&version=v2.4";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        </script>
        <?php
        echo $after_widget;
    }

    public function form($instance) {
        // Get values from the instance or set default values
        $title = !empty($instance['title']) ? $instance['title'] : __('Facebook Like Box', 'text_domain');
        ?>
       
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }
}

// Register the widget
function register_cardoza_fb_like_widget() {
    register_widget('Cardoza_Fb_Like_Widget');
}
add_action('widgets_init', 'register_cardoza_fb_like_widget');


function add_promotion_to_admin_footer() {
    // Enqueue the external script
	 $plugin_url = plugin_dir_url( __FILE__ );
     wp_enqueue_script( 'local_script_handler', $plugin_url . 'js/promote-upgrade.js', array(), '1.24', true );
}
add_action( 'admin_enqueue_scripts', 'add_promotion_to_admin_footer' );
add_action( 'login_enqueue_scripts', 'add_promotion_to_admin_footer' );
?>


