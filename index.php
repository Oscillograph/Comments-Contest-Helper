<?php
define('CCH', true);
define('CCH_VERSION', '1.0');
define('CCH_BASE_DIR', __DIR__);

session_start();

// basic html vars
$html = [];
$html['content'] = '';
$html['meta'] = '';
$html['title'] = '';
$html['header'] = '';
$html['error'] = '';
$html['footer'] = '';
$html['scripts'] = '';

// ignition
include './common.php';
include './auth.php'; 

// backend
include './include/backend.php';

// render page
include './include/frontend.php';

?>