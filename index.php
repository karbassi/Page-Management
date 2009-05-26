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
      <link href="css/reset.css" rel="stylesheet" type="text/css" media="all">
      <!-- <link href="css/jquery.notice.css" rel="stylesheet" type="text/css" media="all"> -->
      <style type="text/css" media="screen">
         /*For testing only*/
/*         div{border: 1px solid #999;}
         form{border: 1px solid #F00;}*/

         /*Production*/
/*         body{font:italic 18px "Warnock Pro","Goudy Old Style","Palatino","Book Antiqua",Georgia,serif;letter-spacing:1px;word-spacing:2px;line-height:10px;}*/
         .clear{clear: both;}
         #navigation, #main {float:left;margin:1em 0.5em;padding:0.5em;}
         #navigation{width:250px;min-width: 250px;max-width:250px;}

         /* Form */
         fieldset{width:600px;}
         label,input,textarea,select{float:left;clear:left;}
         label{margin-top: 0.5em;}
         input{width:200px;}
         select{width:100px;}
         textarea{width:400px;min-height: 200px;}
         #postButton{margin-top: 1em;float:left;}
         #postOptions{float:left;margin-left: 1em;}


         .checkbox {float:left;clear:both;margin-top: 0.5em;}
         .checkbox input{width:10px;float:left;margin-right:0.5em;}
         .checkbox label{width:10px;clear:none;margin: 0;padding: 0;}

         /*Sorting List*/
/*         .sort-handle { cursor:move; }
         .helper{border:2px dashed #777;}
         .current-nesting{background-color:yellow;}
*/
         /* Notification */
         #notification {
            cursor: pointer;
            font-weight: bold;
            font: 30px Helvetica, sans-serif;
            line-height: 2em;
            color: #FFF;
            background-color: #F90;
            position: absolute;
            z-index: 9999;
            opacity: 0.80;
            top: 0;
            text-align: center;
         }


      </style>



      <!--  jQuery -->
      <script type="text/javascript" src="lib/jquery/jquery.pack.js"></script>

      <!-- iNestedSortable -->
      <!-- <script type="text/javascript" src="lib/jquery/interface-1.2.js"></script>
      <script type="text/javascript" src="lib/jquery/inestedsortable.js"></script> -->
      
      <script type="text/javascript" src="lib/jquery/interface-1.2.js"></script>
      <script type="text/javascript" src="lib/jquery/inestedsortable.js"></script>
      <script type="text/javascript" src="lib/jquery/jquery.nestedsortablewidget.js"></script>

      <!-- Notification -->
      <script type="text/javascript" src="lib/jquery/jquery.notification.js"></script>

      <script type="text/javascript">
         // Stuff to do as soon as the DOM is ready. Use $() w/o colliding with other libs;
         (function($){
            jQuery(document).ready(function($) {


               $('#navigation').NestedSortableWidget({
                  name: "nav",
                  loadUrl: "handler.php?menu",
                  handle: true,
                  nestedSortCfg: {
                     accept: 'item',
                     opacity: 0.6,
                     fx: 400,
                     revert: true,
                     helperclass: 'helper',
                     autoScroll: true,
                     handle: '.sort-handle',
                     onChange: function(serialized) {
                        $.post("handler.php",
                           {
                              func:    'menu',
                              data:    serialized[0].hash
                           },
                           function(data){
                              $.addNotification({text: data.message});
                           },
                           "json"
                        );
                     }
                  }
               });
               
               var formChanged = false;


               $('#title, #content, #status, #type, #hidden').change(function(){
                  formChanged = true;
               });

               // Handle submit
               $('#postButton').click(function() {

                  // Stop if there hasn't been any changes.
                  if (!formChanged)
                  {
                     $.addNotification({text: "Nothing was changed."});
                     return;
                  };

                  // Disable button to stop multiple submits simultaneously.
                  $('#postButton').val("Submitting...").attr("disabled","disabled");

                  $.post("handler.php",
                     {
                        func:    "post",
                        id:      $.trim($('#post_id').val()),
                        title:   $.trim($('#title').val()),
                        content: $.trim($('#content').val()),
                        status:  $.trim($('#status').val()),
                        type:    $.trim($('#type').val()),
                        hidden:  $('#hidden').is(':checked'),
                     },
                     function(data){

                        // Only set value if there ID doesn't exist.
                        if (!$.trim($('#post_id').val())) {
                           $('#post_id').val(data.id);
                        };

                        formChanged = false;

                        // Re-enable the button.
                        $('#postButton').val("Post").removeAttr("disabled");

                        // Notify
                        $.addNotification({text: data.message});
                     },
                     "json"
                  );
               })

            });
         })(jQuery);
      </script>
   </head>
   <body>
      <div id="navigation"></div>
      <!-- <div id="navigation">
         <ul id="nav" class="page-list">
            <li id="ele-1" class="clear-element item">
               <div class='sort-handle'>File 1</div>
            </li>
            <li id="ele-2" class="clear-element item">
               <div class='sort-handle'>File 2</div>
            </li>
            <li id="ele-3" class="clear-element item">
               <div class='sort-handle'>Folder 1</div>
            </li>
            <li id="ele-4" class="clear-element item">
               <div class='sort-handle'>Folder 2</div>
               <ul class="page-list">
                  <li id="ele-5" class="clear-element item">
                     <div class='sort-handle'>Folder 3</div>
                     <ul class="page-list" >
                        <li id="ele-6" class="clear-element item">
                           <div class='sort-handle'>File 3</div>
                        </li>
                     </ul>
                  </li>
               </ul>
            </li>
         </ul>
      </div> -->
      <!-- #navigation -->

      <div id="main">
         <fieldset>
            <legend>Post New Content</legend>
            <input type="hidden" name="post_id" value="" id="post_id">
            <label for="title">Title</label><input type="text" name="title" value="" id="title" maxlength="100">
            <label for="content">Content</label><textarea name="content" id="content" rows="8" cols="40"></textarea>

            <div id="postOptions">
               <label for="status">Status</label>
               <select name="status" id="status">
                  <option value="Draft" selected="selected">Draft</option>
                  <option value="Private">Private</option>
                  <option value="Public">Public</option>
               </select>

               <label for="type">Type</label>
               <select name="type" id="type">
                  <option value="Page" selected="selected">Page</option>
                  <option value="Blog">Blog</option>
               </select>

               <div class="checkbox">
                  <input type="checkbox" name="hidden" value="" id="hidden"><label for="hidden">Hidden</label>
               </div>
            </div>
            <!-- #postOptions -->

            <p><input type="submit" value="Post" id="postButton" name="postButton"></p>

            <br class="clear">
         </fieldset>
      </div>
      <!-- #main -->
  </body>
</html>
