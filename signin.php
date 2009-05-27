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
      <link href="css/reset.css" rel="stylesheet" type="text/css" media="all">
      <style type="text/css" media="screen">
         html,body{background-color:#342A1C;color: #FFF;font-family:Georgia,times,serif;letter-spacing:1px;line-height:1.5em;min-height:200px;min-width:200px;}
         /* mac hide \*/
         html,body{height:100%;width:100%;}
         /* end hide */
         
         h1 { text-align: center;}

         /* Form */
         label,input,textarea,select{float:left;clear:left;-moz-box-sizing:border-box;margin-top:0.5em;width:200px;}
         label{margin-left: -99999em;line-height: 1px;}
/*         input{width:200px;}*/
         legend{font:30px "Warnock Pro","Goudy Old Style","Palatino","Book Antiqua",Georgia,serif;letter-spacing:1px;word-spacing:2px;line-height:10px;padding-left:0.5em;text-align: center;width:200px;}

         /* Vertically and Horizontally centered */
         #outer{height:100%;width:100%;display:table;vertical-align:middle;}
         #middle{position:relative;vertical-align:middle;display:table-cell;height:200px;}
         #inner{width:200px;margin: 0 auto;}
         
      </style>

      <!--  jQuery -->
      <script type="text/javascript" src="lib/jquery/jquery.pack.js"></script>

      <script type="text/javascript">
         // Stuff to do as soon as the DOM is ready. Use $() w/o colliding with other libs;
         (function($){
            jQuery(document).ready(function($) {

               // Handle submit
               $('#go').click(function() {

                  // Disable button to stop multiple submits simultaneously.
                  $('#go').val("Checking...").attr("disabled","disabled");

                  $.post("handler.php", {
                        func:       'login',
                        password:   $('#password').val()
                     },
                     function(data){
                        if (data){
                           window.location.reload();
                        }

                        
                        // Re-enable the button and change it to a 
                        // updatebutton
                        $('#go')
                           .val("Let me in...")
                           .removeAttr("disabled");
                     }
                  );
               });

            });
         })(jQuery);
      </script>
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
