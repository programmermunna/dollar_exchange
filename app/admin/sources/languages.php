<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$b = protect($_GET['b']);
if($b == "set_default") {
	$id = protect($_GET['id']);
	if(!file_exists("../languages/$id.php")) {
		header("Location: ./?a=languages");
	}
    ?>
     <div class="row">
             <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-check"></i> Change Default Language</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                 <?php
					$update = $db->query("UPDATE ce_settings SET default_language='$id'");
					echo success("New default language is <b>$id</b>.");
					?>
                  
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "new") {
    ?>
<div class="row">
             <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-plus"></i> New Language</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                 
                <?php
                $CEAction = protect($_POST['ce_btn']);
					if(isset($CEAction) && $CEAction == "new") {
						$lang_name = protect($_POST['lang_name']);
						
						if(empty($lang_name)) { echo error("Please enter language name."); }
						elseif(!isValidUsername($lang_name)) { echo error("Please enter a valid language name. Use latin characters. For example: English"); }
						elseif(file_exists("../languages/$lang_name.php")) { echo error("Language <b>$lang_name</b> already exists."); }
						else {
						$contents = '<?php
// '.$id.' Language for CryptoExchanger PHP Script
// Last update: '.date("d/m/Y H:i").'
$lang = array();
';
						$key = protect($_POST['key']);
						foreach($_POST['key'] as $k=>$v) {
							$contents .= '$lang["'.$k.'"] = "'.$v.'";
';
						}
						$contents .= '?>';
						$update = file_put_contents("../languages/$lang_name.php",$contents);
						if($update) {
							echo success("Language <b>$lang_name</b> was added successfully.");
						} else {
							echo error("Please set chmod 777 of file <b>languages</b> directory.");
						}
						}
					}
                    ?>
                    
                <form action="" method="POST">
						<div class="form-group">
							<label>Language name</label>
							<input type="text" class="form-control" name="lang_name">
						</div>
						
						<table class="table table-bordered">
							<thead>
								<tr>
									<th width="30%">Key</th>
									<th width="70%">Value</th>
								</tr>
							</thead>
							<tbody>
							<?php
							include("temp/English_Language.php");
							foreach($lang as $k=>$v) {
								echo '<tr>
										<td><b>'.$k.'</b></td>
										<td><input type="text" class="form-control" name="key['.$k.']" value="'.$v.'"></td>
									</tr>';
							}
							?>	
							</tbody>
                        </table>
                        <br>
						<button type="submit" class="btn btn-primary" name="ce_btn" value="new"><i class="fa fa-plus"></i> Create</button>
					</form>
                  
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "edit") {
    $id = protect($_GET['id']);
	if(!file_exists("../languages/$id.php")) {
		header("Location: ./?a=languages");
    }
    ?>
<div class="row">
             <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-pencil"></i> Edit Language</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                 
                <?php
                    $CEAction = protect($_POST['ce_btn']);
					if(isset($CEAction) && $CEAction == "save") {
						$contents = '<?php
// '.$id.' Language for CryptoExchanger PHP Script
// Last update: '.date("d/m/Y H:i").'
$lang = array();
';
						$key = protect($_POST['key']);
						foreach($_POST['key'] as $k=>$v) {
							$contents .= '$lang["'.$k.'"] = "'.$v.'";
';
						}
						$contents .= '?>';
						$update = file_put_contents("../languages/$id.php",$contents);
						if($update) {
							echo success("Your changes was saved successfully.");
						} else {
							echo error("Please set chmod 777 of file <b>languages</b> directory.");
						}
					}
					?>
				
					<form action="" method="POST">
                        <div class="form-group">
							<label>Language name</label>
							<input type="text" class="form-control" disname="lang_name" disabled value="<?php echo $id; ?>">
						</div>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th width="30%">Key</th>
									<th width="70%">Value</th>
								</tr>
							</thead>
							<tbody>
							<?php
							include("../languages/$id.php");
							foreach($lang as $k=>$v) {
								echo '<tr>
										<td><b>'.$k.'</b></td>
										<td><input type="text" class="form-control " name="key['.$k.']" value="'.$v.'"></td>
									</tr>';
							}
							?>	
							</tbody>
                        </table>
                        <br>
						<button type="submit" class="btn btn-primary" name="ce_btn" value="save"><i class="fa fa-check"></i> Save changes</button>
					</form>
                  
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "delete") {
    $id = protect($_GET['id']);
	if(!file_exists("../languages/$id.php")) {
		header("Location: ./?a=languages");
	}
    ?>
<div class="row">
             <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-trash"></i> Delete Language</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">

                <?php
			if($settings['default_language'] == $id) { 
				echo error("$id is default language. Please change it and then delete it.");
			} else {	
				@unlink("../languages/$id.php");
				echo success("Language <b>$id</b> was deleted successfully.");
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
                <h4 class="card-title"><i class="fa fa-globe"></i> Languages <span class="pull-right"><a href="./?a=languages&b=new" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Language</a></span></h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                              <div class="card">
                <div class="card-body">
                
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="75%">Language name</th>
                            <th width="25%">Default</th>
                            <th width="5%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($handle = opendir('../languages')) {
                            while (false !== ($file = readdir($handle)))
                            {
                                if ($file != "." && $file != ".." && $file != "index.php" && strtolower(substr($file, strrpos($file, '.') + 1)) == 'php')
                                {
                                    $lang = str_ireplace(".php","",$file);
                                    if($settings['default_language'] == $lang) { $sel ='<i class="fa fa-check"></i>'; } else { $sel = '<i class="fa fa-times"></i> (<a href="./?a=languages&b=set_default&id='.$lang.'">Set default</a>)'; }
                                    echo '<tr>
                                            <td>'.$lang.'</td>
                                            <td>'.$sel.'</td>
                                            <td>
                                                <a href="./?a=languages&b=edit&id='.$lang.'" title="Edit" class="badge badge-primary"><i class="fa fa-pencil"></i> Edit</a> 
                                                <a href="./?a=languages&b=delete&id='.$lang.'" title="Delete" class="badge badge-danger"><i class="fa fa-trash"></i> Delete</a>
                                            </td>
                                        </tr>';
                                }
                            }
                            closedir($handle);
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