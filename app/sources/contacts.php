<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$tpl = new Template("app/templates/".$settings['default_template']."/contacts.html",$lang);
$results = '';
$CEAction = protect($_POST['ce_btn']);
if(isset($CEAction) && $CEAction == "send") {
    $first_name = protect($_POST['first_name']);
    $last_name = protect($_POST['last_name']);
    $email = protect($_POST['email']);
    $subject = protect($_POST['subject']);
    $message = protect($_POST['message']);
    $recaptcha_response = protect($_POST['g-recaptcha-response']);
    if(empty($first_name) or empty($last_name) or empty($email) or empty($subject) or empty($message)) {
        $results = error($lang['error_1']);
    } elseif(!isValidEmail($email)) {
        $results = error($lang['error_23']);
    } elseif($settings['enable_recaptcha'] == "1" && !VerifyGoogleRecaptcha($recaptcha_response)) {
        $results = error($lang['error_58']);  
    } else {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = $smtpconf["host"];
        $mail->Port = $smtpconf["port"];
        $mail->SMTPAuth = $smtpconf['SMTPAuth'];
        $mail->Username = $smtpconf["user"];
        $mail->Password = $smtpconf["pass"];
        $name = $first_name.' '.$last_name;
        $mail->setFrom($email, $name);
        $mail->addAddress($settings['supportemail'], $settings['supportemail']);
        //Set the subject line
        $mail->Subject = $subject;
        $mail->msgHTML($message);
        //Replace the plain text body with one created manually
        $mail->AltBody = $message;
        //Attach an image file
        //send the message, check for errors
        $mail->send();
        $results = success($lang['success_10']);
    }
}
$tpl->set("results",$results);
$insert_recaptcha = '';
if($settings['enable_recaptcha'] == "1") {
    $captcha_tpl = new Template("app/templates/".$settings['default_template']."/rows/recaptcha.html",$lang);
    $captcha_tpl->set("recaptcha_publickey",$settings['recaptcha_publickey']);
    $insert_recaptcha = $captcha_tpl->output();   
}
$tpl->set("insert_recaptcha",$insert_recaptcha);
echo $tpl->output();
?>