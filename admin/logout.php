<?php
session_start();
session_destroy();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
redirect(SITE_URL . '/admin/login.php');
