<?php

	try{
		$conn=new PDO('mysql:host='.$config['servername'].';dbname='.$config['db'],
						$config['username'],
						$config['password']);
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	}catch(Exception $e){
		error_log($e->getMessage());
		die("A database error was encountered");
	}
