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
                <h4 class="card-title"><i class="fa fa-plus"></i> Add Post</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $CEAction = protect($_POST['ce_btn']);
                     if(isset($CEAction) && $CEAction == "new") {
                        $title = protect($_POST['title']);
                        $content = addslashes($_POST['content']);
                        $check = $db->query("SELECT * FROM ce_news WHERE title='$title'");
                        if(empty($title) or empty($content)) {
                            echo error("All fields are required.");
                        } elseif($check->num_rows>0) {
                            echo error("Post with title <b>$title</b> already exists.");
                        } else {
                            $time = time();
                            $insert = $db->query("INSERT ce_news (title,content,created,updated,author) VALUES ('$title','$content','$time','0','$_SESSION[ce_admin_uid]')");
                            echo success("Post <b>$title</b> was published successfully.");
                        }
                     }
                     ?>

                     <form action="" method="POST">
                     <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title">
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
    $query = $db->query("SELECT * FROM ce_news WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=news");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-pencil"></i> Edit Post</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $CEAction = protect($_POST['ce_btn']);
                     if(isset($CEAction) && $CEAction == "save") {
                        $title = protect($_POST['title']);
                        $content = addslashes($_POST['content']);
                        $check = $db->query("SELECT * FROM ce_news WHERE title='$title'");
                        if(empty($title) or empty($content)) {
                            echo error("All fields are required.");
                        } elseif($title !== $row['title'] && $check->num_rows>0) {
                            echo error("Post with title <b>$title</b> already exists.");
                        } else {
                            $time = time();
                            $update = $db->query("UPDATE ce_news SET title='$title',content='$content',updated='$time' WHERE id='$row[id]'");
                            echo success("Your changes was saved successfully");
                            $query = $db->query("SELECT * FROM ce_news WHERE id='$row[id]'");
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
    $query = $db->query("SELECT * FROM ce_news WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=news");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-trash"></i> Delete Post</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $confirmed = protect($_GET['confirmed']);
                     if(isset($confirmed) && $confirmed == "1") {
                        $delete = $db->query("DELETE FROM ce_news WHERE id='$id'");
                        echo success("Post ($row[title]) was deleted successfully.");    
                     } else {
                        echo info("Are you sure you want to delete post ($row[title])?");
                        echo '<a href="./?a=news&b=delete&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-trash"></i> Yes, I confirm</a> 
                        <a href="./?a=news" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
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
                <h4 class="card-title"><i class="fa fa-newspaper-o"></i> News <span class="pull-right"><a href="./?a=news&b=add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Post</a></span></h4>
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
                                <th width="10%">Author</th>
                                <th width="20%">Created on</th>
                                <th width="20%">Updated on</th>
                                <th width="5%">Action</th>
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
                            $statement = "ce_news";
                            $query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                                    <td><a href="<?php echo $settings['url']; ?>news/view/<?php echo $row['id']; ?>" target="_blank"><?php echo $row['title']; ?></a></td>
                                    <td><a href="./?a=users&b=edit&id=<?php echo $row['author']; ?>"><?php echo idinfo($row['author'],"username"); ?></a></td>
                                    <td><?php echo date("d/m/Y H:i",$row['created']); ?></td>
                                    <td><?php if($row['updated']>0) { echo date("d/m/Y H:i",$row['updated']); } ?></td>
                                    <td>
                                        <a href="./?a=news&b=edit&id=<?php echo $row['id']; ?>" class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</a> 
                                        <a href="./?a=news&b=delete&id=<?php echo $row['id']; ?>" class="badge badge-danger"><i class="fa fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="5">No news yet.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    </div>
                    <?php
                    $ver = "./?a=news";
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