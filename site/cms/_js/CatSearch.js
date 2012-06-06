var CurrentCatLevel = 0;
var arrCats = new Array("","","","");
var objCatRequest = null;
var objProdRequest = null;
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//	LoadCategory
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function LoadCategory(CatLevel, Category, NumProducts) {
	if(CurrentCatLevel >= CatLevel && CatLevel < 5) {
		for(l = CurrentCatLevel; l >= CatLevel; l--) {
			if ($("Cat" + l)) {
				$("Cat" + l).empty();
				$("CatHeader" + l).empty();
				arrCats[l-1] = "";
			}
		}
	}
	if (objCatRequest != null) {
		objCatRequest.cancel();
		objCatRequest = null;
	}
	if (objProdRequest != null) {
		objProdRequest.cancel();
		objProdRequest = null;
	}

	var MainContainer = $("Cat" + CatLevel);

	CurrentCatLevel = CatLevel;
	arrCats[CatLevel-2] = Category;

	if(parseInt(CatLevel) < 5) {
		var LoadingDiv = new Element("div", {
			"class": "Loading",
			"id": "LoadingDiv"
		});
		LoadingDiv.inject(MainContainer);
		var JsonURL = "CatJson.php?TargetLevel=" + CatLevel;
		if (Category != null) {
			l = 0;
			while ( l < 4 && arrCats[l].length > 0) {
				JsonURL += "&cat" + (l+1).toString() + "=" + escape(arrCats[l]);
				l++;
			}
			$("CatHeader" + CatLevel).appendText(Category);
		}

		objCatRequest = new Request.JSON({
			url: JsonURL,
			onComplete: function(jsonObj) {
				if(jsonObj != null) {
					DisplayCategories(jsonObj.categories, MainContainer, LoadingDiv);
				}
				objCatRequest = null;
			}
		}).send();
	}
	$("ProductsList").empty();
	if (NumProducts != null && (NumProducts < 21 || CatLevel > 2) ) {
		var ProdLoadingDiv = new Element("div", {
			"class": "LoadingProducts",
			"id": "ProdLoadingDiv"
		});
		ProdLoadingDiv.inject($("ProductsList"));
		JsonURL = "ProdJson.php?level=" + (parseInt(CatLevel) - 1).toString();
		l = 0;
		while ( l < 4 && arrCats[l].length > 0) {
			JsonURL += "&cat" + (l+1).toString() + "=" + escape(arrCats[l]);
			l++;
		}
		objProdRequest = new Request.JSON({
			url: JsonURL,
			onComplete: function(objProdJson) {
				if(objProdJson != null) {
					DisplayProducts(objProdJson.products, ProdLoadingDiv);
				}
				objProdRequest = null;
			}
		}).send();
	}
}
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//	DisplayCategories
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function DisplayCategories(categories, container, LoadingDiv) {
	var cContent = new Element("div", {
		"class" : "ColumnContainer",
		"id" : "cscc"
	});
	var sContent = new Element("div", {
		"class" : "scrollContent",
		"id" : "cssc"
	});
	sContent.inject(cContent);

	var Sbar = new Element("div", {
		"class" : "scrollbar"
	});
	var Knob = new Element("div", {
		"class" : "knob"
	});
	Knob.inject(Sbar);
	Sbar.inject(sContent, 'after');
	categories.each(function(c) {
		DisplayRow(CurrentCatLevel, c.category, c.NumProducts,sContent);
	});
	LoadingDiv.dispose();
	cContent.inject(container);
	var myScrollbars = new Scrollbar(cContent,{});
}
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//	DisplayRow
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function DisplayRow(Level, Name, NumProducts, Container) {
	DestCat = (parseInt(Level)+1);
	var CatDiv = new Element("div", {
		"class": "CategoryRow",
		"rel": DestCat,
		"alt": NumProducts
	});
	CatDiv.appendText(Name);
	CatDiv.inject(Container);
	CatDiv.addEvent("click", function() {
		LoadCategory(CatDiv.getProperty("rel"), Name, CatDiv.getProperty("alt"));
	});

}
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//	DisplayProducts
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function DisplayProducts(Products, LoadingDiv) {
	LoadingDiv.dispose();
	Products.each(function(p) {
		var ProdDiv = new Element("div", {
			"class": "ProductRow"
		});

		var PNumberDiv = new Element("div", {
			"class": "PNumber"
		});
		PNumberDiv.appendText(p.part_number + p.short_code);
		PNumberDiv.inject(ProdDiv);

		var PNameDiv = new Element("div", {
			"class": "PName"
		});
		PNameDiv.appendText(p.products_name);
		PNameDiv.addEvent("click", function(){
			document.location.href = "http://74.8.32.141/jbimporters/checking_product_description.php?products_id=" + p.id;
		});
		PNameDiv.inject(ProdDiv);

		var PImageDiv = new Element("div", {
			"class": "PImage",
			"id": "pimg" + p.id
		});
		if (p.products_image1.length > 3) {
			var ProdImage = new Element("img", {
				"src" : "http://74.8.32.141/jbimporters/" + p.products_image1
			});
			ProdImage.inject(PImageDiv);
		}
		PImageDiv.inject(ProdDiv);

		var AvDiv = new Element("div", {
			"class" : "PAva"
		});
		var WarSel = new Element("select", {
			"name" : "warehouse"
		});
		p.warehouses.each(function(w) {
			var WarOpt = new Element("option", {
				"value": w.id
			});
			WarOpt.appendText(w.name + " : " + w.quantity);
			WarOpt.inject(WarSel);

		});
		WarSel.inject(AvDiv);
		AvDiv.inject(ProdDiv);

		var QDiv = new Element("div", {
			"class" : "PQuan"
		});
		var Inp = new Element("input", {
			"type": "text",
			"name": "quantity"
		});
		Inp.inject(QDiv);
		QDiv.inject(ProdDiv);

		var BDiv = new Element("div", {
			"class" : "PBut"
		});
		var But = new Element("input", {
			"type" : "button",
			"value" : "Add To Cart"
		});
		But.inject(BDiv);
		BDiv.inject(ProdDiv);

		ProdDiv.inject($("ProductsList"));

		var DivClear = new Element("div", {
			"class":"clear"
		});
		DivClear.inject($("ProductsList"));

		if (p.products_image2.length > 3) {
			var DivHover = new Element("div", {
				"class" : "PHoverImage",
				"id": "pimg" + p.id + "_smarthbox"
			});
			var HoverImage = new Element("img", {
				"src" : "http://74.8.32.141/jbimporters/" + p.products_image2
			});
			HoverImage.inject(DivHover);
		}

		DivHover.inject($("ProductsList"));

	});
	var sh = new SmartHoverBox({
		boxTimer: 0,
		yOffset: -10,
		xOffset: 0,
		lockY: 'top',
		lockX: 'right'
	});
}


