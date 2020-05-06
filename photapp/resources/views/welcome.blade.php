
<!Doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>phot app</title>

  <!-- Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Merriweather|Roboto:400">
  <link rel="stylesheet" href="https://unpkg.com/ionicons@4.2.2/dist/css/ionicons.min.css">

  <!-- Styles -->
  <link href='https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons' rel="stylesheet">
  <link href="{{ url(mix('css/style.css')) }}" rel="stylesheet">
</head>
<body>
<?php if (env('VERSION')) : ?>
        <div class="version" style="position:absolute;top: 0;right: 0; font-size: 6px;color: #000;z-index:1000;">ver=<?php echo env('VERSION', 'dev') ?></div>
    <?php endif ?>
  <div id="app"></div>
  <script src="{{ url(mix('/js/manifest.js')) }}"></script>
  <script src="{{ url(mix('/js/vendor.js')) }}"></script>
  <script src="{{ url(mix('/js/main.js')) }}"></script>
</body>
</html>