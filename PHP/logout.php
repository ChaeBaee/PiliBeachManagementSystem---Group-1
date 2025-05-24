<?php
session_start();
session_unset();
session_destroy();


header("Location: /Proj/PiliBeachManagementSystem---Group-1/PHP/home.php");
exit();
