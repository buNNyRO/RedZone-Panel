<link href="<?php echo this::$_PAGE_URL ?>resources/dist/css/pages/ribbon-page.css" rel="stylesheet">
<link href="<?php echo this::$_PAGE_URL ?>resources/dist/css/pages/progressbar-page.css" rel="stylesheet">

<?php
	error_reporting(E_ALL);
	if(!isset(this::$_url[1])) redirect::to('');
	if(!isset(this::$_url[1]) && user::isLogged()) redirect::to('profile/'.htmlspecialchars(auth::user()->name).'');
	else $user = User::where('name', this::$_url[1])->orWhere('id', (int) this::$_url[1])->first();
	$q = connect::$g_con->prepare('SELECT * FROM `users` WHERE `name` = ?');
	$q->execute(array(htmlspecialchars(this::$_url[1])));


	if(!$q->rowCount()) {
	    echo '<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
			<h3><i class="fa fa-exclamation-triangle"></i> This player doesn\'t exist!</h3>
			</div>'
		;
	    return;
	}
	$data = $q->fetch(PDO::FETCH_OBJ);
	
	if(isset($_POST['addyt'])) {
		if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) {
			$q = connect::$g_con->prepare('UPDATE `users` SET `YouTube` = 1 WHERE `id` = ?');
			$q->execute(array($data->id));
	
			$_SESSION['msg'] = '<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
				<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Ai oferit cu succes youtuber account acestui jucator.
				</div>'; redirect::to('profile/'.$data->name.''); return 1;
			}
		}
	
	if(isset($_POST['removeyt'])) {
		if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) {
			$q = connect::$g_con->prepare('UPDATE `users` SET `YouTube` = 0 WHERE `id` = ?');
			$q->execute(array($data->id));
			$_SESSION['msg'] = '<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
				<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Ai sters cu succes youtuber account-ul acestui jucator.
				</div>'; redirect::to('profile/'.$data->name.''); return 1;
			}
	}
	
	if(isset($_POST['email_submit'])) {
	if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) {
		$q = connect::$g_con->prepare('UPDATE `users` SET `Email` = ? WHERE `id` = ?');
		$q->execute(array($purifier->purify(htmlspecialchars($_POST['email'])), $data->id));
		$_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
			<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Ai schimbat cu succes adresa de email a jucatorului.
			</div>'; redirect::to('profile/'.$data->name.''); return 1;
	    }
	}
	
	if(isset($_POST['setname'])) {
		if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) {
			$q = connect::$g_con->prepare('UPDATE `users` SET `name` = ? WHERE `name` = ?');
			$q->execute(array($_POST['nametext'], $data->name));

			$_SESSION['msg'] = '<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
				<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Numele jucatorului a fost schimbat cu succes!
				</div>'; redirect::to('profile/'.$_POST['nametext'].''); return 1;
		}
	}
	
	if(isset($_POST['changeplskin'])) {
		if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) {
			$q = connect::$g_con->prepare('UPDATE `users` SET `Model` = ? WHERE `name` = ?');
			$q->execute(array($_POST['plskinid'], $data->name));

			$_SESSION['msg'] = '<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
				<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Skin-ul jucatorului a fost schimbat cu succes!
				</div>'; redirect::to('profile/'.$data->name.''); return 1;
		}
	}
	
	if(isset($_POST['setplayerfunction'])) {
    if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) {
		$q = connect::$g_con->prepare('INSERT INTO `panel_functions` (`funcPlayerID`, `funcColor`, `funcIcon`, `funcName`) VALUES (?, ?, ?, ?)');
		$q->execute(array($data->id, $purifier->purify(this::Protejez($_POST['functioncolor'])), $purifier->purify(this::Protejez($_POST['functionicon'])), $purifier->purify(this::Protejez($_POST['functionname']))));

		$_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
			<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Ai adaugat cu succes functia <b>'.$_POST['functionname'].'</b> acestui player.
			</div>'; redirect::to('profile/'.$data->name.''); return 1;
	    }
	}

    if(isset($_POST['removefunction'])) {
    if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) {
		$q = connect::$g_con->prepare('DELETE FROM `panel_functions` WHERE `funcID` = ?');
		$q->execute(array($_POST['removefunction']));

		$_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
			<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Ai sters cu succes functia acestui player.
			</div>'; redirect::to('profile/'.$data->name.''); return 1;
	    }
	}

    if(isset($_POST['removeallfunctions'])) {
    if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) {
		$q = connect::$g_con->prepare('DELETE FROM `panel_functions` WHERE `funcPlayerID` = ?');
		$q->execute(array($data->id));

		$_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
			<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Ai sters cu succes toate functiile acestui player.
			</div>'; redirect::to('profile/'.$data->name.''); return 1;
	    }
	}
	
		if(isset($_POST['givepremiumacc'])) {
	if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) {
		$q = connect::$g_con->prepare('UPDATE `users` SET `Premium` = 1 WHERE `id` = ?');
		$q->execute(array($data->id));
		$_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
			<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Ai oferit cu succes premium account acestui jucator.
			</div>'; redirect::to('profile/'.$data->name.''); return 1;
	    }
	}

	if(isset($_POST['removepremiumacc'])) {
	if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) {
		$q = connect::$g_con->prepare('UPDATE `users` SET `Premium` = 0 WHERE `id` = ?');
		$q->execute(array($data->id));
		$_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
			<h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> Ai sters cu succes premium account-ul acestui jucator.
			</div>'; redirect::to('profile/'.$data->name.''); return 1;
	    }
	}
?>                                                                
		<div class="row">
		<div class="col-md-4 _left-side">
			<div class="card">
				<div class="card-body">
						<center class="m-t-30"><img src="<?php echo this::$_PAGE_URL ;?>resources/images/skins/<?php echo $data->Model ?>.png" width="150"/>
						<h4 class="card-title m-t-10"><i class="fa fa-circle fa-fw" style="color:red;"></i> <?php echo htmlspecialchars($data->name); ?></h4>
                        <?php
  						if($data->Admin > 0) echo ' <span class="label" style="background-color:#7460ee"><font style="font-family:verdana;"><i class="fa fa-shield"></i> <strong>admin '.$data->Admin.'</strong></font></span>';
  						if($data->Helper > 0) echo ' <span class="label" style="background-color:#1e88e5"><font style="font-family:verdana;"><i class="fa fa-comment"></i> <strong>helper</strong></font></span>';
  						if($data->Rank == 7) echo ' <span class="label" style="background-color:#26c6da"><font style="font-family:verdana;"><i class="fa fa-user"></i> <strong>faction leader</strong></font></span>';
                        if($data->Rank == 6) echo ' <span class="label" style="background-color:#7460ee"><font style="font-family:verdana;"><i class="fa fa-user"></i> <strong>faction leader</strong></font></span>';
                        if($data->YouTube === 1) echo ' <span class="label" style="background-color:#23a82e"><font style="font-family:verdana;"><i class="fa fa-youtube"></i> <strong>YouTuber</strong></font></span>';
                        if($data->Premium == 1) echo ' <span class="label" style="background-color:#23a82e"><font style="font-family:verdana;"><i class="fa fa-star"></i> <strong>premium user</strong></font></span>';
                        if($data->VIP == 1) echo ' <span class="label" style="background-color:red"><font style="font-family:verdana;"><i class="fa fa-star-half-o"></i> <strong>vip user</strong></font></span>';
  						if($data->ClanRank == 7) echo ' <span class="label" style="background: #7460ee"><font style="font-family:verdana;"><a href="'.this::$_PAGE_URL.'clan/view/'.this::getData('users', 'id', $data->Clan).'" style="color:#fff"><i class="fa fa-external-link"></i> clan owner</a></font></span>';
  						echo '';
						$functii = connect::$g_con->prepare("SELECT * FROM `panel_functions` WHERE `funcPlayerID` = ? ORDER BY funcID ASC");
						$functii->execute(array($data->id));
						while($badge = $functii->fetch(PDO::FETCH_OBJ)) {
							if($badge->funcPlayerID == $data->id) { echo ' <span class="label" style="background-color:'.$badge->funcColor.';"><font style="font-family:verdana;"><i class="'.$badge->funcIcon.'"></i> '.htmlspecialchars($badge->funcName).'</font></span>';
						}
                        }
                        ?>
				</center></div>
			</div>

			</div>
		<!-- Column -->
		<!-- Column -->
		<div class="col-md-8 _right-side">
			<div class="card">
				<!-- Nav tabs -->
				<ul class="nav nav-tabs profile-tab" role="tablist">
					<li class="nav-item"> <a class="nav-link  active " data-toggle="tab" href="#home" role="tab">Profile</a> </li>
					<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#profile" role="tab">Properties</a> </li>
					<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#faction" role="tab">Faction History</a> </li>
					<?php if(isset($_SESSION['user']) && this::getData('users','Admin',$_SESSION['user']) >= 6) { ?>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#admintools-profile" role="tab">Admin</a> </li>
                	<?php } ?>
														</ul>
				<!-- Tab panes -->
				<div class="tab-content">
					<div class="tab-pane  active " id="home" role="tabpanel">
						<div class="card-body">
							<div class="profiletimeline">
							</div>
							<div class="card-block">
								<div class="view-info">
									<div class="row">
										<div class="col-lg-12">
											<div class="general-info">
												<div class="row">
													<div class="col-lg-12">
														<table class="table m-0">
															<tbody>
																<tr>
																	<th scope="row">Clan</th>
																	<?php if($data->Clan > 0) {
																	$s = connect::$g_con->prepare('SELECT * FROM `clans` WHERE `ID` = ?');
																	$s->execute(array($data->Clan));
																	while($cl = $s->fetch(PDO::FETCH_OBJ)) { ?>
																	<td><span style="color:#<?php echo $cl->Color; ?>"><?php echo htmlspecialchars($cl->Name); ?></span>, rank <?php echo $data->ClanRank; ?></td>
																	<?php }} else { ?>
																	<td>No clan</td>
																	<?php } ?>
																</tr>
																<tr>
																	<th scope="row">Faction</th>
																	<?php if($data->Member > 0) {
																	$s = connect::$g_con->prepare('SELECT * FROM `factions` WHERE `ID` = ?');
																	$s->execute(array($data->Member));
																	while($fact = $s->fetch(PDO::FETCH_OBJ)) { ?>
																	<td><?php echo $fact->Name; ?>, rank <?php echo $data->Rank; ?></td>
																	<?php }} else { ?>
																	<td>Civillian</td>
																	<?php } ?>
																</tr>
																																<tr>
																	<th scope="row">Level</th>
																	<td><?php echo $data->Level ?> (<?php echo $data->Experience ?> EXP)</td>
																</tr>
																<tr>
																	<th scope="row">Playing Hours</th>
																	<td><?php echo $data->Hours ?></td>
																</tr>
																<tr>
																	<th scope="row">Phone</th>
																	<td><?php echo $data->Phone ?></td>
																</tr>
																<tr>
																	<th scope="row">Joined</th>
																	<td><?php echo $data->RegisterDate ?></td>
																</tr>
																<tr>
																	<th scope="row">Last Online</th>
																	<td>
																		<a class="text-muted" data-toggle="tooltip" data-original-title="<?php echo $data->LastOn ?>"><?php echo $data->LastOn ?> (<?php echo this::timeAgo($data->LastOn) ?>)</a>
																	</td>
																</tr>

																<tr>
																	<th scope="row">Job</th>
																	<?php if($data->Job > 0) {
																	$s = connect::$g_con->prepare('SELECT * FROM `jobs` WHERE `ID` = ?');
																	$s->execute(array($data->Job));
																	while($job = $s->fetch(PDO::FETCH_OBJ)) { ?>
																	<td><?php echo $job->Name; ?></td>
																	<?php }} else { ?>
																	<td>Invalid</td>
																	<?php } ?>
																</tr>
																<tr>
																	<th scope="row">Faction Punish</th>
																	<td><?php echo $data->FP ?> / 30</td>
																</tr>
																<?php if(user::isLogged() && ((auth::user()->id == $data->id) || (auth::user()->Admin != 0))) { ?>
																<tr>
																<th scope="row">Premium points</th>
																<td><?php echo number_format($data->PremiumPoints) ?></td>
																</tr>
																<tr>
																<th scope="row">Money / Bank</th>
																<td><?php echo number_format($data->Money) ?>$ / <?php echo number_format($data->Bank) ?>$</td>
																</tr>
																<tr>
																<th scope="row">Email</th>
																<td><?php echo htmlspecialchars($data->Email); ?></td>
																</tr>
																<?php }?>
																<tr>
																	<th scope="row">Referral ID</th>
																	<td><?php echo $data->Referral ?></td>
																</tr>
															</tbody>
														</table>
													</div>
												</div>
												<!-- end of row -->
											</div>
											<!-- end of general info -->
										</div>
										<!-- end of col-lg-12 -->
									</div>
									<!-- end of row -->
								</div>
								<!-- end of view-info -->
							</div>
						</div>
					</div>

					<!--second tab-->

					<div class="tab-pane" id="profile" role="tabpanel">
					<?php
						$vehicule = connect::$g_con->prepare('SELECT * FROM `cars` WHERE `Owner` = ?');
						$vehicule->execute(array($data->name)); ?>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table profile-cars">
									<thead>
										<tr>
											<th>Image</th>
											<th class="text-center">Name</th>
											<th>Price</th>
											<th>Odometer</th>
											<th>Colors</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
									<?php while($car = $vehicule->fetch(PDO::FETCH_OBJ)) { ?>
										<tr>
											<td>
												<img src="<?php echo this::$_PAGE_URL ?>resources/images/vehicles/<?php echo $car->Model ?>.png" style="height:50px;" class="">
											</td>
											<td class="text-center">
												<?php echo this::$_vehicles[$car->Model] ?> (ID: <?php echo $car->ID ?>) <br><span>[PLATE: <?php echo htmlspecialchars($car->License) ?>] [<?php echo $car->Days ?> Days]</span>
											</td>
											<td>
												1 $
											</td>
											<td>
												<?php echo $car->Odometer ?> KM
											</td>
											<td>
												<span style="color:#<?php echo $car->Color1 ?>">1</span>,
												<span style="color:#<?php echo $car->Color2 ?>">2</span>
											</td>
											<td class="align-middle">
												<button type="button" data-toggle="modal" data-target="#modalmap<?php echo $car->ID ?>" class="btn waves-effect btn-circle waves-light text-white bg-primary"><i class="fa fa-map-marker"></i></button>

												<div id="modalmap<?php echo $car->ID ?>" class="modal fade show" tabindex="-1" role="dialog">
												    <div class="modal-dialog modal-small">
												        <div class="modal-content">
												            <div class="modal-header">
												                <h4 class="modal-title">Locate <?php echo this::$_vehicles[$car->Model] ?> (ID: <?php echo $car->ID ?>)</h4>
												                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												            </div>
												            <div class="modal-body">
												                <div class="tab-pane active" id="modalmap<?php echo $car->ID ?>" role="tabpanel">
												                    <img src="<?php echo this::$_PAGE_URL; ?>resources/map?x=<?php echo $car->PosX ?>&y=<?php echo $car->PosY ?>" width="100%">
												                </div>
												            </div>
												        </div>
												    </div>
												</div>

								        	</td>
										</tr>
									<?php } ?>
									</tbody>
								</table>
							</div>
							<h4>Properties</h4>
							<hr>
							<div class="row">
								<div class="col-md-4">
								<div class="card">
					            <div class="card-body">
					            	<?php
										$houses = connect::$g_con->prepare('SELECT * FROM `houses` WHERE `Owner` = ?');
										$houses->execute(array($data->name));
										if(!$houses->rowCount()) echo '<div class="alert alert-warning">No House</div>';
										else { ?>
											<?php while($casa = $houses->fetch(PDO::FETCH_OBJ)) { ?>
											<p class="text-left">
											    Name: <b><?php echo htmlspecialchars($casa->Description) ?></b> (#<?php echo $casa->ID ?>)<br>														
												Rent: <b><?php if($casa->Rented!=0) { ?><font color="green">$<?php echo number_format($casa->Rented,0,'.','.') ?></font><?php }?>
												<?php if($casa->Rented==0) { ?><font color="red">Not Rentable</font><?php }?></b><br> 
												Price: <b><?php if($casa->Value!=0) { ?><font color="green">$<?php echo number_format($casa->Value,0,'.','.') ?></font><?php }?>
												<?php if($casa->Value==0) { ?><font color="red">Not for sale</font><?php }?></b><br>
												Status: <b><?php if($casa->Status!=0) { ?><font color="red">Locked</font><?php }?>
												<?php if($casa->Status==0) { ?><font color="green">Unlocked</font><?php }?></b>												
											</p>
										<?php }?>
									<?php }?>
								</div>
								</div>
								</div>
								<div class="col-md-4">
								<div class="card">
					            <div class="card-body">
					            	<?php
										$houses = connect::$g_con->prepare('SELECT * FROM `bussines` WHERE `Owner` = ?');
										$houses->execute(array($data->name));
										if(!$houses->rowCount()) echo '<div class="alert alert-warning">No Business</div>';
										else { ?>
											<?php while($casa = $houses->fetch(PDO::FETCH_OBJ)) { ?>
											<p class="text-left">														
												Name: <b><?php echo htmlspecialchars($casa->Description) ?></b> (#<?php echo $casa->ID ?>)<br>
												Enter price: $<?php echo number_format($casa->Fee,0,'.','.') ?><br>
												Price: <b><?php if($casa->Value!=0) { ?><font color="green">$<?php echo number_format($casa->Value,0,'.','.') ?></font><?php }?>
												<?php if($casa->Value==0) { ?><font color="red">Not for sale</font><?php }?></b><br>								
											</p>
										<?php }?>
									<?php }?>
								</div>
								</div>															
								</div>
								<div class="col-md-4">
								<div class="card">
					            <div class="card-body">
					            	<?php
										$houses = connect::$g_con->prepare('SELECT * FROM `jobs` WHERE `Owner` = ?');
										$houses->execute(array($data->name));
										if(!$houses->rowCount()) echo '<div class="alert alert-warning">No jobs owned</div>';
										else { ?>
											<?php while($casa = $houses->fetch(PDO::FETCH_OBJ)) { ?>
											<p class="text-left">														
												Name: <b><?php echo $casa->Name ?></b> (#<?php echo $casa->ID ?>)<br>
												Balance: $<?php echo number_format($casa->Balance,0,'.','.') ?><br>	
												Minim level: <b><?php echo $casa->Level ?></b><br>								
											</p>
										<?php }?>
									<?php }?>
								</div>
								</div>
								</div>
							</div>
						</div>
					</div>

					<!--Test-->
					<div class="tab-pane" id="faction" role="tabpanel">
						<div class="card-body">
						<?php
							$fhstr = connect::$g_con->prepare("SELECT * FROM `faction_logs` WHERE `Userid` = ? ORDER BY ID DESC");
							$fhstr->execute(array($data->id));
							if(!$fhstr->rowCount()) echo '<div class="feed-element"><div class="media-body">No data to show.</div></div>';
						?>
						<?php while($fh = $fhstr->fetch(PDO::FETCH_OBJ)) { ?>
						
							<tr>
								<th class="align-middle">
									<img class="img-circle" src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo this::getData('users', 'Model', $fh->Userid) ?>.png" alt="User">
								</th>
								<td class="align-middle">
									<a href="<?php echo this::$_PAGE_URL ;?>profile/<?php echo htmlspecialchars(this::getData('users', 'name', $fh->Userid)) ?>"><b><?php echo htmlspecialchars(this::getData('users', 'name', $fh->Userid)) ?></b></a>
										<?php echo $fh->text ;?></p>
								</td>

						<?php } ?>
						</div>
					</div>
					
					<?php if(isset($_SESSION['user']) && this::getData('users','Admin',$_SESSION['user']) >= 6) { ?>
                    <div class="tab-pane" id="admintools-profile" role="tabpanel">
                    <div class="card">
			            <div class="card-body">
			                <div class="row">
			                    <div class="col-md-8">
				        			<h4>Chat Logs</h4>
				        			<?php
        							$fhstr = connect::$g_con->prepare("SELECT * FROM `chat_log` WHERE `Name` = ? ORDER BY ID DESC Limit 50");
        							$fhstr->execute(array($data->name));
        							if(!$fhstr->rowCount()) echo '<div class="feed-element"><div class="media-body">No data to show.</div></div>';
        						?>
        						<?php while($fh = $fhstr->fetch(PDO::FETCH_OBJ)) { ?>
        						
        							<tr>
        								<td class="align-middle">
        										<li><?php echo htmlspecialchars($fh->text); ?> - <?php echo $fh->date; ?></li>
        								</td>
        
        						<?php } ?>

								</div>
			                    <div class="col-md-4">
									<form method="post">
									<button type="submit" onclick="sweet()" onClick="window.location.reload();" class="btn btn-primary btn-xs btn-block" name="refreshprofile">refresh profile</button>
									</form>
									<hr>					
									
									<br>
									<button type="button" class="btn btn-primary btn-xs btn-block" data-toggle="modal" data-target="#managefunctions" onclick="$('#managefunctions-modal').modal();">manage functions</button>
				                    <button type="button" class="btn btn-primary btn-xs btn-block" data-toggle="modal" data-target="#changename" onclick="$('#changename-modal').modal();">change name</button>
				                    <button type="button" class="btn btn-primary btn-xs btn-block" data-toggle="modal" data-target="#changeemail" onclick="$('#changeemail-modal').modal();">change email</button>
				                    <button type="button" class="btn btn-primary btn-xs btn-block" data-toggle="modal" data-target="#changeskin" onclick="$('#changeskin-modal').modal();">change skin</button>
									<?php if(isset($_SESSION['user']) && this::getData('users','Admin',$_SESSION['user']) >= 6) { ?>
									<button type="button" class="btn btn-primary btn-xs btn-block" data-toggle="modal" data-target="#setadmin" onclick="$('#setadmin-modal').modal();">set admin</button>
									<?php } ?>
				                    <hr>
				                    <button type="button" class="btn btn-primary btn-xs btn-block" data-toggle="modal" data-target="#managemoney" onclick="$('#managemoney-modal').modal();">manage money</button>
				        			<button type="button" class="btn btn-primary btn-xs btn-block" data-toggle="modal" data-target="#managepremiumpoints" onclick="$('#managepremiumpoints-modal').modal();">manage premium points</button>

				        			<hr>
								</div>
			                </div>
			            </div>
			        </div>
                    </div>
                    <?php } ?>

				<!--New-->
				</div>
                
		</div>
			</div>
	<!-- Column -->
</div>
</div>

<?php if(isset($_SESSION['user']) && this::getData('users','Admin',$_SESSION['user']) >= 6) { ?>
            	<div id="managefunctions" class="modal fade show" tabindex="-1" role="dialog">
				    <div class="modal-dialog modal-lg">
				        <div class="modal-content">
				            <div class="modal-header">
				                <h4 class="modal-title">Manage player functions</h4>
				                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				            </div>
				            <div class="modal-body" align="center">
				                <div class="tab-pane active" id="managefunctions" role="tabpanel">
				                <h3 class="text-red">Manage functions for <?php echo $data->name ?> ?</h3>
				                <br>
			                    <?php if($data->Premium == 0) { ?>
			                    <form method="post">
			                      <button type="submit" class="btn btn-info btn-block" style="width: 50%;" name="givepremiumacc">give premium account</button>
			                    </form>
			                    <?php } else { ?>
			                    <form method="post">
			                      <button type="submit" class="btn btn-danger btn-block" style="width: 50%;" name="removepremiumacc">remove premium account</button>
			                    </form>
			                    <?php } ?>
                                <br>
								<?php if($data->YouTube == 0) { ?>
			                    <form method="post">
			                      <button type="submit" class="btn btn-info btn-block" style="width: 50%;" name="addyt">add youtuber</button>
			                    </form>
			                    <?php } else { ?>
			                    <form method="post">
			                      <button type="submit" class="btn btn-danger btn-block" style="width: 50%;" name="removeyt">remove youtuber</button>
			                    </form>
			                    <?php } ?>

								

				                <br>
						        <form method="post">
						            <h4>Function color:</h4>
						            <br>
						            <input type="text" name="functioncolor" placeholder="ex: #7ab2fa" class="form-control">
						            <br><br>
						            <h4>Function icon:</h4>
						            <br>
						            <input type="text" name="functionicon" placeholder="ex: fa fa-check" class="form-control">
						            <br><br>
						            <h4>Function name:</h4>
						            <br>
						            <input type="text" name="functionname" placeholder="ex: ovner" class="form-control">
						            <br><br>
						            <button type="submit" class="btn btn-info btn-block" name="setplayerfunction" style="width: 50%;">
						            <i class="fa fa-check"></i> set player function
						            </button>
						            </form>
						            <hr>
						            <h3 class="text-red">Remove functions for <?php echo $data->name ?> ?</h3>
						            <form method="post">
						              <?php $q = connect::$g_con->prepare("SELECT * FROM `panel_functions` WHERE `funcPlayerID` = ? ORDER BY funcID ASC");
						                $q->execute(array($data->id));
						                while($badge = $q->fetch(PDO::FETCH_OBJ)) {
						                  if($badge->funcPlayerID == $data->id) {
						                    echo ' <span class="label" style="background-color:'.$badge->funcColor.';"><font style="font-family:verdana;"><i class="'.$badge->funcIcon.'" data-toggle="tooltip" data-original-title="'.$badge->funcName.'"></i> '.$badge->funcName.'</font></span>';
						                  }
						                  echo '<button name="removefunction" value="'.$badge->funcID.'" style="border: 1px; background-color: #fff;"><i data-toogle="remove function" title="Delete Function" class="fa fa-trash text-red "></i></button>';
						                }
						              ?>
						            </form>
						            <hr>
						            <form action="" method="post">
						              <button type="submit" class="btn btn-danger btn-block" name="removeallfunctions" style="width: 50%;">
						              <i class="fa fa-trash"></i> remove all functions
						              </button>
						            </form>
				                </div>
				            </div>
				        </div>
				    </div>
				</div>
				
				<div id="changename" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
				                <h4 class="modal-title">Change player name</h4>
				            </div>
				            <div class="modal-body" align="center">
				                <div class="tab-pane active" id="changename" role="tabpanel">
						        <form method="post">
							        <input type="text" name="nametext" placeholder="type a new name for player" style="width:100%;" required>
							        <p></p>
							        <button type="submit" class="btn btn-primary btn-block" name="setname">
							        <i class="fa fa-edit"></i> change player name
							        </button>
						        </form>
				                </div>
				            </div>
						</div>
					</div>
				</div>
				
				<div id="changeemail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-body">
								<p>Introdu adresa de email</p>
								<form method="post">
									<input class="form-control" placeholder="New email" type="email" name="email" required>
									<br>
									<button type="submit" name="email_submit" class="btn btn-primary btn-block"><i class="fa fa-check-circle"></i> CHANGE</button>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				<div id="changeskin" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
				                <h4 class="modal-title">Change player skin</h4>
				            </div>
				            <div class="modal-body" align="center">
				                <div class="tab-pane active" id="changeskin" role="tabpanel">
						        <form method="post">
					            <input type="number" name="plskinid" placeholder="type a new skin id for this player" style="width:100%;" required>
					            <p></p>
						            <button type="submit" class="btn btn-primary btn-block" name="changeplskin">
						            <i class="fa fa-edit"></i> change player skin
						            </button>
					            </form>
				                </div>
				            </div>
						</div>
					</div>
				</div>
<?php } ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script type="text/javascript">
    function sweet() {
        swal("Refreshing..", "The page will refresh in a second!", "info");
    }
</script>