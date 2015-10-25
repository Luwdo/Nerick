var ngm = angular.module(module.exports = 'nerick.notifications', [
]);

ngm.factory('NotifyService', function(){
    var notify = {};
    var notifications = [];
    //var globalNotifications = {};
    var types = {
	error:'error',
	warning:'warning',
	message:'message'
    };
    
    var classes = {
	error:'alert alert-danger',
	warning:'alert alert-warning',
	message:'alert alert-success'
    };
    
    var typesList = [types.error,types.warning,types.message];
    
    notify.types = types;
    notify.classes = classes;
    
    notify.add = function(message, type, target){
	var notification = {};
	notification.message = message;
	notification.type = type;
	notification.target = target;
	notifications.push(notification);
    };
    
    notify.input = function(message, type, name){
	//debugger;
	var notification = {};
	notification.message = message;
	notification.type = type;
	 notification.target = null;
	if(name){
	    notification.target = $('input[name='+name+']');
	}
	//debugger;
	notifications.push(notification);
    };
    
    notify.show = function(){
	//debugger;
	var notificationLength = notifications.length;
	for(var key =0; key < notificationLength; key++) {
	    var notification = notifications[key];
	    if(notification == null){
		continue;
	    }
	    var $notification = $('<div class="notification"></div>');
	    
	    $notification.addClass(notify.classes[notification.type]);
	    //$notification.addClass(notification.type);
	    

	    $notification.append(notification.message);
	    applyBehaviors($notification);
	   
	    notifications[key].notification = $notification;
	    //different for global
	    if(typeof notification.target !== 'undefined' && notification.target !== null &&  $(notification.target) !== null){
		$(notification.target).after($notification);
		continue;
	    }
	    var $globalNotificationContainer = $('.notifications');
	    
	    //could add to both then clear from both if there are 2 maybe, give it some thought;
	    //$globalNotificationContainer.first().append($notification);
	    $globalNotificationContainer.each(function(index) {
		$(this).append($notification);
	    });
	    
	    
	    //$(notification.target).after($notification);
	};
    };
    
    notify.clear = function(){
	var notificationLength = notifications.length;
	for(var key =0; key < notificationLength; key++) {
	    //debugger;
	    var notification = notifications[key];
	    if(notification != null && typeof notification.notification !== 'undefined'){
		notification.notification.remove();
		delete notifications[key];
	    }
	};
     };
    
    notify.addGlobal = function(message, type){
	var notification = {};
	notification.message = message;
	notification.type = type;
	//notification.target = null;
	notifications.push(notification);
    };
    
    function applyBehaviors($message){
	$message.click(function(){
	    $(this).fadeOut();
	});
	$message.delay(4000).fadeOut('slow');
//	
//	setTimeout(function($message){
//	    var $self = $message;
//	    $self.fadeOut();
//	},1000);
    };
    
    
    notify.populateNotifications = function(notifications){
	for (var key in notifications) {
	    var notification = notifications[key];
	    notify.input(notification.content, notification.type, notification.target);
	}
    };
    
    return notify;
});