var GeckoEditableDiv = Class.create();
GeckoEditableDiv.prototype = {
	initialize: function( divId, targetURL ) {
		this.div = divId;
		this.theURL = targetURL;
		
		var self = this;
		
		Event.observe(divId, 'click', function(){ self.edit() }, false);
		Event.observe(divId, 'mouseover', function(){ self.showAsEditable() }, false);
		Event.observe(divId, 'mouseout', function(){ self.showAsEditable(true) }, false);
	},
	
	edit: function() {
		var obj = $(this.div);
		
		Element.hide(obj);
		
		var textarea ='<div id="' + obj.id + '_editor"><textarea id="' + obj.id + '_edit" name="' + obj.id + '_edit">' + obj.innerHTML + '</textarea><br />';
		
		var button = '<input id="' + obj.id + '_save" type="button" value="Guardar" /> - <input id="' + obj.id + '_cancel" type="button" value="Cancelar" /><' + '/div>';
		
		var self = this;
		
		new Insertion.After(obj, textarea+button);
		
		Event.observe(obj.id+'_save', 'click', function(){ self.saveChanges() }, false);
		Event.observe(obj.id+'_cancel', 'click', function(){ self.cleanUp() }, false);
	},
	
	showAsEditable: function( clear ) {
		var obj = $(this.div);
		if (!clear){
			Element.addClassName(obj, 'editable');
		}else{
			Element.removeClassName(obj, 'editable');
		}
	},
	
	saveChanges: function() {
		var obj = $(this.div);
		var new_content = escape($(obj.id+'_edit').value);
		
		obj.innerHTML = "Saving...";
		
		this.cleanUp(true);
		var self = this;
		
		var success = function(t){self.editComplete(t);}
		var failure = function(t){self.editFailed(t);}
		
		var pars = 'id=' + obj.id + '&content=' + new_content;
		var myAjax = new Ajax.Request(
			this.theURL, 
			{
				method: 'post',
				postBody: pars,
				onSuccess: success,
				onFailure: failure
			}
		);
	},
	
	cleanUp: function(keepEditable) {
		var obj = $(this.div);
		Element.remove(obj.id+'_editor');
		Element.show(obj);
		if (!keepEditable) this.showAsEditable(true);
	},
	
	editComplete: function( resp ) {
		var obj = $(this.div);
		obj.innerHTML = resp.responseText;
		this.showAsEditable(true);
	},
	
	editFailed: function( resp ) {
		var obj = $(this.div);
		obj.innerHTML = 'ERROR: ' + resp.responseText;
		this.cleanUp();
	}
}