<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

function CE_Send_VE($email) {
	global $db, $settings, $smtpconf;
	$UserQuery = $db->query("SELECT * FROM ce_users WHERE email='$email'");
	$user = $UserQuery->fetch_assoc();
	$email_hash = randomHash(25);
	$update = $db->query("UPDATE ce_users SET email_hash='$email_hash' WHERE id='$user[id]'");
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	//Set the hostname of the mail server
	$mail->Host = $smtpconf["host"];
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = $smtpconf["port"];
	//Whether to use SMTP authentication
	$mail->SMTPAuth = $smtpconf['SMTPAuth'];
	//Username to use for SMTP authentication
	$mail->Username = $smtpconf["user"];
	//Password to use for SMTP authentication
	$mail->Password = $smtpconf["pass"];
	//Set who the message is to be sent from
	$mail->setFrom($settings['infoemail'], $settings['name']);
	//Set who the message is to be sent to
	$mail->addAddress($email, $email);
	//Set the subject line
	$mail->Subject = 'Verify your '.$settings[name].' Account';
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$tpl = new Template("app/templates/Email_Templates/Email_Verification.tpl",$lang);
	$tpl->set("url",$settings['url']);
	$tpl->set("name",$settings['name']);
	$tpl->set("email",$email);
	$tpl->set("hash",$email_hash);
	$email_template = $tpl->output();
	$mail->msgHTML($email_template);
	//Replace the plain text body with one created manually
	$mail->AltBody = 'Preview message as HTML.';
	//Attach an image file
	//send the message, check for errors
	$mail->send();
}

function CE_Send_ACE($email) {
	global $db, $settings, $smtpconf;
	$UserQuery = $db->query("SELECT * FROM ce_users WHERE email='$email'");
	$user = $UserQuery->fetch_assoc();
	$email_hash = randomHash(6);
	$email_hash = strtoupper($email_hash);
	$update = $db->query("UPDATE ce_users SET email_hash='$email_hash' WHERE id='$user[id]'");
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	//Set the hostname of the mail server
	$mail->Host = $smtpconf["host"];
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = $smtpconf["port"];
	//Whether to use SMTP authentication
	$mail->SMTPAuth = $smtpconf['SMTPAuth'];
	//Username to use for SMTP authentication
	$mail->Username = $smtpconf["user"];
	//Password to use for SMTP authentication
	$mail->Password = $smtpconf["pass"];
	//Set who the message is to be sent from
	$mail->setFrom($settings['infoemail'], $settings['name']);
	//Set who the message is to be sent to
	$mail->addAddress($email, $email);
	//Set the subject line
	$mail->Subject = $settings[name].' Authorization Code';
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$tpl = new Template("app/templates/Email_Templates/Authorization_Code.tpl",$lang);
	$tpl->set("url",$settings['url']);
	$tpl->set("name",$settings['name']);
	$tpl->set("email",$email);
	$tpl->set("email_hash",$email_hash);
	$email_template = $tpl->output();
	$mail->msgHTML($email_template);
	//Replace the plain text body with one created manually
	$mail->AltBody = 'Preview message as HTML.';
	//Attach an image file
	//send the message, check for errors
	$mail->send();
}

function CE_Send_RPE($email) {
	global $db, $settings, $smtpconf;
	$UserQuery = $db->query("SELECT * FROM ce_users WHERE email='$email'");
	$user = $UserQuery->fetch_assoc();
	$hash = randomHash(25);
	$update = $db->query("UPDATE ce_users SET password_hash='$hash' WHERE id='$user[id]'");
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	//Set the hostname of the mail server
	$mail->Host = $smtpconf["host"];
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = $smtpconf["port"];
	//Whether to use SMTP authentication
	$mail->SMTPAuth = $smtpconf['SMTPAuth'];
	//Username to use for SMTP authentication
	$mail->Username = $smtpconf["user"];
	//Password to use for SMTP authentication
	$mail->Password = $smtpconf["pass"];
	//Set who the message is to be sent from
	$mail->setFrom($settings['infoemail'], $settings['name']);
	//Set who the message is to be sent to
	$mail->addAddress($email, $email);
	//Set the subject line
	$mail->Subject = $settings[name].' Password Recovery';
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$tpl = new Template("app/templates/Email_Templates/Password_Recovery.tpl",$lang);
	$tpl->set("url",$settings['url']);
	$tpl->set("name",$settings['name']);
	$tpl->set("email",$email);
	$tpl->set("hash",$hash);
	$email_template = $tpl->output();
	$mail->msgHTML($email_template);
	//Replace the plain text body with one created manually
	$mail->AltBody = 'Preview message as HTML.';
	//Attach an image file
	//send the message, check for errors
	$mail->send();
}

function CE_SendToAdmin($first_name,$last_name,$email,$subject,$message) {
	global $db, $settings, $smtpconf;
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	//Set the hostname of the mail server
	$mail->Host = $smtpconf["host"];
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = $smtpconf["port"];
	//Whether to use SMTP authentication
	$mail->SMTPAuth = $smtpconf['SMTPAuth'];
	//Username to use for SMTP authentication
	$mail->Username = $smtpconf["user"];
	//Password to use for SMTP authentication
	$mail->Password = $smtpconf["pass"];
	//Set who the message is to be sent from
	$mail->setFrom($email, $first_name.' '.$last_name);
	//Set who the message is to be sent to
	$mail->addAddress($settings['supportemail'],$settings['supportemail']);
	//Set the subject line
	$mail->Subject = $subject;
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$mail->msgHTML($message);
	//Replace the plain text body with one created manually
	$mail->AltBody = $message;
	//Attach an image file
	//send the message, check for errors
	$mail->send();
}

function CE_Send_CreateOrder($email,$id) {
	global $db, $settings, $smtpconf;
	$UserQuery = $db->query("SELECT * FROM ce_users WHERE email='$email'");
	$user = $UserQuery->fetch_assoc();
	$OrderQuery = $db->query("SELECT * FROM ce_orders WHERE id='$id'");
	$order = $OrderQuery->fetch_assoc();
	$gateway_send_name = gatewayinfo($order['gateway_send'],"name");
	$gateway_send_currency = gatewayinfo($order['gateway_send'],"currency");
	$gateway_receive_name = gatewayinfo($order['gateway_receive'],"name");
	$gateway_receive_currency = gatewayinfo($order['gateway_receive'],"currency");
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging 
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	//Set the hostname of the mail server
	$mail->Host = $smtpconf["host"];
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = $smtpconf["port"];
	//Whether to use SMTP authentication
	$mail->SMTPAuth = $smtpconf['SMTPAuth'];
	//Username to use for SMTP authentication
	$mail->Username = $smtpconf["user"];
	//Password to use for SMTP authentication
	$mail->Password = $smtpconf["pass"];
	//Set who the message is to be sent from
	$mail->setFrom($settings['infoemail'], $settings['name']);
	//Set who the message is to be sent to
	$mail->addAddress($email, $email);
	//Set the subject line
	$mail->Subject = 'Order #'.$order[id].' was created';
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$tpl = new Template("app/templates/Email_Templates/Order_Created.tpl",$lang);
	$tpl->set("url",$settings['url']);
	$tpl->set("name",$settings['name']);
	$tpl->set("email",$email);
	$tpl->set("hash",$order['order_hash']);
	$tpl->set("gateway_send_name",$gateway_send_name);
	$tpl->set("gateway_send_currency",$gateway_send_currency);
	$tpl->set("gateway_receive_name",$gateway_receive_name);
	$tpl->set("gateway_receive_currency",$gateway_receive_currency);
	$tpl->set("amount_send",$order['amount_send']);
	$tpl->set("amount_receive",$order['amount_receive']);
	$email_template = $tpl->output();
	$mail->msgHTML($email_template);
	//Replace the plain text body with one created manually
	$mail->AltBody = 'Preview message as HTML.';
	//Attach an image file
	//send the message, check for errors
	$mail->send();
}


function CE_Send_NewOrderToAdmin($id) {
	global $db, $settings, $smtpconf;
	$UserQuery = $db->query("SELECT * FROM ce_users WHERE email='$email'");
	$user = $UserQuery->fetch_assoc();
	$OrderQuery = $db->query("SELECT * FROM ce_orders WHERE id='$id'");
	$order = $OrderQuery->fetch_assoc();
	$gateway_send_name = gatewayinfo($order['gateway_send'],"name");
	$gateway_send_currency = gatewayinfo($order['gateway_send'],"currency");
	$gateway_receive_name = gatewayinfo($order['gateway_receive'],"name");
	$gateway_receive_currency = gatewayinfo($order['gateway_receive'],"currency");
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging 
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	//Set the hostname of the mail server
	$mail->Host = $smtpconf["host"];
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = $smtpconf["port"];
	//Whether to use SMTP authentication
	$mail->SMTPAuth = $smtpconf['SMTPAuth'];
	//Username to use for SMTP authentication
	$mail->Username = $smtpconf["user"];
	//Password to use for SMTP authentication
	$mail->Password = $smtpconf["pass"];
	//Set who the message is to be sent from
	$mail->setFrom($settings['infoemail'], $settings['name']);
	//Set who the message is to be sent to
	$GetAdmins = $db->query("SELECT * FROM ce_users WHERE level='1' ORDER BY id");
	if($GetAdmins->num_rows>0) {
		while($admin = $GetAdmins->fetch_assoc()) {
			$mail->addAddress($admin['email'], $admin['email']);
		}
	}
	//Set the subject line
	$mail->Subject = 'You have new exchange order #'.$order[id];
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$tpl = new Template("app/templates/Email_Templates/Admin_New_Order.tpl",$lang);
	$tpl->set("url",$settings['url']);
	$tpl->set("name",$settings['name']);
	$tpl->set("email",$email);
	$tpl->set("hash",$order['order_hash']);
	$tpl->set("gateway_send_name",$gateway_send_name);
	$tpl->set("gateway_send_currency",$gateway_send_currency);
	$tpl->set("gateway_receive_name",$gateway_receive_name);
	$tpl->set("gateway_receive_currency",$gateway_receive_currency);
	$tpl->set("amount_send",$order['amount_send']);
	$tpl->set("amount_receive",$order['amount_receive']);
	$email_template = $tpl->output();
	$mail->msgHTML($email_template);
	//Replace the plain text body with one created manually
	$mail->AltBody = 'Preview message as HTML.';
	//Attach an image file
	//send the message, check for errors
	$mail->send();
}

function CE_Send_NewProfit($email,$profit,$cur) {
	global $db, $settings, $smtpconf;
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging 
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	//Set the hostname of the mail server
	$mail->Host = $smtpconf["host"];
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = $smtpconf["port"];
	//Whether to use SMTP authentication
	$mail->SMTPAuth = $smtpconf['SMTPAuth'];
	//Username to use for SMTP authentication
	$mail->Username = $smtpconf["user"];
	//Password to use for SMTP authentication
	$mail->Password = $smtpconf["pass"];
	//Set who the message is to be sent from
	$mail->setFrom($settings['infoemail'], $settings['name']);
	//Set who the message is to be sent to
	$mail->addAddress($email, $email);
	//Set the subject line
	$mail->Subject = 'New profit earned by our Referral Program';
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$tpl = new Template("../../app/templates/Email_Templates/New_Profit.tpl",$lang);
	$tpl->set("url",$settings['url']);
	$tpl->set("name",$settings['name']);
	$tpl->set("profit",$profit);
	$tpl->set("cur",$cur);
	$email_template = $tpl->output();
	$mail->msgHTML($email_template);
	//Replace the plain text body with one created manually
	$mail->AltBody = 'Preview message as HTML.';
	//Attach an image file
	//send the message, check for errors
	$mail->send();
}

function CE_Send_ReserveUpdated($email,$id) {
	global $db, $settings, $smtpconf;
	$query = $db->query("SELECT * FROM ce_reserve_requests WHERE id='$id'");
	$row = $query->fetch_assoc();
	$gateway_name = gatewayinfo($row['gateway_id'],"name");
	$gateway_currency = gatewayinfo($row['gateway_id'],"currency");
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	//Set the hostname of the mail server
	$mail->Host = $smtpconf["host"];
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = $smtpconf["port"];
	//Whether to use SMTP authentication
	$mail->SMTPAuth = $smtpconf['SMTPAuth'];
	//Username to use for SMTP authentication
	$mail->Username = $smtpconf["user"];
	//Password to use for SMTP authentication
	$mail->Password = $smtpconf["pass"];
	//Set who the message is to be sent from
	$mail->setFrom($settings['infoemail'], $settings['name']);
	//Set who the message is to be sent to
	$mail->addAddress($email, $email);
	//Set the subject line
	$mail->Subject = 'Reserve Update Notification';
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$tpl = new Template("../../app/templates/Email_Templates/Reserve_Updated.tpl",$lang);
	$tpl->set("url",$settings['url']);
	$tpl->set("name",$settings['name']);
	$tpl->set("email",$email);
	$tpl->set("gateway_name",$gateway_name);
	$tpl->set("gateway_currency",$gateway_currency);
	$tpl->set("amount",$row['amount']);
	$email_template = $tpl->output();
	$mail->msgHTML($email_template);
	//Replace the plain text body with one created manually
	$mail->AltBody = 'Preview message as HTML.';
	//Attach an image file
	//send the message, check for errors
	$mail->send();
}

function CE_Send_OrderCompleted($email,$id) {
	global $db, $settings, $smtpconf;
	$query = $db->query("SELECT * FROM ce_orders WHERE id='$id'");
	$row = $query->fetch_assoc();
	$gateway_send_name = gatewayinfo($row['gateway_send'],"name");
	$gateway_send_currency = gatewayinfo($row['gateway_send'],"currency");
	$gateway_receive_name = gatewayinfo($row['gateway_receive'],"name");
	$gateway_receive_currency = gatewayinfo($row['gateway_receive'],"currency");
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	//Set the hostname of the mail server
	$mail->Host = $smtpconf["host"];
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = $smtpconf["port"];
	//Whether to use SMTP authentication
	$mail->SMTPAuth = $smtpconf['SMTPAuth'];
	//Username to use for SMTP authentication
	$mail->Username = $smtpconf["user"];
	//Password to use for SMTP authentication
	$mail->Password = $smtpconf["pass"];
	//Set who the message is to be sent from
	$mail->setFrom($settings['infoemail'], $settings['name']);
	//Set who the message is to be sent to
	$mail->addAddress($email, $email);
	//Set the subject line
	$mail->Subject = 'Order #'.$row[id].' Completed';
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$tpl = new Template("../../app/templates/Email_Templates/Order_Completed.tpl",$lang);
	$tpl->set("url",$settings['url']);
	$tpl->set("name",$settings['name']);
	$tpl->set("email",$email);
	$tpl->set("gateway_send_name",$gateway_send_name);
	$tpl->set("gateway_receive_name",$gateway_receive_name);
	$tpl->set("gateway_send_currency",$gateway_send_currency);
	$tpl->set("gateway_receive_currency",$gateway_receive_currency);
	$tpl->set("amount_send",$row['amount_send']);
	$tpl->set("amount_receive",$row['amount_receive']);
	$tpl->set("order_id",$row['id']);
	$tpl->set("transaction_receive",$row['transaction_receive']);
	$email_template = $tpl->output();
	$mail->msgHTML($email_template);
	//Replace the plain text body with one created manually
	$mail->AltBody = 'Preview message as HTML.';
	//Attach an image file
	//send the message, check for errors
	$mail->send();
}

function CE_Send_OrderUpdated($email,$id) {
	global $db, $settings, $smtpconf;
	$query = $db->query("SELECT * FROM ce_orders WHERE id='$id'");
	$row = $query->fetch_assoc();
	$gateway_send_name = gatewayinfo($row['gateway_send'],"name");
	$gateway_send_currency = gatewayinfo($row['gateway_send'],"currency");
	$gateway_receive_name = gatewayinfo($row['gateway_receive'],"name");
	$gateway_receive_currency = gatewayinfo($row['gateway_receive'],"currency");
	$status = ce_decodeStatus($row['status']);
	$status = $status['text'];
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	//Set the hostname of the mail server
	$mail->Host = $smtpconf["host"];
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = $smtpconf["port"];
	//Whether to use SMTP authentication
	$mail->SMTPAuth = $smtpconf['SMTPAuth'];
	//Username to use for SMTP authentication
	$mail->Username = $smtpconf["user"];
	//Password to use for SMTP authentication
	$mail->Password = $smtpconf["pass"];
	//Set who the message is to be sent from
	$mail->setFrom($settings['infoemail'], $settings['name']);
	//Set who the message is to be sent to
	$mail->addAddress($email, $email);
	//Set the subject line
	$mail->Subject = 'Order #'.$row[id].' Completed';
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$tpl = new Template("../../app/templates/Email_Templates/Order_Status_Changed.tpl",$lang);
	$tpl->set("url",$settings['url']);
	$tpl->set("name",$settings['name']);
	$tpl->set("email",$email);
	$tpl->set("hash",$row['order_hash']);
	$tpl->set("status",$status);
	$tpl->set("gateway_send_name",$gateway_send_name);
	$tpl->set("gateway_send_currency",$gateway_send_currency);
	$tpl->set("amount_send",$row['amount_send']);
	$tpl->set("amount_receive",$row['amount_receive']);
	$tpl->set("order_id",$row['id']);
	$email_template = $tpl->output();
	$mail->msgHTML($email_template);
	//Replace the plain text body with one created manually
	$mail->AltBody = 'Preview message as HTML.';
	//Attach an image file
	//send the message, check for errors
	$mail->send();
}

function CE_SendToUser($email,$subject,$message,$fromname,$fromemail) {
	global $db, $settings, $smtpconf;
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	//Set the hostname of the mail server
	$mail->Host = $smtpconf["host"];
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = $smtpconf["port"];
	//Whether to use SMTP authentication
	$mail->SMTPAuth = $smtpconf['SMTPAuth'];
	//Username to use for SMTP authentication
	$mail->Username = $smtpconf["user"];
	//Password to use for SMTP authentication
	$mail->Password = $smtpconf["pass"];
	//Set who the message is to be sent from
	$mail->setFrom($fromemail, $fromname);
	//Set who the message is to be sent to
	$mail->addAddress($email, $email);
	//Set the subject line
	$mail->Subject = $subject;
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$mail->msgHTML($message);
	//Replace the plain text body with one created manually
	$mail->AltBody = $message;
	//Attach an image file
	//send the message, check for errors
	$mail->send();
}
?>