<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$tpl = new Template("app/templates/".$settings['default_template']."/footer.html",$lang);
$tpl->set("url",$settings['url']);
$tpl->set("name",$settings['name']);
echo $tpl->output();
?> 
