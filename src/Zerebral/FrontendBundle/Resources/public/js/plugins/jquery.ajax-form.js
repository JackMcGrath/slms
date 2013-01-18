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
        var options = $.extend({}, {
            success: $.proxy(this.onSuccess, this),
            dataType: 'json'
        }, this.options);

        $(this.element).ajaxForm(options);
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
                var errorHtml = '<span class="help-inline">' + errors.join('<br>') + '</span>';
                var element = form.find('[name^="' + elementName.replace(/\[/g,'\\[').replace(/\]/g,'\\]') + '"]').last();
                element.parents('.control-group').addClass('error');
                var control = element.parents('.controls');
                if (control.length) {
                    control.append(errorHtml);
                } else {
                    element.parent().append(errorHtml);
                }
            });
        } else {
            if (typeof this.options.onSuccess == 'function') {
                this.options.onSuccess(response);
            }
        }
    },

    _: ''
};

$.registry('zerebralAjaxForm', ZerebralAjaxForm, {
    methods: ['init']
});