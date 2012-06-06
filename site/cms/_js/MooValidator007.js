var MooValidator007 = new Class({

	Implements:Options,
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Options
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	options:{
		ShowTips: true,
		TipIcon: "/cms/_img/Info.gif",
		TipIconWidth: 20,
		TipIconHeight: 20,
		TipDelay: 0,
		TipXOffset: 25,
		TipYOffset: -20,
		ReqStyles: {"border-color": "#ff0000"},
		PassStyles: {"border-color": "#00cc00"},
		ErrorMessage: "The form is not ready for submission. Fields which are not ready should have a red border.\n\n"
	},
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Initialize
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	initialize: function(options){
		this.setOptions(options);
		this.objRequest = new Array();
		this.arrErrorMessages = new Array();
		this.arrState = new Array();
		this.IsSubmitting = false;

		this.ReqElements = $$("input.req","textarea.req", "select");
		this.ReqElements.each(function(ReqElement, loc, ReqElements){
			ReqElement.addEvent('keyup', function(event){
				TheValue = ReqElement.value;
				this.ValidationProcess(TheValue, ReqElement, loc);
			}.bindWithEvent(this));
			ReqElement.addEvent('change', function(event){
				TheValue = ReqElement.value;
				this.ValidationProcess(TheValue, ReqElement, loc);
			}.bindWithEvent(this));
			ReqElement.addEvent('blur', function(event){
				TheValue = ReqElement.value;
				this.ValidationProcess(TheValue, ReqElement, loc);
			}.bindWithEvent(this));
			this.arrState[loc] = false;
		},this);

		for (l = 0; l < this.ReqElements.length; l++) {
			ReqElement = this.ReqElements[l];
			this.objRequest[l] = "";
			TheValue = ReqElement.value;
			req = ReqElement.getProperty("alt");
			if (req != null) {
				Requests = req.split(",");
				var pass = true;
				var looper = 0;
				var objDiv = new Element("div", {
					"id" : "Requirements" + l + "_smartHover",
					"class" : "FormTips",
					"styles" : {
						"display": "none"
					}
				});
				while (looper < Requests.length) {
					req = Requests[looper];
					reqs = req.split("-");
					req1 = reqs[0];
					req2 = reqs[1];
					switch (req1) {
						case "ml":
							var el = new Element("div", {}).inject(objDiv);
							el.appendText("Must be " + req2 + " or more characters.");
							break;
						case "mx":
							var el = new Element("div", {}).inject(objDiv);
							el.appendText("Must be " + req2 + " or less characters.");
							break;
						case "em":
							var el = new Element("div", {}).inject(objDiv);
							el.appendText("Must be a valid email address.");
							break;
						case "an":
							var el = new Element("div", {}).inject(objDiv);
							el.appendText("Must be alphanumeric");
							break;
						case "ph":
							var el = new Element("div", {}).inject(objDiv);
							el.appendText("Must be a Phone Number");
							break;
						case "nm":
							var el = new Element("div", {}).inject(objDiv);
							el.appendText("Must be numeric.");
							break;
						case "al":
							var el = new Element("div", {}).inject(objDiv);
							el.appendText("Must be alpha characters.");
							break;
						case "dt":
							var el = new Element("div", {}).inject(objDiv);
							el.appendText("Must be a valid date.");
							break;
						case "uq":
							var el = new Element("div", {}).inject(objDiv);
							el.appendText("Must be unique.");
							break;
						case "mm":
							var el = new Element("div", {}).inject(objDiv);
							el.appendText("Must be unique.");
							break;
						case "sl":
							var el = new Element("div", {}).inject(objDiv);
							el.appendText("Must select a value");
							break;
					}
					looper++;
				}
			
			

				if (this.options.ShowTips) {
					var objImg  = new Element('img', {
							'id' : 'Requirements' + l,
							'src' : this.options.TipIcon,
							'styles' : {
								'display' : 'inline',
								'width' : this.options.TipIconWidth + 'px',
								'height' : this.options.TipIconHeight + 'px',
								'margin' : '-1px 0 0 5px'
							}
						}
					);

					objImg.inject(ReqElement, "after");
					objDiv.inject(objImg, "after");
				}
				this.ValidationProcess(TheValue, ReqElement, l);
			}
			
		}

		this.ReqForm = $$("form.MooValidator");
		this.ReqForm.addEvents({
			"submit": this.ValidateForm.bind(this)
		});


		smartHoverBox(
			   this.options.TipDelay, //delay before vanishing
			   this.options.TipXOffset, //x offset
			   this.options.TipYOffset,  //y offset
			   '_smartHover', //smart hover box suffix
			   'smarthbox_close' //hover box close class
		);

	},
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	ValidateForm
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	ValidateForm: function(e) {
		var l;
		var ReqElements = $$("input.req","textarea.req","select");
		var FormValid = true;
		this.arrErrorMessages = new Array();
		this.IsSubmitting = true;
		for (l = (ReqElements.length - 1); l >= 0 ; l--) {
			ReqElement = ReqElements[l];
			TheValue = ReqElement.value;
			if (!this.ValidationProcess(TheValue, ReqElement, l)) {
				FormValid = false;
				FocusField = ReqElement;
			}
		}
		if (FormValid) {
			//this.ReqForm.submit();
		} else {
			new Event(e).stop();
			var ErrorMessage = this.options.ErrorMessage;
			for (l = (this.arrErrorMessages.length - 1); l >=0; l--) {
				ErrorMessage += this.arrErrorMessages[l] + "\n";
			}
			alert(ErrorMessage);
			FocusField.focus();
			this.IsSubmitting = false;
		}
	},

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	ValidationProcess
	// 		Validater - extra
	// 		Validators
	//		ml - minimum length  extra = length
	//		mx - maximum length  extra = length
	//		em - Email
	//		an - AlphaNumeric
	//		ph - Phone
	//		nm - Numeric
	//		al - Alpha
	//		dt - Date
	//		mm - must match another field
	//		sl - must select on a select box
	//		uq - Unique (req2 is script name.  Will pass fieldname/value to the script)
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	ValidationProcess: function(TheValue, ReqElement, loc) {
		if(this.objRequest[loc] != "") {
			this.objRequest[loc].cancel();
			this.objRequest[loc] = "";
		}
		req = ReqElement.getProperty("alt");
		var pass = true;
		if (req != null) {
			Requests = req.split(",");
			var looper = 0;
			var SetTheState = true;
			while (looper < Requests.length && pass) {
				req = Requests[looper];
				reqs = req.split("-");
				req1 = reqs[0];
				req2 = reqs[1];
				var FieldName = ReqElement.getProperty("name");
				switch (req1) {
					case "ml":
						if (TheValue.length < parseInt(req2)) {
							pass = false;
							this.arrErrorMessages.push(FieldName + " must be at least " + req2 + " characters in length.");
						}
						break;
					case "mx":
						if (TheValue.length > parseInt(req2)) {
							pass = false;
							this.arrErrorMessages.push(FieldName + " must be at no more than " + req2 + " characters in length.");
						}
						break;
					case "em":
						if (!this.isEmail(TheValue)) {
							pass = false;
							this.arrErrorMessages.push(FieldName + " is not a valid Email address.");
						}
						break;
					case "an":
						if(!this.isAlphaNumeric(TheValue)) {
							pass = false;
							this.arrErrorMessages.push(FieldName + " must be at least alphanumeric. (consist only of numbers or letters)");
						}
						break;
					case "ph":
						if (!this.isPhoneNumber(TheValue)) {
							pass = false;
							this.arrErrorMessages.push(FieldName + " is not a valid phone number.");
						}
						break;
					case "nm":
						if (!this.isNumeric(TheValue)) {
							pass = false;
							this.arrErrorMessages.push(FieldName + " must be numeric. (consist only of numbers)");
						}
						break;
					case "al":
						if (!this.isAlpha(TheValue)) {
							pass = false;
							this.arrErrorMessages.push(FieldName + " must be alpha. (consist only of letters)");
						}
						break;
					case "dt":
						if(!this.isDate(TheValue)) {
							pass = false;
							this.arrErrorMessages.push(FieldName + " is not a valid date.");
						}
						break;
					case "mm":
						req2Value = document.getElementById(req2);
						req2Name = req2Value.name;
						req2Value = req2Value.value;
						if (TheValue != req2Value) {
							pass = false;
							this.arrErrorMessages.push(FieldName + " must match " + req2Name + ".");
						}
						break;
					case "uq":
						if (this.IsSubmitting) {
							this.RequireUnique(TheValue, ReqElement, loc, req2);
						} else {
							this.timer = $clear(this.timer);
							this.timer = this.RequireUnique.delay(400, this,[TheValue, ReqElement, loc, req2]);
						}
						SetTheState = false;
						break;
					case "sl":
						if(TheValue == "") {
							pass = false;
							this.arrErrorMessages.push(FieldName + " must be selected.");
						}
						break;
				}
				looper++;
			}
			
		}
		if (SetTheState) {
			this.SetState(pass, ReqElement, loc);
		}
		return pass;
	},
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetState
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	SetState: function(pass, ReqElement, loc) {
		var myEffect = new Fx.Morph(ReqElement, {duration: "short"});
		if (pass) {
			if (!this.arrState[loc]) {
				myEffect.start(this.options.PassStyles);
			}
			this.arrState[loc] = true;
		} else {
			if (this.arrState[loc]) {
				myEffect.start(this.options.ReqStyles);
			}
			this.arrState[loc] = false;
		}
	},
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	RequireUnique
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	RequireUnique: function(inValue, ReqElement, loc, ScriptName) {
		var FieldName = ReqElement.get("name");
		if(this.IsSubmitting) {
			this.objRequest[loc] = new Request({url: ScriptName,
				method: "post",
				data: FieldName + "=" + inValue,
				async: false,
				onSuccess: function(txt) {
					if (txt == "true") {
						this.SetState(true, ReqElement, loc);
					} else {
						this.SetState(false, ReqElement, loc);
					}
				}.bindWithEvent(this),
				onFailure: function() {
					alert("Failed to make request to '" + ScriptName + "'");
				}.bindWithEvent(this)
			});
			this.objRequest[loc].send();
		} else {
			this.objRequest[loc] = new Request({url: ScriptName,
				method: "post",
				data: FieldName + "=" + inValue,
				onSuccess: function(txt) {
					if (txt == "true") {
						this.SetState(true, ReqElement, loc);
					} else {
						this.SetState(false, ReqElement, loc);
					}
				}.bindWithEvent(this),
				onFailure: function() {
					alert("Failed to make request to '" + ScriptName + "'");
				}.bindWithEvent(this)
			});
			this.objRequest[loc].send();
		}
	},
	// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
	//	Validator functions
	// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
	// returns true if the string is empty
	isEmpty: function(str){
		return (str == null) || (str.length == 0);
	},
	// returns true if the string is a valid email
	isEmail: function(str){
		if(this.isEmpty(str)) return false;
		var re = /^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i
		return re.test(str);
	},
	// returns true if the string only contains characters A-Z or a-z
	isAlpha: function(str){
		var re = /[^a-zA-Z]/g
		if (re.test(str)) return false;
		return true;
	},
	// returns true if the string only contains characters 0-9
	isNumeric: function(str){
		var re = /[\D]/g
		if (re.test(str)) return false;
		return true;
	},
	// returns true if the string only contains characters A-Z, a-z or 0-9
	isAlphaNumeric: function(str){
		var re = /[^a-zA-Z0-9]/g
		if (re.test(str)) return false;
		return true;
	},
	// returns true if the string's length is between "min" and "max"
	isLengthBetween: function(str, min, max){
		return (str.length >= min)&&(str.length <= max);
	},
	// returns true if the string is a US phone number formatted as...
	// (000)000-0000, (000) 000-0000, 000-000-0000, 000.000.0000, 000 000 0000, 0000000000
	isPhoneNumber: function(str){
		var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}.*/
		return re.test(str);
	},
	// Is date
	isDate: function(dateStr) {
		//var datePat = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{2,4})$/;
		var datePat = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{2,4})$/;
		var matchArray = dateStr.match(datePat); // is the format ok?
		var datestatus=true;
		datemsg="";



		if (matchArray == null || matchArray[1]==null) {
			datemsg="----- Please enter date as mm/dd/yyyy " + "\n";
			return false;
		} else {
			if(matchArray[3]==null || matchArray[5]==null) {
				datemsg="----- Please enter date as mm/dd/yyyy " + "\n";
				return false;
			}
		}

		month = matchArray[1]; // p@rse date into variables
		day = matchArray[3];
		year = matchArray[5];

		if (year.length != 4 && year.length != 2) {
			datestatus = false;
		}

		if (month < 1 || month > 12) { // check month range
			datemsg=datemsg + "----- Month must be between 1 and 12." + "\n";
			datestatus=false;
		}

		if (day < 1 || day > 31) {
			datemsg=datemsg + "----- Day must be between 1 and 31." + "\n";
			datestatus=false;
		}
		if ((month==4 || month==6 || month==9 || month==11) && day==31) {
			datemsg=datemsg + "----- Month " + month + " doesn`t have 31 days!" + "\n";
			datestatus=false;
		}

		if (month == 2) { // check for february 29th
			var isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
			if (day > 29 || (day==29 && !isleap)) {
				datemsg=datemsg + "----- February " + year + " doesn`t have " + day + " days!" + "\n";
				datestatus=false;
			}
		}
		return datestatus;
	},
	// returns true if "str1" is the same as the "str2"
	isMatch: function(str1, str2){
		return str1 == str2;
	}


});
// #################################################################################
//	Domready init
// #################################################################################
window.addEvent('domready', function(){
	var myValidator = new MooValidator007();
});