<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-WBGdrAeU1oqLPPGgQ46hfeMev6a90U9/b7Q2mRXYm8U5t2rZL2KcE5L5yfc3IVjG4zktgOy7pL+P9V9I9+4gQ==" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-2BXap9zdzWJZ5xMe0sdNudMOO1vt+Vm7BcAIUqrlkSZmjJ40G5LFGQe6qWPSXXanXVGQ6LI2CfZhE0wVuFBUBkA==" crossorigin="anonymous" />

    <link rel="shortcut icon" href="{{ asset('logo.jpg') }}">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <title>@yield('pageTitle')</title>
</head>
<body>
  <div class="container">
    @include('partials.sidebar')
    <div class="main">
      @yield('main')
    </div>
  </div>
  </body>
</html>

