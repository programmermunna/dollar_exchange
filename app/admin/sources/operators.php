
<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$b = protect($_GET['b']);
if($b == "activity") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_operators WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=operators");
    }
    $row = $query->fetch_assoc();
    ?>
<div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-search"></i> Operator <b><?php echo $row['username']; ?></b> Activity</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="20%">Date</th>
                                <th width="20%">IP</th>
                                <th width="60%">Activity</th>
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
                            $statement = "ce_operators_activity WHERE oid='$row[id]'";
                            $aquery = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                            if($aquery->num_rows>0) {
                                while($a = $aquery->fetch_assoc()) {
                                    ?>
                                    <tr>
                                    <td><?php echo date("d/m/Y H:i:s",$a['created']); ?></td>
                                    <td><?php echo $a['ip']; ?></td>
                                    <td>
                                    <?php
                                    if($a['activity_type'] == "update_directions") {
                                        echo 'Update exchange directions <a href="./?a=exchange_directions&b=edit&id='.$a[activity_id].'">#'.$a[activity_id].'</a>';
                                    } elseif($a['activity_type'] == "add_gateway") {
                                        echo 'Add new gateway '.gatewayinfo($a[activity_id],"name").' '.gatewayinfo($a[activity_id],"currency");
                                    } elseif($a['activity_type'] == "edit_gateway") {
                                        echo 'Edit gateway '.gatewayinfo($a[activity_id],"name").' '.gatewayinfo($a[activity_id],"currency");
                                    } elseif($a['activity_type'] == "delete_gateway") {
                                        echo 'Delete gateway '.gatewayinfo($a[activity_id],"name").' '.gatewayinfo($a[activity_id],"currency");
                                    } elseif($a['activity_type'] == "complete_order") {
                                        echo 'Complete order <a href="./?a=exchange_orders&b=explore&id='.$a[activity_id].'">#'.$a[activity_id].'</a>';
                                    } elseif($a['activity_type'] == "update_order") {
                                        echo 'Change status of order <a href="./?a=exchange_orders&b=explore&id='.$a[activity_id].'">#'.$a[activity_id].'</a>';
                                    } elseif($a['activity_type'] == "new_rate") {
                                        echo 'Add exchange rate <a href="./?a=exchange_rules&b=edit&id='.$a[activity_id].'">#'.$a[activity_id].'</a>';
                                    } elseif($a['activity_type'] == "edit_rate") {
                                        echo 'Edit exchange rate <a href="./?a=exchange_rules&b=edit&id='.$a[activity_id].'">#'.$a[activity_id].'</a>';
                                    } elseif($a['activity_type'] == "delete_rate") {
                                        echo 'Delete exchange rate '.$a[activity_id];
                                    } elseif($a['activity_type'] == "new_rule") {   
                                        echo 'Add exchange rule <a href="./?a=exchange_rules&b=edit&id='.$a[activity_id].'">#'.$a[activity_id].'</a>';
                                    } elseif($a['activity_type'] == "edit_rule") {
                                        echo 'Edit exchange rule <a href="./?a=exchange_rules&b=edit&id='.$a[activity_id].'">#'.$a[activity_id].'</a>';
                                    } elseif($a['activity_type'] == "delete_rule") {
                                        echo 'Delete exchange rule '.$a[activity_id];
                                    }  elseif($a['activity_type'] == "new_faq") {
                                        echo 'Add question <a href="./?a=faq&b=edit&id='.$a[activity_id].'">'.$a[activity_value].'</a>';
                                    } elseif($a['activity_type'] == "edit_faq") {
                                        echo 'Edit question <a href="./?a=faq&b=edit&id='.$a[activity_id].'">'.$a[activity_value].'</a>';
                                    }  elseif($a['activity_type'] == "delete_faq") {
                                        echo 'Delete question '.$a[activity_value];
                                    }  elseif($a['activity_type'] == "new_post") {
                                        echo 'Add post <a href="./?a=news&b=edit&id='.$a[activity_id].'">'.$a[activity_value].'</a>';
                                    } elseif($a['activity_type'] == "edit_post") {
                                        echo 'Edit post <a href="./?a=news&b=edit&id='.$a[activity_id].'">'.$a[activity_value].'</a>';
                                    } elseif($a['activity_type'] == "delete_post") {
                                        echo 'Delete post '.$a[activity_value];
                                    } elseif($a['activity_type'] == "new_page") {
                                        echo 'Add page <a href="./?a=pages&b=edit&id='.$a[activity_id].'">'.$a[activity_value].'</a>';
                                    } elseif($a['activity_type'] == "edit_page") {
                                        echo 'Edit page <a href="./?a=pages&b=edit&id='.$a[activity_id].'">'.$a[activity_value].'</a>';
                                    } elseif($a['activity_type'] == "delete_page") {
                                        echo 'Delete page '.$a[activity_value];
                                    } elseif($a['activity_type'] == "update_reserve") {
                                        echo 'Update reserve request of '.gatewayinfo($a[activity_id],"name").' '.gatewayinfo($a[activity_id],"currency").' with '.$a[activity_value].' '.gatewayinfo($a[activity_id],"currency");
                                    }  elseif($a['activity_type'] == "delete_reserve") {
                                        echo 'Delete reserve request of '.gatewayinfo($a[activity_id],"name").' '.gatewayinfo($a[activity_id],"currency").' with '.$a[activity_value].' '.gatewayinfo($a[activity_id],"currency");
                                    }  elseif($a['activity_type'] == "approve_review") {
                                        echo 'Approve user review #'.$a[activity_id];
                                    }  elseif($a['activity_type'] == "cancel_review") {
                                        echo 'Cancel user review #'.$a[activity_id];
                                    }  elseif($a['activity_type'] == "serve_ticket") {
                                        echo 'Serve ticket <a href="./?a=tickets&b=view&id='.$a[activity_id].'">#'.$a[activity_id].'</a>';
                                    }   elseif($a['activity_type'] == "reply_ticket") {
                                        echo 'Post a new reply in ticket <a href="./?a=tickets&b=view&id='.$a[activity_id].'">#'.$a[activity_id].'</a>';
                                    }  elseif($a['activity_type'] == "solve_ticket") {
                                        echo 'Solve ticket <a href="./?a=tickets&b=view&id='.$a[activity_id].'">#'.$a[activity_id].'</a>';
                                    }  elseif($a['activity_type'] == "close_ticket") {
                                        echo 'Close ticket <a href="./?a=tickets&b=view&id='.$a[activity_id].'">#'.$a[activity_id].'</a>';
                                    } elseif($a['activity_type'] == "accept_document") {
                                        echo 'Accept user document of <a href="./?a=users&b=edit&id='.$a[activity_value].'">'.idinfo($a[activity_value],"email").'</a>';
                                    } elseif($a['activity_type'] == "reject_document") {
                                        echo 'Reject user document of <a href="./?a=users&b=edit&id='.$a[activity_value].'">'.idinfo($a[activity_value],"email").'</a>';
                                    }elseif($a['activity_type'] == "update_earnings") {
                                        echo 'Update user earnings of <a href="./?a=users&b=edit&id='.$a[activity_id].'">'.idinfo($a[activity_id],"email").'</a> with '.$a[activity_value].' USD';
                                    } elseif($a['activity_type'] == "edit_user") {
                                        echo 'Edit user <a href="./?a=users&b=edit&id='.$a[activity_id].'">'.idinfo($a[activity_id],"email").'</a>';
                                    }  elseif($a['activity_type'] == "delete_user") {
                                        echo 'Delete user '.$a[activity_value];
                                    } elseif($a['activity_type'] == "approve_withdrawal") {
                                        echo 'Approve withdrawal request #'.$a[activity_id];
                                    }  elseif($a['activity_type'] == "cancel_withdrawal") {
                                        echo 'Cancel withdrawal request #'.$a[activity_id];
                                    } else { echo'Unknown'; }
                                    ?>
                                </td>
                                </tr>   
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="3">No activity yet.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    </div>
                    <?php
                    $ver = "./?a=operators&b=activity&id=$id";
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
} elseif($b == "add") {
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-plus"></i> Add Operator</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $CEAction = protect($_POST['ce_btn']);
                     if(isset($CEAction) && $CEAction == "new") {
                        $name = protect($_POST['name']);
                        $username = protect($_POST['username']);
                        $email = protect($_POST['email']);
                        $password = protect($_POST['password']);
                        $cpassword = protect($_POST['cpassword']);
                        if(isset($_POST['can_login'])) {  $can_login = '1'; } else { $can_login = '0'; }
                        if(isset($_POST['can_manage_gateways'])) {  $can_manage_gateways = '1'; } else { $can_manage_gateways = '0'; }
                        if(isset($_POST['can_manage_directions'])) {  $can_manage_directions = '1'; } else { $can_manage_directions = '0'; }
                        if(isset($_POST['can_manage_rates'])) {  $can_manage_rates = '1'; } else { $can_manage_rates = '0'; }
                        if(isset($_POST['can_manage_rules'])) {  $can_manage_rules = '1'; } else { $can_manage_rules = '0'; }
                        if(isset($_POST['can_manage_orders'])) {  $can_manage_orders = '1'; } else { $can_manage_orders = '0'; }
                        if(isset($_POST['can_manage_users'])) {  $can_manage_users = '1'; } else { $can_manage_users = '0'; }
                        if(isset($_POST['can_manage_reviews'])) {  $can_manage_reviews = '1'; } else { $can_manage_reviews = '0'; }
                        if(isset($_POST['can_manage_withdrawals'])) {  $can_manage_withdrawals = '1'; } else { $can_manage_withdrawals = '0'; }
                        if(isset($_POST['can_manage_support_tickets'])) {  $can_manage_support_tickets = '1'; } else { $can_manage_support_tickets = '0'; }
                        if(isset($_POST['can_manage_news'])) {  $can_manage_news = '1'; } else { $can_manage_news = '0'; }
                        if(isset($_POST['can_manage_pages'])) {  $can_manage_pages = '1'; } else { $can_manage_pages = '0'; }
                        if(isset($_POST['can_manage_faq'])) {  $can_manage_faq = '1'; } else { $can_manage_faq = '0'; }
                        $check_u = $db->query("SELECT * FROM ce_operators WHERE username='$username'");
                        $check_e = $db->query("SELECT * FROM ce_operators WHERE email='$email'");
                        if(empty($name) or empty($username) or empty($email) or empty($password) or empty($cpassword)) {
                            echo error("All fields are required.");
                        } elseif(!isValidUsername($username)) {
                            echo error("Please enter a valid username.");
                        } elseif($check_u->num_rows>0) {
                            echo error("Operator with this username is already exists.");
                        } elseif(!isValidEmail($email)) {
                            echo error("Please enter a valid email address.");
                        } elseif($check_e->num_rows>0) {
                            echo error("Operator with this email address is already exists.");
                        } elseif($password !== $cpassword) {
                            echo error("Passwords does not match.");
                        } else {
                            $password = password_hash($password, PASSWORD_DEFAULT);
                            $insert = $db->query("INSERT ce_operators (name,username,email,password,can_login,can_manage_gateways,can_manage_directions,can_manage_rates,can_manage_rules,can_manage_orders,can_manage_users,can_manage_reviews,can_manage_withdrawals,can_manage_support_tickets,can_manage_news,can_manage_pages,can_manage_faq) VALUES ('$name','$username','$email','$password','$can_login','$can_manage_gateways','$can_manage_directions','$can_manage_rates','$can_manage_rules','$can_manage_orders','$can_manage_users','$can_manage_reviews','$can_manage_withdrawals','$can_manage_support_tickets','$can_manage_news','$can_manage_pages','$can_manage_faq')");
                            echo success("Operator ($username) was added successfully.");
                        }
                     }
                     ?>

                     <form action="" method="POST">
                     <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                     <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username">
                        </div>
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="text" class="form-control" name="email">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" name="cpassword">
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <b>Permissions:</b>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox1" class="form-check-label ">
                                            <input type="checkbox" id="checkbox1" name="can_login" value="1" class="form-check-input"> Can login to Operator Panel
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox2" class="form-check-label ">
                                            <input type="checkbox" id="checkbox2" name="can_manage_gateways" value="1" class="form-check-input"> Can manage Exchange Gateways
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox3" class="form-check-label ">
                                            <input type="checkbox" id="checkbox3" name="can_manage_directions" value="1" class="form-check-input"> Can manage Exchange Directions
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox4" class="form-check-label ">
                                            <input type="checkbox" id="checkbox4" name="can_manage_rates" value="1" class="form-check-input"> Can manage Exchange Rates
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox5" class="form-check-label ">
                                            <input type="checkbox" id="checkbox5" name="can_manage_rules" value="1" class="form-check-input"> Can manage Exchange Rules
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox6" class="form-check-label ">
                                            <input type="checkbox" id="checkbox6" name="can_manage_orders" value="1" class="form-check-input"> Can manage Exchange Orders
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox7" class="form-check-label ">
                                            <input type="checkbox" id="checkbox7" name="can_manage_users" value="1" class="form-check-input"> Can manage Users
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox8" class="form-check-label ">
                                            <input type="checkbox" id="checkbox8" name="can_manage_reviews" value="1" class="form-check-input"> Can manage Users Reviews
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox9" class="form-check-label ">
                                            <input type="checkbox" id="checkbox9" name="can_manage_withdrawals" value="1" class="form-check-input"> Can manage Users Withdrawals
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox10" class="form-check-label ">
                                            <input type="checkbox" id="checkbox10" name="can_manage_support_tickets" value="1" class="form-check-input"> Can manage Support Tickets
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox13" class="form-check-label ">
                                            <input type="checkbox" id="checkbox13" name="can_manage_news" value="1" class="form-check-input"> Can manage News
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox11" class="form-check-label ">
                                            <input type="checkbox" id="checkbox11" name="can_manage_pages" value="1" class="form-check-input"> Can manage Pages
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox12" class="form-check-label ">
                                            <input type="checkbox" id="checkbox12" name="can_manage_faq" value="1" class="form-check-input"> Can manage FAQ
                                        </label>
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <button type="submit" class="btn btn-primary" name="ce_btn" value="new"><i class="fa fa-plus"></i> Add</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "edit") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_operators WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=operators");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-pencil"></i> Edit Operator</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $CEAction = protect($_POST['ce_btn']);
                     if(isset($CEAction) && $CEAction == "save") {
                        $name = protect($_POST['name']);
                        $username = protect($_POST['username']);
                        $email = protect($_POST['email']);
                        $password = protect($_POST['password']);
                        $cpassword = protect($_POST['cpassword']);
                        if(isset($_POST['can_login'])) {  $can_login = '1'; } else { $can_login = '0'; }
                        if(isset($_POST['can_manage_gateways'])) {  $can_manage_gateways = '1'; } else { $can_manage_gateways = '0'; }
                        if(isset($_POST['can_manage_directions'])) {  $can_manage_directions = '1'; } else { $can_manage_directions = '0'; }
                        if(isset($_POST['can_manage_rates'])) {  $can_manage_rates = '1'; } else { $can_manage_rates = '0'; }
                        if(isset($_POST['can_manage_rules'])) {  $can_manage_rules = '1'; } else { $can_manage_rules = '0'; }
                        if(isset($_POST['can_manage_orders'])) {  $can_manage_orders = '1'; } else { $can_manage_orders = '0'; }
                        if(isset($_POST['can_manage_users'])) {  $can_manage_users = '1'; } else { $can_manage_users = '0'; }
                        if(isset($_POST['can_manage_reviews'])) {  $can_manage_reviews = '1'; } else { $can_manage_reviews = '0'; }
                        if(isset($_POST['can_manage_withdrawals'])) {  $can_manage_withdrawals = '1'; } else { $can_manage_withdrawals = '0'; }
                        if(isset($_POST['can_manage_support_tickets'])) {  $can_manage_support_tickets = '1'; } else { $can_manage_support_tickets = '0'; }
                        if(isset($_POST['can_manage_news'])) {  $can_manage_news = '1'; } else { $can_manage_news = '0'; }
                        if(isset($_POST['can_manage_pages'])) {  $can_manage_pages = '1'; } else { $can_manage_pages = '0'; }
                        if(isset($_POST['can_manage_faq'])) {  $can_manage_faq = '1'; } else { $can_manage_faq = '0'; }
                        $check_u = $db->query("SELECT * FROM ce_operators WHERE username='$username'");
                        $check_e = $db->query("SELECT * FROM ce_operators WHERE email='$email'");
                        if(empty($name) or empty($username) or empty($email)) {
                            echo error("All fields are required.");
                        } elseif(!isValidUsername($username)) {
                            echo error("Please enter a valid username.");
                        } elseif($row['username'] !== $username && $check_u->num_rows>0) {
                            echo error("Operator with this username is already exists.");
                        } elseif(!isValidEmail($email)) {
                            echo error("Please enter a valid email address.");
                        } elseif($row['email'] !== $email && $check_e->num_rows>0) {
                            echo error("Operator with this email address is already exists.");
                        } elseif(!empty($password) && $password !== $cpassword) {
                            echo error("Passwords does not match.");
                        } else {
                            if(empty($password)) {
                                $password = $row['password'];
                            } else { 
                                $password = password_hash($password, PASSWORD_DEFAULT);
                            }
                            $update = $db->query("UPDATE ce_operators SET name='$name',username='$username',email='$email',password='$password',can_login='$can_login',can_manage_gateways='$can_manage_gateways',can_manage_directions='$can_manage_directions',can_manage_rates='$can_manage_rates',can_manage_rules='$can_manage_rules',can_manage_orders='$can_manage_orders',can_manage_users='$can_manage_users',can_manage_reviews='$can_manage_reviews',can_manage_withdrawals='$can_manage_withdrawals',can_manage_support_tickets='$can_manage_support_tickets',can_manage_news='$can_manage_news',can_manage_pages='$can_manage_pages',can_manage_faq='$can_manage_faq' WHERE id='$row[id]'");
                            if($db->error) { echo $db->error; }
                            $query = $db->query("SELECT * FROM ce_operators WHERE id='$id'");
                            $row = $query->fetch_assoc();
                            echo success("Your changes was saved successfully.");
                        }
                     }
                     ?>

                     <form action="" method="POST">
                     <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" value="<?php echo $row['name']; ?>">
                        </div>
                     <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" value="<?php echo $row['username']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="text" class="form-control" name="email" value="<?php echo $row['email']; ?>">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Leave empty if do not want to change it.">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" name="cpassword">
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <b>Permissions:</b>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox1" class="form-check-label ">
                                            <input type="checkbox" id="checkbox1" name="can_login" <?php if($row['can_login'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Can login to Operator Panel
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox2" class="form-check-label ">
                                            <input type="checkbox" id="checkbox2" name="can_manage_gateways" <?php if($row['can_manage_gateways'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Can manage Exchange Gateways
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox3" class="form-check-label ">
                                            <input type="checkbox" id="checkbox3" name="can_manage_directions" <?php if($row['can_manage_directions'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Can manage Exchange Directions
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox4" class="form-check-label ">
                                            <input type="checkbox" id="checkbox4" name="can_manage_rates" <?php if($row['can_manage_rates'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Can manage Exchange Rates
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox5" class="form-check-label ">
                                            <input type="checkbox" id="checkbox5" name="can_manage_rules" <?php if($row['can_manage_rules'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Can manage Exchange Rules
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox6" class="form-check-label ">
                                            <input type="checkbox" id="checkbox6" name="can_manage_orders" <?php if($row['can_manage_orders'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Can manage Exchange Orders
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox7" class="form-check-label ">
                                            <input type="checkbox" id="checkbox7" name="can_manage_users" <?php if($row['can_manage_users'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Can manage Users
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox8" class="form-check-label ">
                                            <input type="checkbox" id="checkbox8" name="can_manage_reviews" <?php if($row['can_manage_reviews'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Can manage Users Reviews
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox9" class="form-check-label ">
                                            <input type="checkbox" id="checkbox9" name="can_manage_withdrawals" <?php if($row['can_manage_withdrawals'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Can manage Users Withdrawals
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox10" class="form-check-label ">
                                            <input type="checkbox" id="checkbox10" name="can_manage_support_tickets" <?php if($row['can_manage_support_tickets'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Can manage Support Tickets
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox13" class="form-check-label ">
                                            <input type="checkbox" id="checkbox13" name="can_manage_news" <?php if($row['can_manage_news'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Can manage News
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox11" class="form-check-label ">
                                            <input type="checkbox" id="checkbox11" name="can_manage_pages" <?php if($row['can_manage_pages'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Can manage Pages
                                        </label>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <label for="checkbox12" class="form-check-label ">
                                            <input type="checkbox" id="checkbox12" name="can_manage_faq" <?php if($row['can_manage_faq'] == "1") { echo 'checked'; } ?> value="1" class="form-check-input"> Can manage FAQ
                                        </label>
                                    </div>
                                </div>
                            </div> 
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
    $query = $db->query("SELECT * FROM ce_operators WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=operators");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-trash"></i> Delete Operator</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                        $delete = $db->query("DELETE FROM ce_operators WHERE id='$id'");
                        echo success("Operator ($row[username]) was deleted successfully.");
                     } else {
                        echo info("Are you sure you want to delete operator ($row[username])?");
                        echo '<a href="./?a=operators&b=delete&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-trash"></i> Yes, I confirm</a> 
                        <a href="./?a=operators" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
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
                <h4 class="card-title"><i class="fa fa-user-secret"></i> Operators <span class="pull-right"><a href="./?a=operators&b=add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Operator</a></span></h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="25%">Name</th>
                                <th width="20%">Username</th>
                                <th width="20%">Email address</th>
                                <th width="20%">Activity</th>
                                <th width="5%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = $db->query("SELECT * FROM ce_operators ORDER BY id");
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['username']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php $ActivityQ = $db->query("SELECT * FROM ce_operators_activity WHERE oid='$row[id]'"); echo $ActivityQ->num_rows; ?> <a href="./?a=operators&b=activity&id=<?php echo $row['id']; ?>" class="badge badge-primary"><i class="fa fa-search"></i> Browse</a></td>
                                    <td>
                                        <a href="./?a=operators&b=edit&id=<?php echo $row['id']; ?>" class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</a> 
                                        <a href="./?a=operators&b=delete&id=<?php echo $row['id']; ?>" class="badge badge-danger"><i class="fa fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="5">No operators yet.</td></tr>';
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
}
?>