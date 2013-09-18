<?php
	require('../private/cred.php');
	session_start();
    session_unset();
    session_destroy();
    returnhome();
?>
