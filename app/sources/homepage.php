<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$tpl = new Template("app/templates/".$settings['default_template']."/homepage.html",$lang);
$tpl->set("url",$settings['url']);
$tpl->set("name",$settings['name']);
$send_list = '';
$receive_list = '';
$reserve_list = '';
$fid = '';
$sid = '';
$i = 1;
$r = 1;
$b = protect($_GET['b']);

$QueryGateways = $db->query("SELECT * FROM ce_gateways WHERE allow_send='1' ORDER BY id");
if($QueryGateways->num_rows>0) {
    while($gt = $QueryGateways->fetch_assoc()) {
        if($gt['merchant_source'] == "stripe" or $gt['merchant_source'] == "2checkout") {
            $send_list .= '<option value="'.$gt[id].'">VISA/MasterCard '.$gt[currency].'</option>';
        } else {
        $send_list .= '<option value="'.$gt[id].'">'.$gt[name].' '.$gt[currency].'</option>';
        }
        if($i == 1) {
            $fid = $gt['id'];
        }
        $i++;
    }
} 
$QueryDirections = $db->query("SELECT * FROM ce_gateways_directions WHERE gateway_id='$fid'"); 
if($QueryDirections->num_rows>0) {
    $gd = $QueryDirections->fetch_assoc();
    $directions = explode(",",$gd['directions']);
    foreach($directions as $k=>$v) {
        if(gatewayinfo($v,"merchant_source") == "stripe" or gatewayinfo($v,"merchant_source") == "2checkout") { 
            $receive_list .= '<option value="'.$v.'">VISA/MasterCard '.gatewayinfo($v,"currency").'</option>';
        } else { 
        $receive_list .= '<option value="'.$v.'">'.gatewayinfo($v,"name").' '.gatewayinfo($v,"currency").'</option>';
        }
        if($r == 1) {
            $sid = $v;
        }
        $r++;
    }
}
$rate = get_rates($fid,$sid);
$rate_from = $rate['rate_from'];
$rate_to = $rate['rate_to'];
$tpl->set("am_send",$rate_from);
$tpl->set("am_receive",$rate_to);
$tpl->set("rate_from",$rate_from);
$tpl->set("rate_to",$rate_to);
$tpl->set("currency_from",$rate['currency_from']);
$tpl->set("currency_to",$rate['currency_to']);
$tpl->set("reserve",gatewayinfo($sid,"reserve"));
$tpl->set("send_icon",gticon($fid));
$tpl->set("receive_icon",gticon($sid));
$tpl->set("sic1",gatewayinfo($fid,"is_crypto"));
$tpl->set("sic2",gatewayinfo($sid,"is_crypto"));
$tpl->set("send_list",$send_list);
$tpl->set("receive_list",$receive_list);
$ReservesQuery = $db->query("SELECT * FROM ce_gateways ORDER BY id");
if($ReservesQuery->num_rows>0){
    while($reserve = $ReservesQuery->fetch_assoc()) {
        $rtpl = new Template("app/templates/".$settings['default_template']."/rows/reserve.html",$lang);
        $rtpl->set("gateway_icon",gticon($reserve['id']));
        if($reserve['merchant_source'] == "stripe" or $reserve['merchant_source'] == "2checkout") { 
            $rtpl->set("gateway_name","VISA/MasterCard");     
        } else {
            $rtpl->set("gateway_name",$reserve['name']);
        }
        $rtpl->set("gateway_reserve",$reserve['reserve']);
        $rtpl->set("gateway_currency",$reserve['currency']);
        $reserve_list .= $rtpl->output();
    }
}
$tpl->set("reserve_list",$reserve_list);
$latest_news = '';
$NewsQuery = $db->query("SELECT * FROM ce_news ORDER BY id DESC LIMIT 10");
if($NewsQuery->num_rows>0) {
    while($news = $NewsQuery->fetch_assoc()) {
        $newtpl = new Template("app/templates/".$settings['default_template']."/rows/home_latest_news_row.html",$lang);
        $newtpl->set("url",$settings['url']);
        $newtpl->set("id",$news['id']);
        $newtpl->set("title",$news['title']);
        $newtpl->set("date",date("d/m/y h:ia",$news['created']));
        $latest_news .= $newtpl->output();
    }
}
$tpl->set("latest_news",$latest_news);
$reviews = '';
$ReviewsQuery = $db->query("SELECT * FROM ce_users_reviews WHERE status='1' ORDER BY RAND() LIMIT 5");
if($ReviewsQuery->num_rows>0) {
    while($review = $ReviewsQuery->fetch_assoc()) {
        $retpl = new Template("app/templates/".$settings['default_template']."/rows/home_review_row.html",$lang);
        if($review['type'] == "1") {
            $review_class = 'text text-success';
            $review_icon = 'fa fa-smile';
        } elseif($review['type'] == "2") {
            $review_class = 'text text-danger';
            $review_icon = 'fa fa-frown-o';
        } elseif($review['type'] == "3") {
            $review_class = 'text text-warning';
            $review_icon = 'fa fa-meh-o';
        } else { }
        $retpl->set("review_class",$review_class);
        $retpl->set("review_icon",$review_icon);
        $retpl->set("display_name",$review['display_name']);
        $retpl->set("comment",$review['comment']);
        $retpl->set("date",date("d/m/y h:ia",$review['posted']));
        $reviews .= $retpl->output();
    }
}
$tpl->set("reviews",$reviews);
$latest_exchanges = '';
$ExchangesQuery = $db->query("SELECT * FROM ce_orders ORDER BY id DESC LIMIT 9");
if($ExchangesQuery->num_rows>0) {
    while($ex = $ExchangesQuery->fetch_assoc()) {
        $extpl = new Template("app/templates/".$settings['default_template']."/rows/home_latest_exchange_row.html",$lang);
        $extpl->set("gateway_send_icon",gticon($ex['gateway_send']));
        $extpl->set("gateway_receive_icon",gticon($ex['gateway_receive']));
        $extpl->set("amount_send",$ex['amount_send']);
        $extpl->set("amount_send_currency",gatewayinfo($ex['gateway_send'],"currency"));
        $extpl->set("amount_receive",$ex['amount_receive']);
        
        // Custom added...........................
        
        $status = ce_decodeStatus($ex['status']);
        $statusstyle = $status['style'];
        $statustext = $status['text'];
        
        
        $extpl->set("statusstyle",$statusstyle);
        $extpl->set("statustext",$statustext);
        
        $extpl->set("amount_receive_currency",gatewayinfo($ex['gateway_receive'],"currency"));
        
        $extpl->set("date",date("d/m/y h:ia",$ex['created']));
        $latest_exchanges .= $extpl->output();
    }
}
$tpl->set("latest_exchanges",$latest_exchanges);
$homepage_wops = '';
if($settings['show_operator_status'] == "1" && $settings['show_worktime'] == "1") {
    $hwops = new Template("app/templates/".$settings['default_template']."/rows/homepage_wops.html",$lang);
    $worktpl = new Template("app/templates/".$settings['default_template']."/rows/worktime.html",$lang);
    $worktpl->set("worktime_start",$settings['worktime_start']);
    $worktpl->set("worktime_end",$settings['worktime_end']);
    $worktpl->set("worktime_gmt",$settings['worktime_gmt']);
    $hwops->set("worktime",$worktpl->output());   
    if($settings['operator_status'] == "1") {
        $wops = new Template("app/templates/".$settings['default_template']."/rows/operator_online.html",$lang);
    } else {
        $wops = new Template("app/templates/".$settings['default_template']."/rows/operator_offline.html",$lang);
    }
    $hwops->set("operator_status",$wops->output());
    $homepage_wops = $hwops->output();
} elseif($settings['show_operator_status'] == "1" && $settings['show_worktime'] == "0") {
    $hwops = new Template("app/templates/".$settings['default_template']."/rows/homepage_wops.html",$lang);
    $hwops->set("worktime","");   
    if($settings['operator_status'] == "1") {
        $wops = new Template("app/templates/".$settings['default_template']."/rows/operator_online.html",$lang);
    } else {
        $wops = new Template("app/templates/".$settings['default_template']."/rows/operator_offline.html",$lang);
    }
    $hwops->set("operator_status",$wops->output());
    $homepage_wops = $hwops->output();
} elseif($settings['show_operator_status'] == "0" && $settings['show_worktime'] == "1") {
    $hwops = new Template("app/templates/".$settings['default_template']."/rows/homepage_wops.html",$lang);
    $worktpl = new Template("app/templates/".$settings['default_template']."/rows/worktime.html",$lang);
    $worktpl->set("worktime_start",$settings['worktime_start']);
    $worktpl->set("worktime_end",$settings['worktime_end']);
    $worktpl->set("worktime_gmt",$settings['worktime_gmt']);
    $hwops->set("worktime",$worktpl->output());  
    $hwops->set("operator_status","");
    $homepage_wops = $hwops->output();
} else { }
$tpl->set("homepage_wops",$homepage_wops);
echo $tpl->output();

if($b == "view") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_news WHERE id='$id'");
    if($query->num_rows==0) {
        $redirect = $settings['url']."news";
        header("Location: $redirect");
    }
    $row = $query->fetch_assoc();
    $update = $db->query("UPDATE ce_news SET views=views+1 WHERE id='$id'");
    $tpl = new Template("app/templates/".$settings['default_template']."/news_view.html",$lang);
    $tpl->set("title",$row['title']);
    $tpl->set("content",$row['content']);
    $tpl->set("date",date("d/m/y h:ia",$row['created']));
    $tpl->set("id",$row['id']);
    $tpl->set("url",$settings['url']);
    $recent_popular = '';
    $PopularQuery = $db->query("SELECT * FROM ce_news ORDER BY views DESC LIMIT 5");
    if($PopularQuery->num_rows>0) {
        while($p = $PopularQuery->fetch_assoc()) {
            $rtpl = new Template("app/templates/".$settings['default_template']."/rows/recent_popular_row.html",$lang);
            $rtpl->set("title",$p['title']);
            $rtpl->set("date",date("d/m/y h:ia",$p['created']));
            $rtpl->set("id",$p['id']);
            $rtpl->set("url",$settings['url']);
            $recent_popular .= $rtpl->output();
        }
    }
    $tpl->set("recent_popular",$recent_popular);
    echo $tpl->output();
} else {
    $tpl = new Template("app/templates/".$settings['default_template']."/news.html",$lang);
    $news_list = '';
    $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
	$limit = 10;
	$startpoint = ($page * $limit) - $limit;
	if($page == 1) {
		$i = 1;
	} else {
		$i = $page * $limit;
    }
    $statement = "ce_news";
	$E_Query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
	if($E_Query->num_rows>0) {
        while($e_row = $E_Query->fetch_assoc()) {
            $ntpl = new Template("app/templates/".$settings['default_template']."/rows/news_row.html",$lang);
            $ntpl->set("title",$e_row['title']);
            $ntpl->set("date",date("d/m/y h:ia",$e_row['created']));
            $ntpl->set("id",$e_row['id']);
            $ntpl->set("url",$settings['url']);
            $ntpl->set("content",croptext($e_row['content'],200));
            $news_list .= $ntpl->output();
        }
    }
    $ver = $settings['url']."news";
	if(web_pagination($statement,$ver,$limit,$page)) {
		$pages = web_pagination($statement,$ver,$limit,$page);
	} else {
		$pages = '';
	}
	$tpl->set("pages",$pages);
    $tpl->set("news_list",$news_list);
    $recent_popular = '';
    $PopularQuery = $db->query("SELECT * FROM ce_news ORDER BY views DESC LIMIT 5");
    if($PopularQuery->num_rows>0) {
        while($p = $PopularQuery->fetch_assoc()) {
            $rtpl = new Template("app/templates/".$settings['default_template']."/rows/recent_popular_row.html",$lang);
            $rtpl->set("title",$p['title']);
            $rtpl->set("date",date("d/m/y h:ia",$p['created']));
            $rtpl->set("id",$p['id']);
            $rtpl->set("url",$settings['url']);
            $recent_popular .= $rtpl->output();
        }
    }
    $tpl->set("recent_popular",$recent_popular);
    echo $tpl->output();
}
?>
