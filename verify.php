<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/config.php");
require_once(TEMPLATES_PATH . "/header.php");

$email = test_input($_GET['a']);
$submitHash = test_input($_GET['b']);

include_once(FUNCTION_PATH . "/connect.php");

$query = 'SELECT * FROM Users WHERE email = "' . $email . '"';
$result = mysql_query($query);

$user = array();
$user = mysql_fetch_array($result);

$combo = $user['name'] . $user['email'] . $user['cycle'];
$userHash = hash('sha256', $combo);


if ($user['verified'] == 1) {
	header("Location: error.php?msg=5");	
}

else if ($userHash == $submitHash) {

	$sql = 'UPDATE Users SET verified = 1 WHERE email = "' . $email . '"';
    $result = mysql_query($sql);

    if (!$result) {
        header("Location: error.php?msg=7");    
        die();
    }

    //$_SESSION['user_email_verified'] = 1;

    if (isset($_SESSION['login'])) {

    	if ($_SESSION['user'] == $email) {
    		$_SESSION['user_email_verified'] = 1;
    	}
    }

    if (isset($_SESSION['login']) && $_SESSION['user'] == $email) {
        header("Location: account.php");
    }
    else {
        header("Location: index.php");
    }
}

else {
	header("Location: error.php?msg=6");	
}

mysql_close($con);

?>

<!-- Check the following conditions:

- Confirm email not logged in
- Confirm email logged in wrong account
- Confirm email logged in same account

- Try and match with unconfirmed email
- Try same conditions with confirmed email

- Change profile confirm match drops correctly (update.php acting weird)

- Create test accounts with pre-registered email.

-->