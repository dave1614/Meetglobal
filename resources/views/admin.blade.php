<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link href="babaraba" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">


    <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
    <script src="{{ mix('/js/app.js') }}" defer></script>
    <style>
      .form-error{
        color: red;
        font-style: italic;
      }
      .fab{
        background: #9124a3; 
        cursor: pointer; 
        position: fixed; 
        bottom: 0; right: 0;  
        border-radius: 50%; 
        cursor: pointer; 
        fill: #fff; 
        height: 56px; 
        outline: none; 
        overflow: hidden; 
        margin-bottom: 24px; 
        margin-right: 24px; 
        text-align: center; 
        width: 56px; 
        z-index: 4000;
        box-shadow: 0 8px 10px 1px rgba(0,0,0,0.14), 0 3px 14px 2px rgba(0,0,0,0.12), 0 5px 5px -3px rgba(0,0,0,0.2);
      }
      .fab div{
        display: inline-block; height: 24px; 
        position: absolute; 
        top: 16px; left: 16px; 
        width: 24px;
      }
    </style>
    @routes
  </head>
  <body>
    @inertia
  </body>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('/js/bootstrap-notify.js') }} "></script>
  {{-- <script src="{{ asset('/js/sweetalert2.all.min.js') }}"></script>
  <script src="{{ asset('/js/sweetalert2.min.js') }}"></script>
  <script src="{{ asset('/js/swal-forms.js') }}"></script> --}}
</html>