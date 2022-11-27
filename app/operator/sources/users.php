
<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if($op['can_manage_users'] !== "1") {
    header("Location: ./");
}

$b = protect($_GET['b']);
if($b == "doc_accept") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_users_documents WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=users");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-accept"></i> Accept Document</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                <?php
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                        $update = $db->query("UPDATE ce_users_documents SET status='3' WHERE id='$id'");
                        $check = $db->query("SELECT * FROM ce_users_documents WHERE uid='$row[uid]' and status='1'");
                        if($check->num_rows==0) {
                            $update = $db->query("UPDATE ce_users SET documents_pending='0' WHERE id='$row[uid]'");
                        }
                        echo success("Document was accepted successfully.");
                        // update operator activity start
                        $activity_time = time();
                        $activity_ip = $_SERVER['REMOTE_ADDR'];
                        $update = $db->query("INSERT ce_operators_activity (oid,activity_type,activity_id,activity_value,ip,created) VALUES ('$_SESSION[ce_operator_uid]','accept_document','$row[id]','$row[uid]','$activity_ip','$activity_time')");
                        // update operator activity end
                        echo '<meta http-equiv="refresh" content="1; url=./?a=users&b=edit&id='.$row[uid].'#documents">';
                     } else {
                        echo info("Are you sure you want to accept this document?");
                        $doc = $row;
                        ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                    <th>Username</th>
                                    <th>Document Type</th>
                                        <th>Document Number</th>
                                        <th>Additional Information</th>
                                        <th>Uploaded on</th>
                                        <th>File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                            <tr>
                                                <td><a href="./?a=users&b=edit&id=<?php echo $doc['uid']; ?>"><?php if(empty(idinfo($doc['uid'],"first_name"))) { echo idinfo($doc['uid'],"email"); } else { echo idinfo($doc['uid'],"first_name")." ".idinfo($doc['uid'],"last_name"); } ?></a></td>
                                                <td>
                                                <?php 
                                                if($doc['document_type'] == "1") {
                                                    $document_type = 'Nationality ID Card (front)';
                                                } elseif($doc['document_type'] == "2") {
                                                    $document_type = 'Nationality ID Card (back)';
                                                } elseif($doc['document_type'] == "3") {
                                                    $document_type = 'Passport';
                                                } elseif($doc['document_type'] == "4") {
                                                    $document_type = 'Driver License';
                                                } elseif($doc['document_type'] == "5") {
                                                    $document_type = 'Account Ownership';
                                                } else {
                                                    $document_type = 'Unknown';
                                                }
                                                echo $document_type; 
                                                ?>
                                                </td>
                                                <td><?php echo $doc['u_field_1']; ?></td>
                                                <td><?php echo $doc['u_field_2']; ?></td>
                                                <td><?php echo date("d/m/y h:ia",$doc['uploaded']); ?></td>
                                                <td><a href="<?php echo $settings['url'].$doc['document_path']; ?>" target="_blank" class="badge badge-primary"><i class="fa fa-search"></i> Preview</a></td>
                                            </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <?php
                        echo '<a href="./?a=users&b=doc_accept&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-check"></i> Yes, I accept</a> 
                        <a href="./?a=users&b=edit&id='.$doc[uid].'" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
                     }
                     ?>
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "doc_reject") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_users_documents WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=users");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-times"></i> Reject Document</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                <?php
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                        $u_field_5 = protect($_POST['u_field_5']);
                        $update = $db->query("UPDATE ce_users_documents SET status='2',u_field_5='$u_field_5' WHERE id='$id'");
                        $check = $db->query("SELECT * FROM ce_users_documents WHERE uid='$row[uid]' and status='1'");
                        if($check->num_rows==0) {
                            $update = $db->query("UPDATE ce_users SET documents_pending='0' WHERE id='$row[uid]'");
                        }
                        // update operator activity start
                        $activity_time = time();
                        $activity_ip = $_SERVER['REMOTE_ADDR'];
                        $update = $db->query("INSERT ce_operators_activity (oid,activity_type,activity_id,activity_value,ip,created) VALUES ('$_SESSION[ce_operator_uid]','reject_document','$row[id]','$row[uid]','$activity_ip','$activity_time')");
                        // update operator activity end
                        echo success("Document was rejected successfully.");
                        echo '<meta http-equiv="refresh" content="1; url=./?a=users&b=edit&id='.$row[uid].'#documents">';
                     } else {
                        echo info("Are you sure you want to reject this document?");
                        $doc = $row;
                        ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                    <th>Username</th>
                                    <th>Document Type</th>
                                        <th>Document Number</th>
                                        <th>Additional Information</th>
                                        <th>Uploaded on</th>
                                        <th>File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                            <tr>
                                                <td><a href="./?a=users&b=edit&id=<?php echo $doc['uid']; ?>"><?php if(empty(idinfo($doc['uid'],"first_name"))) { echo idinfo($doc['uid'],"email"); } else { echo idinfo($doc['uid'],"first_name")." ".idinfo($doc['uid'],"last_name"); } ?></a></td>
                                                <td>
                                                <?php 
                                                if($doc['document_type'] == "1") {
                                                    $document_type = 'Nationality ID Card (front)';
                                                } elseif($doc['document_type'] == "2") {
                                                    $document_type = 'Nationality ID Card (back)';
                                                } elseif($doc['document_type'] == "3") {
                                                    $document_type = 'Passport';
                                                } elseif($doc['document_type'] == "4") {
                                                    $document_type = 'Driver License';
                                                } elseif($doc['document_type'] == "5") {
                                                    $document_type = 'Account Ownership';
                                                } else {
                                                    $document_type = 'Unknown';
                                                }
                                                echo $document_type; 
                                                ?>
                                                </td>
                                                <td><?php echo $doc['u_field_1']; ?></td>
                                                <td><?php echo $doc['u_field_2']; ?></td>
                                                <td><?php echo date("d/m/y h:ia",$doc['uploaded']); ?></td>
                                                <td><a href="<?php echo $settings['url'].$doc['document_path']; ?>" target="_blank" class="badge badge-primary"><i class="fa fa-search"></i> Preview</a></td>
                                            </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <form action="./?a=users&b=doc_reject&id=<?php echo $id; ?>&confirmed=1" method="POST">
                        <div class="form-group">
                            <label>Comment</label>
                            <textarea class="form-control" name="u_field_5" rows="5" placeholder="Leave comment to user why his document was rejected."></textarea>
                        </div>
                        <?php
                        echo '<button type="submit" name="ce_btn" value="reject"  class="btn btn-success"><i class="fa fa-check"></i> Yes, I accept</button> 
                        <a href="./?a=users&b=edit&id='.$doc[uid].'" class="btn btn-danger"><i class="fa fa-times"></i> No</a></form>';
                     }
                     ?>
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "edit") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_users WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=users");
    }
    $row = $query->fetch_assoc();
    ?>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-pencil"></i> Edit User</h4>
                <br><br>
            </div>
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <?php
                        $CEAction = protect($_POST['ce_btn']);
                        if(isset($CEAction) && $CEAction == "save") {
                            if($row['status'] == "1") { 
                                echo error("You cannot edit administrator account.");
                            } else { 
                                $email = protect($_POST['email']);
                                $username = protect($_POST['username']);
                                $first_name = protect($_POST['first_name']);
                                $last_name = protect($_POST['last_name']);
                                $ip = protect($_POST['ip']);
                                $country = protect($_POST['country']);
                                $city = protect($_POST['city']);
                                $address = protect($_POST['address']);
                                $zip_code = protect($_POST['zip_code']);
                                $birthday_date = protect($_POST['birthday_date']);
                                $mobile_number = protect($_POST['mobile_number']);
                                $status = protect($_POST['status']);
                                $level = protect($_POST['level']);
                                $discount_level = protect($_POST['discount_level']);
                                $exchanged_volume = protect($_POST['exchanged_volume']);
                                if(isset($_POST['email_verified'])) { $email_verified = 1; } else { $email_verified = 0; }
                                if(isset($_POST['document_verified'])) { $document_verified = 1; } else { $document_verified = 0; }
                                if(isset($_POST['twoFA'])) { $twoFA = 1; } else { $twoFA = 0; }
                                $check_e = $db->query("SELECT * FROM ce_users WHERE email='$email'");
                                $check_u = $db->query("SELECT * FROM ce_users WHERE username='$username'");
                                if(empty($email)) {
                                    echo error("Please enter email address.");
                                } elseif(!isValidEmail($email)) {
                                    echo error("Please enter a valid email address.");
                                } elseif($email !== $row['email'] && $check_e->num_rows>0) {
                                    echo error("This email address is already used.");
                                } elseif(!empty($username) && !isValidUsername($username)) {
                                    echo error("Please enter a valid username.");
                                } elseif(!empty($username && $username !== $row['username']) && $check_u->num_rows>0) {
                                    echo error("This username is already used.");
                                } elseif(!empty($zip_code) && !is_numeric($zip_code)) {
                                    echo error("Please enter zip code with numbers.");
                                } else {
                                    $update = $db->query("UPDATE ce_users SET email_verified='$email_verified',document_verified='$document_verified',twoFA='$twoFA',email='$email',username='$username',first_name='$first_name',last_name='$last_name',ip='$ip',country='$country',city='$city',address='$address',zip_code='$zip_code',birthday_date='$birthday_date',mobile_number='$mobile_number',status='$status',level='$level',discount_level='$discount_level',exchanged_volume='$exchanged_volume' WHERE id='$row[id]'");
                                    echo success("Your changes was saved successfully.");
                                    $query = $db->query("SELECT * FROM ce_users WHERE id='$row[id]'");
                                    $row = $query->fetch_assoc();
                                    // update operator activity start
                        $activity_time = time();
                        $activity_ip = $_SERVER['REMOTE_ADDR'];
                        $update = $db->query("INSERT ce_operators_activity (oid,activity_type,activity_id,activity_value,ip,created) VALUES ('$_SESSION[ce_operator_uid]','edit_user','$row[id]','$row[email]','$activity_ip','$activity_time')");
                        // update operator activity end
                                }
                            }
                        }
                        ?>

                        <form action="" method="POST">
                        <div class="form-group">
                                <label>Email address</label>
                                <input type="text" class="form-control" name="email" value="<?php echo $row['email']; ?>">
                            </div>
                            <div class="form-group">
                                <label>New password</label>
                                <input type="text" class="form-control" name="npassword" placeholder="Leave empty if do not want to change.">
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" class="form-control" name="username" value="<?php echo $row['username']; ?>">
                            </div>
                            <div class="form-group">
                                <label>First name</label>
                                <input type="text" class="form-control" name="first_name" value="<?php echo $row['first_name']; ?>">
                            </div>
                            <div class="form-group">
                                <label>Last name</label>
                                <input type="text" class="form-control" name="last_name" value="<?php echo $row['last_name']; ?>">
                            </div>
                            <div class="form-group">
                                <label>IP Address</label>
                                <input type="text" class="form-control" name="ip" value="<?php echo $row['ip']; ?>">
                            </div>
                            <div class="form-group">
                                <label>Country</label>
                                <input type="text" class="form-control" name="country" value="<?php echo $row['country']; ?>">
                            </div>
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" class="form-control" name="city" value="<?php echo $row['city']; ?>">
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" class="form-control" name="address" value="<?php echo $row['address']; ?>">
                            </div>
                            <div class="form-group">
                                <label>ZIP Code</label>
                                <input type="text" class="form-control" name="zip_code" value="<?php echo $row['zip_code']; ?>">
                            </div>
                            <div class="form-group">
                                <label>Date of birth</label>
                                <input type="text" class="form-control" name="birthday_date" id="datepicker" value="<?php echo $row['birthday_date']; ?>">
                            </div>
                            <div class="form-group">
                                <label>Mobile number</label>
                                <input type="text" class="form-control" name="mobile_number" value="<?php echo $row['mobile_number']; ?>">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status">
                                    <option value="1" <?php if($row['status'] == "1") { echo 'selected'; } ?>>Completed</option>
                                    <option value="2" <?php if($row['status'] == "2") { echo 'selected'; } ?>>Blocked</option>
                                    <option value="16" <?php if($row['status'] == "16") { echo 'selected'; } ?>>Not Completed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Level</label>
                                <select class="form-control" name="level">
                                    <option value="3" <?php if($row['level'] == "3") { echo 'selected'; } ?>>Member</option>
                                    <option value="2" <?php if($row['level'] == "2") { echo 'selected'; } ?>>Operator</option>
                                    <option value="1" <?php if($row['level'] == "1") { echo 'selected'; } ?>>Admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Discount Level</label>
                                <select class="form-control" name="discount_level">
                                    <?php
                                    $dquery = $db->query("SELECT * FROM ce_discount_system ORDER BY discount_level");
                                    if($dquery->num_rows>0) {
                                        while($d = $dquery->fetch_assoc()) {
                                            if($row['discount_level'] == $d['discount_level']) { $sel = 'selected'; } else { $sel = ''; }
                                            echo '<option value="'.$d[discount_level].'" '.$sel.'>Level: '.$d[discount_level].', Discount: '.$d[discount_percentage].'%</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Exchanged Volume</label>
                                <input type="text" class="form-control" name="exchanged_volume" value="<?php echo $row['exchanged_volume']; ?>">
                            </div>
                            <div class="form-check">
                                <div class="checkbox">
                                    <label for="checkbox1" class="form-check-label ">
                                        <input type="checkbox" id="checkbox1" name="email_verified" <?php if($row['email_verified'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Email verified
                                    </label>
                                </div>
                            </div>
                            <div class="form-check">
                                <div class="checkbox">
                                    <label for="checkbox2" class="form-check-label ">
                                        <input type="checkbox" id="checkbox2" name="document_verified" <?php if($row['document_verified'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Documents verified
                                    </label>
                                </div>
                            </div>
                            <div class="form-check">
                                <div class="checkbox">
                                    <label for="checkbox3" class="form-check-label ">
                                        <input type="checkbox" id="checkbox3" name="twoFA" <?php if($row['twoFA'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Two-Factor Auth
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" name="ce_btn" value="save">Save Changes</button>
                        </form>
                    </div>
                </div>
              
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Send quick email</h4>
                        <?php 
                        if(isset($CEAction) && $CEAction == "send") { 
                            $subject = protect($_POST['subject']);
                            $name = protect($_POST['name']);
                            $email = protect($_POST['email']);
                            $message = protect($_POST['message']);
                            if(empty($subject) or empty($name) or empty($email) or empty($message)) {
                                echo error("All fields are required.");
                            } elseif(!isValidEmail($email)) {
                                echo error("Please enter a valid email address.");
                            } else {
                                CE_SendToUser($row['email'],$subject,$message,$name,$email);
                                echo success("Message was sent successfully.");
                            }
                        }
                        ?>

                        <form action="" method="POST">
                                <div class="form-group">
                                    <label>Subject</label>
                                    <input type="text" class="form-control" name="subject">
                            </div>
                                <div class="form-group">
                                    <label>From name</label>
                                    <input type="text" class="form-control" name="name" value="<?php echo $settings['name']; ?> Support">
                            </div>
                                <div class="form-group">
                                    <label>From email</label>
                                    <input type="text" class="form-control" name="email" value="<?php echo $op['email']; ?>">
                            </div>
                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea class="form-control" name="message" rows="5"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" name="ce_btn" value="send">Send</button>
                            </form>
                    </div>
                </div>
                <br>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Referral Earnings</h4>
                        <?php
                        if(isset($CEAction) && $CEAction == "update_balance") {
                            $newb = protect($_POST['newb']);
                            if(empty($newb)) {
                                echo error("Please enter a new balance number.");
                            } elseif(!is_numeric($newb)) {
                                echo error("Please enter new balance with numbers.");
                            } else {
                                $update = $db->query("UPDATE ce_users_earnings SET amount=amount+$newb WHERE uid='$row[id]'");
                                // update operator activity start
                        $activity_time = time();
                        $activity_ip = $_SERVER['REMOTE_ADDR'];
                        $update = $db->query("INSERT ce_operators_activity (oid,activity_type,activity_id,activity_value,ip,created) VALUES ('$_SESSION[ce_operator_uid]','update_earnings','$row[id]','$newb','$activity_ip','$activity_time')");
                        // update operator activity end
                            }
                        }
                        $BalanceQuery = $db->query("SELECT * FROM ce_users_earnings WHERE uid='$row[id]'");
                        if($BalanceQuery->num_rows>0) {
                            $b = $BalanceQuery->fetch_assoc();
                            $balance = $b['amount']." ".$b['currency'];
                        } else {
                            $balance = '0 USD';
                        }
                        ?>
                        <h3>Current balance: <?php echo $balance; ?></h3>
                        <br>
                        <form action="" method="POST">
                            <div class="form-group">
                                <label>Amount</label>
                                <input type="text" class="form-control" name="newb">
                            </div>
                            <button type="submit" class="btn btn-primary" name="ce_btn" value="update_balance">Update</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title" id="documents">Documents</h4>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Document Number</th>
                                        <th>Additional Information</th>
                                        <th>Uploaded on</th>
                                        <th>Status</th>
                                        <th>File</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $DocQuery = $db->query("SELECT * FROM ce_users_documents WHERE uid='$row[id]' ORDER BY id");
                                    if($DocQuery->num_rows>0) {
                                        while($doc = $DocQuery->fetch_assoc()) {
                                            ?>
                                            <tr>
                                                <td>
                                                <?php 
                                                if($doc['document_type'] == "1") {
                                                    $document_type = 'Nationality ID Card (front)';
                                                } elseif($doc['document_type'] == "2") {
                                                    $document_type = 'Nationality ID Card (back)';
                                                } elseif($doc['document_type'] == "3") {
                                                    $document_type = 'Passport';
                                                } elseif($doc['document_type'] == "4") {
                                                    $document_type = 'Driver License';
                                                } elseif($doc['document_type'] == "5") {
                                                    $document_type = 'Account Ownership';
                                                } else {
                                                    $document_type = 'Unknown';
                                                }
                                                echo $document_type; 
                                                ?>
                                                </td>
                                                <td><?php echo $doc['u_field_1']; ?></td>
                                                <td><?php echo $doc['u_field_2']; ?></td>
                                                <td><?php echo date("d/m/y h:ia",$doc['uploaded']); ?></td>
                                                <td>
                                                    <?php
                                                    if($doc['status'] == "1") {
                                                        $status = '<span class="badge badge-primary">Under Review</span>';
                                                    } elseif($doc['status'] == "2") {
                                                        $status = '<span class="badge badge-danger">Rejected</span>';
                                                    } elseif($doc['status'] == "3") {
                                                        $status = '<span class="badge badge-success">Accepted</span>';
                                                    } else {
                                                        $status = '<span class="badge badge-default">Unknown</span>';
                                                    }
                                                    echo $status;
                                                    ?>
                                                </td>
                                                <td><a href="<?php echo $settings['url'].$doc['document_path']; ?>" target="_blank" class="badge badge-primary"><i class="fa fa-search"></i> Preview</a></td>
                                                <td> 
                                                    <?php if($doc['status'] == "1") { ?>
                                                        <a href="./?a=users&b=doc_accept&id=<?php echo $doc['id']; ?>" class="badge badge-success"><i class="fa fa-check"></i> Accept</a>
                                                        <a href="./?a=users&b=doc_reject&id=<?php echo $doc['id']; ?>" class="badge badge-danger"><i class="fa fa-times"></i> Reject</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="7">No documents yet.</td></tr>';
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
} elseif($b == "delete") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_users WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=users");
    }
    $row = $query->fetch_assoc();
    ?>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-trash"></i> Delete User</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                <?php
                    if($row['status'] == "1") { 
                        echo error("You cannot delete administrator account.");
                    } else { 
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                        // update operator activity start
                        $activity_time = time();
                        $activity_ip = $_SERVER['REMOTE_ADDR'];
                        $update = $db->query("INSERT ce_operators_activity (oid,activity_type,activity_id,activity_value,ip,created) VALUES ('$_SESSION[ce_operator_uid]','delete_user','$row[id]','$row[email]','$activity_ip','$activity_time')");
                        // update operator activity end
                        $delete = $db->query("DELETE FROM ce_users WHERE id='$row[id]'");
                        $delete = $db->query("DELETE FROM ce_users_earnings WHERE uid='$row[id]'");
                        $delete = $db->query("DELETE FROM ce_users_withdrawals WHERE uid='$row[id]'");
                        $delete = $db->query("DELETE FROM ce_users_documents WHERE uid='$row[id]'");
                        $delete = $db->query("DELETE FROM ce_tickets WHERE uid='$row[id]'");
                        $delete = $db->query("DELETE FROM ce_tickets_messages WHERE author='$row[id]'");
                        $delete = $db->query("DELETE FROM ce_users_reviews WHERE author='$row[id]'");
                        $delete = $db->query("DELETE FROM ce_orders WHERE uid='$row[id]'");
                        echo success("User ($row[email]) was deleted successfully.");
                     } else {
                        echo info("Are you sure you want to delete user ($row[email])?");
                        echo '<a href="./?a=users&b=delete&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-check"></i> Yes, I confirm</a> 
                        <a href="./?a=users" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
                     }
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
                <h4 class="card-title"><i class="ti-user"></i> Users</h4>
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
                          <th>Email address</th>
                          <th>IP Address</th>
                          <th>Name</th>
                          <th>Orders</th>
                          <th>Status</th>
                          <th>Level</th>
                          <th>Action</th>
                        </tr>  
                        </thead>
                        <tbody>
                            <?php
                            $searching = 0;
                            $CEAction = protect($_POST['ce_btn']);
                            if(isset($CEAction) && $CEAction == "search") {
                                $first_name = protect($_POST['first_name']);
                                $last_name = protect($_POST['last_name']);
                                $email = protect($_POST['email']);
                                $username = protect($_POST['username']);
                                $ip = protect($_POST['ip']);
                                $searcharr = array();
                                if(!empty($email)) {
                                    $searcharr[] = "email='$email'";
                                }
                                if(!empty($first_name)) {
                                    $searcharr[] = "first_name='$first_name'";
                                }
                                if(!empty($last_name)) {
                                    $searcharr[] = "first_name='$last_name'";
                                }
                                if(!empty($username)) {
                                    $searcharr[] = "username='$username'";
                                }
                                if(!empty($ip)) {
                                    $searcharr[] = "ip='$ip'";
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
                                $query = $db->query("SELECT * FROM ce_users WHERE $filters ORDER BY status DESC, id DESC");
                            } else {
                                $statement = "ce_users";
                                $query = $db->query("SELECT * FROM {$statement} ORDER BY status DESC, id DESC LIMIT {$startpoint} , {$limit}");
                            }
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                              <td><?php echo $row['id']; ?></td>
                              <td><?php echo $row['email']; ?> <?php if(!empty($row['username'])) { echo '('.$row[username].')'; } ?></td>
                              <td><?php echo $row['ip'] ?></td>
                              <td><?php echo $row['first_name']." ".$row['last_name']; ?></td>
                              <td><?php $QueryNum = $db->query("SELECT * FROM ce_orders WHERE uid='$row[id]'"); echo (int) $QueryNum->num_rows; ?></td>
                              <td>
                              <?php
                                    if($row['status'] == "1") {
                                        $status = '<span class="badge badge-success">Completed</span>';
                                    } elseif($row['status'] == "2") {
                                        $status = '<span class="badge badge-danger">Blocked</span>';
                                    } elseif($row['status'] == "16") {
                                        $status = '<span class="badge badge-warning">Not Completed</span>';
                                    } else {
                                        $status = '<span class="badge badge-default">Unknown</span>';
                                    }
                                    echo $status;
                                    ?>
                              </td>
                              <td>
                                    <?php
                                    if($row['level'] == "1") {
                                        $level = '<span class="badge badge-success">Admin</span>';
                                    } elseif($row['level'] == "2") {
                                        $level = '<span class="badge badge-info">Operator</span>';
                                    } else {
                                        $level = '<span class="badge badge-default">Member</span>';
                                    }
                                    echo $level;
                                    ?>
                              </td>
                              <td>
                                <a href="./?a=users&b=edit&id=<?php echo $row['id']; ?>" class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</a> 
                                <a href="./?a=users&b=delete&id=<?php echo $row['id']; ?>" class="badge badge-danger"><i class="fa fa-trash"></i> Delete</a>
                              </td>
                            </tr>
                                    <?php
                                }
                            } else {
                                if($searching == "1") {
                                    echo '<tr><td colspan="8">No results were found for the criteria you set.</td></tr>';
                                } else {
                                    echo '<tr><td colspan="8">No users yet.</td></tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                    <?php
                    if($searching == "0") {
                        $ver = "./?a=users";
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
                                    <label>Username</label>
                                    <input type="text" class="form-control" name="username" value="<?php if(isset($_POST['username'])) { echo protect($_POST['username']); } ?>">
                                </div>
                                <div class="form-group">
                                    <label>First name</label>
                                    <input type="text" class="form-control" name="first_name" value="<?php if(isset($_POST['first_name'])) { echo protect($_POST['first_name']); } ?>">
                                </div>
                                <div class="form-group">
                                    <label>Last name</label>
                                    <input type="text" class="form-control" name="last_name" value="<?php if(isset($_POST['last_name'])) { echo protect($_POST['last_name']); } ?>">
                                </div>
                                <div class="form-group">
                                    <label>IP Address</label>
                                    <input type="text" class="form-control" name="ip" value="<?php if(isset($_POST['ip'])) { echo protect($_POST['ip']); } ?>">
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