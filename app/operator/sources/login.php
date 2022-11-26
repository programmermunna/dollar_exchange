<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php echo $settings['name']; ?> Admin Panel</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="./assets/vendors/iconfonts/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="./assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="./assets/vendors/css/vendor.bundle.addons.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="./assets/css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="./assets/images/favicon.png" />
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <img src="./assets/images/site_logo_2.png" alt="logo">
              </div>
              <h4>Hello! let's get started</h4>
              <h6 class="font-weight-light">Sign in to continue as operator.</h6>
              <?php
              $FormBTN = protect($_POST['ce_login']);
              if($FormBTN == "operator") {
                  $username = protect($_POST['username']);
                  $password = protect($_POST['password']);
                  $checkUser = $db->query("SELECT * FROM ce_operators WHERE username='$username'");
                  if(empty($username)) {
                      echo aerror("Please enter your username.");
                  } else {
                      $login = $checkUser->fetch_assoc();
                      if(password_verify($password, $login['password'])) {
                        if($login['can_login'] == "1") {
                            $_SESSION['ce_operator_uid'] = $login['id'];
                            header("Location: ./");
                        } else {
                            echo aerror("We cannot complete your login request, your account no have rights to access this area.");
                        }
                      } else {
                        echo aerror("Your password is incorrect.");
                      }
                  }
              }
              ?>
              <form class="pt-3" action="" method="POST">
                <div class="form-group">
                  <input type="text" name="username" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Username">
                </div>
                <div class="form-group">
                  <input type="password" name="password" class="form-control form-control-lg" id="exampleInputPassword1" placeholder="Password">
                </div>
                <div class="mt-3">
                  <button type="submit" name="ce_login" value="operator" class="btn btn-block btn-success btn-lg font-weight-medium auth-form-btn">LOGIN</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="./assets/vendors/js/vendor.bundle.base.js"></script>
  <script src="./assets/vendors/js/vendor.bundle.addons.js"></script>
  <!-- endinject -->
  <!-- inject:js -->
  <script src="./assets/js/off-canvas.js"></script>
  <script src="./assets/js/hoverable-collapse.js"></script>
  <script src="./assets/js/template.js"></script>
  <script src="./assets/js/settings.js"></script>
  <script src="./assets/js/todolist.js"></script>
  <!-- endinject -->
</body>

</html>
