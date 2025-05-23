<?php
session_start();
session_unset();
session_destroy();


header("Location: /Proj/PiliBeachManagementSystem---Group-1/Admin/login.php");
exit();