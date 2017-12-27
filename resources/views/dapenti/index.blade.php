<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- Title of the page -->
    <title>@yield('title')</title>
    <link rel="icon" type="image/png" href="/touch-icon-iphone-114.png" />
    <link rel="apple-touch-icon" sizes="57x57" href="/touch-icon-iphone-114.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="/touch-icon-iphone-114.png" />
    <!-- Mobi.css -->
    <link rel="stylesheet" href="https://unpkg.com/mobi.css/dist/mobi.min.css">
  </head>
  <body>
    <!-- Your content here -->
	<div class="flex-center">
	  <div class="container-wider">
	  	@yield('content')
	  </div>
	</div>
  </body>
</html>
