if( !window.getObject ) {
	function getObject( objectId ) {
		// checkW3C DOM, then MSIE 4, then NN 4.
		if(document.getElementById) {
			return document.getElementById( objectId );
		} else if (document.all) {
			return document.all[objectId];
		} else if (document.layers) { 
			return document.layers[objectId];
		} else {
			throw objectId + " not existant in the DOM";
			return false;
		}
	};
}

function GeckoHoverTable( tableId, hoverColor ) {
	this.the_table = null;
	this.hoverColor = hoverColor;
	this.previousColor = "";
	this.highlites = 5;
	this.currHighlite = 0;
	var self = this;
	
	try {
		this.the_table = getObject( tableId );
	} catch(e) {
		throw "Couldn't initialize Table " + e;
		return false;
	}
	
	var tableTRs = this.the_table.getElementsByTagName("tr"); // Get a Object for all of the Table trs
	
	for(var i = 0; i < tableTRs.length; i++ ){ 
		if( tableTRs.item(i).getElementsByTagName('th').length<=1) { // Do not apply hover to TH elements
			tableTRs.item(i).onmouseover 	= highliteRow;
			tableTRs.item(i).onmouseout 	= restoreRow;
		}
	}
	function highliteRow() {
		self.previousColor = this.style.backgroundColor;
		this.style.backgroundColor = self.hoverColor;
	};
	function restoreRow() {
		this.style.backgroundColor = self.previousColor;
	};
};
GeckoHoverTable.prototype.getHoverColor = function() {
	return this.hoverColor;
};
GeckoHoverTable.prototype.setHoverColor = function( newColor ) {
	this.hoverColor = newColor;
};
GeckoHoverTable.prototype.showRow = function( rowNumber ) {
	this.theTR = this.the_table.getElementsByTagName("tr")[--rowNumber];
	if( !this.theTR ) {
		alert( rowNumber + " not existant" );
	}
	
	this.theTR.style.backgroundColor = this.hoverColor;
};
