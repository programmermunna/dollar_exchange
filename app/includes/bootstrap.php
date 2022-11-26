<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

include("function.messages.php");
include("function.user.php");
include("function.web.php");
include("function.language.php");
include("class.template.php");
include("function.email.php");
include("function.gateway.php");
include("function.exchange.php");
include("function.payment.php");
include("phpmailer/phpmailer.class.php");
include("payment_src/block_io.php");
include("function.pagination.php");
?>