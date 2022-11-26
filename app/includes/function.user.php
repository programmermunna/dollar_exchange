<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

function checkAdminSession() {
	if(isset($_SESSION['ce_admin_uid'])) {
		return true;
	} else {
		return false;
	}
}

function checkOperatorSession() {
	if(isset($_SESSION['ce_operator_uid'])) {
		return true;
	} else {
		return false;
	}
}

function isValidUsername($str) {
    return preg_match('/^[a-zA-Z0-9-_]+$/',$str);
}

function isValidEmail($str) {
	return filter_var($str, FILTER_VALIDATE_EMAIL);
}

function checkSession() {
	if(isset($_SESSION['ce_uid'])) {
		return true;
	} else {
		return false;
	}
}

function idinfo($uid,$value) {
	global $db;
	$query = $db->query("SELECT * FROM ce_users WHERE id='$uid'");
	$row = $query->fetch_assoc();
	return $row[$value];
}	

function opinfo($uid,$value) {
	global $db;
	$query = $db->query("SELECT * FROM ce_operators WHERE id='$uid'");
	$row = $query->fetch_assoc();
	return $row[$value];
}	
?>