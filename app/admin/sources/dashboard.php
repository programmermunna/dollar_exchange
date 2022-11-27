
<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$operator_status = protect($_GET['operator_status']);
if(isset($operator_status) && $operator_status == "online") {
  $update = $db->query("UPDATE ce_settings SET operator_status='1'");
  header("Location: ./");
}
if(isset($operator_status) && $operator_status == "offline") {
  $update = $db->query("UPDATE ce_settings SET operator_status='0'");
  header("Location: ./");
}
?>
           <div class="row">
              <div class="col-md-12">
                    <span class="pull-right">
Operator status: <?php if($settings['operator_status'] == "1") { ?><span class="badge badge-success">Online</span> <a href="./?operator_status=offline" class="badge badge-danger">Become Offline</a> <?php } else { ?><span class="badge badge-danger">Offline</span> <a href="./?operator_status=online" class="badge badge-success">Become Online</a><?php } ?>
                    </span>
              </div>
              <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-5 mb-4 mb-xl-0">
                  <h3 class="font-weight-normal mb-0">Orders Overview </h3>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">Completed</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?php $QueryStats = $db->query("SELECT * FROM ce_orders WHERE status='4'"); echo (int) $QueryStats->num_rows; ?></h3>
                    <i class="fa fa-check fa-3x icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div> 
                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">Pending</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?php $QueryStats = $db->query("SELECT * FROM ce_orders WHERE status < 4"); echo (int) $QueryStats->num_rows; ?></h3>
                    <i class="fa fa-clock-o fa-3x icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div>  
                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">Canceled</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?php $QueryStats = $db->query("SELECT * FROM ce_orders WHERE status='5' or status='6'"); echo (int) $QueryStats->num_rows; ?></h3>
                    <i class="fa fa-times fa-3x icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div>  
                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">Total</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?php $QueryStats = $db->query("SELECT * FROM ce_orders"); echo (int) $QueryStats->num_rows; ?></h3>
                    <i class="ti-layers-alt icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div>  
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-0">Pending Orders</p>
                  <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Send</th>
                          <th>Receive</th>
                          <th>Username</th>
                          <th>Status</th>
                          <th>Date</th>
                          <th>Action</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <?php
                        $query = $db->query("SELECT * FROM ce_orders WHERE status < 4 ORDER BY id");
                        if($query->num_rows>0) {
                          while($row = $query->fetch_assoc()) {
                            ?>
                            <tr>
                              <td><?php echo $row['id']; ?></td>
                              <td><img src="<?php echo gticon($row['gateway_send']); ?>" width="24px" height="24px"> <?php echo gatewayinfo($row['gateway_send'],"name"); ?> (<?php echo $row['amount_send']." ".gatewayinfo($row['gateway_send'],"currency"); ?>)</td>
                              <td><img src="<?php echo gticon($row['gateway_receive']); ?>" width="24px" height="24px">  <?php echo gatewayinfo($row['gateway_receive'],"name"); ?> (<?php echo $row['amount_receive']." ".gatewayinfo($row['gateway_receive'],"currency"); ?>)</td>
                              <td><?php if($row['uid']) { ?><a href="./?a=users&b=edit&id=<?php echo $row['uid']; ?>"><?php if(idinfo($row['uid'],"first_name")) { echo idinfo($row['uid'],"first_name")." ".idinfo($row['uid'],"last_name"); } else { echo idinfo($row['uid'],"email"); } ?></a><? } else { echo 'none'; } ?></td>
                              <td><?php $status = ce_decodeStatus($row['status']); ?><span class="badge badge-<?php echo $status['style']; ?>"><?php echo $status['text']; ?></span></td>
                              <td><?php echo date("d/m/Y H:ma",$row['created']); ?></td>
                              <td>
                                <a href="./?a=exchange_orders&b=explore&id=<?php echo $row['id']; ?>" class="badge badge-primary"><i class="fa fa-search"></i> Explore</a>
                              </td>
                            </tr>
                            <?php
                          }
                        } else {
                          echo '<tr><td colspan="7">No new orders yet.</td></tr>';
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <hr>

          <div class="row">
              <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-5 mb-4 mb-xl-0">
                  <h3 class="font-weight-normal mb-0">Support Tickets Overview</h3>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">Solved</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?php $QueryStats = $db->query("SELECT * FROM ce_tickets WHERE status='2'"); echo (int) $QueryStats->num_rows; ?></h3>
                    <i class="fa fa-clock-o fa-3x icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div>  
                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">Pending</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?php $QueryStats = $db->query("SELECT * FROM ce_tickets WHERE status='9' or status='8'"); echo (int) $QueryStats->num_rows; ?></h3>
                    <i class="fa fa-check fa-3x icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div> 
                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">Closed</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?php $QueryStats = $db->query("SELECT * FROM ce_tickets WHERE status='1'"); echo (int) $QueryStats->num_rows; ?></h3>
                    <i class="fa fa-times fa-3x icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div>  
                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">Total</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?php $QueryStats = $db->query("SELECT * FROM ce_tickets"); echo (int) $QueryStats->num_rows; ?></h3>
                    <i class="ti-layers-alt icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div>  
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-0">Pending Tickets</p>
                  <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Username</th>
                          <th>Subject</th>
                          <th>Created on</th>
                          <th>Last update</th>
                          <th>Served by</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <?php
                        $query = $db->query("SELECT * FROM ce_tickets WHERE status='9' ORDER BY id");
                        if($query->num_rows>0) {
                          while($row = $query->fetch_assoc()) {
                            ?>
                            <tr>
                              <td><?php echo $row['id']; ?></td>
                              <td><a href="./?a=users&b=edit&id=<?php echo $row['uid']; ?>"><?php if(idinfo($row['uid'],"first_name")) { echo idinfo($row['uid'],"first_name")." ".idinfo($row['uid'],"last_name"); } else { echo idinfo($row['uid'],"email"); } ?></a></td>
                              <td><?php echo $row['title']; ?></td>
                              <td><?php echo date("d/m/Y H:ma",$row['created']); ?></td>
                              <td><?php if($row['updated']) { echo date("d/m/Y H:ma",$row['updated']); } else { echo 'n/a'; } ?></td>
                              <td><?php if($row['served_by']) { echo idinfo($row['served_by'],"username"); } else { echo 'n/a'; } ?></td>
                              <td>
                                <?php
                                if($row['status'] == "9") {
                                    $status = '<span class="badge badge-success">New reply</span>';
                                    $class = 'table-info';
                                } elseif($row['status'] == "8") {
                                    $status = '<span class="badge badge-info">Awaiting reply</span>';
                                    $class = 'table-danger';
                                } else {
                                  $status = '<span class="badge badge-default">Unknown</span>';
                                }
                                echo $status;
                                ?>
                              </td>
                              <td>
                                <a href="./?a=tickets&b=view&id=<?php echo $row['id']; ?>" class="badge badge-primary"><i class="fa fa-search"></i> View</a>
                              </td>
                            </tr>
                            <?php
                          }
                        } else {
                          echo '<tr><td colspan="8">No support tickets yet.</td></tr>';
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <hr>

          <div class="row">
              <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-5 mb-4 mb-xl-0">
                  <h3 class="font-weight-normal mb-0">Reviews Overview</h3>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">Published</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?php $QueryStats = $db->query("SELECT * FROM ce_users_reviews WHERE status='1'"); echo (int) $QueryStats->num_rows; ?></h3>
                    <i class="fa fa-clock-o fa-3x icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div>  
                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">Under Review</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?php $QueryStats = $db->query("SELECT * FROM ce_users_reviews WHERE status='2'"); echo (int) $QueryStats->num_rows; ?></h3>
                    <i class="fa fa-check fa-3x icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div> 
                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">Canceled</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?php $QueryStats = $db->query("SELECT * FROM ce_users_reviews WHERE status='3'"); echo (int) $QueryStats->num_rows; ?></h3>
                    <i class="fa fa-times fa-3x icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div>  
                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">Total</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?php $QueryStats = $db->query("SELECT * FROM ce_users_reviews"); echo (int) $QueryStats->num_rows; ?></h3>
                    <i class="ti-layers-alt icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div>  
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-0">Pending Reviews</p>
                  <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                      <thead>
                        <tr>
                          <th>Order</th>
                          <th>Display name</th>
                          <th>Comment</th>
                          <th>Type</th>
                          <th>Date</th>
                          <th>Action</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <?php
                        $query = $db->query("SELECT * FROM ce_users_reviews WHERE status='2' ORDER BY id");
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
                              <td><?php echo date("d/m/Y H:ma",$row['posted']); ?></td>
                              <td>
                                <a href="./?a=reviews&b=approve&id=<?php echo $row['id']; ?>" class="badge badge-success"><i class="fa fa-check"></i> Approve</a> 
                                <a href="./?a=reviews&b=cancel&id=<?php echo $row['id']; ?>" class="badge badge-danger"><i class="fa fa-times"></i> Cancel</a>
                              </td>
                            </tr>
                            <?php
                          }
                        } else {
                          echo '<tr><td colspan="6">No customer reviews yet.</td></tr>';
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <hr>

          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-0">Pending Users Documents</p>
                  <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                      <thead>
                        <tr>
                          <th>Username</th>
                          <th>Documents</th>
                          <th>Action</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <?php
                        $query = $db->query("SELECT * FROM ce_users WHERE documents_pending='1' ORDER BY id");
                        if($query->num_rows>0) {
                          while($row = $query->fetch_assoc()) {
                            ?>
                            <tr>
                              <td><a href="./?a=users&b=edit&id=<?php echo $row['id']; ?>"><?php if($row['first_name']) { echo $row['first_name']." ".$row['last_name']; } else { echo $row['email']; } ?></a></td>
                              <td><?php $DocQuery = $db->query("SELECT * FROM ce_users_documents WHERE uid='$row[id]' and status='1'"); echo (int) $DocQuery->num_rows; ?></td>
                              <td>
                                <a href="./?a=users&b=edit&id=<?php echo $row['id']; ?>#documents" class="badge badge-primary"><i class="fa fa-search"></i> Preview</a> 
                              </td>
                            </tr>
                            <?php
                          }
                        } else {
                          echo '<tr><td colspan="3">No new documents yet.</td></tr>';
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-0">Pending Reserve Requests</p>
                  <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                      <thead>
                        <tr>
                          <th>Email address</th>
                          <th>Gateway</th>
                          <th>Requested amount</th>
                          <th>Requested on</th>
                          <th>Action</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <?php
                        $query = $db->query("SELECT * FROM ce_reserve_requests WHERE updated_on='' or updated_on='0' ORDER BY id");
                        if($query->num_rows>0) {
                          while($row = $query->fetch_assoc()) {
                            ?>
                            <tr>
                              <td><?php echo $row['email']; ?></td>
                              <td><?php echo gatewayinfo($row['gateway_id'],"name")." ".gatewayinfo($row['gateway_id'],"currency"); ?></td>
                              <td><?php echo $row['amount']." ".gatewayinfo($row['gateway_id'],"currency"); ?></td>
                              <td><?php echo date("d/m/Y H:ma",$row['requested_on']); ?></td>
                              <td>
                                <a href="./?a=reserve_requests&b=update&id=<?php echo $row['id']; ?>" class="badge badge-success"><i class="fa fa-check"></i> Update</a> 
                                <a href="./?a=reserve_requests&b=delete&id=<?php echo $row['id']; ?>" class="badge badge-danger"><i class="fa fa-trash"></i> Delete</a>
                              </td>
                            </tr>
                            <?php
                          }
                        } else {
                          echo '<tr><td colspan="5">No new requests yet.</td></tr>';
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          

          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-0">Pending Withdrawals</p>
                  <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Username</th>
                          <th>Gateway</th>
                          <th>Account</th>
                          <th>Amount</th>
                          <th>Requested on</th>
                          <th>Action</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <?php
                        $query = $db->query("SELECT * FROM ce_users_withdrawals WHERE status='1' ORDER BY id");
                        if($query->num_rows>0) {
                          while($row = $query->fetch_assoc()) {
                            ?>
                            <tr>
                              <td><?php echo $row['id']; ?></td>
                              <td><a href="./?a=users&b=edit&id=<?php echo $row['uid']; ?>"><?php if(idinfo($row['uid'],"first_name")) { echo idinfo($row['uid'],"first_name")." ".idinfo($row['uid'],"last_name"); } else { echo idinfo($row['uid'],"email"); } ?></a></td>
                              <td><?php echo gatewayinfo($row['gateway'],"name")." ".gatewayinfo($row['gateway'],"currency"); ?></td>
                              <td><?php echo $row['account']; ?></td>
                              <td><?php echo $row['amount']." ".$row['currency']; ?></td>
                              <td><?php echo date("d/m/Y H:ma",$row['requested_on']); ?></td>
                              <td>
                                <a href="./?a=withdrawals&b=approve&id=<?php echo $row['id']; ?>" class="badge badge-success"><i class="fa fa-check"></i> Approve</a> 
                                <a href="./?a=withdrawals&b=cancel&id=<?php echo $row['id']; ?>" class="badge badge-danger"><i class="fa fa-times"></i> Cancel</a>
                              </td>
                            </tr>
                            <?php
                          }
                        } else {
                          echo '<tr><td colspan="7">No new withdrawal requests yet.</td></tr>';
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>