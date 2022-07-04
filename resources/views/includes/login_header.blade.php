<!DOCTYPE html>
<html>
<head>
  <title>Log In To Olive Care Staffing Services Admin Panel</title>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('/images/Logo.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('/images/logo-img.jpeg') }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- <link rel="preload" href="https://cdn.shareaholic.net/assets/pub/shareaholic.js" as="script" />
  <meta name="shareaholic:site_id" content="5867ee1e631cfcddacf637057c0c658b" />
  <script data-cfasync="false" async src="https://cdn.shareaholic.net/assets/pub/shareaholic.js"></script> -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<!--===============================================================================================-->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
<!--===============================================================================================-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/animate/animate.css') }}">
<!--===============================================================================================-->  
  <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/css-hamburgers/hamburgers.min.css') }}">
<!--===============================================================================================-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/select2/select2.min.css') }}">

<!--===============================================================================================-->
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('/login_css/util.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('/login_css/main.css') }}"> -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  
  <link rel="stylesheet" type="text/css" href="{{ asset('/css/swal-forms.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('/css/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/owl.theme.default.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/owl.theme.green.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('/login_css/styles.css') }}">

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  
  <script>
    function showLogInModal () {
      $("#login-modal").modal("show");
    }

    function readMore (elem,evt) {
      evt.preventDefault();
      elem = $(elem);
      elem.parent().hide();
      elem.parent().next().show();
    }

    function readLess (elem,evt) {
      evt.preventDefault();
      elem = $(elem);
      elem.parent().hide();
      elem.parent().prev().show();
    }

    function showMore (elem,evt) {
      evt.preventDefault();
      elem = $(elem);
      elem.parent().parent().hide();
      elem.parent().parent().next().show();
    }

    function showLess (elem,evt) {
      evt.preventDefault();
      elem = $(elem);
      elem.parent().parent().hide();
      elem.parent().parent().prev().show();
    }

    function showMoreAboutUs (elem,evt) {
      evt.preventDefault();
      elem = $(elem);
      elem.parent().hide();
      elem.parent().next().show();
    }

    function showLessAboutUs (elem,evt) {
      evt.preventDefault();
      elem = $(elem);
      elem.parent().hide();
      elem.parent().prev().show();
    }
  </script>
</head>
<body class="animated fadeInDown">
  <nav class="navbar navbar-expand-lg navbar-light static-top shadow animated bounceInDown" style="background-color: #fff; margin-bottom: 20px;">
    <div class="container-fluid">
      <a class="navbar-brand" href="/"><h4 style="color: #47a44b; font-weight: bold;">OLIVE CARE</h4></a>
      
    </div>
  </nav>
