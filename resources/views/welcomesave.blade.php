<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>


    </head>
    <body>

    <script src="https://www.paypal.com/sdk/js?client-id={{$_ENV['PAYPALCID']}}"></script>


    <div id="paypal-button-container"></div>

    <script>
        paypal.Buttons().render('#paypal-button-container');
    </script>
    </body>
</html>
