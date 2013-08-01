<?php
	require('/home/faver/bin/cred.php');
	session_start();
    session_unset();
    session_destroy();
    returnhome();
?>
