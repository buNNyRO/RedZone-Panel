<?php
	$factions = connect::$g_con->prepare("SELECT * FROM `factions`");
	$factions->execute();
?>
<?php if(!isset(this::$_url[1])) { ?>
<div class="card">
	<div class="card-body">
		<h4>Factions</h4>
		<div class="table-responsive">
			<table class="table color-table inverse-table">
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Members</th>
						<th>Actions</th>
						<th>Applications</th>
						<th>Level</th>
					</tr>
				</thead>
				<tbody>

                    <?php while($showfaction = $factions->fetch(PDO::FETCH_OBJ)) { ?>
						<!-- bla bla bla.. fiecare factiune.. -->
					<tr>
						<td><?php echo $showfaction->ID ?></td>
						<td><b><?php echo $showfaction->Name; ?></b></td>
						<td><?php
	                   			$factionmembers = connect::$g_con->prepare('SELECT `ID` FROM `users` WHERE `Member` = ?');
								$factionmembers->execute(array($showfaction->ID));
								?>
							<?php echo $factionmembers->rowCount() ?>/<?php echo $showfaction->MaxMembers ?></td>
						<td>
							<a href="<?php echo this::$_PAGE_URL ?>factions/<?php echo $showfaction->ID ?>">members</a>
							/
							<a href="<?php echo this::$_PAGE_URL ?>factions/applications/<?php echo $showfaction->ID ?>">applications</a>
							/
							<a href="<?php echo this::$_PAGE_URL ?>factions/complaints/<?php echo $showfaction->ID ?>">complaints</a>
						</td>
						<td><?php
							if(!user::isLogged()) {
								echo '<b>you\'re not logged in</b>';
							}
							else if($showfaction->Applcation == 0) {
								echo '<b>applications are currently closed</b>';
							}
                            else if(this::getData("users","Member",$_SESSION['user']) > 0) {
                                echo '<b>you are in a faction</b>';
                            }
							else if(user::isLogged() && $showfaction->Level > this::getData("users","Level",$_SESSION['user'])) {
								echo '<b>you do not have the neccesary level to apply</b>';
							}
							else { ?>
								<a href="<?php echo this::$_PAGE_URL ?>factions/apply/<?php echo $showfaction->ID ?>"><button type="button" class="btn btn-primary">Apply</button></a>
							<?php } ?></td>
						<td><?php echo $showfaction->Level; ?></td>
					</tr>
                    <?php } ?>

				</tbody>
			</table>
		</div>
	</div>
</div>
<?php } else if(this::$_url[1] == "apply") { ?>

    <?php
	$sshfac = connect::$g_con->prepare('SELECT * FROM `factions` WHERE `ID` = ?');
	$sshfac->execute(array(this::$_url[2]));
	$fshow = $sshfac->fetch(PDO::FETCH_OBJ); ?>

<?php
  	$apphave = connect::$g_con->prepare('SELECT * FROM `panel_applications` WHERE `UserID` = ? AND `Status` = 0');
    $apphave->execute(array($_SESSION['user']));

    if($apphave->rowCount()) {
  		$_SESSION['msg'] = '<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
		Ai deja o aplicatie activa!
		</div>'; redirect::to('factions'); return 1;
	}
?>

<?php
if(!user::isLogged()) {
	$_SESSION['msg'] = '<div class="alert alert-danger alert-white">Trebuie sa fi logat!</div>';
	redirect::to('factions'); return 1;
}
else if(this::getData("users","Member",$_SESSION['user']) > 0) {
	$_SESSION['msg'] = '<div class="alert alert-danger alert-white">Faci deja parte dintr-o factiune!</div>';
	redirect::to('factions'); return 1;
}
else if(user::isLogged() && $fshow->MinLevel > this::getData("users","Level",$_SESSION['user'])) {
	$_SESSION['msg'] = '<div class="alert alert-danger alert-white">Nu ai level-ul necesar pentru a aplica la aceasta factiune!</div>';
	redirect::to('factions'); return 1;
}
else if($fshow->App == 0) {
	$_SESSION['msg'] = '<div class="alert alert-danger alert-white">Aplicatiile sunt inchise!</div>';
	redirect::to('factions'); return 1;
}
?>

<?php
if(isset($_POST['app_send'])) {
	$checked = 0;
	$questions = "";
	for ($x = 1; $x <= $_SESSION['questions']; $x++) {
		if(strlen($_POST['question'.$x.'']) > 1) 
		{ 
			$checked++; 
			if($x == $_SESSION['questions']) $questions = $questions . $_POST['ques'.$x.''] . ':' . $_POST['question'.$x.'']; 
			else $questions = $questions . $_POST['ques'.$x.''] . ':' . $_POST['question'.$x.''] . '|'; 
		}
	}
	if($checked != $_SESSION['questions']) {
		$_SESSION['msg'] = '<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
		<h3 class="text-danger"><i class="fa fa-check-circle"></i> Atentie</h3> Ai lasat intrebari necompletate!
		</div>';
		redirect::to('factions/apply/'.this::$_url[2].''); return 1;
	}
	else {
		$appsd = connect::$g_con->prepare('INSERT INTO `panel_applications` (`UserID`,`FactionID`,`Answers`,`Questions`) VALUES (?,?,?,?)');
		$appsd->execute(array($_SESSION['user'], this::$_url[2], $purifier->purify(this::Protejez($questions)), $_SESSION['questions']));

		$_SESSION['msg'] = '<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
		<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Aplicatia ta a fost trimisa cu succes!
		</div>';
		redirect::to('factions'); return 1;

		$_SESSION['questions'] = -1;
	}
}
?>

<div class="row">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header">
				<h5 class="card-header-text">Your application for <?php echo this::getSpec("factions","Name","ID",this::$_url[2]); ?></h5>
			</div>
			<div class="card-body">
				<form method="post">

				<?php
					$w = connect::$g_con->prepare("SELECT * FROM `panel_questions` WHERE `factionid` = ?");
					$w->execute(array(this::$_url[2]));
					$count = 1;
					while($question = $w->fetch(PDO::FETCH_OBJ)) { ?>

					<b><?php echo $count ;?>. <?php echo $question->question ;?></b>
					<input type="hidden" name="ques<?php echo $count ;?>" value="<?php echo $question->question ;?>">
					<input type="text" class="form-control" placeholder="type your answer ..." name="question<?php echo $count ;?>">

					<?php $_SESSION['questions'] = $count; $count++; ?>
					<hr>
					<?php }?>

					<center>
						<button type="submit" class="btn btn-info" name="app_send">
							<span>Trimite aplicatia</span>
						</button>
					</center>

				</form>
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card">
			<div class="card-header">
				<h5 class="card-header-text">Salut, <?php echo this::getData('users', 'name', $_SESSION['user']) ?>!</h5>
			</div>

			<div class="card-body">
				<div align="center">
					<img style="border:1px solid solid; width:120px;" src="<?php echo this::$_PAGE_URL ?>resources/images/skins/<?php echo this::getData('users', 'Model', $_SESSION['user']) ?>.png" alt="img-profile">
					<br><br>
					<h4>Cateva informatii:</h4>
					<br>
				</div>
				<ul style="margin-left: 25px; list-style: initial;">
					<li>Poti avea o singura aplicatie deschisa. Daca aplici la aceasta factiune, nu vei putea aplica la o alta factiune pana aplicatia nu este respinsa sau iti stergi aplicatia.</li>
					<br>
					<li>Este important sa cunosti faptul ca liderii au dreptul de a-si alege membrii dupa propriile considerente, mizand in general pe jucatorii ce le inspira incredere si dau dovada de seriozitate prin aplicatia lor.</li>
					<br>
					<li>Odata ce ai realizat o aplicatie, nu vei mai putea realiza o alta aplicatie la aceasta factiune timp de 48 de ore.</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<?php } else if(this::$_url[1] == "applications") { ?>
<?php 
 $acceptatetest = connect::$g_con->prepare("SELECT ID FROM `panel_applications` WHERE `Status` = 1 AND `FactionID` = ?");
 $acceptatetest->execute(array(this::$_url[2]));
 $acceptate = connect::$g_con->prepare("SELECT ID FROM `panel_applications` WHERE `Status` = 2 AND `FactionID` = ?");
 $acceptate->execute(array(this::$_url[2]));
 $respinse = connect::$g_con->prepare("SELECT ID FROM `panel_applications` WHERE `Status` = 3 AND `FactionID` = ?");
 $respinse->execute(array(this::$_url[2])); 
?>
<div class="card">
    <div class="card-body">
    <h4>Applications<span class="float-right">
       <!-- <?php if($showfaction->Applcation == 0) echo '<a class="btn btn-success" style="float:right;" href="<?php echo this::$_PAGE_URL ?>factions/apply/<?php echo this::$_url[2]?>">Create</a>' ?> -->
    </span></h4>
    <div class="row m-t-40">
                <div class="col-md-6 col-lg-3">
					<div class="card card-inverse" style="background-color: #fcba03 !important; border-radius: 4px;">
						<div class="box text-center">
							<h1 class="font-light text-white"><?php echo $acceptatetest->RowCount(); ?></h1>
							<h6 class="text-white">Accepted for tests</h6>
						</div>
					</div>
				</div>
				<!-- Column -->
				<div class="col-md-6 col-lg-3">
					<div class="card card-inverse" style="background-color: #4CAF50 !important; border-radius: 4px;">
						<div class="box text-center">
							<h1 class="font-light text-white"><?php echo $acceptate->RowCount(); ?></h1>
							<h6 class="text-white">Accepted</h6>
						</div>
					</div>
				</div>
				<!-- Column -->
				<div class="col-md-6 col-lg-3">
					<div class="card card-inverse" style="background-color: #fc4b6c !important; border-radius: 4px;">
						<div class="box text-center">
							<h1 class="font-light text-white"><?php echo $respinse->RowCount(); ?></h1>
							<h6 class="text-white">Rejected</h6>
						</div>
					</div>
				</div>
				<!-- Column -->
				<div class="col-md-6 col-lg-3">
					<div class="card card-inverse card-info" style="background-color: #1e88e5 !important; border-radius: 4px;">
						<div class="box text-center">
							<h1 class="font-light text-white"><?php echo connect::rows('panel_applications') ?></h1>
							<h6 class="text-white">Total applications</h6>
						</div>
					</div>
				</div>
				<br>
				<!-- Column -->
		</div>

        <div class="card">
        <div class="card-header text-white">
        <h4>Pending applications</h4>
        </div>
        <div class="card-block">
        <div class="table-responsive">
        <table class="table">
        <tbody>
        <tr>
        <th><center>#</center></th>
        <th><center>Player</center></th>
        <th><center>Status</center></th>
        <th><center>Actions</center></th>
        </tr>
        <?php
        $q = connect::$g_con->prepare('SELECT * FROM `panel_applications` WHERE `FactionID` = ? AND `Status` = 0 ORDER BY ID DESC LIMIT 15');
        $q->execute(array(this::$_url[2]));
        while($rowss = $q->fetch(PDO::FETCH_OBJ)) { ?>
                <tr>
                <td class="align-middle"><center><?php echo $rowss->id;?></center></td>

                <td class="align-middle">
                    <center>
                        <a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('users', 'name', $rowss->UserID) ?>">
                            <img class="media-object img-circle" src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo this::getData('users', 'Model', $rowss->UserID) ?>.png" alt="Generic placeholder image">
                            <br>
                            <?php echo this::getData('users', 'name', $rowss->UserID) ?>
                        </a>
                    </center>
                </td>

                <td class="align-middle"><center>Un-answered</center></td>

                <td class="align-middle"><center><a href="<?php echo this::$_PAGE_URL ?>factions/application/<?php echo $rowss->id ?>">View</a></center></td>
            </tr>
            <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
        </div>

        <div class="card">
        <div class="card-header text-white">
        <h4>Accepted for tests applications</h4>
        </div>
        <div class="card-block">
        <div class="table-responsive">
        <table class="table">
        <tbody>
        <tr>
        <th><center>#</center></th>
        <th><center>Player</center></th>
        <th><center>Status</center></th>
        <th><center>Actions</center></th>
        </tr>
        <?php
        $q = connect::$g_con->prepare('SELECT * FROM `panel_applications` WHERE `FactionID` = ? AND `Status` = 1 ORDER BY ID DESC LIMIT 15');
        $q->execute(array(this::$_url[2]));
        while($rowss = $q->fetch(PDO::FETCH_OBJ)) { ?>
                <tr>
                <td class="align-middle"><center><?php echo $rowss->id;?></center></td>

                <td class="align-middle">
                    <center>
                        <a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('users', 'name', $rowss->UserID) ?>">
                            <img class="media-object img-circle" src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo this::getData('users', 'Model', $rowss->UserID) ?>.png" alt="Generic placeholder image">
                            <br>
                            <?php echo this::getData('users', 'name', $rowss->UserID) ?>
                        </a>
                    </center>
                </td>

                <td class="align-middle"><center>Accepted (for tests)</center></td>

                <td class="align-middle"><center><a href="<?php echo this::$_PAGE_URL ?>factions/application/<?php echo $rowss->id ?>">View</a></center></td>
            </tr>
            <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
        </div>

        <div class="card">
        <div class="card-header text-white">
        <h4>Accepted applications</h4>
        </div>
        <div class="card-block">
        <div class="table-responsive">
        <table class="table">
        <tbody>
        <tr>
        <th><center>#</center></th>
        <th><center>Player</center></th>
        <th><center>Status</center></th>
        <th><center>Actions</center></th>
        </tr>
        <?php
        $q = connect::$g_con->prepare('SELECT * FROM `panel_applications` WHERE `FactionID` = ? AND `Status` = 2 ORDER BY ID DESC LIMIT 15');
        $q->execute(array(this::$_url[2]));
        while($rowss = $q->fetch(PDO::FETCH_OBJ)) { ?>
                <tr>
                <td class="align-middle"><center><?php echo $rowss->id;?></center></td>

                <td class="align-middle">
                    <center>
                        <a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('users', 'name', $rowss->UserID) ?>">
                            <img class="media-object img-circle" src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo this::getData('users', 'Model', $rowss->UserID) ?>.png" alt="Generic placeholder image">
                            <br>
                            <?php echo this::getData('users', 'name', $rowss->UserID) ?>
                        </a>
                    </center>
                </td>

                <td class="align-middle"><center>Accepted (at tests)</center></td>

                <td class="align-middle"><center><a href="<?php echo this::$_PAGE_URL ?>factions/application/<?php echo $rowss->id ?>">View</a></center></td>
            </tr>
            <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
        </div>

        <div class="card">
        <div class="card-header text-white">
        <h4>Rejected applications</h4>
        </div>
        <div class="card-block">
        <div class="table-responsive">
        <table class="table">
        <tbody>
        <tr>
        <th><center>#</center></th>
        <th><center>Player</center></th>
        <th><center>Status</center></th>
        <th><center>Actions</center></th>
        </tr>
        <?php
        $q = connect::$g_con->prepare('SELECT * FROM `panel_applications` WHERE `FactionID` = ? AND `Status` = 3 ORDER BY ID DESC LIMIT 15');
        $q->execute(array(this::$_url[2]));
        while($rowss = $q->fetch(PDO::FETCH_OBJ)) { ?>
                <tr>
                <td class="align-middle"><center><?php echo $rowss->id;?></center></td>

                <td class="align-middle">
                    <center>
                        <a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('users', 'name', $rowss->UserID) ?>">
                            <img class="media-object img-circle" src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo this::getData('users', 'Model', $rowss->UserID) ?>.png" alt="Generic placeholder image">
                            <br>
                            <?php echo this::getData('users', 'name', $rowss->UserID) ?>
                        </a>
                    </center>
                </td>

                <td class="align-middle"><center>Rejected</center></td>

                <td class="align-middle"><center><a href="<?php echo this::$_PAGE_URL ?>factions/application/<?php echo $rowss->id ?>">View</a></center></td>
            </tr>
            <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
        </div>

    </div>
</div>

<?php } else if(this::$_url[1] == "application") { ?>

    <?php
if(!user::isLogged()) {
	$_SESSION['msg'] = '<div class="alert alert-danger alert-white">Trebuie sa fi logat!</div>';
	redirect::to('factions'); return 1;
} ?>

<?php
	$qz = connect::$g_con->prepare('SELECT * FROM `panel_applications` WHERE `id` = ?');
	$qz->execute(array(this::$_url[2]));
	$view = $qz->fetch(PDO::FETCH_OBJ); 
    
    ?>

<?php 
if(isset($_POST['retrageaplicatia'])) {
	  $q = connect::$g_con->prepare("SELECT * FROM `panel_applications` WHERE `UserID` = ? AND `Status` = 0");
	  $q->execute(array($_SESSION['user']));
	  $ro = $q->fetch(PDO::FETCH_OBJ);
	  if(isset($_SESSION['user']) && $_SESSION['user'] == $ro->UserID)
	  {
	      $q = connect::$g_con->prepare('DELETE FROM `panel_applications` WHERE `id` = ?');
	      $q->execute(array(this::$_url[2]));

	      $_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
			<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Ai retras aplicatia ID: # '.this::$_url[2].' cu succes!
			</div>'; redirect::to('factions'); return 1;
	  }
	  else { $_SESSION['msg'] = '<div class="alert alert-danger alert-white">Nu poti face asta.</div>'; redirect::to(''); return 1; }
	}

if(isset($_POST['acceptatpentruteste'])) {
	if(isset($_SESSION['user']) && this::getData('users', 'Member', $_SESSION['user']) == $view->FactionID && this::getData('users', 'Rank', $_SESSION['user']) >= 6) {
		$q = connect::$g_con->prepare('UPDATE `panel_applications` SET `Status` = 1,`ActionBy` = ? WHERE `id` = ?');
		$q->execute(array(this::getData('users', 'name', $_SESSION['user']),this::$_url[2]));


		$_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
			<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Ai acceptat aplicatia ID: # '.this::$_url[2].' cu succes!
			</div>'; redirect::to('factions/application/'.$view->id.''); return 1;
	}
	else { $_SESSION['msg'] = '<div class="alert alert-danger alert-white">Nu poti face asta.</div>'; redirect::to(''); return 1; }
}

if(isset($_POST['acceptainfactiune'])) {
	if(isset($_SESSION['user']) && this::getData('users', 'Member', $_SESSION['user']) == $view->FactionID && this::getData('users', 'Rank', $_SESSION['user']) >= 6) {
		$q = connect::$g_con->prepare('UPDATE `panel_applications` SET `Status` = 2,`ActionBy` = ? WHERE `id` = ?');
		$q->execute(array(this::getData('users', 'name', $_SESSION['user']),this::$_url[2]));


		$_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
			<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Ai acceptat aplicatia ID: # '.this::$_url[2].' cu succes! (trecut la teste)
			</div>'; redirect::to('factions/application/'.$view->id.''); return 1;
	}
	else { $_SESSION['msg'] = '<div class="alert alert-danger alert-white">Nu poti face asta.</div>'; redirect::to('factions'); return 1; }
}

if(isset($_POST['respingeaplicatia'])) {
	if(isset($_SESSION['user']) && this::getData('users', 'Member', $_SESSION['user']) == $view->FactionID && this::getData('users', 'Rank', $_SESSION['user']) >= 6) {
		$q = connect::$g_con->prepare('UPDATE `panel_applications` SET `Status` = 3,`ActionBy` = ? WHERE `id` = ?');
		$q->execute(array(this::getData('users', 'name', $_SESSION['user']),this::$_url[2]));

	 

		$_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
			<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Ai respins aplicatia ID: # '.this::$_url[2].' cu succes!
			</div>'; redirect::to('factions/application/'.$view->id.''); return 1;
	}
  	else { $_SESSION['msg'] = '<div class="alert alert-danger alert-white">Nu poti face asta.</div>'; redirect::to(''); return 1; }
}
?>

<div class="card">
	<div class="card-header bg-dark text-white" style="background-color: #343a40!important;">
	    <h4>Applications</h4>
	</div>
		<div class="row">
		<div class="col-lg-6 col-xlg-6 col-md-6">
			<div align="center">
                <br>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                            <tr>
                            <p class="align-self-center">Created at: <?php echo $view->Date ?></p>
                            <p class="align-self-center">Username: <?php echo this::getData('users', 'name', $view->UserID) ?></p>
                            <p class="align-self-center">Level: <?php echo this::getData('users', 'Level', $view->UserID) ?></p>
                            <p class="align-self-center">Playing time: <?php echo this::getData('users', 'Hours', $view->UserID) ?></p>
                            <p class="align-self-center">Status: 
                            <?php if($view->Status == 0) { ?>
                            Un-answered
                            <?php } else if($view->Status == 1) { ?>
                            Accepted for tests
                            <?php } else if($view->Status == 2) { ?>
                            Accepted at tests
                            <?php } else if($view->Status == 3) { ?>
                            Rejected
                            <?php } ?>
                            </p>
                            </tr>
                            </tbody>
                        </table>
                </div>
				<?php if($view->Status == 1) { ?>
				<b><?php echo this::getData('users', 'name', $view->UserID) ?> a fost acceptat pentru teste de catre <?php echo $view->ActionBy ;?></b>
				<?php } else if($view->Status == 2) { ?>
				<b><?php echo this::getData('users', 'name', $view->UserID) ?> a fost acceptat in factiune de catre <?php echo $view->ActionBy ;?></b>
				<?php } else if($view->Status == 3) { ?>
				<b><?php echo this::getData('users', 'name', $view->UserID) ?> a fost respins de catre <?php echo $view->ActionBy ;?></b>
				<?php } ?>
				<br>
				<div align="center">
					<form method="post">
						<?php if(isset($_SESSION['user']) && this::getData('users', 'Member', $_SESSION['user']) == $view->FactionID && this::getData('users', 'Rank', $_SESSION['user']) >= 6) { ?>

							<?php if($view->Status == 0) { ?>
							<button type="submit" class="btn btn-success btn-flat btn-xs" name="acceptatpentruteste" style="margin-right: 5px; font-size:12px;">Accepta pentru teste</button>
							<button type="submit" class="btn btn-danger btn-flat btn-xs" name="respingeaplicatia" style="margin-right: 5px; font-size:12px;">Respinge aplicatia</button>

							<?php } if($view->Status == 1) { ?>
							<button type="submit" class="btn btn-success btn-flat btn-xs" name="acceptainfactiune" style="margin-right: 5px; font-size:12px;">Accepta in factiune (daca a trecut testul)</button>
							<button type="submit" class="btn btn-danger btn-flat btn-xs" name="respingeaplicatia" style="margin-right: 5px; font-size:12px;">Respinge aplicatia</button>
							<?php }?>
						<?php } ?>

						<?php if(isset($_SESSION['user']) && $_SESSION['user'] == $view->UserID && $view->Status == 0) { ?>
							<button type="submit" class="btn btn-danger btn-flat btn-xs" name="retrageaplicatia" style="margin-right: 5px; font-size:12px;">Retrage aplicatia</button>
						<?php } ?>
					</form>
				</div>

			</div>
			<br><br>

			<div class="card-header">
			    <h4><i class="fa fa-history"></i> Faction History</h4>
			</div>
		
			<table class="table m-b-0 photo-table">
				<tbody>
				<?php
				$fhstr = connect::$g_con->prepare("SELECT * FROM `faction_logs` WHERE `player` = ? AND `deleted` = 0 ORDER BY id DESC");
				$fhstr->execute(array($view->UserID));
				while($fh = $fhstr->fetch(PDO::FETCH_OBJ)) { ?>
				<tr>
					<th class="align-middle">
						<img class="img-circle" src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo this::getData('users', 'Model', $fh->player) ?>.png" alt="User">
					</th>
					<td class="align-middle">
						<a href="<?php echo this::$_PAGE_URL ;?>profile/<?php echo this::getData('users', 'name', $fh->player) ?>"><b><?php echo this::getData('users', 'name', $fh->player) ?></b></a>
						<p><?php echo $fh->Text ;?></p>
					</td>
					<td class="align-middle"><p data-toggle="tooltip" data-original-title="<?php echo $fh->time; ?>"><i class="fa fa-clock-o"></i> <?php echo this::timeAgo($fh->time) ;?></p></td>
				</tr>
				<?php }?>
				</tbody>
			</table>
		</div>

		<div class="col-lg-6 col-xlg-6 col-md-6">
			<div class="card-header">
			    <h4><i class="fa fa-question"></i> Questions and Answers</h4>
			</div>
			<div class="card-body">
				<?php
					$count = 1;
					$row = explode("|", $view->Answers);
					for ($x = 0; $x < $view->Questions; $x++) {
						$show = explode(":", $row[$x]);
						echo'
						<div class="panel" style="padding: 10px; margin-bottom: 7px;">
							<h4><b>'.$count.'. '.$show[0].'</b></h4>
							<h5>'.$show[1].'</h5>
						</div>';
						$_SESSION['Questions'] = $count;
						$count++;
					}
				?>
			</div>
		</div>
	</div>
</div>

<?php } else if(this::$_url[1] == "complaints") { ?>

    <div class="card">
	<div class="card-header bg-dark text-white" style="background-color: #343a40!important;">
		<h4><i class="fa fa-legal"></i> Complaints - <?php echo this::getSpec("factions","Name","ID",this::$_url[2]); ?></h4>
	</div>
	<div class="card-block">
		<div class="table-responsive">
			<table class="table">
				<tbody>
					<tr>
						<th>#ID</th>
						<th>Creator</th>
						<th>Against</th>
						<th>Category</th>
						<th><i class="fa fa-clock-o"></i> Date</th>
						<th>Status</th>
						<th>View</th>
					</tr>
			</thead>
			<tbody>
			<?php $q = connect::$g_con->prepare("SELECT * FROM `panel_complaints` WHERE `ID` = ? AND `Status` = 0 ORDER BY `id` DESC");
			$q->execute(array(this::$_url[2]));
			while($row = $q->fetch(PDO::FETCH_OBJ))
			{ ?>
				<tr>
					<td style="vertical-align:middle"><b><?php echo $row->id ?></b></td>
					
					<td style="vertical-align:middle"><b>
					<center>
						<div class="card-block user-box" style="padding: 0.0rem;">
						
							<a class="media-left">
								<img class="img-fluid img-circle" style="border: 2px solid #E5EDF6; border-radius: 100%;" src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo this::getData('users','Model',$row->byID) ?>.png" alt="Generic placeholder image">
							<br>												
							</a>
							<a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('users', 'name', $row->byID) ?>"><?php echo this::getData('users', 'name', $row->byID) ?></a>
						</div>
						</center></b>
					</td>
					
					<td style="vertical-align:middle"><b>
					<center>
						<div class="card-block user-box" style="padding: 0.0rem;">
						
							<a class="media-left">
								<img class="img-fluid img-circle" style="border: 2px solid #E5EDF6; border-radius: 100%;" src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo this::getData('users','Model',$row->forID) ?>.png" alt="Generic placeholder image">
							<br>													
							</a>
							<a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('users', 'name', $row->forID) ?>"><?php echo this::getData('users', 'name', $row->forID) ?></a>
						</div>
						</center></b>
					</td>

					<td style="vertical-align:middle"><b>
						<?php if($row->Type == 0) { ?>
	               			General complaints
	               		<?php } if($row->Type == 1) { ?>
	               			Faction complaints
	               		<?php } if($row->Type == 2) { ?>
	               			Admin complaints
	               		<?php } if($row->Type > 3) { ?>
	               			Tentativa
	               		<?php }?>
                   	</b></td>
					
					<td style="vertical-align:middle"><b><?php echo $row->Date ?></b></td>
					
					<td style="vertical-align:middle">
                   		<?php if($row->Status == 0) { ?>
                   		<span class="label bg-success"><b>Opened</b></span>
                   		<?php } if($row->Status != 0) { ?>
                   		<span class="label bg-danger"><b>Closed</b></span>
                   		<?php }?>
                   	</td>
					<td style="vertical-align:middle">
						<button type="button" onclick="location.href='<?php echo this::$_PAGE_URL ?>complaints/view/<?php echo $row->id ?>'" class="btn btn-primary btn-icon waves-effect waves-light btn-circle" data-toggle="tooltip" data-placement="top" title="" data-original-title="Vizualizeaza reclamatia ID: #<?php echo $row->id ?>"><i class="fa fa-search"></i>
						</button>
					</td>
				</tr>
			<?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card">
	<div class="card-header bg-dark text-white" style="background-color: #343a40!important;">
		<h4><i class="fa fa-legal"></i> Arhived Complaints</h4>
	</div>
	<div class="card-block">
		<div class="table-responsive">
			<table class="table">
				<tbody>
					<tr>
						<th>#ID</th>
						<th>Creator</th>
						<th>Against</th>
						<th>Category</th>
						<th><i class="fa fa-clock-o"></i> Date</th>
						<th>Status</th>
						<th>View</th>
					</tr>
			</thead>
			<tbody>
			<?php $q = connect::$g_con->prepare("SELECT * FROM `panel_complaints` WHERE `ID` = ? AND `Status` != 0 ORDER BY `id` DESC");
			$q->execute(array(this::$_url[2]));
			while($row = $q->fetch(PDO::FETCH_OBJ))
			{ ?>
				<tr>
					<td style="vertical-align:middle"><b><?php echo $row->id ?></b></td>
					
					<td style="vertical-align:middle"><b>
					<center>
						<div class="card-block user-box" style="padding: 0.0rem;">
						
							<a class="media-left">
								<img class="img-fluid img-circle" style="border: 2px solid #E5EDF6; border-radius: 100%;" src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo this::getData('users','Model',$row->byID) ?>.png" alt="Generic placeholder image">
							<br>												
							</a>
							<a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('users', 'name', $row->byID) ?>"><?php echo this::getData('users', 'name', $row->byID) ?></a>
						</div>
						</center></b>
					</td>
					
					<td style="vertical-align:middle"><b>
					<center>
						<div class="card-block user-box" style="padding: 0.0rem;">
						
							<a class="media-left">
								<img class="img-fluid img-circle" style="border: 2px solid #E5EDF6; border-radius: 100%;" src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo this::getData('users','Model',$row->forID) ?>.png" alt="Generic placeholder image">
							<br>													
							</a>
							<a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('users', 'name', $row->forID) ?>"><?php echo this::getData('users', 'name', $row->forID) ?></a>
						</div>
						</center></b>
					</td>

					<td style="vertical-align:middle"><b>
                   		<?php if($row->Type == 0) { ?>
	               			General complaints
	               		<?php } if($row->Type == 1) { ?>
	               			Faction complaints
	               		<?php } if($row->Type == 2) { ?>
	               			Admin complaints
	               		<?php } if($row->Type > 3) { ?>
	               			Tentativa
	               		<?php }?>
                   	</b></td>
					
					<td style="vertical-align:middle"><b><?php echo $row->Date ?></b></td>
					
					<td style="vertical-align:middle">
                   		<?php if($row->Status == 0) { ?>
                   		<span class="label bg-success"><b>Opened</b></span>
                   		<?php } if($row->Status != 0) { ?>
                   		<span class="label bg-danger"><b>Closed</b></span>
                   		<?php }?>
                   	</td>
					<td style="vertical-align:middle">
						<button type="button" onclick="location.href='<?php echo this::$_PAGE_URL ?>complaints/view/<?php echo $row->id ?>'" class="btn btn-primary btn-icon waves-effect waves-light btn-circle" data-toggle="tooltip" data-placement="top" title="" data-original-title="Vizualizeaza reclamatia ID: #<?php echo $row->id ?>"><i class="fa fa-search"></i>
						</button>
					</td>
				</tr>
			<?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php } ?>