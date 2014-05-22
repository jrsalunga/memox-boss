<?php
require_once('../lib/initialize.php');
!$session->is_logged_in() ? redirect_to("login"): "";
?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Cache-control" content="public">

<title>MemoXpress - Home</title>
<link rel="shortcut icon" type="image/x-icon" href="../images/memoxpress-favicon.jpg" />


<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/styles-ui2.css">

</head>
<body id="app-body" class="state-nav">


	   <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div>
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
  			<a href="/">
          		<img src="images/memoxpress.png" class="img-responsive header-logo" style="height:44px; width:100px; margin: 3px;">
        	</a>
           <a class="navbar-brand" href="/">MemoXpress</a>

        </div>
        
        <div class="navbar-collapse collapse">
        	<ul class="nav navbar-nav">
              <li><a href="reports/">Reports</a></li>
            </ul>
       		<ul class="nav navbar-nav navbar-right">
            <!--
                <li><a href="#home">Home</a></li>
                <li><a href="#location/jeff">About</a></li>
            -->    
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="glyphicon glyphicon-cog"></span>
                    <b class="caret"></b>
                    </a>
                        <ul class="dropdown-menu">
                        	<li><a href="#settings">Settings</a></li>
                            <li><a href="logout">Sign Out</a></li>

     
                      </ul>
                </li>
            </ul>  
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div>
    <div class="stage">

  

    </div>
</div> <!-- /container -->
    
    
    
<div class="modal fade" id="myProject" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
      <!--
			<div>
            	<form id="frm-mdl-project" name="frm-mdl-project" class="table-model form-horizontal" data-table="project" action="" method="post" role="form">
          			<div class="form-group">
                        <label for="code" class="col-sm-2 control-label">Code:</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="code" name="code" placeholder="Code" maxlength="20" required>
                          <span class="validation-error-block"></span>
                        </div>
                        
                  	</div>
                    <div class="form-group">
                        <label for="descriptor" class="col-sm-2 control-label">Descriptor:</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="descriptor" name="descriptor" placeholder="Descriptor" maxlength="50">
                        </div>
                  	</div>
                    <div class="form-group">
                        <label for="location" class="col-sm-2 control-label">Location:</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="location" name="location" placeholder="Location" maxlength="120">
                        </div>
                  	</div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">Type:</label>
                        <div class="col-sm-10">
                        	<select class="form-control">
                              <option value="1">Singles</option>
                              <option value="2">Hi-Rise</option>
                            </select>
                        </div>
                  	</div>
                    <div class="form-group">
                        <label for="datestart" class="col-sm-2 control-label">Date-Start:</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="datestart" name="datestart" placeholder="YYYY-MM-DD">
                        </div>
                  	</div>
                    <div class="form-group">
                        <label for="dateendx" class="col-sm-2 control-label">Date-Target:</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="dateendx" name="dateendx" placeholder="YYYY-MM-DD">
                        </div>
                  	</div>
                    <div class="form-group">
                        <label for="dateend" class="col-sm-2 control-label">Date-End:</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="dateend" name="dateend" placeholder="YYYY-MM-DD">
                        </div>
                  	</div>
                    <div class="form-group">
                        <label for="amount" class="col-sm-2 control-label">Amount:</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="amount" name="amount" placeholder="Amount">
                        </div>
                  	</div>
                    <div class="form-group">
                        <label for="balance" class="col-sm-2 control-label">Balance:</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="balance" name="balance" placeholder="Balance">
                        </div>
                  	</div>
                    <div class="form-group">
                        <label for="notes" class="col-sm-2 control-label">Notes:</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" id="notes" name="notes" placeholder="Notes"></textarea>
                        </div>
                  	</div>
                  
                  
                </form>
            </div>
     	-->
      </div>
      <div class="modal-footer">
        	<button type="button" id="mdl-btn-save" class="btn btn-primary model-btn-save" data-dismiss="modal" disabled>Save</button>
          <button type="button" is="mdl-btn-save-blank" class="btn btn-primary model-btn-save-blank" disabled>Save &amp; Blank</button>
          <button type="button" id="mdl-btn-save" class="btn btn-default model-btn-cancel" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="myActivity" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
			<div>
            	<form id="frm-mdl-activity" name="frm-mdl-activity" class="table-model form-horizontal" data-table="activity" action="" method="post" role="form">
		<div class="form-group">
			<label for="code" class="col-sm-2 control-label">Code:</label>
			<div class="col-sm-10">
			  <input type="text" class="form-control" id="code" name="code" placeholder="Code" maxlength="20" required>
			  <span class="validation-error-block"></span>
			</div>
			
		</div>
		<div class="form-group">
			<label for="descriptor" class="col-sm-2 control-label">Descriptor:</label>
			<div class="col-sm-10">
			  <input type="text" class="form-control" id="descriptor" name="descriptor" placeholder="Descriptor" maxlength="50">
			</div>
		</div>
		<div class="form-group">
			<label for="action" class="col-sm-2 control-label">Action:</label>
			<div class="col-sm-10">
			  <input type="text" class="form-control" id="action" name="action" placeholder="Action" maxlength="120">
			</div>
		</div>
		<div class="form-group">
			<label for="stageid" class="col-sm-2 control-label">Stageid:</label>
			<div class="col-sm-10">
			  <input type="text" class="form-control" id="stageid" name="stageid" placeholder="Stageid" maxlength="120">
			</div>
		</div>
		<div class="form-group">
			<label for="date" class="col-sm-2 control-label">Date:</label>
			<div class="col-sm-10">
			  <input type="text" class="form-control" id="date" name="date" placeholder="YYYY-MM-DD">
			</div>
		</div>
		
		<div class="form-group">
			<label for="notes" class="col-sm-2 control-label">Notes:</label>
			<div class="col-sm-10">
			  <textarea class="form-control" id="notes" name="notes" placeholder="Notes"></textarea>
			</div>
		</div>
	</form>
            </div>
      </div>
      <div class="modal-footer">
        	<button type="button" id="mdl-btn-save" class="btn btn-primary model-btn-save" data-dismiss="modal" disabled>Save</button>
          <button type="button" is="mdl-btn-save-blank" class="btn btn-primary model-btn-save-blank" disabled>Save &amp; Blank</button>
          <button type="button" id="mdl-btn-save" class="btn btn-default model-btn-cancel" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->







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
-->
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


<script>




$(document).ready(function(e) {
	
	

	
});
</script>

</body>
</html>