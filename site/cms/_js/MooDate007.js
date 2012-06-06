var MooDate007 = new Class({

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetMonthName
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	GetMonthName: function(inDate, Abbrev) {
		if (Abbrev == null) {
			Abbrev = false;
		}
		if (Abbrev) {
			var MonthNames = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		} else {
			var MonthNames = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		}
		return MonthNames[inDate.getMonth()];
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetDayName
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	GetDayName: function(inDate, Abbrev) {
		if (Abbrev == null) {
			Abbrev = false;
		}
		if (Abbrev) {
			var DayNames = new Array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
		} else {
			var DayNames = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
		}
		return DayNames[inDate.getDay()];
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetYear
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	GetYear: function(inDate, Short) {
		if (Short == null) {
			Short = false;
		}
		var rv = inDate.getFullYear();
		if (Short) {
			old = rv;
			rv = rv.toString().slice(2);
		}
		return rv;
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetDaysInMonth
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	GetDaysInMonth: function(inDate) {
		var TheMonth = inDate.getMonth();
		if (this.GetYear(inDate)/4 == (this.GetYear(inDate)/4).round()) {
			var arrDays = new Array(31,29,31,30,31,30,31,31,30,31,30,31);
		} else {
			var arrDays = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
		}
		return arrDays[TheMonth];
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	DateAdd
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	DateAdd: function(p_Interval, p_Number, p_Date){
		var dt = new Date(p_Date);
		p_Number = new Number(p_Number);

		switch(p_Interval.toLowerCase()){
			case "yyyy": {	// year
				dt.setFullYear(dt.getFullYear() + p_Number);
				break;
			}
			case "q": {		//quarter
				dt.setMonth(dt.getMonth() + (p_Number*3));
				break;
			}
			case "m": {		//month
				dt.setMonth(dt.getMonth() + p_Number);
				break;
			}
			case "y":			// day of year
			case "d":			// day
			case "w": {			// weekday
				dt.setDate(dt.getDate() + p_Number);
				break;
			}
			case "ww": {		// week of year
				dt.setDate(dt.getDate() + (p_Number*7));
				break;
			}
			case "h": {			// hour
				dt.setHours(dt.getHours() + p_Number);
				break;
			}
			case "n": {		// minute
				dt.setMinutes(dt.getMinutes() + p_Number);
				break;
			}
			case "s": {
				dt.setSeconds(dt.getSeconds() + p_Number);
				break;
			}
			case "ms": {	// JS extension
				dt.setMilliseconds(dt.getMilliseconds() + p_Number);
				break;
			}
			default: {
				return "invalid interval: '" + p_Interval + "'";
			}
		}
		return dt;
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	isDate
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	isDate: function(inDateString) {
		var datePat = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{2,4})$/;
		var matchArray = inDateString.match(datePat);
		var datestatus=true;

		if (matchArray == null || matchArray[1]==null) {
			return false;
		} else {
			if(matchArray[3]==null || matchArray[5]==null) {
				return false;
			}
		}

		month = matchArray[1]; // p@rse date into variables
		day = matchArray[3];
		year = matchArray[5];
		this.month = month;
		this.day = day;
		if (year.length == 2) {
			this.year = "20" + year;
		} else {
			this.year = year;
		}

		if (year.length != 4 && year.length != 2) {
			datestatus = false;
		}

		if (month < 1 || month > 12) { // check month range
			datestatus=false;
		}

		if (day < 1 || day > 31) {
			datestatus=false;
		}
		if ((month==4 || month==6 || month==9 || month==11) && day==31) {
			datestatus=false;
		}

		if (month == 2) { // check for february 29th
			var isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
			if (day > 29 || (day==29 && !isleap)) {
				datestatus=false;
			}
		}
		return datestatus;
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	ParseDate
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	ParseDate: function(inDateString) {
		if (this.isDate(inDateString)) {
			var rv = new Date();
			rv.setFullYear(this.year);
			rv.setMonth(this.month -1);
			rv.setDate(this.day);

			return rv;
		} else {
			return false;
		}
	},

	// ---------------------------------------------------------------------------------
	//	DateFormat
	//	Accepts most of the PHP date formatting codes.
	//	• %a - "am" or "pm"
	//	• %A - "AM" or "PM"
	//	• %d - day of the month, 2 digits with leading zeros; i.e. "01" to "31"
	//	• %j - day of the month without leading zeros; i.e. "1" to "31"
	//	• %D - day of the week, textual, 3 letters; i.e. "Fri"
	//	• %l (lowercase ’L’) - day of the week, textual, long; i.e. "Friday"
	//	• %g - hour, 12-hour format without leading zeros; i.e. "1" to "12"
	//	• %G - hour, 24-hour format without leading zeros; i.e. "0" to "23"
	//	• %h - hour, 12-hour format; i.e. "01" to "12"
	//	• %H - hour, 24-hour format; i.e. "00" to "23"
	//	• %i - minutes; i.e. "00" to "59"
	//	• %m - month; i.e. "01" to "12"
	//	• %M - month, textual, 3 letters; i.e. "Jan"
	//	• %F - month, textual, long; i.e. "January"
	//	• %n - month without leading zeros; i.e. "1" to "12"
	//	• %s - seconds; i.e. "00" to "59"
	//	• %Y - year, 4 digits; i.e. "1999"
	//	• %y - year, 2 digits; i.e. "99"
	// ---------------------------------------------------------------------------------
	DateFormat: function(inDate,inFormat) {
		var rv = inFormat;
		rv = rv.replace(/\%d/g,this.pad(inDate.getDate()));
		rv = rv.replace(/\%j/g,inDate.getDate());
		rv = rv.replace(/\%D/g,this.GetDayName(inDate,true));
		rv = rv.replace(/\%l/g,this.GetDayName(inDate));
		rv = rv.replace(/\%g/g,this.GetHours(inDate,true));
		rv = rv.replace(/\%G/g,this.GetHours(inDate));
		rv = rv.replace(/\%h/g,this.pad(this.GetHours(inDate,true)));
		rv = rv.replace(/\%H/g,this.pad(this.GetHours(inDate)));
		rv = rv.replace(/\%i/g,this.pad(inDate.getMinutes()));
		rv = rv.replace(/\%m/g,this.pad(inDate.getMonth() + 1));
		rv = rv.replace(/\%M/g,this.GetMonthName(inDate,true));
		rv = rv.replace(/\%F/g,this.GetMonthName(inDate));
		rv = rv.replace(/\%n/g,inDate.getMonth() + 1);
		rv = rv.replace(/\%s/g,inDate.getSeconds());
		rv = rv.replace(/\%Y/g,this.GetYear(inDate));
		rv = rv.replace(/\%y/g,this.GetYear(inDate,true));
		if (this.GetHours(inDate) > 12) {
			rv = rv.replace(/\%a/g,"pm");
			rv = rv.replace(/\%A/g,"PM");
		} else {
			rv = rv.replace(/\%a/g,"am");
			rv = rv.replace(/\%A/g,"AM");
		}
		return rv;
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetHours
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	GetHours: function(inDate, TwelveHour) {
		if(TwelveHour == null) {
			TwelveHour = false;
		}
		rv = inDate.getHours();
		if (TwelveHour) {
			if (rv > 12) {
				rv-=12;
			}
		}
		return rv;
	},
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	pad
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	pad: function(inValue) {
		inValue = inValue.toString();
		if (inValue.length == 1) {
			inValue = "0" + inValue;
		}
		return inValue;
	}

});