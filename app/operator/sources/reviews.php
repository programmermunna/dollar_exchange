
<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if($op['can_manage_reviews'] !== "1") {
    header("Location: ./");
}

$b = protect($_GET['b']);
if($b == "approve") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_users_reviews WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=reviews");
    }
    $row = $query->fetch_assoc();
    ?>
<div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-check"></i> Approve Review</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                <?php
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                        $update = $db->query("UPDATE ce_users_reviews SET status='1' WHERE id='$id'");
                        // update operator activity start
                        $activity_time = time();
                        $activity_ip = $_SERVER['REMOTE_ADDR'];
                        $update = $db->query("INSERT ce_operators_activity (oid,activity_type,activity_id,activity_value,ip,created) VALUES ('$_SESSION[ce_operator_uid]','approve_review','$row[id]','','$activity_ip','$activity_time')");
                        // update operator activity end
                        echo success("Review ($row[comment]) by $row[display_name] was approved successfully.");
                     } else {
                        echo info("Are you sure you want to approve review ($row[comment]) by $row[display_name]?");
                        echo '<a href="./?a=reviews&b=approve&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-check"></i> Yes, I confirm</a> 
                        <a href="./?a=reviews" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
                     }
                     ?>
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "cancel") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_users_reviews WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=reviews");
    }
    $row = $query->fetch_assoc();
    ?>
<div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-times"></i> Cancel Review</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                <?php
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                        $update = $db->query("UPDATE ce_users_reviews SET status='0' WHERE id='$id'");
                        // update operator activity start
                        $activity_time = time();
                        $activity_ip = $_SERVER['REMOTE_ADDR'];
                        $update = $db->query("INSERT ce_operators_activity (oid,activity_type,activity_id,activity_value,ip,created) VALUES ('$_SESSION[ce_operator_uid]','cancel_review','$row[id]','','$activity_ip','$activity_time')");
                        // update operator activity end
                        echo success("Review ($row[comment]) by $row[display_name] was canceled successfully.");
                     } else {
                        echo info("Are you sure you want to cancel review ($row[comment]) by $row[display_name]?");
                        echo '<a href="./?a=reviews&b=cancel&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-check"></i> Yes, I confirm</a> 
                        <a href="./?a=reviews" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
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
                <h4 class="card-title"><i class="ti-comment-alt"></i> Reviews</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                          <th>Order</th>
                          <th>Display name</th>
                          <th>Comment</th>
                          <th>Type</th>
                          <th>Date</th>
                          <th>Status</th>
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
                            $statement = "ce_users_reviews";
                            $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                              <td><a href="./?a=exchange_orders&b=explore&id=<?php echo $row['order_id']; ?>"><?php echo $row['order_id']; ?></a></td>
                              <td><?php if($row['uid']>0) { echo '<a href="./?a=users&b=edit&id='.$row[uid].'">'.$row[display_name].'</a>'; } else { echo $row['display_name']; } ?></td>
                              <td><?php echo $row['comment']; ?></td>
                              <td>
                                <?php
                                if($row['type'] == "1") {
                                  $type = '<span class="text text-success"><i class="fa fa-smile"></i> Positive</span>';
                                } elseif($row['type'] == "2") {
                                  $type = '<span class="text text-danger"><i class="fa fa-frown-o"></i> Negative</span>';
                                } elseif($row['type'] == "3") {
                                  $type = '<span class="text text-warning"><i class="fa fa-meh-o"></i> Neutral</span>';
                                } else { }
                                echo $type;
                                ?>
                              </td>
                              <td><?php echo date("d/m/Y H:i",$row['posted']); ?></td>
                              <td><?php if($row['status'] == "1") { echo '<span class="badge badge-success">Approved</span>'; } elseif($row['status'] == "2") { echo '<span class="badge badge-primary">Under Review</span>'; } else { echo '<span class="badge badge-danger">Canceled</span>'; } ?></td>
                              <td>
                                <?php if($row['status'] == "2") { ?>
                                <a href="./?a=reviews&b=approve&id=<?php echo $row['id']; ?>" class="badge badge-success"><i class="fa fa-check"></i> Approve</a> 
                                <a href="./?a=reviews&b=cancel&id=<?php echo $row['id']; ?>" class="badge badge-danger"><i class="fa fa-times"></i> Cancel</a>
                                <?php } else { echo ''; } ?>
                              </td>
                            </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="7">No reviews yet.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    </div>
                    <?php
                    $ver = "./?a=reviews";
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