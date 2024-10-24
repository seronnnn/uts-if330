<?php
session_start();
include('config.php');
include('functions.php');

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}