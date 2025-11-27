<?php
require_once 'core/functions.php';

startSession();

if (isLoggedIn()) {
    header('Location: pages/dashboard.php');
} else {
    header('Location: auth/login.php');
}
exit;
