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
      <style type="text/css" media="screen">
         /*For testing only*/
/*         div{border: 1px solid #999;}
         form{border: 1px solid #F00;}*/


/*font:italic 18px "Warnock Pro","Goudy Old Style","Palatino","Book Antiqua",Georgia,serif;letter-spacing:1px;word-spacing:2px;line-height:10px;*/
         /*Production*/
         html,body{background-color: #F8F6F3;height:100%;font-family:Georgia,times,serif;letter-spacing: 1px;line-height: 1.5em;}
         .clear{clear: both;}
         #navigation, #main {float:left;}


         /* Form */
         fieldset{width:600px;}
         label,input,textarea,select{float:left;clear:left;-moz-box-sizing: border-box;}
         label{margin-top: 0.5em;}
         input{width:200px;}
         select{width:100px;}
         textarea{width:400px;min-height: 200px;}
         legend{font:30px "Warnock Pro","Goudy Old Style","Palatino","Book Antiqua",Georgia,serif;letter-spacing:1px;word-spacing:2px;line-height:10px;padding-bottom: 0.5em;}
         fieldset #formElement {margin-left: 2em;}
         #deleteButton, #postButton, #updateButton{margin-top: 1em;float:left;clear:none !important;}
         #postOptions{float:left;margin-left: 1em;}
         #title{width: 400px;}
         


         .checkbox {float:left;clear:both;margin-top: 0.5em;}
         .checkbox input{width:10px;float:left;margin-right:0.5em;}
         .checkbox label{width:10px;clear:none;margin: 0;padding: 0;}

         /* Navigation */
         #navigation{width:250px;min-width: 250px;max-width:250px;background-color: #342A1C;color: #FFF;}
         #navigation{min-height:100%;}
         * html #navigation{height:100%;}
         #sortHelper { border-left:2px dashed #777; }
         #navigation>div, #main>div{padding:3em 2em;}
         .nsw-item-row div { cursor: pointer; }
         /*#dragHelper { border:2px dashed #777777; }*/
         
         
         
         
         
         

         /* Notification */
         #notification {
            cursor: pointer;
            font-weight: bold;
            font: 30px Helvetica, sans-serif;
            line-height: 2em;
            color: #FFF;
            background-color: #AEAEAE;
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

               $('#title, #content, #status, #type, #hidden').change(function(){
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
                     // $('#post_id, ').val('');
                     $('#formElement').clearForm();
                  }
                  
                  console.debug(formChanged);
                  console.debug(newContent);
                  console.debug($('#post_id').val());
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
                        id:      $.trim($('#post_id').val()),
                        order:   $.trim($('#order').val()),
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
                        
                        // // Enable Post Button
                        // $('#deleteButton').val("Delete").removeAttr("disabled");
                        // 
                        // // Change legend
                        // $('fieldset>legend').text("Post Content Changes");

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
                     loadUrl: "handler.php?menu",
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
                              console.debug($(this).text());
                           });
                        });
                     }
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
                     
                     <label for="display">Display</label>
                     <select name="display" id="display">
                        <option value="Shown" selected="selected">Shown</option>
                        <option value="Hidden">Hidden</option>
                     </select>

                     <!-- <div class="checkbox">
                        <input type="checkbox" name="hidden" value="" id="hidden"><label for="hidden">Hidden</label>
                     </div> -->
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
