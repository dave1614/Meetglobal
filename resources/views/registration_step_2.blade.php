<!DOCTYPE html>
<html lang="en">
<head>
  <title>Login V1</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->  
  <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/bootstrap/css/bootstrap.min.css') }}">
<!--===============================================================================================-->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
<!--===============================================================================================-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/animate/animate.css') }}">
<!--===============================================================================================-->  
  <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/css-hamburgers/hamburgers.min.css') }}">
<!--===============================================================================================-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/select2/select2.min.css') }}">
<!--===============================================================================================-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/login_css/util.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('/login_css/main.css?v=1.3') }}">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  
  <link rel="stylesheet" type="text/css" href="{{ asset('/css/swal-forms.css') }}">
  <link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.7/css/select.dataTables.min.css">
  <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
  <script src="{{ asset('/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
  {{csrf_field()}}
  <style>
    tr{
      cursor: pointer;
    }
  </style>
<!--===============================================================================================-->
<script>
  var tok = $('input[name="_token"]').val();
  var submit_btn1;
  var submit_btn_spinner1;
  var sponsor_id = "";
  var registrar_user_name = "";

  var use_as_sponsor_username;
  var use_as_placement_id;
  var use_as_position;
  var package;

  function enterANewPlacement(elem,evt){
    $("#enter-placement-username-form .wrap-input100").show();
    $("#enter-placement-username-form .container-login100-form-btn").show();
    $("#placement-info-card").hide();
  }

  function getPlacementUserInfo(elem,evt){
    elem = $(elem);
    var placement_user_name = $("#enter-placement-username-form .wrap-input100 #placement_user_name").val();
    
    var url = "/get_sponsor_info_registration";
    var form_data = {
      _token: tok,
      sponsor_user_name : placement_user_name
    }
    console.log(form_data)
    if(placement_user_name != ""){
      $("#placement-spinner").show();
      $.ajax({
        url : url,
        type : "POST",
        responseType : "json",
        dataType : "json",
        data : form_data,
        success : function (response) {
          console.log(response)
          $("#placement-spinner").hide();
          if(response.success && response.user_profile_img != "" && response.sponsor_full_name != "" && response.sponsor_phone_num && response.sponsor_email_address != ""){
            var text_html = "";
            var user_profile_img = response.user_profile_img;
            var sponsor_full_name = response.sponsor_full_name;
            var sponsor_phone_num = response.sponsor_phone_num;
            var sponsor_email_address = response.sponsor_email_address;
            text_html = "<div class='container'>";
            text_html += "<button class='btn btn-warning' onclick='enterANewPlacement(this,event)'>< < Go Back</button>";
            text_html += "<h3 style='font-size: 20px; font-weight: 700;' class='text-center'>Placement Details</h3>";
            text_html += "<div class='row' style='margin-top: 22px;'>";
            text_html += user_profile_img;
            text_html += "<div class='col-sm-8'>";
            text_html += "<p class='text-left' style='font-size: 16px; font-weight: 500;'>Full Name: <em class='text-primary'>"+sponsor_full_name+"</em></p>";
            text_html += "<p class='text-left' style='font-size: 16px; font-weight: 500;'>User Name: <em class='text-primary'>"+placement_user_name+"</em></p>";
            text_html += "<p class='text-left' style='font-size: 16px; font-weight: 500;'>Phone Number: <em class='text-primary'>"+sponsor_phone_num+"</em></p>";
            text_html += "<p class='text-left' style='font-size: 16px; font-weight: 500;'>Email Adress: <em class='text-primary'>"+sponsor_email_address+"</em></p>";
            text_html += "<button type='button' class='btn btn-success' onclick='proceedToPlacementMlmAccount(this,event)'>Proceed > > </button>";
            text_html += "</div>";
            text_html += "</div>";
            text_html += "</div>";
            $("#placement-info-card #placement-info-div").html(text_html);
            $("#placement-info-card #placement-info-div").show();
            $("#placement-info-card #select-placement-positioning-div").hide();
            $("#placement-info-card #select-placement-mlm-account-div").hide();
            $("#enter-placement-username-form .wrap-input100").hide();
            $("#enter-placement-username-form .container-login100-form-btn").hide();
            $("#placement-info-card").show();
          }else if(response.user_name_does_not_exist){
            $("#placement_mlm_db_id").val("");
            $("#placement_position").val("");
            $("#placement-info-card #select-placement-positioning-div").hide();
            $("#placement-info-card #select-placement-mlm-account-div").hide();
            $("#placement-info-card #placement-info-div").hide();
            $("#placement-info-card").hide();
            swal({
              title: 'Error!',
              html: "Sorry Placement Username Entered Does Not Exist.",
              type: 'error'
            })
          }else{
            $("#placement_mlm_db_id").val("");
            $("#placement_position").val("");
            $("#placement-info-card #select-placement-positioning-div").hide();
            $("#placement-info-card #select-placement-mlm-account-div").hide();
            $("#placement-info-card #placement-info-div").hide();
            $("#placement-info-card").hide();
            swal({
              title: 'Error!',
              html: "Something Went Wrong.",
              type: 'error'
            })
          }  
        },error : function () {
          $("#placement_mlm_db_id").val("");
          $("#placement_position").val("");
          $("#placement-spinner").hide();
          $("#placement-info-card #select-placement-positioning-div").hide();
          $("#placement-info-card #select-placement-mlm-account-div").hide();
          $("#placement-info-card #placement-info-div").hide();
          $("#placement-info-card").hide();
          swal({
            title: 'Error',
            text: "Something Went Wrong. Please Check Your Internet Connection",
            type: 'error',                              
          })
        },
        complete: function(xhr, textStatus) {
          console.log(xhr.status);
          if(xhr.status == 419){
            document.location.reload()
          }
        }  
      }); 
    }else{
      $("#placement_mlm_db_id").val("");
      $("#placement_position").val("");
      $("#placement-info-card #select-placement-positioning-div").hide();
      $("#placement-info-card #select-placement-mlm-account-div").hide();
      $("#placement-info-card #placement-info-div").hide();
      $("#placement-info-card").hide();

    }
  }

  function proceedToPlacementMlmAccount (elem,evt) {

      var placement_user_name = $("#placement_user_name").val();
      
      var url = "/get_placement_mlm_account_registration";
      var form_data = {
        _token: tok,
        placement_user_name : placement_user_name
      }
      console.log(form_data)
      if(placement_user_name != ""){
        $("#placement-spinner").show();
        $.ajax({
              url : url,
              type : "POST",
              responseType : "json",
              dataType : "json",
              data : form_data,
              success : function (response) {
               console.log(response)
               $("#placement-spinner").hide();
               if(response.success && response.messages != ""){
                  var messages = response.messages;
                  $("#placement-info-card #select-placement-mlm-account-div").html(messages);
                  $("#placement-info-card #select-placement-mlm-account-div").show();
                  $("#placement-info-card #select-placement-positioning-div").hide();
                  $("#placement-info-card #placement-info-div").hide();
                  $("#placement-info-card").show();
                }else if(response.user_name_does_not_exist){
                  $("#placement_mlm_db_id").val("");
                  $("#placement_position").val("");
                  $("#placement-info-card #select-placement-positioning-div").hide();
                  $("#placement-info-card #select-placement-mlm-account-div").hide();
                  $("#placement-info-card #placement-info-div").hide();
                  $("#placement-info-card").hide();
                  swal({
                    title: 'Error!',
                    html: "Sorry Placement Username Entered Does Not Exist.",
                    type: 'error'
                  })
              }else{
                $("#placement_mlm_db_id").val("");
                $("#placement_position").val("");
                $("#placement-info-card #select-placement-positioning-div").hide();
                $("#placement-info-card #select-placement-mlm-account-div").hide();
                    $("#placement-info-card #placement-info-div").hide();
            $("#placement-info-card").hide();
                  swal({
                    title: 'Error!',
                    html: "Something Went Wrong.",
                    type: 'error'
                  })
              }  
        },error : function () {
          $("#placement_mlm_db_id").val("");
          $("#placement_position").val("");
          $("#placement-spinner").hide();
          $("#placement-info-card #select-placement-positioning-div").hide();
          $("#placement-info-card #select-placement-mlm-account-div").hide();
          $("#placement-info-card #placement-info-div").hide();
          $("#placement-info-card").hide();
          swal({
            title: 'Error',
            text: "Something Went Wrong. Please Check Your Internet Connection",
            type: 'error',                              
          })
        },
        complete: function(xhr, textStatus) {
          console.log(xhr.status);
          if(xhr.status == 419){
            document.location.reload()
          }
        } 
      }); 
    }else{
      $("#placement_mlm_db_id").val("");
      $("#placement_position").val("");
      $("#placement-info-card #select-placement-positioning-div").hide();
      $("#placement-info-card #select-placement-mlm-account-div").hide();
      $("#placement-info-card #placement-info-div").hide();
      $("#placement-info-card").hide();
    }
    }

    function goBackFromSelectPlacementMlmAccount(elem,evt){
      $("#placement-info-card #select-placement-positioning-div").hide();
      $("#placement-info-card #select-placement-mlm-account-div").hide();
      $("#placement-info-card #placement-info-div").show();
      $("#placement-info-card").show();
    }

    function selectThisUserAsPlacement(elem,event){
      elem = $(elem);
      var mlm_db_id = elem.attr("data-mlm-db-id");
      var str = elem.attr("data-str");
      swal({
        title: 'Proceed?',
        html: "Are You Sure You Want To Select <em class='text-primary'>"+str+"</em> As Your Placement?",
        type: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes Proceed!',
        cancelButtonText : "No Cancel"
      }).then((result) => {
        if (result.value) {
          $("#placement_mlm_db_id").val(mlm_db_id);

          $("#placement-spinner").show();
          var url = "/select_positioning_for_mlm_registration";
          var form_data = {
            _token: tok,
            mlm_db_id : mlm_db_id
          }
          console.log(form_data)
          
          $.ajax({
            url : url,
            type : "POST",
            responseType : "json",
            dataType : "json",
            data : form_data,
            success : function (response) {
              console.log(response)
              $("#placement-spinner").hide();
              if(response.success && response.messages != ""){
                var messages = response.messages;
                $("#placement-info-card #select-placement-positioning-div").html(messages);
                $("#placement-info-card #select-placement-positioning-div").show();
                $("#placement-info-card #placement-info-div").hide();
                $("#placement-info-card #select-placement-mlm-account-div").hide();
                $("#placement-info-card").show();
              }else if(response.no_available_position){
                // $("#placement_mlm_db_id").val("");
                // $("#placement_position").val("");
                // $("#placement-info-card #select-placement-positioning-div").hide();
                // $("#placement-info-card #select-placement-mlm-account-div").hide();
                // $("#placement-info-card #placement-info-div").hide();
                // $("#placement-info-card").hide();
                swal({
                  title: 'No Available Position',
                  text: "Sorry No Available Position Under This Account.",
                  type: 'error'
                })
              }else{
                // $("#placement_mlm_db_id").val("");
                // $("#placement_position").val("");
                // $("#placement-info-card #select-placement-positioning-div").hide();
                // $("#placement-info-card #select-placement-mlm-account-div").hide();
                // $("#placement-info-card #placement-info-div").hide();
                // $("#placement-info-card").hide();
                swal({
                  title: 'Error!',
                  html: "Something Went Wrong.",
                  type: 'error'
                })
              }  
            },error : function () {
              $("#placement_mlm_db_id").val("");
              $("#placement_position").val("");
              $("#placement-spinner").hide();
              $("#placement-info-card #select-placement-positioning-div").hide();
              $("#placement-info-card #select-placement-mlm-account-div").hide();
              $("#placement-info-card #placement-info-div").hide();
              $("#placement-info-card").hide();
              swal({
                title: 'Error',
                text: "Something Went Wrong. Please Check Your Internet Connection",
                type: 'error',                              
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
      }); 
    }

    function goBackFromSelectPlacementPosition (elem,evt) {
      $("#placement-info-card #select-placement-positioning-div").hide();
      $("#placement-info-card #select-placement-mlm-account-div").show();
      $("#placement-info-card #placement-info-div").hide();
      $("#placement-info-card").show();
    }

    function selectThisPositionPlacement (elem,evt) {
      elem = $(elem);
      var placement_id = elem.attr("data-mlm-db-id");
      var position = elem.attr("data-position");
      
      if(placement_id != "" && position != ""){
        swal({
          title: 'Confirm?',
          text: "Are You Sure You Want To Select Position " + position + "?",
          type: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes',
          cancelButtonText : "No"
        }).then((result) => {
          if (result.value) {
            // $("#placement_mlm_db_id").val(placement_id);
            // $("#placement_position").val(position);
            
            $.notify({
              message:"Placement Selected. Completing Your Registration..."
            },{
                type : "success"  
            });

            $("#placement-spinner").show();
            $("#placement-info-card").hide();

            var form_data = {
              _token: tok,
              position_selected: true,
              placement_id: placement_id,
              position: position 
            }

            var url =  "/complete_registration_step_2/{{$user_slug}}"

            $.ajax({
              url : url,
              type : "POST",
              responseType : "json",
              dataType : "json",
              data : form_data,
              success : function (response) {
                console.log(response)
                
                if(response.success && response.url != ""){
                  $("#placement-spinner").hide();
                  var url = response.url;
                  $.notify({
                    message:"Successful."
                  },{
                      type : "success"  
                  });
                  setTimeout(function () {
                    window.location.assign(url)
                  }, 2000)
                }else{
                  $("#placement-spinner").hide();
                  $("#placement-info-card").show();
                  swal({
                    title: 'Error!',
                    html: "Something Went Wrong. Try Again",
                    type: 'error'
                  })
                }  
              },error : function () {
                $("#placement-spinner").hide();
                $("#placement-info-card").show();
                swal({
                  title: 'Error',
                  text: "Something Went Wrong. Please Check Your Internet Connection",
                  type: 'error',                              
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
        });
      }
    }

</script>
<style>
  /*.center {
    display: block;
    margin-left: auto;
    margin-right: auto;
    width: 50%;
  }*/
  #main-div h5{
    font-size: 18px;
    margin-bottom: 5px;
  }
  .form-error {
    color: red;
  }
</style>
</head>
<body>
  
  <div class="limiter">

    <div class="container-login100">

      


      <div class="wrap-login100">

        <div class="text-center" style="width: 100%;">
          <img src="{{ asset('/images/logo-img.jpeg?v=1.3') }}" style="width: 150px; height: 150px; border-radius: 50%;">
        </div>
        <!-- <h3 class="text-center" style="width: ">Lorem ipsum dolor sit amet.</h3> -->
        <div class="login100-pic js-tilt" data-tilt>
          <img src="{{ asset('/login_images/img-01.png') }}" alt="IMG">
        </div>

        <div class="login100-form" id="test-0-div">
          <div class="text-center">
            <img src="{{ asset('/images/ajax-loader.gif') }}" id="main-page-spinner" style="display: none;">
          </div>
        </div>
        <?php 

          if($balance > 0){
        ?>

        <div class="login100-form" id="main-div">
          <span class="login100-form-title" style="padding-bottom: 10px;">
            Step 2 Of Registration
          </span>
          
            <div class="">
            
            
              <!-- <h5 style="margin-bottom: 40px;">Commitment Fee: <b></b></h5>
            
              <h5 class="col-6">User Name: </h5><h5 class="col-6 text-right"><b><em style="color: #4158d0;"><?php echo $user_name; ?></em></b></h5>
              <h5 class="col-6">Reg. Expiry Date: </h5><h5 class="col-6 text-right"><b><em style="color: #4158d0;"><?php echo $account_expiry_date; ?></em></b></h5> -->

              <table class="table">
                <tbody>
                  <tr>
                    <td>First Commitment Fee</td>
                    <td><em class="text-primary">₦500</em></td>
                  </tr>
                   <tr>
                    <td>User Name</td>
                    <td><em class="text-primary">{{ $user_name }}</em></td>
                  </tr>
                  <tr>
                    <td>Reg. Expiry Date</td>
                    <td><em class="text-primary">{{ $account_expiry_date }}</em></td>
                  </tr>
                  <tr>
                    <td>Balance</td>
                    <td><em class="text-primary">₦{{ number_format($balance,2) }}</em></td>
                  </tr>
                </tbody>
              </table>

              <h5 class="text-center" style="margin-top: 30px;">Deposit To De Meet Global Resources: </h5>

              <table class="table">
                <tbody>
                  <tr>
                    <td>Bank Name</td>
                    <td>GTBank</td>
                  </tr>
                   <tr>
                    <td>A/c No</td>
                    <td><em class="text-primary">0597824729</em></td>
                  </tr>
                  <tr>
                    <td>Bank Name</td>
                    <td>Access Bank</td>
                  </tr>
                   <tr>
                    <td>A/c No</td>
                    <td><em class="text-primary">0103304419</em></td>
                  </tr>
                </tbody>
              </table>

              <h5 class="text-center" style="margin-top: 30px; margin-bottom: 20px;">Submit Proof Of Payment</h5>
              
              <form action="/submit_proof_of_payment_to_admin/{{ $user_slug }}" method="POST" id="proof-of-payment-form" enctype="multipart/form-data">
               
                <!-- <p style="margin-bottom: 15px;">Click Outside The Input Field When Done With Inputing Placement Username</p> -->
                
                <div class="form-group" >
                  <input class="form-control" type="number" id="amount" placeholder="Enter Amount" name="amount" step="any">
                  <span class="form-error"></span>
                </div>

                <div class="form-group" >
                  <input name="depositors_name" class="form-control" type="text" id="depositors_name" placeholder="Enter Depositors Name" >
                  <span class="form-error"></span>
                </div>

                <div class="form-group" >
                  <label for="date_of_payment">Select Date Of Payment</label>
                  <input class="form-control" type="date" id="date_of_payment" placeholder="Select Date Of Payment" name="date_of_payment" >
                  <span class="form-error"></span>
                </div>

                <div class="form-group">
                  <label for="image">Choose Image: </label>
                  

                  <input placeholder="Upload Payment Proof" type="file" name="image" id="image" class="image inputfile-1" accept="image/*"/>
                 
                  <span class="form-error"></span>
                </div>
                <div class="text-center">
                  <img src="{{ asset('/images/ajax-loader.gif') }}" id="placement-spinner" style="display: none;">
                </div>

                <input type="submit" class="btn btn-primary col-12" style="cursor: pointer;">
              </form>

              
              <?php 
              // $attr = array('id' => 'proof-of-payment-form');
              // echo form_open_multipart('meetglobal/submit_proof_of_payment_to_admin');
              ?>
               <!-- <span class="login100-form-title" style="margin-bottom: 0; margin-top: 30px;">
                  Submit Proof Of Payment
                </span>
              
                
                <div class="wrap-input100" >
                  <input class="input100" type="number" id="amount" placeholder="Enter Amount" name="amount" >
                  <span class="focus-input100"></span>
                  <span class="symbol-input100">
                    <i class="fas fa-dollar-sign" aria-hidden="true"></i>
                  </span>

                </div>

                <div class="wrap-input100" >
                  <input class="input100" type="date" id="date_of_payment" placeholder="Select Date Of Payment" name="date_of_payment" >
                  <span class="focus-input100"></span>
                  <span class="symbol-input100">
                    <i class="fas fa-calendar-week" aria-hidden="true"></i>
                  </span>

                </div>

                <div class="wrap-input100" >
                  <input class="input100" type="file" id="image" placeholder="Select Proof Of Payment" name="image" >
                  <span class="focus-input100"></span>
                  <span class="symbol-input100">
                    <i class="fas fa-image" aria-hidden="true"></i>
                  </span>

                </div>

                <div class="container-login100-form-btn">
                  <button  type="submit" class="login100-form-btn" style="position: relative;">
                    Submit
                    <img src="{{ asset('/images/ajax-loader.gif') }}"  class="spinner">
                  </button>
                </div>
              </form>

               <h5 class="col-6">Bank Name: </h5><h5 class="col-6 text-right"><b>GT Bank</b></h5><br>
              <h5 class="col-6">Account Name: </h5><h5 class="col-6 text-right"><b>De Meet Global Resources</b></h5><br>
              <h5 class="col-6">Account Number: </h5> <h5 class="col-6 text-right"><b>0597824729</b></h5><br>

              <h4 class="col-12 text-center">OR</h4>
              <h5 class="col-6">Bank Name: </h5><h5 class="col-6 text-right"><b>Access Bank</b></h5>
              <h5 class="col-6">Account Name: </h5><h5 class="col-6 text-right"><b>De Meet Global Resources</b></h5>
              <h5 class="col-6">Account Number: </h5> <h5 class="col-6 text-right"><b>0103304419</b></h5>
              <h5 class="col-6">Balance: </h5><h5 class="col-6 text-right"><b>₦<?php echo number_format($balance,2); ?></b> </h5><br><br>
              <h5 class="col-12">Whatsapp <em class="text-primary">08036302232</em> or SMS <em class="text-primary">08133903747</em> with your username for immediate funding.</h5>  -->
            </div>
            <!-- <h4 class="text-center" style="margin-top: 40px; margin-bottom: 20px;">OR</h4>
            <h5 class="text-center">Personalized Account Funding</h5>
            <p class="text-center">1% services charge apply</p> -->
            <?php
            // $monnify_account_details = $this->meetglobal_model->getMonnifyAccountDetails($user_id);
            // if($this->meetglobal_model->isJson($monnify_account_details)){
            //   $monnify_account_details = json_decode($monnify_account_details);
            //   if($monnify_account_details->requestSuccessful && $monnify_account_details->responseMessage == "success"){

            ?>

            
              {{-- <h5 class="col-6">Bank Name: </h5><h5 class="col-6 text-right"><b><?php echo $monnify_account_details->responseBody->bankName; ?></b></h5>
              <h5 class="col-6">Account Name: </h5><h5 class="col-6 text-right"><b><?php echo $monnify_account_details->responseBody->accountName; ?></b></h5>
              <h5 class="col-6">Account Number: </h5> <h5 class="col-6 text-right"><b><?php echo $monnify_account_details->responseBody->accountNumber; ?></b></h5>
              <h5 class="col-6">Balance: </h5><h5 class="col-6 text-right"><b>₦<?php echo number_format($balance,2); ?></b> </h5> --}}


              {{-- <table class="table">
                <tbody>
                  <tr>
                    <td>Bank Name</td>
                    <td><?php echo $monnify_account_details->responseBody->bankName; ?></td>
                  </tr>
                  <tr>
                    <td>A/c Name</td>
                    <td><em class="text-primary"><?php echo $monnify_account_details->responseBody->accountName; ?></em></td>
                  </tr>
                  <tr>
                    <td>A/c No</td>
                    <td><em class="text-primary"><?php echo $monnify_account_details->responseBody->accountNumber; ?></em></td>
                  </tr>
                  <?php 
                  $real_balance = $balance + (0.01 * $balance);
                  ?>
                  <tr>
                    <td>Balance</td>
                    <td><em class="text-primary">₦<?php echo number_format($real_balance,2); ?></em></td>
                  </tr>
                </tbody>
              </table>
 --}}

            
              
            <?php 
                //   } 
                // }
            ?>

        </div>

          
          <?php }else if($balance <= 0){
            
            
          ?>
          <form action="process_user_sign_up" method="POST" class="login100-form animated fadeInDown" id="enter-placement-username-form" style="display: none;">
        
            <span class="login100-form-title" style="margin-bottom: 0;">
              Select Placement
            </span>
            <!-- <p style="margin-bottom: 15px;">Click Outside The Input Field When Done With Inputing Placement Username</p> -->
            
            <div class="wrap-input100" >
              <input class="input100" type="text" id="placement_user_name" placeholder="Enter Placement Username" name="placement_user_name" >
              <span class="focus-input100"></span>
              <span class="symbol-input100">
                <i class="fa fa-users" aria-hidden="true"></i>
              </span>

            </div>

            <div class="text-center">
              <img src="{{ asset('/images/ajax-loader.gif') }}" id="placement-spinner" style="display: none;">
            </div>

            <div class="card" id="placement-info-card" style="display: none; margin-bottom: 20px;">

              <div id="placement-info-div" style="display: none;">
                
              </div>
              
              <div id="select-placement-mlm-account-div" style="display: none;">
                
              </div>

              <div id="select-placement-positioning-div" style="display: none;">
                
              </div>
            </div>

            
            <div class="container-login100-form-btn">
              <button onclick="getPlacementUserInfo(this,event)" type="button" class="login100-form-btn" style="position: relative;">
                Click Here
                <img src="{{ asset('/images/ajax-loader.gif') }}"  class="spinner">
              </button>
            </div>

            <!-- <div class="text-center p-t-12">
              <span class="txt1">
                Have An Account?
              </span>
              <a class="txt2 create-account" href="#">
                Sign In
              </a>
            </div> -->
          </form>

          <?php } ?>


      </div>
    </div>
  </div>
<!--===============================================================================================-->  
  <script src="{{ asset('/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
<!--===============================================================================================-->
  <script src="{{ asset('/vendor/bootstrap/js/popper.js') }}"></script>
  <script src="{{ asset('/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<!--===============================================================================================-->
  <script src="{{ asset('/vendor/select2/select2.min.js') }}"></script>
<!--===============================================================================================-->
  <script src="{{ asset('/vendor/tilt/tilt.jquery.min.js') }}"></script>
  <script >
    $('.js-tilt').tilt({
      scale: 1.1
    })
    $(document).ready(function () {

      $("#proof-of-payment-form").submit(function (evt) {
        evt.preventDefault();
        var me = $(this);
        var url = me.attr("action");
        var file_input = document.querySelector("#proof-of-payment-form #image");
        var file_name = file_input.getAttribute("name");
        var file = file_input.files;
        

        
        var form_data = new FormData();
        if(file_input.value !== ""){
          if(file.length == 1){
            form_data.append(file_name,file[0]);
          }
        }

        var amount = document.querySelector("#proof-of-payment-form #amount").value;
        var depositors_name = document.querySelector("#proof-of-payment-form #depositors_name").value;
        var date_of_payment = document.querySelector("#proof-of-payment-form #date_of_payment").value;


        form_data.append("_token",tok);
        form_data.append("amount",amount);
        form_data.append("depositors_name",depositors_name);
        form_data.append("date_of_payment",date_of_payment);
        $("#proof-of-payment-form #placement-spinner").show();
        $("#proof-of-payment-form .btn.btn-primary").addClass('disabled')
        
        $.ajax({
          url : url,
          type : "POST",
          responseType : "json",
          dataType : "json",
          data : form_data,
          contentType : false,
          cache : false,
          processData : false,
          success : function (response) {
            console.log(response)
            $("#proof-of-payment-form #placement-spinner").hide();
            $("#proof-of-payment-form .btn.btn-primary").removeClass('disabled')
            if(response.success){
              
              $.notify({
              message:"Your Request Has Been Sent To The Admin. You Will Soon Be Credited."
              },{
                type : "success"  
              });
              setTimeout(function () {
                document.location.reload();
              }, 1500);
            }else if(response.empty){
              swal({
                title: 'Ooops',
                text: "You Must Select An Image To Upload For Payment Proof",
                type: 'error',                              
              })
            }else if(!response.only_one_image){
              swal({
                title: 'Ooops',
                text: "You Can Only Select One Image To Upload As Payment Proof",
                type: 'error',                              
              })
            }else if(response.errors != ""){
              
              swal({
                title: 'Error',
                html: response.errors,
                type: 'error',
                
              })
            }else{
              
              me.find(".form-error").html("");
              $.each(response.messages, function (key,value) {

                var element = me.find("#"+key);
                
                  element.closest('div.form-group')
                        
                        .find('.form-error').remove();
                  element.after(value);
                
              });
              $.notify({
                message:"Some Values Were Not Entered Correctly. Please Correct It"
               },{
                  type : "warning"  
              });
            }
          },error : function () {
            $("#proof-of-payment-form #placement-spinner").hide();
            $("#proof-of-payment-form .btn.btn-primary").removeClass('disabled')
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
        
      })


      <?php 
      if($balance <= 0){
      ?>
      $("#enter-placement-username-form").submit(function (evt) {
        evt.preventDefault();
        // var url = $(this).attr("action");
        // var form_data = $(this).serializeArray();
        // var spinner = $(this).find(".spinner");
        // var btn = $(this).find("button");
        // btn.addClass("disabled");
        // spinner.show();
        
        // $.ajax({
        //   url : url,
        //   type : "POST",
        //   responseType : "json",
        //   dataType : "json",
        //   data : form_data,
        //   success : function (response) {         
        //     console.log(response)                         
        //     spinner.hide();
        //     btn.removeClass("disabled");
            
        //     if(response.success ){
        //         document.location.assign(response.url);
        //     }else if(response.half_registered){
        //         var url = response.url;
        //         var user_name = response.user_name;
        //         window.location.assign(url);
        //     }
        //     else if(response.user_exists == false){
        //       swal({
        //         title: 'Error',
        //         text: "This User Does Not Exist",
        //         type: 'error',                                          
        //       })
        //     }else if(response.wrong_password == true){
        //       swal({
        //         title: 'Error',
        //         text: "Wrong Credentials Entered. Try Again",
        //         type: 'error',                                          
        //       })
        //     }else{
        //       swal({
        //         title: 'Error',
        //         text: "Something Went Wrong",
        //         type: 'error',                                          
        //       })
        //     }
        //   },error: function () {
        //     spinner.hide();
        //     btn.removeClass("disabled");
        //     swal({
        //       title: 'Error',
        //       text: "Something Went Wrong. Please Check Your Internet Connection",
        //       type: 'error',                                          
        //     })
        //   }
        // });

      })

      swal({
        title: 'Commitment Fee Successful',
        html: 'Do You Have A Placement?',
        type: 'success',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText : "No"
      }).then((result) => {
        if (result.value) {
          $("#enter-placement-username-form").show();
        }
        else if (
          // Read more about handling dismissals
          result.dismiss === Swal.DismissReason.cancel
        ) {
          $("#main-page-spinner").show();
          // $("#placement-info-card").hide();

          var form_data = {
            _token: tok,
          }

          var url = "/complete_registration_step_2/{{$user_slug}}"

          $.ajax({
            url : url,
            type : "POST",
            responseType : "json",
            dataType : "json",
            data : form_data,
            success : function (response) {
              console.log(response)
              
              if(response.success && response.url != ""){
                $("#main-page-spinner").hide();
                var url = response.url;
                $.notify({
                  message:"Successful."
                },{
                    type : "success"  
                });
                setTimeout(function () {
                  window.location.assign(url)
                }, 2000)
              }else{
                $("#main-page-spinner").hide();
                
                swal({
                  title: 'Error!',
                  html: "Something Went Wrong.",
                  type: 'error'
                })
                setTimeout(function () {
                  document.location.reload();
                }, 2000);
              }  
            },error : function () {
              $("#main-page-spinner").hide();
              
              swal({
                title: 'Error',
                text: "Something Went Wrong. Please Check Your Internet Connection",
                type: 'error',                              
              })
              setTimeout(function () {
                document.location.reload();
              }, 2000);
            },
            complete: function(xhr, textStatus) {
              console.log(xhr.status);
              if(xhr.status == 419){
                document.location.reload()
              }
            } 
          }); 

        }
      });
      <?php
      }
      ?>

    });
      
  
  </script>
<!--===============================================================================================-->
  <script src="{{ asset('/login_js/main.js') }}"></script>
  <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('/js/bootstrap-notify.js') }} "></script>
  <script src="{{ asset('/js/sweetalert2.all.min.js') }}"></script>
  <script src="{{ asset('/js/sweetalert2.min.js') }}"></script>
  <script src="{{ asset('/js/swal-forms.js') }}"></script>
  <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap4.min.js"></script>
  
  <!-- <script src="https://sdk.accountkit.com/en_EN/sdk.js"></script> -->
  <script>
    AccountKit_OnInteractive = function(){
    AccountKit.init(
      {
        appId:320429851941197,         
        state:"abcd", 
        version:"v1.1"
      }
      //If your Account Kit configuration requires app_secret, you have to include ir above
    );
  };
  </script>


</body>
</html>