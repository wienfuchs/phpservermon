<?php

define('PSM_VERSION', '2.1.0');
// Include paths
define('PSM_PATH_SRC', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('PSM_PATH_VENDOR', PSM_PATH_SRC . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);
define('PSM_PATH_INC', PSM_PATH_SRC . 'includes' . DIRECTORY_SEPARATOR);
define('PSM_PATH_TPL', PSM_PATH_SRC . 'templates' . DIRECTORY_SEPARATOR);
define('PSM_PATH_LANG', PSM_PATH_SRC . 'lang' . DIRECTORY_SEPARATOR);

// find config file
$path_conf = PSM_PATH_SRC . '../config.php';
if(file_exists($path_conf)) {
	include_once $path_conf;
}
// check for a debug var
if(defined('PSM_DEBUG') && PSM_DEBUG) {
	error_reporting(E_ALL);
	ini_set('display_erors', 1);
} else {
	error_reporting(0);
	ini_set('display_errors', 0);
}

// set autoloader, make sure to set $prepend = true so that our autoloader is called first
function __autoload($class) {
	// remove leading \
	$class = ltrim($class, '\\');
	$path_parts = explode('\\', $class);

	$filename = array_pop($path_parts);
	$path = implode(DIRECTORY_SEPARATOR, $path_parts) .
			DIRECTORY_SEPARATOR .
			$filename . '.class.php'
	;
	// search in these dirs:
	$basedirs = array(
		PSM_PATH_SRC,
		PSM_PATH_VENDOR
	);
	foreach($basedirs as $dir) {
		if(file_exists($dir . $path)) {
			require_once $dir . $path;
			return;
		}
	}
}

// auto-find all include files
$includes = glob(PSM_PATH_INC . '*.inc.php');
foreach($includes as $file) {
	include_once $file;
}
// init db connection
$db = new psm\Service\Database();

if($db->getLink() !== null) {

	psm_load_conf();
} else {
	// no config yet! lets help them in the right direction
	if(!defined('PSM_INSTALL')) {
		header('Location: install.php');
		die();
	}
}
$lang = psm_get_conf('language', 'en');
psm_load_lang($lang);

?>