<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$b = protect($_GET['b']);
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
    $tpl->set("date",date("d/m/Y H:i",$row['created']));
    $tpl->set("id",$row['id']);
    $tpl->set("url",$settings['url']);
    $recent_popular = '';
    $PopularQuery = $db->query("SELECT * FROM ce_news ORDER BY views DESC LIMIT 5");
    if($PopularQuery->num_rows>0) {
        while($p = $PopularQuery->fetch_assoc()) {
            $rtpl = new Template("app/templates/".$settings['default_template']."/rows/recent_popular_row.html",$lang);
            $rtpl->set("title",$p['title']);
            $rtpl->set("date",date("d/m/Y H:i",$p['created']));
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
            $ntpl->set("date",date("d/m/Y H:i",$e_row['created']));
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
            $rtpl->set("date",date("d/m/Y H:i",$p['created']));
            $rtpl->set("id",$p['id']);
            $rtpl->set("url",$settings['url']);
            $recent_popular .= $rtpl->output();
        }
    }
    $tpl->set("recent_popular",$recent_popular);
    echo $tpl->output();
}
?>