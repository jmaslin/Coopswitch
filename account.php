<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/config.php");
require_once(TEMPLATES_PATH . "/header.php");
include(FUNCTION_PATH . "/connect.php");

if ($_SESSION['login'] == "") {
	header("Location: /error.php"); // Temporary I guess maybe add like ?error=1
}

else if ($_SESSION['login'] == "1") {
}

$rowClass = "col-md-6 col-md-offset-3 text-center";
$userFieldClass = "col-sm-2 col-sm-offset-4 text-center";

$majorBeginClass = "";
$majorBeginEditClass = "";

$beginTextClass = "col-sm-4 col-sm-offset-2";
$beginTextStyle = "padding-right: 0px; text-align: justify;";

?>

<!-- Work on edit button location -->

<!-- <hr> -->

<div class="container-fluid">

  <!-- Make sure user has validated their email, else display this. -->
  <!-- Cool message saying "Email sent." Limit amount of times it can be sent. -->
  <?php if ($_SESSION['user_email_verified'] == 0) { ?>
    <div class="row">
      <div class="<?php echo $rowClass; ?>">
        <p class="lead text-danger">Your email has not been verified. You are not eligible for a switch.</p>
        <button class="btn btn-info btn-sm">Resend Email</button>
      </div>
    </div>
    
  <?php } ?>

  <div class="row">
  	<div class="<?php echo $rowClass; ?>">
  		<div class="panel-heading">
  			<h2>Hello, <div style="display: inline-block;" class="text-primary"><?php echo "{$_SESSION['user_name']}" ?></div></h2>
  		</div>
  	</div>
  </div>
  
  <div class="row">
  	<div class="<?php echo $rowClass; ?>">
  		<span><div id="droppedMatches"></div></span>
  	</div>
  </div>

	<!--
	     * Js to go to location to stop jumping, may not be necessary with master edit btn
	      Left/right align
	 -->
	      
	<!-- Should I add confirmations? "Sure you want to change/drop match, sure you want to change. Nice popup. Learn jquery -->

  <!-- Profile Fields Containter -->
 <form id="profileForm" name="profileForm" method="post" action="update.php" onsubmit="return saveChanges()">
  
  	<div id="profileFields" name="profileFields" class="container-fluid col-sm-4 col-sm-offset-4" style="border: 0px solid black; padding: 5px;">
  	
    	<div class="row"> <!-- Major -->
  
      	<div id="majorBeginText" class="<?php echo $beginTextClass; ?>" style="<?php echo $beginTextStyle; ?>">
      		<h4>Your major is</h4>
      	</div>
      	
      	<div id="majorNameText" class="col-sm-5" style="text-align: left; padding-left: 5px;">
      	  <h4>
      	   	<div style="display: inline-block;"><?php echo "{$_SESSION['user_major_name']}."; ?></div>
      		</h4>
      	</div>
    
       <!-- <form id="majorForm" name="majorForm" method="post" action="update.php" onsubmit="return saveMajor();"> -->
        	<div id="majorSpan" style="display: none; padding-left: 5px;" class="col-sm-6">
    				<input type="hidden" name="newMajorId" id="newMajorId" value="">
    				<select id="selectMajor" class="form-control selectpicker" name="major" data-live-search="true" data-size="5">
    					<?php print_majors(); ?>
    				</select>
          </div>
        <!-- </form> -->
      	
    	</div>
    
    	<div class="row"> <!-- Cycle -->
    	
    	  <div id="cycleBeginText" class="<?php echo $beginTextClass; ?>" style="<?php echo $beginTextStyle; ?>">
      		<h4>Your cycle is</h4>
      	</div>
      	
      	<div id="cycleNameText" class="col-sm-5" style="text-align: left; padding-left: 5px;">
      	  <h4>
      	   	<div style="display: inline-block;"><?php echo "{$_SESSION['user_cycle_name']}."; ?></div>
      		</h4>
      	</div>
      	
       <!-- <form id="cycleForm" name="cycleForm" method="post" action="update.php" onsubmit="return saveCycle();"> -->
        	<div id="cycleSpan" style="display: none; padding-left: 5px;" class="col-sm-6">
    				<input type="hidden" name="newCycleId" id="newCycleId" value="">
    				<select id="selectCycle" class="form-control selectpicker" name="cycle" data-live-search="true" data-size="5">
    					<?php if ($_SESSION['user_cycle'] == 1) { ?>
    						<option selected="selected" value="1">Fall-Winter</option>
    						<option value="2">Spring-Summer</option>
    					<?php } else { ?>
    						<option value="1">Fall-Winter</option>
    						<option selected="selected" value="2">Spring-Summer</option>
    					<?php } ?>
    				</select>
          </div>
      <!-- </form> -->
      	
    	</div>
    
    	<div class="row"> <!-- Program -->
    	
    	 <div id="programBeginText" class="<?php echo $beginTextClass; ?>" style="<?php echo $beginTextStyle; ?>">
      		<h4>You will have</h4>
      	</div>
      	
      	<div id="programNameText" class="col-sm-5" style="text-align: left; padding-left: 5px;">
      	  <h4>
      	   	<div style="display: inline-block;"><?php echo "{$_SESSION['user_program_name']}"; ?>.</div>
      		</h4>
      	</div>
      	
        <!-- <form id="programForm" name="programForm" method="post" action="update.php" onsubmit="return saveProgram();"> -->
        	<div id="programSpan" style="display: none; padding-left: 5px;" class="col-sm-6">
    				<input type="hidden" name="newProgramId" id="newProgramId" value="">
    				<select id="selectProgram" class="form-control selectpicker" name="program" data-live-search="true" data-size="5">
    					<?php if ($_SESSION['user_program'] == 1) { ?>
    						<option selected="selected" value="1">1 co-op</option>
    						<option value="2">3 co-ops</option>
    					<?php } else { ?>
    						<option value="1">1 co-op</option>
    						<option selected="selected" value="2">3 co-ops</option>
    					<?php } ?>
    				</select>
          </div>
      <!-- </form> -->
      </div>
      
    </div> <!-- End profile field div begin btn container -->
  
    <div id="editBtns" class="container col-sm-1" style="border: 0px solid black; padding: 5px; padding-top: 8px; margin-left: 20px;">
      <div class="row" style="padding-bottom: 5px;">
        <button id="saveMainBtn" class="btn btn-success btn-sm" style="width: 75%;" onclick="saveChanges()">Save</button>
      </div>
      <div class="row">
        <button type="button" id="editMainBtn" class="btn btn-warning btn-sm" style="width: 75%;" onclick="editProfile()">Edit</button>
      </div>
      <div class="row">
        <button type="button" id=cancelMainBtn class="btn btn-info btn-sm" style="width: 75%;" onclick="cancelChanges()">Cancel</button>
      </div>
    </div>
    
 </form>

    
	<?php
		// Get the latest user_matched status
		if (!$debug_login)
	  	$_SESSION['user_matched'] = mysql_get_var("SELECT matched FROM Users WHERE id = " . $_SESSION['user_id']);

		// If the user has a match, get the match's info and display it.
		if ($_SESSION['user_matched'] == 1) {
			$other_user_data = get_match_info();
			if ($debug_login) {
        $other_user_data[0]['name'] = "John Fry";
        $other_user_data[0]['email'] = "fry@drexel.edu";
			}
		?>
      
    <div class="container col-sm-6 col-sm-offset-3">
      
      <hr>
      <div class="row">
  			<div class="col-sm-6 col-sm-offset-3 text-center">
  				<h3>You have a match!</h3>
  			</div>
      </div>
      <br>
      
      <div class="row">
  			<div class="col-sm-8 col-sm-offset-2 well text-center">
  				<p class="lead">You have matched with <strong class="text-info"> <?php echo $other_user_data[0]['name']; ?></strong>.</p>
  				<br>
  				<p>You can email them at <strong class="text-info"> <?php echo $other_user_data[0]['email']; ?></strong>.</p>
  			</div>
  		</div>
  		
		</div>
	
	<?php } else { // If the user does not have a match tell them they still do not. ?>
			
			<div class="row">
  			<div class="row-fluid col-md-6 col-md-offset-3 text-center">
  				<br><p class="lead">You do not have a match yet, but we will keep looking!</p>
  			</div>
			</div>
			
	<?php } ?>

</div>

<?php
mysql_close($con);
require_once(TEMPLATES_PATH . "/footer.php");
?>


<script type="text/javascript">
	
	// Get some vars from PHP
	window.hasDroppedMatch = "<?php echo $_SESSION['user_dropped_matches']; ?>";
	if (hasDroppedMatch == "") {
		window.hasDroppedMatch = 0;
	}

	window.isMatched = "<?php echo $_SESSION['user_matched']; ?>";

	window.droppedMatches = $("droppedMatches");
	window.droppedMatches.style.display = 'none';

  // Main Profile Buttons
  
  window.editMainBtn = $("editMainBtn");
  window.saveMainBtn = $("saveMainBtn");
  window.cancelMainBtn = $("cancelMainBtn");
  
  window.saveMainBtn.style.display = 'none';
  window.cancelMainBtn.style.display = 'none';

	// Major Window Vars
	
	window.majorSaveBtn = $("majorSaveBtn");
	window.majorEditBtn = $("majorEditBtn");
	window.majorCancelBtn = $("majorCancelBtn");
	window.majorSpan = $("majorSpan");
	window.majorDiv = $("majorNameText");
	window.selectMajor = $("selectMajor");
	window.majorBeginText = $("majorBeginText");

	// Cycle Window Vars
	window.cycleSaveBtn = $("cycleSaveBtn");
	window.cycleEditBtn = $("cycleEditBtn");
	window.cycleCancelBtn = $("cycleCancelBtn");
	window.cycleSpan = $("cycleSpan");
	window.cycleDiv = $("cycleNameText");
	window.selectCycle = $("selectCycle");

	// Program Window Vars
	window.programSaveBtn = $("programSaveBtn");
	window.programEditBtn = $("programEditBtn");
	window.programCancelBtn = $("programCancelBtn");
	window.programSpan = $("programSpan");
	window.programDiv = $("programNameText");
	window.selectProgram = $("selectProgram");

	window.errorClassVals = "alert alert-warning";

  var editProfile = function () {
    
    window.editMainBtn.style.display ='none';
    window.saveMainBtn.style.display = '';
    window.cancelMainBtn.style.display = '';
    
    editMajor();
    editCycle();
    editProgram();
    
    return false;
    
  }
  
  var cancelChanges = function () {
    
    window.editMainBtn.style.display = '';
    window.saveMainBtn.style.display = 'none';
    window.cancelMainBtn.style.display = 'none';
    
    cancelEdit();
    
  }
  
  var saveChanges = function () {
    
    
    saveMajor();
    saveCycle();
    saveProgram();
    
    //alert("Test");
    
  }

	var editMajor = function () {
		
		if (check_if_dropped()) {
			window.majorDiv.style.display = 'none';
			//window.majorDiv.innerHTML = "";
			window.majorSpan.style.display = '';

		// 	window.majorEditBtn.style.display = 'none';
		// 	window.majorSaveBtn.style.display = '';
		// 	window.majorCancelBtn.style.display = '';
			
		//	window.majorBeginText.className = 'col-sm-2 col-sm-offset-3';
			
		}

	}

	var saveMajor = function () {

		var i = window.selectMajor.selectedIndex;
		var newId = window.selectMajor[i].value;

		//document.majorForm.newCycleId.value = newId;
		//document.profileForm.newMajorId.value = newId;
		$("newMajorId").value = newId;

		//return true;
	}

	var editCycle = function () {
		
		if (check_if_dropped()) {
			window.cycleDiv.style.display = 'none';
			window.cycleSpan.style.display = '';

		// 	window.cycleEditBtn.style.display = 'none';
		// 	window.cycleSaveBtn.style.display = '';
		// 	window.cycleCancelBtn.style.display = '';
		}
		
	}

	var saveCycle = function () {

		var i = window.selectCycle.selectedIndex;
		var newId = window.selectCycle[i].value;

		//document.cycleForm.newCycleId.value = newId;
		//document.profileForm.newCycleId.value = newId;
    $("newCycleId").vaule = newId;
    
		//return true;
	}


	var editProgram = function () {
		
		if (check_if_dropped()) {
			window.programDiv.style.display = 'none';
			window.programSpan.style.display = '';

		// 	window.programEditBtn.style.display = 'none';
		// 	window.programSaveBtn.style.display = '';
		// 	window.programCancelBtn.style.display = '';
		}

	}

	var saveProgram = function () {

		var i = window.selectProgram.selectedIndex;
		var newId = window.selectProgram[i].value;

		//document.programForm.newProgramId.value = newId;
		//document.profileForm.newProgramId.value = newId;
		$("newProgramId").value = newId;

		//return true;
	}

	var check_if_dropped = function () {

		if (window.isMatched == 0) {
			//window.droppedMatches.textContent = "";
			window.droppedMatches.style.display = 'none';
			return true;
		}

		// else if (hasDroppedMatch > 0) {
		// 	setTimeout(1000);
		// 	window.droppedMatches.textContent = "You cannot edit your profile and drop another match.";
		// 	window.droppedMatches.className = window.errorClassVals;
		// 	window.droppedMatches.style.display = '';
		// 	return false;
		// }

		else if (window.isMatched == 1) {
			window.droppedMatches.textContent = "By editing your profile, your current match will be dropped. The more matches you drop, the lower you go in the queue."
			window.droppedMatches.className = 'alert alert-warning lead';
			window.droppedMatches.style.display = '';
			return true;
		}
		else {

		}

	}

	var cancelEdit = function () {

		//if (field == "major") {
		//window.majorSaveBtn.style.display = 'none';
			window.majorSpan.style.display = 'none';
			window.majorDiv.style.display = 'inline-block';
		// 	window.majorCancelBtn.style.display = 'none';
		// 	window.majorEditBtn.style.display = '';
		//}
		//else if (field == "cycle") {
	  //window.cycleSaveBtn.style.display = 'none';
			window.cycleSpan.style.display = 'none';
			window.cycleDiv.style.display = 'inline-block';
		// 	window.cycleCancelBtn.style.display = 'none';
		// 	window.cycleEditBtn.style.display = '';
		//}
		//else if (field == "program") {
		//window.programSaveBtn.style.display = 'none';
			window.programSpan.style.display = 'none';
			window.programDiv.style.display = 'inline-block';
		// 	window.programCancelBtn.style.display = 'none';
		// 	window.programEditBtn.style.display = '';
		//}

		window.droppedMatches.style.display = 'none';
	}

</script>


<!-- To Do For User Page 1

- Ability to change information (cycle, major, 4/5 year)
- Other misc. I guess