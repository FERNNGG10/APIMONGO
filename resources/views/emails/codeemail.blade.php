<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!--<link rel="stylesheet" href="{{ asset('css/email.css') }}">-->
    <title>Confirma tu cuenta</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.container {
    width: 80%;
    margin: auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
    margin-top: 50px;
}

h1 {
    color: #444;
    font-size: 24px;
    text-align: center;
    margin-bottom: 20px;
}

p {
    color: #666;
    font-size: 16px;
    line-height: 1.6;
    text-align: center;
    margin-bottom: 20px;
}

.button {
    display: block;
    width: 200px;
    height: 50px;
    margin: 20px auto;
    background-color: #5cb85c;
    color: #fff;
    text-align: center;
    border-radius: 5px;
    line-height: 50px;
    font-size: 18px;
    text-decoration: none;
}

.button:hover {
    background-color: #4cae4c;
}
    </style>
</head>
<body>
    <div class="container">
        <h1>Codigo login</h1>
        <p>Tu codigo para iniciar sesion</p>
        <p class="button" >{{$code}}</p>
    </div>
</body>
</html>