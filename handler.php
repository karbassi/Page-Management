<?php

// Stop any calls not from the main page.
// if (!$_POST || !$_REQUEST) {
//    header("Location: ./");
//    exit;
// }

// Load PageManagement Class.
require_once 'lib/pm.php';

// Create a new PageManagement instance.
$pm = new PageManagement();

// Make sure you're not trying to create when you want to update.
if (!empty($_POST['id'])) {
   $_POST['func'] = 'update';
}

// Handle which function gets called.
switch ($_POST['func']) {
   case 'post':
      unset($_POST['func']);
      $id = $pm->post($_POST);

      if ($id) {
         $message = "Content create.";
      } else {
         $message = "Content NOT created. Try again in a few minutes.";
      }
      
      echo json_encode(array("id" => $id, "message" => $message));

      break;

   case 'update':
      unset($_POST['func']);
      $id = $pm->update($_POST);

      if ($id) {
         $message = "Content updated.";
      } else {
         $message = "Content NOT updated. Try again in a few minutes.";
      }
      
      echo json_encode(array("id" => $id, "message" => $message));
      break;
   
   case 'menu':
      unset($_POST['func']);
      if ($pm->updateMenu($_POST['data'])) {
         $message = "Menu order updated.";
      } else {
         $message = "Menu order NOT updated. Try again in a few minutes.";
      }
      
      echo json_encode(array("message" => $message));
      

      break;

   default:
      echo '<pre>';
      $pm->categories();
      var_dump($pm->a);
      break;
}
