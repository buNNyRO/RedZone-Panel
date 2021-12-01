<?php
if(isset($_POST['setstock'])) {
	if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) {
		$q = connect::$g_con->prepare('UPDATE `dsvehicle` SET `Stock` = ? WHERE `ID` = ?');
		$q->execute(array($_POST['nrstock'], $_POST['setstock']));

		$_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
			<h3><i class="fa fa-check-circle"></i> Success</h3> Stock-ul a fost modificat.
			</div>'; redirect::to('dealership'); return 1;
	}
}

if(isset($_POST['setprice'])) {
	if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) {
		$q = connect::$g_con->prepare('UPDATE `dsvehicle` SET `Price` = ? WHERE `ID` = ?');
		$q->execute(array($_POST['nrprice'], $_POST['setprice']));

		$_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
			<h3><i class="fa fa-check-circle"></i> Success</h3> Pretul a fost modificat.
			</div>'; redirect::to('dealership'); return 1;
	}
}
?>


<div class="card">
	<div class="card-header">
        <h4><i class="fa fa-car"></i> Dealership</h4>
    </div>
	<div class="card-block">
		<div class="table-responsive">
			<table class="table">
		        <?php
			      $dealership = connect::$g_con->prepare("SELECT * FROM `dsvehicle` ORDER BY ID ASC");
			      $dealership->execute(); ?>
				<tbody>
					<tr>
		                <th>#</th>
						<th>View</th>
						<th>Model</th>
						<th>Price</th>
						<th>Premium points</th>
						<th>Stock</th>
		            </tr>
	                <?php while($row = $dealership->fetch(PDO::FETCH_OBJ)) { ?>
					
	                <tr>
											   <!--<img src="<?php echo this::$_PAGE_URL ?>resources/images/vehicles/<?php echo $row->Model ?>.png" alt="560" title="560" style="width: 105px"/> -->
	                   	<td class="align-middle"><?php echo $row->ID; ?></td>
						   <td class="align-middle">
						<img src="<?php echo this::$_PAGE_URL ?>resources/images/vehicles/<?php echo $row->Model ?>.png" alt="560" title="560" style="width: 105px"/>
						</td>
						<td class="align-middle"><?php echo this::$_vehicles[$row->Model] ?> (ID: <?php echo $row->Model; ?>)</td>
						<td class="align-middle">
							<?php if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) { ?>
								<form method="post">
									<div class="input-group" style="margin-bottom: 5px">
										<input class="form-control" type="text" name="nrprice" value="<?php echo $row->Price ;?>">
										<span class="input-group-btn">
											<button class="btn btn-primary" type="submit" name="setprice" value="<?php echo $row->ID ;?>"><i class="fa fa-check"></i></button>
										</span>
									</div>
								</form>
					    	<?php } else { ?>
								$<?php echo number_format($row->Price,0,'.','.'); ?>
					    	<?php } ;?>
					    </td>
					    <td class="align-middle"><?php echo number_format($row->PremiumPoints,0,'.','.'); ?></td>
						<td class="align-middle">
							<?php if(isset($_SESSION['user']) && this::getData('users', 'Admin', $_SESSION['user']) >= 6) { ?>
								<form method="post">
									<div class="input-group" style="margin-bottom: 5px">
										<input class="form-control" type="text" name="nrstock" value="<?php echo $row->Stock ;?>">
										<span class="input-group-btn">
											<button class="btn btn-primary" type="submit" name="setstock" value="<?php echo $row->ID ;?>"><i class="fa fa-check"></i></button>
										</span>
									</div>
								</form>
					    	<?php } else { ?>
								<?php echo $row->Stock ?>
					    	<?php } ;?>
						</td>
	                </tr>
	            <?php } ?>
			</table>
		</div>
	</div>
</div>