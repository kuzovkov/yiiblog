externalLinks();
function showAndHide(show, hide) {
	document.getElementById(show).style.display = "block";
	document.getElementById(hide).style.display = "none";
}

function externalLinks() {
	if (!document.getElementsByTagName) return;
	var anchors = document.getElementsByTagName("a");
	for (var i=0; i < anchors.length; i++) {
		if (anchors[i].getAttribute("href") && anchors[i].getAttribute("rel") == "external")
			anchors[i].target = "_blank";
	}
}

function SR_IsListSelected(el) {
	for (var i = 0; i < el.length; i ++)
		if (el[i].selected || el[i].checked) return i;
	return -1;
}

function SR_trim(f) {
	return f.toString().replace(/^[ ]+/, '').replace(/[ ]+$/, '');
}

function SR_submit(f) {
	f["email"].value = SR_trim(f["email"].value);
	f["name"].value = SR_trim(f["name"].value);
	if (f["name"].value == "Ваше имя") {
		alert("Укажите Ваше имя");
		return false;
	}
	if ((SR_focus = f["email"]) && f["email"].value.replace(/^[ ]+/, '').replace(/[ ]+$/, '').length < 1 || (SR_focus = f["name"]) && f["name"].value.replace(/^[ ]+/, '').replace(/[ ]+$/, '').length < 1) {
		alert("Укажите значения всех обязательных для заполнения полей (помечены звездочкой)");
		SR_focus.focus();
		return false;
	}
	if (!f["email"].value.match(/^[A-Za-z0-9][A-Za-z0-9\._-]*[A-Za-z0-9_]*@([A-Za-z0-9]+([A-Za-z0-9-]*[A-Za-z0-9]+)*\.)+[A-Za-z]+$/)) {
		alert("Некорректный синтаксис email-адреса!");
		f["email"].focus();
		return false;
	} 
	return true;
}

function changeMiniCourse(forward, id) {
	var list = document.getElementById(id);
	var minicourses = new Array();
	var i = 0;
	for (var childItem in list.childNodes) {
		var obj = list.childNodes[childItem];
		if (obj instanceof HTMLDivElement) {
			minicourses[i] = obj;
			i++;
		}
	}
	for (i = 0; i < minicourses.length; i++) {
		if (minicourses[i].className == "show") {
			minicourses[i].className = "hide";
			var show = i + 1;
			if (forward) {
				if (i + 1 == minicourses.length) show = 0;
			}
			else {
				if (i == 0) show = minicourses.length - 1;
				else show = i - 1;
			}
			minicourses[show].className = "show";
			break;
		}
	}
}

function checkFormAddSite(form) {
	var address = form.address.value;
	var description = form.description.value;
	var bad = "";
	if (address.length == 0) bad += "Вы не указали адрес";
	else if (address.length > 255) bad +=  "\n" + "Адрес сайта слишком длинный";
	else if (address.match(/(ht|f)tp(s?)\:\/\/[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*(\/?)([a-zA-Z0-9\-\.\?\,\'\/\\\+&%\$#_]*)?/) == null) bad += "\n" + "Адрес сайта некорректный";
	if (description.length == 0) bad += "\n" + "Вы не написали описание";
	else if (description.length > 255) bad +=  "\n" + "Описание сайта слишком длинный";
	if (bad == "") return true;
	alert(bad);
	return false;
}

function closePopup() {
	history.back();
}

function showFormSubscribe() {
	closePopupElements();
	document.getElementById("popup_form_subscribe").style.display = "block";
}

function showFormCode() {
	closePopupElements();
	document.getElementById("popup_form_code").style.display = "block";
}

function showFormCodeReset() {
	closePopupElements();
	document.getElementById("popup_form_code_reset").style.display = "block";
}

function showPopupMain() {
	closePopupElements();
	document.getElementById("popup_main").style.display = "block";
}

function closePopupElements() {
	document.getElementById("popup_form_subscribe").style.display = "none";
	document.getElementById("popup_form_code").style.display = "none";
	document.getElementById("popup_form_code_reset").style.display = "none";
	document.getElementById("popup_main").style.display = "none";
}

function setPlaceHolder(input, text) {
	if (input.value == text) input.value = "";
	else if (input.value == "") input.value = text;
}

function getXmlHttp() {
	var xmlhttp;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
		}
	}
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

function ajaxFormSubscribe(form) {
	if (SR_submit(form)) {
		var data = "";
		data += "name=" + encodeURIComponent(form.field_name_first.value);
		data += "&email=" + encodeURIComponent(form.field_email.value);
		data += "&delivery_id=" + encodeURIComponent(form.delivery_id.value);
		data += "&func=" + encodeURIComponent("subscribe");
		var xmlhttp = getXmlHttp();
		xmlhttp.open("POST", "../functions.php", true);
		xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xmlhttp.send(data);
		document.getElementById("button_form_sr").style.display = "none";
		document.getElementById("form_sr_preloader").style.display = "block";
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4) {
				if(xmlhttp.status == 200) {
					document.getElementById("button_form_sr").style.display = "table";
					document.getElementById("form_sr_preloader").style.display = "none";
					if (xmlhttp.responseText == "1") alert("Проверьте почтовый ящик (" + form.field_email.value + ") и подтвердите подписку на рассылку.");
					else alert("Произошла ошибка при добавлении! Возможно, Вы уже подписаны на рассылку.");
				}
			}
		};
	}
	return false;
}

function ajaxFormCode(form) {
	var data = "";
	data += "func=" + encodeURIComponent("check_code");
	data += "&code=" + encodeURIComponent(form.code.value);
	var xmlhttp = getXmlHttp();
	xmlhttp.open("POST", "../functions.php", true);
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlhttp.send(data);
	document.getElementById("button_form_code").style.display = "none";
	document.getElementById("form_code_preloader").style.display = "block";
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			if(xmlhttp.status == 200) {
				if (xmlhttp.responseText == "1") {
					document.cookie = "popup=0";
					document.getElementById("parent_popup").style.display = "none";
				}
				else {
					document.getElementById("form_code_preloader").style.display = "none";
					document.getElementById("button_form_code").style.display = "table";
					alert("Неверный код!");
				}
			}
		}
	};
	return false;
}

function ajaxFormCodeReset(form) {
	var data = "";
	data += "func=" + encodeURIComponent("code_reset");
	data += "&email=" + encodeURIComponent(form.email.value);
	var xmlhttp = getXmlHttp();
	xmlhttp.open("POST", "../functions.php", true);
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlhttp.send(data);
	document.getElementById("button_form_code_reset").style.display = "none";
	document.getElementById("form_code_reset_preloader").style.display = "block";
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			document.getElementById("form_code_reset_preloader").style.display = "none";
			document.getElementById("button_form_code_reset").style.display = "table";
			if(xmlhttp.status == 200) {
				if (xmlhttp.responseText == "1") alert("Код отправлен на указанный e-mail");
				else alert("E-mail не найден в базе!");
			}
		}
	};
	return false;
}