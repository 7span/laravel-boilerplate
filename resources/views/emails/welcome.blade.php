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

            <p>Welcome to {{ config('app.name') }}! We are thrilled to have you on board and excited to see you join our community. </p>

	    	<p>Thank you for choosing {{ config('app.name') }}. </p>

	    	<p style="font-size:0.9em;">Regards,<br />{{ config('app.name') }}</p>
	  	</div>
	</div>
</body>
</html>
