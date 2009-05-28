<?php
session_start();

// Already logged in.
if (isset($_SESSION['l_o_g_g_e_d__i_n'])) {
   header("Location: ./");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<!-- Because HTML 5 will be the future! -->
<html lang="en">
   <head>
      <title>Page Manager</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta http-equiv="Content-Language" content="en-us">
      <!-- RED favicon because you need to login! -->
      <link rel="icon" href="data:image/png,%89PNG%0D%0A%1A%0A%00%00%00%0DIHDR%00%00%00%10%00%00%00%10%08%02%00%00%00%90%91h6%00%00%00%19IDAT(%91c%BCd%AB%C2%40%0A%60%22I%F5%A8%86Q%0DCJ%03%00%DE%B5%01S%07%88%8FG%00%00%00%00IEND%AEB%60%82" type="image/png">

      <!-- Let's work from scratch, shall we? -->
      <link rel="stylesheet" type="text/css" media="all" href="css/reset.css">
      <link rel="stylesheet" type="text/css" media="all" href="css/login.css">
      <link rel="stylesheet" type="text/css" media="all" href="css/notification.css">

      <!--  jQuery -->
      <script type="text/javascript" src="lib/jquery/jquery.pack.js"></script>
      <script type="text/javascript" src="lib/jquery/jquery.notification.js"></script>
      <script type="text/javascript" src="lib/jquery/login.js"></script>
   </head>
   <body>
      <div id="outer">
        <div id="middle">
          <div id="inner">
             <fieldset>
                <legend>Please Log In</legend>
                <div id="formElement">
                   <label for="password">Password</label><input type="password" name="password" value="" id="password">
                   <input type="submit" value="Let me in..." id="go" name="go">
                </div>
             </fieldset>
          </div>
        </div>
      </div>
  </body>
</html>