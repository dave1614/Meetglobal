@include('includes/login_header')
<style>  
    form .spinner-border{
    	display: none;
    }
    
</style> 
<script>
	function openSignUpDiv (elem,evt) {
		$("#login-div").hide();
		$("#signup-div").show();
	}

	function openLogInDiv (elem,evt) {
		$("#signup-div").hide();
		$("#login-div").show();
	}

	function changeYourDetails(elem,evt){
		$("#signup-otp-form input").val("");
		// $("#sign-up-form input").val("");
		$("#signup-div").show();
        $("#signup-otp-div").hide();
	}

	function sendSignUpOtpAgain(elem,evt){
		evt.preventDefault();(
		$("#signup-form").submit);
		$("#signup-otp-form input").val("");
		$("#signup-otp-form").find(".spinner-border").show();
        $("#signup-otp-form").find("button").addClass("disabled");
		setTimeout(function () {
			$("#signup-otp-form").find(".spinner-border").hide();
        	$("#signup-otp-form").find("button").removeClass("disabled");
		}, 3000);
	}

	function forgotPass (elem,evt) {
        $("#login-div").hide("fast");
        $("#enter-username-div").show("fast");
    }

    function sendForgotPassOtp(elem,evt,again = false){
		evt.preventDefault();
		if(!again){
			var spinner = $("#enter-username-form").find(".spinner-border");
		    var btn = $("#enter-username-form").find("button");
		}else{
			var spinner = $("#forgot-pass-otp-form").find(".spinner-border");
		    var btn = $("#forgot-pass-otp-form").find("button");
		}
	    var form_data = $("#enter-username-form").serializeArray();
		btn.addClass("disabled");
		btn.css({
			"cursor" : "unset"
		})
        spinner.show();

        var url = "send_forgot_password_otp";
		
		$.ajax({
	        url : url,
	        type : "POST",
	        responseType : "json",
	        dataType : "json",
	        data : form_data,
	        success : function (response) {
	        	console.log(response)
	        	btn.removeClass("disabled");
	        	btn.css({
					"cursor" : "pointer"
				})
        		spinner.hide();
        		if(response.success){
		        	$("#enter-username-div").hide();
                    $("#forgot-pass-otp-div .heading-text").html("An OTP Has Been Sent To The Following Email <br> <small><em class=''>" + global_email + "</em></small>");
                    $("#forgot-pass-otp-div").show();
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
            }
        });
	}
</script> 
<div class="container" style="margin-top: 100px;">
	<div class="row justify-content-center">
		<div class="col-sm-6 card shadow" style="">
			<div class="card-body" style="padding-top: 30px; padding-bottom: 30px;">
				<div class="row justify-content-center">

					
					<div class="col-10 animated fadeInLeftBig " style="" id="login-div">

						<h4 class="text-center heading-text">Log Into Admin Account</h4>
						 
						<form action="/process_sign_in" class="container" id="login-form" method="POST">
						
							@csrf
							<div class="form-group">
								
								<input type="text" class="form-control shadow-sm" id="user_name" name="user_name" placeholder="User Name">
								<span class="form-error"></span>
							</div>
							
							<div class="form-group">
								
								<input type="password" class="form-control shadow-sm" id="password" name="password" placeholder="Password">
								<span class="form-error"></span>
							</div>
							<button class="btn btn-submit btn-block text-center shadow-sm" type="submit">
								Submit 
								<!-- <div class="clearfix"> -->
									<div style="margin-bottom:20px;" class="spinner-border spinner-border-sm float-right" role="status">
									  <span class="sr-only">Loading...</span>
									</div>
								<!-- </div> -->
							</button>
							
						</form>

					</div>

				</div>
			</div>
		</div>
	</div>
	
</div>

<script>
	$(document).ready(function () {
	
		$("#login-form").submit(function (evt) {
			evt.preventDefault();
			var me = $(this);
			var url = me.attr("action");
			var form_data = me.serializeArray();
			var spinner = me.find(".spinner-border");
			var submit_btn = me.find("button");
			console.log(url)
			console.log(form_data)
			spinner.show();
			submit_btn.addClass('disabled');
			submit_btn.css({
				"cursor" : "unset"
			})

			$.ajax({
		        url : url,
		        type : "POST",
		        responseType : "json",
		        dataType : "json",
		        data : form_data,
		        success : function (response) {
		        	spinner.hide();
					submit_btn.removeClass('disabled');
					submit_btn.css({
						"cursor" : "pointer"
					})
	        		if(response.success){
                        var url = response.url;
                        window.location.assign(url);
                    }
                    else if(response.user_exists == false){
                        swal({
                          title: 'Error',
                          text: "This User Does Not Exist",
                          type: 'error',                                          
                        })
                    }else if(response.wrong_password == true){
                        swal({
                          title: 'Error',
                          text: "Wrong Credentials Entered. Try Again",
                          type: 'error',                                          
                        })
                    }else{
                    	$.each(response.messages, function (key,value) {

			              var element = $('#'+key);
			              
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
	            	spinner.hide();
					submit_btn.removeClass('disabled');
					submit_btn.css({
						"cursor" : "pointer"
					})
	            	swal({
	                    title: 'Ooops',
	                    text: "Something Went Wrong. Please Check Your Internet Connection",
	                    type: 'error',						                  
	                })
	            }
	        });

		})

	})
</script>

@include('includes/login_footer')