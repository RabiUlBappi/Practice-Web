	
	function myFunction(){
		var name=document.getElementById("name").value;
		var email=document.getElementById("email").value;
		var password=document.getElementById("password").value;
		var cnfpassword=document.getElementById("cnfpassword").value;
		var contact=document.getElementById("contact").value;

		if(name==''||email==''||password==''||contact==''){alert('Please Fill All Fields!')}
		else if(cnfpassword!=password || (password.length<6)){alert('Password didn\'t match! Or Password is short!')}
		else{
			var dataString='name1='+name+'&email1='+email+'&password1='+password+'&contact1='+contact;
			$.ajax({
				type: "POST",
				url: "postajaxjs.php",
				data: dataString,
				cache: false,
				success: function(html){
					alert(html);
				}
			});
		}
		return false;
	}

///////////////////////         Full JQuery        ////////////////////////////

/*

$(document).ready(function(){
$("#submit").click(function(){
var name = $("#name").val();
var email = $("#email").val();
var password = $("#password").val();
var contact = $("#contact").val();
// Returns successful data submission message when the entered information is stored in database.
var dataString = 'name1='+ name + '&email1='+ email + '&password1='+ password + '&contact1='+ contact;
if(name==''||email==''||password==''||contact=='')
{
alert("Please Fill All Fields");
}
else
{
// AJAX Code To Submit Form.
$.ajax({
type: "POST",
url: "ajaxsubmit.php",
data: dataString,
cache: false,
success: function(result){
alert(result);
}
});
}
return false;
});
});

*/