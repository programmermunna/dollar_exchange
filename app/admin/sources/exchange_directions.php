<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$b = protect($_GET['b']);
if($b == "edit") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_gateways WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=exchange_directions");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-pencil"></i> Edit Exchange Direction</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                    <?php
                    $CEAction = protect($_POST['ce_btn']);
                    if(isset($CEAction) && $CEAction == "save") {
                        $imp = implode(",",$_POST['directions']);
                        $update = $db->query("UPDATE ce_gateways_directions SET directions='$imp' WHERE gateway_id='$row[id]'");
                        $query = $db->query("SELECT * FROM ce_gateways WHERE id='$row[id]'");
                        $row = $query->fetch_assoc();
                        echo success("Your changes was saved successfully.");
                    }
                    ?>
                     <form action="" method="POST">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Send</th>
                                        <th>Allow</th>
                                        <th>Receive</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $DirQuery = $db->query("SELECT * FROM ce_gateways_directions WHERE gateway_id='$row[id]'");
                                    $dir = $DirQuery->fetch_assoc();
                                    $arr = explode(",",$dir['directions']);
                                    $getquery = $db->query("SELECT * FROM ce_gateways ORDER BY id");
                                    if($getquery->num_rows>0) {
                                        while($get = $getquery->fetch_assoc()) {
                                            if(in_array($get['id'],$arr)) { $ch = 'checked="checked"'; } else { $ch = ''; }
                                            echo '<tr>
                                                    <td>'.$row[name].' '.$row[currency].'</td>
                                                    <td><div class="form-check">
                                                    <div class="checkbox">
                                                        <label for="checkbox'.$i.'" class="form-check-label ">
                                                            <input type="checkbox" id="checkbox'.$i.'" '.$ch.' name="directions[]" value="'.$get[id].'"> 
                                                        </label>
                                                    </div>
                                                </div></td>
                                                    <td>'.$get[name].' '.$get[currency].'</td>
                                                </tr>';
                                                $i++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary" name="ce_btn" value="save">Save Changes</button>
                     </form>
                </div>
              </div>
            </div>
        </div>
    <?php
} else {
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="ti-direction-alt"></i> Exchange Directions</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Gateway</th>
                                <th>Directions</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = $db->query("SELECT * FROM ce_gateways ORDER BY id");
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                                    <td><?php echo $row['name']." ".$row['currency']; ?></td>
                                    <td>
                                        <?php
                                        $DirQuery = $db->query("SELECT * FROM ce_gateways_directions WHERE gateway_id='$row[id]'");
                                        $dir = $DirQuery->fetch_assoc();
                                        $expl = explode(",",$dir['directions']);
                                        $list = array();
                                        foreach($expl as $k=>$v) {
                                            $list[] = gatewayinfo($v,"name")." ".gatewayinfo($v,"currency"); 
                                        }
                                        echo implode(", ",$list);
                                        ?>
                                    </td>
                                    <td>
                                        <a href="./?a=exchange_directions&b=edit&id=<?php echo $row['id']; ?>" class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</a> 
                                    </td>
                                </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="3">No gateways yet.</td></tr>';
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