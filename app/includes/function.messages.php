<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

function success($text) {
	return '<div class="alert alert-success"><i class="fa fa-check"></i> <span>'.$text.'</span></div>';
}

function error($text) {
	return '<div class="alert alert-danger"><i class="fa fa-times"></i> <span>'.$text.'</span></div>';
}

function info($text) {
	return '<div class="alert alert-info" ><i class="fa fa-info-circle"></i> <span>'.$text.'</span></div>';
}

function asuccess($text) {
	return '<div class="alert alert-success"><i class="fa fa-check"></i> <span>'.$text.'</span></div>';
}

function aerror($text) {
	return '<div class="alert alert-danger"><i class="fa fa-times"></i> <span>'.$text.'</span></div>';
}

function ainfo($text) {
	return '<div class="alert alert-info"><i class="fa fa-info-circle"></i> <span>'.$text.'</span></div>';
}
?>