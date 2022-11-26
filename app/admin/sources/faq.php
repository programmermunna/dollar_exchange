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
                <h4 class="card-title"><i class="fa fa-plus"></i> Add Question</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $CEAction = protect($_POST['ce_btn']);
                     if(isset($CEAction) && $CEAction == "new") {
                        $question = protect($_POST['question']);
                        $answer = addslashes($_POST['answer']);
                        if(empty($question) or empty($answer)) {
                            echo error("All fields are required.");
                        } else {
                            $time = time();
                            $insert = $db->query("INSERT ce_faq (question,answer,created,updated) VALUES ('$question','$answer','$time','0')");
                            echo success("Question <b>$question</b> was added successfully.");
                        }
                     }
                     ?>

                     <form action="" method="POST">
                        <div class="form-group">
                            <label>Question</label>
                            <input type="text" class="form-control" name="question">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea id="tinyMceExample" name="answer">
                                
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
    $query = $db->query("SELECT * FROM ce_faq WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=faq");
    }
    $row = $query->fetch_assoc();
    ?>
    <div class="row">
            <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-pencil"></i> Edit Question</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                     <?php
                     $CEAction = protect($_POST['ce_btn']);
                     if(isset($CEAction) && $CEAction == "save") {
                        $question = protect($_POST['question']);
                        $answer = addslashes($_POST['answer']);
                        if(empty($question) or empty($answer)) {
                            echo error("All fields are required.");
                        } else {
                            $time = time();
                            $update = $db->query("UPDATE ce_faq SET question='$question',answer='$answer',updated='$time' WHERE id='$row[id]'");
                            echo success("Your changes was saved successfully");
                            $query = $db->query("SELECT * FROM ce_faq WHERE id='$row[id]'");
                            $row = $query->fetch_assoc();
                        }
                     }
                     ?>

                     <form action="" method="POST">
                        <div class="form-group">
                            <label>Question</label>
                            <input type="text" class="form-control" name="question" value="<?php echo $row['question']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea id="tinyMceExample" name="answer">
                                <?php echo $row['answer']; ?>
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
    $query = $db->query("SELECT * FROM ce_faq WHERE id='$id'");
    if($query->num_rows==0) {
        header("Location: ./?a=faq");
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
                        $delete = $db->query("DELETE FROM ce_faq WHERE id='$id'");
                        echo success("Question ($row[question]) was deleted successfully.");
                     } else {
                        echo info("Are you sure you want to delete question ($row[question])?");
                        echo '<a href="./?a=faq&b=delete&id='.$id.'&confirmed=1" class="btn btn-success"><i class="fa fa-trash"></i> Yes, I confirm</a> 
                        <a href="./?a=faq" class="btn btn-danger"><i class="fa fa-times"></i> No</a>';
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
                <h4 class="card-title"><i class="fa fa-question-circle"></i> FAQ <span class="pull-right"><a href="./?a=faq&b=add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Question</a></span></h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="45%">Question</th>
                                <th width="25%">Created on</th>
                                <th width="25%">Updated on</th>
                                <th width="5%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = $db->query("SELECT * FROM ce_faq ORDER BY id");
                            if($query->num_rows>0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                                    <td><?php echo $row['question']; ?></td>
                                    <td><?php echo date("d/m/Y H:i",$row['created']); ?></td>
                                    <td><?php if($row['updated']>0) { echo date("d/m/Y H:i",$row['updated']); } ?></td>
                                    <td>
                                        <a href="./?a=faq&b=edit&id=<?php echo $row['id']; ?>" class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</a> 
                                        <a href="./?a=faq&b=delete&id=<?php echo $row['id']; ?>" class="badge badge-danger"><i class="fa fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="4">No faq yet.</td></tr>';
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