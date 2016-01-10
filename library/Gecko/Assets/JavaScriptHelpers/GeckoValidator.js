/**
  * GeckoValidator
  *
  * Todo:
  * - Add AJAX Callbacks and returns
  * 
  * Id: 20060426 GeckoValidator.js
  * Author: Christopher Valderrama
  **/
  
/**
  * GeckoValidatorDefault Language
  **/
var defaultLang = {
	validation_notNumber: " this value is not a number.",
	validation_notInRange: "this value is not in the specified range.",
	validation_valueMax: " this value is maxium than the permited.",
	validation_valueMin: " this value is minium than the permited.",
	validation_notFloat: " this value is not a float.",
	validation_notDate: " this value is not a correct date.",
	validation_fieldEmpty: " this field is required.",
	validation_notTelephone: " this field is not a telephone.",
	validation_notThisValue: " this field can not contain this value.",
	validation_keyMissing: " there isn't a defined error for this field.",
	validation_notEmail: " this value is not a correct email."
};
/**
  * GeckoValidator::GeckoValidator
  *
  * Constructs a new instance of GeckoValidator, and adds a listiener to the form
  *
  * @param string target_form
  * @return GeckoValidator Object
  **/
function GeckoValidator( target_form ) {
	this.form = document.getElementById( target_form );
	this.vFields = new Array();
	this.result = true;
	
	if( !this.form ) throw "Target Form not found";
	var self = this;
	this.form.onsubmit = function() {
		return self.validate();
	};
};
/**
  * GeckoValidator::addField
  *
  * Adds a Field to the Validator Pool, currently supported validators:
  * - email
  * - number
  * - telephone
  *
  * @param string field_name
  * @param object field_flags
  * @return GeckoValidator Object
  **/
GeckoValidator.prototype.addField = function( field_name, field_flags ) {
	this.vFields[this.vFields.length] = [ field_name, field_flags ];
};
GeckoValidator.prototype.getMsg = function( key ) {
	if( ( typeof(Lang) != "undefined" ) && ( typeof( Lang[key] ) != 'undefined' ) ) {
		return Lang[key];
	} else {
		return defaultLang[key];
	}
}
/**
  * GeckoValidator::validate
  *
  * Validates a form before submit
  *
  * @access private
  * @return boolean
  **/
GeckoValidator.prototype.validate = function() {
	var total = this.vFields.length;
	var msg = "";
	this.result = true;
	var fResult = true;
	
	for( i = 0; i < total; i++ ) {
		var field = this.vFields[i];
		var fObj = this.form.elements[field[0]];
		
		fResult = true;
		switch( fObj.nodeName ) {
		case "INPUT":
		case "TEXTAREA":
			if( ( fObj.value == "" ) && ( typeof( field[1].empty ) != "undefined" ) && ( field[1].empty == false ) ) {
				fResult = false;
				msg = this.getMsg( 'validation_fieldEmpty' );
			} else {
				if ( ( typeof( field[1].content ) != "undefined" ) ) {
					var value = fObj.value;
					
					if( value == "" )
						continue;
					
					switch( field[1].content ) {
					case "email":
						var eTest = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
						msg = this.getMsg( 'validation_notEmail' );
						break;
					case "number":
						var eTest = /^([0-9]+)$/;
						msg = this.getMsg( 'validation_notNumber' );
						break;
					case "telephone":
						var eTest = /^([0-9]{3})-([0-9]{3})-([0-9]{4})$/;
						msg = this.getMsg( 'validation_notTelephone' );
						break;
					case "date":
						var eTest = /^([0-9]{2})-([0-9]{2})-([0-9]{4})$/;
						msg = this.getMsg( 'validation_notDate' );
						break;
					}
					
					if( !eTest.test(value) )
						fResult = false;
				}
			}
			break;
		case "SELECT":
			var fValue = fObj.value;
			if( !fValue ) fValue = fObj.options[fObj.selectedIndex].value;
			if( !fValue ) fValue = fObj.options[fObj.selectedIndex].text;
			
			if( ( fValue.value == "" ) && ( typeof( field[1].empty ) != "undefined" ) && ( field[1].empty == false ) ) {
				msg = this.getMsg( 'validation_fieldEmpty' );
				fResult = false;
			}
			
			if( fObj.selectedIndex <= 0 )  {
				msg = this.getMsg( 'validation_fieldEmpty' );
				fResult = false;
			}
			break;
		}
		
		if( !fResult )
			this.invalidate( fObj, msg );
	}
	
	if( this.result ) {
		/**
		 * Inhabilitar el submit si la forma es valida
		 **/
		
		//this.form.elements[''].disabled = true;
		for( i = 0; i < this.form.elements.length; i++ ) {
			if( ( this.form.elements[i].type == "submit" ) || ( this.form.elements[i].type == "reset" ) ) {
				this.form.elements[i].disabled = true;
			}
		}
	}
	
	return this.result;
};
/**
  * GeckoValidator::invalidate
  *
  * Invalida la forma, y agrega el mensaje de error al campo, tambien agrega
  * un listener para quitar el error cuando se escribe.
  *
  * @access private
  * @return void
  **/
GeckoValidator.prototype.invalidate = function( field, msg ) {
	var self = this;
	this.result = false;
	
	this.cleanField( field );
	this.createError( field, msg );
	
	field.onblur = function() {
		var myOtherParent = this.parentNode.parentNode;
		var the_span = this.parentNode;
		
		if( myOtherParent && ( the_span.nodeName == "SPAN" ) && ( the_span.className == "form-error" ) ) {
			try {
				myOtherParent.removeChild( the_span );
			} catch( e ) {}
			
			myOtherParent.appendChild( this );
		}
		
		this.onblur = function() {};
	};
};

GeckoValidator.prototype.cleanField = function( field ) {
	var myParentParent = field.parentNode.parentNode;
	var myParent = field.parentNode;
	if( ( myParent.nodeName == "SPAN" ) && ( myParent.className == "form-error" ) ) {
		try {
			myParentParent.removeChild( myParent );
		} catch( e ) {}
		
		myParentParent.appendChild( field );
	}
}

GeckoValidator.prototype.createError = function( field, msg ) {
	var txtNode = document.createTextNode( msg );
	var tmpSpan = document.createElement( "SPAN" );
	var myParent = field.parentNode;
	
	tmpSpan.className = "form-error";
	tmpSpan.appendChild( field );
	tmpSpan.appendChild( txtNode );
	try {
		myParent.removeChild( field );
	} catch( e ) {}
	
	myParent.appendChild( tmpSpan );
};