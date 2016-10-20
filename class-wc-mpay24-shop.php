<?php
include_once("MPay24/MPay24Shop.php");

/**
 * Create and update transaction to mPAY24
 *
 */
class WC_MPAY24_Shop extends MPay24Shop {

	/**
	 * transaction id
	 * @var        string
	 */
	protected $tid;

	/**
	 * total price
	 * @var        decimal
	 */
	protected $price;

	/**
	 * user interface language in 2 uppercased letters
	 *
	 * @var string
	 */
	protected $language = 'EN';

	/**
	 * customer name from merchant website customer
	 *
	 * @var string
	 */
	protected $customer;

	/**
	 * customer id from merchant website customer
	 *
	 * @var string
	 */
	protected $customerId;

	/**
	 * urls for callbacks
	 *
	 * @var string
	 */
	protected $successUrl = '';
	protected $errorUrl   = '';
	protected $cancelUrl  = '';
	protected $confirmUrl = '';

	/**
	 * logger
	 *
	 * @var WC_Logger
	 */
	protected $log = null;

	/**
	 * styling options for payment page
	 *
	 * @var string
	 */
	protected $pageBgColor = '#ffffff';
	protected $logoStyle = 'max-width: 100%';
	protected $pageHeaderStyle = 'height: auto;line-height: 1.75em;color: #000000;margin-bottom: 10px';
	protected $pageCaptionStyle = 'background: transparent;color: #000000;border-radius: 0;margin: 0 5px 0 0;padding: 0;font-size: 1.167em;line-height: 1.357em;text-shadow: none';
	protected $pageStyle = 'background: #ffffff';
	protected $inputFieldsStyle = '';
	protected $dropDownListsStyle = '';
	protected $buttonsStyle = '';
	protected $errorsStyle = '';
	protected $errorsHeaderStyle = '';
	protected $successTitleStyle = '';
	protected $errorTitleStyle = '';
	protected $footerStyle = '';

	public function getTid() {
		return $this->tid;
	}

	public function getPrice() {
		return $this->price;
	}

	public function getLanguage() {
		return $this->language;
	}

	public function getCustomer() {
		return $this->customer;
	}

	public function getCustomerId() {
		return $this->customerId;
	}

	public function getSuccessUrl() {
		return $this->successUrl;
	}

	public function getErrorUrl() {
		return $this->errorUrl;
	}

	public function getCancelUrl() {
		return $this->cancelUrl;
	}

	public function getConfirmUrl() {
		return $this->confirmUrl;
	}

	public function getLog() {
		return $this->log;
	}

	public function getPageBgColor() {
		return $this->pageBgColor;
	}

	public function getLogoStyle() {
		return $this->logoStyle;
	}

	public function getPageHeaderStyle() {
		return $this->pageHeaderStyle;
	}

	public function getPageCaptionStyle() {
		return $this->pageCaptionStyle;
	}

	public function getPageStyle() {
		return $this->pageStyle;
	}

	public function getInputFieldsStyle() {
		return $this->inputFieldsStyle;
	}

	public function getDropDownListsStyle() {
		return $this->dropDownListsStyle;
	}

	public function getButtonsStyle() {
		return $this->buttonsStyle;
	}

	public function getErrorsStyle() {
		return $this->errorsStyle;
	}

	public function getErrorsHeaderStyle() {
		return $this->errorsHeaderStyle;
	}

	public function getSuccessTitleStyle() {
		return $this->successTitleStyle;
	}

	public function getErrorTitleStyle() {
		return $this->errorTitleStyle;
	}

	public function getFooterStyle() {
		return $this->footerStyle;
	}

	public function setTid( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->tid !== $v ) {
			$this->tid = $v;
		}

		return $this;
	}

	public function setPrice( $v ) {
		if ( null !== $v ) {
			$v = (float) $v;
		}

		if ( $this->price !== $v ) {
			$this->price = $v;
		}

		return $this;
	}

	public function setLanguage( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->language !== $v ) {
			$this->language = strtoupper( $v );
		}

		return $this;
	}

	public function setCustomer( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->customer !== $v ) {
			$this->customer = $v;
		}

		return $this;
	}

	public function setCustomerId( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->customerId !== $v ) {
			$this->customerId = $v;
		}

		return $this;
	}

	public function setSuccessUrl( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->successUrl !== $v ) {
			$this->successUrl = $v;
		}

		return $this;
	}

	public function setErrorUrl( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->errorUrl !== $v ) {
			$this->errorUrl = $v;
		}

		return $this;
	}

	public function setCancelUrl( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->cancelUrl !== $v ) {
			$this->cancelUrl = $v;
		}

		return $this;
	}

	public function setConfirmUrl( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->confirmUrl !== $v ) {
			$this->confirmUrl = $v;
		}

		return $this;
	}

	public function setLog( $v ) {
		if ( $this->log !== $v ) {
			$this->log = $v;
		}

		$this->mPay24Api->setDebug( true );

		return $this;
	}

	public function setPageBgColor( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->pageBgColor !== $v ) {
			$this->pageBgColor = $v;
		}

		return $this;
	}

	public function setLogoStyle( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->logoStyle !== $v ) {
			$this->logoStyle = $v;
		}

		return $this;
	}

	public function setPageHeaderStyle( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->pageHeaderStyle !== $v ) {
			$this->pageHeaderStyle = $v;
		}

		return $this;
	}

	public function setPageCaptionStyle( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->pageCaptionStyle !== $v ) {
			$this->pageCaptionStyle = $v;
		}

		return $this;
	}

	public function setPageStyle( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->pageStyle !== $v ) {
			$this->pageStyle = $v;
		}

		return $this;
	}

	public function setInputFieldsStyle( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->inputFieldsStyle !== $v ) {
			$this->inputFieldsStyle = $v;
		}

		return $this;
	}

	public function setDropDownListsStyle( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->dropDownListsStyle !== $v ) {
			$this->dropDownListsStyle = $v;
		}

		return $this;
	}

	public function setButtonsStyle( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->buttonsStyle !== $v ) {
			$this->buttonsStyle = $v;
		}

		return $this;
	}

	public function setErrorsStyle( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->errorsStyle !== $v ) {
			$this->errorsStyle = $v;
		}

		return $this;
	}

	public function setErrorsHeaderStyle( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->errorsHeaderStyle !== $v ) {
			$this->errorsHeaderStyle = $v;
		}

		return $this;
	}

	public function setSuccessTitleStyle( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->successTitleStyle !== $v ) {
			$this->successTitleStyle = $v;
		}

		return $this;
	}

	public function setErrorTitleStyle( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->errorTitleStyle !== $v ) {
			$this->errorTitleStyle = $v;
		}

		return $this;
	}

	public function setFooterStyle( $v ) {
		if ( null !== $v ) {
			$v = (string) $v;
		}

		if ( $this->footerStyle !== $v ) {
			$this->footerStyle = $v;
		}

		return $this;
	}

	/**
	 * Create a transaction with the reuqired transaction's parameters - TID and PRICE
     *
	 * @return Transaction
	 */
	public function createTransaction() {
		global $wpdb;

		// transaction exists for payment on failed orders (order-pay url param)
		$transactionDb = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . GATEWAY_MPAY24_TABLE_NAME . " WHERE tid = %s", $this->getTid() ) );

		if ($transactionDb != null) {
			$update = array();
			$update['price'] = $this->getPrice() * 100;
			$update['updated_at'] = date( 'Y-m-d H:i:s', time() );

			$wpdb->update(
				$wpdb->prefix . GATEWAY_MPAY24_TABLE_NAME ,
				$update,
				array( 'tid' => $this->getTid() )
			);

			$secret = $transactionDb->secret;
		} else {
			$secret = $this->createSecret( $this->getTid(), $this->getPrice(), 'EUR', time() );

			$wpdb->insert(
				$wpdb->prefix . GATEWAY_MPAY24_TABLE_NAME,
				array(
					'tid'        => $this->getTid(),
					'price'      => $this->getPrice() * 100,
					'secret'     => $secret,
					'created_at' => date( 'Y-m-d H:i:s', time() ),
				),
				array( '%s', '%d', '%s', '%s' )
			);


		}

		$transaction = new Transaction( $this->getTid() );

		// setting via magic method __set
		$transaction->PRICE = $this->getPrice();
		$transaction->CURRENCY = 'EUR';
		$transaction->SECRET = $secret;

		return $transaction;
	}

	/**
	 * Actualize the transaction, writing all the transaction's parameters into result.txt
	 *
	 * @param string $tid               The transaction ID you want to update with the confirmation
	 * @param array  $args              Arrguments with them the transaction is to be updated
	 * @param bool   $shippingConfirmed TRUE if the shipping address is confirmed, FALSE - otherwise (in case of PayPal Express Checkout)
	 */
	public function updateTransaction( $tid, $args, $shippingConfirmed ) {
		global $wpdb;

		$transactionDb = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . GATEWAY_MPAY24_TABLE_NAME . " WHERE tid = %s", $tid ) );
		$update = array();
		$result = "TID: " . $tid . "\n\ntransaction arguments:\n\n";
		foreach ( $args as $key => $value ) {
			$result .= $key . " = " . $value . "\n";
			// exception for "CUSTOMER" -> customer_name db field
			if ( $key == 'CUSTOMER' ) {
				$update['customer_name'] = $value;
			}
			elseif ( 'shippingConfirmed' != $key && 'mpay24Listener' != $key ) {
				$key = strtolower( $key );
				$update[ $key ] = $value;
			}
		}
		$update['updated_at'] = date( 'Y-m-d H:i:s', time() );

		$wpdb->update(
			$wpdb->prefix . GATEWAY_MPAY24_TABLE_NAME ,
			$update,
			array( 'tid' => $tid )
			);

		if ( ! is_null( $this->log ) ) {
			$this->log->add( 'mpay24', $result );
		}
	}

	/**
	 * Give the transaction object back, after the required parameters (TID and PRICE) was set
	 *
	 * @param  string      $tid The transaction ID of the transaction you want get
	 * @return Transaction
	 */
	public function getTransaction( $tid ) {
		global $wpdb;

		$transaction = new Transaction( $tid );
		$transactionDb = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . GATEWAY_MPAY24_TABLE_NAME . " WHERE tid = %s", $tid ) );

		$transaction->PRICE = $transactionDb->price;
		$transaction->SECRET = $transactionDb->secret;

		return $transaction;
	}

	/**
	 * Using the ORDER object, create a MDXI-XML
	 * @param  Transaction $transaction The transaction you want to make a MDXI XML file for
	 * @return ORDER
	 * @see    MDXI.xsd and mPAY24 spezification page 78ff.
	 */
	public function createMDXI( $transaction ) {
		$mdxi = new ORDER();
		$subTotal = 0.00;

		$paymentMethods = $this->getPaymentMethods();
		if ( 'OK' != $paymentMethods->getGeneralResponse()->getStatus() ) {
			throw new \Exception( 'STATUS: ' . $paymentMethods->getGeneralResponse()->getStatus() . ' RETURNCODE: ' . $paymentMethods->getGeneralResponse()->getReturnCode() );
		}

		// $mdxi = XML Document
		$mdxi->Order->Tid         = $transaction->TID;
		$mdxi->Order->TemplateSet = 'WEB';
		$mdxi->Order->TemplateSet->setLanguage( $this->getLanguage() );
		$mdxi->Order->TemplateSet->setCSSName( 'MOBILE' ); // use responsive template

		$mdxi->Order->PaymentTypes->setEnable( 'true' );

		$pTypes = $paymentMethods->getPTypes();
		$brands = $paymentMethods->getBrands();
		foreach ( $pTypes as $key => $value ) {
			$mdxi->Order->PaymentTypes->Payment( $key+1 )->setType( $value );
			if ( 'CC' == $value || 'ELV' == $value ) {
				$mdxi->Order->PaymentTypes->Payment( $key+1 )->setBrand( $brands[ $key ] );
			}
		}

		$mdxi->Order->Price = $transaction->PRICE;
		/**
		 * styling options
		 *
		 * no semicolon after last rule
		 * example: $mdxi->Order->Price->setStyle('background-color: #DDDDDD; border: none; border-top: 1px solid #5B595D');
		 */
		//$mdxi->Order->Price->setStyle('');
		//$mdxi->Order->Price->setHeaderStyle('');

		$mdxi->Order->Customer = $this->getCustomer();
		$mdxi->Order->Customer->setId( $this->getCustomerId() );
		//$mdxi->Order->Customer->setUseProfile( 'true' ); // proSafe required

		$mdxi->Order->URL->Success      = $this->getSuccessUrl();
		$mdxi->Order->URL->Error        = $this->getErrorUrl();
		$mdxi->Order->URL->Confirmation = $this->getConfirmUrl() . '&token=' . $transaction->SECRET;
		$mdxi->Order->URL->Cancel       = $this->getCancelUrl();

		/**
		 * styling options
		 *
		 * no semicolon after last rule
		 * example: $mdxi->Order->setStyle('font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;');
		 */
		$mdxi->Order->setStyle( 'background-color: '.$this->getPageBgColor().';font-size: 12px;font-family: "Helvetica Neue",Helvetica,Arial,sans-serif' ); // body styling
		$mdxi->Order->setLogoStyle( $this->getLogoStyle() ); // styling of logo image
		$mdxi->Order->setPageHeaderStyle( $this->getPageHeaderStyle() );
		$mdxi->Order->setPageCaptionStyle( $this->getPageCaptionStyle() );
		$mdxi->Order->setPageStyle( $this->getPageStyle() );
		$mdxi->Order->setInputFieldsStyle( $this->getInputFieldsStyle() );
		$mdxi->Order->setDropDownListsStyle( $this->getDropDownListsStyle() );
		$mdxi->Order->setButtonsStyle( $this->getButtonsStyle() );
		$mdxi->Order->setErrorsStyle( $this->getErrorsStyle() );
		$mdxi->Order->setErrorsHeaderStyle( $this->getErrorsHeaderStyle() );
		$mdxi->Order->setSuccessTitleStyle( $this->getSuccessTitleStyle() );
		$mdxi->Order->setErrorTitleStyle( $this->getErrorTitleStyle() );
		$mdxi->Order->setFooterStyle( $this->getFooterStyle() );

		return $mdxi;
	}

	/**
	 * NOT IMPLEMENTED
	 *
	 * Using the ORDER object, create a order-xml, which is needed for a transaction with profiles to be started
	 * @param  string $tid The transaction ID of the transaction you want to make an order transaction XML file for
	 * @return XML
	 * @see    MPay24Shop.php and mPAY24 spezification page 63.
	 */
	public function createProfileOrder( $tid ) {}

	/**
	 * NOT IMPLEMENTED
	 *
	 * Using the ORDER object, create a order-xml, which is needed for a transaction with PayPal Express Checkout to be started
	 * @param  string $tid The transaction ID of the transaction you want to make an order transaction XML file for
	 * @return XML
	 */
	public function createExpressCheckoutOrder( $tid ) {}

	/**
	 * NOT IMPLEMENTED
	 *
	 * Using the ORDER object, create a order-xml, which is needed for a transaction with PayPal Express Checkout to be finished
	 * @param  string $tid           The transaction ID of the transaction you want to make an order transaction XML file for
	 * @param  string $shippingCosts The shipping costs amount for the transaction, provided by PayPal, after changing the shipping address
	 * @param  string $amount        The new amount for the transaction, provided by PayPal, after changing the shipping address
	 * @param  bool   $cancel        TRUE if the a cancelation is wanted after renewing the amounts and FALSE otherwise
	 * @return XML
	 */
	public function createFinishExpressCheckoutOrder( $tid, $shippingCosts, $amount, $cancel ) {}

	/**
	 * Write a mpay24 log into /wp-content/plugins/woocommerce/logs/, since WC 2.2 /wc-logs
	 * @param string $operation   The operation, which is to log: GetPaymentMethods, Pay, PayWithProfile, Confirmation, UpdateTransactionStatus, ClearAmount, CreditAmount, CancelTransaction, etc.
	 * @param string $info_to_log The information, which is to log: request, response, etc.
	 */
	public function write_log( $operation, $info_to_log ) {
		$result = $operation.$info_to_log;

		if ( ! is_null( $this->log ) ) {
			$this->log->add( 'mpay24', $result );
		}
	}

	/**
	 * It should build a hash from the transaction ID of your shop, the amount of the transaction,
	 * the currency and the timeStamp of the transaction. The mPAY24 confirmation interface will be called
	 * with this hash (parameter name 'token'), so you would be able to check whether the confirmation is
	 * really coming from mPAY24 or not. The hash should be then saved in the transaction object, so that
	 * every transaction has an unique secret token.
	 * @param  string $tid       The transaction ID you want to make a secret key for
	 * @param  string $amount    The amount, reserved for this transaction
	 * @param  string $currency  The timeStamp at the moment the transaction is created
	 * @param  string $timeStamp The timeStamp at the moment the transaction is created
	 * @return string
	 */
	public function createSecret( $tid, $amount, $currency, $timeStamp ) {
		return md5( $tid . $amount . $currency . $timeStamp . mt_rand() );
	}

	/**
	 * Get the secret (hashed) token for a transaction
	 * @param  string $tid The transaction ID you want to get the secret key for
	 * @return string
	 */
	public function getSecret( $tid ) {
		$transaction = $this->getTransaction( $tid );

		return $transaction->SECRET;
	}

    /**
     * NOT IMPLEMENTED
     *
     * Using the ORDER object from order.php, create a order-xml, which is needed for a backend to backend transaction to be started
     *
     * @param string $tid
     *          The transaction ID of the transaction you want to make an order transaction XML file for
     * @param string $paymentType
     *          The payment type which will be used for the backend to backend payment (EPS, SOFORT, PAYPAL, MASTERPASS or TOKEN)
     * @return XML
     */
	public function createBackend2BackendOrder($tid, $paymentType) {}

}
