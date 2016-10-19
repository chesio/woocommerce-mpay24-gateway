<?php
if ( preg_match( '#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'] ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
	* creates all tables for the gateway
	* called during register_activation_hook
	*
	* @access internal
	* @return void
	*/
function wc_gateway_mpay24_install() {
	global $wpdb, $woocommerce;

	$wpdb->hide_errors();

	$collate = '';
	if ( $wpdb->has_cap('collation') ) {
		if ( ! empty( $wpdb->charset ) ) {
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$collate .= " COLLATE $wpdb->collate";
		}
	}

	$table_name = $wpdb->prefix . GATEWAY_MPAY24_TABLE_NAME;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	// Table for storing transaction infos from mPAY24
	$sql = "
	CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		status enum('OK','ERROR') NOT NULL,
		operation enum('CONFIRMATION','TRANSACTIONSTATUS') NOT NULL,
		tstatus enum('ERROR','RESERVED','REVERSED','BILLED','CREDITED','SUSPENDED') NOT NULL,
		tid varchar(32) NOT NULL,
		price int(11) NOT NULL,
		currency enum('EUR') NOT NULL,
		p_type varchar(255) NOT NULL,
		brand varchar(255) NOT NULL,
		mpaytid int(11) NOT NULL,
		user_field varchar(255) DEFAULT NULL,
		orderdesc varchar(255) DEFAULT NULL,
		customer_name varchar(50) DEFAULT NULL,
		customer_email varchar(64) DEFAULT NULL,
		language enum('BG','ZH','HR','CS','NL','EN','FR','DE','HU','IT','JA','PL','PT','RO','RU','SR','SK','SL','ES','TR','DA','FI','SV','NO','UK','EL') DEFAULT NULL,
		customer_id varchar(32) DEFAULT NULL,
		profile_id varchar(32) DEFAULT NULL,
		appr_code varchar(255) DEFAULT NULL,
		profile_status enum('IGNORED','USED','ERROR','CREATED','UPDATED','DELETED') DEFAULT NULL,
		filter_status enum('OK','LOW','MID','HIGH','BLOCKED') DEFAULT NULL,
		suspended_reason enum('address','commit','echeck','intl','ipn','multi-currency','other','uniliteral','upgrade','verify') DEFAULT NULL,
		msg varchar(255) DEFAULT NULL,
		secret varchar(255) DEFAULT NULL,
		created_at datetime DEFAULT NULL,
		updated_at datetime DEFAULT NULL,
		PRIMARY KEY  (id),
		KEY FI_mpay24_transaction_customer_id (customer_id)
		) $collate;
	";
	dbDelta( $sql );

	update_option( 'hide_gateway_mpay24_welcome_notice', '' );
}

/**
	* Uninstall all settings and tables
	* Called during register_unstall_hook
	*
	* @access internal
	* @return void
	*/
function wc_gateway_mpay24_uninstall() {
	global $wpdb;

	// first remove all tables
	$table_name = $wpdb->prefix . GATEWAY_MPAY24_TABLE_NAME;
	$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

	// then remove all options
	delete_option( 'hide_gateway_mpay24_welcome_notice' );
	delete_option( 'gateway_mpay24_version' );
}