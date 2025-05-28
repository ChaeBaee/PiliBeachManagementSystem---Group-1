<?php
session_start();
session_unset();
session_destroy();


header("Location: /Proj/Admin/Login/front/login.php");
exit();