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
                <h4 class="card-title"><i class="fa fa-plus"></i> New Discount Level</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $CEAction = protect($_POST['ce_btn']);
                     if(isset($CEAction) && $CEAction == "new") {
                        $discount_level = protect($_POST['discount_level']);
                        $from_value = protect($_POST['from_value']);
                        $to_value = protect($_POST['to_value']);
                        $discount_percentage = protect($_POST['discount_percentage']);
                        $check = $db->query("SELECT * FROM ce_discount_system WHERE discount_level='$discount_level'");
                        if(empty($discount_level) or empty($from_value) or empty($to_value) or empty($discount_percentage)) {
                            echo error("All fields are required.");
                        } elseif(!is_numeric($discount_level)) {
                            echo error("Please enter discount level with numbers.");
                        } elseif(!is_numeric($from_value)) {
                            echo error("Please enter range with numbers.");
                        } elseif(!is_numeric($to_value)) {
                            echo error("Please enter range with numbers.");
                        } elseif(!is_numeric($discount_percentage)) {
                            echo error("Please enter discount percentage with numbers.");
                        } elseif($check->num_rows>0) {
                            echo error("Discount level <b>$discount_level</b> already exists.");
                        } else {
                            $time = time();
                            $insert = $db->query("INSERT ce_discount_system (discount_level,from_value,to_value,discount_percentage,currency) VALUES ('$discount_level','$from_value','$to_value','$discount_percentage','USD')");
                            echo success("Discount level <b>$discount_level</b> was created successfully.");
                        }
                     }
                     ?>

                     <form action="" method="POST">
                        <div class="form-group">
                            <label>Discount Level</label>
                            <input type="text" class="form-control" name="discount_level">
                        </div>
                        <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Range / From value</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="from_value">
                                    <div class="input-group-append">
                                        <span class="input-group-text">USD</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                        <div class="form-group">
                                <label>Range / To value</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="to_value">
                                    <div class="input-group-append">
                                        <span class="input-group-text">USD</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="form-group">
                                <label>Discount percentage</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="discount_percentage">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
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
    $query = $db->query("SELECT * FROM ce_discount_system WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=discount_system");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-pencil"></i> Edit Discount level</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $CEAction = protect($_POST['ce_btn']);
                     if(isset($CEAction) && $CEAction == "save") {
                        $discount_level = protect($_POST['discount_level']);
                        $from_value = protect($_POST['from_value']);
                        $to_value = protect($_POST['to_value']);
                        $discount_percentage = protect($_POST['discount_percentage']);
                        $check = $db->query("SELECT * FROM ce_discount_system WHERE discount_level='$discount_level'");
                        if(empty($discount_level) or empty($from_value) or empty($to_value) or empty($discount_percentage)) {
                            echo error("All fields are required.");
                        } elseif(!is_numeric($discount_level)) {
                            echo error("Please enter discount level with numbers.");
                        } elseif(!is_numeric($from_value)) {
                            echo error("Please enter range with numbers.");
                        } elseif(!is_numeric($to_value)) {
                            echo error("Please enter range with numbers.");
                        } elseif(!is_numeric($discount_percentage)) {
                            echo error("Please enter discount percentage with numbers.");
                        } elseif($discount_level !== $row['discount_level'] && $check->num_rows>0) {
                            echo error("Discount level <b>$discount_level</b> already exists.");
                        } else {
                            $time = time();
                            $update = $db->query("UPDATE ce_discount_system SET discount_level='$discount_level',from_value='$from_value',to_value='$to_value',discount_percentage='$discount_percentage' WHERE id='$id'");
                            echo success("Discount level <b>$discount_level</b> was created successfully.");
                            $query = $db->query("SELECT * FROM ce_discount_system WHERE id='$id'");
                            $row = $query->fetch_assoc();
                        }
                     }
                     ?>

                    <form action="" method="POST">
                        <div class="form-group">
                            <label>Discount Level</label>
                            <input type="text" class="form-control" name="discount_level" value="<?php echo $row['discount_level']; ?>">
                        </div>
                        <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Range / From value</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="from_value" value="<?php echo $row['from_value']; ?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text">USD</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                        <div class="form-group">
                                <label>Range / To value</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="to_value" value="<?php echo $row['to_value']; ?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text">USD</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="form-group">
                                <label>Discount percentage</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="discount_percentage" value="<?php echo $row['discount_percentage']; ?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
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
    $query = $db->query("SELECT * FROM ce_discount_system WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=discount_system");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-trash"></i> Delete Question</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                        $delete = $db->query("DELETE FROM ce_discount_system WHERE id='$id'");
                        $update = $db->query("UPDATE ce_users SET discount_level='0' WHERE discount_level='$row[discount_level]'");
                        echo success("Discount level ($row[id]) was deleted successfully.");
                     } else {
                        echo info("Are you sure you want to delete discount leve ($row[id])?");
                        echo '<a href="./?a=discount_system&b=delete&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-trash"></i> Yes, I confirm</a> 
                        <a href="./?a=discount_system" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
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
                <h4 class="card-title"><i class="fa fa-percent"></i> Discount System <span class="pull-right"><a href="./?a=discount_system&b=new" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Level</a></span></h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Discount Level</th>
                                <th>Discount range</th>
                                <th>Discount percentage</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = $db->query("SELECT * FROM ce_discount_system ORDER BY discount_level");
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                                    <td><?php echo $row['discount_level']; ?></td>
                                    <td><?php echo $row['from_value']." ".$row['currency']; ?> - <?php echo $row['to_value']." ".$row['currency']; ?></td>
                                    <td><?php echo $row['discount_percentage']; ?>%</td>
                                    <td>
                                        <a href="./?a=discount_system&b=edit&id=<?php echo $row['id']; ?>" class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</a> 
                                        <a href="./?a=discount_system&b=delete&id=<?php echo $row['id']; ?>" class="badge badge-danger"><i class="fa fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="4">No levels yet.</td></tr>';
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