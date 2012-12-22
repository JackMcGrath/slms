/**

jQuery plugins registry helper

USAGE EXAMPLE


1. define class

var TestClass = function(element, options) {
	var self = this;
	self.element = element;
	self.options = options;
};

TestClass.prototype = {
	element: undefined,
	options: undefined,
	feedLoading: false,
	
	init: function() {
		console.log('test inited');
	},
	
	close: function(params) {
		console.log('test close');
	},
	
	_: ''
};

2. registry plugin to class

$.registry('test', TestClass, {
	methods: ['init','close'],
	globalMethods: ['close'],
	customMethods: {
		'custom': function(options) {
			console.log('custom');
			console.log(this);
			console.log(options);
		}
	},
	defaults: {
		commentLink: 'a.comments',
		showMoreLink: '.feed-more',
		lastItemId: 0,
		total: 0,
		userId: 0,
		type: "friends"
	},
});

3. call plugin :)

// init
$(selector).test(options);

// standard method
$(selector).test('close', options);

// custom method
$(selector).test('custom', options);

// global method (for all instances)
$.test.close(options)

**/

(function($) {	
	
	$.registry = function(name, relativeClass, options) {
		options = $.extend({}, $.registry.defaults, options);
		
		var plugin = {
			instances: new Array(),
			defaults: options.defaults,
			methods: {},
			relativeClass: relativeClass
		};
		
		// global methods for all instaces
		$.each(options.globalMethods, function(index, methodName){
			plugin[methodName] = function(options){
				$.each(plugin.instances, function(i, instance) {
					instance[name].call(instance, methodName, options);
				});
			};
		});
		
		$[name] = plugin;
		
		// standart plugin methods
		$.each(options.methods, function(index, methodName){
			
			plugin.methods[methodName] = function(options) {
		
				if (methodName == 'init')
					options = $.extend({}, plugin.defaults, options);
				
				return this.each(function(index, value){
					// $this - jQuery instance of element
					var $this = $(this);
					// activity_feed - instance of ActivityFeed class
					var pluginInstance = $this.data(name);
					
					// If the activity_feed hasn't been initialized yet
					if (!pluginInstance && methodName == 'init') {
						var pluginInstance = new plugin.relativeClass($this, options);
						
						pluginInstance.init();
						
						$this.data(name, pluginInstance);
						// push new element with inited jQuery plugin to instances stack
						plugin.instances.push($this);
					} else if (methodName != 'init') {
						pluginInstance[methodName].call(pluginInstance, options);
					}
				});
			}
			
		});
		
		// custom methods
		$.each(options.customMethods, function(methodName, method){
			plugin.methods[methodName] = method;
		});
		
		$.fn[name] = function(method) {
			if (plugin.methods[method]) {
				return plugin.methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
			}
			else if (typeof method === 'object' || !method) {
				return plugin.methods.init.apply(this, arguments);
			}
			else {
				$.error('Method ' +  method + ' does not exist on jQuery.plugin');
			} 
		}
			
	};
	
	$.registry.defaults = {
		methods: ['init'],
		customMethods: [],
		globalMethods: []
	};
	
})(jQuery);