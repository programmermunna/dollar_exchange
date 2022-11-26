<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$tpl = new Template("app/templates/".$settings['default_template']."/reviews.html",$lang);
$reviews_list = '';
    $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
	$limit = 10;
	$startpoint = ($page * $limit) - $limit;
	if($page == 1) {
		$i = 1;
	} else {
		$i = $page * $limit;
    }
    $statement = "ce_users_reviews WHERE status='1'";
	$E_Query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
	if($E_Query->num_rows>0) {
        while($review = $E_Query->fetch_assoc()) {
            $retpl = new Template("app/templates/".$settings['default_template']."/rows/review_row.html",$lang);
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
            $retpl->set("date",date("d/m/Y h:ma",$review['posted']));
            $reviews_list .= $retpl->output();
        }
    }
    $ver = $settings['url']."reviews";
	if(web_pagination($statement,$ver,$limit,$page)) {
		$pages = web_pagination($statement,$ver,$limit,$page);
	} else {
		$pages = '';
	}
	$tpl->set("pages",$pages);
$tpl->set("reviews_list",$reviews_list);
echo $tpl->output();
?>