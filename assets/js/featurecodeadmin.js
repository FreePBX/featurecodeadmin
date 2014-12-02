function usedefault_onclick(chk){
		var featureid = chk.name.substring(11).replace('#','\\#');
		var btnval = chk.value;
		var inputelem = $("#custom" + featureid);
		var origcustom = $("#origcustom_" + featureid);
		var defaultcode = $("#default_" + featureid);
		console.log(featureid);
		if(btnval == 0){
			inputelem.prop('readonly', false);
			inputelem.val(origcustom.val());
		}else{
			inputelem.prop('readonly', true);
			origcustom.val(inputelem.val());
			inputelem.val(defaultcode.val());
		}
	}
	function frmAdmin_onsubmit(theForm){
		var msgErrorMissingFC = "Please enter a Feature Code or check Use Default for all Enabled Feature Codes";
		var msgErrorDuplicateFC = "Feature Codes have been duplicated";
		var msgErrorProceedOK = "Are you sure you wish to proceed?";
	
		for (var i=0; i<theForm.elements.length; i++) {
			var theFld = theForm.elements[i];
			if (theFld.name.substring(0,7) == "custom#") {
				var featureid = theFld.name.substring(7);
				// check that every non default has a custom code
			if (!theForm.elements['usedefault_' + featureid].checked) {
					defaultEmptyOK = false;
			if (!isDialDigits(theFld.value))
					return warnInvalid(theFld, msgErrorMissingFC);
					
			if (isDuplicated(theFld.name, theFld.value))
					return confirm(msgErrorDuplicateFC+".  "+msgErrorProceedOK);
			}
		}
	}
	
	
	return true;
}
function isDuplicated(firstfldname, firstfc) {
	var fcs = new Array()
	$("input[type=text]").each(function() {
		fcs.push($(this).val());
	});
	var occurs = $.grep(fcs,function(elem){ return elem === firstfc }).length;
	if( occurs > 1 ) { 
		return true;
	}else{
		return false;
	}

}
function callallusedefaults() {
	var theForm = document.frmAdmin;
	for (var i=0; i<theForm.elements.length; i++) {
		var theFld = theForm.elements[i];
		if (theFld.name.substring(0,11) == "usedefault_") {
			usedefault_onclick(theFld);
		}
	}
}
