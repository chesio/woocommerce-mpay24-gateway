<?php
/*
	Plugin Name: WooCommerce mPAY24 Gateway
	Plugin URI: http://wordpress.org/plugins/woocommerce-mpay24-gateway/
	Description: Add mPAY24 Payment Gateway to WooCommerce Plugin
	Version: 2.1.0.beta
	Author: datenwerk innovationsagentur GmbH
	Author URI: http://www.datenwerk.at
	Requires at least: 3.5
	Tested up to: 4.5.4
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if WooCommerce is active
 **/
if (in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	if ( ! defined( 'GATEWAY_MPAY24_VERSION' ) ) {
		define( 'GATEWAY_MPAY24_VERSION', '2.1.0.beta' );
	}
	if ( ! defined( 'GATEWAY_MPAY24_TABLE_NAME' ) ) {
		define( 'GATEWAY_MPAY24_TABLE_NAME', 'woocommerce_mpay24_transaction' );
	}
	if ( ! defined( 'GATEWAY_MPAY24_URL' ) ) {
		define( 'GATEWAY_MPAY24_URL', plugin_dir_url( __FILE__ ) ); // with trailing slash
	}
	if ( ! defined( 'GATEWAY_MPAY24_PATH' ) ) {
		define( 'GATEWAY_MPAY24_PATH', plugin_dir_path( __FILE__ ) ); // with trailing slash
	}

	/**
	 * Localisation
	 */
	load_plugin_textdomain( 'wc-mpay24', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	/**
	 * Installation (create table)
	 */
	register_activation_hook( __FILE__, 'woocommerce_install_gateway_mpay24' );

	function woocommerce_install_gateway_mpay24() {
		require_once( GATEWAY_MPAY24_PATH . 'admin/gateway-mpay24-install.php' );
		wc_gateway_mpay24_install();
		update_option( 'gateway_mpay24_version', GATEWAY_MPAY24_VERSION );
	}

	/**
	 * Uninstallation (remove table and options)
	 */
	register_uninstall_hook( __FILE__, 'woocommerce_uninstall_gateway_mpay24' );

	function woocommerce_uninstall_gateway_mpay24() {
		require_once( GATEWAY_MPAY24_PATH . 'admin/gateway-mpay24-install.php' );
		wc_gateway_mpay24_uninstall();
	}

	/**
	 * Install check (for updates)
	 */
	add_action( 'plugins_loaded', 'woocommerce_check_dbupdate_gateway_mpay24' );

	function woocommerce_check_dbupdate_gateway_mpay24() {
		if ( get_option( 'gateway_mpay24_version' ) < GATEWAY_MPAY24_VERSION ) {
			woocommerce_install_gateway_mpay24();
		}
	}

	/**
	 * Welcome notices
	 */
	if ( '' == get_option( 'hide_gateway_mpay24_welcome_notice' ) ) {
		add_action( 'admin_notices', 'woocommerce_welcome_notice_gateway_mpay24' );
	}

	function woocommerce_welcome_notice_gateway_mpay24() {
		global $woocommerce;

		wp_enqueue_style( 'woocommerce-activation', $woocommerce->plugin_url() . '/assets/css/activation.css' );
		?>
		<div id="message" class="updated woocommerce-message wc-connect">
			<?php if( version_compare( $woocommerce->version, '2.1', '<' ) ): ?>
			<div class="squeezer">
				<h4><?php _e( '<strong>mPAY24 gateway is installed</strong> &#8211; Configure the setting to get started :)', 'wc-mpay24' ); ?></h4>
				<p class="submit"><a href="<?php echo admin_url( 'admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_Gateway_MPAY24' ); ?>" class="button-primary"><?php _e( 'Payment Settings', 'wc-mpay24' ); ?></a></p>
			</div>
			<?php else: ?>
			<p><?php _e( '<strong>mPAY24 gateway is installed</strong> &#8211; Configure the setting to get started :)', 'wc-mpay24' ); ?></p>
			<p class="submit"><a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_mpay24' ); ?>" class="button-primary"><?php _e( 'Checkout', 'wc-mpay24' ); ?></a></p>
			<?php endif; ?>
		</div>
		<?php
		update_option( 'hide_gateway_mpay24_welcome_notice', 1 );
	}

	/**
	 * Init Class
	 */
	add_action( 'plugins_loaded', 'woocommerce_init_gateway_mpay24' );

	function woocommerce_init_gateway_mpay24() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}

		/**
		 * mPAY24 Gateway Class
		 */
		class WC_Gateway_MPAY24 extends WC_Payment_Gateway {

			private $thankyou_text;
			private $email_text;
			private $payment_page_lang = 'EN';
			private $apiusertest;
			private $apipasstest;
			private $apiuserprod;
			private $apipassprod;
			private $testmode;
			private $debug;
			private $notify_url;
			private $page_bg_color = '#ffffff';
			private $logo_style = 'max-width: 100%';
			private $page_header_style = 'height: auto;line-height: 1.75em;color: #000000;margin-bottom: 10px';
			private $page_caption_style = 'background: transparent;color: #000000;border-radius: 0;margin: 0 5px 0 0;padding: 0;font-size: 1.167em;line-height: 1.5em;text-shadow: none';
			private $page_style = 'background: #ffffff';
			private $input_fields_style = '';
			private $drop_down_lists_style = '';
			private $buttons_style = '';
			private $errors_style = '';
			private $errors_header_style = '';
			private $success_title_style = '';
			private $error_title_style = '';
			private $footer_style = '';
			private $htaccessuser;
			private $htaccesspass;

			/**
			 * @var WC_Logger
			 */
			private $log = null;

			/**
			 * @link https://make.wordpress.org/polyglots/teams/ List of WP locales
			 * @link https://docs.mpay24.com/docs/redirect-integration#section-supported-languages List of languages supported by mPAY24
			 * @var array Mapping: WordPress locale => mPAY24 language code
			 */
			private $wp_locale_mpay24 = array(
				'bg_BG' => 'BG',
				'cs_CZ' => 'CS',
				'da_DK' => 'DA',
				'de_DE' => 'DE',
				'de_CH' => 'DE',
				'en_AU' => 'EN',
				'en_CA' => 'EN',
				'en_GB' => 'EN',
				'en_NZ' => 'EN',
				'en_ZA' => 'EN',
				'el'    => 'EL',
				'es_AR' => 'ES',
				'es_CL' => 'ES',
				'es_CO' => 'ES',
				'es_GT' => 'ES',
				'es_MX' => 'ES',
				'es_PE' => 'ES',
				'es_PR' => 'ES',
				'es_ES' => 'ES',
				'es_VE' => 'ES',
				'fi'    => 'FI',
				'fr_BE' => 'FR',
				'fr_CA' => 'FR',
				'fr_FR' => 'FR',
				'hr'    => 'HR',
				'hu_HU' => 'HU',
				'nl_BE' => 'NL',
				'nl_NL' => 'NL',
				'it_IT' => 'IT',
				'ja'    => 'JA',
				'nl_BE' => 'NL',
				'nl_NL' => 'NL',
				'nb_NO' => 'NO',
				'nn_NO' => 'NO',
				'pl_PL' => 'PL',
				'pt_BR' => 'PT',
				'pt_PT' => 'PT',
				'ro_RO' => 'RO',
				'ru_RU' => 'RU',
				'sk_SK' => 'SK',
				'sl_SI' => 'SL',
				'sr_RS' => 'SR',
				'sv_SE' => 'SV',
				'tr_TR' => 'TR',
				'uk'    => 'UK',
				'zh_CN' => 'ZH',
				'zh_HK' => 'ZH',
				'zh_TW' => 'ZH',
			);

			/**
			 * @var array Mapping: WPML language code => mPAY24 language code
			 * @todo Mapping is out-dated, mPAY24 supports more languages now.
			 */
			private $wpml_lang_mpay24 = array(
				'zh-hans' => 'ZH',
				'pt-pt'   => 'PT',
				'bg'      => 'BG',
				'hr'      => 'HR',
				'cs'      => 'CS',
				'nl'      => 'NL',
				'en'      => 'EN',
				'fr'      => 'FR',
				'de'      => 'DE',
				'hu'      => 'HU',
				'it'      => 'IT',
				'ja'      => 'JA',
				'pl'      => 'PL',
				'ro'      => 'RO',
				'ru'      => 'RU',
				'sr'      => 'SR',
				'sk'      => 'SK',
				'sl'      => 'SL',
				'es'      => 'ES',
				'tr'      => 'TR'
			);

			/**
			 * init vars, settings and hooks
			 */
			public function __construct() {

				$this->id                 = 'mpay24';
				$this->icon               = apply_filters( 'woocommerce_mpay24_icon', GATEWAY_MPAY24_URL . 'assets/images/cards.png' );
				$this->order_button_text  = apply_filters( 'woocommerce_mpay24_button_text', __( 'Proceed to mPAY24', 'wc-mpay24' ) );
				$this->has_fields         = false;
				$this->method_title       = __( 'mPAY24', 'wc-mpay24' ); // backend title
				$this->method_description = __( 'Online payment via mPAY24 payment page', 'wc-mpay24' ); // backend description
				$this->notify_url         = add_query_arg( 'wc-api', 'WC_Gateway_MPAY24', home_url( '/' ) );

				require_once( GATEWAY_MPAY24_PATH . 'class-wc-mpay24-shop.php' );

				// Load the form fields
				$this->init_form_fields();

				// Load the settings.
				$this->init_settings();

				// Get setting values
				$this->title                 = $this->get_option( 'title' ); // frontend title
				$this->description           = $this->get_option( 'description' ); // frontend description
				$this->thankyou_text         = $this->get_option( 'thankyou_text' );
				$this->email_text            = $this->get_option( 'email_text' );

				$this->apiusertest           = $this->get_option( 'apiusertest' );
				$this->apipasstest           = $this->get_option( 'apipasstest' );
				$this->apiuserprod           = $this->get_option( 'apiuserprod' );
				$this->apipassprod           = $this->get_option( 'apipassprod' );
				$this->testmode              = $this->get_option( 'testmode' );
				$this->debug                 = $this->get_option( 'debug' );

				$this->payment_page_lang     = $this->get_payment_page_lang();
				$this->page_bg_color         = $this->get_option( 'page_bg_color' );
				$this->logo_style            = $this->get_option( 'logo_style' );
				$this->page_header_style     = $this->get_option( 'page_header_style' );
				$this->page_caption_style    = $this->get_option( 'page_caption_style' );
				$this->page_style            = $this->get_option( 'page_style' );
				$this->input_fields_style    = $this->get_option( 'input_fields_style' );
				$this->drop_down_lists_style = $this->get_option( 'drop_down_lists_style' );
				$this->buttons_style         = $this->get_option( 'buttons_style' );
				$this->errors_style          = $this->get_option( 'errors_style' );
				$this->errors_header_style   = $this->get_option( 'errors_header_style' );
				$this->success_title_style   = $this->get_option( 'success_title_style' );
				$this->error_title_style     = $this->get_option( 'error_title_style' );
				$this->footer_style          = $this->get_option( 'footer_style' );

				$this->htaccessuser           = $this->get_option( 'htaccessuser' );
				$this->htaccesspass           = $this->get_option( 'htaccesspass' );
				// Logging
				if ( 'yes' == $this->debug ) {
					$this->log = new WC_Logger();
				}

				// Hooks
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) ); // inherit method
				add_action( 'woocommerce_thankyou_mpay24', array( $this, 'thankyou_page_text' ) ); // woocommerce_thankyou_.$order->payment_method
				add_action( 'woocommerce_email_after_order_table', array( $this, 'customer_email_text' ), 10, 2 ); // customer email

				// Payment listener/API hook
				add_action( 'woocommerce_api_wc_gateway_mpay24', array( $this, 'check_mpay24_response' ) ); // mPAY24 transaction confirmation
				add_action( 'valid_mpay24_standard_request', array( $this, 'successful_request' ) ); // order status update
			}

			/**
			 * Initialise Settings Form Fields
			 *
			 * Add an array of fields to be displayed
			 * on the gateway's settings screen.
			 *
			 * @access public
			 * @return void
			 * @see woocommerce/classes/class-wc-settings-api.php
			 */
			public function init_form_fields() {
				$this->form_fields = array(
					'woocommerce_settings' => array(
						'title'       => __( 'Woocommerce Settings', 'wc-mpay24' ),
						'type'        => 'title',
						'description' => '',
					),
					'enabled' => array(
						'title'       => __( 'Enable/Disable', 'wc-mpay24' ),
						'label'       => __( 'Enable mPAY24', 'wc-mpay24' ),
						'type'        => 'checkbox',
						'description' => '',
						'default'     => 'no'
					),
					'title' => array(
						'title'       => __( 'Title', 'wc-mpay24' ),
						'type'        => 'text',
						'description' => __( 'This controls the title which the user sees during checkout.', 'wc-mpay24' ),
						'desc_tip'    => true,
						'default'     => __( 'Credit card', 'wc-mpay24' )
					),
					'description' => array(
						'title'       => __( 'Description', 'wc-mpay24' ),
						'type'        => 'textarea',
						'description' => __( 'This controls the description which the user sees during checkout.', 'wc-mpay24' ),
						'desc_tip'    => true,
						'default'     => __( 'Pay with your credit card via mPAY24.', 'wc-mpay24' )
					),
					'thankyou_text' => array(
						'title'       => __( 'Thank you page text', 'wc-mpay24' ),
						'type'        => 'textarea',
						'description' => __( 'This controls the text which the user sees on the order thank you page above order details.', 'wc-mpay24' ),
						'desc_tip'    => true
					),
					'email_text' => array(
						'title'       => __( 'Customer email text', 'wc-mpay24' ),
						'type'        => 'textarea',
						'description' => __( 'This controls the text which the user sees in order confirmation email below order table.', 'wc-mpay24' ),
						'desc_tip'    => true
					),
					'mpay24_settings' => array(
						'title'       => __( 'mPAY24 Settings', 'wc-mpay24' ),
						'type'        => 'title',
						'description' => '',
					),
					'apiusertest' => array(
						'title'       => __( 'Username TEST', 'wc-mpay24' ),
						'type'        => 'text',
						'description' => __( 'This is the SOAP API username (without leading u) for test environment.', 'wc-mpay24' ),
						'desc_tip'    => true,
						'default'     => ''
					),
					'apipasstest' => array(
						'title'       => __( 'Password TEST', 'wc-mpay24' ),
						'type'        => 'password',
						'description' => __( 'This is the SOAP API password for test environment.', 'wc-mpay24' ),
						'desc_tip'    => true,
						'default'     => ''
					),
					'apiuserprod' => array(
						'title'       => __( 'Username PROD', 'wc-mpay24' ),
						'type'        => 'text',
						'description' => __( 'This is the SOAP API username (without leading u) for prod environment.', 'wc-mpay24' ),
						'desc_tip'    => true,
						'default'     => ''
					),
					'apipassprod' => array(
						'title'       => __( 'Password PROD', 'wc-mpay24' ),
						'type'        => 'password',
						'description' => __( 'This is the SOAP API password for prod environment.', 'wc-mpay24' ),
						'desc_tip'    => true,
						'default'     => ''
					),
					'testmode' => array(
						'title'       => __( 'Test Mode', 'wc-mpay24' ),
						'label'       => __( 'Enable Test Mode', 'wc-mpay24' ),
						'type'        => 'checkbox',
						'description' => __( 'Place the payment gateway in test mode.', 'wc-mpay24' ),
						'desc_tip'    => true,
						'default'     => 'yes'
					),
					'debug' => array(
						'title'       => __( 'Debug Log', 'wc-mpay24' ),
						'label'       => __( 'Enable logging', 'wc-mpay24' ),
						'type'        => 'checkbox',
						'description' => __( 'Log mPAY24 events inside <code>/wp-content/plugins/woocommerce/logs/</code>, since WC 2.2 to WC_LOG_DIR (default: <code>/wc-logs/</code>)', 'wc-mpay24' ),
						'default'     => 'no'

					),
					'styling' => array(
						'title'       => __( 'mPAY24 Payment Page Styling', 'wc-mpay24' ),
						'type'        => 'title',
						'description' => 'Tipp for CSS rules: no semicolon after last rule',
					),
					'payment_page_lang' => array(
						'title'       => __( 'Language', 'wc-mpay24' ),
						'type'        => 'select',
						'options'     => array(
							'locale' => __('Use WordPress locale', 'wc-mpay24' ),
							'auto' => __( 'auto with WPML', 'wc-mpay24' ),
							'BG'   => __( 'Bulgarian', 'wc-mpay24' ),
							'ZH'   => __( 'Chinese', 'wc-mpay24' ),
							'HR'   => __( 'Croatian', 'wc-mpay24' ),
							'CS'   => __( 'Czech', 'wc-mpay24' ),
							'DA'   => __( 'Danish', 'wc-mpay24' ),
							'NL'   => __( 'Dutch', 'wc-mpay24' ),
							'EN'   => __( 'English', 'wc-mpay24' ),
							'FI'   => __( 'Finnish', 'wc-mpay24' ),
							'FR'   => __( 'French', 'wc-mpay24' ),
							'DE'   => __( 'German', 'wc-mpay24' ), // default
							'EL'   => __( 'Greek', 'wc-mpay24' ),
							'HU'   => __( 'Hungarian', 'wc-mpay24' ),
							'IT'   => __( 'Italian', 'wc-mpay24' ),
							'JA'   => __( 'Japanese', 'wc-mpay24' ),
							'NO'   => __( 'Norwegian', 'wc-mpay24' ),
							'PL'   => __( 'Polish', 'wc-mpay24' ),
							'PT'   => __( 'Portuguese', 'wc-mpay24' ),
							'RO'   => __( 'Romanian', 'wc-mpay24' ),
							'RU'   => __( 'Russian', 'wc-mpay24' ),
							'SR'   => __( 'Serbian', 'wc-mpay24' ),
							'SK'   => __( 'Slovak', 'wc-mpay24' ),
							'SL'   => __( 'Slovenian', 'wc-mpay24' ),
							'ES'   => __( 'Spanish', 'wc-mpay24' ),
							'SV'   => __( 'Swedish', 'wc-mpay24' ),
							'TR'   => __( 'Turkish', 'wc-mpay24' ),
							'UK'   => __( 'Ukrainian', 'wc-mpay24' ),
						), // See: https://docs.mpay24.com/docs/redirect-integration#section-supported-languages
						'description' => __( 'This controls in which language the payment page is shown. If "auto with WPML" is set current user language is used (if avaiable on mPAY24 side, otherwise "EN"). WPML plugin required for "auto with WPML" option.', 'wc-mpay24' ),
						'desc_tip'    => true,
						'default'     => 'EN'
					),
					'page_bg_color' => array(
						'title'       => __( 'Page Background Color', 'wc-mpay24' ),
						'description' => __( 'Background color of the payment page. Default', 'wc-mpay24' ).' <code>#ffffff</code>.',
						'type'        => 'text',
						'class'       => 'colorpick',
						'css'         => 'width:6em;',
						'default'     => '#ffffff'
					),
					'logo_style' => array(
						'title'       => __( 'Logo Styling', 'wc-mpay24' ),
						'description' => __( 'CSS rules for payment page logo image. Default', 'wc-mpay24' ).' <code>max-width: 100%</code>.',
						'type'        => 'textarea',
						'default'     => 'max-width: 100%'
					),
					'page_header_style' => array(
						'title'       => __( 'Page Header Styling', 'wc-mpay24' ),
						'description' => __( 'CSS rules for payment page header. Default', 'wc-mpay24' ).' <code>height: auto;line-height: 1.75em;color: #000000;margin-bottom: 10px</code>.',
						'type'        => 'textarea',
						'default'     => 'height: auto;line-height: 1.75em;color: #000000;margin-bottom: 10px'
					),
					'page_caption_style' => array(
						'title'       => __( 'Page Caption Styling', 'wc-mpay24' ),
						'description' => __( 'CSS rules for payment page caption. Default', 'wc-mpay24' ).' <code>background: transparent;color: #000000;border-radius: 0;margin: 0 5px 0 0;padding: 0;font-size: 1.167em;line-height: 1.357em;text-shadow: none</code>.',
						'type'        => 'textarea',
						'default'     => 'background: transparent;color: #000000;border-radius: 0;margin: 0 5px 0 0;padding: 0;font-size: 1.167em;line-height: 1.357em;text-shadow: none'
					),
					'page_style' => array(
						'title'       => __( 'Page Styling', 'wc-mpay24' ),
						'description' => __( 'CSS rules for payment page box with inputs. Default', 'wc-mpay24' ).' <code>background: #ffffff</code>.',
						'type'        => 'textarea',
						'default'     => 'background: #ffffff'
					),
					'input_fields_style' => array(
						'title'       => __( 'Input Fields Styling', 'wc-mpay24' ),
						'description' => __( 'CSS rules for payment page input fields.', 'wc-mpay24' ),
						'type'        => 'textarea',
						'default'     => ''
					),
					'drop_down_lists_style' => array(
						'title'       => __( 'Drop Down Lists Styling', 'wc-mpay24' ),
						'description' => __( 'CSS rules for payment page drop down lists.', 'wc-mpay24' ),
						'type'        => 'textarea',
						'default'     => ''
					),
					'buttons_style' => array(
						'title'       => __( 'Buttons Styling', 'wc-mpay24' ),
						'description' => __( 'CSS rules for payment page buttons.', 'wc-mpay24' ),
						'type'        => 'textarea',
						'default'     => ''
					),
					'errors_style' => array(
						'title'       => __( 'Errors Styling', 'wc-mpay24' ),
						'description' => __( 'CSS rules for payment page error messages.', 'wc-mpay24' ),
						'type'        => 'textarea',
						'default'     => ''
					),
					'errors_header_style' => array(
						'title'       => __( 'Errors Header Styling', 'wc-mpay24' ),
						'description' => __( 'CSS rules for payment page error heading.', 'wc-mpay24' ),
						'type'        => 'textarea',
						'default'     => ''
					),
					'success_title_style' => array(
						'title'       => __( 'Success Title Styling', 'wc-mpay24' ),
						'description' => __( 'CSS rules for payment success page title area.', 'wc-mpay24' ),
						'type'        => 'textarea',
						'default'     => ''
					),
					'error_title_style' => array(
						'title'       => __( 'Error Title Styling', 'wc-mpay24' ),
						'description' => __( 'CSS rules for payment error page title area.', 'wc-mpay24' ),
						'type'        => 'textarea',
						'default'     => ''
					),
					'footer_style' => array(
						'title'       => __( 'Footer Styling', 'wc-mpay24' ),
						'description' => __( 'CSS rules for payment page footer area.', 'wc-mpay24' ),
						'type'        => 'textarea',
						'default'     => ''
					),
					'protection' => array(
						'title'       => __( 'Access Protection', 'wc-mpay24' ),
						'type'        => 'title',
						'description' => __( 'If your site has an access protection with .htaccess fill in the credentials to get the right status from mPAY24 during payment process. Please remove them as website is productive.' ),
					),
					'htaccessuser' => array(
						'title'       => __( 'Username .htaccess', 'wc-mpay24' ),
						'type'        => 'text',
						'default'     => ''
					),
					'htaccesspass' => array(
						'title'       => __( 'Password .htaccess', 'wc-mpay24' ),
						'type'        => 'password',
						'default'     => ''
					)
				);
			}

			/**
			 * Admin Panel Options
			 * - Options for bits like 'title' and availability on a country-by-country basis
			 *
			 * @access public
			 * @return void
			 */
			public function admin_options() {
				?>
				<h3><?php _e( 'mPAY24', 'wc-mpay24' ); ?></h3>
				<p><?php _e( 'Online payment via mPAY24 payment page', 'wc-mpay24' ); ?></p>
				<table class="form-table">
				<?php
					// Generate the HTML For the settings form.
					$this->generate_settings_html();
				?>
				</table>
				<?php
			}

			/**
			 * add .htaccess credentials from options to url
			 *
			 * @param string $url
			 * @return string - protected url
			 */
			protected function protect_url( $url ) {
				if( '' != $this->htaccessuser && '' != $this->htaccesspass ) {
					return str_replace( '://', '://' . $this->htaccessuser . ':' . $this->htaccesspass . '@', $url );
				}

				return $url;
			}

			/**
			 * Get payment page language.
			 * @return string Language code recognized by mPAY24 API.
			 */
			protected function get_payment_page_lang() {
				$payment_page_lang = $this->get_option('payment_page_lang');

				if ( $payment_page_lang === 'locale' ) {
					// This works with Polylang and any other multilanguage plugins that performs locale switching.
					$locale = get_locale();
					return isset($this->wp_locale_mpay24[$locale]) ? $this->wp_locale_mpay24[$locale] : 'EN';
				}

				if ( $payment_page_lang === 'auto' ) {
					// WPML installed?
					$lang_code = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : 'en';
					return isset($this->wpml_lang_mpay24[$lang_code]) ? $this->wpml_lang_mpay24[$lang_code] : 'EN';
				}

				return $payment_page_lang;
			}

			/**
			 * Process Payment
			 *
			 * @access public
			 * @return void
			 * @see woocommerce/classes/class-wc-payment-gateway.php
			 */
			public function process_payment( $order_id ) {

				$order = new WC_Order( $order_id );

				$testmode = ( 'yes' == $this->testmode );
				$user     = ( 'yes' == $this->testmode ) ? $this->apiusertest : $this->apiuserprod;
				$pass     = ( 'yes' == $this->testmode ) ? $this->apipasstest : $this->apipassprod;
				$debug    = ( 'yes' == $this->debug );

				// start mpay24 transaction
				$shop = new WC_MPAY24_Shop( $user, $pass, $testmode, $debug );
				if ( isset( $this->log ) ) {
					$shop->setLog( $this->log );
				}
				$tid = $order_id . '-' . sanitize_title( $order->billing_last_name ) . '-' . sanitize_title( $order->billing_first_name );
				$shop->setTid( substr( $tid, 0, 32 ) );
				$shop->setPrice( $order->order_total );

				$shop->setLanguage( $this->payment_page_lang );
				$customer_name = ( $order->billing_company != '' ) ? $order->billing_company : $order->billing_first_name . ' ' . $order->billing_last_name;
				$shop->setCustomer( $customer_name );
				$shop->setCustomerId( $order->user_id );

				$shop->setSuccessUrl( $this->get_return_url( $order ) ); // thank you page
				$shop->setErrorUrl( $order->get_cancel_order_url() ); // failed orders will also be marked as cancelled
				$shop->setCancelUrl( $order->get_cancel_order_url() );
				$shop->setConfirmUrl( $this->protect_url( $this->notify_url ) );

				$shop->setPageBgColor( $this->page_bg_color );
				$shop->setLogoStyle( $this->logo_style );
				$shop->setPageHeaderStyle( $this->page_header_style );
				$shop->setPageCaptionStyle( $this->page_caption_style );
				$shop->setPageStyle( $this->page_style );
				$shop->setInputFieldsStyle( $this->input_fields_style );
				$shop->setDropDownListsStyle( $this->drop_down_lists_style );
				$shop->setButtonsStyle( $this->buttons_style );
				$shop->setErrorsStyle( $this->errors_style );
				$shop->setErrorsHeaderStyle( $this->errors_header_style );
				$shop->setSuccessTitleStyle( $this->success_title_style );
				$shop->setErrorTitleStyle( $this->error_title_style );
				$shop->setFooterStyle( $this->footer_style );

				$result = $shop->pay();

				if ( $result->getGeneralResponse()->getStatus() != 'OK' ) {
					wc_add_notice( $result->getGeneralResponse()->getReturnCode(), 'error' );
					return array(
						'result'   => 'fail',
						'redirect' => ''
					);
				}

				// open payment page
				return array(
					'result'   => 'success',
					'redirect' => $result->getLocation()
				);
			}

			/**
			 * There are no payment fields, so show the description if set.
			 * Also show info if test mode is enabled.
			 *
			 * @access public
			 * @return void
			 * @see woocommerce/classes/class-wc-payment-gateway.php
			 */
			public function payment_fields() {
				if ( 'yes' == $this->testmode ) {
					echo '<p>' . esc_html__( 'TEST MODE ENABLED', 'wc-mpay24' ) . '</p>';
				}
				if ( ($description = $this->get_description()) ) {
					echo '<p>' . wpautop( wptexturize( $description ) ) . '</p>';
				}
			}

			/**
			 * Add text to thankyou page above order details
			 * called by 'woocommerce_thankyou_mpay24' action
			 *
			 * @access public
			 * @return void
			 */
			public function thankyou_page_text( $order ) {
				if ( $this->thankyou_text ) {
					echo '<p>' . wpautop( wptexturize( $this->thankyou_text ) ) . '</p>';
				}
			}

			/**
			 * Add text to order confirmation mail to customer below order table
			 * called by 'woocommerce_email_after_order_table' action
			 *
			 * @access public
			 * @return void
			 */
			public function customer_email_text( $order, $sent_to_admin ) {
				if ( $sent_to_admin ) {
					return;
				}
				if ( 'mpay24' !== $order->payment_method ) {
					return;
				}
				if ( !$this->email_text ) {
					return;
				}

				echo '<p>' . wpautop( wptexturize( $this->email_text ) ) . '</p>';
			}

			/**
			 * Confirm MPAY24 transaction
			 */
			function check_ipn_request_is_valid() {
				$get_params = $_GET;
				$args	   = array();

				if ( !($tid = filter_input(INPUT_GET, 'TID')) ) {
					return false;
				}

				// Store all _GET params but TID into $args.
				foreach ( $get_params as $key => $value ) {
					if ( 'TID' !== $key ) {
						$args[ $key ] = $value;
					}
				}

				$testmode = ( 'yes' == $this->testmode );
				$user     = ( 'yes' == $this->testmode ) ? $this->apiusertest : $this->apiuserprod;
				$pass     = ( 'yes' == $this->testmode ) ? $this->apipasstest : $this->apipassprod;
				$debug    = ( 'yes' == $this->debug );

				$shop = new WC_MPAY24_Shop( $user, $pass, $testmode, $debug );
				if ( isset( $this->log ) ) {
					$shop->setLog( $this->log );
				}
				$shop->confirm( $tid, $args );

				return true;
			}

			/**
			 * Confirm mPAY24 transaction and write status in plugin's database table
			 * called by 'woocommerce_api_wc_gateway_mpay24' action
			 * call 'valid_mpay24_standard_request' action to process order
			 *
			 * @access public
			 * @return void
			 */
			function check_mpay24_response() {
				@ob_clean();
				if ( ! empty( $_GET ) && $this->check_ipn_request_is_valid() ) {
					header( 'HTTP/1.1 200 OK' );
					do_action( 'valid_mpay24_standard_request', $_GET );
				} else {
					wp_die( 'mPAY24 IPN Request Failure' );
				}
			}

			/**
			 * Lock up transaction status from database and update order status
			 * called by 'valid_mpay24_standard_request' action
			 *
			 * @param array $posted - request parameters
			 * @access public
			 * @return void
			 */
			public function successful_request( $posted ) {
				global $wpdb;

				if ( ! empty( $posted['TID'] ) ) {

					$order          = wc_get_order( (int) $posted['TID'] );
					$table_name     = $wpdb->prefix . GATEWAY_MPAY24_TABLE_NAME;
					$transaction_db = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE tid = %s", $posted['TID'] ) );

					//mpay24 transaction status: BILLED,RESERVED,ERROR,SUSPENDED,CREDITED,REVERSED
					// Lowercase
					$tstatus = strtolower( $transaction_db->tstatus );

					if ( 'yes' == $this->debug ) {
						$this->log->add( 'mpay24', 'Transaction status: ' . $tstatus );
					}

					// We are here so lets check status and do actions
					switch ( $tstatus ) {
						case 'billed':
							// Check order not already completed
							if ( 'completed' == $order->status ) {
								if ( 'yes' == $this->debug ) {
									$this->log->add( 'mpay24', 'Aborting, Order #' . $order->id . ' is already complete.' );
								}
								exit;
							}

							// Payment completed
							$order->add_order_note( __( 'mPAY24 payment completed', 'wc-mpay24' ) );
							$order->payment_complete();

							if ( 'yes' == $this->debug ) {
								$this->log->add( 'mpay24', 'Payment complete.' );
							}
						break;
                        case 'suspended':
						case 'reserved':
							// Order pending
							$order->update_status( 'on-hold', sprintf( __( 'Payment %s via mPAY24.', 'wc-mpay24' ), $tstatus ) );
						break;
						case 'error':
							// Order failed
							$order->update_status( 'failed', sprintf( __( 'Payment %s via mPAY24.', 'wc-mpay24' ), $tstatus ) );
							if( version_compare( $woocommerce->version, '2.1', '>=' ) ) {
								wc_add_notice( __( 'Payment failed via mPAY24.', 'wc-mpay24' ), 'error' );
							}
						break;
						case 'credited':
							$order->update_status( 'refunded', sprintf(__('Payment %s via mPAY24.', 'wc-mpay24' ), $tstatus ) );
						break;
						case 'reversed':
							// Order cancelled
							$order->cancel_order( sprintf( __( 'Payment %s via mPAY24.', 'wc-mpay24' ), $tstatus ) );
						break;

						default:
						// No action
						break;
					}
					exit;
				}
			}
		}

		/**
		 * Add the gateway to woocommerce
		 */
		function woocommerce_add_gateway_mpay24( $methods ) {
			$methods[] = 'WC_Gateway_MPAY24';
			return $methods;
		}
		add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_mpay24' );
	}
}