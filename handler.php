<?php

require_once 'lib/pm.php';

switch ($_POST['func']) {
   case 'post':
      unset($_POST['func']);
      $pm = new PageManagement();
      $pm->post($_POST);
      break;
   
   default:
      # code...
      break;
}
// var_dump($_POST);
// $function = $_POST['func'];
// unset($_POST['func']);
// var_dump($_POST);