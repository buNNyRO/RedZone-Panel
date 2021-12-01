<?php
$qa = connect::$g_con->prepare('SELECT id FROM users WHERE Admin > 0 AND Status > 0'); $qa->execute();
$qa1 = connect::$g_con->prepare('SELECT id FROM users WHERE Admin > 0'); $qa1->execute();
$acount = $qa->rowCount() . ' / ' . $qa1->rowCount();
$qh = connect::$g_con->prepare('SELECT id FROM users WHERE Helper > 0 AND Status > 0'); $qh->execute();
$qh1 = connect::$g_con->prepare('SELECT id FROM users WHERE Helper > 0'); $qh1->execute();
$hcount = $qh->rowCount() . ' / ' . $qh1->rowCount();
$ql = connect::$g_con->prepare('SELECT id FROM users WHERE Rank = 7 AND Status > 0'); $ql->execute();
$ql1 = connect::$g_con->prepare('SELECT id FROM users WHERE Rank = 7'); $ql1->execute();
$lcount = $ql->rowCount() . ' / ' . $ql1->rowCount();
?>

<div class="card">
<div class="card-header">
    <h4><i class="fa fa-shield"></i> Staff</h4>
</div>
<div class="card-body p-b-0">
<ul class="nav customtab nav-tabs nav-fill" role="tablist">
<li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#admins" role="tab"><span class="hidden-xs-down">Admins [<?php echo $acount; ?>]</span></a></li>
<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#helpers" role="tab"><span class="hidden-xs-down">Helpers [<?php echo $hcount; ?>]</span></a></li>
<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#leaders" role="tab"><span class="hidden-xs-down">Leaders [<?php echo $lcount; ?>]</span></a></li>
</ul>

<div class="tab-content">
<div class="tab-pane active" id="admins" role="tabpanel">
<div class="p-20">
<table class="table table-condensed table-hover">
<thead>
		<tr>
			<th>Avatar</th>
			<th>Username</th>
			<th>Admin</th>
			<th>Grades</th>
			<th>Last Login</th>
		</tr>
</thead>
<tbody>
<?php
$adm = connect::$g_con->prepare("SELECT * FROM `users` WHERE `Admin` > 0 ORDER BY Admin DESC");
$adm->execute();
while($row = $adm->fetch(PDO::FETCH_OBJ))
{ ?>
<tr>
<td style="width:50px;"><img src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo $row->Model; ?>.png" alt="" class="round"></td>
<td>
<a href="<?php echo this::$_PAGE_URL; ?>profile/<?php echo htmlspecialchars($row->name); ?>"><?php echo htmlspecialchars($row->name); ?></a>
</td>
<td><?php echo $row->Admin; ?></td>
<td>
<div class="text-white">
<?php
	if($row->Admin >= 7) echo ' <span class="label" style="background-color:#00a65a"><i class="fa fa-child" data-toggle="tooltip" data-original-title="admin level: '.$row->Admin.'"></i> <strong>owner</strong></span>';

	if($row->Admin == 6) echo ' <span class="label" style="background-color:#00a65a"><i class="fa fa-shield" data-toggle="tooltip" data-original-title="admin level: '.$row->Admin.'"></i> <strong>co-owner</strong></span>';

	if($row->Admin > 0 && $row->Admin < 6) echo ' <span class="label" style="background-color:#000000"><i class="fa fa-legal" data-toggle="tooltip" data-original-title="admin level: '.$row->Admin.'"></i> <strong>admin</strong></span>';

	$functii = connect::$g_con->prepare("SELECT * FROM `panel_functions` WHERE `funcPlayerID` = ? ORDER BY funcID ASC");
	$functii->execute(array($row->id));
	while($badge = $functii->fetch(PDO::FETCH_OBJ)) {
		if($badge->funcPlayerID == $row->id) echo ' <span class="label" style="background-color:'.$badge->funcColor.';"><font style="font-family:verdana;"><i class="'.$badge->funcIcon.'" data-toggle="tooltip" data-original-title="'.htmlspecialchars($badge->funcName).'"></i> '.htmlspecialchars($badge->funcName).'</font></span>';
	}
?>
</div>
</td>
<td><?php echo $row->LastOn; ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>
<div class="tab-pane  p-20" id="helpers" role="tabpanel">
<table class="table table-hover">
<thead>
<tr>
<th>Avatar</th>
<th>Name</th>
<th>Helper Level</th>
<th>Last Login</th>
</tr>
</thead>
<tbody>
<?php
$help = connect::$g_con->prepare("SELECT * FROM `users` WHERE `Helper` > 0 ORDER BY Helper ASC");
$help->execute();
while($row = $help->fetch(PDO::FETCH_OBJ))
{ ?>
<tr>
<td style="width:50px;"><img src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo $row->Model; ?>.png" alt="" class="round"></td>
<td>
<a href="<?php echo this::$_PAGE_URL; ?>profile/<?php echo $row->name; ?>"><?php echo htmlspecialchars($row->name); ?></a>
</td>
<td><?php echo $row->Helper; ?></td>
<td><?php echo $row->LastOn; ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
<div class="tab-pane p-20" id="leaders" role="tabpanel">
<table class="table table-hover">
<thead>
<tr>
<th>Avatar</th>
<th>Name</th>
<th>Faction</th>
<th>Last Login</th>
</tr>
</thead>
<tbody>
<?php
$lead = connect::$g_con->prepare("SELECT * FROM `users` WHERE `Rank` = 7 ORDER BY id ASC");
$lead->execute();
while($row = $lead->fetch(PDO::FETCH_OBJ))
{ ?>
<tr>
<td style="width:50px;"><img src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo $row->Model; ?>.png" alt="" class="round"></td>
<td>
<a href="<?php echo this::$_PAGE_URL; ?>profile/<?php echo htmlspecialchars($row->name); ?>"><?php echo htmlspecialchars($row->name); ?></a>
</td>
<?php if($row->Member > 0) {
$s = connect::$g_con->prepare('SELECT * FROM `factions` WHERE `ID` = ?');
$s->execute(array($row->Member));
while($fact = $s->fetch(PDO::FETCH_OBJ)) { ?>
<td><?php echo $fact->Name; ?></td>
<?php }} else { ?>
<td>Civilian</td>
<?php }?>
<td><?php echo $row->LastOn; ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>
</div>
</div>