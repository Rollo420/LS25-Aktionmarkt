<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!--<![endif]-->
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    @vite(['resources/scss/style.scss', 'resources/js/app.js'])
</head>

<body>

<section>
    <nav class="navbar">
        <ul>
            <li>
                <a href="/account">Account</a>
            </li>
            <li>
                <a href="/stock">Stock</a>
            </li>
            <li>
                <a href="/transaction">Transaction</a>
            </li>
        </ul>
    </nav>
</section>

<section>
    <div class="title">
        @yield('title')
    </div>

    <div class="content">
        @yield('content')
    </div>
</section>

    <script src="" async defer></script>
</body>

</html>