<?php
/*
Plugin Name: Mechanic Visitor Counter
Plugin URI: https://www.adityasubawa.com/mechanic-visitor-counter/
Description: Mechanic Visitor Counter is a widgets which will display the Visitor counter and traffic statistics on WordPress.
Version: 3.3.3
Author: Aditya Subawa
Author URI: https://www.adityasubawa.com
*/
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
   
// load local language since v3.1
add_action('plugins_loaded', 'statsmechanic_load_textdomain');
function statsmechanic_load_textdomain() {
	load_plugin_textdomain( 'wp-statsmechanic', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

global $wpdb;
define('BMW_TABLE_NAME', $wpdb->prefix . 'mech_statistik');
define('BMW_PATH', ABSPATH . 'wp-content/plugins/mechanic-visitor-counter');
require_once(ABSPATH . 'wp-includes/pluggable.php');

function install(){
global $wpdb;
if ( $wpdb->get_var('SHOW TABLES LIKE "' . BMW_TABLE_NAME . '"') != BMW_TABLE_NAME )
{
$sql = "CREATE TABLE IF NOT EXISTS `". BMW_TABLE_NAME . "` (";
$sql .= "`ip` varchar(20) NOT NULL default '',";
$sql .= "`tanggal` date NOT NULL,";
$sql .= "`hits` int(10) NOT NULL default '1',";
$sql .= "`online` varchar(255) NOT NULL,";
$sql .= "PRIMARY KEY  (`ip`,`tanggal`)";
$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1;";
$wpdb->query($sql);
 }
}
	 
function uninstall(){
global $wpdb;
$sql = "DROP TABLE `". BMW_TABLE_NAME . "`;";
$wpdb->query($sql);
}

function acak($path, $exclude = ".|..|.svn|.DS_Store", $recursive = true) {
    $path = rtrim($path, "/") . "/";
    $folder_handle = opendir($path) or die("Eof");
    $exclude_array = explode("|", $exclude);
    $result = array();
    while(false !== ($filename = readdir($folder_handle))) {
        if(!in_array(strtolower($filename), $exclude_array)) {
            if(is_dir($path . $filename . "")) {
                if($recursive) $result[] = acak($path . $filename . "", $exclude, true);
            } else {
                if ($filename === '0.gif') {
                    if (!$done[$path]) {
                        $result[] = $path;
                        $done[$path] = 1;
                    }
                }
            }
        }
    }
    return $result;
}
register_activation_hook(__FILE__, 'install');
register_deactivation_hook(__FILE__, 'uninstall');

                                  
class Wp_StatsMechanic extends WP_Widget{
    
    function __construct(){
	 $params=array(
            'description' => 'Display Visitor Counter and Statistics Traffic', //plugin description
            'name' => 'Mechanic - Visitor Counter'  //title of plugin
        );
        
        parent::__construct('WP_StatsMechanic', '', $params);
    }
       
  // extract($instance);
	 public function form($instance)  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];


?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','wp-statsmechanic');?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<p><label for="<?php echo $this->get_field_id('font_color'); ?>"><?php _e('Font Color:','wp-statsmechanic');?> <input class="widefat" id="<?php echo $this->get_field_id('font_color'); ?>" name="<?php echo $this->get_field_name('font_color'); ?>" type="text" value="<?php echo $instance['font_color']; ?>" /></label></p>
<p><font size='2'><?php _e('To change the font color, fill the field with the HTML color code. example: #333','wp-statsmechanic');?> </font></p>
<p><font size='2'><a href="https://www.adityasubawa.com/color-picker/" target="_blank"><?php _e('Click here</a> to select another color variation.', 'wp-statsmechanic');?></font></p>
<p><font size='3'><b><?php _e('Widget Options', 'wp-statsmechanic');?></b></font></p>

<p><label for="<?php echo $this->get_field_id('count_start'); ?>"><?php _e('Counter Start:','wp-statsmechanic');?> <input class="widefat" id="<?php echo $this->get_field_id('count_start'); ?>" name="<?php echo $this->get_field_name('count_start'); ?>" type="text" value="<?php echo $instance['count_start']; ?>" /></label></p>
<p><font size='2'><?php _e('Fill in with numbers to start the initial calculation of the counter, if the empty counter will start from 1','wp-statsmechanic');?></font></p>
<p><label for="<?php echo $this->get_field_id('hits_start'); ?>"><?php _e('Hits Start:','wp-statsmechanic');?> <input class="widefat" id="<?php echo $this->get_field_id('hits_start'); ?>" name="<?php echo $this->get_field_name('hits_start'); ?>" type="text" value="<?php echo $instance['hits_start']; ?>" /></label></p>
<p><font size='2'><?php _e('Fill in the numbers to start the initial calculation of the hits, if the empty hits will start from 1','wp-statsmechanic'); ?></font></p>

<p><label for="<?php echo $this->get_field_id('count_length'); ?>"><?php _e('Image Counter Length:','wp-statsmechanic');?><select class="select" id="<?php echo $this->get_field_id('count_length'); ?>" name="<?php echo $this->get_field_name('count_length'); ?>" selected="<?php echo $instance['count_length']; ?>">
		  <option value="<?php echo $instance['count_length']; ?>" selected><?php echo $instance['count_length']; ?></option>
		  <option value="4">4</option>
		  <option value="5">5</option>
		  <option value="6">6</option>
		  <option value="7">7</option>
		 </select></label></p>
<p><font size='2'><?php _e('Define your Image counter length, the default length is 4','wp-statsmechanic');?></font></p>

<p><label for="<?php echo $this->get_field_id('today_view'); ?>"><?php _e('Enable Visit Today display? ', 'wp-statsmechanic'); ?><input type="checkbox" class="checkbox" <?php checked( $instance['today_view'], 'on' ); ?> id="<?php echo $this->get_field_id('today_view'); ?>" name="<?php echo $this->get_field_name('today_view'); ?>" /></label></p>
<p><label for="<?php echo $this->get_field_id('yesterday_view'); ?>"><?php _e('Enable Visit Yesterday display? ', 'wp-statsmechanic'); ?><input type="checkbox" class="checkbox" <?php checked( $instance['yesterday_view'], 'on' ); ?> id="<?php echo $this->get_field_id('yesterday_view'); ?>" name="<?php echo $this->get_field_name('yesterday_view'); ?>" /></label></p>
<p><label for="<?php echo $this->get_field_id('month_view'); ?>"><?php _e('Enable Month display? ', 'wp-statsmechanic'); ?><input type="checkbox" class="checkbox" <?php checked( $instance['month_view'], 'on' ); ?> id="<?php echo $this->get_field_id('month_view'); ?>" name="<?php echo $this->get_field_name('month_view'); ?>" /></label></p>
<p><label for="<?php echo $this->get_field_id('year_view'); ?>"><?php _e('Enable Year display? ', 'wp-statsmechanic'); ?><input type="checkbox" class="checkbox" <?php checked( $instance['year_view'], 'on' ); ?> id="<?php echo $this->get_field_id('year_view'); ?>" name="<?php echo $this->get_field_name('year_view'); ?>" /></label></p>
<p><label for="<?php echo $this->get_field_id('total_view'); ?>"><?php _e('Enable Total Visit display? ', 'wp-statsmechanic'); ?><input type="checkbox" class="checkbox" <?php checked( $instance['total_view'], 'on' ); ?> id="<?php echo $this->get_field_id('total_view'); ?>" name="<?php echo $this->get_field_name('total_view'); ?>" /></label></p>
<p><label for="<?php echo $this->get_field_id('hits_view'); ?>"><?php _e('Enable Hits Today display? ', 'wp-statsmechanic'); ?><input type="checkbox" class="checkbox" <?php checked( $instance['hits_view'], 'on' ); ?> id="<?php echo $this->get_field_id('hits_view'); ?>" name="<?php echo $this->get_field_name('hits_view'); ?>" /></label></p>
<p><label for="<?php echo $this->get_field_id('totalhits_view'); ?>"><?php _e('Enable Total Hits display? ', 'wp-statsmechanic'); ?><input type="checkbox" class="checkbox" <?php checked( $instance['totalhits_view'], 'on' ); ?> id="<?php echo $this->get_field_id('totalhits_view'); ?>" name="<?php echo $this->get_field_name('totalhits_view'); ?>" /></label></p>
<p><label for="<?php echo $this->get_field_id('online_view'); ?>"><?php _e('Enable Whos Online display? ', 'wp-statsmechanic'); ?><input type="checkbox" class="checkbox" <?php checked( $instance['online_view'], 'on' ); ?> id="<?php echo $this->get_field_id('online_view'); ?>" name="<?php echo $this->get_field_name('online_view'); ?>" /></label></p>
<p><label for="<?php echo $this->get_field_id('ip_display'); ?>"><?php _e('Enable IP address display? ', 'wp-statsmechanic'); ?><input type="checkbox" class="checkbox" <?php checked( $instance['ip_display'], 'on' ); ?> id="<?php echo $this->get_field_id('ip_display'); ?>" name="<?php echo $this->get_field_name('ip_display'); ?>" /></label></p>
<p><label for="<?php echo $this->get_field_id('server_time'); ?>"><?php _e('Enable Server Time display? ', 'wp-statsmechanic'); ?><input type="checkbox" class="checkbox" <?php checked( $instance['server_time'], 'on' ); ?> id="<?php echo $this->get_field_id('server_time'); ?>" name="<?php echo $this->get_field_name('server_time'); ?>" /></label></p>
<p><label for="<?php echo $this->get_field_id('statsmechanic_align'); ?>"><?php _e('Plugins align? ', 'wp-statsmechanic'); ?>
		<select class="select" id="<?php echo $this->get_field_id('statsmechanic_align'); ?>" name="<?php echo $this->get_field_name('statsmechanic_align'); ?>" selected="<?php echo $instance['statsmechanic_align']; ?>">
		  <option value="<?php echo $instance['statsmechanic_align']; ?>" selected><?php echo $instance['statsmechanic_align']; ?></option>
		  <option value="Left">Left</option>
		  <option value="Center">Center</option>
		  <option value="Right">Right</option>
		 </select></label></p>

<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ZMEZEYTRBZP5N&lc=ID&item_name=Aditya%20Subawa&item_number=426267&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted" target="_blank"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" alt="<?php _e('Donate', 'wp-statsmechanic') ?>" /></a></p>
<?php

  }

    public function widget($args, $instance){
        extract($args, EXTR_SKIP);
    
	
	
	echo $before_widget;
    $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
	
 
    if (!empty($title))
      echo $before_title . $title . $after_title;

	$ipaddress = isset($instance['ip_display']) ? $instance['ip_display'] : false ; // display ip address
	$stime = isset($instance['server_time']) ? $instance['server_time'] : false ; // display server time
	$fontcolor= $instance['font_color'];
	$count_length = $instance['count_length'];
	$style = get_option ('statsmechanic_style');
	$align = $instance['statsmechanic_align'];
	$todayview = $instance ['today_view'];
	$yesview = $instance ['yesterday_view'];
	$monthview = $instance ['month_view'];
	$yearview = $instance ['year_view'];
	$totalview = $instance ['total_view'];
	$hitsview = $instance ['hits_view'];
	$totalhitsview = $instance ['totalhits_view'];
	$onlineview = $instance ['online_view'];
	$count_start = $instance ['count_start'];
	$hits_start = $instance ['hits_start'];
	
	 		  $ip      = $_SERVER['REMOTE_ADDR']; // Getting the user's computer IP
              $tanggal = date("y-m-d"); // Getting the current date
              $waktu  = time();  
  			  $bln=date("m");  
   			  $tgl=date("d");  
              $blan=date("Y-m");  
              $thn=date("Y");  
              $tglk=$tgl-1;  
			  global $wpdb;
              // Check your IP, whether the user has had access to today's 
              $sql = $wpdb->query("SELECT * FROM `". BMW_TABLE_NAME . "` WHERE ip='$ip' AND tanggal='$tanggal'");
              // If not there, save the user data to the database
              if( $wpdb->get_results($sql) == 0){
                $wpdb->query("INSERT INTO `". BMW_TABLE_NAME . "`(ip, tanggal, hits, online) VALUES('$ip','$tanggal','1','$waktu')");
              } 
              else{
                 $wpdb->query("UPDATE `". BMW_TABLE_NAME . "` SET hits=hits+1, online='$waktu' WHERE ip='$ip' AND tanggal='$tanggal'");
              }
			  //yesterday by ip
			  if($tglk=='1' | $tglk=='2' | $tglk=='3' | $tglk=='4' | $tglk=='5' | $tglk=='6' | $tglk=='7' | $tglk=='8' | $tglk=='9'			){  
    		  $kemarin1=$wpdb->query("SELECT * FROM `". BMW_TABLE_NAME . "` WHERE tanggal='$thn-$bln-$tglk'");  
     		  } else {  
    		  $kemarin1=$wpdb->query("SELECT * FROM `". BMW_TABLE_NAME . "` WHERE tanggal='$thn-$bln-$tglk'");  
    		  }  
			  //this month by ip
			  $bulan1=$wpdb->query("SELECT * FROM `". BMW_TABLE_NAME . "` WHERE tanggal LIKE '%$blan%'");  
    		  //this year by ip
			  $tahunini1=$wpdb->query("SELECT * FROM `". BMW_TABLE_NAME . "` WHERE tanggal LIKE '%$thn%'"); 
			  // visitor today by ip
    		  $pengunjung       = $wpdb->query("SELECT * FROM `". BMW_TABLE_NAME . "` WHERE tanggal='$tanggal' GROUP BY ip");
			  // total visitor by ip
              $totalpengunjung  = $wpdb->get_var("SELECT COUNT(hits) FROM `". BMW_TABLE_NAME . "`"); 
			  // hits Today
              $hits             = $wpdb->get_var("SELECT SUM(hits) FROM `". BMW_TABLE_NAME . "` WHERE tanggal='$tanggal' GROUP BY tanggal"); //masih gagal
			  // total hits
			  $totalhits        = $wpdb->get_var("SELECT SUM(hits) FROM `". BMW_TABLE_NAME . "`");  
			  // unique visitor by ip
			  $tothitsgbr      = $wpdb->get_var("SELECT COUNT(hits) FROM `". BMW_TABLE_NAME . "`"); 
              // whos online
			  $bataswaktu       = time() - 300;
              $pengunjungonline = $wpdb->query("SELECT * FROM `". BMW_TABLE_NAME . "` WHERE online > '$bataswaktu'");
			  $ext = ".gif";
			 // image print
			 // thnks to Jack All https://wordpress.org/support/profile/jack-all
			if ($count_length==NULL){
				$new_count_length = '4';
			}else{
				$new_count_length = $count_length;
			}
			if ($count_start==NULL) { 
			  $tothitsgbr = sprintf("%0".$new_count_length."d", $tothitsgbr);
			  $tothitsstring = "";
			  $arr = str_split($tothitsgbr);
				foreach ($arr as $value) {
					$tothitsstring = $tothitsstring . "<img src='". WP_PLUGIN_URL ."/mechanic-visitor-counter/styles/$style/$value$ext' alt='$value'>";
				}
				$tothitsgbr = $tothitsstring;
			}else{
			  $tothitsgbr = sprintf("%07d", $tothitsgbr + $count_start);
			  $tothitsstring = "";
			  $arr = str_split($tothitsgbr);
				foreach ($arr as $value) {
					$tothitsstring = $tothitsstring . "<img src='". WP_PLUGIN_URL ."/mechanic-visitor-counter/styles/$style/$value$ext' alt='$value'>";
				}
				$tothitsgbr = $tothitsstring;
			  
			}
		     
			   	    //image
			  		$imgvisit= "<img src='".plugins_url ('counter/mvcvisit.png' , __FILE__ ). "'>";
					$yesterday="<img src='".plugins_url ('counter/mvcyesterday.png' , __FILE__ ). "'>";
					$month="<img src='".plugins_url ('counter/mvcmonth.png' , __FILE__ ). "'>";
					$year="<img src='".plugins_url ('counter/mvcyear.png' , __FILE__ ). "'>";
					$imgtotal="<img src='".plugins_url ('counter/mvctotal.png' , __FILE__ ). "'>";
					$imghits="<img src='".plugins_url ('counter/mvctoday.png' , __FILE__ ). "'>";
					$imgtotalhits="<img src='".plugins_url ('counter/mvctotalhits.png' , __FILE__ ). "'>";
					$imgonline="<img src='" .plugins_url ('counter/mvconline.png' , __FILE__ ). "'>";
					//style and widgetne
					
                    echo "<link rel='stylesheet' type='text/css' href='". WP_PLUGIN_URL ."/mechanic-visitor-counter/styles/css/default.css' />";
					?>
<div id='mvcwid' style='font-size:2; text-align:<?php echo $align ?>;color:<?php echo $fontcolor ?>;'>
	<div id="mvccount"><?php echo $tothitsgbr ?></div>
	<div id="mvctable">
        	<table width='100%'>
            <?php if ($todayview) { ?>
            <tr><td style='font-size:2; text-align:<?php echo $align ?>;color:<?php echo $fontcolor ?>;'><?php echo $imgvisit ?> <?php _e('Visit Today :', 'wp-statsmechanic') ?> <?php echo $pengunjung ?></td></tr>
            <?php } ?>
            <?php if ($yesview) { ?>
            <tr><td style='font-size:2; text-align:<?php echo $align ?>;color:<?php echo $fontcolor ?>;'><?php echo $yesterday ?> <?php _e('Visit Yesterday :', 'wp-statsmechanic');?> <?php echo $kemarin1 ?></td></tr>
            <?php } ?>
            <?php if ($monthview) { ?>
            <tr><td style='font-size:2; text-align:<?php echo $align ?>;color:<?php echo $fontcolor ?>;'><?php echo $month ?> <?php _e('This Month :', 'wp-statsmechanic'); ?> <?php echo $bulan1 ?></td></tr>
            <?php } ?>
            <?php if ($yearview) { ?>
            <tr><td style='font-size:2; text-align:<?php echo $align ?>;color:<?php echo $fontcolor ?>;'><?php echo $year ?> <?php _e('This Year :', 'wp-statsmechanic'); ?> <?php echo $tahunini1 ?></td></tr>
            <?php } ?>
			<?php if ($totalview) { ?>
            <tr><td style='font-size:2; text-align:<?php echo $align ?>;color:<?php echo $fontcolor ?>;'><?php echo $imgtotal ?> <?php _e('Total Visit :', 'wp-statsmechanic');?> <?php echo $totalpengunjung + $count_start ?></td></tr>
            <?php } ?>
            <?php if ($hitsview) { ?>
            <tr><td style='font-size:2; text-align:<?php echo $align ?>;color:<?php echo $fontcolor ?>;'><?php echo $imghits ?> <?php _e('Hits Today :', 'wp-statsmechanic');?> <?php echo $hits ?></td></tr>
            <?php } ?>
            <?php if ($totalhitsview) { ?>
            <tr><td style='font-size:2; text-align:<?php echo $align ?>;color:<?php echo $fontcolor ?>;'><?php echo $imgtotalhits ?> <?php _e('Total Hits :','wp-statsmechanic');?> <?php if ($hits_start==NULL) { 
					echo $totalhits ;
			}else{
				$totalhitsfake = $totalhits + $hits_start;
				echo $totalhitsfake;
			}?></td></tr>
            <?php } ?>
            <?php if ($onlineview) { ?>
            <tr><td style='font-size:2; text-align:<?php echo $align ?>;color:<?php echo $fontcolor ?>;'><?php echo $imgonline ?> <?php _e("Who's Online :", 'wp-statsmechanic');?> <?php echo $pengunjungonline ?></td></tr>
            <?php } ?>
            </table>
    	</div>
        <?php if ($ipaddress) { ?>
        <div id="mvcip"><?php _e('Your IP Address:', 'wp-statsmechanic'); ?> <?php echo $ip ?></div>
        <?php } ?>
		<?php if ($stime) { ?>
        <div id="mvcserver"><?php _e('Server Time:', 'wp-statsmechanic'); ?> <?php echo $tanggal ?></div>
        <?php } ?>
       
 </div> 
            <?php
	echo $after_widget;
  }}
add_action('widgets_init', 'register_wp_statsmechanic');
// Shortcode
// source: https://digwp.com/2010/04/call-widget-with-shortcode/
function mvc_shortcode($atts) { 
 
    global $wp_widget_factory;
    
    extract(shortcode_atts(array(
        'widget_name' => 'Wp_StatsMechanic',
        'instance'    => ''
    ), $atts));
       
    $widget_name = wp_specialchars($widget_name);
    $instance = str_ireplace("&amp;", '&' ,$instance);
    
    ob_start();
    the_widget($widget_name, $instance, array('widget_id'=>'arbitrary-instance-'.$id,
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
 
} 
	add_shortcode('mechanic_visitor', 'mvc_shortcode');
function register_wp_statsmechanic() {
register_widget('Wp_StatsMechanic', 'statsmechanic_style');
}	
//ADMIN OPTIONS
add_action('admin_menu', 'statsmechanic_menu');
function statsmechanic_menu() {
register_setting('plugin_statsmechanic_menu', 'statsmechanic_style');
add_options_page('Plugin Stats Mechanic', 'Visitor Counter Options', 1, 'plugin_statsmechanic_menu', 'statsmechanic_options');
}
function statsmechanic_options() {
if (!current_user_can('administrator'))  {
wp_die( __('You do not have sufficient permissions to access this page.') );
}
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
    <h2>Plugin Options Mechanic Visitor Counter</h2><br/>
    <div class="mvc_plugins_wrap"><!-- start mvc wrap -->
     <div class="mvc_right_sidebar"><!-- start right sidebar -->
		<div class="mvc_plugins_text">
        	<div class="mvc_option_wrap">
        <h3 class="hndle">Donate</h3>
		<p>If you like and helped with my plugins, please donate to the developer. how much your nominal will help developers to develop these plugins.</p><br/>
<p style="margin:-48px 0px 0px 10px;"><form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/id_ID/i/scr/pixel.gif" width="1" height="1">
</form>
</p>
			</div>
		</div>
        <!-- Support Banner -->
        <div class="mvc_plugins_text">
        
		</div>
        <!-- Sidebar Space -->
       
    </div><!-- End Right sidebar -->
    <div class="mvc_left_sidebar"><!-- start Left sidebar -->
    <div class="mvc_plugins_text">
    <div class="mvc_option_wrap">
	<h3 class="hndle">Join my mailing list</h3>
<p>Join my mailing list for tips, tricks, and Website secrets.</p>
<form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open("http://feedburner.google.com/fb/a/mailverify?uri=adityasubawa", "popupwindow", "scrollbars=yes,width=550,height=520");return true">
  <p>Enter your email address: 
    <input type="text" style="width:140px" name="email"/> 
    <input type="hidden" value="adityasubawa" name="uri"/>
    <input type="hidden" name="loc" value="en_US"/>
    <input type="submit" value="Subscribe" />
  </p>
  </form>
  </div>
  </div>
  <div class="mvc_option_wrap">
  <div class="mvc_plugins_text">
<h3 class="hndle">Image Counter</h3>
<form method="post" action="options.php">

<?php settings_fields( 'plugin_statsmechanic_menu' ); ?>
       <?php
            $data = acak(WP_CONTENT_DIR . '/plugins/mechanic-visitor-counter/styles/');
            foreach ($data as $parent_folder => $records) {
                foreach ($records as $style_folder => $style_records) {
                    foreach ($style_records as $style => $test) {
                        preg_match('/styles\/(.*?)\/(.*?)\//', $test, $match);
                        $groups[$match[1]][] = $match[2];
                    }
                }
            }
        ?>
		  <?php
            foreach ($groups as $style_name => $style) {
?>
					
 					<p><b>Choose one of the <?php echo $style_name; ?> counter styles below:</b></p>
						<table class="form-table">
						<?php
                foreach ($style as $name) {
                    ?>
                    	<tr>
                		<td>
                		<input type="radio" id="img1" name="statsmechanic_style" value="<?php echo $style_name . '/' . $name; ?>" <?php echo checked($style_name . '/' . $name, get_option ('statsmechanic_style')) ?> />
                		<img src='<?php echo WP_PLUGIN_URL?>/mechanic-visitor-counter/styles/<?php echo $style_name . '/' . $name . '/'; ?>0.gif'>
                		<img src='<?php echo WP_PLUGIN_URL?>/mechanic-visitor-counter/styles/<?php echo $style_name . '/' . $name . '/'; ?>1.gif'>
                		<img src='<?php echo WP_PLUGIN_URL?>/mechanic-visitor-counter/styles/<?php echo $style_name . '/' . $name . '/'; ?>2.gif'>
                		<img src='<?php echo WP_PLUGIN_URL?>/mechanic-visitor-counter/styles/<?php echo $style_name . '/' . $name . '/'; ?>3.gif'>
                		<img src='<?php echo WP_PLUGIN_URL?>/mechanic-visitor-counter/styles/<?php echo $style_name . '/' . $name . '/'; ?>4.gif'>
                		</td>
                	</tr>
					  <?php
                }
			?>
          
		  </table>
         
<?php
            }
        ?>    
        <p style="margin-top:20px;" >
        <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'wp-statsmechanic') ?>" />
        </p>
</form>
</div>
</div>
</div><!-- End Left sidebar -->
</div><!-- End mvc wrap -->
</div>
<style type="text/css">
/*ADMIN STYLING*/
.form-table {
	clear: none;
}
.form-table td {
	vertical-align: top;
	padding: 16px 20px 5px;
	line-height: 10px;
	font-size: 12px;
}
.form-table th {
	width: 200px;
	padding: 10px 0 12px 9px;
}
.mvc_right_sidebar {
	width: 42%;
	float: right;
}
.mvc_left_sidebar {
	width: 55%;
	margin-left: 10px;
}
.mvc_plugins_text {
	margin-bottom: 0px;
}
.mvc_plugins_text p {
	padding: 5px 10px 10px 10px;
	width: 90%;
}
.mvc_plugins_text h2 {
	font-size: 14px;
	padding: 0px;
	font-weight: bold;
	line-height: 29px;
}
.mvc_plugins_wrap .hndle {
	font-size: 15px;
	font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
	font-weight: normal;
	padding: 7px 10px;
	margin: 0;
	line-height: 1;
	border-top-left-radius: 3px;
	border-top-right-radius: 3px;
	border-bottom-color: rgb(223, 223, 223);
    text-shadow: 0px 1px 0px rgb(255, 255, 255);
    box-shadow: 0px 1px 0px rgb(255, 255, 255);
	background: linear-gradient(to top, rgb(236, 236, 236), rgb(249, 249, 249)) repeat scroll 0% 0% rgb(241, 241, 241);
	margin-top: 1px;
	border-bottom-width: 1px;
	border-bottom-style: solid;
	-moz-user-select: none;
}
.mvc_option_wrap {
	border:1px solid rgb(223, 223, 223);
	width:100%;
	margin-bottom:30px;
	height:auto;
}

</style>
<?php
}
     /**
     * Compatibility check for hosting php version.
     * Returns error if php version is below v5.4
     * @author      Aditya Subawa
     * @copyright   CV. Bali Mechanic Media (c)
     * @link        http://www.adityasubawa.com
     * @since       Version 3.1
     * @last_update Version 3.1
     */
    function statsmechanic_admin_notice__error() {
		if ( version_compare( phpversion(), '5.4', '<' ) ) {
		$class = 'notice notice-error';
		$message = _e( 'Your PHP version must be above V5.4. Mechanic visitor counter plugin no longer support php legacy versions (v5.2.X, v5.3.X). Your current PHP version is <b>' . phpversion().'</b>.', 'wp-statsmechanic' );

		printf( '<div id="message" class="%1$s is-dismissable"><p>%2$s</p></div>', $class, $message ); 
		//wp_die();
		deactivate_plugins( '/mechanic-visitor-counter/wp-statsmechanic.php' );
		}
	}
	add_action( 'admin_notices', 'statsmechanic_admin_notice__error' );

?>
