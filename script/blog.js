/*
// vale[ blog ] JS
*/
function check(form) {
	switch(form) {
		case 'reg':
			_mail = document.getElementById('r_email').value;
			_user = document.getElementById('r_username').value;
			if(_mail.length < 6) {
				alert("The entered e-mail address is too short.");
				return false;
			} else if(_mail.indexOf(".") == -1 || _mail.indexOf("@") == -1) {
				alert("The entered e-mail address is invalid.");
				return false;
			} else if(_user.length < 2) {
				alert("The entered username is too short. The minimum is 2 characters.");
				return false;
			} else if(_user.search(/^[[A-Za-z0-9_\-\.]{2,}$/) == -1) {
				alert("The entered username is too short. Please only use alphanumeric characters and _ and . and -");
				return false;
			} else {
				return true;
			}
			break;
		case 'admin':
			if(document.getElementById('a_login_uname').value.length < 4) {
				alert('The entered username is too short');
				return false;
			} else if(document.getElementById('a_login_pass').value.length < 4) {
				alert('The entered password is too short');
				return false;
			} else {
				return true;
			}
			break;
		case 'login':
			if(document.getElementById('username').value.length < 2) {
				alert('The entered username is too short');
				return false;
			} else if(document.getElementById('password').value.length < 4) {
				alert('The entered password is too short');
				return false;
			} else {
				return true;
			}
			break;
	}
	return false;
}

function print_emil() {
	var aend = "</a>";
	var deli = "@";
	var name = "valerauko";
	var alin = "<a href=\"";
	var href = "mailto:";
	var serv = "gmail.com";
	var apre = "\">";
	document.write(alin+href+name+deli+serv+apre+name+deli+serv+aend);
}

function print_form() {
	var fopn = "<form action=\"http://blog.valerauko.net/\" method=\"post\">";
	var fili = "<li><label for=\"sign_name\">Who are you?</label></li>";
	var fiin = "<li><input type=\"text\" name=\"sign_name\" id=\"sign_name\" class=\"input_text\" /></li>";
	var seli = "<li><label for=\"sign_mail\" title=\"Optional, stored encoded, not displayed\">E-mail contact</label></li>";
	var sein = "<li><input type=\"text\" name=\"sign_mail\" id=\"sign_mail\" class=\"input_text\" /></li>";
	var suin = "<li><input type=\"submit\" name=\"sign_it\" id=\"sign_it\" class=\"submit\" value=\"Sign it!\" /></li>";
	var focl = "</form>";
	document.write(fopn+fili+fiin+seli+sein+suin+focl);
}