<?php
session_start();
session_unset();
session_destroy();


header("Location: ../PHP/home.php");
//header("Location: /Proj/PiliBeachManagementSystem---Group-1/PHP/home.php");

exit();
