/*
 jQuery Loupe v1.3.2
 https://github.com/iufer/jLoupe
*/

jQuery.fn.jloupe = function(o){
	
	//var version = '1.3.2';
	var jloptions = {		
		width:200,
		height:200,
		margin:6,
		cursorOffsetX:10,
		cursorOffsetY:10,
		radiusLT:0,
		radiusLB:100,
		radiusRT:100,
		radiusRB:100,
		borderColor:'#999',
		backgroundColor:'#ddd',
		image: false,
		repeat: false,
		fade: true
	};
	if(o) {
		jQuery.extend(jloptions, o);
		if(o.hasOwnProperty('color')) {
			jloptions.borderColor = jloptions.backgroundColor = o.color;
		}
	}
	loupe = $('<div />').addClass('thejloupe')
		.css('position','absolute')
		.css('width',jloptions.width +'px')
		.css('height',jloptions.height +'px')
		.css('backgroundColor', jloptions.borderColor)
		.hide()
		.appendTo('body');
	if(!jloptions.borderColor) loupe.css('backgroundColor', 'none')
	if(jloptions.repeat) loupe.css('backgroundRepeat', 'repeat');	
	else loupe.css('backgroundRepeat', 'no-repeat');	
			
	view = $('<div />').addClass('thejloupeview')
		.css('width',jloptions.width-jloptions.margin*2 +'px')
		.css('height',jloptions.height-jloptions.margin*2 +'px')
		.css('backgroundRepeat','no-repeat')
		.css('marginLeft', jloptions.margin +'px')
		.css('marginTop', jloptions.margin +'px')
		.appendTo(loupe);

	if(jloptions.backgroundColor) view.css('backgroundColor', jloptions.backgroundColor);

	//if($.support.cssProperty('borderRadius')){
	
		if(jloptions.image) loupe.css('backgroundImage', 'url('+ jloptions.image +')');
		
		$(view)			
			.css('border-top-left-radius', jloptions.radiusLT)
			.css('border-bottom-left-radius', jloptions.radiusLB)
			.css('border-bottom-right-radius', jloptions.radiusRB)
			.css('border-top-right-radius', jloptions.radiusRT)
			.css('-moz-border-radius-topleft', jloptions.radiusLT)
			.css('-moz-border-radius-bottomright', jloptions.radiusRB)
			.css('-moz-border-radius-bottomleft', jloptions.radiusLB)
			.css('-moz-border-radius-topright', jloptions.radiusRT)
			.css('border-radius', 
					jloptions.radiusLT + "px " 
					+ jloptions.radiusLB + "px "
					+ jloptions.radiusRB + "px "
					+ jloptions.radiusRT + "px");
					
		if(!jloptions.image || jloptions.repeat) {
			$(loupe)
				.css('border-top-left-radius', jloptions.radiusLT)
				.css('border-bottom-left-radius', jloptions.radiusLB)
				.css('border-bottom-right-radius', jloptions.radiusRB)
				.css('border-top-right-radius', jloptions.radiusRT)
				.css('-moz-border-radius-topleft', jloptions.radiusLT)
				.css('-moz-border-radius-bottomright', jloptions.radiusRB)
				.css('-moz-border-radius-bottomleft', jloptions.radiusLB)
				.css('-moz-border-radius-topright', jloptions.radiusRT);
		}
	//}		
	
	function updateLoupe(e)
	{
		var o = $(this).offset();
		var i = $(this).data('zoom');
		var zoom = $(this).data('zoomValue');
		
		var posx = 0, posy = 0;
		if(e.pageX || e.pageY){
			posx = e.pageX;
			posy = e.pageY;
		}
		else if(e.clientX || e.clientY){
			posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
			posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
		}
		w = $(this).prop ? $(this).prop('width') : $(this).attr('width');
		h = $(this).prop ? $(this).prop('height') : $(this).attr('height');
		
		var offX = (posx - o.left);
		var offY = (posy - o.top);
		var viewR = jloptions.width - jloptions.margin*2;
		var largeW = zoom/100 * viewR;
		var scale = largeW / w;
		
		var zlo = offX * scale - (viewR/2);
		var zto = offY * scale - (viewR/2);

		if(((posy-$(window).scrollTop())+jloptions.cursorOffsetY+jloptions.height) > (winH=$(window).height()) && jloptions.imageInv)
		{
			if($(this).data('orientation') != 2)
			{
				$(loupe).css('backgroundImage', 'url('+ jloptions.imageInv +')');
				$(view).css('backgroundImage', 'url('+ $(i).attr('src') +')');
				$(this).data('orientation', 2);
			}
			$(loupe).offset({top:posy-jloptions.cursorOffsetY-jloptions.height, left:posx+jloptions.cursorOffsetX});
			$(view)
			.css('backgroundPosition', (-zlo)+'px ' + (-zto)+'px')
			// Zoom disabled
			//.css('MozBackgroundSize', zoom + '%')
			//.css('backgroundSize', zoom + '%');	
		}
		else {
			if($(this).data('orientation') != 1)
			{
				$(loupe).css('backgroundImage', 'url('+ jloptions.image +')');
				$(view).css('backgroundImage', 'url('+ $(i).attr('src') +')');
				$(this).data('orientation', 1);
			}
			if($(loupe).css('backgroundImage') != ('url('+ jloptions.image +')'))
				$(loupe).css('backgroundImage', 'url('+ jloptions.image +')');
			$(loupe).offset({top:posy+jloptions.cursorOffsetY, left:posx+jloptions.cursorOffsetX});
			$(view)
			.css('backgroundPosition', (-zlo)+'px ' + (-zto)+'px')
			// Zoom disabled
			//.css('MozBackgroundSize', zoom + '%')
			//.css('backgroundSize', zoom + '%');
		}
	}
		
	$(this).each(function(){
		var h = $(this).parent('a').attr('href');
		var s = $(this).attr('src');
		s = (h) ? h : s;
		var i = $('<img />').attr('src', s);	
		$(this).data('zoom',i);	
		$(this).data('zoomValue', jloptions.minZoom);
	})
	.bind('mousemove', function(e){ 
		updateLoupe.call(this, e);
	})
	.bind('mouseleave', function(){
		$(loupe).stop(true, true);
		if(jloptions.fade) $(loupe).fadeOut(100);
		else $(loupe).hide();
	})
	.bind('mouseenter', function(){
		$(loupe).stop(true, true);
		if(jloptions.fade) $(loupe).fadeIn();
		else $(loupe).show();
	})
	/* Zoom disabled for being buggy
	.bind('mousewheel', function(e){
		var zoom = $(this).data('zoomValue');
		e.preventDefault();
		if(e.wheelDelta)
			zoom += e.wheelDelta / 120 * jloptions.zoomTick;
		else if(e.detail)
			zoom -= e.detail * jloptions.zoomTick;
		if(zoom < jloptions.minZoom)
			zoom = jloptions.minZoom;
		else if(zoom > jloptions.maxZoom)
			zoom = jloptions.maxZoom;
		
		$(this).data('zoomValue', zoom);
		updateLoupe.call(this, e);
	});
	*/
	
	return $(this);
};
	

$.support.cssProperty = (function() {
  function cssProperty(p, rp) {
    var b = document.body || document.documentElement;
    var s = b.style;
    if(typeof s == 'undefined') { return false; }
    if(typeof s[p] == 'string') { return rp ? p : true; }
    var v = ['Moz', 'Webkit', 'Khtml', 'O', 'Ms'];
    p = p.charAt(0).toUpperCase() + p.substr(1);
    for(var i=0; i<v.length; i++) {if(typeof s[v[i] + p] == 'string') { return rp ? (v[i] + p) : true; }}
  }
  return cssProperty;
})();


$(function(){ $('.jLoupe, .jloupe').jloupe(); });