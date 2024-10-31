<?php
/*
Plugin Name: Plumba
Plugin URI: http://wordpress.org/extend/plugins/plumba/
Description: Online poll plugin for WordPress. Add to any page or post!
Author: EkAndreas, Flowcom AB
Version: 1.1.4
Author URI: http://www.flowcom.se/
*/

//Load textdomain to this plugin, mo/po-files placed inside the languages folder
load_plugin_textdomain( 'plumba', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

//Include classes
include_once 'classes/plumba.php';
include_once 'classes/shortcodes.php';
include_once 'classes/presentation.php';
include_once 'classes/ajax.php';
include_once 'classes/metabox.php';
include_once 'classes/comments.php';
include_once 'classes/options.php';

//Initialize the main object from the plumba class
$plumba_main         = new Plumba_Main();
$plumba_presentation = new Plumba_Presentation();
$plumba_ajax         = new Plumba_Ajax();
$plumba_options      = new Plumba_Options();
$plumba_shortcodes   = new Plumba_Shortcodes();

?>
