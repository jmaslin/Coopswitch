<?php
include('header.php')


?>

<br />
<div class="container-fluid">

<?php

$rowClass = "row-fluid col-md-6 col-md-6-offset-3 col-sm-6 col-sm-offset-3 text-center";

include_once('connect.php');

// Get all the peoples not matched...
$query="SELECT * FROM Users WHERE matched = 0";
$result=mysql_query($query);
$num=mysql_num_rows($result); // ...It's this many

?>

  <div class="<?php echo $rowClass; ?>">
    <p>There are <?php echo $num; ?> people who have not been matched. <br> Will now attempt to match.</p>
    <?php if ($num == 0) echo "Hooray!" ?>
  </div>

<?php

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

      echo "<br><br><hr>";

      /*

      Main issue: People are showing up twice. Need to sync both arrays or remove people. 
                  Make sure they cannot exist in second somehow?

      */

      for ($x = 0; $x < $index; $x++) // Why doesn't count() work for array?
        {
          // Select people with the same major who are not the person we are searching for. Forgot the matched = 0

         // $IdsGoneThrough = array(); // Save the Ids gone through and do not let them be compared again? Wait no .

          $query = "SELECT * FROM Users WHERE matched = 0 AND major = " . $users_not_matched[$x]['major'] . " AND id != " . $users_not_matched[$x]['id'] . " AND cycle != " . $users_not_matched[$x]['cycle'] . " AND num_year_program = " . $users_not_matched[$x]['num_year_program'] . "";
          $result = mysql_query($query);

          //  
          if ((mysql_num_rows($result) > 0) && ($users_not_matched[$x]['matched'] != 1)) // We found people who match the guy inside $users_not_matched. NEED TO UPDATE $users_not_matched after a match is made. Remove from array after matched.
            {
              //echo "<hr><em>Looks like we found a match!</em><br>";
              $matched_user_data = array(); // Reset the array.

              /* For right now, only need first row since matches are done first-come, first-serve. Do not need to store all matches, just the first. Loop not needed. */
              //$index = 0;
              //while ($row = mysql_fetch_array($result))
              //{
              $row = mysql_fetch_array($result);
              $matched_user_data[0] = $row; // Save the user's info in an array. Temp for now to do stuff easier
              //}

              // Testing
              //echo $users_not_matched[$x]['id'] . " and " . $matched_user_data[0]['id'] . "<br>";

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

              //echo $users_not_matched[$x]['matched'] . " and " . $matched_user_data[0]['matched'] . "<br>";

              // Insert into the database. 
              $query = sprintf("INSERT INTO Matches (userA, userB) VALUES (" . $users_not_matched[$x]['id'] . ", " . $matched_user_data[0]['id'] . ")");
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

            //  unset($users_not_matched[$x]);

            } // End If Statement (If match)
          else
            {
            }
        } // End For Loop
    } // End Main If Statement (If there is even a reason to run through all this code)

  mysql_close($con);

?>

  <div class="<?php echo $rowClass; ?>">
    <p>There were <?php echo "$matches"; ?> matches made!</p><br>
    <p>There are <?php echo $num - $matches; ?> people who still need to be matched.</p>
  </div>

</div>

<br />
<?php
include('footer.php')

// Add to Matches table, set matched val to 1, connect with each other.
// Matches table: id, personA id, personB id, isFinished val, date.

// After a match is made it will show up on users profile and they will get an email.

?>