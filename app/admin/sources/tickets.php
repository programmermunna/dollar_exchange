
<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$b = protect($_GET['b']);
if($b == "view") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_tickets WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=tickets");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-support"></i> Support Tickets <small>Ticket ID: <?php echo $row['id']; ?>, Ticket Hash: <?php echo $row['hash']; ?></small></h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                    <?php
                    $CEAction = protect($_POST['ce_btn']);
                    if(isset($CEAction) && $CEAction == "post") {
                        $message = addslashes($_POST['message']);
                        if(empty($message)) {
                            echo error("Please enter message.");
                        } else {
                            $time = time();
                            if(empty($row['served_by'])) {
                                $update = $db->query("UPDATE ce_tickets SET served_level='1',served_by='$_SESSION[ce_admin_uid]' WHERE id='$row[id]'");
                            }
                            $update = $db->query("UPDATE ce_tickets SET updated='$time',status='8' WHERE id='$row[id]'");
                            $insert = $db->query("INSERT ce_tickets_messages (tid,message,author,created) VALUES ('$row[id]','$message','0','$time')");
                            echo success("Your reply was added to ticket #$id.");
                        }
                    }
                    ?>
                    <?php if($row['status'] !== "1" && $row['status'] !== "2") { ?>
                    <span class="pull-right">
                        Mark as: <a href="./?a=tickets&b=solve&id=<?php echo $row['id']; ?>" class="btn btn-success btn-xs">Solved</a> <a href="./?a=tickets&b=close&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-xs">Closed</a>
                    </span>
                    <br><br>
                    <?php } ?>
                    <div style="width:100%;height:500px;overflow-y:scroll;overflow-x:hidden;border: 1px solid #c1c1c1;padding:10px;">
                        <div class="table-responsive">
                            <table class="table table-striped">
                            <tbody>
                            <?php
                            $MsgQuery = $db->query("SELECT * FROM ce_tickets_messages WHERE tid='$row[id]' ORDER BY id DESC");
                            if($MsgQuery->num_rows>0) {
                                while($msg = $MsgQuery->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td width="30%">
                                            <?php if($msg['author']>0) { ?>
                                                <a href="./?a=users&b=edit&id=<?php echo $msg['author']; ?>"><?php if(idinfo($msg['author'],"first_name")) { echo idinfo($msg['author'],"first_name")." ".idinfo($msg['author'],"last_name"); } else { echo idinfo($msg['author'],"email"); } ?></a>
                                            <?php } else { ?>
                                                Support (<?php if($row['served_level'] == "1") { echo idinfo($row['served_by'],"username"); } else { echo opinfo($row['served_by'],"username"); } ?>)
                                            <?php } ?>:<br/>
                                            <small><?php echo date("d/m/Y H:i",$msg['created']); ?></small>
                                        </td>
                                        <td width="70%">
                                            <?php echo $msg['message']; ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if($row['status'] !== "1" && $row['status'] !== "2") { ?>
                    <br><br>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label>Add reply</label>
                            <textarea id="tinyMceExample" name="message">

                            </textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" name="ce_btn" value="post"><i class="fa fa-plus"></i> Post</button>
                    </form>
                    <?php } ?>
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "solve") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_tickets WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=tickets");
    }
    $row = $query->fetch_assoc();
    $time = time();
    $update = $db->query("UPDATE ce_tickets SET updated='$time',status='2' WHERE id='$row[id]'");
    if(empty($served_by)) {
        $update = $db->query("UPDATE ce_tickets SET served_by='$_SESSION[ce_admin_uid]' WHERE id='$row[id]'");  
    }
    header("Location: ./?a=tickets&b=view&id=$id");
} elseif($b == "close") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_tickets WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=tickets");
    }
    $row = $query->fetch_assoc();
    $time = time();
    $update = $db->query("UPDATE ce_tickets SET updated='$time',status='1' WHERE id='$row[id]'");
    if(empty($served_by)) {
        $update = $db->query("UPDATE ce_tickets SET served_by='$_SESSION[ce_admin_uid]' WHERE id='$row[id]'");  
    }
    header("Location: ./?a=tickets&b=view&id=$id");
} else {
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-support"></i> Support Tickets</h4>
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
                            $searching = 0;
                            $CEAction = protect($_POST['ce_btn']);
                            if(isset($CEAction) && $CEAction == "search") {
                                $subject = protect($_POST['subject']);
                                $hash = protect($_POST['hash']);
                                $email = protect($_POST['email']);
                                $id = protect($_POST['id']);
                                $searcharr = array();
                                if(isValidEmail($email) && !empty($email)) {
                                    $UserQuery = $db->query("SELECT * FROM ce_users WHERE email='$email'");
                                    if($UserQuery->num_rows>0) {
                                        $user = $UserQuery->fetch_assoc();
                                        $uid = $user['id'];
                                        $searcharr[] = "uid='$uid'";
                                    }
                                }
                                if(!empty($subject)) {
                                    $searcharr[] = "title LIKE '%$subject%'";
                                }
                                if(!empty($hash)) {
                                    $searcharr[] = "hash='$hash'";
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
                                $query = $db->query("SELECT * FROM ce_tickets WHERE $filters ORDER BY status DESC, id DESC");
                            } else {
                                $statement = "ce_tickets";
                                $query = $db->query("SELECT * FROM {$statement} ORDER BY status DESC, id DESC LIMIT {$startpoint} , {$limit}");
                            }
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><a href="./?a=users&b=edit&id=<?php echo $row['uid']; ?>"><?php if(idinfo($row['uid'],"first_name")) { echo idinfo($row['uid'],"first_name")." ".idinfo($row['uid'],"last_name"); } else { echo idinfo($row['uid'],"email"); } ?></a></td>
                                        <td><?php echo $row['title']; ?></td>
                                        <td><?php echo date("d/m/Y H:i",$row['created']); ?></td>
                                        <td><?php if($row['updated']) { echo date("d/m/Y H:i",$row['updated']); } else { echo 'n/a'; } ?></td>
                                        <td><?php if($row['served_by']) { if($row['served_level'] == "1") { echo idinfo($row['served_by'],"username"); } else { echo opinfo($row['served_by'],"username"); } } else { echo 'n/a'; } ?></td>
                                        <td>
                                            <?php
                                            if($row['status'] == "9") {
                                                $status = '<span class="badge badge-success">New reply</span>';
                                                $class = 'table-info';
                                            } elseif($row['status'] == "8") {
                                                $status = '<span class="badge badge-info">Awaiting reply</span>';
                                                $class = 'table-danger';
                                            } elseif($row['status'] == "2") {
                                                $status = '<span class="badge badge-success">Solved</span>';
                                            } elseif($row['status'] == "1") { 
                                                $status = '<span class="badge badge-danger">Closed</span>';
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
                                if($searching == "1") {
                                    echo '<tr><td colspan="8">No results were found for the criteria you set.</td></tr>';
                                } else {
                                    echo '<tr><td colspan="8">No support tickets yet.</td></tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                    <?php
                    if($searching == "0") {
                        $ver = "./?a=tickets";
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
                                    <label>Subject</label>
                                    <input type="text" class="form-control" name="subject" value="<?php if(isset($_POST['subject'])) { echo protect($_POST['subject']); } ?>">
                                </div>
                                <div class="form-group">
                                    <label>Ticket ID</label>
                                    <input type="text" class="form-control" name="id" value="<?php if(isset($_POST['id'])) { echo protect($_POST['id']); } ?>">
                                </div>
                                <div class="form-group">
                                    <label>Ticket Hash</label>
                                    <input type="text" class="form-control" name="hash" value="<?php if(isset($_POST['hash'])) { echo protect($_POST['hash']); } ?>">
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