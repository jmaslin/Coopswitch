<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/config.php");
require_once(TEMPLATES_PATH . "/header.php"); 
?>

<div class="container-fluid">


    <?php // include_once('stats_top.php'); ?>
    <?php require_once(TEMPLATES_PATH . "/stats_top.php"); ?>
   
<!-- Stats for users who receive matches as dropped_matches increase somehow. -->


<!-- Major specific stats for everything 
     Get as specific as possible, enter major to get stats for, etc.
     Make line graphs that go over time, etc. (ADD MATCHED DATE COLUMN TO Matches TABLE FOR THIS TOO)
-->

</div>

<?php require_once(TEMPLATES_PATH . "/footer.php"); ?>