<?php
ob_start();
$start_time = microtime(TRUE);
error_reporting(0);
if(!file_exists('str/' . self::$_url[0] . '.cw.php') && strlen(self::$_url[0])) redirect::to("");

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

$playerdata = connect::$g_con->prepare('SELECT * FROM `users` WHERE `id` = ?');
$playerdata->execute(array($_SESSION['user']));
$player = $playerdata->fetch(PDO::FETCH_OBJ);

if(isset($_POST['autentificama']) && !user::isLogged()) { 

  $que = connect::$g_con->prepare('SELECT * FROM `users` WHERE `name` = ? AND `password` = ?');
  $que->execute(array($_POST['your_name'], strtoupper(hash('sha256', $_POST['your_password'] . "32ing2fsfsgseh56f"))));
    if($_POST['token'] == $_SESSION['token'] && $que->rowCount()) {
        $inter = $que->fetch(PDO::FETCH_OBJ);
        $updateip = connect::$g_con->prepare('UPDATE `users` SET IP = ? WHERE `id` = ?');
        $updateip->execute(array($_SERVER['REMOTE_ADDR'], $inter->id));

        $_SESSION['login'] = time();
        session_start();
        $_SESSION['token'] = hash('sha512', md5(bin2hex(random_bytes(32))));
        $_SESSION['user'] = $inter->id;
        $_SESSION['msg'] = '<div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
            <h3><i class="fa fa-check-circle"></i> Success</h3> You logged in successfully! <br> Attention: your session will expire automatically in 30 minutes.
            </div>';
    } else {
        $_SESSION['msg'] = '<div class="alert alert-danger">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
          <h3><i class="fa fa-exclamation-triangle"></i> Fail</h3> Your token, password or username may not be correct.
          </div>';
    }
}

if(isset($_POST['amviatasociala']) && user::isLogged()) {
    unset($_SESSION['user']);
    session_destroy();
    $_SESSION['msg'] = '<div class="alert alert-success">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
      <h3><i class="fa fa-check-circle"></i> Success</h3> You logged out successfully!
      </div>';
}

if(isset($_GET['check']) && isset($_GET['notify']) && user::isLogged()) {
	if($_GET['check'] == "on" && is_numeric($_GET['notify'])) {
		$check = connect::$g_con->prepare('SELECT `ID` FROM `panel_notifications` WHERE `ID` = ?');
		$check->execute(array($_GET['notify']));
		if($check->rowCount()) {
			$nread = connect::$g_con->prepare('UPDATE `panel_notifications` SET `Seen` = 1 WHERE `ID` = ?');
			$nread->execute(array($_GET['notify']));
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
  <meta charset="utf-8" />
  <meta name="description" content="">
  <meta name="token" content="<?=$_SESSION['token']?>" />
  <meta name="keywords" content="sa-mp, sa:mp, romania, panel, userpanel, consty, servere samp, romania rpg samp, samp romania, samp rpg, samp rpg romania, samp puncte premium">
  
  <title>RED-ZONE.RO</title>

  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

  <link href="<?php echo this::$_PAGE_URL ?>resources/assets/node_modules/morrisjs/morris.css" rel="stylesheet">
  <link href="<?php echo this::$_PAGE_URL ?>resources/assets/node_modules/toast-master/css/jquery.toast.css" rel="stylesheet">
  <link href="<?=this::$_PAGE_URL?>resources/dist/css/style.min.css" rel="stylesheet"  id="theme">
  <link href="<?php echo this::$_PAGE_URL ?>resources/css/main.css" rel="stylesheet">
  
    <script>var _PAGE_URL = "<?=this::$_PAGE_URL?>";</script>
    <script>
        let style = (localStorage.getItem('theme') == 'dark' ? 'style-dark' : 'style');
        document.getElementById("theme").setAttribute("href", _PAGE_URL + 'resources/dist/css/' + style + '.min.css');
    </script>

</head>

<style>
a { color: #dc3545; }
body::-webkit-scrollbar {
    width: 1em;
}
 
body::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
}
 
body::-webkit-scrollbar-thumb {
  background-color: darkgrey;
  outline: 1px solid slategrey;
}
</style>

<body class="skin-red-dark fixed-layout">

<?php if(!user::isLogged()) { ?>
<div id="login-up-modal" class="modal fade show" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-small">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Sign in</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="tab-pane active" id="login-up" role="tabpanel">
                    <form method="post">
                      <input class="form-control" placeholder="username" type="text" name="your_name" style="width:100%" required>
                      <p></p>
                      <input class="form-control" placeholder="password" type="password" name="your_password" style="width:100%" required>
                      <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                      <br><br>
                      <button type="submit" name="autentificama" class="btn btn-info btn-block">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if(user::isLogged()) { ?>
<div id="logout-up-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-small">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Logout</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="tab-pane active" id="logout-up" role="tabpanel">
                    <form role="form" method="post" action="" id = "form">
                        <div class="form-group">
                            <h4 align="center">Are you sure that you want to log out?</h4>
                        </div>
                        <hr>
                        <div align="center">
                            <button type="amviatasociala" name="amviatasociala" action="Logout" class="btn btn-info btn-block">Yes, log me out!</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>


    <div id="main-wrapper">
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header">
                    <a class="navbar-brand" href="<?php echo this::$_PAGE_URL ?>">
                        <b>
                        <img src="https://media.discordapp.net/attachments/715436605732683797/830414858779623445/unknown.png" alt="homepage" class="dark-logo light-logo" style="max-width: 70px">
                        </b>
                        <span>
                        Red-zone
                        </span>
                    </a>
                </div>

                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"> <a class="nav-link sidebartoggler d-lg-block d-md-block waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                        <li class="nav-item"> <a class="nav-link d-md-block waves-effect waves-dark" href="<?php echo this::$_PAGE_URL ?>"><i class="mdi mdi-message"></i></a> </li>
                    </ul>
                    <ul class="navbar-nav my-lg-0">
                      <?php if(user::isLogged()) { ?>

                        <li class="nav-item dropdown u-pro">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark text-white" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span><?php echo htmlspecialchars(auth::user()->name) ?> <i class="fa fa-angle-down"></i></span> </a>
                            <div class="dropdown-menu dropdown-menu-right animated flipInX">
                                <a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo auth::user()->name; ?>" class="dropdown-item"><i class="ti-user"></i> My profile</a>
                                <a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo auth::user()->name; ?>" class="dropdown-item"><i class="ti-settings"></i> Account settings</a>

                                <?php if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) { ?>
                                	<a href="<?php echo this::$_PAGE_URL ?>panellogs" class="dropdown-item"><i class="fa fa-list"></i> Panel Logs</a>
                            	<?php } ?>

                                <div class="dropdown-divider"></div>
                                <a onclick="$('#logout-up-modal').modal();" href="javascript:void(0)" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
                            </div>
                        </li>


                      <?php } else { ?>
                        <li class="nav-item">
                          <a class="nav-link waves-effect waves-dark text-white" onclick="$('#login-up-modal').modal();" href="javascript:void(0)" aria-haspopup="true" aria-expanded="false"> <span><i class="icon-user"></i> Guest</span> </a>
                        </li>
                      <?php } ?>

                        <li class="nav-item change-theme"> <a class="nav-link text-muted waves-effect waves-dark"
                                href="javascript:;"><i class="ti-wand" style="color: #fff;"></i></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="left-sidebar">
        <?php if(user::isLogged()) { ?>
                    <br>
                    <center>
                    <h4 class="text-muted" style="padding-top:10px;line-height:18px;font-size:16px;"><?php echo htmlspecialchars(auth::user()->name); ?></h4>
                    <h5 class="text-muted">
                    <a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo auth::user()->name; ?>" role="button" aria-haspopup="true" style="color: #798699 !important; padding: 0 5px;"><i class="fa fa-user"></i></a>
                    <a onclick="$('#logout-up-modal').modal();" href="javascript:void(0)" data-original-title="Logout" style="color: #798699 !important; padding: 0 5px;"><i class="mdi mdi-power"></i></a>
                    </h5>
                    </center>
                    <div style="height: 1px;background: rgba(120,130,140,.13);display: block;margin: 20px 0;"></div>
                <?php } ?>
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                <ul id="sidebarnav">
                    	<li <?php echo this::isActive(''); ?>>
							<a class="waves-effect waves-dark" href="<?php echo this::$_PAGE_URL ?>" aria-expanded="false">
								<i class="mdi mdi-home"></i><span class="hide-menu"> Dashboard</span>
							</a>                
						</li>

                        <li <?php echo this::isActive('online'); ?>>
							<a class="waves-effect waves-dark" href="<?php echo this::$_PAGE_URL ?>online" aria-expanded="false">
								<i class="mdi mdi-account"></i><span class="hide-menu"> Jucatori online</span>
							</a>                
						</li>

                        <li <?php echo this::isActive('search'); ?>>
							<a class="waves-effect waves-dark" href="<?php echo this::$_PAGE_URL ?>search" aria-expanded="false">
								<i class="mdi mdi-magnify"></i><span class="hide-menu"> Cauta un jucator</span>
							</a>                
						</li>

                        <li <?php echo this::isActive('staff'); ?>>
							<a class="waves-effect waves-dark" href="<?php echo this::$_PAGE_URL ?>staff" aria-expanded="false">
								<i class="mdi mdi-shield"></i><span class="hide-menu"> Staff</span>
							</a>                
						</li>

                        <li <?php echo this::isActive('factions'); ?>>
							<a class="waves-effect waves-dark" href="<?php echo this::$_PAGE_URL ?>factions" aria-expanded="false">
								<i class="mdi mdi-laptop"></i><span class="hide-menu"> Factiuni <span class="label label-rouded label-red pull-right"><?php echo connect::rows('factions') ?></span></span>
							</a>                
						</li>

                        <li <?php echo this::isActive('complaints'); ?>>
							<a class="waves-effect waves-dark" href="<?php echo this::$_PAGE_URL ?>complaints" aria-expanded="false">
								<i class="mdi mdi-gavel"></i><span class="hide-menu"> Reclamatii <span class="label label-rouded label-red pull-right"><?php echo connect::rows('panel_complaints') ?></span></span>
							</a>                
						</li>

                        <li <?php echo this::isActive('tickets'); ?>>
							<a class="waves-effect waves-dark" href="<?php echo this::$_PAGE_URL ?>tickets" aria-expanded="false">
								<i class="mdi mdi-ticket-account"></i><span class="hide-menu"> Tickete <span class="label label-rouded label-red pull-right"><?php echo connect::rows('panel_tickets') ?></span></span>
							</a>                
						</li>

                        <li <?php echo this::isActive('complaints'); ?>>
							<a class="waves-effect waves-dark" href="<?php echo this::$_PAGE_URL ?>clans" aria-expanded="false">
								<i class="mdi mdi-file-chart"></i><span class="hide-menu"> Clanuri</span>
							</a>                
						</li>

                        <li <?php echo this::isActive('unban'); ?>>
							<a class="waves-effect waves-dark" href="<?php echo this::$_PAGE_URL ?>unban" aria-expanded="false">
								<i class="mdi mdi-gavel"></i><span class="hide-menu"> Cereri unban <span class="label label-rouded label-red pull-right"><?php echo connect::rows('panel_unbans') ?></span></span>
							</a>                
						</li>

                      <li>
                        <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-arrange-send-backward"></i><span class="hide-menu"> Statistici</span></a>
                          <ul aria-expanded="false" class="collapse">
                                <li <?php echo this::isActive('top'); ?>><a href="<?php echo this::$_PAGE_URL ?>top">Top jucatori</a></li>
                                <li <?php echo this::isActive('houses'); ?>><a href="<?php echo this::$_PAGE_URL ?>houses">Case</a></li>
                                <li <?php echo this::isActive('businesses'); ?>><a href="<?php echo this::$_PAGE_URL ?>businesses">Afaceri</a></li>
                                <li <?php echo this::isActive('dealership'); ?>><a href="<?php echo this::$_PAGE_URL ?>dealership">Dealership</a></li>
                          </ul>
                      </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="page-subtitle" style="margin-top: 12px"></div>
                

          <?php if(isset($_SESSION['msg'])) { echo $_SESSION['msg']; $_SESSION['msg'] = ''; } ?>
