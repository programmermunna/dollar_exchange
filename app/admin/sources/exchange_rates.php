<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
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
                        $rate_from = protect($_POST['rate_from']);
                        $rate_to = protect($_POST['rate_to']);
                        $percentage_fee = protect($_POST['percentage_fee']);
                        $fee = protect($_POST['fee']);
                        $check = $db->query("SELECT * FROM ce_rates WHERE gateway_from='$gateway_from' and gateway_to='$gateway_to'");
                        if(empty($gateway_from) or empty($gateway_to)) {
                            echo error("Please select gateways.");
                        } elseif($percentage_fee !== "allow" && empty($rate_from) or $percentage_fee !== "allow" && empty($rate_to)) {
                            echo error("Please enter exchange rate between gateways or select automatic rate.");
                        }  elseif($percentage_fee == "allow" && empty($settings['curcnv_api'])) {
                            echo error("To use automatic rates you must enter your Currency Convertor API Key in Web Settings.");
                        } elseif($percentage_fee == "allow" && empty($fee)) {
                            echo error("Please enter fee for automatic rate.");
                        } elseif($percentage_fee == "allow" && !is_numeric($fee)) {
                            echo error("Please enter fee with numbers.");
                        } elseif($check->num_rows>0) {
                            $gateway_send = gatewayinfo($gateway_from,"name")." ".gatewayinfo($gateway_from,"currency");
                            $gateway_receive = gatewayinfo($gateway_to,"name")." ".gatewayinfo($gateway_to,"currency");
                            $r = $check->fetch_assoc();
                            $link = './?a=exchange_rates&b=edit&id='.$r[id];
                            echo error("Exchange rate from $gateway_send to $gateway_receive already exists. <a href='$link'>Click here</a> to edit it.");  
                        } else {
                            if($percentage_fee == "allow") { $pfee = 1; } else { $pfee = 0; }
                            $insert = $db->query("INSERT ce_rates (gateway_from,gateway_to,rate_from,rate_to,percentage_rate,fee) VALUES ('$gateway_from','$gateway_to','$rate_from','$rate_to','$pfee','$fee')");
                            echo success("Your rate was added successfully.");
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Rate from</label>
                                    <input type="text" class="form-control" name="rate_from">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                            <div class="form-group">
                                    <label>Rate to</label>
                                    <input type="text" class="form-control" name="rate_to">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Automatic fee</label>
                            <select class="form-control" name="percentage_fee" id="percentage_fee">
                                <option value="allow">Allow</option>
                                <option value="disallow">Disallow</option>
                            </select>
                            <small>If you use percentage fee, manual rate will not work. After you allow "automatic fee" and enter fee in field below, rate will be shown below fee field.</small>
                        </div>
                        <div class="form-group">
                            <label>Percentage fee</label>
                            <input type="text" class="form-control" name="fee" onkeyup="CEA_ShowRate(this.value);" onkeydown="CEA_ShowRate(this.value);">
                        </div>
                        <span id="rate_status" style="display:none;">
                            <div class="form-group">
                                <label>Exchange rate</label>
                                <input type="text" class="form-control" disabled id="exchange_rate">
                                <small>When you use this exchange rate, it will be updated every time a customer requests an exchange so you can use the actual exchange rate + your fee.</small>
                            </div>
                        </span> 
                        <button type="submit" class="btn btn-primary" name="ce_btn" value="new"><i class="fa fa-plus"></i> Create</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "edit") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_rates WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=exchange_rates");
    }
    $row = $query->fetch_assoc();
    ?>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-pencil"></i> Edit Exchange Rate</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $CEAction = protect($_POST['ce_btn']);
                     if(isset($CEAction) && $CEAction == "save") {
                        $rate_from = protect($_POST['rate_from']);
                        $rate_to = protect($_POST['rate_to']);
                        $percentage_fee = protect($_POST['percentage_fee']);
                        $fee = protect($_POST['fee']);
                        $check = $db->query("SELECT * FROM ce_rates WHERE gateway_from='$gateway_from' and gateway_to='$gateway_to'");
                        if($percentage_fee !== "allow" && empty($rate_from) or $percentage_fee !== "allow" && empty($rate_to)) {
                            echo error("Please enter exchange rate between gateways or select automatic rate.");
                        } elseif($percentage_fee == "allow" && empty($settings['curcnv_api'])) {
                            echo error("To use automatic rates you must enter your Currency Convertor API Key in Web Settings.");
                        } elseif($percentage_fee == "allow" && empty($fee)) {
                            echo error("Please enter fee for automatic rate.");
                        } elseif($percentage_fee == "allow" && !is_numeric($fee)) {
                            echo error("Please enter fee with numbers.");
                        } else {
                            if($percentage_fee == "allow") { $pfee = 1; } else { $pfee = 0; }
                            $update = $db->query("UPDATE ce_rates SET rate_from='$rate_from',rate_to='$rate_to',percentage_rate='$pfee',fee='$fee' WHERE id='$row[id]'");
                            echo success("Your changes was saved successfully.");
                            $query = $db->query("SELECT * FROM ce_rates WHERE id='$row[id]'");
                            $row = $query->fetch_assoc();
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Rate from</label>
                                    <input type="text" class="form-control" name="rate_from" value="<?php echo $row['rate_to']; ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                            <div class="form-group">
                                    <label>Rate to</label>
                                    <input type="text" class="form-control" name="rate_to" value="<?php echo $row['rate_to']; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Automatic fee</label>
                            <select class="form-control" name="percentage_fee" id="percentage_fee">
                                <option value="allow" <?php if($row['percentage_rate'] == "1") { echo 'selected'; }?>>Allow</option>
                                <option value="disallow" <?php if($row['percentage_rate'] == "0") { echo 'selected'; }?>>Disallow</option>
                            </select>
                            <small>If you use percentage fee, manual rate will not work. After you allow "automatic fee" and enter fee in field below, rate will be shown below fee field.</small>
                        </div>
                        <div class="form-group">
                            <label>Percentage fee</label>
                            <input type="text" class="form-control" name="fee" value="<?php echo $row['fee']; ?>" onkeyup="CEA_ShowRate(this.value);" onkeydown="CEA_ShowRate(this.value);">
                        </div>
                        <span id="rate_status" style="display:none;">
                            <div class="form-group">
                                <label>Exchange rate</label>
                                <input type="text" class="form-control" disabled id="exchange_rate">
                                <small>When you use this exchange rate, it will be updated every time a customer requests an exchange so you can use the actual exchange rate + your fee.</small>
                            </div>
                        </span> 
                        <button type="submit" class="btn btn-primary" name="ce_btn" value="save"><i class="fa fa-check"></i> Save Changes</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "delete") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_rates WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=exchange_rates");
    }
    $row = $query->fetch_assoc();
    ?>
 <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-trash"></i> Delete Exchange Rate</h4>
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
                        $delete = $db->query("DELETE FROM ce_rates WHERE id='$id'");
                        echo success("Exchange rate from $gateway_send to $gateway_receive was deleted successfully.");    
                     } else {
                        echo info("Are you sure you want to delete exchange rate from $gateway_send to $gateway_receive?");
                        echo '<a href="./?a=exchange_rates&b=delete&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-trash"></i> Yes, I confirm</a> 
                        <a href="./?a=exchange_rates" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
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
                <h4 class="card-title"><i class="ti-bar-chart-alt"></i> Exchange Rates <span class="pull-right"><a href="./?a=exchange_rates&b=new" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Rate</a></span></h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                            <th>Gateway from</th>
                            <th>Gateway to</th>
                            <th>Exchange rate</th>
                            <th>Automatic rate</th>
                            <th>Percentage fee</th>
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
                            $statement = "ce_rates";
                            $query = $db->query("SELECT * FROM {$statement} ORDER BY gateway_from DESC LIMIT {$startpoint} , {$limit}");
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                                     <td><?php echo gatewayinfo($row['gateway_from'],"name")." ".gatewayinfo($row['gateway_from'],"currency"); ?></td>
                                     <td><?php echo gatewayinfo($row['gateway_to'],"name")." ".gatewayinfo($row['gateway_to'],"currency"); ?></td>
                                      <td><?php if($row['percentage_rate']=="1") { echo '-'; } else { ?><?php echo $row['rate_from']; ?> <?php echo gatewayinfo($row['gateway_from'],"currency"); ?> = <?php echo $row['rate_to']; ?> <?php echo gatewayinfo($row['gateway_to'],"currency"); ?><?php  } ?></td>
                                     <td><?php if($row['percentage_rate']=="1") { echo '<span class="badge badge-success"><i class="fa fa-check"></i> Yes</span>'; } else { echo '<span class="badge badge-daner"><i class="fa fa-times"></i> No</span>'; }?></td>
                                     <td><?php if($row['fee']>0) { echo $row['fee']."%"; } else { echo '-'; } ?></td>
                                    <td>
                                        <a href="./?a=exchange_rates&b=edit&id=<?php echo $row['id']; ?>" class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</a> 
                                        <a href="./?a=exchange_rates&b=delete&id=<?php echo $row['id']; ?>" class="badge badge-danger"><i class="fa fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="6">No exchange rates yet.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    </div>
                    <?php
                    $ver = "./?a=exchange_rates";
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