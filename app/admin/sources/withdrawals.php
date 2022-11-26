
<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$b = protect($_GET['b']);
if($b == "approve") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_users_withdrawals WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=withdrawals");
    }
    $row = $query->fetch_assoc();
    ?>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-check"></i> Approve Withdrawal</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                <?php
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                        $update = $db->query("UPDATE ce_users_withdrawals SET status='2' WHERE id='$id'");
                        echo success("Withdrawal #$id was approved successfully.");
                     } else {
                        $gateway = gatewayinfo($row['gateway'],"name")." ".gatewayinfo($row['gateway'],"currency");
                        echo info("Are you sure you want to approve withdrawal of $row[amount] $row[currency] to account $row[account] ($gateway)?");
                        echo '<a href="./?a=withdrawals&b=approve&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-check"></i> Yes, I confirm</a> 
                        <a href="./?a=withdrawals" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
                     }
                     ?>
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "cancel") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_users_withdrawals WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=withdrawals");
    }
    $row = $query->fetch_assoc();
    ?>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-times"></i> Cancel Withdrawal</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                <?php
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                        $update = $db->query("UPDATE ce_users_withdrawals SET status='3' WHERE id='$id'");
                        $update = $db->query("UPDATE ce_users_earnings SET earnings=earnings+$row[amount] WHERE uid='$row[uid]' and currency='$row[currency]'");
                        echo success("Withdrawal #$id was canceled successfully.");
                     } else {
                        $gateway = gatewayinfo($row['gateway'],"name")." ".gatewayinfo($row['gateway'],"currency");
                        echo info("Are you sure you want to cancel withdrawal of $row[amount] $row[currency] to account $row[account] ($gateway)?");
                        echo '<a href="./?a=withdrawals&b=cancel&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-check"></i> Yes, I confirm</a> 
                        <a href="./?a=withdrawals" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
                     }
                     ?>
                </div>
              </div>
            </div>
        </div>
    <?php
} else {
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-upload"></i> Withdrawals</h4>
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
                          <th>Gateway</th>
                          <th>Account</th>
                          <th>Amount</th>
                          <th>Requested on</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>  
                        </thead>
                        <tbody>
                            <?php
                            $searching = 0;
                            $CEAction = protect($_POST['ce_btn']);
                            if(isset($CEAction) && $CEAction == "search") {
                                $gateway = protect($_POST['gateway']);
                                $account = protect($_POST['account']);
                                $email = protect($_POST['email']);
                                $searcharr = array();
                                if(isValidEmail($email) && !empty($email)) {
                                    $UserQuery = $db->query("SELECT * FROM ce_users WHERE email='$email'");
                                    if($UserQuery->num_rows>0) {
                                        $user = $UserQuery->fetch_assoc();
                                        $uid = $user['id'];
                                        $searcharr[] = "uid='$uid'";
                                    }
                                }
                                if(!empty($gateway)) {
                                    $searcharr[] = "gateway='$gateway'";
                                }
                                if(!empty($account)) {
                                    $searcharr[] = "account='$account'";
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
                                $query = $db->query("SELECT * FROM ce_users_Withdrawals WHERE $filters ORDER BY status DESC, id DESC");
                            } else {
                                $statement = "ce_users_Withdrawals";
                                $query = $db->query("SELECT * FROM {$statement} ORDER BY status DESC, id DESC LIMIT {$startpoint} , {$limit}");
                            }
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                              <td><?php echo $row['id']; ?></td>
                              <td><a href="./?a=users&b=edit&id=<?php echo $row['uid']; ?>"><?php if(idinfo($row['uid'],"first_name")) { echo idinfo($row['uid'],"first_name")." ".idinfo($row['uid'],"last_name"); } else { echo idinfo($row['uid'],"email"); } ?></a></td>
                              <td><?php echo gatewayinfo($row['gateway'],"name")." ".gatewayinfo($row['gateway'],"currency"); ?></td>
                              <td><?php echo $row['account']; ?></td>
                              <td><?php echo $row['amount']." ".$row['currency']; ?></td>
                              <td><?php echo date("d/m/Y H:i",$row['requested_on']); ?></td>
                              <td>
                                    <?php
                                    if($row['status'] == "1") {
                                        $status = '<span class="badge badge-warning">Pending</span>';
                                    } elseif($row['status'] == "2") {
                                        $status = '<span class="badge badge-success">Completed</span>';
                                    } elseif($row['status'] == "3") {
                                        $status = '<span class="badge badge-danger">Canceled</span>';
                                    } else {
                                        $status = '<span class="badge badge-default">Unknown</span>';
                                    }
                                    echo $status;
                                    ?>
                              </td>
                              <td>
                                <?php if($row['status'] == "1") { ?>
                                <a href="./?a=withdrawals&b=approve&id=<?php echo $row['id']; ?>" class="badge badge-success"><i class="fa fa-check"></i> Approve</a> 
                                <a href="./?a=withdrawals&b=cancel&id=<?php echo $row['id']; ?>" class="badge badge-danger"><i class="fa fa-times"></i> Cancel</a>
                                <?php } ?>
                              </td>
                            </tr>
                                    <?php
                                }
                            } else {
                                if($searching == "1") {
                                    echo '<tr><td colspan="8">No results were found for the criteria you set.</td></tr>';
                                } else {
                                    echo '<tr><td colspan="8">No withdrawals yet.</td></tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                    <?php
                    if($searching == "0") {
                        $ver = "./?a=withdrawals";
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
                                    <label>Email address</label>
                                    <input type="text" class="form-control" name="email" value="<?php if(isset($_POST['email'])) { echo protect($_POST['email']); } ?>">
                                </div>
                                <div class="form-group">
                                    <label>Gateway</label>
                                    <select class="form-control" name="gateway">
                                        <option></option>
                                        <?php
                                        $GatewaysQuery = $db->query("SELECT * FROM ce_gateways ORDER BY id");
                                        if($GatewaysQuery->num_rows>0) {
                                            while($g = $GatewaysQuery->fetch_assoc()) {
                                                $sel = '';
                                                if(isset($_POST['gateway']) && $_POST['gateway'] == $g['id']) { $sel = 'selected'; } 
                                                echo '<option value="'.$g[id].'" '.$sel.'>'.$g[name].' '.$g[currency].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Account</label>
                                    <input type="text" class="form-control" name="account" value="<?php if(isset($_POST['account'])) { echo protect($_POST['account']); } ?>">
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