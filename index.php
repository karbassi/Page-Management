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

      <script type="text/javascript">
         // Stuff to do as soon as the DOM is ready. Use $() w/o colliding with other libs;
         (function($){
            jQuery(document).ready(function($) {
               
               var formChanged = false;
               var newContent = true;

               $('#title, #content, #status, #type, #display').change(function(){
                  formChanged = true;
               });
               
               function checkFormStatus() {
                  if (newContent){
                     $('#deleteButton').attr("disabled","disabled");

                     // Change legend
                     $('fieldset>legend').text("Post New Content");
                  } else {
                     $('#deleteButton').removeAttr("disabled");

                     // Change legend
                     $('fieldset>legend').text("Post Content Changes");
                  }
                  
                  if (newContent){
                     // $('#ID, ').val('');
                     $('#formElement').clearForm();
                  }
               }
               

               // Handle submit
               $('#postButton, #updateButton').click(function() {
                  
                  var button = $(this)[0].id.split("Button");
                  // Stop if there hasn't been any changes.
                  if (!formChanged)
                  {
                     $.addNotification({text: "Nothing was changed."});
                     return;
                  };

                  // Disable button to stop multiple submits simultaneously.
                  $('#postButton').val("Submitting...").attr("disabled","disabled");

                  $.post("handler.php", {
                        func:    button[0],
                        id:      $.trim($('#ID').val()),
                        order:   $.trim($('#order').val()),
                        title:   $.trim($('#title').val()),
                        content: $.trim($('#content').val()),
                        status:  $.trim($('#status').val()),
                        type:    $.trim($('#type').val()),
                        display: $.trim($('#display').val())
                     },
                     function(data){

                        // Only set value if there ID doesn't exist.
                        if (!$.trim($('#ID').val())) {
                           $('#ID').val(data.id);
                        }

                        // Change back so that we know the form has been
                        // saved and not updated again.
                        formChanged = false;
                        
                        // Not new content anymore.
                        newContent = false;

                        // Re-enable the button and change it to a 
                        // updatebutton
                        $('#postButton')
                           .attr("id","updateButton")
                           .attr("name","updateButton")
                           .val("Update")
                           .removeAttr("disabled");
                        
                        checkFormStatus();
                        
                        // Notify
                        $.addNotification({text: data.message});
                        
                        // Update Menu
                        $('#navigation').empty().append("<div></div>");
                        createMenu();
                        
                     },
                     "json"
                  );
               });
               
               // Delete Button
               $("#deleteButton").click(function() {
                  // $.addNotification({text: "Delete button is not currently working."});
                  
                  $.post("handler.php", {
                        func:    'delete',
                        id:      $.trim($('#ID').val())
                     },
                     function(data){
                        
                        console.debug(data);
                        
                        // Notify
                        $.addNotification({text: data.message});

                        // Update Menu
                        $('#navigation').empty().append("<div></div>");
                        createMenu();
                     },
                     "json"
                  );
               });
               
               function createMenu(){
                  // Navigation
                  $('#navigation>div').NestedSortableWidget({
                     name: "nav",
                     loadUrl: "handler.php?func=menu",
                     nestedSortCfg: {
                        accept: 'item',
                        opacity: 0.6,
                        fx: 400,
                        revert: true,
                        helperclass: 'helper',
                        autoScroll: true,
                        handle: '.sort-handle',
                        onChange: function(serialized) {
                           $.post("handler.php", {
                                 func:    'menu',
                                 data:    serialized[0].hash
                              },
                              function(data){
                                 $.addNotification({text: data.message});
                              },
                              "json"
                           );
                        }
                     },
                     onLoad: function() {
                        $('.nsw-item > .nsw-item-row > div').each(function(){
                           $(this).click(function(){
                              var id = $(this).parent().parent().attr('id').split('-').pop();
                              loadPost(id);
                           });
                        });
                     }
                  });
               }
               
               function loadPost(id) {
                  $.getJSON("handler.php",{func: 'load', id: id}, function(data) {
                     $('#ID').val(data['item']['ID']);
                     $('#title').val(data['item']['title']);
                     $('#content').val(data['item']['content']);
                     $('#type').val(data['item']['type']);
                     $('#status').val(data['item']['status']);
                     $('#display').val(data['item']['display']);
                     newContent = false;
                     formChanged = false;
                     checkFormStatus();
                     $.addNotification({text: data['message']});
                     
                  });
               }
               
               $.fn.clearForm = function() {
                  // iterate each matching form
                  return this.each(function() {
                     // iterate the elements within the form
                     $(':input', this).each(function() {
                        var type = this.type, tag = this.tagName.toLowerCase();
                        if (type == 'text' || type == 'password' || tag == 'textarea'){
                           this.value = '';
                        } else if (type == 'checkbox' || type == 'radio'){
                           this.checked = false;
                        } else if (tag == 'select') {
                           this.selectedIndex = 0;
                        }
                     });
                  });
               };
               
               createMenu();
               checkFormStatus();

            });
         })(jQuery);
      </script>
   </head>
   <body>

      <div id="navigation"><div></div></div>
      <!-- #navigation -->

      <div id="main">
         <div>
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
