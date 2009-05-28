// Stuff to do as soon as the DOM is ready. 
// Use $() w/o colliding with other libs;
(function($) {
    jQuery(document).ready(function($) {

        // Handle submit
        $('#go').click(function() {

            // Disable button to stop multiple submits simultaneously.
            $('#go').val("Checking...").attr("disabled", "disabled");

            $.post("handler.php", {
                    func: 'login',
                    password: $('#password').val()
                },
                function(data) {
                    if (data) {
                        window.location.reload();
                    } else {
                        $.addNotification({
                            text: "Wrong password, try again."
                        });
                        $('#password').val('');
                    }

                    // Re-enable the button
                    $('#go').val("Let me in...").removeAttr("disabled");
                }
            );
        });
    });
})(jQuery);