<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$b = protect($_GET['b']);
if($b == "success") {
    $tpl = new Template("app/templates/".$settings['default_template']."/payment_success.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $tpl->set("order_id",$_SESSION['ce_order_id']);
    $tpl->set("order_hash",$_SESSION['ce_order_hash']);
    echo $tpl->output();
} elseif($b == "fail") {
    $tpl = new Template("app/templates/".$settings['default_template']."/payment_fail.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $tpl->set("order_id",$_SESSION['ce_order_id']);
    $tpl->set("order_hash",$_SESSION['ce_order_hash']);
    echo $tpl->output();
} else {
    header("Location: $settings[url]");
}
?>