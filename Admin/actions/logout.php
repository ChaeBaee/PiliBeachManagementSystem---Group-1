<?php
session_start();
session_unset();
session_destroy();


header("Location: /Proj/Admin/login.php");
exit();