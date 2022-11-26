<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$b = protect($_GET['prefix']);
if($b == "faq") {
    $tpl = new Template("app/templates/".$settings['default_template']."/faq.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $faq_rows = '';
    $i=1;
    $query = $db->query("SELECT * FROM ce_faq ORDER BY id"); 
    if($query->num_rows>0) {
        while($row = $query->fetch_assoc()) {
            $ftpl = new Template("app/templates/".$settings['default_template']."/rows/faq_row.html",$lang);
            $ftpl->set("num",$i);
            $ftpl->set("question",$row['question']);
            $ftpl->set("answer",$row['answer']);
            $faq_rows .= $ftpl->output();
            $i++;
        }
    }
    $tpl->set("faq_rows",$faq_rows);
    echo $tpl->output();   
} elseif($b == "discount_system") {
    $tpl = new Template("app/templates/".$settings['default_template']."/discount_system.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $discount_rows = '';
    $query = $db->query("SELECT * FROM ce_discount_system ORDER BY discount_level");
    if($query->num_rows>0) {
        while($row = $query->fetch_assoc()) {
            $dtpl = new Template("app/templates/".$settings['default_template']."/rows/account_discount_row.html",$lang);    
            $dtpl->set("discount_level",$row['discount_level']);
            $dtpl->set("from_value",$row['from_value']);
            $dtpl->set("to_value",$row['to_value']);
            $dtpl->set("currency",$row['currency']);
            $dtpl->set("discount_percentage",$row['discount_percentage']);
            $discount_rows .= $dtpl->output();
        }   
    }
    $tpl->set("discount_rows",$discount_rows);
    $UserMenu = '';
    if(checkSession()) {
        $umtpl = new Template("app/templates/".$settings['default_template']."/rows/e_user_logged.html",$lang);
        $umtpl->set("url",$settings['url']);
        $UserMenu= $umtpl->output();
    } else {
        $umtpl = new Template("app/templates/".$settings['default_template']."/rows/e_user_notlogged.html",$lang);
        $umtpl->set("url",$settings['url']);
        $UserMenu= $umtpl->output();
    }
    $tpl->set("UserMenu",$UserMenu);
    echo $tpl->output();   
} elseif($b == "affiliate_program") {
    $tpl = new Template("app/templates/".$settings['default_template']."/affiliate_program.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $tpl->set("referral_comission",$settings['referral_comission']);
    $UserMenu = '';
    if(checkSession()) {
        $umtpl = new Template("app/templates/".$settings['default_template']."/rows/e_user_logged.html",$lang);
        $umtpl->set("url",$settings['url']);
        $UserMenu= $umtpl->output();
    } else {
        $umtpl = new Template("app/templates/".$settings['default_template']."/rows/e_user_notlogged.html",$lang);
        $umtpl->set("url",$settings['url']);
        $UserMenu= $umtpl->output();
    }
    $tpl->set("UserMenu",$UserMenu);
    echo $tpl->output();   
} else {
    $query = $db->query("SELECT * FROM ce_pages WHERE prefix='$b'");
    if($query->num_rows>0) {
        $row = $query->fetch_assoc();
        $tpl = new Template("app/templates/".$settings['default_template']."/page.html",$lang);
        $tpl->set("url",$settings['url']);
        $tpl->set("name",$settings['name']);
        $tpl->set("title",$row['title']);
        $tpl->set("content",$row['content']);
        echo $tpl->output();   
    } else {
        header("Location: $settings[url]");
    }
}
?>