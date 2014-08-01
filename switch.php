<?php
require_once(__DIR__ . "/resources/config.php");
require_once(TEMPLATES_PATH . "/header.php");
//include('mail.php');
/* Have a cron job run this page every minute so checks are always done. FOR PRODUCTION SERVER. */

if (isset($_GET['msg'])) {
  $msg = test_input($_GET['msg']);
}

if (isset($_GET['check'])) {
  $check = test_input($_GET['check']);
}
else {
  $check = 0;
}

$matches = 0;

?>


<script>
$(function(){
    $('#generate_records').click(function(){
        $.ajax({
            url: '/resources/dev/testdb.php',
            success: function(data) { // data is the response from your php script
                // This function is called if your AJAX query was successful
                //alert("Response is: " + data);
                window.location.href = 'check.php?msg=1';
            },
            error: function() {
                // This callback is called if your AJAX query has failed
                alert("Error!");
            }
        });
    });
});

$(function(){
    $('#delete_records').click(function(){
        $.ajax({
            url: '/resources/dev/emptydb.php',
            success: function(data) { // data is the response from your php script
                // This function is called if your AJAX query was successful
                //alert("Response is: " + data);
                window.location.href = 'check.php?msg=2';
            },
            error: function() {
                // This callback is called if your AJAX query has failed
                alert("Error!");
            }
        });
    });
});

</script>

<div class="container-fluid">

<?php

$rowClass = "col-sm-6 col-sm-offset-3 text-center";

include(FUNCTION_PATH . "/connect.php");

// Filter out not verifieds.

// How many users have a match:
$query = "SELECT * FROM Users WHERE matched = 1";
$usersMatched = mysql_num_rows(mysql_query($query));

// How many users do not have a match:
$query="SELECT * FROM Users WHERE matched = 0 AND verified = 1 AND withdraw != 1 ORDER BY dropped_matches ASC, new_date ASC";
$result=mysql_query($query);

if ($result) {
  $num=mysql_num_rows($result); // ...It's this many
  $notMatched = $num; 
}
else {
  $notMatched = 0;
  $num = 0;
  $matches = 0;
}

?>

  <div class="row">
    <div class="<?php echo $rowClass; ?>">
      <?php
      if (isset($msg)) {
        if ($msg == 1)
            echo "<strong>Records generated.</strong><br><br>";
        else if ($msg == 2)
            echo "<strong>Database cleared.</strong><br><br>";

        $msg = 0;
      }

      ?>
      <p class="lead">
      <?php 
      if ($notMatched+$usersMatched > 0) {
        $percentNotMatched = $notMatched/($notMatched+$usersMatched)*100;
        $percentNotMatched = number_format((float)$percentNotMatched, 2, '.', '');
      }

        if ($num == 0) echo "No switches need to be made.<br><br>";
        else { ?>
            There are still <span class="text-info"><?php echo $notMatched ?></span> people who still need to be switched, or <span class="text-info"><?php echo $percentNotMatched ?>%</span> of verified users.
      </p>
        <?php if ($check) { ?>
          <p class="lead">
          <!-- Will now attempt to manually match. -->
          </p>
        <?php } ?>
      <?php } ?>

      <?php if ($debug_db) { ?>
      <button id="generate_records" type="button" class="btn btn-warning">Generate Records</button>
      <button id="delete_records" type="button" class="btn btn-danger">Delete Records</button>
      <?php } ?>
    </div>
  </div>

<?php
  
  // I need to order both arrays by number of dropped matches? Less = first.

  if ($num > 0 && $check == 1) {// If there are people not matched, run this.
   
      $users_not_matched = array(); // Array of those who are not matched.
      $matches = 0; // Lets see how many matches are made this round. Maybe save value later? Stats, stats, stats.

      $index = 0;
      while ($row = mysql_fetch_array($result)) {
  
        $users_not_matched[$index] = $row; // Save the users into the array.
        $index++; // KIND OF NEED THIS...
      }

      //echo "<br><br>";

      for ($x = 0; $x < $index; $x++) {// Why doesn't count() work for array?

      /* 
      Select people with the same major who are not the person we are searching for.
      Criteria: Not switched, email verified, not withdrawn, same major, not the same ID, opposite cycle.
      Order: First by dropped matches, then by new_date (users who have not redrawn have no new date).
      */

          $query = " SELECT * FROM Users WHERE matched = 0 AND verified = 1 AND withdraw != 1 AND major = " . $users_not_matched[$x]['major'] .
                   " AND id != " . $users_not_matched[$x]['id'] . " AND cycle != " . $users_not_matched[$x]['cycle'] .
                   " AND num_year_program = " . $users_not_matched[$x]['num_year_program'] .
                   " ORDER BY dropped_matches ASC, new_date ASC";

          $result = mysql_query($query);

          if ((mysql_num_rows($result) > 0) && ($users_not_matched[$x]['matched'] != 1)) {// We found people who match the guy inside $users_not_matched. NEED TO UPDATE $users_not_matched after a match is made. Remove from array after matched.
        
              $matched_user_data = array(); // Reset the array.

              $row = mysql_fetch_array($result);
              $matched_user_data = $row; // Save the user's info in an array. 

              /* Put the users into the Matches table and set equaled to matched. */

              // Update both users to be matched.
              $query = "UPDATE Users SET matched = 1 WHERE id = " . $users_not_matched[$x]['id'] . " OR id = " . $matched_user_data['id'] . "";
              $users_not_matched[$x]['matched'] = 1;

              // Find where $matched_user_data is in $users_not_matched and set matched = 1 so that it stops dupes.
              // Learn how to foreach loops and MAKE MORE EFFICIENT.
              for ($i = 0; $i < count($users_not_matched); $i ++) {
                if ($users_not_matched[$i]['id'] == $matched_user_data['id']) {
                    $users_not_matched[$i]['matched'] = 1;
                    break;
                  }
              }

              //$query = "INSERT INTO Users (id, matched) VALUES (" . $users_not_matched[$x]['matched'] . ",1),(" . $matched_user_data['id'] . ",1)";
              $result = mysql_query($query);


              /* *** Matches db need to add date matched, date completed, major val (to compare to when change major in profile, also for stats [ie. most popular majors]) */

              // Insert into the Matches database.
              $query = sprintf("INSERT INTO Matches (userA, userB, major, isFinished, date_matched) VALUES (" . $users_not_matched[$x]['id'] . ", " . $matched_user_data['id'] . ", " . $users_not_matched[$x]['major'] . ", 0, " .'date("Y-m-d H:i:s")' . " )");
              //$query = sprintf("INSERT INTO Matches (userA, userB) VALUES (" . $users_not_matched[$x]['id'] . ", " . $matched_user_data['id'] . ")");
              $result = mysql_query($query);

              // Grab the Id of the match from Matches table
              $query = mysql_query("SELECT id FROM Matches WHERE userA= " . $users_not_matched[$x]['id'] . " OR userB= " . $matched_user_data['id'] . "");
              $result = mysql_fetch_array($query);
              $newMatchId = $result['id'];
              //echo "Match ID: " . $newMatchId . "<br>";

              // Add the Matched ID to the Users
              $query = "UPDATE Users SET Matches_id = " . $newMatchId . " WHERE id = " . $users_not_matched[$x]['id'] . " OR id = " . $matched_user_data['id'] . "";
              $result = mysql_query($query);
          
              $matches ++;

              // Send names and emails to mail script to mail users that they have been matched.
              if (!$send_match_mail) {
                //mail_matched_users($users_not_matched[$x]['name'], $users_not_matched[$x]['email'], $matched_user_data['name'], $matched_user_data['email']);
              }


          } // End If Statement (If match)

        } // End For Loop
    } // End Main If Statement (If there is even a reason to run through all this code)

?>

<?php 

  $query = "select * from Matches ORDER BY id DESC LIMIT 10";
  $result = mysql_query($query) OR die(mysql_error());

  $last_matches = array();
  $index = 0;

  while ($row = mysql_fetch_array($result)) {

      $last_matches[$index] = $row; // Save the users into the array.
      $last_matches[$index]['major_name'] = mysql_get_var("SELECT major_long from Majors WHERE id = " . $last_matches[$index]['major']);

      if ($index == 0) {
        $lastMatch = $last_matches[$index]['date_matched'];
      }
      
      $index++;
  }

?>

<?php if ($check == 1) { ?>

  <div class="modal fade" id="checkmatches" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h2 class="modal-title">Manual Switch Check</h2>
          </div>
          <div class="modal-body">

              <?php 

                if ($matches > 0) {
                  echo '<p class="lead">There were ' . $matches . ' switches made!</p>';
                } 
                else {
                  echo '<p class="lead">No switches were made.</p>';
                  if (isset($lastMatch)) { 
                    echo '<p class="lead">The last switch was on <span class="text-info">' . $lastMatch . '</span>.</p>'; 
                  }
                } 

              ?>

          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
  </div>
 
<?php } ?>

  <!-- Show last 10 switches completed. -->
  <div class="row" style="margin-top: 35px;">
    <div class="<?php echo $rowClass; ?>">

      <ul class="list-group">
            <h2 class="list-group-item-heading" style="padding-bottom: 10px;">Last 10 Switches</h2>

              <?php

                $max = sizeof($last_matches);
                if ( $max == 0) { 
                echo '<p class="lead">No recent switches found.</p>';
                }
                else {

                  for ($x = 0; $x < $max; $x++) {
                    echo '<li class="list-group-item lastMatch" data-toggle="tooltip" data-trigger="hover" data-placement="right" title="' . $last_matches[$x]['date_matched'] . '">' . $last_matches[$x]['major_name'] . '</li>';
                    if ($debug) {
                      echo '<li class="list-group-item"> ' . $last_matches[$x]['id'] .' ' . $last_matches[$x]['userA'] . ' ' . $last_matches[$x]['userB'] . '</li>'; 
                    }
                  }

                }

              ?>
      
      </ul>
    </div>
  </div>

  <div class="row">
    <div class="<?php echo $rowClass; ?>">

      <a href="?check=1"><button class="btn">Manual Switch Check</button></a>

    </div>
  </div>

</div>

<br />

<?php
  mysql_close($con);
  require_once(TEMPLATES_PATH . "/footer.php");
?>


<script>

  manualCheck = "<?php echo $check; ?>";

  if (manualCheck == 1) {
      $('#checkmatches').modal('show');
  }

  $('.lastMatch').tooltip();

</script>