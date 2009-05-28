<?php
session_start();
if (!isset($_SESSION['l_o_g_g_e_d__i_n'])) {
   header("Location: login.php");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<!-- Because HTML 5 will be the future! -->
<html lang="en">
   <head>
      <title>Page Manager</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta http-equiv="Content-Language" content="en-us">
      <!-- Green favicon because it's sexy! -->
      <link rel="icon" href="data:image/png,%89PNG%0D%0A%1A%0A%00%00%00%0DIHDR%00%00%00%10%00%00%00%10%08%02%00%00%00%90%91h6%00%00%00%19IDAT(%91c%0C%DD%10%C5%40%0A%60%22I%F5%A8%86Q%0DCJ%03%00dy%01%7F%0C%9F0%7D%00%00%00%00IEND%AEB%60%82" type="image/png">

      <!-- Let's work from scratch, shall we? -->
      <link rel="stylesheet" type="text/css" media="all" href="css/reset.css">
      <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
      <link rel="stylesheet" type="text/css" media="all" href="css/notification.css">

      <!--  jQuery -->
      <script type="text/javascript" src="lib/jquery/jquery.pack.js"></script>

      <!-- iNestedSortable -->
      <script type="text/javascript" src="lib/jquery/interface-1.2.js"></script>
      <script type="text/javascript" src="lib/jquery/jquery.inestedsortable.js"></script>
      <script type="text/javascript" src="lib/jquery/jquery.nestedsortablewidget.js"></script>

      <!-- Notification -->
      <script type="text/javascript" src="lib/jquery/jquery.notification.js"></script>

      <!-- Local JS -->
      <script type="text/javascript" src="lib/jquery/local.js"></script>
   </head>
   <body>

      <div id="navigation">
         <div class="inner">
            <div id="createButton">Create New</div>
            <div id="pageListing"><div></div></div>
         </div>
      </div>
      <!-- #navigation -->

      <div id="main">
         <div class="inner">
            <fieldset>
               <legend>Post New Content</legend>
               <div id="formElement">
                  <input type="hidden" name="ID" value="" id="ID">
                  <label for="title">Title</label><input type="text" name="title" value="" id="title" maxlength="100">
                  <label for="content">Content</label><textarea name="content" id="content" rows="8" cols="40"></textarea>

                  <div id="postOptions">
                     <label for="status">Status</label>
                     <select name="status" id="status">
                        <option value="draft" selected="selected">Draft</option>
                        <option value="private">Private</option>
                        <option value="public">Public</option>
                     </select>

                     <label for="type">Type</label>
                     <select name="type" id="type">
                        <option value="page" selected="selected">Page</option>
                        <option value="blog">Blog</option>
                     </select>

                     <label for="display">Display</label>
                     <select name="display" id="display">
                        <option value="shown" selected="selected">Shown</option>
                        <option value="hidden">Hidden</option>
                     </select>
                  </div>
                  <!-- #postOptions -->

                  <input type="button" value="Delete" id="deleteButton" name="deleteButton" disabled="disabled"> <input type="submit" value="Post" id="postButton" name="postButton">

                  <br class="clear">
               </div>
            </fieldset>
         </div>
      </div>
      <!-- #main -->
  </body>
</html>
