var GeckoDependantSelects = Class.create({
	initialize: function( control, childControl ) {
		this.controlName = control;
		this.control = $('select_' + control );
		
		if( this.control == null ) {
			throw ("Control not found, unable to register" );
		}
		
		this.childName = childControl;
		this.gateway = this.control.form.action;
		this.value = 0;
		
		if( this.childName != "" ) {
			this.control.onchange = function() {
				this.updateChild();
			}.bind(this);
		}
	},
	
	updateChild: function() {
		var child = this.getChildObject();
		if( child == null ) return;
		
		child.reset();
		
		this.value = this.control.options[this.control.selectedIndex].value;
		
		if( this.control.selectedIndex == 0 ) {
			return; // No value
		}
		
		var pars = "ajaxControlProcess=1&parentControl=" + this.controlName + "&childControl=" + this.childName + "&parentValue=" + this.value;
		var theAjax = new Ajax.Request(
			this.gateway,
			{
				method: 'get',
				parameters: pars,
				onLoading: function() {
					Element.show( 'loading_' + this.childName );
				}.bind(this),
				onComplete: function( req ) {
					this.updateChildOptions( req.responseText );
					Element.hide( 'loading_' + this.childName );
				}.bind(this)
			}
		);
	},
	
	reset: function() {
		var theFirstOption = this.control.options[0];
		
		this.control.options.length = 0;
		this.control.options.add( theFirstOption );
		
		var child = this.getChildObject();
		if( child == null ) return;
		
		child.reset();
	},
	
	getChildObject: function() {
		if( this.childName == "" ) {
			return; // No child
		}
		evalStr = "var child = dependantSelect_" + this.childName + ";";
		try  {
		eval( evalStr );
		} catch( e ) {}
		
		if( typeof child == "undefined" ) {
			return; // No child
		}
		
		return child;
	},
	
	updateChildOptions: function( data, value ) {
		
		var child = this.getChildObject();
		if( child == null ) return;
		
		child.fillComboData( data );
		
		if( value != null ) {
			child.selectValue( value );
		}
	},
	
	fillComboData: function( data ) {
		if( data == "" ) return;
		
		var theOptions = data.split( "|" );
		var theFirstOption = this.control.options[0];
		
		this.control.options.length = 0;
		this.control.options.add( theFirstOption );
		
		for( var i = 0; i < theOptions.length; i++ ) {
			var aOption = theOptions[i].split("~");
			
			this.control.options.add( new Option( aOption[1], aOption[0] ) );
		}
	},
	
	selectValue: function( optionVal ) {
		var lst = this.control;
		
		for( var i = 0; i< lst.options.length; i++ ){
			if( lst.options[i].value == optionVal ){
				lst.selectedIndex = i;
				return;
			}
		}
	},
	
	preSelect: function( parentValue, childValue ) {
		this.selectValue( parentValue );
		
		var child = this.getChildObject();
		if( child == null ) return;
		
		child.reset();
		
		pars = "parentControl=" + this.controlName + "&childControl=" + this.childName + "&parentValue=" + parentValue;
		
		var theAjax = new Ajax.Request(
			this.gateway,
			{
				method: 'get',
				parameters: pars,
				onLoading: function() {
					Element.show( 'loading_' + this.childName );
				}.bind(this),
				onComplete: function( req ) {
					this.updateChildOptions( req.responseText, childValue );
					Element.hide( 'loading_' + this.childName );
				}.bind(this)
			}
		);
	}
});
