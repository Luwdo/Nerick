
$.fn.animateRotate = function(startAngle, endAngle, duration, easing, complete){
    return this.each(function(){
        var elem = $(this);

        $({deg: startAngle}).animate({deg: endAngle}, {
            duration: duration,
            easing: easing,
            step: function(now){
                elem.css({
                  '-moz-transform':'rotate('+now+'deg)',
                  '-webkit-transform':'rotate('+now+'deg)',
                  '-o-transform':'rotate('+now+'deg)',
                  '-ms-transform':'rotate('+now+'deg)',
                  'transform':'rotate('+now+'deg)'
                });
            },
            complete: complete || $.noop
        });
    });
};

$.fn.getRotation = function(){
    var el = $( this ).get( 0 );
    var st = window.getComputedStyle(el, null);
    var tr = st.getPropertyValue("-webkit-transform") ||
             st.getPropertyValue("-moz-transform") ||
             st.getPropertyValue("-ms-transform") ||
             st.getPropertyValue("-o-transform") ||
             st.getPropertyValue("transform") ||
             false;
    if(tr == false || tr == "none"){
        return 0;
    }
    var values = tr.split('(')[1];
        values = values.split(')')[0];
        values = values.split(',');
    var a = values[0];
    var b = values[1];
    var angle = Math.round(Math.atan2(b, a) * (180/Math.PI));
    return angle;
};

function installBehaviors($ctx){
    
    
    $(window).scroll(function () {
	
	
	if($(window).scrollTop() > 0){
	    $("#header .nav-custom").stop().animate({ 
		'padding-top' : 0,
		'padding-bottom' : 0
	    }, "slow");
	}
	else{
	   $("#header .nav-custom").stop().animate({ 
		'padding-top' : '70px',
		'padding-bottom' : '70px'
	    }, "slow"); 
	}
    });
    
    
}
$(document).ready(function(){
    installBehaviors($('body'));
});
