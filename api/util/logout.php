<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: /pollosBrothers/html/index.html");
