<?php

/*
Check Password strength
Check for password strength, password should be at least n characters,
contain at least one number, contain at least one lowercase letter, contain at least one uppercase letter,
 contain at least one special character.
*/

function password_strength($password){
	
	//validate password strength
	$uppercase = preg_match("@[A-Z]@", $password);
	$lowercase = preg_match("@[a-z]@", $password);
	$number = preg_match("@[0-9]@", $password);
	$specialChars = preg_match("@[^\w]@", $password);

	if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
		return false;
	}else {
		return true;
	}

}

?>