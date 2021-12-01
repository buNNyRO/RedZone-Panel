<div class="card">
	<div class="card-header">
        <h4><i class="fa fa-home"></i> Houses</h4>
    </div>
	<div class="card-block">
		<div class="table-responsive">
        	<table class="table table-bordered">
		        <?php
			      $houses = connect::$g_con->prepare("SELECT * FROM `houses` ORDER BY ID ASC ".this::limit());
			      $houses->execute(); ?>
				<tbody>
					<tr>
		                <th>#ID</th>
						<th>Owner</th>
						<th>Description</th>
						<th>Rent</th>
						<th>Price</th>
						<th>Action</th>
		            </tr>
	                  <?php
				      while($row = $houses->fetch(PDO::FETCH_OBJ))
				      { ?>
	                <tr>
	                   	<td class="align-middle"><?php echo $row->ID; ?></td>
						<td class="align-middle"><?php echo htmlspecialchars($row->Owner); ?></td>
						<td class="align-middle"><?php echo htmlspecialchars($row->Description); ?></td>
						<td class="align-middle"><?php if($row->Rentabil!=0) { ?><font color="green">$<?php echo number_format($row->Rent,0,'.','.') ?></font><?php }?>
						<?php if($row->Rentabil==0) { ?><font color="red">Not Rentable</font><?php }?></td>
						<td class="align-middle"><?php if($row->Value!=0) { ?><font color="green">$<?php echo number_format($row->Value,0,'.','.') ?></font><?php }?>
						<?php if($row->Value==0) { ?><font color="red">Not for sale</font><?php }?></td>
				        <td class="align-middle">
				        	<button type="button" data-toggle="modal" data-target="#modalmap<?php echo $row->ID ?>" class="btn btn-circle waves-light text-white bg-primary"><i class="fa fa-map-marker"></i></button>

			        	<div id="modalmap<?php echo $row->ID ?>" class="modal fade show" tabindex="-1" role="dialog">
						    <div class="modal-dialog modal-small">
						        <div class="modal-content">
						            <div class="modal-header">
						                <h4 class="modal-title">Locate House ID: <?php echo $row->ID ?></h4>
						                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						            </div>
						            <div class="modal-body">
						                <div class="tab-pane active" id="modalmap<?php echo $row->ID ?>" role="tabpanel">
						                    <img src="<?php echo this::$_PAGE_URL; ?>resources/map?x=<?php echo $row->Entrancex ?>&y=<?php echo $row->Entrancey ?>" width="100%">
						                </div>
						            </div>
						        </div>
						    </div>
						</div>

			        	</td>
	                </tr>
	            <?php } ?>
			</table>
		</div>
		<br>
	<?php echo this::create(connect::rows('houses')); ?>
	</div>
</div>