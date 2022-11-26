<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if($op['can_manage_rules'] !== "1") {
    header("Location: ./");
}

$b = protect($_GET['b']);
if($b == "new") {
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-plus"></i> New Exchange Rate</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $CEAction = protect($_POST['ce_btn']);
                     if(isset($CEAction) && $CEAction == "new") {
                        $gateway_from = protect($_POST['gateway_from']);
                        $gateway_to = protect($_POST['gateway_to']);
                        $exchange_rules = addslashes($_POST['exchange_rules']);
                        $check = $db->query("SELECT * FROM ce_gateways_rules WHERE gateway_from='$gateway_from' and gateway_to='$gateway_to'");
                        if(empty($gateway_from) or empty($gateway_to)) {
                            echo error("Please select gateways.");
                        } elseif(empty($exchange_rules)) {
                            echo error("Please enter exchange rules.");
                        }  elseif($check->num_rows>0) {
                            $gateway_send = gatewayinfo($gateway_from,"name")." ".gatewayinfo($gateway_from,"currency");
                            $gateway_receive = gatewayinfo($gateway_to,"name")." ".gatewayinfo($gateway_to,"currency");
                            $r = $check->fetch_assoc();
                            $link = './?a=exchange_rules&b=edit&id='.$r[id];
                            echo error("Exchange rules from $gateway_send to $gateway_receive already exists. <a href='$link'>Click here</a> to edit it.");  
                        } else {
                            $insert = $db->query("INSERT ce_gateways_rules (gateway_from,gateway_to,exchange_rules) VALUES ('$gateway_from','$gateway_to','$exchange_rules')");
                            $query = $db->query("SELECT * FROM ce_gateways_rules WHERE gateway_from='$gateway_from' and gateway_to='$gateway_to'");
                            $row = $query->fetch_assoc();
                            // update operator activity start
							$activity_time = time();
							$activity_ip = $_SERVER['REMOTE_ADDR'];
							$update = $db->query("INSERT ce_operators_activity (oid,activity_type,activity_id,activity_value,ip,created) VALUES ('$_SESSION[ce_operator_uid]','new_rule','$row[id]','','$activity_ip','$activity_time')");
							// update operator activity end
                            echo success("Your exchange rule was added successfully.");
                        }
                     }
                     ?>

                     <form action="" method="POST">
                     <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Gateway from</label>
                                    <select class="form-control" name="gateway_from" id="gateway_from">
                                        <option></option>
                                        <?php
                                        $GetGateways = $db->query("SELECT * FROM ce_gateways WHERE allow_send='1' ORDER BY id");
                                        if($GetGateways->num_rows>0) {
                                            while($get = $GetGateways->fetch_assoc()) {
                                                echo '<option value="'.$get[id].'">'.$get[name].' '.$get[currency].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                            <div class="form-group">
                                    <label>Gateway to</label>
                                    <select class="form-control" name="gateway_to" id="gateway_to">
                                        <option></option>
                                        <?php
                                        $GetGateways = $db->query("SELECT * FROM ce_gateways ORDER BY id");
                                        if($GetGateways->num_rows>0) {
                                            while($get = $GetGateways->fetch_assoc()) {
                                                echo '<option value="'.$get[id].'">'.$get[name].' '.$get[currency].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Exchange Rules</label>
                            <textarea id="tinyMceExample" name="exchange_rules">
                                
                            </textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" name="ce_btn" value="new"><i class="fa fa-plus"></i> Create</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "edit") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_gateways_rules WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=exchange_rules");
    }
    $row = $query->fetch_assoc();
    ?>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-pencil"></i> Edit Exchange Rule</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $CEAction = protect($_POST['ce_btn']);
                     if(isset($CEAction) && $CEAction == "save") {
                        $gateway_from = protect($_POST['gateway_from']);
                        $gateway_to = protect($_POST['gateway_to']);
                        $exchange_rules = addslashes($_POST['exchange_rules']);
                        $check = $db->query("SELECT * FROM ce_gateways_rules WHERE gateway_from='$gateway_from' and gateway_to='$gateway_to'");
                        if(empty($exchange_rules)) {
                            echo error("Please enter exchange rules.");
                        } else {
                            $update = $db->query("UPDATE ce_gateways_rules SET exchange_rules='$exchange_rules' WHERE id='$row[id]'");
                            echo success("Your changes was saved successfully.");
                            $query = $db->query("SELECT * FROM ce_gateways_rules WHERE id='$row[id]'");
                            $row = $query->fetch_assoc();
                            // update operator activity start
							$activity_time = time();
							$activity_ip = $_SERVER['REMOTE_ADDR'];
							$update = $db->query("INSERT ce_operators_activity (oid,activity_type,activity_id,activity_value,ip,created) VALUES ('$_SESSION[ce_operator_uid]','edit_rule','$row[id]','','$activity_ip','$activity_time')");
							// update operator activity end
                        }
                     }
                     ?>

                     <form action="" method="POST">
                     <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Gateway from</label>
                                    <input type="text" class="form-control" disabled  value="<?php echo gatewayinfo($row['gateway_from'],"name")." ".gatewayinfo($row['gateway_from'],"currency"); ?>">
                                    <input type="hidden" id="gateway_from" value="<?php echo $row['gateway_from']; ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                            <div class="form-group">
                                    <label>Gateway to</label>
                                    <input type="text" class="form-control" disabled  value="<?php echo gatewayinfo($row['gateway_to'],"name")." ".gatewayinfo($row['gateway_to'],"currency"); ?>">
                                    <input type="hidden" id="gateway_to" value="<?php echo $row['gateway_to']; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Exchange Rules</label>
                            <textarea id="tinyMceExample" name="exchange_rules">
                                <?php echo $row['exchange_rules']; ?>
                            </textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" name="ce_btn" value="save"><i class="fa fa-check"></i> Save Changes</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "delete") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_gateways_rules WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=exchange_rules");
    }
    $row = $query->fetch_assoc();
    ?>
 <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-trash"></i> Delete Exchange Rule</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $gateway_from = $row['gateway_from'];
                     $gateway_to = $row['gateway_to'];
                     $gateway_send = gatewayinfo($gateway_from,"name")." ".gatewayinfo($gateway_from,"currency");
                     $gateway_receive = gatewayinfo($gateway_to,"name")." ".gatewayinfo($gateway_to,"currency");
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                         // update operator activity start
							$activity_time = time();
							$activity_ip = $_SERVER['REMOTE_ADDR'];
							$update = $db->query("INSERT ce_operators_activity (oid,activity_type,activity_id,activity_value,ip,created) VALUES ('$_SESSION[ce_operator_uid]','delete_rate','$row[id]','','$activity_ip','$activity_time')");
							// update operator activity end
                        $delete = $db->query("DELETE FROM ce_gateways_rules WHERE id='$id'");
                        echo success("Exchange rules from $gateway_send to $gateway_receive was deleted successfully.");    
                     } else {
                        echo info("Are you sure you want to delete exchange rules from $gateway_send to $gateway_receive?");
                        echo '<a href="./?a=exchange_rules&b=delete&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-trash"></i> Yes, I confirm</a> 
                        <a href="./?a=exchange_rules" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
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
                <h4 class="card-title"><i class=" fa fa-flag"></i> Exchange Rules <span class="pull-right"><a href="./?a=exchange_rules&b=new" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Rule</a></span></h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                    <?php echo info("If you have any special terms and conditions for exchanges between some exchange directions can add them here. They will be displayed on the page for exchanging directions, so the visitor/user agrees to them automatically after making an exchange request."); ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                            <th>Gateway from</th>
                            <th>Gateway to</th>
                            <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
                            $limit = 20;
                            $startpoint = ($page * $limit) - $limit;
                            if($page == 1) {
                                $i = 1;
                            } else {
                                $i = $page * $limit;
                            }
                            $statement = "ce_gateways_rules";
                            $query = $db->query("SELECT * FROM {$statement} ORDER BY gateway_from DESC LIMIT {$startpoint} , {$limit}");
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                                     <td><?php echo gatewayinfo($row['gateway_from'],"name")." ".gatewayinfo($row['gateway_from'],"currency"); ?></td>
                                     <td><?php echo gatewayinfo($row['gateway_to'],"name")." ".gatewayinfo($row['gateway_to'],"currency"); ?></td>
                                      <td>
                                        <a href="./?a=exchange_rules&b=edit&id=<?php echo $row['id']; ?>" class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</a> 
                                        <a href="./?a=exchange_rules&b=delete&id=<?php echo $row['id']; ?>" class="badge badge-danger"><i class="fa fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="3">No exchange rules yet.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    </div>
                    <?php
                    $ver = "./?a=exchange_rules";
                    if(admin_pagination($statement,$ver,$limit,$page)) {
                        echo '<br>';
                        echo admin_pagination($statement,$ver,$limit,$page);
                    }
                    ?>
                </div>
              </div>
            </div>
        </div>
    <?php
}
?>