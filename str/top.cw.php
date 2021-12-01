<div class="card">
    <div class="card-body">
        <h4>Top players</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Avatar</th>
                    <th>Username</th>
                    <th></th>
                    <th>Level</th>
                    <th>Playing time</th>
                    <th>XP</th>
                </tr>
            </thead>
            <?php $topplayers = user::take(15)->orderBy(\connect::raw('Hours'),'desc')->get(); ?>
            <tbody>
                 <?php foreach($topplayers as $row=>$top): ?>
                <tr <?php if($row+1 === 1) echo 'class="table-info"'; else if($row+1 === 2) echo 'class="table-warning"'; else if($row+1 === 3) echo 'style="background-color: #4d4177;"' ?>><td style="color: #fff"><?php echo $row+1 ;?></td>
                    <td style="width:50px;"><img src="<?php echo this::$_PAGE_URL ;?>resources/images/avatars/<?php echo $top->Model ?>.png" alt="" class="round"></td>
                    <td><a id="8865" href="<?php echo this::$_PAGE_URL ;?>profile/<?php echo htmlspecialchars($top->name); ?>"><?php echo htmlspecialchars($top->name); ?></a></td>
                    <td> 
                    <?php
                    if($top->Admin > 0) echo ' <span class="label" style="background-color:#5c4ac7"><i class="fa fa-legal" data-toggle="tooltip"></i> <strong>admin</strong></span>';
                    if($top->Helper > 0) echo ' <span class="label" style="background-color:#1976d2"><i class="fa fa-legal" data-toggle="tooltip"></i> <strong>helper</strong></span>';
                    $functii = connect::$g_con->prepare("SELECT * FROM `panel_functions` WHERE `funcPlayerID` = ? ORDER BY funcID ASC");
                    	$functii->execute(array($top->id));
                    	while($badge = $functii->fetch(PDO::FETCH_OBJ)) {
                    		if($badge->funcPlayerID == $top->id) echo ' <span class="label" style="background-color:'.$badge->funcColor.';"><font style="font-family:verdana;"><i class="'.$badge->funcIcon.'" data-toggle="tooltip" data-original-title="'.htmlspecialchars($badge->funcName).'"></i> '.htmlspecialchars($badge->funcName).'</font></span>';
                    	}
                    ?>
                    </td>
                    <td style="color: #fff"><?php echo $top->Level; ?></td>
                    <td style="color: #fff"><?php echo $top->Hours; ?></td>
                    <td style="color: #fff"><?php echo $top->Experience; ?></td>
                </tr>
                <?php endforeach ;?>
            </tbody>
        </table>
    </div>
</div>