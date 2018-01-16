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
    <link rel="stylesheet" href="/site.min.css">
  </head>
  <body>
    <!-- Your content here -->
<header class="site-header flex-center">
<div class="container-wider">
  <div class="flex-left units-gap-big">
    <div class="unit-3-4 unit-1-on-mobile">
      <nav class="flex-middle units-gap-big">
      <div class="unit">
      </div>
      <div>
        {{ $title }}&nbsp;&nbsp;
      </div>
      </nav>
    </div>
  </div>
</div>
</header>

<aside class="show-on-mobile">
<input type="checkbox" id="site-aside-toggle-checkbox" class="hide-on-mobile"/>
<label class="site-aside-toggle-button" for="site-aside-toggle-checkbox">
<img src="http://getmobicss.com/img/menu.png" height="24"/>
</label>
<div class="site-aside-mobile-wrapper flex-vertical">
  <div class="unit-1 scroll-view">
    <ul class="site-menu-list">
      <li>
      <a href="/" class="site-text-plain site-side-title">喷嚏网</a>
      <ul>
        <li>
        <a class=" site-text-plain text-small @if($routeInfo = app('request')->route()[1]['as']=='dapenti.index') current @endif" href="/dapenti">首页</a>
        </li>
        <li>
        <a class="site-text-plain text-small @if($routeInfo = app('request')->route()[1]['as']=='dapenti.list') current @endif" href="/dapenti/list/tugua">图卦</a>
        </li>
      </ul>
      </li>
    </ul>
  </div>
</div>
</aside>

  <div class="site-article-wrapper">
    <div class="flex-center">
      <div class="container-wider">
        @yield('content')
      </div>
    </div>
  </div>

  </body>
</html>
