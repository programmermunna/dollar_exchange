<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$b = protect($_GET['b']);
if($b == "set_default") {
    $id = protect($_GET['id']);
	$id = str_ireplace("../","",$id);
	$id = str_ireplace("./","",$id);
	if(!is_dir("../templates/$id")) {
		header("Location: ./?a=templates");
	}
    ?>
<div class="row">
        <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-check"></i> Change Default Template</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                
                <?php
					$update = $db->query("UPDATE ce_settings SET default_template='$id'");
					echo success("New default template is <b>$id</b>.");
					?>

                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "edit") {
    $id = protect($_GET['id']);
	$id = str_ireplace("../","",$id);
	$id = str_ireplace("./","",$id);
	if(!is_dir("../templates/$id")) {
		header("Location: ./?a=templates");
	}
    ?>
<div class="row">
        <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-pencil"></i> Edit Template <small><?php echo $id; ?></small></h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  
                <table class="table table-stripped">
						<tbody>
							<?php
							if(isset($_GET['sub'])) {	
								$sub = protect($_GET['sub']);
								$sub = str_ireplace("../","",$sub);
								$sub = str_ireplace("./","",$sub);
								$dir = '../templates/'.$id.'/'.$sub;
								if(!is_dir($dir)) {
									header("Location: ./?a=templates");
								}
							} else {
								$dir = '../templates/'.$id;
							}
							if("../templates/$id" !== $dir) {
								echo '<tr>
										<td colspan="2"><a href="javascript:void(0);" onclick="javascript:history.go(-1);"><i class="fa fa-arrow-circle-left"></i> Back</a></td>
									</tr>';
							}
							$files = scandir($dir);
							foreach($files as $folder) {
								if($folder !== "." && $folder !== "..") {
									if(is_dir($dir.'/'.$folder)) {
										if($sub) { $prefix = $sub.'/'; } else { $prefix = ''; }
										echo '<tr>
												<td width="5%"><i class="fa fa-folder-o"></i></td>
												<td width="95%"><a href="./?a=templates&b=edit&id='.$id.'&sub='.$prefix.$folder.'">'.$folder.'</a></td>
											</tr>';
									} else {
									}
								}
							}
							foreach($files as $file) {
								if($file !== "." && $file !== "..") {
									if(is_file($dir.'/'.$file)) {
										if($sub) { $prefix = $sub.'/'; } else { $prefix = ''; }
										$ext = pathinfo("$dir/$file", PATHINFO_EXTENSION);
										if($ext == 'css' or $ext == 'tpl' or $ext == 'js' or $ext == 'html') {
											echo '<tr>
													<td width="5%"><i class="fa fa-file-o"></i></td>
													<td width="95%"><a href="./?a=templates&b=edit_file&id='.$id.'&file='.$prefix.$file.'">'.$file.'</a></td>
												</tr>';
										}
									} else {
									}
								}
							}
							?>
						</tbody>
					</table>
                </div>
              </div>
            </div>
        </div>
    <?php
} elseif($b == "edit_file") {
    $id = protect($_GET['id']);
	$id = str_ireplace("../","",$id);
	$id = str_ireplace("./","",$id);
	$file = protect($_GET['file']);
	$file = str_ireplace("../","",$file);
	$file = str_ireplace("./","",$file);
	if(!is_dir("../templates/$id")) {
		header("Location: ./?a=templates");
	}
	if(!is_file("../templates/$id/$file")) {
		header("Location: ./?a=templates");
	}
	$ext = pathinfo("../templates/$id/$file", PATHINFO_EXTENSION);
	if($ext != 'css' && $ext != 'tpl' && $ext != 'js' && $ext != 'html') {
		header("Location: ./?a=templates");
	}
	if($ext == 'css') { $type = 'css'; } elseif($ext == 'tpl') { $type = 'html'; } elseif($ext == "js") { $type = 'js'; } else { $type = 'html'; }
    ?>
    <div class="row">
        <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="fa fa-pencil"></i> Edit File <small><?php echo $id.'/'.$file; ?></small></h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  
                <?php
					if(isset($_POST['btn_save'])) {
						$contents = str_ireplace("<?php","",$_POST['contents']);
						$contents = str_ireplace("?>","",$contents);
						$update = file_put_contents("../templates/$id/$file",$contents);
						if($update) {
							echo success("Your changes was saved successfully.");
						} else {
							echo error("Please set chmod 777 of folder <b>templates/$id</b>.");
						}
					}
					?>
					<table class="table table-stripped">
						<tbody>
							<tr>
								<td><a href="./?a=templates&b=edit&id=<?php echo $id; ?>"><i class="fa fa-arrow-circle-left"></i> Back</a></td>
							</tr>
							<tr>
								<td>
								<script language="Javascript" type="text/javascript" src="assets/edit_area/edit_area_full.js"></script>
								<script language="Javascript" type="text/javascript">
									// initialisation
									editAreaLoader.init({
										id: "example_1"	// id of the textarea to transform		
										,start_highlight: true	// if start with highlight
										,allow_resize: "both"
										,allow_toggle: false
										,word_wrap: true
										,language: "en"
										,syntax: "<?php echo $type; ?>"	
									});
								</script>
								<form action="" method="POST">
								<textarea id="example_1" style="height: 350px; width: 100%;" name="contents">
									<?php
									$contents = file_get_contents("../templates/$id/$file");
									echo $contents;
									?>
								</textarea>
								<br><br>
								<?php echo info("<b>Important:</b> tags with [@...] and [#lang_...] was content from system, do not change names becouse will not display system content."); ?>
							
								<button type="submit" class="btn btn-primary" name="btn_save"><i class="fa fa-check"></i> Save Changes</button>
								</form>
								</td>
							</tr>
						</tbody>
					</table>
                </div>
              </div>
            </div>
        </div>
    <?php
} else {
    ?>
    <div class="row">
        <div class="col-md-12 col-lg-12">
                <h4 class="card-title"><i class="ti-palette "></i> Templates</h4>
                <br><br>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
            
                        <?php
                            $templates = glob("../templates/*");
                            foreach($templates as $tpln => $tplv) {
                                $tpl = str_ireplace("../templates/","",$tplv);
                                if($settings['default_template'] == $tpl) { $sel = 'selected'; } else { $sel = ''; }
                                if($tpl !== "index.php") {
                                ?>
                                <div class="col-sm-6 col-md-4 col-lg-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="thumbnail">
                                            <img src="../templates/<?php echo $tpl; ?>/preview.png" class="img-responsive" style="max-width:300px;" alt="...">
                                            <div class="caption">
                                                <br/>
                                                <h3><?php echo $tpl; ?></h3>
                                                <br>
                                                <p><?php if($tpl !== "Email_Templates") { if($settings['default_template'] == $tpl) { echo '<a href="#" class="btn btn-success" role="button"><i class="fa fa-check"></i> Default</a> '; } else { echo '<a href="./?a=templates&b=set_default&id='.$tpl.'" class="btn btn-primary" role="button">Set Default</a> '; } } ?><a href="./?a=templates&b=edit&id=<?php echo $tpl; ?>" class="btn btn-default" role="button"><i class="fa fa-edit"></i> Edit</a></p>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                }
                            }
                            ?>
               
            </div>
        </div>
    <?php
}
?>