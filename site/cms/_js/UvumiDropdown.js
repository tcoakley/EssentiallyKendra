/*
UvumiTools Dropdown Menu v1.0.1 http://uvumi.com/tools/dropdown.html

Copyright (c) 2008 Uvumi LLC

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
*/

var UvumiDropdown = new Class({
	Implements:Options,

	options:{
		duration:250,	//duration in millisecond of opening/closing effect
		transition:Fx.Transitions.linear	//effect's transitions. See http://docs.mootools.net/Fx/Fx.Transitions for more details
	},

	initialize: function(menu,options){
		this.menu = menu;
		this.setOptions(options);
		window.addEvent('domready',this.domReady.bind(this));
	},

	domReady:function(){
		this.menu = $(this.menu);
		if(!$defined(this.menu)){
			return false;
		}
		if(this.menu.get('tag')!='ul'){
			this.menu = this.menu.getFirst('ul');
			if(!$defined(this.menu)){
				return false;
			}
		}
		this.menu.setStyles({
			overflow:'hidden',
			height:0,
			marginLeft:(Browser.Engine.trident?1:-1)
		});
		//we call the createSubmenu function on the main UL, which is a recursive function
		this.createSubmenu(this.menu);
		//the LIs must be floated to be displayed horisotally
		this.menu.getChildren('li').setStyles({
			'float':'left',
			display:'block',
			top:0
		});
		//We create an extar LI which role will be to clear the floats of the others
		var clear = new Element('li',{
			html:"&nbsp;",
			styles:{
				clear:'both',
				display:(Browser.Engine.trident?'inline':'block'), //took me forever to find that fix
				position:'relative',
				top:0,
				height:0,
				width:0,
				fontSize:0,
				lineHeight:0,
				margin:0,
				padding:0
			}
		}).inject(this.menu);
		this.menu.setStyles({
			height:'auto',
			overflow:'visible',
			visibility:'visible'
		});
		this.menu.getElements('a').setStyle('display',(Browser.Engine.trident?'inline-block':'block'));
	},

	createSubmenu:function(ul){
		//we collect all the LI of the ul
		var LIs = ul.getChildren('li');
		var offset = 0;
		//loop through the LIs
		LIs.each(function(li){
			li.setStyles({
				position:'relative',
				display:'block',
				top:-offset,
				zIndex:1
			});
			offset += li.getSize().y;
			var innerUl = li.getFirst('ul');
			//if the current LI contains a UL
			if($defined(innerUl)){
				innerUl.setStyle('display','none');
				//if the current UL is the main one, that means we are still in the top row, and the submenu must drop down
				if(ul == this.menu){
					var x = 0;
					var y = li.getSize().y;
					this.options.link='cancel';
					li.store('animation',new Fx.Elements($$(innerUl,innerUl.getChildren('li')).setStyle('opacity',0),this.options));
				//if the current UL is not the main one, the sub menu must pop from the side
				}else{
					var x = li.getSize().x-li.getStyle('border-left-width').toInt();
					var y = -li.getStyle('border-bottom-width').toInt();
					this.options.link='chain';
					li.store('animation',new Fx.Elements($$(innerUl,innerUl.getChildren('li')).setStyle('opacity',0),this.options));
					offset=li.getSize().y+li.getPosition(this.menu).y;
				}
				innerUl.setStyles({
					position:'absolute',
					display:'block',
					top:y,
					left:x,
					marginLeft:-x,
					opacity:0
				});
				//we call the createsubmenu function again, on the new UL
				this.createSubmenu(innerUl);
				//apply events to make the submenu appears when hovering the LI
				li.addEvents({
					mouseenter: this.showChildList.bind(this,li),
					mouseleave: this.hideChildList.bind(this,li)
				}).addClass('submenu');
			}
		},this);
	},

	//display submenu
	showChildList:function(li){
		var ul = li.getFirst('ul');
		var LIs =  $$(ul.getChildren('li'));
		var animation = li.retrieve('animation');
		//if the parent menu is not the main menu, the submenu must pop from the side
		if(li.getParent('ul')!=this.menu){
			animation.cancel();
			animation.start({
				0:{
					opacity:1,
					marginLeft:0
				},
				1:{
					opacity:1
				}
			});
			var animobject={};
		//if the parent menu us the main menu, the submenu must drop down
		}else{
			var animobject = {0:{opacity:1}};
		}
		LIs.each(function(innerli,i){
			animobject[i+1]={
				top:0,
				opacity:1
			};
		});
		li.setStyle('z-index',99);
		animation.start(animobject);
	},

	//hide the menu
	hideChildList:function(li){
		var animation = li.retrieve('animation');
		var ul = li.getFirst('ul');
		var LIs =  $$(ul.getChildren('li'));
		var offset = 0;
		var animobject={};
		LIs.each(function(innerli,i){
			animobject[i+1]={
				top:-offset,
				opacity:0
			};
			offset += innerli.getSize().y;
		});
		li.setStyle('z-index',1);
		//if the parent menu is not the main menu, the submenu must fold up, and go to the left
		if(li.getParent('ul')!=this.menu){
			animobject[1]=null;
			animation.cancel();
			animation.start(animobject);
			animation.start({
				0:{
					opacity:0,
					marginLeft:-ul.getSize().x
				},
				1:{
					opacity:0
				}
			});
		//if the parent menu is the main menu, the submenu must just fold up
		}else{
			animobject[0]={opacity:0};
			animation.start(animobject);
		}

	}
});