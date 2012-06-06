var ButtonStatus = new Array();
var MenuStatus = new Array();
var arrMenus = new Array();

function MainMenuInit() {
	var Buttons = $$("#TopMenu li a");
	Buttons.each(DetailInit);
}

function DetailInit(Button, loc, Buttons) {
	var MenuName = Button.get('SubMenu');
	TheMenu = $(MenuName);
	if (TheMenu) {
		arrMenus[loc] = TheMenu;
		Button.addEvent("mouseenter", function() {
			ButtonStatus[loc] = true;
			CheckMenu(loc);
		});
		Button.addEvent("mouseleave", function() {
			ButtonStatus[loc] = false;
			CheckMenu(loc);
		});
		TheMenu.addEvent("mouseenter", function() {
			MenuStatus[loc] = true;
			CheckMenu(loc);
		});
		TheMenu.addEvent("mouseleave", function() {
			MenuStatus[loc] = false;
			CheckMenu(loc);
		});
	}
}

function CheckMenu(inLoc) {
	if (ButtonStatus[inLoc] || MenuStatus[inLoc]) {
		arrMenus[inLoc].setStyle("display", "block");
	} else {
		arrMenus[inLoc].setStyle("display", "none");
	}
}