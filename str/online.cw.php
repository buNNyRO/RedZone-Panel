<div class="card">
	<div class="card-header">
		<h4>Online</h4>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered">
				<?php
					$onlineplayers = connect::$g_con->prepare("SELECT * FROM `users` WHERE `Status` != -1 ORDER BY id ASC");
					$onlineplayers->execute(); ?>
				<thead>
				<tr>
					<th>Avatar</th>
					<th>Username</th>
					<th>Faction</th>
					<th>Level</th>
					<th>Playing time</th>
					<th>Job</th>
				</tr>
				</thead>
				<tbody>
					<?php
					while($row = $onlineplayers->fetch(PDO::FETCH_OBJ))
					{ ?>
					<tr>
						<td class="align-middle" style="width:50px;"><img src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo $row->Model; ?>.png" alt="" class="round"></td>
						<td class="align-middle"><a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo htmlspecialchars($row->name); ?>"><?php echo htmlspecialchars($row->name); ?></a></td>
						<?php if($row->Member > 0) {
						$s = connect::$g_con->prepare('SELECT * FROM `factions` WHERE `ID` = ?');
						$s->execute(array($row->Member));
						while($fact = $s->fetch(PDO::FETCH_OBJ)) { ?>
						<td class="align-middle"><?php echo $fact->Name; ?></td>
						<?php }} else { ?>
						<td class="align-middle">Civillian</td>
						<?php }?>
						<td class="align-middle"><?php echo $row->Level; ?></td>
						<td class="align-middle"><?php echo $row->Hours; ?></td>
						<?php if($row->Job > 0) {
						$s = connect::$g_con->prepare('SELECT * FROM `jobs` WHERE `ID` = ?');
						$s->execute(array($row->Job));
						while($fact = $s->fetch(PDO::FETCH_OBJ)) { ?>
						<td class="align-middle"><?php echo $fact->Name; ?></td>
						<?php }} else { ?>
						<td class="align-middle">No job</td>
						<?php }?>
					</tr>
				<?php } ?>
			</table>
		</div>
	</div>
</div>