<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login V1</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
	<!-- <link rel="preload" href="https://cdn.shareaholic.net/assets/pub/shareaholic.js" as="script" />
	<meta name="shareaholic:site_id" content="5867ee1e631cfcddacf637057c0c658b" />
	<script data-cfasync="false" async src="https://cdn.shareaholic.net/assets/pub/shareaholic.js"></script> -->
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
	<script src="{{ asset('/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
<!--===============================================================================================-->
	{{csrf_field()}}
<script>
	var tok = $('input[name="_token"]').val();
	var url1 = "";
	var submit_btn1;
	var submit_btn_spinner1;
	var global_email;

	var placement_mlm_db_id;
	var placement_position;

	function changeUserName (elem,evt) {
		$("#forgot-pass-otp-form input").val("");
		
		$("#enter-username-form").show();
        $("#forgot-pass-otp-form").hide();
	}

	function sendForgotPassOtp(elem,evt,again = false){
		evt.preventDefault();
		if(!again){
			var spinner = $("#enter-username-form").find(".spinner");
		    var btn = $("#enter-username-form").find("button");
		}else{
			var spinner = $("#forgot-pass-otp-form").find(".spinner");
		    var btn = $("#forgot-pass-otp-form").find("button");
		}
	    var form_data = $("#enter-username-form").serializeArray();
		btn.addClass("disabled");
        spinner.show();
        var url = "/send_forgot_password_otp";
        form_data = form_data.concat({
        	"name": "_token",
        	"value": tok
        })
		
		$.ajax({
	        url : url,
	        type : "POST",
	        responseType : "json",
	        dataType : "json",
	        data : form_data,
	        success : function (response) {
	        	console.log(response)
	        	btn.removeClass("disabled");
        		spinner.hide();
        		if(response.success){
		        	$("#enter-username-form").hide();
                    $("#forgot-pass-otp-form .login100-form-title").html("An OTP Has Been Sent To The Following Email <br> <small><em class='text-primary'>" + global_email + "</em></small>");
                    $("#forgot-pass-otp-form").show();
                }else{
                	$.notify({
	                	message:"Sorry Something Went Wrong."
	                },{
	                  	type : "warning"  
	                });
                }
            },error : function () {
            	btn.removeClass("disabled");
        		spinner.hide();
            	$.notify({
                	message:"Sorry Something Went Wrong. Please Check Your Internet Connection And Try Again"
                },{
                  	type : "danger"  
                });
            },
            complete: function(xhr, textStatus) {
              console.log(xhr.status);
              if(xhr.status == 419){
                document.location.reload()
              }
            } 
        });
	}

	function changeYourDetails(elem,evt){
		$("#signup-otp-form input").val("");
		// $("#sign-up-form input").val("");
		$("#sign-up-form").show();
        $("#signup-otp-form").hide();
	}

	function sendSignUpOtpAgain(elem,evt){
		evt.preventDefault();
		$("#sign-up-form").submit();
		
		$("#signup-otp-form").find(".spinner").show();
        $("#signup-otp-form").find("button").addClass("disabled");
		setTimeout(function () {
			$("#signup-otp-form").find(".spinner").hide();
        	$("#signup-otp-form").find("button").removeClass("disabled");
		}, 3000);
	}

	function forgotPassCallback(response) {
        console.log(response);
        if(response.status === "PARTIALLY_AUTHENTICATED") {
            window.scrollTo(0,document.body.scrollHeight);
            var code = response.code;
            var state = response.state;
            console.log(code)
            var form_data = {
            	_token: tok,
                code : code,
                state : state
            }
            console.log(form_data)
            submit_btn_spinner1.show();
            submit_btn1.addClass("disabled");

            $.ajax({
                url : url1,
                type : "POST",
                responseType : "json",
                dataType : "json",
                data : form_data,
                success : function (response) {
                    submit_btn_spinner1.hide();
                    submit_btn1.removeClass("disabled");
                    console.log(response)
                    if(response.success && response.url != ""){
                        var url = response.url;
                        $("#new-password-form").attr("action",url);
                        $("#new-password-modal").modal("show");
						
                    }else if(response.phone_change){
                    	swal({
		                  title: 'Error',
		                  text: "The Phone Number Was Changed! Try Again And Don't Change It",
		                  type: 'error',						                  
		                })
                    }
                },error : function (jqXHR,error, errorThrown) {
                    submit_btn_spinner1.hide();
                    submit_btn1.removeClass("disabled");
                    $.notify({
                      message:"Sorry Something Went Wrong."
                      },{
                        type : "danger"  
                      });
                },
	            complete: function(xhr, textStatus) {
	              console.log(xhr.status);
	              if(xhr.status == 419){
	                document.location.reload()
	              }
	            } 
            });  
        }
        else if (response.status === "NOT_AUTHENTICATED") {
          // handle authentication failure
          console.log("Authentication failure");
        }
        else if (response.status === "BAD_PARAMS") {
          // handle bad parameters
          console.log("Bad parameters");
        }
    }

	function forgotPass (elem,evt) {
        $("#login-form").hide("fast");
        $("#enter-username-form").show("fast");
    }

    function signInForgot (elem,evt) {
    	$("#login-form").show("fast");
        $("#enter-username-form").hide("fast");
    }

    function getSponsorUserInfo(elem,evt){
    	elem = $(elem);
    	var sponsor_user_name = elem.val();
    	
    	var url = "/get_sponsor_info_registration";
    	var form_data = {
    		_token: tok,
    		sponsor_user_name : sponsor_user_name
    	}
    	console.log(form_data)
    	if(sponsor_user_name != ""){
    		$("#sponsor-spinner").show();
		    $.ajax({
	            url : url,
	            type : "POST",
	            responseType : "json",
	            dataType : "json",
	            data : form_data,
	            success : function (response) {
	               console.log(response)
	               $("#sponsor-spinner").hide();
	               if(response.success && response.user_profile_img != "" && response.sponsor_full_name != "" && response.sponsor_phone_num && response.sponsor_email_address != ""){
	               		var text_html = "";
			            var user_profile_img = response.user_profile_img;
			            var sponsor_full_name = response.sponsor_full_name;
			            var sponsor_phone_num = response.sponsor_phone_num;
			            var sponsor_email_address = response.sponsor_email_address;
			            text_html = "<div class='container'>";
			            text_html += "<h3 style='font-size: 20px; font-weight: 700;' class='text-center'>Sponsor Details</h3>";
			            text_html += "<div class='row' style='margin-top: 22px;'>";
			            text_html += user_profile_img;
			            text_html += "<div class='col-sm-8'>";
			            text_html += "<p class='text-left' style='font-size: 16px; font-weight: 500;'>Full Name: <em class='text-primary'>"+sponsor_full_name+"</em></p>";
			            text_html += "<p class='text-left' style='font-size: 16px; font-weight: 500;'>User Name: <em class='text-primary'>"+sponsor_user_name+"</em></p>";
			            text_html += "<p class='text-left' style='font-size: 16px; font-weight: 500;'>Phone Number: <em class='text-primary'>"+sponsor_phone_num+"</em></p>";
			            text_html += "<p class='text-left' style='font-size: 16px; font-weight: 500;'>Email Adress: <em class='text-primary'>"+sponsor_email_address+"</em></p>";
			            text_html += "</div>";
			            text_html += "</div>";
			            text_html += "</div>";
		               	$("#sponsor-info-card").html(text_html);
					   	$("#sponsor-info-card").show();
	               	}else if(response.user_name_does_not_exist){
	               		$("#sponsor-info-card").html("");
						$("#sponsor-info-card").hide();
			            swal({
			              title: 'Error!',
			              html: "Sorry Sponsor Username Entered Does Not Exist.",
			              type: 'error'
			            })
			        }else{
			        	$("#sponsor-info-card").html("");
						$("#sponsor-info-card").hide();
			            swal({
			              title: 'Error!',
			              html: "Something Went Wrong.",
			              type: 'error'
			            })
			        }  
				},error : function () {
					$("#sponsor-spinner").hide();
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
			$("#sponsor-info-card").html("");
			$("#sponsor-info-card").hide();
		}
    }

    function getPlacementUserInfo(elem,evt){
    	elem = $(elem);
    	var placement_user_name = elem.val();
    	
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
		               		$("#placement_mlm_db_id").val("");
		      				$("#placement_position").val("");
		               		$("#placement-info-card #select-placement-positioning-div").hide();
		               		$("#placement-info-card #select-placement-mlm-account-div").hide();
			               	$("#placement-info-card #placement-info-div").hide();
							$("#placement-info-card").hide();
				            swal({
				              title: 'No Available Position',
				              text: "Sorry No Available Position Under This Account.",
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
		      		$("#placement_mlm_db_id").val(placement_id);
		      		$("#placement_position").val(position);
		      		
		      		$.notify({
	                	message:"Placement Selected. Fill Other Details."
	                },{
	                  	type : "success"  
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

				<?php
					
					if(isset($_GET['id'])){
						
						echo '<form action="/process_sign_in" method="POST" class="llogin100-form validate-form animated fadeInDown" id="login-form" style="display: none;">';
						
					}else{
						
						echo '<form action="/process_sign_in" method="POST" class="login100-form validate-form animated fadeInDown" id="login-form" >';

					}
				?>

				
					<span class="login100-form-title">
						Member Login
					</span>

					<div class="wrap-input100 validate-input" data-validate = "">
						<input class="input100" type="text" id="user_name_login" name="user_name_login" placeholder="Username" >
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "">
						<input class="input100" type="password" id="password_login" placeholder="Password" name="password_login">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn" style="position: relative;">
							Login
							<img src="{{ asset('/images/ajax-loader.gif') }}"  class="spinner">
						</button>
					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							Forgot
						</span>
						<a class="txt2" href="#" onclick="forgotPass(this,event)">
							Username / Password?
						</a>
					</div>

					<div class="text-center p-t-136 create-account">
						<a class="txt2" href="#">
							Create Your Account
							<i class="fas fa-arrow-right m-l-5" aria-hidden="true"></i>
						</a>
					</div>
				</form>



				<?php
					
					if(isset($_GET['id'])){
						
						echo '<form action="/process_user_sign_up?id='.$_GET['id'].'" method="POST" class="login100-form validate-form animated fadeInDown" id="sign-up-form">';
					}else{
						
						echo '<form action="/process_user_sign_up" method="POST" class="login100-form validate-form animated fadeInDown" id="sign-up-form" style="display: none;">';
					}
				?>
					<span class="login100-form-title">
						Create Your Account
					</span>

					
		      		<input type="hidden" name="placement_mlm_db_id" id="placement_mlm_db_id">
		      		<input type="hidden" name="placement_position" id="placement_position">

					<div class="wrap-input100 validate-input" data-validate = "">
						<input class="input100" type="text" id="sponsor_user_name" placeholder="Sponsor Username" name="sponsor_user_name" onfocusout="getSponsorUserInfo(this,event)"  value="<?php if(isset($_GET['id'])){ echo $_GET['id']; } ?>">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user-friends" aria-hidden="true"></i>
						</span>

					</div>
					<div class="text-center">
						<img src="{{ asset('/images/ajax-loader.gif') }}" id="sponsor-spinner" style="display: none;">
					</div>

					<div class="card" id="sponsor-info-card" style="display: none; margin-bottom: 20px;">
						
					</div>

					<div class="wrap-input100 validate-input" data-validate = "">
						<input class="input100" type="text" id="full_name" name="full_name" placeholder="Full Name" >
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fas fa-user-tie" aria-hidden="true"></i>
						</span>
					</div>

					

					<div class="wrap-input100 validate-input" data-validate = "">
						<input class="input100" type="number" id="phone" name="phone" placeholder="Mobile Number e.g 08127027321">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fas fa-mobile" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "">
						<input class="input100" type="email" id="email" name="email" placeholder="Email" >
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>
					
	
					<div class="wrap-input100 validate-input" data-validate = "">
						<input class="input100" type="text" id="user_name_sign_up" name="user_name_sign_up" placeholder="Username" >
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "">
						<input class="input100" type="password" id="password_sign_up" placeholder="Password" name="password_sign_up">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>

					
					
					<div class="container-login100-form-btn">
						<button type="submit" class="login100-form-btn" style="position: relative;">
							Register
							<img src="{{ asset('/images/ajax-loader.gif') }}"  class="spinner">
						</button>
					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							Have An Account?
						</span>
						<a class="txt2 create-account" href="#">
							Sign In
						</a>
					</div>

					<input type="hidden" id="random_bytes" name="random_bytes" value='{{ md5(uniqid(rand(), true)) }}' readonly>
				</form>


				<?php
					$attr = array('class' => 'login100-form validate-form animated fadeInDown','id' => 'signup-otp-form','style' => 'display: none;');

					if(isset($_GET['id'])){
						
						echo '<form action="/process_user_sign_up_cont?id='.$_GET['id'].'" method="POST" class="login100-form validate-form animated fadeInDown" id="signup-otp-form" style="display: none;">';
					}else{
						
						echo '<form action="/process_user_sign_up_cont" method="POST" class="login100-form validate-form animated fadeInDown" id="signup-otp-form" style="display: none;">';
					}
				?>
					<span class="login100-form-title">
						An OTP Has Been Sent To 
					</span>

					<div class="wrap-input100 validate-input" data-validate = "">
						<input class="input100" type="number" id="otp_input" name="otp_input" placeholder="Enter OTP" >
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>

					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn" style="position: relative;">
							Proceed
							<img src="{{ asset('/images/ajax-loader.gif') }}"  class="spinner">
						</button>
					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							Not Received? 
						</span>
						<a class="txt2" href="#" onclick="sendSignUpOtpAgain(this,event)">
							Send OTP Again
						</a>
					</div>

					<div class="text-center p-t-12">
						
						<a class="txt2" href="#" onclick="changeYourDetails(this,event)">
							Change Your Details
						</a>
					</div>

					
				</form>


				<?php
					$attr = array('class' => 'login100-form validate-form animated fadeInDown','id' => 'forgot-pass-otp-form','style' => 'display: none;');

					if(isset($_GET['id'])){
						
						echo '<form action="/verify_user_forgot_password_otp?id='.$_GET['id'].'" method="POST" class="login100-form validate-form animated fadeInDown" id="forgot-pass-otp-form" style="display: none;">';
					}else{
						echo '<form action="/verify_user_forgot_password_otp" method="POST" class="login100-form validate-form animated fadeInDown" id="forgot-pass-otp-form" style="display: none;">';
					}
				?>
					<span class="login100-form-title">
						An OTP Has Been Sent To 
					</span>

					<div class="wrap-input100 validate-input" data-validate = "">
						<input class="input100" type="number" id="otp_input" name="otp_input" placeholder="Enter OTP" >
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>

					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn" style="position: relative;">
							Proceed
							<img src="{{ asset('/images/ajax-loader.gif') }}"  class="spinner">
						</button>
					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							Not Received? 
						</span>
						<a class="txt2" href="#" onclick="sendForgotPassOtp(this,event,true)">
							Send OTP Again
						</a>
					</div>

					<div class="text-center p-t-12">
						
						<a class="txt2" href="#" onclick="changeUserName(this,event)">
							Wrong Username Entered?
						</a>
					</div>

					
				</form>


				<?php
					$attr = array('class' => 'login100-form validate-form animated fadeInDown','id' => 'enter-new-password-form','style' => 'display: none;');

					if(isset($_GET['id'])){
						
						echo '<form action="/change_password_reset?id='.$_GET['id'].'" method="POST" class="login100-form validate-form animated fadeInDown" id="enter-new-password-form" style="display: none;">';
					}else{
						echo '<form action="/change_password_reset" method="POST" class="login100-form validate-form animated fadeInDown" id="enter-new-password-form" style="display: none;">';
					}
				?>
					<span class="login100-form-title">
						Enter New Password For Username
					</span>

					<div class="wrap-input100 validate-input" data-validate = "">
						<input class="input100" type="text" id="new_password" name="new_password" placeholder="Password" >
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>

					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn" style="position: relative;">
							Proceed
							<img src="{{ asset('/images/ajax-loader.gif') }}"  class="spinner">
						</button>
					</div>
					
				</form>


				
				<form action="/check_if_user_name_exists" method="POST" class="login100-form validate-form animated fadeInDown" id="enter-username-form" style="display: none;">
					<span class="login100-form-title">
						Enter Username
					</span>

					<div class="wrap-input100 validate-input" data-validate = "">
						<input class="input100" type="text" id="user-name" name="user_name" placeholder="Enter UserName">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user" aria-hidden="true"></i>
						</span>
					</div>
					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn" style="position: relative;">
							Submit
							<img src="{{ asset('/images/ajax-loader.gif') }}"  class="spinner">
						</button>
					</div>

					<div class="text-center p-t-12">
						
						<a class="txt2" href="#" onclick="signInForgot(this,event)">
							Sign In
						</a>
					</div>
				</form>


			</div>
		</div>
	</div>

	<div class="modal animated fadeInDown" id="new-password-modal">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h4 class="modal-title">Enter New Password</h4>
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	      </div>
	      <div class="modal-body">
	        <form action="" method="POST" id="new-password-form">
		        <div class="form-group">
		        	<input type="text" class="form-control required" id="new-password" name="new-password">
		        </div>
	    	</form>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	      </div>
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

			$("#new-password-form").submit(function(evt){
				evt.preventDefault();
			    var form_data = $(this).serializeArray();
			    console.log(form_data)
			    var url = $(this).attr("action");
			    form_data = form_data.concat({
		        	"name": "_token",
		        	"value": tok
		        })
			    $.ajax({
	                url : url,
	                type : "POST",
	                responseType : "json",
	                dataType : "json",
	                data : form_data,
	                success : function (response) {
	                    submit_btn_spinner1.hide();
	                    submit_btn1.removeClass("disabled");
	                    $("#new-password-modal").modal("hide");
        				console.log(response);
        				if(response.success){
        					document.location.reload();
        				}else if(response.expired){
        					swal({
			                  title: 'Error',
			                  text: "This Session Has Expired Please Try Again",
			                  type: 'error',						                  
			                })
        				}else{
        					swal({
			                  title: 'Error',
			                  text: "Something Went Wrong Please Try Again",
			                  type: 'error',						                  
			                })
        				}
        			},error : function () {
        				swal({
		                  title: 'Error',
		                  text: "Something Went Wrong Please Try Again",
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
			})

			// $("#new-password-modal").modal("show");

			$("#enter-username-form").submit(function (evt) {
				evt.preventDefault();
				var url = $(this).attr("action");
				var form_data = $(this).serializeArray();
				var spinner = $(this).find(".spinner");
	            var btn = $(this).find("button");
	            btn.addClass("disabled");
	            spinner.show();

	            form_data = form_data.concat({
		        	"name": "_token",
		        	"value": tok
		        })
				
				$.ajax({
			        url : url,
			        type : "POST",
			        responseType : "json",
			        dataType : "json",
			        data : form_data,
			        success : function (response) {
			        	console.log(response)
			        	btn.removeClass("disabled");
	            		spinner.hide();
						if(response.empty == true){
			    			swal({
			                  title: 'Error',
			                  text: "Sorry, This Field Cannot Be Empty",
			                  type: 'error',						                  
			                })
			            }    
			        	else if(response.no_post == true){
			        		$.notify({
			                	message:"Sorry Something Went Wrong"
			                },{
			                  	type : "warning"  
			                });
			    		
			    		}else if(response.success == true && response.mobile !== "" && response.full_name !== "" && response.user_id !== ""){
			    			var email = response.email;
			    			var mobile = response.mobile;
			    			var full_name = response.full_name;
			    			var user_id = response.user_id;
			    			var url = response.url;

			    			global_email = email;

			    			swal({
					            title: 'Choose Action',
					            html: "<h4>Are These Your Details?</h4><p style='color: black;'>Full Name: <span class='text-primary' style='font-style: italic;'>"+ full_name +"</span></p><p style='color: black;'>Mobile Number :<span class='text-primary' style='font-style: italic;'>"+ mobile +"</span></p><p style='color: black;'>Email Adress :<span class='text-primary' style='font-style: italic;'>"+ email +"</span></p>",
					            type: 'success',
					            showCancelButton: true,
					            confirmButtonColor: '#3085d6',
					            cancelButtonColor: '#d33',
					            confirmButtonText: 'Yes',
					            cancelButtonText : "No"
					        }).then((result) => {
								if (result.value) {

									sendForgotPassOtp(this,event);
	
							    }
					        },function(dismiss){
					            if(dismiss == 'cancel'){
					            	
					            }
				            });
			    		}else{
			    			swal({
			                  title: 'Error',
			                  text: "Sorry, This Username Is Not Associated With Any Registered Account",
			                  type: 'error',						                  
			                })
			    		}
			    	},error : function () {
			    		btn.removeClass("disabled");
						spinner.hide();
			    		$.notify({
		                	message:"Sorry Something Went Wrong. Please Check Your Internet Connection And Try Again"
		                },{
		                  	type : "danger"  
		                });
			    	},
		            complete: function(xhr, textStatus) {
		              console.log(xhr.status);
		              if(xhr.status == 419){
		                document.location.reload()
		              }
		            } 
			    });	
			    		
			})

			$(".create-account").click(function (evt) {
				evt.preventDefault();
				$("#login-form").toggle();
				$("#sign-up-form").toggle();
				window.scrollTo(0,0);
			})
			    
		})

	
	</script>
<!--===============================================================================================-->
	<script src="{{ asset('/login_js/main.js?version=3.0') }}"></script>
	<script src="{{ asset('/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('/js/bootstrap-notify.js') }}"></script>
	<script src="{{ asset('/js/sweetalert2.all.min.js') }}"></script>
	<script src="{{ asset('/js/sweetalert2.min.js') }}"></script>
	<script src="{{ asset('/js/swal-forms.js') }}"></script>
	
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