<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
function getLanguage($url, $ln = null, $type = null) {
	global $settings;
	// Type 1: Output the available languages
	// Type 2: Change the path for the /requests/ folder location
	// Set the directory location
	if($type == 2) {
		$languagesDir = '../languages/';
	} else {
		$languagesDir = './app/languages/';
	}
	// Search for pathnames matching the .png pattern
	$language = array();
	if ($handle = opendir($languagesDir)) {
		while (false !== ($file = readdir($handle)))
		{
			if ($file != "." && $file != ".." && $file != "index.php" && strtolower(substr($file, strrpos($file, '.') + 1)) == 'php')
			{
				$file = str_ireplace(".php","",$file);
				$language[] = $file;
			}
		}
	}
	
	if($type == 1) {
		// Add to array the available images
		foreach($language as $lang) {
			// The path to be parsed
			$path = pathinfo($lang);
			
			// Add the filename into $available array
			if($path['filename'] == "index") {
			
			} else {
			$available .= '<li class="dropdown-item"><a href="'.$url.'index.php?lang='.$path['filename'].'">'.ucfirst(strtolower($path['filename'])).'</a></li>';
			}
		}
		return $available;
	} else {
		// If get is set, set the cookie and stuff
		$lang = $settings['default_language']; // DEFAULT LANGUAGE
		if($type == 2) {
			$path = '../languages/';
		} else {
			$path = './app/languages/';
		}
		if(isset($_GET['lang'])) {
			if(in_array($lang,$language)) {
				$lang = protect($_GET['lang']);
				setcookie('lang', $lang, time() +  (10 * 365 * 24 * 60 * 60)); // Expire in one month
			} else {
				setcookie('lang', $lang, time() +  (10 * 365 * 24 * 60 * 60)); // Expire in one month
			}
			header("Location: $url");
		} elseif(isset($_COOKIE['lang'])) {
			if(in_array($lang,$language)) {
				$lang = $_COOKIE['lang'];
			}
		} else {
			setcookie('lang', $lang, time() +  (10 * 365 * 24 * 60 * 60)); // Expire in one month
		}

		if(in_array($lang,$language)) {
			return $path.$lang.'.php';
		}
	}
}
?>