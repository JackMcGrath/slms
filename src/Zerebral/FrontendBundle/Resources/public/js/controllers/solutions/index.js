function Controller() {
}

Controller.prototype = {
    delayed:false,
    options:undefined,

    init:function (options) {
        this.options = options || {};
        this.sorting = new Sorting(this, $('.right-navbar .sorting'));
    }
};


var Sorting = function (container, target) {
    this.container = container;
    this.target = target;
    this.bind();
};

Sorting.prototype = {
    bind:function () {
        this.target.find('li input[type="radio"]').bind('change', $.proxy(this.onChangeSorting, this));
    },

    onChangeSorting: function(e) {
        this.target.find('form').submit();
    }
};