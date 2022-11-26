<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

function ce_decodeStatus($status) {
    global $db, $lang;
    if($status == "1") { 
        $text = $lang['status_order_1'];
        $class_style = 'badge badge-warning';
    } elseif($status == "2") {
        $text = $lang['status_order_2'];
        $class_style = 'badge badge-warning';
    } elseif($status == "3") {
        $text = $lang['status_order_3']; 
        $class_style = 'badge badge-info';
    } elseif($status == "4") {
        $text = $lang['status_order_4']; 
        $class_style = 'badge badge-success';
    } elseif($status == "5") {
        $text = $lang['status_order_5']; 
        $class_style = 'badge badge-danger';
    } elseif($status == "6") {
        $text = $lang['status_order_6'];
        $class_style = 'badge badge-danger';
    } else {
        $text = $lang['status_unknown'];
        $class_style = 'badge badge-primary'; 
    }
    $data = array();
    $data['text'] = $text;
    $data['style'] = $class_style;
    return $data;
}

function orderinfo($id,$value) {
	global $db;
	$query = $db->query("SELECT * FROM ce_orders WHERE id='$id'");
	$row = $query->fetch_assoc();
	return $row[$value];
}	

function formatBytes($bytes, $precision = 2) { 
    if ($bytes > pow(1024,3)) return round($bytes / pow(1024,3), $precision)."GB";
    else if ($bytes > pow(1024,2)) return round($bytes / pow(1024,2), $precision)."MB";
    else if ($bytes > 1024) return round($bytes / 1024, $precision)."KB";
    else return ($bytes)."B";
} 

function croptext($text,$chars) {
	$string = $text;
	if(strlen($string) > $chars) $string = substr($string, 0, $chars);
	return $string;
}
?>