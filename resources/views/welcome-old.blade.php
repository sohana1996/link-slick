<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{$page['title']}}</title>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>

    <style>
        body, html {
            background: url('/img/spark-bg.png');
            background-repeat: repeat;
            background-size: 300px 200px;
            height: 100%;
            margin: 0;
        }

        .content {
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .details {
            display: table;
            margin: auto;
        }

        .title {
            text-align: center;
            font-size: 100px;
            margin: 0;
            padding: 0;
            color: #777777;
            font-family: 'Open Sans';
        }

        .actions {
            text-align: center;
            margin: 0;
            padding: 10px 0;
        }

        .auth {
            background-color: #3097D1;
            border: 0;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            font-family: 'Open Sans';
            font-size: 14px;
            font-weight: 600;
            padding: 15px 0;
            text-align: center;
            width: 150px;
            text-decoration: none;
            display: inline-block;
        }

    </style>
</head>
<body>

<div class="content">
    <div class="details">
        <h1 class="title">Link Slick</h1>
        @if (Auth::check())
            <p class="actions">
                <a class="auth" href="/home" style="margin-right: 15px;">Go to Dashboard</a>
            </p>
        @else
            <p class="actions">
                <a class="auth" href="/login" style="margin-right: 15px;">Login</a>
                <a class="auth" href="/register">Register</a>
            </p>
        @endif
    </div>
</div>
</body>
</html>
