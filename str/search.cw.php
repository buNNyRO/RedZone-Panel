<div class="card">
	<div class="card-body">
		<h4>Searchbar</h4>
		<div class="container container-md">
		<div id="step1">
			<form method="POST">
				<div class="input-group input-group-lg">
					<input name="searchPlayer" type="text" class="form-control flat" placeholder="Search" required>
					<span class="input-group-btn">
						<button class="btn btn-danger btn-lg" name="searchButton" id="searchButton" type="submit">Search</button>
					</span>
				</div>
			</form>
		</div>
	  </div>
	</div>
</div>
    <?php if(empty($_POST['searchPlayer'])) return 1; ?>
    <?php if(isset($_POST['searchButton'])) {
		if(isset($_POST['searchButton'])) $_SESSION['searchPlayer'] = $_POST['searchPlayer'];
		$search = connect::$g_con->prepare("SELECT * FROM `users` WHERE `name` LIKE ? LIMIT 50");
		$search->execute(array('%'.htmlspecialchars($_SESSION['searchPlayer']).'%')); ?>
	    <?php if(!$search->RowCount()) echo '<div class="alert alert-info">Niciun jucator nu a fost gasit!</div>'; ?>
		<?php if($search->RowCount()) { ?>
			<div class="card">
	<div class="card-body">
		<h4>Search</h4>
		<div class="alert alert-info">
			Keep in mind that only the first 50 results are shown !
		</div>
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Avatar</th>
						<th>Username</th>
						<th>Level</th>
						<th>Faction</th>
						<th>Played hours</th>
											</tr>
				</thead>
				<tbody>
				<?php while($player = $search->fetch(PDO::FETCH_OBJ)) { ?>
										<tr>
						<td style="width:50px;"><img src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo $player->Model; ?>.png" alt="" class="round"></td>
						<td><a id="<?php echo $player->id ;?>" href="<?php echo this::$_PAGE_URL; ?>profile/<?php echo htmlspecialchars($player->name); ?>"><?php echo htmlspecialchars($player->name) ;?></a></td>
						<td><?php echo $player->Level ;?></td>
						<?php if($row->Member > 0) {
						$s = connect::$g_con->prepare('SELECT * FROM `factions` WHERE `ID` = ?');
						$s->execute(array($row->Member));
						while($fact = $s->fetch(PDO::FETCH_OBJ)) { ?>
						<td><?php echo $fact->Name; ?></td>
						<?php }} else { ?>
						<td>Civillian</td>
						<?php }?>
						<td><?php echo $player->Hours ;?></td>
										</tr>
				<?php } ?>
									</tbody>
			</table>
		</div>
			</div>
</div>
		<?php }?>
	<?php }?>
</div>