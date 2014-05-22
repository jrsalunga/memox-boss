<?php
require_once('../lib/initialize.php');
$session->is_logged_in() ? redirect_to("index"): "";
?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Cache-Control" content="max-age=3600"/>


<title>MemoXpress - Login</title>
<link rel="shortcut icon" type="image/x-icon" href="../images/memoxpress-favicon.jpg" />

<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/styles-ui2.css">

</head>
<body id="app-body" class="state-nav">
	<!-- fixed nav bar -->
	<div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="navbar-header">
            <a href="">
                <img src="images/memoxpress.png" class="img-responsive" style="height:44px; width:100px; margin: 3px;">
            </a>

            
       </div>
       <!--
       <span class="comp-name">Modularfusion</span>
       -->
    </div>
	<!-- end fixed nav bar -->
    <div class="row">
   		<div class="login-container img-rounded">
        
			<form class="form-signin" role="form" method="post" action="api/AuthUserLogin">
                <h2 class="form-signin-heading">Please sign in</h2>
                	<input class="form-control" name="username" type="texr" autofocus="" required="" placeholder="Username">
                	<input class="form-control" name="password" type="password" required="" placeholder="Password">
                	<label class="checkbox">
                	<input type="checkbox" value="remember-me">
                	Remember me
                	</label>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            </form>
    	</div>
	</div> <!-- /container -->
    







<!--
<link rel="stylesheet" href="css/main-ui.css">
<link rel="stylesheet" href="css/styles-ui.css">
-->

<script src="js/vendors/jquery-1.10.1.min.js"></script>
<script src="js/vendors/jquery-ui-1.10.3.js"></script>
<!--
<script src="../js/vendors/jquery-ui-1.10.3.js"></script>
<script src="../js/vendors/jquery-1.9.1.js"></script>
<script src="js/vendors/underscore-min.js"></script>
<script src="js/vendors/backbone-min.js"></script>

<script src="js/vendors/underscore-min.js"></script>
<script src="js/vendors/backbone-min.js"></script>
<script src="js/vendors/bootstrap.min.js"></script>
<script src="js/vendors/backbone-validation-min.js"></script>
<script src="js/vendors/jquery.cookie-1.4.js"></script>
<script src="js/vendors/moment.2.1.0-min.js"></script>
<script src="js/vendors/accounting.js"></script>
<script src="js/vendors/jquery.filedrop.js"></script>
<script src="js/common.js"></script>

<script src="js/app.js"></script>
-->


<script>




$(document).ready(function(e) {
	
	

	
});
</script>

</body>
</html>