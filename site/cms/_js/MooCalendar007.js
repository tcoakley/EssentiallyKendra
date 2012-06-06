var MooCalendar007 = new Class({

	Extends: MooDate007,

	Implements:Options,
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Options
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	options:{
		xOffset: 0,
		yOffset: 0,
		width: 180,
		height: 180,
		ArrowWidth: 20,
		cssGraphics: false,
		UseDate: true,
		DateFormat: "%m/%d/%y",
		align: "tr"
	},
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Initialize
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	initialize: function(options){

		this.setOptions(options);
		switch(this.options.align) {
			case "tl":
				this.VerticalAlign = "top";
				this.HorizontalAlign = "left";
				break;
			case "bl":
				this.VerticalAlign = "bottom";
				this.HorizontalAlign = "left";
				break;
			case "tr":
				this.VerticalAlign = "top";
				this.HorizontalAlign = "right";
				break;
			case "br":
				this.VerticalAlign = "bottom";
				this.HorizontalAlign = "right";
				break;
		}
		this.CurrentStatus = "closed";
		this.CurrentContainer = null;
		this.CellWidth = Math.round(this.options.width/7) - 1;
		this.CellHeight = Math.round((this.options.height - 42)/7) - 1;
		this.MonthWidth = Math.round(this.options.width/3) - 1;
		this.MonthHeight = Math.round((this.options.height - 42)/4) - 1;
		this.YearWidth = Math.round(this.options.width/4) - 1;
		this.YearHeight = Math.round((this.options.height - 42) / 5) -1;


		// Add Events
		$$('input').each(function(a){
			if( a.getProperty("rel") && a.getProperty("rel").test(/^moocalendar/i) ){
				a.addEvent("click", function() {
					this.OpenCalendar(a);
				}.bindWithEvent(this));
			}
		},this);

	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Cancel All Effects
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	CancelAllEffects: function() {
		this.MainContainer.get('tween').cancel();
		if (this.CurrentContainer) {
			this.CurrentContainer.get('tween').cancel();
		}
		if (this.InContainer) {
			this.InContainer.get('tween').cancel();
		}
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	CloseCalendar
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	CloseCalendar: function() {
		this.CancelAllEffects();
		$("mc007C").dispose();
		this.CurrentStatus = "closed";
		this.CurrentContainer = null;
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	OpenCalendar
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	OpenCalendar: function(CurrentElement) {
		// Close any already open calendars
		if ($("mc007C")) {
			$("mc007C").dispose();
			this.CurrentStatus = "closed";
		}
		this.CurrentElement = CurrentElement;

		//	Get the date
		var CurrentValue = CurrentElement.value;
		var CurrentDate = new Date();
		var ValueDate = new Date();
		if (this.options.UseDate) {
			ValueDate = this.ParseDate(CurrentValue);
			if (!ValueDate) {
				ValueDate = new Date();
			}
		}
		this.ValueDate = ValueDate;
		this.CurrentDate = CurrentDate;

		//	Calculate where to open at
		var coords = CurrentElement.getCoordinates();
		if (this.VerticalAlign == "top") {
			var CalTop = coords.top + this.options.yOffset;
		} else {
			var CalTop = (coords.top - this.options.height + coords.height) + this.options.yOffset;
		}
		if (this.HorizontalAlign == "right") {
			var CalLeft = (coords.left + coords.width) + this.options.xOffset;
		} else {
			var CalLeft = (coords.left - this.options.width) + this.options.xOffset;
		}

		//	Reusable Elements
		this.DivClear = new Element("div", {
			"class" : "clear"
		});

		//	Build the main container
		var MainContainer = new Element("div", {
			"id" : "mc007C",
			"styles" : {
				"position" : "absolute",
				"overflow" : "hidden",
				"top": CalTop,
				"left": CalLeft,
				"width": this.options.width + "px",
				"height": this.options.height + "px",
				'opacity':'0'
			}
		}).inject($(document.body));
		this.MainContainer = MainContainer;
		//DragBar
		var DragBar = new Element("div", {
			"class":"DragBar",
			"styles" : {
				"height": "21px",
				"line-height": "21px"
			}
		});
		var CloseButton = new Element("div", {
			"class":"CloseButton"
		});
		if (!this.options.cssGraphics) {
			CloseButton.appendText("X");
		}
		CloseButton.addEvent("click", function() {
			this.CloseCalendar();
		}.bindWithEvent(this));
		this.AddHover(CloseButton);
		CloseButton.inject(DragBar);
		DragBar.appendText(this.CurrentElement.getProperty("name"));
		DragBar.inject(this.MainContainer);
		this.DivClear.clone().inject(this.MainContainer);

		var coords = DragBar.getCoordinates();
		this.CalHeight = this.options.height - coords.height;

		this.ContentContainer = new Element("div", {
			"class" : "CalBackground",
			"styles": {
				"position": "absolute",
				"overflow" : "hidden",
				"width": this.options.width + "px",
				"height": this.CalHeight + "px"
			}
		});
		this.ContentContainer.inject(this.MainContainer);

		this.OpenMonthView("next", ValueDate);
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	OpenMonthView
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	OpenMonthView: function(Direction, TargetDate) {
		this.CancelAllEffects();
		Animate = false;
		this.cmX = 0;
		this.cmY = 0;
		if (this.CurrentContainer) {
			Animate = true;
			this.GetMargins(Direction);
		}
		var CalendarContainer = new Element("div", {
			"class" : "CalBackground",
			"styles": {
				"position": "absolute",
				"display": "inline",
				"width": this.options.width + "px",
				"height": this.CalHeight + "px",
				"overflow": "hidden",
				"margin-left": this.cmX + "px",
				"margin-top": this.cmY + "px"
			}
		});

		//Header row
		this.BuildHeaderRow(this.DateFormat(TargetDate,"%F %Y"), CalendarContainer);
		this.ArrowLeft.addEvent("click", function() {
			this.OpenMonthView("previous", this.DateAdd("m", -1, TargetDate));
		}.bindWithEvent(this));
		this.ArrowRight.addEvent("click", function() {
			this.OpenMonthView("next", this.DateAdd("m", 1, TargetDate));
		}.bindWithEvent(this));
		this.HeaderName.addEvent("click", function() {
			this.OpenMonthSelect("open", TargetDate);
		}.bindWithEvent(this));
		this.AddHover(this.HeaderName);

		//ColHeaders
		var HeaderDiv = new Element("div", {
			"styles" : {
				"border-bottom": "1px solid #555",
				"text-align": "center",
				"height": "21px",
				"line-height": "21px"
			}
		});
		var ColHeader = new Element("div", {
			"class": "DayName",
			"styles": {
				"display": "inline",
				"float": "left",
				"width": this.CellWidth + "px"
			}
		});
		var su = ColHeader.clone();
		su.appendText("Su");
		su.inject(HeaderDiv);
		var mo = ColHeader.clone();
		mo.appendText("Mo");
		mo.inject(HeaderDiv);
		var tu = ColHeader.clone();
		tu.appendText("Tu");
		tu.inject(HeaderDiv);
		var we = ColHeader.clone();
		we.appendText("We");
		we.inject(HeaderDiv);
		var th = ColHeader.clone();
		th.appendText("Th");
		th.inject(HeaderDiv);
		var fr = ColHeader.clone();
		fr.appendText("Fr");
		fr.inject(HeaderDiv);
		var sa = ColHeader.clone();
		sa.appendText("Sa");
		sa.inject(HeaderDiv);
		HeaderDiv.inject(CalendarContainer);
		this.DivClear.clone().inject(CalendarContainer);

		//	Days of month
		var ColLoc = 0;
		var PointerDate = new Date(TargetDate);
		PointerDate.setDate(1);
		PointerDate = this.DateAdd("d", -PointerDate.getDay(), PointerDate);
		var EndDate = new Date(TargetDate);
		EndDate.setDate(this.GetDaysInMonth(EndDate));
		EndDate = this.DateAdd("d", (6 - EndDate.getDay()), EndDate);
		while (PointerDate <= EndDate) {
			ColLoc++;
			if (TargetDate.getMonth() == PointerDate.getMonth()) {
				CCell = this.CreateCell("ThisMonth", PointerDate.getDate(), this.DateFormat(PointerDate,this.options.DateFormat));
			} else {
				CCell = this.CreateCell("DifferentMonth", PointerDate.getDate(), this.DateFormat(PointerDate,this.options.DateFormat));
			}
			CCell.inject(CalendarContainer);
			if (ColLoc > 6) {
				this.DivClear.clone().inject(CalendarContainer);
				ColLoc = 0;
			}
			PointerDate = this.DateAdd("d", 1, PointerDate);
		}


		CalendarContainer.inject(this.ContentContainer);
		if (Animate) {
			this.PerformSlide(CalendarContainer);
		} else {
			this.CurrentContainer = CalendarContainer;
		}

		if(this.CurrentStatus == "closed") {
			this.MainContainer.tween('opacity',1);
			this.MainContainer.get('tween').addEvent('onComplete',function(){
				var myDrag = new Drag(this.MainContainer);
				this.CurrentStatus = "month";
			}.bindWithEvent(this));
		}

	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	OpenMonthSelect
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	OpenMonthSelect: function(Direction, TargetDate) {
		this.CancelAllEffects();
		this.GetMargins(Direction);
		var MonthContainer = new Element("div", {
			"class" : "CalBackground",
			"styles": {
				"position": "absolute",
				"display": "inline",
				"width": this.options.width + "px",
				"height": this.CalHeight + "px",
				"overflow": "hidden",
				"margin-left": this.cmX + "px",
				"margin-top": this.cmY + "px"
			}
		});

		//Header row
		this.BuildHeaderRow(this.DateFormat(TargetDate,"%Y"), MonthContainer);
		this.ArrowLeft.addEvent("click", function() {
			this.OpenMonthSelect("previous", this.DateAdd("yyyy", -1, TargetDate));
		}.bindWithEvent(this));
		this.ArrowRight.addEvent("click", function() {
			this.OpenMonthSelect("next", this.DateAdd("yyyy", 1, TargetDate));
		}.bindWithEvent(this));
		this.HeaderName.addEvent("click", function() {
			this.OpenYearSelect("open", TargetDate);
		}.bindWithEvent(this));
		this.AddHover(this.HeaderName);

		//Build Months
		ColLoc = 0;
		var PointerDate = this.DateAdd("m", -TargetDate.getMonth(), TargetDate);
		CurrentYear = PointerDate.getFullYear();
		while(PointerDate.getFullYear() == CurrentYear) {
			ColLoc++;
			MonthCell = this.CreateMonthCell(PointerDate);
			MonthCell.inject(MonthContainer);
			if (ColLoc > 2) {
				this.DivClear.clone().inject(MonthContainer);
				ColLoc = 0;
			}
			PointerDate = this.DateAdd("m", 1, PointerDate);
		}
		MonthContainer.inject(this.ContentContainer);
		this.PerformSlide(MonthContainer);
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	OpenYearSelect
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	OpenYearSelect: function(Direction, TargetDate) {
		this.CancelAllEffects();
		this.GetMargins(Direction);
		var YearContainer = new Element("div", {
			"class" : "CalBackground",
			"styles": {
				"position": "absolute",
				"display": "inline",
				"width": this.options.width + "px",
				"height": this.CalHeight + "px",
				"overflow": "hidden",
				"margin-left": this.cmX + "px",
				"margin-top": this.cmY + "px"
			}
		});

		//Header row
		this.BuildHeaderRow("Select Year", YearContainer);
		this.ArrowLeft.addEvent("click", function() {
			this.OpenYearSelect("previous", this.DateAdd("yyyy", -20, TargetDate));
		}.bindWithEvent(this));
		this.ArrowRight.addEvent("click", function() {
			this.OpenYearSelect("next", this.DateAdd("yyyy", 20, TargetDate));
		}.bindWithEvent(this));

		//Build Years
		ColLoc = 0;
		var PointerDate = new Date(TargetDate);
		FinalYear = this.DateAdd("yyyy", 20, TargetDate);
		while(PointerDate.getFullYear() <= FinalYear.getFullYear()) {
			ColLoc++;
			YearCell = this.CreateYearCell(PointerDate);
			YearCell.inject(YearContainer);
			if (ColLoc > 3) {
				this.DivClear.clone().inject(YearContainer);
				ColLoc = 0;
			}
			PointerDate = this.DateAdd("yyyy", 1, PointerDate);
		}
		YearContainer.inject(this.ContentContainer);
		this.PerformSlide(YearContainer);
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetMargins
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	GetMargins: function(Direction) {
		switch (Direction) {
			case "next":
				this.cmX = this.options.width;
				this.cmY = 0;
				this.cmO = -this.options.width;
				this.SlideMargin = "margin-left";
				break;

			case "previous":
				this.cmX = -this.options.width;
				this.cmY = 0;
				this.cmO = this.options.width;
				this.SlideMargin = "margin-left";
				break;

			case "open":
				this.cmX = 0;
				this.cmY = -this.CalHeight;
				this.cmO = this.CalHeight;
				this.SlideMargin = "margin-top";
				break;

			case "close":
				this.cmX = 0;
				this.cmY = this.CalHeight;
				this.cmO = -this.CalHeight;
				this.SlideMargin = "margin-top";
				break;
		}
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	PerformSlide
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	PerformSlide: function(Container) {
		this.InContainer = Container;
		this.CurrentContainer.set('tween');
		this.CurrentContainer.tween(this.SlideMargin, this.cmO);
		this.InContainer.set('tween');
		this.InContainer.tween(this.SlideMargin, 0);
		this.InContainer.get('tween').addEvent('onComplete',function(){
			this.CurrentContainer.dispose();
			this.CurrentContainer = this.InContainer;
		}.bindWithEvent(this));
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	CreateCell	(Creates a date cell)
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	CreateCell: function(ClassName, DisplayDay, ReturnValue) {
		var nc = new Element("div", {
			"class": ClassName,
			"styles": {
				"width": this.CellWidth + "px",
				"height": this.CellHeight + "px",
				"line-height": this.CellHeight + "px",
				"text-align": "center",
				"display": "inline",
				"float": "left",
				"cursor": "pointer"
			}
		});
		nc.appendText(DisplayDay);
		nc.addEvent("click", function() {
			this.SetValue(ReturnValue);
		}.bindWithEvent(this));
		return nc;
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	CreateMonthCell
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	CreateMonthCell: function(TargetDate) {
		var mc = new Element("div", {
			"styles": {
				"width": this.MonthWidth + "px",
				"height": this.MonthHeight + "px",
				"float" : "left",
				"display": "inline",
				"text-align": "center",
				"cursor": "pointer"
			}
		});
		mc.appendText(this.DateFormat(TargetDate, "%M"));
		mc.addEvent("click", function() {
			this.OpenMonthView("close", TargetDate);
		}.bindWithEvent(this));
		return mc;
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	CreateYearCell
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	CreateYearCell: function(TargetDate) {
		var mc = new Element("div", {
			"styles": {
				"width": this.YearWidth + "px",
				"height": this.YearHeight + "px",
				"line-height": this.YearHeight + "px",
				"float" : "left",
				"display": "inline",
				"text-align": "center",
				"cursor": "pointer"
			}
		});
		mc.appendText(this.DateFormat(TargetDate, "%Y"));
		mc.addEvent("click", function() {
			this.OpenMonthSelect("close", TargetDate);
		}.bindWithEvent(this));
		return mc;
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	BuildHeaderRow
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	BuildHeaderRow: function(DisplayText, InjectionContainer) {
		//Header row
		this.ArrowLeft = new Element("div", {
			"class": "ArrowLeft",
			"styles": {
				"display": "inline",
				"float":"left",
				"width": this.options.ArrowWidth + "px",
				"cursor": "pointer",
				"margin-top": "50px"
			}
		});
		if (!this.options.cssGraphics) {
			this.ArrowLeft.appendText("<");
		}
		this.ArrowRight = new Element("div", {
			"class": "ArrowRight",
			"styles": {
				"display": "inline",
				"float":"right",
				"width": this.options.ArrowWidth + "px",
				"cursor": "pointer"
			}
		});
		if (!this.options.cssGraphics) {
			this.ArrowRight.appendText(">");
		}
		var HeaderBar = new Element("div", {
			"styles": {
				"height" : "21px",
				"text-align" : "center"
			}
		});
		var NameWidth = this.options.width - (this.options.ArrowWidth * 2);
		this.HeaderName = new Element("div", {
			"class": "MonthName",
			"styles": {
				"height" : "21px",
				"line-height" : "21px",
				"text-align": "center",
				"display" : "inline",
				"float" : "left",
				"width" : NameWidth + "px"
			}
		});


		this.HeaderName.appendText(DisplayText);
		this.ArrowLeft.inject(HeaderBar);
		this.HeaderName.inject(HeaderBar);
		this.ArrowRight.inject(HeaderBar);
		HeaderBar.inject(InjectionContainer);
		this.DivClear.clone().inject(InjectionContainer);
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	AddHover
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	AddHover: function(inElement) {
		inElement.setStyle("cursor","pointer");
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetValue
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	SetValue: function(inValue) {
		this.CurrentElement.value = inValue;
		this.CurrentElement.fireEvent("change");
		this.CloseCalendar();
	}


});
// #################################################################################
//	Domready init
// #################################################################################
window.addEvent('domready', function(){
	myCalendar = new MooCalendar007({"xOffset": 25});
});