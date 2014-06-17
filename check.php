<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/config.php");
require_once(TEMPLATES_PATH . "/header.php");
//include('mail.php');
/* Have a cron job run this page every minute so checks are always done. FOR PRODUCTION SERVER. */

if (isset($msg))
  $msg = test_input($_GET['msg']);

?>

<script>
$(function(){
    $('#generate_records').click(function(){
        $.ajax({
            url: 'testdb.php',
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
            url: 'emptydb.php',
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

<br />
<div class="container-fluid">

<?php

$rowClass = "row-fluid col-md-6 col-md-6-offset-3 col-sm-6 col-sm-offset-3 text-center";

include_once('connect.php');

// Get all the peoples not matched...
$query="SELECT * FROM Users WHERE matched = 0 ORDER BY dropped_matches ASC";
$result=mysql_query($query);
$num=mysql_num_rows($result); // ...It's this many

?> 
  
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
    <p>There are <?php echo $num; ?> people who have not been matched. <br> Will now attempt to manually match.</p>
    <?php if ($num == 0) echo "Hooray, everyone is matched!<br><br>" ?>

    <button id="generate_records" type="button" class="btn btn-warning">Generate Records</button>
    <button id="delete_records" type="button" class="btn btn-danger">Delete Records</button>
  </div>

<?php
  
  // I need to order both arrays by number of dropped matches? Less = first. 

  if ($num > 0) // If there are people not matched, run this.
   {
      $users_not_matched = array(); // Array of those who are not matched.
      $matches = 0; // Lets see how many matches are made this round. Maybe save value later? Stats, stats, stats.

      $index = 0;
      while ($row = mysql_fetch_array($result))
      {
        $users_not_matched[$index] = $row; // Save the users into the array.
        $index++; // KIND OF NEED THIS...
      }

      echo "<br><br>";
      /*

      Show what majors do not have matches. Fancy bar/circle graph?    

      */

      for ($x = 0; $x < $index; $x++) // Why doesn't count() work for array?
        {
          // Select people with the same major who are not the person we are searching for. Forgot the matched = 0

         // $IdsGoneThrough = array(); // Save the Ids gone through and do not let them be compared again? Wait no .

          $query = " SELECT * FROM Users WHERE matched = 0 AND major = " . $users_not_matched[$x]['major'] . 
                   " AND id != " . $users_not_matched[$x]['id'] . " AND cycle != " . $users_not_matched[$x]['cycle'] . 
                   " AND num_year_program = " . $users_not_matched[$x]['num_year_program'] .
                   " ORDER BY dropped_matches ASC";

          $result = mysql_query($query);

          //  
          if ((mysql_num_rows($result) > 0) && ($users_not_matched[$x]['matched'] != 1)) // We found people who match the guy inside $users_not_matched. NEED TO UPDATE $users_not_matched after a match is made. Remove from array after matched.
            {
              $matched_user_data = array(); // Reset the array.

              /* For right now, only need first row since matches are done first-come, first-serve. Do not need to store all matches, just the first. Loop not needed. */
              //$index = 0;
              //while ($row = mysql_fetch_array($result))
              //{
              $row = mysql_fetch_array($result);
              $matched_user_data[0] = $row; // Save the user's info in an array. Temp for now to do stuff easier
              //}

              // Testing

              /* Put the users into the Matches table and set equaled to matched. */

              // Update both users to be matched.
              $query = "UPDATE Users SET matched = 1 WHERE id = " . $users_not_matched[$x]['id'] . " OR id = " . $matched_user_data[0]['id'] . ""; 
              $users_not_matched[$x]['matched'] = 1;

              // Find where $matched_user_data[0] is in $users_not_matched and set matched = 1 so that it stops dupes.
              // Learn how to foreach loops and MAKE MORE EFFICIENT.

              for ($i = 0; $i < count($users_not_matched); $i ++)
              {
                if ($users_not_matched[$i]['id'] == $matched_user_data[0]['id'])
                  {
                    $users_not_matched[$i]['matched'] = 1;
                    break;
                  }
              }

              //$query = "INSERT INTO Users (id, matched) VALUES (" . $users_not_matched[$x]['matched'] . ",1),(" . $matched_user_data[0]['id'] . ",1)";
              $result = mysql_query($query);


              /* *** Matches db need to add date matched, date completed, major val (to compare to when change major in profile, also for stats [ie. most popular majors]) */

              // Insert into the Matches database.
              $query = sprintf("INSERT INTO Matches (userA, userB, major, isFinished, date_matched) VALUES (" . $users_not_matched[$x]['id'] . ", " . $matched_user_data[0]['id'] . ", " . $users_not_matched[$x]['major'] . ", 0, " .'date("Y-m-d H:i:s")' . " )");
              //$query = sprintf("INSERT INTO Matches (userA, userB) VALUES (" . $users_not_matched[$x]['id'] . ", " . $matched_user_data[0]['id'] . ")");
              $result = mysql_query($query);

              // Grab the Id of the match from Matches table
              $query = mysql_query("SELECT id FROM Matches WHERE userA= " . $users_not_matched[$x]['id'] . " OR userB= " . $matched_user_data[0]['id'] . "");
              $result = mysql_fetch_array($query);
              $newMatchId = $result['id'];
              //echo "Match ID: " . $newMatchId . "<br>";

              // Add the Matched ID to the Users
              $query = "UPDATE Users SET Matches_id = " . $newMatchId . " WHERE id = " . $users_not_matched[$x]['id'] . " OR id = " . $matched_user_data[0]['id'] . "";
              $result = mysql_query($query);
          
              $matches ++;

              // Send names and emails to mail script to mail users that they have been matched.
              mail_matched_users($users_not_matched[$x]['name'], $users_not_matched[$x]['email'], $matched_user_data[0]['name'], $matched_user_data[0]['email']);

              // if ($debug)
              // {
              //    echo "<hr><em>Looks like we found a match!</em><br>";
              //    // IDs of matched people.
              //    echo $users_not_matched[$x]['id'] . " and " . $matched_user_data[0]['id'] . "<br>";
              //    // Is the matched value set?
              //    echo $users_not_matched[$x]['matched'] . " and " . $matched_user_data[0]['matched'] . "<br>";

              // }

              // Need to update user sessions if logged in and a match happens.
              // Best way to do that?


            } // End If Statement (If match)
          else
            {
            }
        } // End For Loop
    } // End Main If Statement (If there is even a reason to run through all this code)




?>

  <div class="<?php echo $rowClass; ?>">
    <br>

    <?php if ($matches > 0) { ?>
    <p class="lead">There were <?php echo "$matches"; ?> matches made!</p>
    <?php } ?>
    
    <p>There are still <?php echo $num - ($matches*2); ?> people who still need to be matched.</p>

    <br>
    <?php 

    $query = "select major from Matches ORDER BY id DESC LIMIT 10";
    $result = mysql_query($query) OR die(mysql_error());
    $row = mysql_fetch_array($result);

    $last_matches = array();
    $index = 0;

    ?>

    <ul class="list-group">
          <h2 class="list-group-item-heading">Last 10 Matches</h2>
    <?php

    while ($row = mysql_fetch_array($result))
    {
      $last_matches[$index] = $row; // Save the users into the array.
      $last_matches[$index]['major_name'] = mysql_get_var("SELECT major_long from Majors WHERE id = " . $last_matches[$index]['major']);
      $last_matches[$index]['userA'] = mysql_get_var("SELECT userA FROM Matches WHERE id = " . $last_matches[$index]['major']);
      $last_matches[$index]['userB'] = mysql_get_var("SELECT userB FROM Matches WHERE id = " . $last_matches[$index]['major']);
      ?>
      <li class="list-group-item"><?php echo $last_matches[$index]['major_name']; ?></li>
      <?php 
      // Show the IDs of the matched users. 

      if ($debug) {
        echo '<li class="list-group-item"> ' . $last_matches[$index]['userA'] . ' ' . $last_matches[$index]['userB'] . '</li>';
      }
      $index++; // KIND OF NEED THIS...
    }


    ?>
    
  </div>

</div>

<br />
<?php

mysql_close($con);

require_once(TEMPLATES_PATH . "/footer.php");
// Add to Matches table, set matched val to 1, connect with each other.
// Matches table: id, personA id, personB id, isFinished val, date.

// After a match is made it will show up on users profile and they will get an email.

?>