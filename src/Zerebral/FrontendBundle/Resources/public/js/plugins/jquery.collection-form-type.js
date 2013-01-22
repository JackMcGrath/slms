var CollectionFormType = function(element, options) {
    this.element = element;
    this.options = options;
    this.lastIndex = element.data('last-index');
    this.template = '';
};

CollectionFormType.prototype = {
    element: undefined,
    options: undefined,
    lastIndex: undefined,
    template: undefined,

    init: function() {
        $(this.options.add).click($.proxy(this, 'add'));
        this.element.on('click', this.options.remove, $.proxy(this, 'remove'));

        this.template = $(this.options.template).html();
    },

    add: function(e) {
        e.preventDefault();
        var index = ++this.lastIndex;
        var indexReplacement = new RegExp(this.options.indexPlaceholder, 'g');
        this.element.append(this.template.replace(indexReplacement, index));
    },

    remove: function(e) {
        e.preventDefault();
        $(e.target).parents(this.options.item).remove();
    },

    _: ''
};

$.registry('collectionFormType', CollectionFormType, {
    methods: ['init'],
    defaults: {
        add: '.add-item',
        remove: '.remove-item',
        template: '#item_form_template',
        item: '.item',
        indexPlaceholder: '__name__'
    }
});