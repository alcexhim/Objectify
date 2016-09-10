window.addEventListener("load", function()
{
	var cmdSubmit = document.getElementById("cmdSubmit");
	cmdSubmit.addEventListener("click", function()
	{
		document.getElementById("formMain").submit();
	});
});