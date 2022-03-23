// vBlog admin JS 1.0

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
function checkSchedule() {
	if(!document.getElementById('scheduled').checked && document.getElementById('schedule').title != "s") {
		document.getElementById('schedule').style.display = "none";
	} else {
		document.getElementById('schedule').style.display = "block";
	}
}
function checkLogin() {
	if(document.getElementById('a_login_username').value.length < 3 || document.getElementById('a_login_password').value.length < 3) {
		return false;
	} else {
		return true;
	}
}
function getcookie(name) {
	if (document.cookie.length > 0) {
		begin = document.cookie.indexOf(name+"=");
		if (begin != -1) {
			begin += name.length+1;
			end = document.cookie.indexOf(";", begin);
			if (end == -1) end = document.cookie.length;
			return unescape(document.cookie.substring(begin, end));
		}
	}
	return false;
}

var last = 0;
function savetext(text,one) {
	if(last == 5) {
		if(text.length > 10) {
			var today = new Date();
			var expire = new Date();
			expire.setTime(today.getTime() + 3600000*24*14);
			document.cookie = "vblog_save"+one+"="+encodeURIComponent(text)+";expires="+expire.toGMTString();
		}
	} else {
		last++;
	}
}