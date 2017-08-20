<?php
/*
	Plugin Name: Pell Editor
	Plugin URI:
	Plugin Description: Wrapper for Pell
	Plugin Version: 1.0
	Plugin Date: 2011-12-06
	Plugin Author: Question2Answer
	Plugin Author URI: http://www.question2answer.org/
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.3
	Plugin Update Check URI:
*/


if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}


qa_register_plugin_module('editor', 'fast-editor.php', 'qa_fast_editor', 'Fast Editor');