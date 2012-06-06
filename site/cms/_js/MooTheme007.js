var MooTheme007 = new Class({

	Implements:Options,
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Options
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	options:{

	},
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Initialize
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	initialize: function(options){

		this.setOptions(options);


		// init variables
		this.CurrentState = false;
		this.ClosedHeight = 39;
		this.OpenHeight = 167;
		this.ArrowWidth = 32;
		this.SlideContainerWidth = null;
		this.SliderWidth = 0;
		this.CurrentSlide = 0;

		// Set up objects
		this.ChangeThemeButton = $("ChangeTheme");
		this.ThemeBar = $("ThemeBar");
		this.ThemeSelector = $("ThemeSelector");
		this.SlideContainer = $("SlideContainer");
		this.SlideBar = $("Slider");
		this.ArrowLeft = $("ArrowLeft");
		this.ArrowRight = $("ArrowRight");

		$$(".ThemeCell").each(function() {
			this.SliderWidth += 150;
		}.bindWithEvent(this));

		this.SlideBar.setStyles({"width": this.SliderWidth + "px"});

		// Set up button events
		this.ChangeThemeButton.addEvent("click", function() {
			this.ToggleThemeDisplay();
		}.bindWithEvent(this));


		this.ArrowRight.addEvent("click", function() {
			if (Math.abs(this.CurrentSlide) < this.MaxSlide) {
				this.CurrentSlide -= 150;
				Margin = this.CurrentSlide + "px";
				this.SlideBar.tween('margin-left',Margin);
			}
		}.bindWithEvent(this));

		this.ArrowLeft.addEvent("click", function() {
			if (Math.abs(this.CurrentSlide) > 0) {
				this.CurrentSlide += 150;
				Margin = this.CurrentSlide + "px";
				this.SlideBar.tween('margin-left',Margin);
			}
		}.bindWithEvent(this));


		// Setup Scrolling calculations
		this.SetupThemeScroller();
		window.addEvent("resize", function() {
			this.SetupThemeScroller();
		}.bindWithEvent(this));


	},

	SetupThemeScroller: function() {
		coords = this.ThemeSelector.getCoordinates();
		this.CorrectWidth = coords.width - (this.ArrowWidth * 2);
		this.SlideContainer.setStyles({"width" : this.CorrectWidth + "px"});
		this.MaxSlide = this.SliderWidth - this.CorrectWidth;

		if (ThemeAutoOpen) {
			this.ToggleThemeDisplay();
		}
	},

	ToggleThemeDisplay: function() {
		var TargetHeight = null;
		if(this.CurrentState) {
			TargetHeight = this.ClosedHeight;
			this.CurrentState = false;
		} else {
			TargetHeight = this.OpenHeight;
			this.CurrentState = true;
		}
		Height = TargetHeight + "px";
		this.ThemeBar.tween('height',Height);

	}


});
// #################################################################################
//	Domready init
// #################################################################################
window.addEvent('domready', function(){
	var myTheme = new MooTheme007();
});