<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/config.php");
require_once(TEMPLATES_PATH . "/header.php");

$rowClass = "";


if (isset($_GET['msg'])) {
	$msg = test_input($_GET['msg']);
}
else {
	$msg = "0";
}

?>

<div class="container">

		<div class="row">
			<div class="centeredRow">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="aboutTabs">
					<li class="active" id="infoTab"><a href="#info">Info</a></li>
					<li id="contactTab"><a href="#contact">Contact</a></li>
				</ul>
			</div>
		</div>

		<div class="tab-content">

			<div class="tab-pane fade in active" id="info">
				<div class="row">
					<div class="normalRow">
						<div class="text-center text-primary">
							<h2>What is this website?</h2>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="normalRow">
						<div class="lead text-center">
							<p>Coopswitch automates the co-op switch process to save Drexel students time.</p>
						</div>
					</div>
				</div>

				<div class="row">	
					<div class="normalRow">
						<div clsudo apt-get install ubuntu-restricted-extras avvass="bg-info text-center" style="padding: 20px; font-size: 135%; line-height: 200%;">
							<p>Coopswitch was created to help Drexel students quickly and easily find someone to switch co-op cycles with.</p>
							<br>
							<p>As more people register, the switch frequency will be higher.</p>
						</div>
					</div>
				</div>
			</div>

			<div class="tab-pane fade" id="contact">

				<div class="row">
					<div class="normalRow">
						<div class="text-center text-primary">
							<h2>Contact Us</h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="centeredRow">
						<p class="lead">Feedback is always appreciated!</p>
					</div>
				</div>

				<div class="row">
					<div class="centeredRow">
						<form role="form" iid="emailUs" method="post" action="mailsite.php">

							<div class="row">
				  				<label for="nameField">Name</label>
				  				<?php if (isset($_SESSION['login']) && $_SESSION['login'] == "1") { ?>
				  				<input disabled class="form-control" id="name" name="name" value="<?php echo $_SESSION['user_name']; ?>">
				  				<?php } else { ?>
				  				<input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" onchange="validate(name)">
				  				<?php } ?>
				  				<span class="help-block error"><div id="errorName"></div></span>
		  					</div>

							<div class="row">
				  				<label for="emailField">Email</label>
				  				<?php if (isset($_SESSION['login']) && $_SESSION['login'] == "1") { ?>
				  				<input disabled type="email" class="form-control" id="name" name="name" value="<?php echo $_SESSION['user']; ?>">
				  				<?php } else { ?>
				  				<input type="email" class="form-control" id="email" name="email" placeholder="Enter your contact email" onchange="validate(email)">
				  				<?php } ?>
				  				<span class="help-block error"><div id="errorEmail"></div></span>
		  					</div>

		  					<div class="row">
		  						<label for="reasonField">Subject</label>
		  						<select class="form-control selectpicker" name="subject" title="Please choose a subject">
		  							<option value="Comment">Comment</option>
		  							<option value="Question">Question</option>
		  							<option value="Site Problem">Site Problem</option>
		  							<!-- <option value="Advertising">Advertising</option> -->
		  							<option value="Other">Other</option>
		  						</select>
		  					</div>
		  					<div class="row">
		  						<label for="bodyField">Message</label>
		  						<textarea class="form-control" rows="5" id="message" name="message" placeholder="What would you like to say?"></textarea>
		  					</div>
		  					<div class="row" style="padding-top: 15px;">
		  						<button class="btn btn-lg btn-success" type="submit" value="Submit">Send</button>
		  					</div>
		  					
						</form>

					</div>
				</div>

			</div>

		</div>
</div>

<div class="modal fade" id="emailSent" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h2 class="modal-title">Form Feedback</h2>
            </div>
            <div id="emailSubmitted" class="modal-body">
                <p class="lead">Email sent, thank you!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

	rowClass = "col-sm-6 col-sm-offset-3";

	$('.normalRow').addClass(rowClass);
	$('.centeredRow').addClass(rowClass + " text-center");

</script>

<script>
    $('.selectpicker').selectpicker();

	$('#infoTab a').click(function (e) {
		e.preventDefault()
		$(this).tab('show')
		//$('#infoTab').addClass('active')
		//$('#contactTab').removeClass('active')
	})
	$('#contactTab a').click(function (e) {
		e.preventDefault()
		$(this).tab('show')
		//$('#contactTab').addClass('active')
		//$('#infoTab').removeClass('active')
	})

	msg = "<?php echo $msg; ?>";

	if (msg == 1) {
		$('#emailSent').modal('show');
	}

	var validate = function (field) {

		if (field == "name") {

		}

	}

</script>

<br>
<?php
require_once(TEMPLATES_PATH . "/footer.php");
?>