var smartHoverBox = function(boxTimer, xOffset, yOffset, smartBoxSuffix, smartBoxClose) {
	var smartBoxes = $(document.body).getElements('[id$=' + smartBoxSuffix + ']');
	var closeElem = $(document.body).getElements('.' + smartBoxClose);
	var closeBoxes = function() { smartBoxes.setStyle('display', 'none'); };
	closeElem.addEvent('click', function(){ closeBoxes() }).setStyle('cursor', 'pointer');
	var closeBoxesTimer = 0;
	smartBoxes.each(function(item){
		var currentBox = item.getProperty('id');
		currentBox = currentBox.replace('' + smartBoxSuffix + '', '');


		$(currentBox).addEvent('mouseleave', function(){
			closeBoxesTimer = closeBoxes.delay(boxTimer);
		});

		item.addEvent('mouseleave', function(){
			closeBoxesTimer = closeBoxes.delay(boxTimer);
		});

		$(currentBox).addEvent('mouseenter', function(){
			if($defined(closeBoxesTimer)) $clear(closeBoxesTimer);
		});

		item.addEvent('mouseenter', function(){
			if($defined(closeBoxesTimer)) $clear(closeBoxesTimer);
		});



		item.setStyle('margin', '0');
		$(currentBox).addEvent('mouseenter', function(){
				smartBoxes.setStyle('display', 'none');
				item.setStyles({ display: 'block', position: 'absolute' }).setStyle('z-index', '1000000');

				//coordinates and size vars and math
				var windowSize = $(window).getSize();
				var windowScroll = $(window).getScroll();
				var halfWindowY = windowSize.y / 2;
				var halfWindowX = windowSize.x / 2;
				var boxSize = item.getSize();
				var inputPOS = $(currentBox).getCoordinates();
				var inputCOOR = $(currentBox).getPosition();
				var inputSize = $(currentBox).getSize();
				var inputBottomPOS = inputPOS.top + inputSize.y;
				var inputBottomPOSAdjust = inputBottomPOS - windowScroll.y
				var inputLeftPOS = inputPOS.left + xOffset;
				var inputRightPOS = inputPOS.right;
				var leftOffset = inputCOOR.x + xOffset;

				if(halfWindowY < inputBottomPOSAdjust) {
					item.setStyle('top', inputPOS.top - boxSize.y - yOffset);
					if (inputLeftPOS < halfWindowX) { item.setStyle('left', leftOffset); }
					else { item.setStyle('left', (inputPOS.right - boxSize.x) - xOffset); };
				}
				else {
					item.setStyle('top', inputBottomPOS + yOffset);
					if (inputLeftPOS < halfWindowX) { item.setStyle('left', leftOffset); }
					else { item.setStyle('left', (inputPOS.right - boxSize.x) - xOffset); };
				};
		}).setStyle('cursor', 'pointer');
	});
};