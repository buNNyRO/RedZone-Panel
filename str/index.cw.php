<?php

$q = connect::$g_con->prepare('SELECT * FROM `users` WHERE `name` = ?');
$data = $q->fetch(PDO::FETCH_OBJ);

$onlineplayers = connect::$g_con->prepare("SELECT * FROM `users` WHERE `Status` != -1");
$onlineplayers->execute();

$houses = connect::rows('houses');
$businesses = connect::rows('bussines');
$users = connect::rows('users');

?>

<div class="row">

    <div class="col-lg-3 col-md-6">
        <div class="card text-dark">
            <div class="card-body">
                <div class="d-flex no-block">
                    <div class="align-self-center"><h3><i class="fa fa-users fa-lg" ></i></h3></div>
                    <div class="m-l-10 align-self-center">
                        <h4 class="m-b-0"><?php echo $onlineplayers->RowCount(); ?> online players</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card text-dark">
            <div class="card-body">
                <div class="d-flex no-block">
                    <div class="align-self-center"><h3><i class="fa fa-arrow-up fa-lg"></i></h3></div>
                    <div class="m-l-10 align-self-center">
                        <h4 class="m-b-0"><?php echo $users; ?> registered accounts</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card text-dark">
            <div class="card-body">
                <div class="d-flex no-block">
                    <div class="align-self-center"><h3><i class="fa fa-home fa-lg"></i></h3></div>
                    <div class="m-l-10 align-self-center">
                        <h4 class="m-b-0"><?php echo $houses; ?> houses</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card text-dark">
            <div class="card-body">
                <div class="d-flex no-block">
                    <div class="align-self-center"><h3><i class="fa fa-line-chart fa-lg"></i></h3></div>
                    <div class="m-l-10 align-self-center">
                        <h4 class="m-b-0"><?php echo $businesses; ?> businesses</h4>

                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<div class="row">
<div class="col-md-8">

<div class="card">
    <ul class="nav nav-tabs customtab justify-content-end" role="tablist">
        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#factionhistory" role="tab" aria-selected="false" style="color: #dc3545;"><span class="hidden-xs-down"><i class="fa fa-feed"></i> Recent activity</span></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">

        <div class="tab-pane p-2o active show" id="factionhistory" role="tabpanel">
        	<div class="table-responsive">
				<table class="table">
					<thead>
						<tr class="text-uppercase"></tr>
					</thead>
					<tbody>
						<?php
							$factionhistory = connect::$g_con->prepare('SELECT * FROM `faction_logs` ORDER BY `ID`  DESC Limit 15');
							$factionhistory->execute(); ?>
						<?php while($fh = $factionhistory->fetch(PDO::FETCH_OBJ)) { ?>
						<tr>
							<th class="align-middle">
								<img class="img-circle" src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo this::getData('users', 'Model', $fh->Userid) ?>.png" alt="User">
							</th>
							<td class="align-middle">
								<p><a href="<?php echo this::$_PAGE_URL ;?>profile/<?php echo htmlspecialchars(this::getData('users', 'name', $fh->Userid)) ?>"><b><?php echo htmlspecialchars(this::getData('users', 'name', $fh->Userid)) ?></b></a> <?php echo $fh->text ;?></p>
							</td>
							<td class="align-middle"><p data-toggle="tooltip" data-original-title="<?php echo $fh->time; ?>"><i class="fa fa-clock-o"></i> <?php echo this::timeAgo($fh->time) ;?></p></td>
						</tr>
						<?php }?>
					</tbody>
				</table>
			</div>
		</div>

        </div>
    </div>
</div>

<?php $topplayers = user::take(3)->orderBy(\connect::raw('WeekHours'),'desc')->get(); ?>
<div class="col-md-4">

<div class="card">
    <div class="card-header">
        <h4><i class="fa fa-line-chart"></i> Top Players in this week</h4>
    </div>
    <div class="card-body feed feed-activity-list top3">
                <table class="table">
                    <tbody>
                    <?php foreach($topplayers as $row=>$top): ?>
                        <tr>
                            <td style="text-align:center;">
                                <div class="img img-bordered">

                                    <img class="img-fluid img-circle round" style="max-width:inherit;border: 2px solid #E5EDF6;border-radius: 100%;" src="<?php echo this::$_PAGE_URL ?>resources/images/avatars/<?php echo $top->Model; ?>.png">
                                </div>
                            </td>
                            <td>
                             <a id="<?php echo $top->id ;?>" href="<?php echo this::$_PAGE_URL; ?>profile/<?php echo htmlspecialchars($top->name); ?>"><?php echo htmlspecialchars($top->name) ;?></a> <br>
                                <i class="fa fa-clock-o"></i> Time played:
                                <?php echo $top->WeekHours ;?> hours this week
                            </td>
                            <td style="vertical-align: middle;" class="font-18">
                                # <?php echo $row+1 ;?>
                            </td>
                        </tr>
                        <?php endforeach ;?>
                        </tbody>
                </table>
            </div>

</div>
</div>