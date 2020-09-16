function popup(report)
{
	var width = 1000;
	var height = 800;
	var left = (screen.width - width) / 2;
	var topp = ( (screen.height - height) / 2 - 60 );
	window.open(report, "", "width=" + width + ",height=" + height + ",left=" + left + ",top=" + topp);
}
function storePosition()
{
	var yOffset = window.pageYOffset;
	document.cookie = yOffset;
}
function scrollPosition()
{
	var yOffset = document.cookie;
	window.scrollTo(0, yOffset);
}
function filter(table, column, criterion)
{
	criterion = criterion.value;
    window.location.href = encodeURI("index.php?table=" + table + "&filter=" + column + "&criterion=" + criterion);
}
function formFocus()
{
	var fieldFound = false;
	for(var i = 0; i < document.forms[0].length; i++)
	{
		if(document.forms[0][i].type != "hidden")
		{
			document.forms[0][i].focus();
			document.forms[0][i].select();
			fieldFound = true;
		}
		if (fieldFound == true) {break;}
	}
}
function validation()
{
	for(var i = 0; i < document.forms[0].length; i++)
	{
		if(document.forms[0][i].value == "")
		{
			document.forms[0][i].focus();
			return false;
		}
	}
	if(document.forms[0]["Course_Fee"])
	{
		var fee = parseInt(document.forms[0]["Course_Fee"].value);
		if(isNaN(fee)) {fee = 0;}
		if(fee <= 0)
		{
			document.forms[0]["Course_Fee"].focus();
			document.forms[0]["Course_Fee"].select();
			return false;
		}
	}
	if(document.forms[0]["Hotel_Fee"])
	{
		var fee = parseInt(document.forms[0]["Hotel_Fee"].value);
		if(isNaN(fee)) {fee = 0;}
		if(fee <= 0)
		{
			document.forms[0]["Hotel_Fee"].focus();
			document.forms[0]["Hotel_Fee"].select();
			return false;
		}
	}
	if(document.forms[0]["Duration"])
	{
		var duration = parseInt(document.forms[0]["Duration"].value);
		if(isNaN(duration)) {duration = 0;}
		if(duration < 1 || duration > 5)
		{
			document.forms[0]["Duration"].focus();
			document.forms[0]["Duration"].select();
			return false;
		}
	}
}