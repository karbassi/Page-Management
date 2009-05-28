// Stuff to do as soon as the DOM is ready.
// Use $() w/o colliding with other libs;
(function($){
   jQuery(document).ready(function($) {

      var formChanged = false;
      var newContent = true;

      $('#title, #content, #status, #type, #display').change(function(){
         formChanged = true;
      });

      $('#createButton').click(function() {
         if (!newContent){
            formChanged = false;
            newContent = true;
            checkFormStatus();
         }
      });

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
               $.addNotification({text: data['message']});

               // Update Menu
               $('#pageListing').empty().append("<div></div>");
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
               // Notify
               $.addNotification({text: data['message']});

               // Update Menu
               $('#pageListing').empty().append("<div></div>");
               createMenu();

               // Clear form is item was deleted.
               if (data['pass']){
                  if (!newContent){
                     formChanged = false;
                     newContent = true;
                     checkFormStatus();
                  }
               }
            },
            "json"
         );
      });

      function checkFormStatus() {
         if (newContent){
            $('#deleteButton').attr("disabled","disabled");

            // Re-enable the button and change it to a
            // updatebutton
            $('#updateButton')
               .attr("id","postButton")
               .attr("name","postButton")
               .val("Post")
               .removeAttr("disabled");

            // Change legend
            $('fieldset>legend').text("Post New Content");

            $('#formElement').clearForm();
         } else {
            $('#deleteButton').removeAttr("disabled");

            // Change legend
            $('fieldset>legend').text("Post Content Changes");
         }
      }

      function createMenu(){
         // Navigation
         $('#pageListing>div').NestedSortableWidget({
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
                        $.addNotification({text: data['message']});
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
               if (type == 'text' || type == 'password' || type == 'hidden' || tag == 'textarea'){
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