<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
	<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
	  	<div style="margin:50px auto;width:70%;padding:20px 0">
		    <div style="border-bottom:1px solid #eee;text-align: center;">
		      	<a href="{{ config('app.url') }}" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">{{ config('app.name') }}</a>
		    </div>
	    	<p style="font-size:1.1em; margin-top:20px; ">Hi {{ $user['name'] }},</p>

            <p>We're writing to inform you that your password reset request has been successfully processed. Your account password has been changed, and you can now access your account using your new password. </p>

            <p>If you did not request this password reset or believe this is a mistake, please contact our support team immediately for assistance.</p>

	    	<p style="font-size:0.9em;">Regards,<br />{{ config('app.name') }}</p>
	  	</div>
	</div>
</body>
</html>
