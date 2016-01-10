var GeckoGrid = Class.create();
GeckoGrid.prototype = {
	initialize: function( tbl_name, uri, upImg, dnImg ) {
		this.name = tbl_name;
		this.container = $("content_" + tbl_name);
		this.uri = uri;
		this.upImg = upImg;
		this.dnImg = dnImg;
	},
	
	setOrderParam: function( param ) {
		this.orderp = param;
	},
	
	setSortParam: function( param ) {
		this.sortp = param;
	},
	
	setPaginatorParam: function( param ) {
		this.pagip = param;
	},
	
	doSort: function( link, field, sort ) {
		var pars = this.name + "_processAjax=processTable&" + this.orderp + "=" + field;
		if( sort ) {
			pars += "&" + this.sortp + "=" + sort;
		}
		
		var self = this;
		
		new Ajax.Request(this.uri, {
			method: 'get',
			parameters: pars,
			onSuccess: function(transport) {
				var txt = transport.responseText;
				self.updateBody(txt);
				self.processLink( link, sort );
			}
		});
		
		return false;
	},
	
	doPaginate: function( page ) {
		var pars = this.name + "_processAjax=processTable&" + this.pagip + "=" + page;
		
		var self = this;
		
		new Ajax.Request(this.uri, {
			method: 'get',
			parameters: pars,
			onSuccess: function(transport) {
				var txt = transport.responseText;
				self.updateBody(txt);
			}
		});
		
		return false;
	},
	
	updateBody: function( text ) {
		this.container.innerHTML = text;
	},
	
	processLink: function( link, sort ) {
		var hasImg = link.innerHTML.include('img');
		var label;
		var text;
		if( hasImg ) {
			text = link.innerHTML;
			var pos = text.indexOf( "<" );
			label = text.substr( 0, pos );
		} else {
			label = link.innerHTML;
		}
		
		link.innerHTML = label + this.getOtherSortImg( sort );
	},
	
	getOtherSortImg: function( sort ) {
		if( sort == "ASC" || !sort ) {
			var im = " <img src=\""+ this.dnImg +"\" border=\"0\"/>";
		} else {
			var im = " <img src=\""+ this.upImg +"\" border=\"0\"/>";
		}
		
		return im;
	}
};