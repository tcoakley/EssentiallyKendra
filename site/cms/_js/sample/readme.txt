/**
* @file elSelect.js
* @downloaded from http://www.cult-f.net/2007/12/14/elselect/
* @author Sergey Korzhov aka elPas0
* @site  http://www.cult-f.net
* @date December 14, 2007
* 
*/

Hi
Here is short info on how to use it.

elSelect is made with mootools Framework. 
You need following components:
- core  ( core )
- class  ( all )
- native ( all )
- element ( event, filters, selectors )
- window ( domready )

Inside the pack you will find
/js
    * elSelect.js - the class itself,
    * mootools.js - mootools framework with needed components
/theme
    * style.css - default stylesheet containing all the possible classnames you can style
	* top_center.gif top_right.gif top_line.gif top_left.gif - images for top round corners
	* select_bg.gif select_arrow.gif - for styling select element
	* bottom_right.gif bottom_left.gif bottom_center.gif  - images for bottom round corners

	
In the head of your html insert

<code>
	<script src="js/mootools.js" type="text/javascript"></script>
	<script src="js/elSelect.js" type="text/javascript"></script>	
</code>

Ok, now class is included, so we can create new element

<code>
	<script type="text/javascript">
	window.addEvent('domready', function(){
		var mySelect = new elSelect( {container : 'someId'} );
	</script>
</code>	

and html code like this:

<code>
	<div id="someId">
		<select name="test">
			<option>-select number-</option>
			<option value="1" class="icon_1">one</option>
			<option value="2" class="icon_2">two</option>
			<option value="3" disabled="disabled">three</option>
			<option value="4">fourdrdfghhffourdrdfghhffourdrdfghhf</option>
			<option value="5">five</option>
			<option value="6">six</option>
		</select>
	</div>
</code>

And in the result the structure of new control will be following

<code>
<div id="mySelect">
	<div class="elSelect">
		<div class="selected">
			<div class="selectedOption">-select number-</div>
			<div class="dropDown"></div>
		</div>
		<div class="clear"/>
		<div class="optionsContainer">
			<div class="optionsContainerTop">
				<div>
					<div></div>
				</div>
			</div>
			<div class="option selected">-select number-</div>
			<div class="option icon_1">one</div>
			<div class="option icon_2">two</div>
			<div class="option disabled">three</div>
			<div class="option">f</div>
			<div class="option">five</div>
			<div class="option">six</div>
			<div class="optionsContainerBottom">
				<div>
					<div></div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="test" value="" >
</div>
</code>

This select acts as usual if it is a part of form cause selected value duplicated to hidden field.
If you noticed, to add an icon to the option you need to add a class to option element.
<code>
	<option value="1" class="icon_1">one</option>
</code>