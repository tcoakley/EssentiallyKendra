/*
onTick: function(position){
	if(this.options.snap) position = this.toPosition(this.step);
	this.knob.setStyle(this.property, position);
},
snap: false,
offset: 0,
range: false,
wheel: false,
steps: 100,
mode: 'horizontal'
*/

if( typeof MooTools != "undefined" ){
	var Scrollbar = new Class({
		Extends: Slider,
		Implements: [Events, Options],
		options:{
			mode: 'vertical',
			wheel: true,
			snap: false,
			onSnap: $empty,
			buttons: true,
			onScrollClick: function(){
			},
			onScroll: $empty,
			offSet: {
				top: 10,
				bottom: 10
			},
			onChange: function(step){
				var myFx = new Fx.Morph(this.content, {duration: 'short', transition: Fx.Transitions.Sine.easeOut});

				this.fireEvent('onScroll',step);

				/* THIS IF FOR THE SNAP... TO LOCK TO ELEMENTS INSIDE THE SCROLLBAR */
				this.options.snap = (this.options.mode == 'horizontal') ? this.options.snap : false; // IF mode == vertical no snapping allowed...
				(this.options.snap) ? this.snap() : null; // if options.snap is true then fire snap -- only happen if m	ode is set to horizontal

				if(this.options.mode == 'vertical'){
					if(this.tweenFlag){
					myFx.start({'margin-top': step * -1});
					}
					else{
						this.content.setStyle('margin-top',step * -1);
					}
				}
				else{
					if(this.tweenFlag){
						myFx.start({'margin-left': step * -1});
					}
					else{
						this.content.setStyle('margin-left',step * -1);
					}
				}
			}
		},
		initialize: function(container, options){
			this.container = $(container);
			this.tweenFlag = false;
			this.scrollbar = this.container.getElement('.scrollbar');
			this.content = this.container.getElement('.scrollContent');
			this.options.mode = ($type(options.mode)) ? options.mode : 'vertical';

			if(!this.getDifference()){
				return 0;
			}
			else{

				this.knob = this.container.getElement('.knob');

				if(!this.element) {
					this.element = new Element('div',{'class':'track'});
					this.knob.inject(this.element);
					this.element.inject(this.scrollbar);
				}

				this.setOptions(options);
				if(!this.contentWrapper){
					this.contentWrapper = new Element('div');
					this.contentWrapper.adopt(this.content);
					this.contentWrapper.inject(this.container,'top');
				}
				var styles = this.content.getStyles();
				this.contentWrapper.setStyles(styles);
				this.contentWrapper.setStyle('position','static');

				this.calculateSteps(this.options.mode);

				if(this.options.mode == 'vertical'){
					this.element.setStyles({
						'height':this.container.getSize().y,
						'position':'static',
						'float':'right'
					});
					this.content.setStyle('float','left');
				}else{
					var topTarget = this.container.getSize().y - this.element.getStyle('height').toInt()
					this.scrollbar.setStyles({
						'width':this.container.getSize().x,
						'position':'relative',
						'top': topTarget,
						'clear':'both',
						'float':'none',
						'left':0
					});
					if(!this.options.buttons){
						this.element.setStyle('width',this.container.getSize().x);
						this.element.setStyle('float','none');
					}
					this.scrollbar.inject(this.container,'top');
				}
				if(this.options.buttons) this.createButtons();
				this.scrollbar.addClass(this.options.mode);
				if (this.options.wheel) this.wheelEvent = this.container.addEvent('mousewheel', this.scrolledElement.bindWithEvent(this));

				if(this.options.buttons){
					this.sizeElement();
				}

				this.parent(this.element, this.knob, options);
			}
		},
		clickedElement: function(event){
			var mouseDown = new Event(event);
			mouseDown.stopPropagation();
			if(mouseDown.target.getParent() === this.knob){
				this.tweenFlag = false;
				mouseDown.stop();
			}else{
				this.fireEvent('onScrollClick');
				this.tweenFlag = true;
				this.parent(mouseDown);
			}
		},
		calculateSteps: function(mode){
			if(mode == 'vertical'){
				if(this.container.getScrollSize().y - this.container.getSize().y > 0){
					this.options.steps = this.container.getScrollSize().y - this.container.getSize().y;
					var ch = this.container.getScrollSize().y;
					var mh = this.container.getSize().y;
					var th = this.container.getSize().y;
					var diff = (th * mh)/ch;
					this.createKnob(diff.toInt());
				}
				else{
					this.knob.setStyle('display','none');
					this.element.setStyle('display','none');
				}
			}
			else{
				if(this.container.getScrollSize().x - this.container.getSize().x > 0){
					this.options.steps = this.container.getScrollSize().x - this.container.getSize().x;
					var ch = this.container.getScrollSize().x;
					var mh = this.container.getSize().x;
					var th = this.container.getSize().x;
					var diff = (th * mh)/ch;
					this.createKnob(diff.toInt());
				}
				else{
					this.knob.setStyle('display','none');
					this.element.setStyle('display','none');
				}
			}
		},
		scrolledElement: function(event){
			var mode = (this.options.mode == 'horizontal') ? (event.wheel > 0) : (event.wheel > 0);
			this.set(mode ? this.step - 40 : this.step + 40);
			if(event.type != 'mousedown'){
				this.tweenFlag = false;
				new Event(event).stop();
			}
			else{
			}
		},
		createKnob: function(totalSize){
			var style = '';
			var endSize;
			this.knob.addClass(this.options.mode);
			if(this.options.mode == 'vertical'){
				this.knob.setStyle('height',totalSize);
				style = 'height';
				endSize = 4;
			}
			else{
				style= 'width';
				this.knob.setStyle('width',totalSize);
				endSize = 4;
			}
			if(this.knob.getChildren().length >=3){
				this.knob.getElement('.knobMiddle').setStyle(style,totalSize - (2 * endSize));
			}else{
				var knobMiddle = new Element('div',{'class': 'knobMiddle'});
				knobMiddle.setStyle(style,totalSize - (2 * endSize));

				var knobTop = new Element('div',{
					'class':'knobTop'
				});
				var knobBottom = new Element('div',{
					'class':'knobBottom'
				});
				knobMiddle.inject(this.knob,'top');
				knobTop.inject(this.knob,'top');
				knobBottom.inject(knobMiddle,'after');
			}
		},
		createButtons: function(){
			var clickValueDown = -1;
			var clickValueUp = 1;
			if(this.options.mode == 'vertical'){
				var downStyles = {'position':'static','clear': 'both'}
			}
			else{
				this.element.setStyle('width',this.element.getSize().x);
				var downStyles = {'position':'static'}
			}

			if(!this.upButton){
				this.upButton = new Element('a',{
					'class':'scrollbarUp',
					'events':{
						'mousedown':this.startScroll.bind([this,clickValueUp]),
						'mouseup': this.stopScroll.bind(this),
						'mouseout': this.stopScroll.bind(this),
						'mouseover': function(){
							this.setStyle('background-position','center left');
						}
					}
				});
				this.upButton.inject(this.scrollbar,'top');
			}
			if(!this.downButton){
				this.downButton = new Element('a',{
					'class':'scrollbarDown',
					'events':{
						'mousedown':this.startScroll.bind([this,clickValueDown]),
						'mouseup': this.stopScroll.bind(this),
						'mouseout': this.stopScroll.bind(this),
						'mouseover': function(){
							this.setStyle('background-position','center left');
						}
					}
				});

				this.downButton.setStyles(downStyles);
				this.downButton.inject(this.scrollbar,'bottom');
			}

		},
		sizeElement: function(){

			if(this.options.mode == 'vertical'){
				var sum = this.upButton.getStyle('height').toInt();
				sum += this.downButton.getStyle('height').toInt();
				var diff = this.element.getSize().y - (sum);
				this.element.setStyle('height',diff);
			}
			else{
				var sum = this.upButton.getStyle('width').toInt();
				sum += this.downButton.getStyle('width').toInt();
				var diff = this.element.getSize().x - (sum);
				this.element.setStyle('width',diff);
			}
		},
		startScroll: function(e){
			new Event(e).stop();
			var direction = this[1];
			e.wheel = direction;
			e.target.setStyle('background-position','bottom left');
			this.tweenFlag = false;
			this[0].interval = (function(event){
				this.tweenFlag = false;
				this.container.fireEvent('mousewheel',e);
			}).bind(this[0]).periodical(40);
		},
		stopScroll: function(e){
			new Event(e).stop();
			e.target.setStyle('background-position','top left');
			$clear(this.interval);
		},
		scrollToElement: function(id){
			this.tweenFlag = true;
			if(this.content.getElement('#'+id)){
				if(this.options.mode == 'vertical') this.set(this.content.getElement('#'+id).getPosition(this.content).y);
				if(this.options.mode == 'horizontal') this.set(this.content.getElement('#'+id).getPosition(this.content).x);
			}
		},
		snap: function(){
			this.fireEvent('onSnap',this);
		},
		getDifference:function(){
			if(this.options.mode == 'vertical'){
				var diff = this.content.getCoordinates().height - this.container.getCoordinates().height;
			}
			else{
				var diff = this.content.getCoordinates().width - this.container.getCoordinates().width;
			}
			if(diff <= 0){
				return false;
			}
			else{
				return diff;
			}
		},
		refresh:function(){
			//IF TRACK DOESNT EXIST AND THE CONTENT IS LARGER THAN THE CONTAINER CALL this.initialize(this.container,this.options);
			if(!this.element && this.getDifference()){
				this.initialize(this.container,this.options);
			}
			//IF TRACK DOES EXIST AND CONTENT IS SMALLER THAN THE CONTAINER THEN HIDE SCROLLBAR AND REMOVE MOUSEWHEEL EVENT
			else if(this.element && !this.getDifference()){
                if(this.options.mode == "vertical"){
                    this.content.setStyle('margin-top',0);
                }
                else{
                    this.content.setStyle('margin-left',0);
                }
				this.container.removeEvents();
				this.scrollbar.setStyle('visibility','hidden');
			}
			//IF TRACK DOES EXIST AND CONTENT IS LARGER THAN THE CONTAINER THEN SHOW SCROLLBAR.
			else if(this.element && this.getDifference()){
                if(this.options.mode == "vertical"){
                    this.content.setStyle('margin-top',0);
                }
                else{
                    this.content.setStyle('margin-left',0);
                }
				this.initialize(this.container,this.options);
				this.scrollbar.setStyle('visibility','visible');
			}


		}

	});
}