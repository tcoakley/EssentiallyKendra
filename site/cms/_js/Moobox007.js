var Moobox007 = new Class({

	Implements:Options,
	/// Options
	options:{
		overlayOpacity:0.9,
		topPosition:80,
		initialWidth:200,
		initialHeight:200,
		resizeDuration:500,
		finalWidth:400,
		finalHeight:285,
		resizeTransition:'sine:in:out'/*function (ex. Transitions.Sine.easeIn) or string (ex. 'quint:out')*/
	},
	/// Initialize
	initialize: function(options){

		this.setOptions(options);
		this.formtags = $$('select','textarea');

		this.PrepareObjects();
		this.targetSize = {};
		this.targetSize.width = this.options.finalWidth;
		this.targetSize.height = this.options.finalHeight;
		this.targetSize.marginLeft = -(this.options.finalWidth/2).round();
		this.FlickrBox = $("FlickrContainer");

	},
	/// open
	openBox: function() {
		if(this.FlickrBox) {
			this.FlickrBox.setStyle("opacity","0");
			FlickrStopSlideTimer();
		}
		//Formtags
		if(this.formtags.length != 0){ this.formtags.setStyle('display','none') };
		//overlay
		this.overlay.setStyles({
			'display': 'block',
			'opacity': '0',
			'top': -$(window).getScroll().y,
			'height':$(window).getScrollSize().y+$(window).getScroll().y
		});
		this.overlay.tween('opacity',this.options.overlayOpacity);
		//video canister
		this.canister.setStyles({
			'top':$(window).getScroll().y+this.options.topPosition,
			'opacity': '1'
		});
		this.canister.morph(this.targetSize);

	},
	/// Close
	closeBox: function() {
		this.cancelAllEffects();
		this.overlay.setStyles({
			'display': 'none',
			'opacity': '0'
		});
		this.canister.setStyles({
			'opacity':0,
			'width':this.options.initialWidth,
			'height':this.options.initialHeight,
			'marginLeft':-(this.options.initialWidth/2)
		});
		this.video.dispose();
		this.CloseDiv.dispose();
		this.CaptionDiv.dispose();
		if(this.formtags.length != 0){ this.formtags.setStyle('display','inline') };
		if(this.FlickrBox) {
			this.FlickrBox.setStyle("opacity","1");
			FlickrStartSlideTimer();
	}
	},
	/// CancelEffects
	cancelAllEffects:function(){
		this.overlay.get('tween').cancel();
		this.canister.get('morph').cancel();
		this.canister.retrieve('setFinalHeight').cancel();
	},
	///  Prepare Objects
	PrepareObjects: function() {

		// Create Overlay
		this.overlay = new Element('div', {
			'id':'mb7Overlay',
			'styles': {
				'opacity':'0',
				'display':'none'
			}
		}).inject($(document.body));

		this.overlay.addEvent("click", function() {
			this.closeBox();
		}.bindWithEvent(this));

		//Create Video Canister
		this.canister = new Element('div', {
			'id':'mb7Canister',
			'styles': {
				'width':this.options.initialWidth,
				'height':this.options.initialHeight,
				'marginLeft':-(this.options.initialWidth/2),
				'opacity':0
			}
		}).inject($(document.body));
		this.canister.set('morph',{
			duration:this.options.resizeDuration,
			link:'chain',
			transition:this.options.resizeTransition
		});
		this.canister.store('setFinalHeight',new Fx.Tween(this.canister,{
			property:'height',
			duration:'short'
		}));
		this.canister.get('morph').addEvent('onComplete',function(){
			//Create Video Div
			this.video = new Element("div", {
				"id":"mb7Video"
			}).inject(this.canister);
			var so = new SWFObject("/_swf/PopupVideo.swf", "FlashHeader", "400", "285", "8", "#FFFFFF");
			so.addParam("allowScriptAccess", "sameDomain");
			so.addParam("quality", "high");
			so.addParam("scale", "noscale");
			so.addParam("menu", "false");
			so.addParam("loop", "false");
			so.addParam("wmode", "transparent");
			//so.addVariable("WebRoot", "http://wa007dev.miamicityballet.org/");
			so.addVariable("PreloadImage", this.PreloadImage);
			so.addVariable("VideoName", this.VideoName);
			so.write("mb7Video");
			// Create the caption bar
			this.canister.retrieve('setFinalHeight').start(this.options.finalHeight,this.options.finalHeight+35);

		}.bindWithEvent(this));


		this.canister.retrieve('setFinalHeight').addEvent('onComplete',function(){
			this.CloseDiv = new Element("div", {
				"id": "mb7CloseButton"
			}).inject(this.video,'after');
			this.CloseDiv.addEvent("click", function() {
				this.closeBox();
			}.bindWithEvent(this));
			this.CaptionDiv = new Element("div", {
				"id": "mb7CaptionText"
			}).inject(this.video, 'after');
			//alert(this.caption.indexOf("</i>"));
			if (this.caption.indexOf("</i>") > -1) {
				this.CaptionDiv.setStyle("font-style","italic");
				this.caption = this.caption.replace(/<i>/gi,"");
				this.caption = this.caption.replace(/<\/i>/gi,"");
			}
			this.CaptionDiv.appendText(this.caption);
		}.bindWithEvent(this));



		// Add Events
		$$('a').each(function(a){
			if( a.rel && a.rel.test(/^moobox007/i) ){
				a.addEvent("click", function() {
					this.openBox();
					this.caption = a.getProperty("title");
					this.PreloadImage = a.getProperty("preload");
					this.VideoName = a.getProperty("video");
				}.bindWithEvent(this));
			}
		},this);



	}


});

window.addEvent('domready', function(){
	mybox = new Moobox007();
});