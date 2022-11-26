<?php
define('CryptExchanger_INSTALLED',TRUE);
ob_start();
session_start();
error_reporting(0);
include("../configs/bootstrap.php");
include("../includes/bootstrap.php");
include(getLanguage($settings['url'],null,2));
if(checkOperatorSession()) {
	$OperQuery = $db->query("SELECT * FROM ce_operators WHERE id='$_SESSION[ce_operator_uid]'");
	$op = $OperQuery->fetch_assoc();
	include("sources/header.php");
	$a = protect($_GET['a']);
	switch($a) {
		case "exchange_gateways": include("sources/exchange_gateways.php"); break;
		case "exchange_directions": include("sources/exchange_directions.php"); break;
		case "exchange_rates": include("sources/exchange_rates.php"); break;
		case "exchange_rules": include("sources/exchange_rules.php"); break;
		case "exchange_orders": include("sources/exchange_orders.php"); break;
		case "users": include("sources/users.php"); break;
		case "reserve_requests": include("sources/reserve_requests.php"); break;
		case "withdrawals": include("sources/withdrawals.php"); break;
		case "reviews": include("sources/reviews.php"); break;
		case "tickets": include("sources/tickets.php"); break;
		case "news": include("sources/news.php"); break;
		case "pages": include("sources/pages.php"); break;
		case "faq": include("sources/faq.php"); break;
		case "logout": 
			unset($_SESSION['ce_operator_uid']);
			session_unset();
			session_destroy();
			header("Location: ./");
		break;
		default: include("sources/dashboard.php");
	}
	include("sources/footer.php");
} else {
    include("sources/login.php");
}
mysqli_close($db);