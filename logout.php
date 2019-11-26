<?php
require('function.php');
unset($_SESSION['login_date']);
unset($_SESSION['login_limit']);
unset($_SESSION['user_id']);

debug(print_r($_SESSION,true));
header('Location:login.php');
