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
                <a href="{{ config('app.url') }}"
                    style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">{{ config('app.name') }}</a>
            </div>
            <p style="font-size:1.1em; margin-top:20px; ">Hi {{ $data['firstname'] }} {{ $data['lastname'] }},</p>
            <p>Thank you for choosing {{ config('app.name') }}. Use the following OTP to complete your
                {{ $data['subject'] }} process.
                OTP is valid for 10 minutes.</p>

            <h2
                style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">
                {{ $data['otp'] }}</h2>

            <p style="font-size:0.9em;">Regards,<br />{{ config('app.name') }}</p>
        </div>
    </div>
</body>

</html>
