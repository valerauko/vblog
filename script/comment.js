function showform() {
	document.getElementById("comment_saved").style.display = "none";
	document.getElementById("comment_new").style.display = "block";
}
function hideform() {
	document.getElementById("comment_saved").style.display = "block";
	document.getElementById("comment_new").style.display = "none";
}
function comment_error(e) {
	var c = e ? e : 1;
	var errors = new Array();
	errors[1] = "A fatal error occurred. Please try reloading the page.";
	errors[2] = "The name you entered is too short.";
	errors[3] = "The e-mail address you entered is too short.";
	errors[4] = "The entered e-mail address is invalid.";
	errors[5] = "The website address you entered is invalid. If you don't have a website, leave that field empty.";
	errors[6] = "";
	
	var _mess = document.getElementById('comment_mess');

	_mess.style.display = "block";
	_mess.innerHTML = errors[c];
}
function antispam() {
	document.write("<input type=\"hidden\" name=\"antispam\" value=\"javascript\" />");
}
function reply(id,name){
	document.getElementById('comment_main').value+='[re='+id+']'+name+'[/re]: ';
	document.getElementById('comment_main').focus();
}
function comment_check() {
	/*
	// fieldek:
	// ha belpett: comment_user
	// ha nem: commant_name, comment_mail, comment_site
	*/
	var _user = document.getElementById('comment_user');
	var _name = document.getElementById('comment_name');
	var _mail = document.getElementById('comment_mail');
	var _site = document.getElementById('comment_site');
	
	if(!_user && (!_name || !_mail || _site)) {
		comment_error(1);
		return false;
	}
	if(_user && _user.value > 0 && !_name && !_mail && !_site) {
		return true;
	} else {
		comment_error(1);
		return false;
	}
	if(!_user && _name && _mail && _site) {
		if(_name.value.length < 2) {
			comment_error(2);
			return false;
		} else if(_mail.value.lenth < 6) {
			comment_error(3);
			return false;
		} else if(_mail.value.indexOf(".") == -1 || _mail.value.indexOf("@") == -1) {
			comment_error(4);
			return false;
		} else if(_site.value != "website" && _site.value.length > 3 && _site.value.indexOf(".") == -1) {
			comment_error(5);
			return false;
		} else {
			return true;
		}
	}
	comment_error(1);
	return false;
}

// surroundText function from SMF forum engine. yeah, i've stolen this one...
function surroundText(text1, text2, textarea)
{
	// Can a text range be created?
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange)
	{
		var caretPos = textarea.caretPos, temp_length = caretPos.text.length;

		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text1 + caretPos.text + text2 + ' ' : text1 + caretPos.text + text2;

		if (temp_length == 0)
		{
			caretPos.moveStart("character", -text2.length);
			caretPos.moveEnd("character", -text2.length);
			caretPos.select();
		}
		else
			textarea.focus(caretPos);
	}
	// Mozilla text range wrap.
	else if (typeof(textarea.selectionStart) != "undefined")
	{
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var selection = textarea.value.substr(textarea.selectionStart, textarea.selectionEnd - textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var newCursorPos = textarea.selectionStart;
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text1 + selection + text2 + end;

		if (textarea.setSelectionRange)
		{
			if (selection.length == 0)
				textarea.setSelectionRange(newCursorPos + text1.length, newCursorPos + text1.length);
			else
				textarea.setSelectionRange(newCursorPos, newCursorPos + text1.length + selection.length + text2.length);
			textarea.focus();
		}
		textarea.scrollTop = scrollPos;
	}
	// Just put them on the end, then.
	else
	{
		textarea.value += text1 + text2;
		textarea.focus(textarea.value.length - 1);
	}
}