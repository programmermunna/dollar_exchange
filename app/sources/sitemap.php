<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$tpl = new Template("app/templates/".$settings['default_template']."/sitemap.html",$lang);
$tpl->set("url",$settings['url']);
$directions = '';
$GetDirections = $db->query("SELECT * FROM ce_gateways ORDER BY id");
if($GetDirections->num_rows>0) {
    while($d = $GetDirections->fetch_assoc()) {
        $ddquery = $db->query("SELECT * FROM ce_gateways_directions WHERE gateway_id='$d[id]'");
        if($ddquery->num_rows>0) {
            $dr = $ddquery->fetch_assoc();
            $dirs = explode(",",$dr['directions']);
            foreach($dirs as $k=>$v) {
                $gtname_send = str_ireplace(" ","-",gatewayinfo($d['id'],"name"));
                $gtname_send = $gtname_send.'_'.gatewayinfo($d['id'],"currency");
                $gtname_receive = str_ireplace(" ","-",gatewayinfo($v,"name"));
                $gtname_receive = $gtname_receive.'_'.gatewayinfo($v,"currency");
                $_SESSION['ce_ex_amount'] = $amount_send;
                $redirect = $settings['url']."exchange/".$d[id]."_".$v."/".$gtname_send."-to-".$gtname_receive;
                $directions .= '<li><a href="'.$redirect.'">'.$d[name].' '.$d[currency].' <i class="fa fa-long-arrow-right"></i> '.gatewayinfo($v,"name").' '.gatewayinfo($v,"currency").'</a></li>';
            }
        }
    }
}
$tpl->set("directions",$directions);
$news_list = '';
$GetNews = $db->query("SELECT * FROM ce_news");
if($GetNews->num_rows>0) {
    while($news = $GetNews->fetch_assoc()) {
        $news_list .= '<li><a href="'.$settings[url].'news/view/'.$news[id].'">'.$news[title].'</a></li>';
    }
}
$tpl->set("news",$news_list);
echo $tpl->output();
?>