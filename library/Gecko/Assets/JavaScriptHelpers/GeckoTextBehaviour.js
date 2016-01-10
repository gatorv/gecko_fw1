function GeckoTextBehaviour( target_txt ) {
	this.checkForTarget = function( target ) {
		if( typeof( target_txt ) != "object" ) {
			throw "Target not a object";
		}
		
		if( target_txt.nodeName != "INPUT" ) {
			throw "Target not a input";
		}
	};
	
	this.registerListeners = function() {
		var self = this;
		this.target.onclick = function() {
			if( self.orValue == this.value )
				this.value = "";
		};
		
		this.target.onblur = function() {
			if( this.value == "" )
				this.value = self.orValue;
		};
	};
	
	this.checkForTarget( target_txt );
	this.target = target_txt;
	this.orValue = target_txt.value;
	this.registerListeners();
}