<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$b = protect($_GET['b']);
if($b == "update") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_reserve_requests WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./");
    }
    $row = $query->fetch_assoc();
    
    $gateway = gatewayinfo($row['gateway_id'],"name")." ".gatewayinfo($row['gateway_id'],"currency");
    $currency = gatewayinfo($row['gateway_id'],"currency");
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-check"></i> Update Reserve Request</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                <?php
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                        $update = $db->query("UPDATE ce_gateways SET reserve=reserve+$row[amount] WHERE id='$row[gateway_id]'");
                        $time = time();
                        $update = $db->query("UPDATE ce_reserve_requests SET updated_on='$time',updated_by='$_SESSION[ce_admin_uid]' WHERE id='$row[id]'");
                        CE_Send_ReserveUpdated($row['email'],$id);
                        echo success("Reserve of $gateway is updated.");
                     } else {
                        echo info("Are you sure you want to update resevre update for $row[amount] $currency to $gateway?");
                        echo '<a href="./?a=reserve_requests&b=update&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-check"></i> Yes, I confirm</a> 
                        <a href="./?a=reserve_requests" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
                     }
                     ?>
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "delete") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_reserve_requests WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-times"></i> Cancel Reserve Request</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                <?php
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                        $delete = $db->query("DELETE FROM ce_reserve_requests WHERE id='$id'");
                        echo success("Reserve request was canceled successfully.");
                     } else {
                        $gateway = gatewayinfo($row['gateway_id'],"name")." ".gatewayinfo($row['gateway_id'],"currency");
                        $currency = gatewayinfo($row['gateway_id'],"currency");
                        echo info("Are you sure you want to cancel resevre update for $row[amount] $currency to $gateway?");
                        echo '<a href="./?a=reserve_requests&b=delete&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-check"></i> Yes, I confirm</a> 
                        <a href="./?a=reserve_requests" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
                     }
                     ?>
                </div>
              </div>
            </div>
        </div>
    <?php
} else {
    header("Location: ./");
}
?>