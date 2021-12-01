<?php
$_SESSION['msg'] = '<div class="alert alert-danger">
<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
This might work later!
</div>';
redirect::to(''); 
return 1;
?>