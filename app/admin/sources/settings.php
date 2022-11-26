<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$b = protect($_GET['b']);

if($b == "web") {
?>
<div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-cogs"></i> Web Settings</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <?php
                  $CEAction = protect($_POST['ce_btn']);
                  if(isset($CEAction) && $CEAction == "save") {
                    $title = protect($_POST['title']);
                    $description = protect($_POST['description']);
                    $keywords = protect($_POST['keywords']);
                    $name = protect($_POST['name']);
                    $url = protect($_POST['url']);
                    $infoemail = protect($_POST['infoemail']);
                    $supportemail = protect($_POST['supportemail']);
                    $curcnv_api = protect($_POST['curcnv_api']);
                    $au_rate_int = protect($_POST['au_rate_int']);
                    $referral_comission = protect($_POST['referral_comission']);
                    $referral_min_withdrawal = protect($_POST['referral_min_withdrawal']);
                    $show_operator_status = protect($_POST['show_operator_status']);
                    if($show_operator_status == "Yes") { $show_operator_status = 1; } else { $show_operator_status = 0; } 
                    $show_worktime = protect($_POST['show_worktime']);
                    if($show_worktime == "Yes") { $show_worktime = 1; } else { $show_worktime = 0; }
                    $worktime_start = protect($_POST['worktime_start']);
                    $worktime_end = protect($_POST['worktime_end']);
                    $worktime_gmt = protect($_POST['worktime_gmt']);
                    $expire_uncompleted_time = protect($_POST['expire_uncompleted_time']);

                    if(empty($expire_uncompleted_time) or empty($title) or empty($description) or empty($keywords) or empty($name) or empty($url) or empty($infoemail) or empty($supportemail) or empty($au_rate_int) or empty($referral_comission) or empty($referral_min_withdrawal)) {
                        echo error("All fields are required."); 
                    } elseif(!isValidURL($url)) { 
                        echo error("Please enter valid site url address.");
                    } elseif(!isValidEmail($infoemail)) { 
                        echo error("Please enter valid info email address.");
                    } elseif(!isValidEmail($supportemail)) { 
                        echo error("Please enter valid support email address.");
                    } elseif(!is_numeric($au_rate_int)) {
                        echo error("Please enter auto refresh interval with numbers.");
                    } elseif(!is_numeric($referral_comission)) {
                        echo error("Please enter referral comission with numbers.");
                    } elseif(!is_numeric($referral_min_withdrawal)) {
                        echo error("Please enter minimal withdrawal amount with numbers.");
                    } elseif($show_worktime == "1" && empty($worktime_start)) {
                        echo error("Please enter a work time start.");  
                    }  elseif($show_worktime == "1" && empty($worktime_end)) {
                        echo error("Please enter a work time end.");  
                    }  elseif($show_worktime == "1" && empty($worktime_gmt)) {
                        echo error("Please enter a work time gmt zone.");  
                    } elseif(!is_numeric($expire_uncompleted_time)) {
                        echo error("Please enter a minutes with numbers.");
                    } else {
                        $expire_uncompleted_time = $expire_uncompleted_time * 60;
                        $update = $db->query("UPDATE ce_settings SET expire_uncompleted_time='$expire_uncompleted_time',show_operator_status='$show_operator_status',show_worktime='$show_worktime',worktime_start='$worktime_start',worktime_end='$worktime_end',worktime_gmt='$worktime_gmt',curcnv_api='$curcnv_api',au_rate_int='$au_rate_int',referral_comission='$referral_comission',referral_min_withdrawal='$referral_min_withdrawal',title='$title',description='$description',keywords='$keywords',name='$name',url='$url',infoemail='$infoemail',supportemail='$supportemail'");
                        $settingsQuery = $db->query("SELECT * FROM ce_settings ORDER BY id DESC LIMIT 1");
                        $settings = $settingsQuery->fetch_assoc();
                        echo success("Your changes was saved successfully.");
                    }
                  }
                  ?>
                  <form action="" method="POST">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" name="title" value="<?php echo $settings['title']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="2"><?php echo $settings['description']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Keywords</label>
                        <textarea class="form-control" name="keywords" rows="2"><?php echo $settings['keywords']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Site name</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $settings['name']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Site url address</label>
                        <input type="text" class="form-control" name="url" value="<?php echo $settings['url']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Info email address</label>
                        <input type="text" class="form-control" name="infoemail" value="<?php echo $settings['infoemail']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Support email address</label>
                        <input type="text" class="form-control" name="supportemail" value="<?php echo $settings['supportemail']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Currency Converter API Key</label>
                        <input type="text" class="form-control" name="curcnv_api" value="<?php echo $settings['curcnv_api']; ?>">
                        <small>To use automatic converter from USD to EUR and etc. You must enter here your API Key provided by <a href="https://currencyconverterapi.com">currencyconverterapi.com</a>. How to get API Key: Open <a href="https://currencyconverterapi.com">https://currencyconverterapi.com</a> and click on "Pricing", choose plan "PREMIUM" and follow steps.</small>
                    </div>
                    <div class="form-group">
                        <label>Auto rate refresh interval</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="au_rate_int" value="<?php echo $settings['au_rate_int']; ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">minute(s)</span>
                            </div>
                        </div>
                        <small>Entered number will be converted to minutes automatically. For example use 5. This is to update the exchange rate when the customer is on the exchange page but has not yet placed an exchange order.</small>
                    </div>
                    <div class="form-group">
                        <label>Referral comission</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="referral_comission" value="<?php echo $settings['referral_comission']; ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Referral min. withdrawal amount</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="referral_min_withdrawal" value="<?php echo $settings['referral_min_withdrawal']; ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">USD</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Show Operator Status</label>
                        <select class="form-control" name="show_operator_status" id="show_operator_status">
                            <option value="Yes" <?php if($settings['show_operator_status'] == "1") { echo 'selected'; } ?>>Yes</option>
                            <option value="No" <?php if($settings['show_operator_status'] == "0") { echo 'selected'; } ?>>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Show Work time</label>
                        <select class="form-control" name="show_worktime" id="show_worktime">
                            <option value="Yes" <?php if($settings['show_worktime'] == "1") { echo 'selected'; } ?>>Yes</option>
                            <option value="No" <?php if($settings['show_worktime'] == "0") { echo 'selected'; } ?>>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Work time start</label>
                        <input type="text" class="form-control" name="worktime_start" value="<?php echo $settings['worktime_start']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Work time end</label>
                        <input type="text" class="form-control" name="worktime_end" value="<?php echo $settings['worktime_end']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Work time GMT Zone</label>
                        <input type="text" class="form-control" name="worktime_gmt" value="<?php echo $settings['worktime_gmt']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Expire orders after</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="expire_uncompleted_time" value="<?php echo $settings['expire_uncompleted_time']/60; ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">minutes</span>
                            </div>
                        </div>
                        <small>This will change the "Expired" status after XX minutes have expired and the order has not been paid.</small>
                    </div>
                    <button type="submit" class="btn btn-primary" name="ce_btn" value="save"><i class="fa fa-check"></i> Save changes</button>
                </form>
                </div>
              </div>
            </div>
        </div>
<?php
} elseif($b == "smtp") {
    ?>
            <div class="row">
            <div class="col-md-12 col-lg-12">
                    <h4 class="card-title"><i class="ti-email"></i> SMTP Settings</h4>
                    <br><br>
                </div>
                <div class="col-lg-12 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <br>
                      <?php
                      $CEAction = protect($_POST['ce_btn']);
                      if(isset($CEAction) && $CEAction == "save") {
                        if(isset($_POST['SMTPAuth'])) { $SMTPAuth = true; $SMTPAuth_c = 'true'; } else { $SMTPAuth = false; $SMTPAuth_c = 'false'; }
                        $smtp_host = protect($_POST['smtp_host']);
                        $smtp_port = protect($_POST['smtp_port']);
                        $smtp_user = protect($_POST['smtp_user']);
                        $smtp_pass = protect($_POST['smtp_pass']);
                        if(isset($_POST['smtp_ssl'])) { $ssl = 1; } else { $ssl = 0; }
                        
                        if($SMTPAuth == true && empty($smtp_host) or empty($smtp_port) or empty($smtp_user) or empty($smtp_pass)) { echo error("Please enter a SMTP settings."); }
                        else {
                            $contents = '<?php
    if(!defined("CryptExchanger_INSTALLED")){
        header("HTTP/1.0 404 Not Found");
        exit;
    }
    
    $smtpconf = array();
    $smtpconf["host"] = "'.$smtp_host.'"; // SMTP SERVER IP/HOST
    $smtpconf["user"] = "'.$smtp_user.'";	// SMTP AUTH USERNAME if SMTPAuth is true
    $smtpconf["pass"] = "'.$smtp_pass.'";	// SMTP AUTH PASSWORD if SMTPAuth is true
    $smtpconf["port"] = "'.$smtp_port.'";	// SMTP SERVER PORT
    $smtpconf["ssl"] = "'.$ssl.'"; // 1 -  YES, 0 - NO
    $smtpconf["SMTPAuth"] = '.$SMTPAuth_c.'; // true / false
    ?>
    ';				
                            $update = file_put_contents("../configs/smtp.settings.php",$contents);
                            if($update) {
                                $smtpconf["host"] = $smtp_host; // SMTP SERVER IP/HOST
                                $smtpconf["user"] = $smtp_user;		// SMTP AUTH USERNAME if SMTPAuth is true
                                $smtpconf["pass"] = $smtp_pass;	// SMTP AUTH PASSWORD if SMTPAuth is true
                                $smtpconf["port"] = $smtp_port;	// SMTP SERVER PORT
                                $smtpconf["ssl"] = $ssl; // 1 -  YES, 0 - NO
                                $smtpconf["SMTPAuth"] = $SMTPAuth; // true / false
                                echo success("Your changes was saved successfully.");
                            } else {
                                echo error("Please set chmod 777 of file <b>includes/smtp.settings.php</b>.");
                            }
                        }
                      }
                      ?>
                      <form action="" method="POST">
                        <div class="form-check">
                            <div class="checkbox">
                                <label for="checkbox1" class="form-check-label ">
                                    <input type="checkbox" id="checkbox1" name="SMTPAuth" <?php if($smtpconf['SMTPAuth'] == true) { echo 'checked'; } ?> value="1" class="form-check-input"> SMTP Authentication
                                </label>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <label>SMTP Host</label>
                            <input type="text" class="form-control" name="smtp_host" value="<?php echo $smtpconf['host']; ?>">
                        </div>
                        <div class="form-group">
                            <label>SMTP Port</label>
                            <input type="text" class="form-control" name="smtp_port" value="<?php echo $smtpconf['port']; ?>">
                        </div>
                        <div class="form-group">
                            <label>SMTP Username</label>
                            <input type="text" class="form-control" name="smtp_user" value="<?php echo $smtpconf['user']; ?>">
                        </div>
                        <div class="form-group">
                            <label>SMTP Password</label>
                            <input type="text" class="form-control" name="smtp_pass" value="<?php echo $smtpconf['pass']; ?>">
                        </div>
                        <div class="form-check">
                            <div class="checkbox">
                                <label for="checkbox2" class="form-check-label ">
                                    <input type="checkbox" id="checkbox2" name="smtp_ssl" <?php if($smtpconf['ssl'] == 1) { echo 'checked'; } ?> value="1" class="form-check-input"> Secure SSL/TLS Connection
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="ce_btn" value="save"><i class="fa fa-check"></i> Save changes</button>
                    </form>
                    </div>
                  </div>
                </div>
            </div>
    <?php
    }  elseif($b == "recaptcha") {
        ?>
                <div class="row">
                <div class="col-md-12 col-lg-12">
                        <h4 class="card-title"><i class="fa fa-google"></i> reCaptcha Settings</h4>
                        <br><br>
                    </div>
                    <div class="col-lg-12 grid-margin stretch-card">
                      <div class="card">
                        <div class="card-body">
                          <br>
                          <?php
                          $CEAction = protect($_POST['ce_btn']);
                          if(isset($CEAction) && $CEAction == "save") {
                            if(isset($_POST['enable_recaptcha'])) { $enable_recaptcha = 1; } else { $enable_recaptcha = '0'; }
                            $recaptcha_publickey = protect($_POST['recaptcha_publickey']);
                            $recaptcha_privatekey = protect($_POST['recaptcha_privatekey']);
                            if($enable_recaptcha == "1" && empty($recaptcha_publickey)) {
                                echo error("Please enter a reCaptcha public key.");
                            } elseif($enable_recaptcha == "1" && empty($recaptcha_privatekey)) {
                                echo error("Please enter a reCaptcha private key.");
                            } else {
                                $update = $db->query("UPDATE ce_settings SET enable_recaptcha='$enable_recaptcha',recaptcha_publickey='$recaptcha_publickey',recaptcha_privatekey='$recaptcha_privatekey'");
                                $settingsQuery = $db->query("SELECT * FROM ce_settings ORDER BY id DESC LIMIT 1");
                                $settings = $settingsQuery->fetch_assoc();
                                echo success("Your changes was saved successfully.");
                            }
                          }
                          ?>
                          <form action="" method="POST">
                            <div class="form-check">
                                <div class="checkbox">
                                    <label for="checkbox1" class="form-check-label ">
                                        <input type="checkbox" id="checkbox1" name="enable_recaptcha" <?php if($settings['enable_recaptcha'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Enable Google reCaptcha
                                    </label>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <label>reCaptcha Public Key</label>
                                <input type="text" class="form-control" name="recaptcha_publickey" value="<?php echo $settings['recaptcha_publickey']; ?>">
                            </div>
                            <div class="form-group">
                                <label>reCaptcha Private Key</label>
                                <input type="text" class="form-control" name="recaptcha_privatekey" value="<?php echo $settings['recaptcha_privatekey']; ?>">
                            </div>
                            <button type="submit" class="btn btn-primary" name="ce_btn" value="save"><i class="fa fa-check"></i> Save changes</button>
                        </form>
                        </div>
                      </div>
                    </div>
                </div>
        <?php
        } else {
    header("Location: ./");
}
?>