<?php
session_start();
// Stop any calls not from the main page.
if (empty($_REQUEST)) {
   header("Location: ./");
   exit;
}

// Load PageManagement Class.
require_once 'lib/pm.php';

// Create a new PageManagement instance.
$pm = new PageManagement();

// Handle which function gets called.
switch ($_REQUEST['func']) {
   case 'post':
      // Make sure you're not trying to create when you want to update.
      if (!empty($_POST['id'])) {
         $_REQUEST['func'] = 'update';
         // break;
      } else {
         unset($_POST['func']);
         $id = $pm->postNewContent($_POST);

         if ($id) {
            $message = "Content create.";
         } else {
            $message = "Content NOT created. Try again in a few minutes.";
         }

         echo json_encode(array("id" => $id, "message" => $message));

         break;
      }

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

   case 'delete':
      unset($_POST['func']);
      if ($pm->deleteContent((int)$_POST['id'])) {
         $message = "Content deleted.";
         $pass = true;
      } else {
         $message = "Content NOT deleted. Try again in a few minutes.";
         $pass = false;
      }

      echo json_encode(array("pass" => $pass, "message" => $message));
      break;

   case 'menu':
      if ($_GET) {
         echo $pm->buildMenu();
         break;
      }

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
      break;

   case 'load':
      $item = $pm->loadContent($_GET['id']);

      if ($item) {
         $message = "Content loaded.";
      } else {
         $message = "Content NOT loaded. Try again in a few minutes.";
      }

      echo json_encode(array('item' => $item, 'message' => $message));
      break;
}