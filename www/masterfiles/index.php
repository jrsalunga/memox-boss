<?php
require_once('../../lib/initialize.php');
ini_set('display_errors','On');
!$session->is_logged_in() ? redirect_to("../login"): "";
if(isset($_GET['fr']) && isset($_GET['to'])){
    sanitize($_GET);
    $dr = new DateRange($_GET['fr'],$_GET['to']);
} else {
    $dr = new DateRange(NULL,NULL,false);   
}
?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MemoXpress - Masterfiles Dashboard</title>
<link rel="shortcut icon" type="image/x-icon" href="../images/memoxpress-favicon.jpg" />

<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="../css/styles-ui2.css">
<!--
<link rel="stylesheet" href="css/main-ui.css">
<link rel="stylesheet" href="css/styles-ui.css">
-->


</head>
<body id="app-body" class="state-nav">

	<!-- Fixed navbar -->
    <?php
        include_once('../inc/navbar.php');
    ?>
    <div>
    <div class="stage">
		<div class="col-sm-2 col-md-2 l-pane">
    	<?php
            $activ = '';
            include_once('../inc/masterfiles-menu-nav.php');
        ?>
    	</div>
    	<div class="col-sm-10 col-md-10 r-pane pull-right">
        	<section>
            	<div class="row">
                	<div class="col-md-12 title">
                		<h1>Masterfiles</h1>
                	</div>
                </div>
                
      		</section>
        </div>
  

    </div>
</div> <!-- /container -->
    
    
    
    
    
    <!--
<link rel="stylesheet" href="css/main-ui.css">
<link rel="stylesheet" href="css/styles-ui.css">
-->

<script src="../js/vendors/jquery-1.10.1.min.js"></script>
<script src="../js/vendors/jquery-ui-1.10.3.js"></script>
<!--
<script src="../js/vendors/jquery-ui-1.10.3.js"></script>
<script src="../js/vendors/jquery-1.9.1.js"></script>
<script src="js/vendors/underscore-min.js"></script>
<script src="js/vendors/backbone-min.js"></script>
-->
<script src="../js/vendors/underscore-min.js"></script>
<script src="../js/vendors/backbone-min.js"></script>
<script src="../js/vendors/bootstrap.min.js"></script>
<script src="../js/vendors/backbone-validation-min.js"></script>
<script src="../js/vendors/jquery.cookie-1.4.js"></script>
<script src="../js/vendors/moment.2.1.0-min.js"></script>
<script src="../js/vendors/accounting.js"></script>
<script src="../js/vendors/jquery.filedrop.js"></script>


<script src="../js/vendors/highcharts-4.0.1.min.js"></script>
<script src="../js/vendors/highcharts.data.js"></script>
<script src="../js/vendors/highcharts.exporting-4.0.1.js"></script>

<script src="../js/common.js"></script>
<script src="../js/highcharts.js"></script>



<script>




$(document).ready(function(e) {
	
	
	
});
</script>


</body>
</html>