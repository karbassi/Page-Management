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
         div{border: 1px solid #999;}
         form{border: 1px solid #F00;}
         
         /*Production*/
         body{}
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
         .sort-handle { cursor:move; }
         .helper{border:2px dashed #777;}
         .current-nesting{background-color:yellow;}
         
      </style>
      
      <script src="lib/jquery/jquery.pack.js" type="text/javascript" charset="utf-8"></script>
      <script src="lib/jquery/interface-1.2.js" type="text/javascript" charset="utf-8"></script>
      <script src="lib/jquery/inestedsortable.pack.js" type="text/javascript" charset="utf-8"></script>
      <script type="text/javascript" charset="utf-8">
         // Stuff to do as soon as the DOM is ready. Use $() w/o colliding with other libs;
         jQuery(document).ready(function($) {

            // Sorting List
            $('#left-to-right').NestedSortable({
               accept: 'page-item1',
               noNestingClass: "no-nesting",
               opacity: 0.8,
               helperclass: 'helper',
               onChange: function(serialized) {
                  console.debug(serialized[0].hash);
               },
               autoScroll: true,
               handle: '.sort-handle'
            });
            
            // Handle submit
            $('#postButton').click(function() {
               $.post("handler.php",
                     {
                        func: "post",
                        title: $('#title').val(),
                        body: $('#body').val(),
                        status: $('#status').val(),
                        type: $('#type').val(),
                        hidden: $('#hidden').is(':checked')
                     },
                     function(data){
                        console.log(data);
                     }
               );
            })
         });
      </script>
   </head>
   <body>
      <div id="navigation">
         <ul id="left-to-right" class="page-list">
            <li id="ele-12" class="clear-element page-item1 left sort-handle">
               <div class='sort-handle'>File 1</div>
            </li>
            <li id="ele-2" class="clear-element page-item1 left">
               <div class='sort-handle'>File 2</div>
            </li>
            <li id="ele-3" class="clear-element page-item1 left">
               <div class='sort-handle'>Folder 1</div>
            </li>
            <li id="ele-4" class="clear-element page-item1 left">
               <div class='sort-handle'>Folder 2</div>
               <ul class="page-list">
                  <li id="ele-5" class="clear-element page-item1 left">
                     <div class='sort-handle'>Folder 3</div>
                     <ul class="page-list" >
                        <li id="ele-6" class="clear-element page-item1 left">
                           <div class='sort-handle'>File 3</div>
                        </li>
                     </ul>
                  </li>
               </ul>
            </li>
         </ul>
      </div>
      <!-- #navigation -->
      
      <div id="main">
         <!-- <form method="post"> -->
            <fieldset>
               <legend>Post New Content</legend>
               <label for="title">Title</label><input type="text" name="title" value="" id="title" maxlength="100">
               <label for="body">Body</label><textarea name="body" id="body" rows="8" cols="40"></textarea>

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

               <p><input type="submit" value="Post &rarr;" id="postButton" name="postButton"></p>               

               <br class="clear">
            </fieldset>
         <!-- </form> -->
         
      </div>
      <!-- #main -->
  </body>
</html>
