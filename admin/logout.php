<?php
session_start();
// Clear all admin session variables
unset($_SESSION['admin_user']);
unset($_SESSION['admin_user_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_role']);
unset($_SESSION['admin_full_name']);
session_destroy();
header('Location: login.php');
exit;
