(function($) {
    var Y_Finance = {
        editor: null,
        init: function() {
            var self = this;
            tinymce.PluginManager.add('yfinance', function(editor, url) {
                editor.addButton('yfinance', {
                    text: 'Add Yahoo Finance Chart',
                    icon: false,
                    onclick: function() {
                        if (!$('#yfinance').length) {
                            self.getForm();
                        } else {
                            $('#yfinance').fadeIn();
                        }
                    }
                });
                self.editor = editor;
            });
        },
        getForm: function() {
            $.ajax({
                url: ajaxurl,
                data: {action: 'yfinance_get_form'},
                type: 'post',
                success: function(response) {
                    $('#wpfooter').after(response);
                }
            });
        },
        insert: function() {
            var form = $('#yfinance form').serializeArray(),
            shortcode = '[yfinance';
            for (var i = 0; i < form.length; i++) {
                shortcode += ' ' + form[i].name + '="' + form[i].value + '"';
            }
            this.editor.insertContent(shortcode + ']');
        }
    };
    Y_Finance.init();
    /** Insert shortcode from form **/
    $(document).on('submit', '#yfinance form', function(e) {
        e.preventDefault();
        Y_Finance.insert();
        $('#yfinance').fadeOut();
    });
    /** Close UI **/
    $(document).on('click', '#yfinance #yfinance_close', function() {
        $('#yfinance').fadeOut();
    });
})(jQuery);