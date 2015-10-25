var ngm = angular.module(module.exports = 'nerick.lightBox', [
    'ngRoute'
]);

ngm.provider('lightCrate', LightCrateProvider);

ngm.config(function (lightCrateProvider) {
    lightCrateProvider.setLightboxOpen(function($html){
	$html.addClass('mfp-with-anim');	
	$.magnificPopup.open({
	    items: {
		src: $html,
		type: 'inline'
	    },
	    removalDelay: 500, //delay removal by X to allow out-animation
	    mainClass: 'mfp-zoom-in'
	}, 0);
    });
    
    lightCrateProvider.setLightboxClose(function(){
	var magnificPopup = $.magnificPopup.instance;
	magnificPopup.close();
    });
    
    lightCrateProvider.setTemplateUrl('html/nerick/lightCrate/template.html');
});


//provider options
var providerTemplate = null;
var providerTemplateUrl = null;

var providerLightboxOpen = function(){};
var providerLightboxClose = function(){};

//service options

function LightCrateProvider() {
    return {
	setTemplate: function(value){
	    providerTemplate = value;
	},
	setTemplateUrl: function(url){
	    providerTemplateUrl = url;
	},
	setLightboxOpen: function(callable){
	    if (!$.isFunction(callable)) {
		throw "LightboxOpen must be a function";
	    }
	    providerLightboxOpen = callable;
	},
	setLightboxClose: function(callable){
	    if (!$.isFunction(callable)) {
		throw "LightboxClose must be a function";
	    }
	    providerLightboxClose = callable;
	},
	$get: LightCrate
    };
}

function LightCrate($rootScope, $route, $templateCache, $http, $injector, $compile, $controller, $q){
    return {
	show: function(userOptions){
	    //debugger
	    var options = angular.copy(userOptions);
	    
	    var settings = angular.extend({
		route: null,
		templateUrl : null,
		template: null,
		locals: {},
		resolve: {},
		controller: null
	    }, options );
	    
	    var routeMatch = null;

	    if(settings.route){
		routeMatch = matchRoute(settings.route, $route);
		if(routeMatch){
		    settings.locals.$routeParams = routeMatch.params;
		    if(routeMatch.route.resolve){
			settings.resolve = angular.extend(settings.resolve, routeMatch.route.resolve);
		    }
		}
	    }
	    
	    //var retrieveTemplate = Promise.reject();
	    //var retrieveTemplate = $q.defer();
	    var retrieveTemplate = null;
	    
	    if(settings.template){
		retrieveTemplate = Promise.resolve(settings.template)
	    }
	    else{
		retrieveTemplate = new Promise(function (resolve, reject) {
		    var templateUrl = settings.templateUrl;
		    if(!templateUrl && routeMatch){
			templateUrl = routeMatch.route.templateUrl;
		    }
		    
		    if(!templateUrl){
			reject();
		    }
		    
		    
		    var cachedTemplateHtml = $templateCache.get(templateUrl);
		    if (cachedTemplateHtml) {
			if(cachedTemplateHtml instanceof Array){
			    resolve(cachedTemplateHtml[1]);
			}
			resolve(cachedTemplateHtml);
		    }
		    else {
			$http.get(templateUrl).then(function(response){
			    $templateCache.put(templateUrl, response.data);
			    resolve(response.data);
			}).catch(function(rejection){
			    throw rejection;
			});
		    }
		});		
	    }
	    
	    retrieveTemplate.then(function(html){
		//var $childScope = $rootScope.$new(false, scope);
		//var $childScope = $rootScope.$new();
		var $childScope = $rootScope.$new(false);
		
		settings.locals.$scope = $childScope
		
		var $html = angular.element(html);
		var link = $compile($html);
		
		var controller = settings.controller;
		if(!controller && routeMatch && routeMatch.route.controller){
		    controller = routeMatch.route.controller;
		}
		
		var resolutions = Promise.resolve();
		
		for (var key in settings.resolve) {
		    var resolvePromiseGetter = settings.resolve[key];
		    (function(key, resolvePromiseGetter) {
		      resolutions = resolutions.then(function(){
			    return $injector.invoke(resolvePromiseGetter).then(function(result){
				settings.locals[key] = result;
			    });
			});
		    })(key, resolvePromiseGetter);			
		}
		
		resolutions.then(function(){
		    if(controller){
			controller = $controller(controller, settings.locals);
		    }
		    
		    var getProviderTemplate = Promise.resolve();

		    if(providerTemplate){
			getProviderTemplate = Promise.resolve(providerTemplate);
		    }
		    else if(providerTemplateUrl){
			getProviderTemplate = new Promise(function (resolve, reject) {
			    $http.get(providerTemplateUrl).then(function(response){
				//$templateCache.put(templateUrl, response.data);
				resolve(response.data);
			    }).catch(function(rejection){
				throw rejection;
				//throw 'Light box template failed to load.';
			    });
			});
		    }

		    getProviderTemplate.then(function (providerTemplateHtml) {
			$result = link($childScope);
			var $html = $result;
			var $providerTemplateHtml = angular.element(providerTemplateHtml);

			if (typeof $providerTemplateHtml != 'undefined') {
			    $providerTemplateHtml.find('[data-lightBoxContent]').html($html);
			    $html = $providerTemplateHtml;
			}
			$childScope.$digest();
			if (controller) {
			    $html.data('$ngControllerController', controller);
			    $html.children().data('$ngControllerController', controller);
			}
			if (!providerLightboxOpen) {
			    throw 'You must specify a function to open your lightbox in $lightBoxProvider config';
			}

			providerLightboxOpen($html);
		    });	
		});
	    });
	},
	hide: function(){
	    providerLightboxClose();
	}
    };
}

function matchRoute(on, $route) {
    for (var path in $route.routes) {
	var route = $route.routes[path];
	var keys = route.keys;
	var params = {};

	if (!route.regexp)
	    continue;

	var m = route.regexp.exec(on);
	if (!m)
	    continue;

	for (var i = 1, len = m.length; i < len; ++i) {
	    var key = keys[i - 1];

	    var val = m[i];

	    if (key && val) {
		params[key.name] = val;
	    }
	}
	return {route: route, params: params};

    }
    return null;
};

