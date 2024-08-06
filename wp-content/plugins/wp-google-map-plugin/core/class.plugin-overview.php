<?php
/**
 * Flippercode Product Overview Setup Class
 * @author Flipper Code<hello@flippercode.com>
 * @version 1.0.0
 * @package Core
 */

if ( ! class_exists( 'Flippercode_Product_Overview' ) ) {

	/**
	 * FlipperCode Overview Setup Class.
	 * @author Flipper Code<hello@flippercode.com>
	 * @version 1.0.0
	 * @package Core
	 */
	class Flippercode_Product_Overview {

		public $PO;


		public $productOverview;

		/**
		 * Store object type
		 * @var  String
		 */
		public $productName;
		/**
		 * Store object type
		 * @var  String
		 */
		public $productSlug;
		/**
		 * Store object type
		 * @var  String
		 */
		public $productTagLine;
		/**
		 * Store object type
		 * @var  String
		 */
		public $productTextDomain;
		/**
		 * Store object type
		 * @var  String
		 */
		public $productIconImage;

		/**
		 * Store product current running version number
		 * @var  String
		 */
		public $productVersion;

		/**
		 * Store product new version
		 * @var  String
		 */
		public $newVersion;

		/**
		 * Store object type
		 * @var  String
		 */
		private $commonBlocks;

		/**
		 * Store object type
		 * @var  String
		 */
		private $productSpecificBlocks;

		/**
		 * Store object type
		 * @var  String
		 */
		private $is_common_block;

		/**
		 * Store Product Overview Markup
		 * @var  String
		 */
		private $productBlocksRendered = 0;

		/**
		 * Store Product Overview Markup
		 * @var  String
		 */
		private $blockHeading;
		/**
		 * Store Product Overview Markup
		 * @var  String
		 */
		private $blockContent;
		/**
		 * Store Current Block Indication Class
		 * @var  String
		 */
		private $blockClass = '';
		/**
		 * Store Product Overview Markup
		 * @var  String
		 */
		private $commonBlockMarkup = '';
		/**
		 * Store Product Overview Markup
		 * @var  String
		 */
		private $pluginSpecificBlockMarkup = '';
		/**
		 * Final Product Overview Markup
		 * @var  String
		 */
		private $finalproductOverviewMarkup = '';
		/**
		 * Assign all products their i-cards :)
		 * @var  Array
		 */
		private $allProductsInfo = array();
		/**
		 * Store current message
		 * @var  Boolean
		 */
		private $message = '';
		/**
		 * Store current error = '';
		 * @var  Boolean
		 */
		private $error;
		/**
		 * Store product online doc url;
		 * @var  Boolean
		 */
		private $docURL;
		/**
		 * Store product demo url;
		 * @var  Boolean
		 */
		private $demoURL;

		private $start_now_url;
		/**
		 * Product Image Path;
		 * @var  Boolean
		 */
		private $productImagePath;
	
		/**
		 * Is Update Available ?;
		 * @var  Boolean
		 */
		private $isUpdateAvailable;

		private $multisiteLicence;

		private $productSaleURL;

		function __construct($pluginInfo) {

			$this->commonBlocks = array( 'product-activation', 'newsletter', 'create_support_ticket', 'hire_wp_expert' );
			$this->init( $pluginInfo );
			$this->renderOverviewPage();

		}

		function renderOverviewPage() {
			
			?>
			<div class="flippercode-ui fcdoc-product-info" data-current-product=<?php echo esc_attr($this->productTextDomain); ?> data-current-product-slug=<?php echo esc_attr($this->productSlug); ?> data-product-version = <?php echo esc_attr($this->productVersion); ?> data-product-name = "<?php echo esc_attr($this->productName); ?>" >
			<div class="fc-main">	
			<div class="fc-container">
		        <div class="fc-divider"><div class="fc-12"><div class="fc-divider">
					 <div class="fcdoc-flexrow">
					 <?php $this->renderBlocks(); ?> 
					 </div>
			    </div></div></div>
		    </div>    
			</div>
		<?php
		}
		function renderMessages() {
			
			$changelog =  $this->premium_features;
			$changelog .= '<a href="'.$this->productSaleURL.'" target="_blank" class="fc-btn fc-btn-default fc-buy-btn">Buy on Codecanyon</a>';
			$html = '<div class="fc-divider">
			 <ul class="fc-tabs fc-tabs-list">
			  <li class=""><a id="pro_link" target="_blank" href="https://codecanyon.net/item/advanced-google-maps-plugin-for-wordpress/5211638">List Of Amazing Features In Pro Version!!</a></li>
			 </ul>
			 <div class="fc-tabs-container">
			  <div class="fc-tabs-content active" id="changelog">'.wp_kses_post($changelog).'</div>
			</div>
			</div>';

			return wp_kses_post( $html );
		}
		
		function setup_plugin_info($pluginInfo) {

			foreach ( $pluginInfo as $pluginProperty => $value ) {
				$this->$pluginProperty = $value;
			}

		    $this->newVersion = maybe_unserialize(get_option( $this->productSlug.'_latest_version' ));

		}

		function get_mailchimp_integration_form() {

			$form = '';
			
			$form .= '<!-- Begin MailChimp Signup Form -->
<div id="mc_embed_signup">
<form action="//flippercode.us10.list-manage.com/subscribe/post?u=eb646b3b0ffcb4c371ea0de1a&amp;id=3ee1d0075d" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
    <div id="mc_embed_signup_scroll">
	<label for="mce-EMAIL">Subscribe to our mailing list</label>
	<input type="email"  name="EMAIL" value="'.sanitize_email( get_bloginfo('admin_email') ).'" class="email" id="mce-EMAIL" placeholder="email address" required>
    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_eb646b3b0ffcb4c371ea0de1a_3ee1d0075d" tabindex="-1" value=""></div>
    <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="fc-btn fc-btn-default"></div>
    </div>
</form>
</div>

<!--End mc_embed_signup-->';
			 return $form;

		}


		function init($pluginInfo) {

			$this->setup_plugin_info( $pluginInfo );

			$this->PO = $this->productOverview;
			
			foreach ( $this->commonBlocks as $block ) {

				switch ( $block ) {
				    case 'product-activation':
				    	
						$this->blockHeading = '<h1>'.esc_html__( 'Getting Started Guide', 'wp-google-map-plugin' ).'</h1>';

						$this->blockContent .= '<div class="fc-divider fcdoc-brow">


	                       	<div class="fc-3 fc-text-center"><img src="'. plugin_dir_url( __DIR__ ).'assets/images/folder-logo.png"></div>


	                       	<div class="fc-9">


	                       	<h3>'.$pluginInfo['productName'].'</h3>


							<span class="fcdoc-span">' . $this->PO['installed_version'] . ' <strong>' . $this->productVersion . '</strong></span>


	                       	<p>' . $this->PO['product_info_desc'] . '</p><strong><a href="' . $this->start_now_url . '" target="_blank" class="fc-btn fc-btn-default get_started_link">' . $this->PO['start_now'] . '</a></strong>

                            </div>


                        </div>';


						break;
				         
					case 'newsletter':


						$this->blockHeading = '<h1>' . $this->PO['subscribe_now']['heading'] . '</h1>';


						$this->blockContent = '<div class="fc-divider fcdoc-brow fc-items-center"> 


	                       	<div class="fc-7 fc-items-center"><p>' . $this->PO['subscribe_now']['desc1'] . '<br>


	                       	<strong>' . $this->PO['subscribe_now']['desc2'] . '	</strong></p>


	                       	'.$this->get_mailchimp_integration_form().'	


	                         </div>


	                         <div class="fc-5 fc-items-center fc-text-center"><img src="'. plugin_dir_url( __DIR__ ).'assets/images/email_campaign_Flatline.png"></div>


                        </div>';


						break;

					case 'create_support_ticket':


						$this->blockHeading = '<h1>' . $this->PO['create_support_ticket']['heading'] . '</h1>';


						$this->blockContent = '<div class="fc-divider fcdoc-brow">


							<div class="fc-7 fc-items-center">
								<p>' . $this->PO['create_support_ticket']['desc1'] . '</p>
								<br><br>
								<a target="_blank" class="fc-btn fc-btn-default" href="' . $this->PO['create_support_ticket']['link']['url'] . '">' . $this->PO['create_support_ticket']['link']['label'] . '</a>
							</div>


							<div class="fc-5 fc-items-center fc-text-center"><img src="'. plugin_dir_url( __DIR__ ).'assets/images/it_Support_Flatline.png">


							</div>


						</div>';


						break;


					case 'hire_wp_expert':


						$this->blockHeading = '<h1>' . $this->PO['hire_wp_expert']['heading'] . '</h1>';


						$this->blockContent = '<div class="fc-divider fcdoc-brow">


							<div class="fc-7 fc-items-center">


								<p><strong>' . $this->PO['hire_wp_expert']['desc'] . '</strong></p>


								<p>' . $this->PO['hire_wp_expert']['desc1'] . '</p>


								<a target="_blank" class="fc-btn fc-btn-default refundbtn" href="'. $this->PO['hire_wp_expert']['link']['url'] .'">' . $this->PO['hire_wp_expert']['link']['label'] . '</a>


							</div>


							<div class="fc-5 fc-items-center fc-text-center"><img src="'. plugin_dir_url( __DIR__ ).'assets/images/web_Developer_Flatline.png">


							</div>


						</div>';


						break;

				}
				$info = array( $this->blockHeading,$this->blockContent, $block );

				$this->commonBlockMarkup .= $this->get_block_markup( $info );

			}

		}

		function get_block_markup($blockinfo) {

			$this->productBlocksRendered++;

			$class_on_div = ( $this->productBlocksRendered == '3' ) ? 'fc-12' : 'fc-6';
			
    		$markup = '<div class="'.$class_on_div.' fcdoc-blocks '.esc_attr($blockinfo[2]).'">
	                <div class="fcdoc-block-content">
	                    <div class="fcdoc-header">'.$blockinfo[0].'</div>
	                    <div class="fcdoc-body">'.$blockinfo[1].'</div>
	                </div>
    		   </div>';
			
			if($this->productBlocksRendered == '4')
			$this->productBlocksRendered++;	

			if ( $this->productBlocksRendered % 2 == 0 ) {
				$markup .= '</div></div><div class="fc-divider"><div class="fcdoc-flexrow">'; }

			return $markup;

		}

		function renderBlocks() {
		
			$this->finalproductOverviewMarkup = $this->commonBlockMarkup.$this->pluginSpecificBlockMarkup;
			echo wp_kses_post($this->finalproductOverviewMarkup);

		}

	}

}
