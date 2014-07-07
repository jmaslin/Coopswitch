<?php
// if (!isset($_SESSION['login']))
// 	$_SESSION['login'] = "";

session_start();
session_regenerate_id(true);

//include('/var/www/scripts.php');

// Include useful scripts so I do not have to on each page.
foreach (glob($_SERVER['DOCUMENT_ROOT'] . "/resources/functions/*.php") as $filename) {
    
    // Ignore connect.php because we will use it when necessary only, avoid unnecessary connections
    if (strpos($filename, 'connect.php') !== TRUE) {
    	include $filename;
    }
    // echo $filename;
}

$title = "Coopswitch";
$slogan = "A simple way to switch co-ops."; //Get on the right cycle! Ha.

if ($_SESSION['login'] == 0) {
  if ($debug_login) {
    $_SESSION['login'] = 1;
    $_SESSION['user_name'] = "Test User";
  	$_SESSION['user_major_name'] = "Computer Science";
  	$_SESSION['user_cycle_name'] = "Spring-Summer";
  	$_SESSION['user_program_name'] = "3 co-ops";
  	$_SESSION['user_matched'] = 1;
    $_SESSION['user_email_verified'] = 1;
  }
}

?>

<html lang="en">

<head>

	<title><?php echo "$title"; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta content="utf-8" http-equiv="encoding">
	<link href="../css/bootstrap.css" rel="stylesheet">
	<link href="../css/other.css" rel="stylesheet">
	
	<link rel="stylesheet" type="text/css" media="screen" href="http://silviomoreto.github.io/bootstrap-select/stylesheets/bootstrap-select.css">
	
	<link href="http://fonts.googleapis.com/css?family=Cutive" rel="stylesheet" type="text/css">

	<style>
		h1 {
			font-family: 'Cutive';
			font-size: 48px;
		}
	</style>
	
	<script src="../js/global.js"></script>
	<script src="../js/bootstrap-select.js"></script>
	
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

</head>

<body>
	<div class="container-fluid">
		<div class="row col-md-6 col-md-offset-3 text-center">
			<h1><?php echo "$title"; ?></h1>
			<h4><?php echo "$slogan"; ?> </h4>
		</div>
		<div class="row col-lg-4 col-lg-offset-4 col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 text-center">
			<ul class="nav nav-pills nav-justified">
				<li>
					<a href="/">Home</a>
				</li>
				<li>
					<a href="/about">About</a>
				</li>
				<li>
					<a href="/stats">Stats</a>
				</li>
				<li>
					<a href="/check">Matches</a>
				</li>
			</ul>
		</div>

			<!-- <div class="panel panel-default"> <br /> -->
		<br><br>

				<?php if (!isset($_SESSION['login'])) { //if ($_SESSION['login'] == "") { ?>
  				<div class="row-fluid col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 text-center">
  					<br><form class="form-inline" role="form" name="login_form" method="post" action="/login.php">
  						<fieldset>
  							<div class="form-group">
  						    	<label class="sr-only" for="email">Email address</label>
  						   		<input type="email" class="form-control" name="email" id="email" placeholder="Email">
  	  						</div>
  	  						<div class="form-group">
  						    	<label class="sr-only" for="password">Password</label>
  						    	<input type="password" class="form-control" name="password" id="password" placeholder="Password">
  							</div>
  					   		<button type="submit" class="btn btn-default btn-success">Sign In</button>
  					    </fieldset>
  					</form>
  					<hr>
  				</div>
				<?php } else { ?>

					<div class="row-fluid col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 text-center">
						<br><p class="lead">
							Hey, <?php echo $_SESSION['user_name']; ?>.&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="/account.php"><button type="button" class="btn btn-primary" >Profile</button></a>
							<a href="/logout.php"><button type="button" class="btn btn-danger">Logout</button></a>
						</p>
						<hr>
					</div>

				<?php } ?>
				
		<!--	</div> -->
		</div>
	<!-- </div> -->