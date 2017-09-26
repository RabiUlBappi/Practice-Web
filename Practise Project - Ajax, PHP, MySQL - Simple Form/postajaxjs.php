<?php
	include "config.php";
	include "connect.php";
	include "db_helper.php";
	// Fetching Values From URL
	$name2 		= $_POST['name1'];
	$email2 	= $_POST['email1'];
	$password2  = $_POST['password1'];
	$contact2 	= $_POST['contact1'];

	if (isset($_POST['name1'])) {
		$sql = myQuery("SELECT id FROM form_element WHERE name=:name OR email=:email OR contact=:contact",
				array(':name'=>$name2,':email'=>$email2,':contact'=>$contact2),$conn); //Insert Query
		if($sql->rowCount()>0) {echo "ERROR! Duplicate name, email or contact.";}
		else{
			$sql = myQuery("INSERT INTO form_element(name, email, password, contact) 
							VALUES (:name, :email, :password,:contact)",
							array(':name'=>$name2,':email'=>$email2, ':password'=>$password2,':contact'=>$contact2),$conn); //Insert Query
			echo "Form Submitted succesfully";
		}
	}
?>