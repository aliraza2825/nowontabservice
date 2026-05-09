<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Widget Login</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            align-items: center;
            background: #f7f5ef;
            color: #222;
            display: flex;
            font-family: Arial, sans-serif;
            justify-content: center;
            margin: 0;
            min-height: 100vh;
            padding: 18px;
        }

        .login {
            background: #fff;
            border: 1px solid #ddd5c8;
            border-radius: 6px;
            max-width: 390px;
            padding: 24px;
            width: 100%;
        }

        h1 {
            font-size: 24px;
            margin: 0 0 18px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin: 14px 0 7px;
        }

        input {
            border: 1px solid #ccc4b8;
            border-radius: 6px;
            font-size: 16px;
            padding: 11px 12px;
            width: 100%;
        }

        .button {
            background: #111;
            border: 0;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
            margin-top: 18px;
            padding: 12px 16px;
            width: 100%;
        }

        .error {
            background: #fff0ee;
            border: 1px solid #efb7ad;
            border-radius: 6px;
            margin-bottom: 14px;
            padding: 10px 12px;
        }
    </style>
</head>
<body>
    <form class="login" method="POST" action="{{ route('menu-widget.admin.login.submit') }}">
        @csrf
        <h1>Menu Widget Login</h1>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <label for="username">Username</label>
        <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>

        <button class="button" type="submit">Login</button>
    </form>
</body>
</html>
