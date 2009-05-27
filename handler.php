<?php
session_start();
// Stop any calls not from the main page.
if (empty($_POST) && empty($_GET)) {
   header("Location: ./");
   exit;
}

// Load PageManagement Class.
require_once 'lib/pm.php';

// Create a new PageManagement instance.
$pm = new PageManagement();

switch ($_GET['func']) {
   case 'menu':
      echo $pm->buildMenu();
      break;

   case 'load':
      echo json_encode($pm->loadContent($_GET['id']));
      break;
}


// Make sure you're not trying to create when you want to update.
if (!empty($_POST['id'])) {
   $_POST['func'] = 'update';
}

// Handle which function gets called.
switch ($_POST['func']) {
   case 'post':
      unset($_POST['func']);
      $id = $pm->postNewContent($_POST);

      if ($id) {
         $message = "Content create.";
      } else {
         $message = "Content NOT created. Try again in a few minutes.";
      }
      
      echo json_encode(array("id" => $id, "message" => $message));

      break;

   case 'update':
      unset($_POST['func']);
      $id = $pm->updateContent($_POST);

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
   
   case 'login':
      unset($_POST['func']);
      echo (string)$pm->login($_POST['password']);
      if ($pm->login($_POST['password'])) {
         $_SESSION['l_o_g_g_e_d__i_n'] = true;
      } else {
         // Unset all of the session variables.
         $_SESSION = array();

         // If it's desired to kill the session, also delete the session cookie.
         // Note: This will destroy the session, and not just the session data!
         if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
         }

         // Finally, destroy the session.
         session_destroy();
      }
      // echo json_encode(array("message" => $message));
      break;
}
