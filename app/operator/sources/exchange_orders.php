
<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if($op['can_manage_orders'] !== "1") {
    header("Location: ./");
}

$b = protect($_GET['b']);
if($b == "explore") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_orders WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=exchange_orders");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-refresh"></i> Exchange Orders <small>Order ID: <?php echo $row['id']; ?></small></h4>
                <br><br>
                <?php
                $update = protect($_GET['update']);
                if(isset($update) && $update == "rate") {
                    $rate = get_rates($row['gateway_send'],$row['gateway_receive']);
                    $amount_send = $row['amount_send'];
                    $amount_receive = ce_ExCalculator($row['gateway_send'],$row['gateway_receive'],$amount_send);
                    $rate_from = $rate['rate_from'];
                    $rate_to = $rate['rate_to'];
                    $update = $db->query("UPDATE ce_orders SET amount_send='$amount_send',amount_receive='$amount_receive',rate_From='$rate_from',rate_to='$rate_to' WHERE id='$row[id]'");
                    echo success("Exchange rate is updating! Please wait...");
                    echo '<meta http-equiv="refresh" content="3; url=./?a=exchange_orders&b=explore&id='.$row[id].'">';
                }
                $CEAction = protect($_POST['ce_btn']);
                if(isset($CEAction) && $CEAction == "update") {
                    $status = protect($_POST['status']);
                    if($status == "4") { 
                        $txrec = protect($_POST['txrec']);
                        $time =time();
                        $update = $db->query("UPDATE ce_orders SET status='$status',updated='$time',transaction_receive='$txrec',processed_by='$_SESSION[ce_admin_uid]' WHERE id='$row[id]'");
                        CE_Send_OrderCompleted($row['u_field_1'],$row['id']);
                        echo success("Order status was updated. Client was notified by email.");
                        if($row['uid']>0) {
                            if(gatewayinfo($row['gateway_send'],"is_crypto")=="1") {
                                $price = getCryptoPrice($row['currency_from']);
                                $newamount = $row['amount_send'] * $price;
                                $amount = number_format($newamount,2);
                            } else {
                                if($row['currency_from'] == "USD") {
                                    $amount = $row['amount_send'];
                                } else {
                                    $calc = currencyConvertor("1",$row['currency_from'],"USD");
                                    $newamount = $row['amount_send'] * $calc;
                                    $amount = number_format($newamount,2);
                                }
                            }
                            $update = $db->query("UPDATE ce_users SET exchanged_volume=exchanged_volume+$amount WHERE id='$row[uid]'");
                            $uexvolume = idinfo($row['uid'],"exchanged_volume");
                            $checkdl = $db->query("SELECT * FROM ce_discount_system WHERE from_value<$uexvolume and to_value>$uexvolume ORDER BY id");
                            if($checkdl->num_rows>0) {
                                $d = $checkdl->fetch_assoc();
                                if(idinfo($row['uid'],"discount_level") !== $d['discount_level']) {
                                    $update = $db->query("UPDATE ce_users SET discount_level='$d[discount_level]' WHERE id='$row[uid]'");
                                }
                            
                            }
                        }
                        $query = $db->query("SELECT * FROM ce_orders WHERE id='$row[id]'");
                        $row = $query->fetch_assoc();    
                        
							// update operator activity start
							$activity_time = time();
							$activity_ip = $_SERVER['REMOTE_ADDR'];
							$update = $db->query("INSERT ce_operators_activity (oid,activity_type,activity_id,activity_value,ip,created) VALUES ('$_SESSION[ce_operator_uid]','complete_order','$row[id]','','$activity_ip','$activity_time')");
							// update operator activity end
                    } else {
                        CE_Send_OrderUpdated($row['u_field_1'],$row['id']);
                        $time =time();
                        $update = $db->query("UPDATE ce_orders SET status='$status',updated='$time',processed_by='$_SESSION[ce_admin_uid]' WHERE id='$row[id]'");
                        echo success("Order status was updated. Client was notified by email.");
                        $query = $db->query("SELECT * FROM ce_orders WHERE id='$row[id]'");
                        $row = $query->fetch_assoc();
                        // update operator activity start
							$activity_time = time();
							$activity_ip = $_SERVER['REMOTE_ADDR'];
							$update = $db->query("INSERT ce_operators_activity (oid,activity_type,activity_id,activity_value,ip,created) VALUES ('$_SESSION[ce_operator_uid]','update_order','$row[id]','','$activity_ip','$activity_time')");
							// update operator activity end
                    }
                }

                if(isset($CEAction) && $CEAction == "give_profit") {
                    $profit = protect($_POST['profit']);
                    $time = time();
                    if(empty($profit)) { echo error("Please enter user profit."); }
                    elseif(!is_numeric($profit)) { echo error("Please enter profit with numbers."); }
                    else {
                        $cur = 'USD';
					    $refid = $row['refereer'];
					    $refemail = idinfo($refid,"email");
                        $refuser = idinfo($refid,"username");
                        if(empty($refuser)) {
                            $refuser = $refemail;
                        }
                        $CheckWallet = $db->query("SELECT * FROM ce_users_earnings WHERE uid='$refid' and currency='$cur'");
                        if($CheckWallet->num_rows>0) {
                            $update = $db->query("UPDATE ce_users_earnings SET amount=amount+$profit,updated='$time' WHERE uid='$refid' and currency='$cur'");
                        } else {
                            $insert = $db->query("INSERT ce_users_earnings (uid,amount,currency,updated) VALUES ('$refid','$profit','$cur','$time')");
                        }
						$update = $db->query("UPDATE ce_orders SET refereer_comission='$profit',refereer_comission_currency='$cur',refereer_set='1' WHERE id='$row[id]'");
						$query = $db->query("SELECT * FROM ce_orders WHERE id='$id'");
						$row = $query->fetch_assoc();
						CE_Send_NewProfit($refemail,$profit,$cur);
                        echo success("Profit <b>$profit $cur</b> was gived to $refuser.");
                    }
                }
                ?>
            </div>
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                    <h4 class="card-title">ORDER OVERVIEW</h4>

                    <div class="row">
                        <div class="col-md-5">
                            <center><img src="<?php echo gticon($row['gateway_send']); ?>" width="64px" height="64px"><br/><br/><h3><?php echo gatewayinfo($row['gateway_send'],"name"); ?> <?php echo gatewayinfo($row['gateway_send'],"currency"); ?></h3><h5><?php echo $row['amount_send']." ".gatewayinfo($row['gateway_send'],"currency"); ?></h5></center>
                        </div>
                        <div class="col-md-2"><br/><br/><center><i class="fa fa-arrow-right fa-3x"></i></center></div>
                        <div class="col-md-5">
                        <center><img src="<?php echo gticon($row['gateway_receive']); ?>" width="64px" height="64px"><br/><br/><h3><?php echo gatewayinfo($row['gateway_receive'],"name"); ?> <?php echo gatewayinfo($row['gateway_receive'],"currency"); ?></h3><h5><?php echo $row['amount_receive']." ".gatewayinfo($row['gateway_receive'],"currency"); ?></h5></center>
                        </div>
                    </div>
                    
                    <br>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                    <td><b>Exchange rate:</b></td>
                                    <td><span class="pull-right"><?php echo $row['rate_from']." ".gatewayinfo($row['gateway_send'],"currency")." = ".$row['rate_to']." ".gatewayinfo($row['gateway_receive'],"currency"); ?> <a href="./?a=exchange_orders&b=explore&id=<?php echo $row['id']; ?>&update=rate" data-toggle="tooltip" data-placement="bottom" title="If you click here, the exchange rate will be updated with the current and amount that the user will receive will be recalculated."><i class="fa fa-refresh"></i></a></span></td>
                                </tr>
                                <tr>
                                    <td><b>Order hash:</b></td>
                                    <td><span class="pull-right"><?php echo $row['order_hash']; ?></span></td>
                                </tr>
                                <tr>
                                    <td><b><?php echo gatewayinfo($row['gateway_send'],"name"); ?> Transaction ID:</b></td>
                                    <td><span class="pull-right"><?php if($row['transaction_send']) { echo $row['transaction_send']; } else { echo 'n/a'; } ?></span></td>
                                </tr>
                                <tr>
                                    <td><b><?php echo gatewayinfo($row['gateway_receive'],"name"); ?> Transaction ID:</b></td>
                                    <td><span class="pull-right"><?php if($row['transaction_receive']) { echo $row['transaction_receive']; } else { echo 'n/a'; }  ?></span></td>
                                </tr>
                                <tr>
                                    <td><b>Status:</b></td>
                                    <td><span class="pull-right"><?php $status = ce_decodeStatus($row['status']); ?><span class="badge badge-<?php echo $status['style']; ?>"><?php echo $status['text']; ?></span> </span></td>
                                </tr>
                                <tr>
                                    <td><b>Order created on:</b></td>
                                    <td><span class="pull-right"><?php echo date("d/m/Y h:ma",$row['created']); ?></span></td>
                                </tr>
                                <tr>
                                    <td><b>Order updated on:</b></td>
                                    <td><span class="pull-right"><?php if($row['updated']>0) { echo date("d/m/Y h:ma",$row['updated']); } ?></span></td>
                                </tr>
                                <tr>
                                    <td><b>Order expired on:</b></td>
                                    <td><span class="pull-right"><?php if($row['expired']>0) { echo date("d/m/Y h:ma",$row['expired']); } ?></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                    <h4 class="card-title">TAKE ACTION</h4>

                    <form action="" method="POST">
                        <div class="form-group">
                            <label>Choose status:</label>
                            <select class="form-control" name="status" onchange="CEA_ShowField(this.value);">
                            <option value="1" <?php if($row['status'] == "1") { echo 'selected'; } ?>>Pending</option>
                            <option value="2" <?php if($row['status'] == "2") { echo 'selected'; } ?>>Awaiting Confirmation(s)</option>
                            <option value="3" <?php if($row['status'] == "3") { echo 'selected'; } ?>>Processing</option>
                            <option value="4" <?php if($row['status'] == "4") { echo 'selected'; } ?>>Completed</option>
                            <option value="5" <?php if($row['status'] == "5") { echo 'selected'; } ?>>Canceled</option>
                            <option value="6" <?php if($row['status'] == "6") { echo 'selected'; } ?>>Expired</option>
                            </select>
                        </div>
                        <div class="form-group" id="txfield" style="display:none;">
                            <label><?php echo gatewayinfo($row['gateway_receive'],"name"); ?> Transaction ID:</label>
                            <input type="text" class="form-control" name="txrec">
                        </div>
                        <button type="submit" class="btn btn-primary" name="ce_btn" value="update">Update</button>
                    </form>
                </div>
              </div>
            </div>

            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                    <h4 class="card-title">USER DETAILS</h4>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <?php
                                if($row['uid']>0) { 
                                    ?>
                                    <tr>
                                        <td><b>Username:</b></td>
                                        <td><span class="pull-right"><a href="./?a=users&b=edit&id=<?php echo $row['id']; ?>"><?php if(idinfo($row['uid'],"first_name")) { echo idinfo($row['uid'],"first_name")." ".idinfo($row['uid'],"last_name"); } else { echo idinfo($row['uid'],"email"); } ?></a></span></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td><b>IP Address:</b></td>
                                    <td><span class="pull-right"><?php echo $row['ip']; ?></span></td>
                                </tr>
                                <tr>
                                    <td><b>Email address:</b></td>
                                    <td><span class="pull-right"><?php echo $row['u_field_1']; ?></span></td>
                                </tr>
                                <?php
                                if(gatewayinfo($row['gateway_receive'],"external_gateway") == "1" or gatewayinfo($row['gateway_receive'],"manual_payment") == "1") {
                                    $fieldsquery = $db->query("SELECT * FROM ce_gateways_fields WHERE gateway_id='$row[gateway_receive]' ORDER BY id");
                                    if($fieldsquery->num_rows>0) {
                                        $account_data = '';
                                        while($field = $fieldsquery->fetch_assoc()) {
                                            $field_number = $field['field_number']+1;
                                            $fild = 'u_field_'.$field_number;
                                            $ret = $row[$fild];
                                            $account_data .= '<tr>
                                                    <td><b>'.$field[field_name].':</b></td>
                                                    <td><span class="pull-right">'.$ret.'</span></td>
                                            </tr>';
                                        }
                                        echo $account_data;
                                    }
                                } else {
                                    if(gatewayinfo($row['gateway_receive'],"is_crypto") == "1") {
                                        ?>
                                        <tr>
                                            <td><b><?php echo gatewayinfo($row['gateway_receive'],"name"); ?> address:</b></td>
                                            <td><span class="pull-right"><?php echo $row['u_field_2']; ?></span></td>
                                        </tr>    
                                        <?php
                                    } else {
                                        ?>
                                        <tr>
                                            <td><b><?php echo gatewayinfo($row['gateway_receive'],"name"); ?> account:</b></td>
                                            <td><span class="pull-right"><?php echo $row['u_field_2']; ?></span></td>
                                        </tr>    
                                        <?php   
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
              </div>
            </div>

            <?php
			if($row['refereer']>0 && $row['refereer_set'] == "0") {
				?>
<div class="col-lg-4 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                    <h4 class="card-title">GIVE REFERRAL PROFIT</h4>

                    <small>This exchange is due to the user <a href="./?a=users&b=edit&id=<?php echo $row['referral_id']; ?>"><?php echo idinfo($row['referral_id'],"username"); ?></a> according partner program, he/she must get its reward from system.</small>
						<form action="" method="POST">
							<?php if(gatewayinfo($row['gateway_send'],"currency") == gatewayinfo($row['gateway_receive'],"currency")) { ?>
								<?php
								$com = $row['amount_send'] - $row['amount_receive'];
								$percentage = 100 + $settings['referral_comission'];
								$com2 = ($com * 100) / $percentage; 
								$com = $com-$com2; 
								$comission = number_format($com,2);
								?>
								<div class="form-group">
									<label>Profit</label>
									<div class="input-group">
									  <input type="text" class="form-control" placeholder="Amount" name="profit" value="<?php echo $comission; ?>" aria-describedby="basic-addon1">
									  <div class="input-group-append">
                                <span class="input-group-text">USD</span>
                            </div>
									</div>
								</div>
							<?php } else { ?>
								<?php echo info("As the currencies of exchange are different, we can not automatically calculate the profit of the user. Please enter it manually."); ?>
								<div class="form-group">
									<label>Profit</label>
									<div class="input-group">
									  <input type="text" class="form-control" placeholder="Amount" name="profit" aria-describedby="basic-addon1">
									  <div class="input-group-append">
                                        <span class="input-group-text">USD</span>
                                    </div>
									</div>
								</div>
							<?php } ?>
							<button type="submit" class="btn btn-primary" name="ce_btn" value="give_profit"><i class="fa fa-check"></i> Give profit</button>
						</form>
                </div>
              </div>
            </div>
            <?php
            }
            ?>

            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                    <h4 class="card-title">ORDER ATTACHMENTS</h4>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Filename</th>
                                    <th>Size</th>
                                    <th>Uploaded on</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $GetFiles = $db->query("SELECT * FROM ce_orders_attachments WHERE oid='$row[id]' ORDER BY id");
                                if($GetFiles->num_rows>0) {
                                    while($file = $GetFiles->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><a href="<?php echo $settings['url'].$file['filepath']; ?>" target="_blank"><?php echo $file['filename']; ?></a></td>
                                            <td><?php echo formatBytes($file['filesize']); ?></td>
                                            <td><?php echo date("d/m/Y H:ma",$file['uploaded']); ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="3">No have uploaded files yet.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
              </div>
            </div>

        </div>
    <?php
} else {
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-refresh"></i> Exchange Orders</h4>
                <br><br>
            </div>
            
            <div class="col-lg-9 col-md-9 grid-margin stretch-card">

                <div class="card">
                <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Exchange from</th>
                            <th>Exchange to</th>
                            <th>Status</th>
                            <th>Created on</th>
                            <th>Processed by</th>
                            <th>Action</th>
                            </tr>  
                        </thead>
                        <tbody>
                            <?php
                            $searching = 0;
                            $CEAction = protect($_POST['ce_btn']);
                            if(isset($CEAction) && $CEAction == "search") {
                                $gateway_from = protect($_POST['gateway_from']);
                                $gateway_to = protect($_POST['gateway_to']);
                                $email = protect($_POST['email']);
                                $id = protect($_POST['order_id']);
                                $searcharr = array();
                                if(isValidEmail($email) && !empty($email)) {
                                    $UserQuery = $db->query("SELECT * FROM ce_users WHERE email='$email'");
                                    if($UserQuery->num_rows>0) {
                                        $user = $UserQuery->fetch_assoc();
                                        $uid = $user['id'];
                                        $searcharr[] = "uid='$uid'";
                                    }
                                }
                                if(!empty($gateway_from)) {
                                    $searcharr[] = "gateway_send='$gateway_from'";
                                }
                                if(!empty($gateway_to)) {
                                    $searcharr[] = "gateway_receive='$gateway_to'";
                                }
                                if(!empty($id) && is_numeric($id)) {
                                    $searcharr[] = "id='$id'";
                                }
                                $searching = 1;
                            }
                            $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
                            $limit = 20;
                            $startpoint = ($page * $limit) - $limit;
                            if($page == 1) {
                                $i = 1;
                            } else {
                                $i = $page * $limit;
                            }
                            if($searching == "1") {
                                $filters = implode(" and ",$searcharr);
                                $query = $db->query("SELECT * FROM ce_orders WHERE $filters ORDER BY id DESC");
                            } else {
                                $statement = "ce_orders";
                                $query = $db->query("SELECT * FROM {$statement} ORDER BY  id DESC LIMIT {$startpoint} , {$limit}");
                            }
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><a href="./?a=users&b=edit&id=<?php echo $row['uid']; ?>"><?php if(idinfo($row['uid'],"first_name")) { echo idinfo($row['uid'],"first_name")." ".idinfo($row['uid'],"last_name"); } else { echo idinfo($row['uid'],"email"); } ?></a></td>
                                        <td><img src="<?php echo gticon($row['gateway_send']); ?>" width="24px" height="24px"> <?php echo gatewayinfo($row['gateway_send'],"name"); ?> (<?php echo $row['amount_send']." ".gatewayinfo($row['gateway_send'],"currency"); ?>)</td>
                                        <td><img src="<?php echo gticon($row['gateway_receive']); ?>" width="24px" height="24px">  <?php echo gatewayinfo($row['gateway_receive'],"name"); ?> (<?php echo $row['amount_receive']." ".gatewayinfo($row['gateway_receive'],"currency"); ?>)</td>
                                        <td><?php $status = ce_decodeStatus($row['status']); ?><span class="badge badge-<?php echo $status['style']; ?>"><?php echo $status['text']; ?></span></td>
                                        <td><?php echo date("d/m/Y H:ma",$row['created']); ?></td>
                                        <td><?php if($row['processed_by']>0) { ?><a href="./?a=users&b=edit&id=<?php echo $row['processed_by']; ?>"><?php echo idinfo($row['processed_by'],"username"); ?></a><?php } ?></td>
                                        <td>
                                            <a href="./?a=exchange_orders&b=explore&id=<?php echo $row['id']; ?>" class="badge badge-primary"><i class="fa fa-search"></i> Explore</a>
                                        </td>
                                        </tr>
                                    <?php
                                }
                            } else {
                                if($searching == "1") {
                                    echo '<tr><td colspan="8">No results were found for the criteria you set.</td></tr>';
                                } else {
                                    echo '<tr><td colspan="8">No exchange orders yet.</td></tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                    <?php
                    if($searching == "0") {
                        $ver = "./?a=exchange_orders";
                        if(admin_pagination($statement,$ver,$limit,$page)) {
                            echo '<br>';
                            echo admin_pagination($statement,$ver,$limit,$page);
                        }
                    }
                    ?>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Search</h4>
                        <form action="" method="POST">
                                <div class="form-group">
                                    <label>Gateway from</label>
                                    <select class="form-control" name="gateway_form">
                                        <option></option>
                                        <?php
                                        $GatewaysQuery = $db->query("SELECT * FROM ce_gateways WHERE allow_send='1'");
                                        if($GatewaysQuery->num_rows>0) {
                                            while($g = $GatewaysQuery->fetch_assoc()) {
                                                if(isset($_POST['gateway_from']) && $_POST['gateway_from'] == $g['id']) { $sel = 'selected'; } else { $sel = ''; }
                                                echo '<option value="'.$g[id].'" '.$sel.'>'.$g[name].' '.$g[currency].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Gateway to</label>
                                    <select class="form-control" name="gateway_to">
                                        <option></option>
                                        <?php
                                        $GatewaysQuery = $db->query("SELECT * FROM ce_gateways");
                                        if($GatewaysQuery->num_rows>0) {
                                            while($g = $GatewaysQuery->fetch_assoc()) {
                                                if(isset($_POST['gateway_to']) && $_POST['gateway_to'] == $g['id']) { $sel = 'selected'; } else { $sel = ''; }
                                                echo '<option value="'.$g[id].'" '.$sel.'>'.$g[name].' '.$g[currency].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Order ID</label>
                                    <input type="text" class="form-control" name="order_id" value="<?php if(isset($_POST['order_id'])) { echo protect($_POST['order_id']); } ?>">
                                </div>
                                <div class="form-group">
                                    <label>Email address</label>
                                    <input type="text" class="form-control" name="email" value="<?php if(isset($_POST['email'])) { echo protect($_POST['email']); } ?>">
                                </div>
                                <button type="submit" class="btn btn-primary btn-block" name="ce_btn" value="search"><i class="fa fa-search"></i> Search</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
}
?>