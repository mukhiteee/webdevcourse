<?php
session_start();
header('Content-Type: application/json');
require_once 'backend/config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: pages/dashboard.php');
} else {
    header('Location: index.html');
}