var sgd = {
	version: '0.1alpha',
	debug: false
};

/**
 * @author jon
 */



 sgd.ui = {};




/**
 * Corner plugin based on the jQuery corners plugin by Dave Methin and Mike Alsup
 */


 //Class for doing corners
 sgd.ui.corner = new Class({
 	Implements: Options,
	options:{
		corners: null,
		fx: 'round',
		keepBorders: false,
		cornerColor: null,
		stripColor: null,
		cornerWidth: 10
	},

	initialize: function(options){
		this.changeOptions(options);

		//set variables
		this.edges = {T:0, B:1};
		this.opts = {
			TL: /top|tl/.test(this.options.corners),
			TR: /top|tr/.test(this.options.corners),
			BL: /bottom|bl/.test(this.options.corners),
			BR: /bottom|br/.test(this.options.corners)
		};

		if (!this.opts.TL && !this.opts.TR && !this.opts.BL && !this.opts.BR ) {
			this.opts = { TL:1, TR:1, BL:1, BR:1 };
		}

		this.strip = new Element('div',{
			styles: {
				'overflow' : 'hidden',
				'height' : '1px',
				'background-color' : this.options.stripColor,
				'border-style' : 'solid'
			}
		});

	},

	gpc: function(node){
		for (; node && node.nodeName.toLowerCase() != 'html'; node = node.parentNode) {
			var v = $(node).getStyle('background-color');
			if (v.indexOf('rgb') > 0) {
				if (Browser.Engine.webkit && v == 'rgba(0, 0, 0, 0)'){
					continue;
				}
				return v.rgbToHex();
			}
			if (v && v!='transparent') {
				return v;
			}
		}
		return '#ffffff';
	},

	getW: function(i){
		var width = this.options.cornerWidth;
		switch (this.options.fx) {
			case 'round': 	return Math.round(width*(1-Math.cos(Math.asin(i/width))));
			case 'cool': 	return Math.round(width*(1+Math.cos(Math.asin(i/width))));
			case 'sharp': 	return Math.round(width*(1-Math.cos(Math.acos(i/width))));
			case 'bite': 	return Math.round(width*(Math.cos(Math.asin((width-i-1)/width))));
			case 'slide': 	return Math.round(width*(Math.atan2(i,width/i)));
			case 'jut': 	return Math.round(width*(Math.atan2(width,(width-i-1))));
			case 'curl': 	return Math.round(width*(Math.atan(i)));
			case 'tear': 	return Math.round(width*(Math.cos(i)));
			case 'wicked': 	return Math.round(width*(Math.tan(i)));
			case 'long': 	return Math.round(width*(Math.sqrt(i)));
			case 'sculpt': 	return Math.round(width*(Math.log((width-i-1),width)));
			case 'dog': 	return (i&1) ? (i+1) : width;
			case 'dog2': 	return (i&2) ? (i+1) : width;
			case 'dog3': 	return (i&3) ? (i+1) : width;
			case 'fray': 	return (i%2)*width;
			case 'notch': 	return width;
			case 'bevel': 	return i+1;
		}
	},

	round: function(elements){
		$$(elements).each(function(item){
			item = $(item);
			var pad = {
				T: item.getStyle('padding-top').toInt(), 	R: item.getStyle('padding-right').toInt(),
				B: item.getStyle('padding-bottom').toInt(), L: item.getStyle('padding-left').toInt()
			};

			if (Browser.Engine.trident) {
				item.setStyle('zoom',1);
			}

			if (!this.options.keepBorders){
				item.setStyle('border','none');
			}
			this.strip.setStyle('border-color', this.options.cornerColor || this.gpc(item.getParent()));

			for (var j in this.edges) {
				var bot = this.edges[j];
				if ((bot && (this.opts.BL || this.opts.BR)) || (!bot && (this.opts.TL || this.opts.TR))) {
					this.strip.setStyle('border-style', 'none '+(this.opts[j+'R']?'solid':'none')+' none '+(this.opts[j+'L']?'solid':'none'));
					var d = new Element('div', {
						'class' :'x-corner'
					});

					bot ? d.inject(item,'bottom') : d.inject(item,'top');

					if (bot && item.getStyle('height') != 'auto') {
						if (item.getStyle('position') == 'static') {
							item.setStyle('position','relative');
						}
						d.setStyles({
							position: 'absolute',
							bottom: '0',
							left: '0',
							padding: '0',
							margin: '0'
						});
						if (Browser.Engine.trident) {
							d.style.setExpression('width','this.parentNode.offsetWidth');
						} else {
							d.setStyle('width', '100%');
						}
					} else if (!bot && Browser.Engine.trident) {
						if (item.getStyle('position') == 'static') {
							item.setStyle('position','relative');
						}
						d.setStyles({
							position: 'absolute',
							top: '0',
							left: '0',
							right: '0',
							padding: '0',
							margin: '0'
						});
						var bw = 0;
						if (Browser.Engine.trident4) {
							bw = item.getStyle('border-left-width').toInt() + item.getStyle('border-right-width').toInt();
						}
						Broswer.Engine.trident4 ? d.style.setExpression('width', 'this.parentNode.offsetWidth - '+bw+'+ "px"') : d.setStyle('width','100%');
					} else {
						d.setStyle('margin', !bot ? '-'+pad.T+'px -'+pad.R+'px '+(pad.T-this.options.cornerWidth)+'px -'+pad.L+'px':
													(pad.B-this.options.cornerWidth)+'px -'+pad.R+'px -'+pad.B+'px -'+pad.L+'px');
					}

					for (var i=0; i < this.options.cornerWidth; i++) {
						var w = Math.max(0, this.getW(i));
						var e = this.strip.cloneNode(false);
						$(e).setStyle('border-width', '0 '+(this.opts[j+'R']?w:0)+'px 0 '+(this.opts[j+'L']?w:0)+'px');
						bot ? e.inject(d,'bottom') : e.inject(d,'top');
					}
				}
			}
		}, this);
		return elements;
	},

	unround: function(elements){
		$$(elements).each(function(item){
			$(item).getElements('.x-corner').destroy();
		})
		return elements;
	},

	changeOptions: function(options){
		this.setOptions(options);
		return this;
	}
 });

//we should create a shortcut as part of the Element object and the Elements array
Element.implement({

	c: null,

	corner: function(options){
		c = new sgd.ui.corner(options);
		c.round(this);
		return this;
	},

	uncorner: function(){
		c.unround(this);
	}
})


