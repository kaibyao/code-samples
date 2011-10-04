function submitForm(type, formObj, initParams) {
	coregURLs = Array();
	coregURLs['edu'] = '/ajax/coregEdu.php?' + initParams;
	coregURLs['debt'] = '/ajax/coregDebt.php?' + initParams;
	coregURLs['bank'] = '/ajax/coregBank.php?' + initParams;
	
	successMsg = Array();
	successMsg['edu'] = "Your request for Additional Education Information was successful!";
	successMsg['debt'] = "Your request for debt relief was successful!";
	successMsg['health'] = "Your request for a free health insurance quote was successful!";
	successMsg['bank'] = "Your request for bankruptcy consultation was successful!";
	
	if (typeof(coregURLs[type]) == 'undefined' || typeof(coregURLs[type]) == 'null') return false;
	
	var paramArr = Array();
	$(formObj).find(".formInputText, .formSelect, .formRadio, .formInputHidden").each(function(index, element) {
		paramArr[paramArr.length] = $(element).attr("name") +"="+ encodeURIComponent($(element).val());
	});
	
	var paramStr = paramArr.join('&');
	
	$(formObj).find(".coregForm").slideUp("slow", function() {
		$(formObj).find(".formProcessWaiting").fadeIn("fast", function() {
			$.post(coregURLs[type], paramStr, function(data) {
				if (data == 'SUCCESS') {
					$(formObj).find(".formProcessWaiting").html('<span style="color:#0099ff;">'+ successMsg[type] +'</type>');
					if ($(formObj).find(".formExpandRadio").length > 0)
						$(formObj).find(".formExpandRadio").attr("onclick", "");
				} else {
					alert(data);
					$(formObj).find(".coregForm").slideDown("fast");
					$(formObj).find(".formProcessWaiting").fadeOut("fast");
				}
			}, 'text');
		});
	});
}