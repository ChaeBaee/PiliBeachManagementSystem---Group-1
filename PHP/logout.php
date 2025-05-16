<?php
session_start();        
session_unset();        
session_destroy();       


header("Location: /Proj/PHP/home.php");
exit();
