var ZerebralAjaxForm = function(element, options) {
    var self = this;
    self.element = element;
    self.options = options;
};

ZerebralAjaxForm.prototype = {
    element: undefined,
    options: undefined,
    feedLoading: false,

    init: function() {
        $(this.element).ajaxForm({
            success: $.proxy(this.onSuccess, this),
            dataType: 'json'
        })
    },

    onSuccess: function(response) {

        if (typeof(response.redirect) !== 'undefined') {
            window.location.href = response.redirect;
            return;
        }

        var form = $(this.element);
        form.find('.control-group').removeClass('error');
        form.find('.control-group .help-inline').remove();

        if (response.has_errors) {
            $.each(response.errors, function(elementName, errors) {
                form
                    .find('[name="' + elementName.replace(/\[/g,'\\[').replace(/\]/g,'\\]') + '"]')
                        .parents('.control-group')
                            .addClass('error')
                    .end()
                    .after('<span class="help-inline">' + errors.join('<br>') + '</span>');
            });
        }
    },

    _: ''
};

$.registry('zerebralAjaxForm', ZerebralAjaxForm, {
    methods: ['init']
});