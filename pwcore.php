<?php
/*
Plugin Name: PW Core
Plugin URI: https://example.com
Description: Core plugin for Publish Wiki Pages
Version: 0.1.0
Author: Brian Tuju
Author URI: https://github.com/briantuju
Text Domain: pwcore
*/

// Plugin must be accessed via WordPress itself
if ( ! defined( 'ABSPATH' ) ) {
  die( 'NOPE! You cannot be here.' );
}

error_reporting( E_ALL & ~E_WARNING & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE );

if ( ! class_exists( 'PWCore' ) ) {

  /**
   * This is the core class that will be used with the plugin.
   */
  class PWCore {

	public function __construct() {
	  /**
	   * PWCore plugin path
	   */
	  define( 'PW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

	  /**
	   * PWCore plugin url
	   */
	  define( 'PW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

	  require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

	  // Setup env variables using dotenv
	  $dotenv = Dotenv\Dotenv::createImmutable( __DIR__ );
	  $dotenv->load();
	}

	public function initialize(): void {
	  // Boostrap files first
	  include_once PW_PLUGIN_PATH . '/bootstrap/app.php';
	  include_once PW_PLUGIN_PATH . '/bootstrap/extra.php';
	  include_once PW_PLUGIN_PATH . '/bootstrap/faq.php';
	  include_once PW_PLUGIN_PATH . '/bootstrap/orders.php';
	  include_once PW_PLUGIN_PATH . '/bootstrap/packages.php';
	  include_once PW_PLUGIN_PATH . '/bootstrap/services.php';
	  include_once PW_PLUGIN_PATH . '/bootstrap/invoices.php';
	  include_once PW_PLUGIN_PATH . '/bootstrap/transactions.php';

	  include_once PW_PLUGIN_PATH . '/routes/api.php';
	  include_once PW_PLUGIN_PATH . '/routes/web.php';
	}
  }

  $pwcore_plugin = new PWCore;

  $pwcore_plugin->initialize();
}
