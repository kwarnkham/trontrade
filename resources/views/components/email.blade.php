<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? env('APP_NAME') }}</title>
    <style>
        * {
            font-family: Inter;
        }

        .logo {
            width: 155px;
            margin: 25px 38px;
        }

        .banner {
            background-color: #59b5a4;
            height: 106px;
            display: flex;
            align-items: flex-end;
            background-image: url(https://storage.googleapis.com/tron-assets/1logo);
            background-size: 310px auto;
            background-repeat: no-repeat;
            background-position: top right;
            background-blend-mode: soft-light;
        }

        .content {
            padding: 24px;
            margin-top: 18px;
        }

        h2 {
            font-size: 16px;
            font-weight: 600;
        }

        h3 {
            font-weight: 600;
            font-size: 24px;
        }

        h4 {
            font-weight: 600;
            margin-top: 2rem;
        }

        p,
        h4 {
            font-size: 14px;
            color: #666;
        }

        a {
            color: #59b5a4;
        }

        .main {
            max-width: 760px;
            margin: auto;
        }

        @media screen and (max-width:500px) {

            .logo {
                margin: 0 8px;
            }

        }
    </style>
</head>

<body>
    <div class="main">
        <div class="banner">
            <img class="logo" src="https://storage.googleapis.com/{{env('GOOGLE_BUCKET_NAME')}}/1logo" alt="logo" />
        </div>
        <div class="content">
            <h2>{{ $title ?? env('APP_NAME') }}</h2>
            {{ $slot }}
            <!-- <h2>Reset Password</h2>
            <p>
                You've requested to reset the password linked with your Binance
                account.<br />
                Please note: After updating your password, we'll disable withdrawals
                for 24 hours.<br />
                To confirm your request, please use the 6-digit code below:
            </p>
            <h3>449228</h3>
            <p>
                The verification code will be valid for 30 minutes. Please do not
                share this code with anyone.
            </p> -->
        </div>
        <div class="content">
            <p>
                {{__("messages.If you don't recognize this activity, please disable your account and contact our customer support immediately at")}} <a href="mailto:{{env('SUPPORT_EMAIL')}}">{{env("SUPPORT_EMAIL")}}</a>
            </p>
            <h4>{{env("APP_NAME")}} {{__("messages.Team")}}</h4>
            <p>{{__("messages.This is an automated message, please do not reply.")}}</p>
        </div>
    </div>
</body>

</html>