<html>

	<head>

		<link href="/css/bootstrap.css" rel="stylesheet">
		<script src="/js/jquery-2.1.1.js"></script>
		<script src="/js/bootstrap.js"></script>

		<script src="/js/bootstrap-select.js"></script>
		<link href="/css/bootstrap-select.css" rel="stylesheet" media="screen">
		<link href="/css/other.css" rel="stylesheet" media="screen">

		<style type="text/css">

			#title {
				padding-bottom: 15%;
			}

			#titleText {
				padding-bottom: .5em;
				display: block;
			}

			#mainContainer {
				/*background-color: white;*/
			}

			.vertical-center {
			  min-height: 100%;  /* Fallback for browsers do NOT support vh unit */
			  min-height: 100vh; /* These two lines are counted as one :-)       */

			  display: flex;
			  align-items: center;
			}

			::-webkit-input-placeholder {
				text-align: center;
				font-weight: bold;
			}
			::-moz-placeholder { 
				text-align: center;
				font-weight: bold;
			}
			:-ms-input-placeholder {
				text-align: center;
				font-weight: bold;
			}

			#userInfo {
				padding-bottom: .5em;
			}

		</style>

	</head>

	<body>
		<div id="mainContainer" class="jumbotron vertical-center"> 
  		<div class="container text-center">

  			<div class="row">
	  			<h1 id="title">
	  				<span id="titleText">Welcome to <span id="siteTitle" class="text-primary">Coopswitch</span></span>
	  				<small><span id="titleSubtext">Enter your email to get started.</span></small>
	  			</h1>
  			</div>

  			<div class="row">
  				<div class="col-lg-6 col-lg-offset-3">
	  				<div id="helpBlock" class="alert alert-info alert-dismissible" role="alert">
	  					<button type="button" class="close" data-dismiss=""><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	  					<span id="alertText">Only Drexel emails will be accepted!</span>
	  				</div>
  				</div>
  			</div>

  			<form id="register" class="form-horizontal">
  				<div class="row">
	  				<div class="col-lg-6 col-lg-offset-3">
	  					<input id="email" type="text" autocomplete="off" placeholder="Email">
	  					<div id="userAccount">
	  						<!-- <div id="inputName"> -->
		  						<input id="name" type="text" autocomplete="off" placeholder="Name">
		  					<!-- </div> -->
		  					<div id="inputPassword">
		  						<input id="password" class="passwordField" type="password" placeholder="Password">
		  			  		<input id="passwordConfirm" class="passwordField" type="password" placeholder="Confirm Password">
	  			  		</div>
	  			  	</div>
	  			  	<div id="userInfo">
	  			  		<div class="row">
			  			  	<h3>Are you doing 
			  			  		<button id="oneCoop" type="button" class="btn btn-lg">one</button> or 
			  			  		<button id="threeCoop" type="button" class="btn btn-lg">three</button> co-ops?
			  			  	</h3>
			  			  </div>
			  			  <div class="row">
			  			 	  <h3>Is your co-op in the 
			  			  		<button id="cycleFall" type="button" class="btn btn-lg">Fall</button> or 
			  			  		<button id="cycleSpring" type="button" class="btn btn-lg">Spring</button>?
			  			  	</h3>
		  			  	</div><br />
		  			  	<div class="row">
		  			  		<div class="col-lg-4">
		  					    <label><p>Your major is </p></label>
		  						</div>
		  						<div class="col-lg-8">
		  							<select class="form-control selectpicker input-lg" id="major" name="major" data-live-search="true" data-size="5">
			  			  		</select>
			  			  	</div>
		  			  	</div>
	  			  	</div>
	  				  <button id="continue" type="button" class="btn btn-primary btn-lg btn-block">Continue</button>
	  				  <button id="submit" type="button" class="btn btn-success btn-lg btn-block">Submit</button>
	  			</div>
  			</form>

  		</div>
		</div>

	</body>

</html>

<script>

	$( window ).ready(function() {

		// Set classes.
		$('form#register input').addClass('form-control input-lg');

		// Hide other inputs.
		$('#userAccount').hide();
		$('#userInfo').hide();
		$('#submit').hide();

		$('#continue').fadeTo(0, 0.5);
		$('#continue').attr("disabled", true);

		getMajors();

		validEmail = false;
		validAccount = false;

		isTyping = false;

		hasEnabledPass = false;
		hasEnteredPass = false;

		program = "";
		cycle = "";

		// $('#password').attr("disabled", true);
		// $('#passwordConfirm').attr("disabled", true);
		$('#password').fadeTo(0, 0.5);
		$('#passwordConfirm').fadeTo(0, 0.5);

		// $(".alert").alert();
		$(".alert").alert();
		// $('.alert').alert('close');

	});

	$('#continue').click(function() {

		// TODO: Update continue button to link to next section. IE: #email, #settings, #info
		//			 This way it is easier to go forward / back (and implement those buttons.)

		$('.alert').fadeTo(0, 0); // Hide existing alerts on continue.

		if (validAccount) {
			// $('#userAccount').fadeToggle("fast", function() {
			// 	$('#userInfo').fadeToggle("fast");
			// });
			$('#userAccount').fadeTo(300, 0).slideUp(200);
			$('#continue').fadeTo(300, 0).slideUp(200);

			$('#userInfo').delay(520).slideDown(200);
			$('#submit').delay(520).slideDown(200);
		}
		else if (validEmail) {

			$('#titleSubtext').delay(100).fadeTo(500, 0, function() {
				$('#titleSubtext').html("We just need some basic info.");
				$('#titleSubtext').fadeTo(500, 1);
			});

			$('#email').fadeTo(300, 0).slideUp(200);
			$('#userAccount').delay(520).slideDown(200);	
		}		

		canContinue(false);

	});

	$('#oneCoop').click(function(e) {
		$('#oneCoop').addClass('btn-info');
		$('#threeCoop').removeClass('btn-info');

		program = "oneCoop";
	});

	$('#threeCoop').click(function(e) {
		$('#threeCoop').addClass('btn-info');
		$('#oneCoop').removeClass('btn-info');

		program = "threeCoop";
	});

	$('#cycleFall').click(function(e) {
		$('#cycleFall').addClass('btn-info');
		$('#cycleSpring').removeClass('btn-info');

		cycle = "cycleFall";
	});

	$('#cycleSpring').click(function(e) {
		$('#cycleSpring').addClass('btn-info');
		$('#cycleFall').removeClass('btn-info');

		cycle = "cycleSpring";
	});


	function alertCheck() { // Not really needed, more purpose as debug
		// $('#alertText').html("Checking to see if input is valid.");
		$('#alertText').fadeToggle();
		$('.alert').removeClass('alert-warning');
		$('.alert').addClass('alert-info');
		// $(".alert").alert();
	}

	function alertStatus(isValid) {
		if (!isValid) {
			$('.alert').removeClass('alert-info');
			$('.alert').addClass('alert-warning');			
		}
		else {
			$('.alert').addClass('alert-success');
			$('.alert').removeClass('alert-info');
			$('.alert').removeClass('alert-warning');
		}
	}

	function showAlert(isShown) {
		// Alert fades in faster than it fades out, currently.
		if (isShown) {
			$('.alert').fadeTo(250, 1);
		}
		else {
			$('.alert').fadeTo(400, 0);
		}
	}

	function canContinue(canContinue) {
		console.log("Function canContinue triggered.");
		if (canContinue) {
			$('#continue').fadeTo(250, 1, function() {
				$('#continue').attr("disabled", false);
			});
		}
		else {
			$('#continue').fadeTo(250, 0.5, function() {
				$('#continue').attr("disabled", true);
			});
		}
	}

	var typingTimer;
	var doneTypingInterval = 400;

	$('form#register #email').keyup(function() {

		clearTimeout(typingTimer);
		typingTimer = setTimeout(validateEmail, doneTypingInterval); // Validate email if past typing threshhold

	});

	$('form#register').keydown(function() {

		if (!isTyping) { // User has started typing 
			canContinue(false); // Typing, so disable continue
			isTyping = true;
			showAlert(false);
		}
		clearTimeout(typingTimer);
	});

	function validateEmail() {

		// TODO: Go through some more email validation.

		validEmail = false; // Email is set to not valid before checks because we are unsure.

		email = $('form#register #email').val();
		email = email.toLowerCase();

		var regex = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;

		if (regex.test(email)) { 	// Valid email true, now check domain.
			if (email.indexOf('@drexel.edu', email.length - '@drexel.edu'.length) !== -1) {
				helpBlockText = "Valid Drexel email!";
				validEmail = true;
				canContinue(true);
			}
			else {
				helpBlockText = "Not a Drexel email."; // May allow valid emails in future.
			}
		}
		else {
			helpBlockText = "Invalid email format.";
		}

		if (!validEmail) { // Sho alert warning email is invalid.
			alertStatus(validEmail);
			$('#alertText').html(helpBlockText);
			showAlert(true);
		}
		else {
			alertStatus(validEmail); // Set alert to success. Currently, not showing alert if valid.
		}

		isTyping = false;

		return validEmail;

	}

	$('form#register #name').keyup(function() {

		clearTimeout(typingTimer);
		typingTimer = setTimeout(validateName, doneTypingInterval); // Validate email if past typing threshhold

	});

	function validateName() {

		console.log("Validate Name!");
		nameIsValid = false; // TODO: Turn isValid into a function 

		name = $('form#register #name').val();
		console.log(name);

		if (name == "") {
			helpBlockText = "You need a name!";
		}

		else {
			nameIsValid = true;
		}

		if (!nameIsValid) {
			console.log("Show name alert.");
			alertStatus(nameIsValid);
			$('#alertText').html(helpBlockText);
			showAlert(true);

			enablePassword(false);
		}
		else {
			enablePassword(true);
			showAlert(false);
		}
	
		isTyping = false;

	}	

	$('#inputPassword').children().keydown(function(e) {
		if (!hasEnabledPass) {
			e.preventDefault();
		}
	});

	function enablePassword(enable) {

		// TODO: Create div around both password inputs and use JQuery child selector to set.
		fadeSpeed = 200;

		if (enable) {
			$('#inputPassword').children().fadeTo(fadeSpeed, 1);
			hasEnabledPass = true;
		}
		else {
			$('#inputPassword').children().fadeTo(fadeSpeed, 0.5);
			hasEnabledPass = false;
		}
	}

	$('form#register #inputPassword').children().keyup(function() {

		clearTimeout(typingTimer);
		typingTimer = setTimeout(validatePassword, doneTypingInterval); // Validate email if past typing threshhold

	});

	function validatePassword() {

		console.log(hasEnteredPass);
		passwordIsValid = false;

		password = $('form#register #password').val();
		passwordConfirm = $('form#register #passwordConfirm').val();

		if (!hasEnteredPass && password != "" && passwordConfirm != "") {
			hasEnteredPass = true;
		}

	 	if (hasEnteredPass) {
			if (password == passwordConfirm) {
				passwordIsValid = true;

				validAccount = true;
				canContinue(true);
			}
			else {
				helpBlockText = "Your passwords do not match.";
			}

			if (!passwordIsValid) {
				alertStatus(passwordIsValid);
				$('#alertText').html(helpBlockText);
				showAlert(true);			
			}
			else {
				showAlert(false);			
			}			
		}

		isTyping = false;

	}

	function validateAccount() {

		// TODO: Basic info validation. 
		validAccount = true;
		valid = true;

		if (valid) {
			$('#userAccount').fadeToggle("fast", function() {
				$('#userInfo').fadeToggle("fast");
			});
			$('#titleSubtext').fadeToggle("fast", function() {
				$('#titleSubtext').html("What is your current status?");
				$('#titleSubtext').fadeToggle("fast");
			});
			validAccount = true;
			$('#continue').fadeToggle("fast", function() {
				$('#submit').fadeToggle();
			});
		}
	}

	function getMajors() {
		var majors = new Array();

		idName = "#major";

		$.ajax({

			dataType: "json",
			url: "/resources/functions/scripts.php",
			data: "g=majors", 
			success: function(data) {

				majors = data;

				//for (var x=0; x<majors.length; x++) {
				$.each(majors, function() {

					var statement = '<option value="' + this.key + '" class="' + this.class + '">'+ this.name + '</select>';

					$('#major').append(statement);

					if (this.class == "noSwitch") {
						$('#major option:last-child').attr("data-canSwitch", "0");
						$('#major option:last-child').attr("data-subtext", "Not Available");
					}

					if (this.name == "Business Administration") {
						$('#major option:last-child').attr("data-subtext", "(All Business Majors)");
					}

				});

				$('.selectpicker').selectpicker('refresh');
			}
		});

	}


</script>