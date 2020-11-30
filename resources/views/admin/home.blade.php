<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/css/app.css">
</head>

<body>
<div id="app">
  <v-app id="sandbox">
    <navbar-component></navbar-component>
    <v-content>
      <v-container>
        <router-view></router-view>
      </v-container>
    </v-content>
      <v-footer app>
      <span class="px-4">&copy; 2020</span>
    </v-footer>
  </v-app>
</div>
<script type="text/javascript" src="/js/app.js"></script>
</body>
</html>
