<?php
    if(!defined("CryptExchanger_INSTALLED")){
        header("HTTP/1.0 404 Not Found");
        exit;
    }
    
    $smtpconf = array();
    $smtpconf["host"] = "premium171-3.web-hosting.com"; // SMTP SERVER IP/HOST
    $smtpconf["user"] = "support@atopwallet.com";	// SMTP AUTH USERNAME if SMTPAuth is true
    $smtpconf["pass"] = "GWanQ9w;2ep;";	// SMTP AUTH PASSWORD if SMTPAuth is true
    $smtpconf["port"] = "26";	// SMTP SERVER PORT
    $smtpconf["ssl"] = "0"; // 1 -  YES, 0 - NO
    $smtpconf["SMTPAuth"] = true; // true / false
    ?>
    