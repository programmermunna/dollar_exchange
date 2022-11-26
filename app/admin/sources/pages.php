<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$b = protect($_GET['b']);
if($b == "add") {
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-plus"></i> Add Page</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $CEAction = protect($_POST['ce_btn']);
                     if(isset($CEAction) && $CEAction == "new") {
                        $title = protect($_POST['title']);
                        $prefix = protect($_POST['prefix']);
                        $content = addslashes($_POST['content']);
                        $check = $db->query("SELECT * FROM ce_pages WHERE prefix='$prefix'");
                        if(empty($title) or empty($content) or empty($prefix)) {
                            echo error("All fields are required.");
                        } elseif(!isValidUsername($prefix)) {
                            echo error("Please enter valid prefix.");  
                        } elseif($check->num_rows>0) {
                            echo error("Page with prefix <b>$prefix</b> already exists.");
                        } else {
                            $time = time();
                            $insert = $db->query("INSERT ce_pages (title,content,created,updated,prefix) VALUES ('$title','$content','$time','0','$prefix')");
                            echo success("Page <b>$title</b> was added successfully.");
                        }
                     }
                     ?>

                     <form action="" method="POST">
                     <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title">
                        </div>
                        <div class="form-group">
                            <label>Prefix</label>
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text"><?php echo $settings['url']; ?>page/</span>
                                </div>
                                <input type="text" class="form-control" name="prefix">
                            </div>
                         </div>  
                        <div class="form-group">
                            <label>Content</label>
                            <textarea id="tinyMceExample" name="content">
                            </textarea>
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
    $query = $db->query("SELECT * FROM ce_pages WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=pages");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-pencil"></i> Edit Page</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $CEAction = protect($_POST['ce_btn']);
                     if(isset($CEAction) && $CEAction == "save") {
                        $title = protect($_POST['title']);
                        $prefix = protect($_POST['prefix']);
                        $content = addslashes($_POST['content']);
                        $check = $db->query("SELECT * FROM ce_pages WHERE prefix='$prefix'");
                        if(empty($title) or empty($content) or empty($prefix)) {
                            echo error("All fields are required.");
                        } elseif(!isValidUsername($prefix)) {
                            echo error("Please enter valid prefix.");  
                        } elseif($prefix !== $row['prefix'] && $check->num_rows>0) {
                            echo error("Page with prefix <b>$prefix</b> already exists.");
                        } else {
                            $time = time();
                            $update = $db->query("UPDATE ce_pages SET title='$title',content='$content',prefix='$prefix',updated='$time' WHERE id='$row[id]'");
                            echo success("Your changes was saved successfully");
                            $query = $db->query("SELECT * FROM ce_pages WHERE id='$row[id]'");
                            $row = $query->fetch_assoc();
                        }
                     }
                     ?>

                     <form action="" method="POST">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" value="<?php echo $row['title']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Prefix</label>
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text"><?php echo $settings['url']; ?>page/</span>
                                </div>
                                <input type="text" class="form-control" name="prefix" value="<?php echo $row['prefix']; ?>">
                            </div>
                         </div>  
                        <div class="form-group">
                            <label>Content</label>
                            <textarea id="tinyMceExample" name="content">
                                <?php echo $row['content']; ?>
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
    $query = $db->query("SELECT * FROM ce_pages WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=pages");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-trash"></i> Delete Page</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                        $delete = $db->query("DELETE FROM ce_pages WHERE id='$id'");
                        echo success("Page ($row[title]) was deleted successfully.");
                     } else {
                        echo info("Are you sure you want to delete page ($row[title])?");
                        echo '<a href="./?a=pages&b=delete&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-trash"></i> Yes, I confirm</a> 
                        <a href="./?a=pages" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
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
                <h4 class="card-title"><i class="fa fa-file-o"></i> Pages <span class="pull-right"><a href="./?a=pages&b=add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Page</a></span></h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="45%">Title</th>
                                <th width="10%">Prefix</th>
                                <th width="20%">Created on</th>
                                <th width="20%">Updated on</th>
                                <th width="5%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = $db->query("SELECT * FROM ce_pages ORDER BY id");
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                                    <td><a href="<?php echo $settings['url']; ?>page/<?php echo $row['prefix']; ?>" target="_blank"><?php echo $row['title']; ?></a></td>
                                    <td><?php echo $row['prefix']; ?></td>
                                    <td><?php echo date("d/m/Y H:i",$row['created']); ?></td>
                                    <td><?php if($row['updated']>0) { echo date("d/m/Y H:i",$row['updated']); } ?></td>
                                    <td>
                                        <a href="./?a=pages&b=edit&id=<?php echo $row['id']; ?>" class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</a> 
                                        <a href="./?a=pages&b=delete&id=<?php echo $row['id']; ?>" class="badge badge-danger"><i class="fa fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="4">No pages yet.</td></tr>';
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