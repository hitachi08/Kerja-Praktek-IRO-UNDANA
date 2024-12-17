<?php

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
  case 'status':
    include 'status_page.php';
    break;
  case 'review':
    include 'review_page.php';
    break;
  default:
    include 'home_page.php';
    break;
}
?>