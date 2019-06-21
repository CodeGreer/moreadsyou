<?php

// if the url field is empty, but the message field isn't
if(isset($_POST['contact-next-week']) && $_POST['contact-next-week'] == '' && $_POST['message'] != ''){

	// put your email address here
	$youremail = 'moreadsyou@outlook.com';

	// prepare a "pretty" version of the message
	// Important: if you added any form fields to the HTML, you will need to add them here also
	$body = "This is the form that was just submitted:
	Name:  $_POST[name]
	E-Mail: $_POST[email]
    Website: $_POST[phone]
	Message: $_POST[message]";

	// Use the submitters email if they supplied one
	// (and it isn't trying to hack your form).
	// Otherwise send from your email address.
	if( $_POST['email'] && !preg_match( "/[\r\n]/", $_POST['email']) ) {
	  $headers = "From: $_POST[email]";
	} else {
	  $headers = "From: $youremail";
	}

	// finally, send the message
	mail($youremail, 'Mo Reads You Website Inquiry', $body, $headers );

}

// otherwise, let the spammer think that they got their message through

// uncomment these lines to redirect instead of displaying HTML
header('Location: http://moreadsyou.com/thanks.html');
exit('Redirecting you to http://moreadsyou.com/thanks.html');

?>
<!DOCTYPE HTML>
<html>
<head>

<title>Thanks!</title>

</head>
<body>


</body>
</html>
