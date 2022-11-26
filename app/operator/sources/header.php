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
  <title><?php echo $settings['name']; ?> Atopwallet Operator</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="./assets/vendors/iconfonts/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="./assets/vendors/iconfonts/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="./assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="./assets/vendors/css/vendor.bundle.addons.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="./assets/css/vertical-layout-light/style.css">
  
  <link rel="stylesheet" href="./assets/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="./assets/images/favicon.png" />
</head>
<body class="sidebar-dark">
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row navbar-dark">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="./"><img src="./assets/images/site_logo_2.png" class="mr-2" style="width:100px;height:48px;margin:0 auto;" alt="logo"/></a>
        <a class="navbar-brand brand-logo-mini" href="./"><img src="./assets/images/site_logo_mini.png" alt="logo"/></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="ti-layout-grid2"></span>
        </button>
        <ul class="navbar-nav navbar-nav-right">
          
          <li class="nav-item nav-profile ">
            <a  class="nav-link" href="./?a=logout" >
              <i class="fa fa-power-off"></i> Logout
            </a>
           
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="ti-layout-grid2"></span>
        </button>
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
      <div class="theme-setting-wrapper">
        <div id="settings-trigger"><i class="ti-settings"></i></div>
        <div id="theme-settings" class="settings-panel">
          <i class="settings-close ti-close"></i>
          <p class="settings-heading">SIDEBAR SKINS</p>
          <div class="sidebar-bg-options selected" id="sidebar-light-theme"><div class="img-ss rounded-circle bg-light border mr-3"></div>Light</div>
          <div class="sidebar-bg-options" id="sidebar-dark-theme"><div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark</div>
          <p class="settings-heading mt-2">HEADER SKINS</p>
          <div class="color-tiles mx-0 px-4">
            <div class="tiles success"></div>
            <div class="tiles warning"></div>
            <div class="tiles danger"></div>
            <div class="tiles info"></div>
            <div class="tiles dark"></div>
            <div class="tiles default"></div>
          </div>
        </div>
      </div>
      <div id="right-sidebar" class="settings-panel">
        <i class="settings-close ti-close"></i>
        <ul class="nav nav-tabs border-top" id="setting-panel" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab" aria-controls="todo-section" aria-expanded="true">TO DO LIST</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="chats-tab" data-toggle="tab" href="#chats-section" role="tab" aria-controls="chats-section">CHATS</a>
          </li>
        </ul>
        <div class="tab-content" id="setting-content">
          <div class="tab-pane fade show active scroll-wrapper" id="todo-section" role="tabpanel" aria-labelledby="todo-section">
            <div class="add-items d-flex px-3 mb-0">
              <form class="form w-100">
                <div class="form-group d-flex">
                  <input type="text" class="form-control todo-list-input" placeholder="Add To-do">
                  <button type="submit" class="add btn btn-primary todo-list-add-btn" id="add-task">Add</button>
                </div>
              </form>
            </div>
            <div class="list-wrapper px-3">
              <ul class="d-flex flex-column-reverse todo-list">
                <li>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox">
                      Team review meeting at 3.00 PM
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox">
                      Prepare for presentation
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox">
                      Resolve all the low priority tickets due today
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li class="completed">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox" checked>
                      Schedule meeting for next week
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li class="completed">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox" checked>
                      Project review
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
              </ul>
            </div>
            <h4 class="px-3 text-muted mt-5 font-weight-light mb-0">Events</h4>
            <div class="events pt-4 px-3">
              <div class="wrapper d-flex mb-2">
                <i class="ti-control-record text-primary mr-2"></i>
                <span>Feb 11 2018</span>
              </div>
              <p class="mb-0 font-weight-thin text-gray">Creating component page build a js</p>
              <p class="text-gray mb-0">The total number of sessions</p>
            </div>
            <div class="events pt-4 px-3">
              <div class="wrapper d-flex mb-2">
                <i class="ti-control-record text-primary mr-2"></i>
                <span>Feb 7 2018</span>
              </div>
              <p class="mb-0 font-weight-thin text-gray">Meeting with Alisa</p>
              <p class="text-gray mb-0 ">Call Sarah Graves</p>
            </div>
          </div>
          <!-- To do section tab ends -->
          <div class="tab-pane fade" id="chats-section" role="tabpanel" aria-labelledby="chats-section">
            <div class="d-flex align-items-center justify-content-between border-bottom">
              <p class="settings-heading border-top-0 mb-3 pl-3 pt-0 border-bottom-0 pb-0">Friends</p>
              <small class="settings-heading border-top-0 mb-3 pt-0 border-bottom-0 pb-0 pr-3 font-weight-normal">See All</small>
            </div>
            <ul class="chat-list">
              <li class="list active">
                <div class="profile"><img src="https://via.placeholder.com/40x40" alt="image"><span class="online"></span></div>
                <div class="info">
                  <p>Thomas Douglas</p>
                  <p>Available</p>
                </div>
                <small class="text-muted my-auto">19 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="https://via.placeholder.com/40x40" alt="image"><span class="offline"></span></div>
                <div class="info">
                  <div class="wrapper d-flex">
                    <p>Catherine</p>
                  </div>
                  <p>Away</p>
                </div>
                <div class="badge badge-success badge-pill my-auto mx-2">4</div>
                <small class="text-muted my-auto">23 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="https://via.placeholder.com/40x40" alt="image"><span class="online"></span></div>
                <div class="info">
                  <p>Daniel Russell</p>
                  <p>Available</p>
                </div>
                <small class="text-muted my-auto">14 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="https://via.placeholder.com/40x40" alt="image"><span class="offline"></span></div>
                <div class="info">
                  <p>James Richardson</p>
                  <p>Away</p>
                </div>
                <small class="text-muted my-auto">2 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="https://via.placeholder.com/40x40" alt="image"><span class="online"></span></div>
                <div class="info">
                  <p>Madeline Kennedy</p>
                  <p>Available</p>
                </div>
                <small class="text-muted my-auto">5 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="https://via.placeholder.com/40x40" alt="image"><span class="online"></span></div>
                <div class="info">
                  <p>Sarah Graves</p>
                  <p>Available</p>
                </div>
                <small class="text-muted my-auto">47 min</small>
              </li>
            </ul>
          </div>
          <!-- chat tab ends -->
        </div>
      </div>
      <!-- partial -->
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="./"><i class=" ti-dashboard  menu-icon"></i> <span class="menu-title">Dashboard</span></a>
          </li>
          <?php if($op['can_manage_gateways'] == "1") { ?>
          <li class="nav-item">
            <a class="nav-link" href="./?a=exchange_gateways"><i class=" ti-credit-card  menu-icon"></i> <span class="menu-title">Exchange Gateways</span></a>
          </li>
          <?php } ?>
          <?php if($op['can_manage_directions'] == "1") { ?>
          <li class="nav-item">
            <a class="nav-link" href="./?a=exchange_directions"><i class="  ti-direction-alt menu-icon"></i> <span class="menu-title">Exchange Directions</span></a>
          </li>
          <?php } ?>
          <?php if($op['can_manage_rates'] == "1") { ?>
          <li class="nav-item">
            <a class="nav-link" href="./?a=exchange_rates"><i class=" ti-bar-chart-alt  menu-icon"></i> <span class="menu-title">Exchange Rates</span></a>
          </li>
          <?php } ?>
          <?php if($op['can_manage_rules'] == "1") { ?>
          <li class="nav-item">
            <a class="nav-link" href="./?a=exchange_rules"><i class=" fa fa-flag  menu-icon"></i> <span class="menu-title">Exchange Rules</span></a>
          </li>
          <?php } ?>
          <?php if($op['can_manage_orders'] == "1") { ?>
          <li class="nav-item">
            <a class="nav-link" href="./?a=exchange_orders"><i class="fa fa-refresh menu-icon"></i> <span class="menu-title">Exchange Orders</span></a>
          </li>
          <?php } ?>
          <?php if($op['can_manage_users'] == "1") { ?>
          <li class="nav-item">
            <a class="nav-link" href="./?a=users"><i class=" ti-user  menu-icon"></i> <span class="menu-title">Users</span></a>
          </li>
          <?php } ?>
          <?php if($op['can_manage_withdrawals'] == "1") { ?>
          <li class="nav-item">
            <a class="nav-link" href="./?a=withdrawals"><i class="fa fa-upload  menu-icon"></i> <span class="menu-title">Withdrawals</span></a>
          </li>
          <?php } ?>
          <?php if($op['can_manage_reviews'] == "1") { ?>
          <li class="nav-item">
            <a class="nav-link" href="./?a=reviews"><i class=" ti-comment-alt  menu-icon"></i> <span class="menu-title">Reviews</span></a>
          </li>
          <?php } ?>
          <?php if($op['can_manage_support_tickets'] == "1") { ?>
          <li class="nav-item">
            <a class="nav-link" href="./?a=tickets"><i class="fa fa-support  menu-icon"></i> <span class="menu-title">Support Tickets</span></a>
          </li>
          <?php } ?>
          <?php if($op['can_manage_news'] == "1") { ?>
          <li class="nav-item">
            <a class="nav-link" href="./?a=news"><i class="fa fa-newspaper-o  menu-icon"></i> <span class="menu-title">News</span></a>
          </li>
          <?php } ?>
          <?php if($op['can_manage_pages'] == "1") { ?>
          <li class="nav-item">
            <a class="nav-link" href="./?a=pages"><i class="fa fa-file-o  menu-icon"></i> <span class="menu-title">Pages</span></a>
          </li>
          <?php } ?>
          <?php if($op['can_manage_faq'] == "1") { ?>
          <li class="nav-item">
            <a class="nav-link" href="./?a=faq"><i class="fa fa-question-circle  menu-icon"></i> <span class="menu-title">FAQ</span></a>
          </li>
          <?php } ?>
        </ul>
      </nav>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">