<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
<link rel="icon" type="image/png" href="../../assets/img/favicon.png">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />



<!-- <link rel="preload" href="https://cdn.shareaholic.net/assets/pub/shareaholic.js" as="script" />
<meta name="shareaholic:site_id" content="5867ee1e631cfcddacf637057c0c658b" />
<script data-cfasync="false" async src="https://cdn.shareaholic.net/assets/pub/shareaholic.js"></script> -->

<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />


<!-- Extra details for Live View on GitHub Pages -->
<!-- Canonical SEO -->

<link rel="stylesheet" href="{{ asset('/css/some_styles.css') }}">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<link rel="canonical" href="https://www.creative-tim.com/product/material-dashboard-pro" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
<link href="{{ asset('/css/fine-uploader-new.min.css')  }}" rel="stylesheet">

<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.7/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">

<!--     Fonts and icons     -->
<!-- <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" /> -->
<link href="https://fonts.googleapis.com/css?family=Roboto|Roboto+Slab" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/css/component.css') }}">
<link rel="stylesheet" href="{{ asset('/css/custom_file_input.css') }}">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
<!-- <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" /> -->
<link rel="stylesheet" href="{{ asset('/css/owl.carousel.css') }}">
<link rel="stylesheet" href="{{ asset('/css/owl.theme.css') }}">

<link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<link href="{{ asset('/css/jquery.ccpicker.css') }}" rel="stylesheet" type="text/css">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.4/chartist.min.css" integrity="sha512-V0+DPzYyLzIiMiWCg3nNdY+NyIiK9bED/T1xNBj08CaIUyK3sXRpB26OUCIzujMevxY9TRJFHQIxTwgzb0jVLg==" crossorigin="anonymous" /> -->

<link href="{{ asset('/css/chartist.min.css') }}" rel="stylesheet" type="text/css">
<!-- CSS Files -->

<link href="{{ asset('/css/treeflex.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/css/perfect-scrollbar.css') }}">


<link rel="stylesheet" href="{{ asset('/css/bs-pagination.min.css') }}">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/emojify.js/1.1.0/css/basic/emojify.min.css" />



<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{ asset('/js/jquery.fine-uploader.js') }}"></script>
<!-- <script async defer src="https://buttons.github.io/buttons.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/0.9.0rc1/jspdf.min.js"></script>
<link rel="stylesheet" href="{{ asset('/css/styles.css') }}">
<link rel="stylesheet" href="{{ asset('/css/fileinput.css') }}">

<script src="<?php echo mix('/css/app.css') . '?v=2.8.8' ?>"  defer></script>
<link href="{{ asset('/css/material-dashboard.min.css?v=2.0.2') }}" rel="stylesheet" />
<script src="<?php echo mix('/js/app.js') . '?v=2.8.8' ?>"  defer></script>
<link href="{{ asset('/css/treeflex.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/css/quill.snow.css') }}" rel="stylesheet">
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
<script src="{{ asset('/js/quill.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<script>
  
  function goCoopInvDownMlm(elem,event,mlm_db_id,your_mlm_db_id){
    elem = $(elem);
    var package = elem.attr("data-package");
    $(".overlay").show();
    // $("#main-page-col-md-12").hide();
    var form_data = {
      _token : "<?php echo csrf_token(); ?>",
      mlm_db_id : mlm_db_id,
      your_mlm_db_id : your_mlm_db_id,
      package : package
    }
    var url = "/coop_inv/view_your_genealogy_tree_down"
    $.ajax({
      url : url,
      type : "POST",
      responseType : "json",
      dataType : "json",
      data : form_data,
      success : function (response) {
        $(".overlay").hide();
        // $("#main-page-col-md-12").show();
        if(response.success && response.messages != ""){
          var messages = response.messages;
          
          // $('img').bind('contextmenu', function(e) {
          //     return false;
          // });
          $("#main-page-col-md-12").html(messages);
          
          window.scrollTo(0, 0);
        }else{
          swal({
            title: 'Ooops',
            text: "Something Went Wrong",
            type: 'warning'
          })
        }
      },error : function () {
        $(".overlay").hide();
        // $("#main-page-col-md-12").show();
        swal({
          title: 'Ooops',
          text: "Something Went Wrong",
          type: 'error'
        })
      },
      complete: function(xhr, textStatus) {
        console.log(xhr.status);
        if(xhr.status == 419){
          document.location.reload()
        }
      } 
    });
  }

  function goCoopInvUpMlm(elem,event,mlm_db_id,your_mlm_db_id){
    elem = $(elem);
    var package = elem.attr("data-package");
    $(".overlay").show();
    var form_data = {
      _token : "<?php echo csrf_token(); ?>",
      show_records : true,
      mlm_db_id : your_mlm_db_id,
      package : package
    }
    var url = "/coop_inv/view_your_genealogy_tree"
    $.ajax({
      url : url,
      type : "POST",
      responseType : "json",
      dataType : "json",
      data : form_data,
      success : function (response) {
        $(".overlay").hide();
        if(response.success && response.messages != ""){
          var messages = response.messages;
          
          // $('img').bind('contextmenu', function(e) {
          //     return false;
          // });
          $("#main-page-col-md-12").html(messages);
          
          window.scrollTo(0, 0);
        }else{
          swal({
            title: 'Ooops',
            text: "Something Went Wrong",
            type: 'warning'
          })
        }
      },error : function () {
        $(".overlay").hide();
        swal({
          title: 'Ooops',
          text: "Something Went Wrong",
          type: 'error'
        })
      },
      complete: function(xhr, textStatus) {
        console.log(xhr.status);
        if(xhr.status == 419){
          document.location.reload()
        }
      } 
    });
  }

  function submitSearchMlmInput (elem,evt) {
    evt.preventDefault();
    var me = $(elem);

    $(".overlay").show();
    // $("#main-page-col-md-12").hide();
    var user_name_search = me.find("#search-mlm-input").val();
    var form_data = {
      _token : "<?php echo csrf_token(); ?>",
      show_records : true,
      user_name: user_name_search
    }
    var url = "/search_users_genealogy_tree";
    $.ajax({
      url : url,
      type : "POST",
      responseType : "json",
      dataType : "json",
      data : form_data,
      success : function (response) {
        $(".overlay").hide();
        // $("#main-page-col-md-12").show();
        if(response.success && response.messages != ""){
          var messages = response.messages;
          
         
          $("#main-page-col-md-12").html(messages);
          
          // $('img').bind('contextmenu', function(e) {
          //     return false;
          // });
          window.scrollTo(0, 0);
        }else if(response.invalid_user){
          swal({
            title: 'Ooops',
            text: "This User Name Is Invalid.",
            type: 'warning'
          })
        }else{
          swal({
            title: 'Ooops',
            text: "Something Went Wrong",
            type: 'warning'
          })
        }
      },error : function () {
        $(".overlay").hide();
        // $("#main-page-col-md-12").show();
        swal({
          title: 'Ooops',
          text: "Something Went Wrong",
          type: 'error'
        })
      },complete: function(xhr, textStatus) {
        console.log(xhr.status);
        if(xhr.status == 419){
          document.location.reload()
        }
      } 
    });
  }
  
  function clearSearchInputTextField (elem,evt) {
    $("#search-mlm-form #search-mlm-input").val("");
  }
  function goDownMlm(elem,event,mlm_db_id,your_mlm_db_id){
    elem = $(elem);
    var package = elem.attr("data-package");
    $(".overlay").show();
    // $("#main-page-col-md-12").hide();
    var form_data = {
      _token : "<?php echo csrf_token(); ?>",
      mlm_db_id : mlm_db_id,
      your_mlm_db_id : your_mlm_db_id,
      package : package
    }
    var url = "/view_your_genealogy_tree_down"
    $.ajax({
      url : url,
      type : "POST",
      responseType : "json",
      dataType : "json",
      data : form_data,
      success : function (response) {
        $(".overlay").hide();
        // $("#main-page-col-md-12").show();
        if(response.success && response.messages != ""){
          var messages = response.messages;
          
          // $('img').bind('contextmenu', function(e) {
          //     return false;
          // });
          $("#main-page-col-md-12").html(messages);
          
          window.scrollTo(0, 0);
        }else{
          swal({
            title: 'Ooops',
            text: "Something Went Wrong",
            type: 'warning'
          })
        }
      },error : function () {
        $(".overlay").hide();
        // $("#main-page-col-md-12").show();
        swal({
          title: 'Ooops',
          text: "Something Went Wrong",
          type: 'error'
        })
      },
      complete: function(xhr, textStatus) {
        console.log(xhr.status);
        if(xhr.status == 419){
          document.location.reload()
        }
      } 
    });
  }

  function goUpMlm(elem,event,mlm_db_id,your_mlm_db_id){
    elem = $(elem);
    var package = elem.attr("data-package");
    $(".overlay").show();
    var form_data = {
      _token : "<?php echo csrf_token(); ?>",
      show_records : true,
      mlm_db_id : your_mlm_db_id,
      package : package
    }
    var url = "/view_your_genealogy_tree"
    $.ajax({
      url : url,
      type : "POST",
      responseType : "json",
      dataType : "json",
      data : form_data,
      success : function (response) {
        $(".overlay").hide();
        if(response.success && response.messages != ""){
          var messages = response.messages;
          
          // $('img').bind('contextmenu', function(e) {
          //     return false;
          // });
          $("#main-page-col-md-12").html(messages);
          
          window.scrollTo(0, 0);
        }else{
          swal({
            title: 'Ooops',
            text: "Something Went Wrong",
            type: 'warning'
          })
        }
      },error : function () {
        $(".overlay").hide();
        swal({
          title: 'Ooops',
          text: "Something Went Wrong",
          type: 'error'
        })
      },
      complete: function(xhr, textStatus) {
        console.log(xhr.status);
        if(xhr.status == 419){
          document.location.reload()
        }
      } 
    });
  }
</script>

<!-- <style>
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
</style>  -->
<style>
  .sidebar .nav p{
    font-weight: bold !important;
  }
  #services-nav-link  ul  li a  span.sidebar-normal{
    font-weight: bold !important;
  }
  .ftb{
    background: #9124a3; 
    cursor: pointer; 
    position: fixed; 
    bottom: 0; right: 0;  
    border-radius: 4px; 
    cursor: pointer; 
    fill: #fff; 
    height: 56px; 
    outline: none; 
    overflow: hidden; 
    margin-bottom: 24px; 
    margin-right: 24px; 
    text-align: center; 
    width: 86px; 
    z-index: 2;
    box-shadow: 0 8px 10px 1px rgba(0,0,0,0.14), 0 3px 14px 2px rgba(0,0,0,0.12), 0 5px 5px -3px rgba(0,0,0,0.2);
  }
  .ftb div{
    display: inline-block; height: 24px; 
    position: absolute; 
    top: 16px; left: 16px; 
    width: 24px;
  }
  .fba{
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
  .fba div{
    display: inline-block; height: 24px; 
    position: absolute; 
    top: 16px; left: 16px; 
    width: 24px;
  }
  .overlay {
    display: none;
    background: black;
    height: 100%;
    left: 0;
    opacity: .5;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 9999;
    bottom: 0;
  }

  #other-overlay {
    
    background: black;
    height: 100%;
    left: 0;
    opacity: .5;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 9999;
    bottom: 0;
  }


</style>
    @routes
  </head>
  <body>

    @inertia
  </body>
  
  {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
  <script src="{{ asset('/js/sweet_alert2.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

  <!-- <script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script> -->
  <script src="{{ asset('/js/bootstrap-material-design.min.js') }}" type="text/javascript"></script>


  <script src="{{ asset('/js/perfect-scrollbar.jquery.min.js') }}"></script>
  <script src="{{ asset('/js/perfect-scrollbar.min.js') }}"></script>


  <!--  Plugin for Sweet Alert -->
  {{-- <script src="{{ asset('/js/sweet_alert2.js') }}"></script> --}}

  <script src="{{ asset('/js/jquery.ccpicker.min.js') }}"></script>
  <script src="{{ asset('/js/jquery.fine-uploader.js') }}"></script>

  <!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>


  <!-- Chartist JS -->
   <script src="{{ asset('/js/chartist.min.js') }}"></script>
   <script src="{{ asset('/js/pagination.min.js') }}"></script>

  <!--  Notifications Plugin    -->
  <script src="{{ asset('/js/bootstrap-notify.js') }}"></script>
  <script src="{{ asset('/js/jquery.bootstrap-wizard.js') }} "></script>
  <script src="{{ asset('/js/moment.js') }} "></script>
  <script src="{{ asset('/js/bootstrap-datetimepicker.min.js') }} "></script>
  <script src="{{ asset('/js/letter_avatar.js') }}"></script>
  <script src="{{ asset('/js/jquery.custom-file-input.js') }}"></script>
  <!-- <script src="//cdnjs.cloudflare.com/ajax/libs/emojify.js/1.1.0/js/emojify.min.js"></script> -->

  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/0.9.0rc1/jspdf.min.js"></script> -->
  <script src="{{ asset('/js/jspdf.min.js') }} "></script>
  <script src="{{ asset('/js/jspdf.plugin.autotable.js') }} "></script>
  <script src="{{ asset('/js/jsPdf_Plugins.js') }}"></script>
  <script src="{{ asset('/js/index (2).js') }}"></script>
  <script src="{{ asset('/js/index.js') }}"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <!-- <script src="{{ asset('/js/material-dashboard.min.js?v=2.1.0') }}" type="text/javascript"></script> -->
  <script src="{{ asset('/js/fileinput.min.js') }}"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('/js/material-dashboard.min.js') }}" type="text/javascript"></script>
  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  <!-- test -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
  <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap4.min.js"></script>
  <!-- <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script> -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
  <script src="{{ asset('/js/functions.js') }}"></script>

  <script src="{{ asset('/js/compressor.min.js') }}"></script>
  <!-- <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script> -->
  <script src="{{ asset('/js/owl.carousel.js') }}"></script>
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.4/chartist.min.js" integrity="sha512-9rxMbTkN9JcgG5euudGbdIbhFZ7KGyAuVomdQDI9qXfPply9BJh0iqA7E/moLCatH2JD4xBGHwV6ezBkCpnjRQ==" crossorigin="anonymous"></script> -->
  <!-- <script async defer src="https://buttons.github.io/buttons.js"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/0.9.0rc1/jspdf.min.js"></script>


  <script async charset="utf-8"
      src="//cdn.iframe.ly/embed.js?api_key=4f057d1dcac3d7a31ad680" 
  ></script>


  <script>
    
    $('.main-panel').perfectScrollbar('destroy');

    $(document).ready( function () {
        $('#myTable').DataTable();
        $("#shop #change-address-form").submit(function (evt) {
          evt.preventDefault();
          var me  = $(this);
          var url = me.attr("action");
          var form_data = me.serializeArray();
          var address = me.find('#address').val();
          $(".spinner-overlay").show();
          
          $.ajax({
            url : url,
            type : "POST",
            responseType : "json",
            dataType : "json",
            data : form_data,
            success : function (response) {
              console.log(response)
              $(".spinner-overlay").hide();
              if(response.success){
                $.notify({
                message:"Address Edited Successfully"
                },{
                  type : "success"  
                });
                $(".details-card .card-body .details-div .address").html(address);
                $("#change-address-modal").modal("hide");

              }else{
                $.each(response.messages, function (key,value) {

                  var element = me.find("#"+key);
                  
                  element.closest('div.form-group')
                          
                          .find('.form-error').remove();
                  element.after(value);
                  
                 });
                  $.notify({
                  message:"Some Values Where Not Valid. Please Enter Valid Values"
                  },{
                    type : "warning"  
                  });
                }
              },error : function () {
                $(".spinner-overlay").hide();
                swal({
                  title: 'Ooops',
                  text: "Something Went Wrong",
                  type: 'error'
                })
              }
          });   
        })
      
      $("#shop #checkout-btn").click(function (evt) {

        var obj = [];
        $("#cart-modal .main-row").each(function(index, el) {
          el = $(el);
          var cart_id = el.attr("data-cart-id");
          var quantity = el.find('.quantity_requested').val();

          obj.push({
            cart_id : cart_id,
            quantity : quantity
          })
        });

        console.log(obj)

        var form_data = {
          data : obj
        };
        $(".spinner-overlay").show();
        var url = "/submit_cart_final_mini_importation";
        $.ajax({
          url : url,
          type : "POST",
          responseType : "json",
          dataType : "json",
          data : form_data,
          success : function (response) {
            console.log(response)
            $(".spinner-overlay").hide();
            if(response.success && response.products.length != 0){
              var messages = response.messages;
              var products = response.products;
              var products_info = '';
              for(var i = 0; i < products.length; i++){
                var image = products[i].image;
                var name = products[i].name;
                var price = products[i].new_price;
                var quantity_requested = products[i].quantity_requested;
                var price_times_quantity = parseFloat((price * quantity_requested).toFixed(2));
                var total_price = response.total_price;
                var total_shipping = response.total_shipping;
                var service_charge = response.service_charge;
                var full_name = response.full_name;
                var user_name = response.user_name;
                var address = response.address;
                var phone_number = response.phone_number;

                var grand_total_fee = parseFloat(((total_shipping + total_price) + (total_shipping + total_price) * 0.02).toFixed(2));



                if(name.length > 50){
                  name = name.slice(0, 50) + "...";
                }

                products_info += '<div class="product-row row">';
                products_info += '<div class="col-4" style="margin-top: 6px; padding: 0;">';
                products_info += '<img class="col-12" style="padding: 0;" src="{{ asset('/images/') }}'+ image +'" alt="">';
                products_info += '</div>';

                products_info += '<div class="col-8" style="padding: 0">';

                products_info += '<span class="product-name">'+name+'</span>';

                products_info += '<span class="sub-total-price text-primary">₦ '+addCommas(price_times_quantity)+'</span>';
                products_info += '<span class="quantity">Qty: '+quantity_requested+'</span>';
                products_info += '</div>';
                products_info += '</div>';
              }      
              console.log(products_info)

              $(".product-advance-details-div").html(messages)
              
              $("#products-div-container").html(products_info);
              $(".order-summary-card .total-div .sub-total-val").html('₦' + addCommas(total_price));
              $(".order-summary-card .total-div .shipping-val").html('₦' + addCommas(total_shipping));
              $(".order-summary-card .total-div .service-charge-val").html('₦' + addCommas(service_charge));
              $(".order-summary-card .total-div .total-val").html('₦' + addCommas(grand_total_fee));
              $(".details-card .card-body .details-div .name").html(full_name);
              $(".details-card .card-body .details-div .address").html(address);
              $(".details-card .card-body .details-div .phone").html(phone_number);
              $(".order-summary-card .heading span").html("YOUR ORDER("+products.length+" items)")
              $("#cart-modal").modal("hide");
              $("#shop .products-row").hide();
              $("#shop .pagination-links").hide();
              $("#shop .top-links").hide();
              $("#checkout-container").show()
            }else{
              var minimum_quantity_order_error_details = response.minimum_quantity_order_error_details;
              console.log(minimum_quantity_order_error_details)
              var text = "<h5 class=''>Checkout Couldn't Be Processed because Of The Following Errors: </h5>";
              text += "<table class='table table-bordered table-responsive'>";
              text += "<thead>";
              text += "<tr style='cursor: unset;'>";
              text += "<th>#</th>";
              text += "<th></th>";
              text += "<th>Product Name</th>";
              text += "<th>Error Details</th>";
              text += "</tr>";
              text += "</thead>";

              text += "<tbody>";
              var j = 0;
              for(var i = 0; i < minimum_quantity_order_error_details.length; i++){
                j++;
                var product_name = minimum_quantity_order_error_details[i].name;
                var product_image = minimum_quantity_order_error_details[i].image;
                var quantity_requested = minimum_quantity_order_error_details[i].quantity_requested;
                var minimum_quantity_order = minimum_quantity_order_error_details[i].minimum_quantity_order;

                product_image = "{{ asset('/images/') }}"+product_image
                // if(product_name.length > 70){
                //   product_name = product_name.slice(0, 70) + "...";
                // }

                product_name = "<span style='font-size: 11px;'>" + product_name + "</span>";

                var details = "<span style='font-size: 11px;'>You Requested <em class='text-primary'>"+ addCommas(quantity_requested) + " Unit(s)</em>. But The Minimum quantity Order of this product is <em class='text-primary'>" + minimum_quantity_order + " Unit(s)</em>.</span>";

                text += "<tr style='cursor: unset;'>";

                text += "<td>"+j+"</td>";
                
                text += "<td>";
                text += "<img style='width: 60px; height: 60px;' src='"+product_image+"'>";
                text += "</td>";

                text += "<td>";
                text += "<p>"+product_name+"</p>";
                text += "</td>";
                text += "<td>"+details+"</td>";
                text += "</tr>";
              }
              text += "</tbody>";

              text += "</table>";
              swal({
                title: 'Error',
                text: text,
                type: 'error'
              })
            }

          },error : function () {
            $(".spinner-overlay").hide();
            $.notify({
              message:"Sorry Something Went Wrong. Please Check Your Internet Connection"
              },{
                type : "danger" 
            });
          } 
        }); 
      })
      

      $("#shop .product-card").click(function (evt) {
        var me = $(this);
        var id = me.attr("data-id");
        // var slug = me.attr("data-slug");

        // var form_data = {
        //   product_id : id
        // };

        // $(".spinner-overlay").show();
        var url = "/product/"+id;
        // $.ajax({
        //   url : url,
        //   type : "POST",
        //   responseType : "json",
        //   dataType : "json",
        //   data : form_data,
        //   success : function (response) {
        //     console.log(response)
        //     $(".spinner-overlay").hide();
        //     if(response.success && response.messages != ""){
        //       var messages = response.messages;
              
        //       $("#shop").hide();
        //       $("#product-info-card .card-body").html(messages);
        //       $("#product-info-card").show();
              
        //     }

        //   },error : function () {
        //     $(".spinner-overlay").hide();
        //     $.notify({
        //       message:"Sorry Something Went Wrong. Please Check Your Internet Connection"
        //       },{
        //         type : "danger" 
        //     });
        //   } 
        // });   

        window.location.assign(url)
      })

     $("#shop .product-card").mouseenter(function(event) {
        $(this).css({
          "box-shadow" : "2px 2px #D8D8D8"
        })
      });

      $("#shop .product-card").mouseleave(function(event) {
        $(this).css({
          "box-shadow" : "0 1px 4px 0 rgba(0,0,0,.14)"
        })
      });

      $("#shop .btn-orange").mouseenter(function (evt) {
        $(this).css({
          "background-color" : "#9124a3",
          "border" : "1px solid #9124a3",
          "color" : "#FFF"
        })
      })

      $("#shop .btn-orange").mouseleave(function (evt) {
        $(this).css({
          "background-color" : "rgb(0,0,0,0)",
          "border" : "1px solid #9124a3",
          "color" : "#9124a3"
        })
      })
    } );

    $("#shop #search-products-form").submit(function (evt) {
      evt.preventDefault();
      var me = $(this);
      var search_val = me.find("#search_products").val();
      if(search_val != ""){
        search_val = search_val.toLowerCase()
        var url = "/mini_importation_search/"+search_val;
        // console.log(url)
        window.location.assign(url)
      }else{
        swal({
          title: 'Ooops',
          text: "Enter Text In The Search Field To Search",
          type: 'error'
        })
      }
    })

    $('.datetimepicker').datetimepicker({
      // minView: 2,
        // format: 'YYYY-MM-DD',
        icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar",
            up: "fa fa-chevron-up",
            down: "fa fa-chevron-down",
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-screenshot',
            clear: 'fa fa-trash',
            close: 'fa fa-remove'
        }
    });

  </script>
</body>


</html>
