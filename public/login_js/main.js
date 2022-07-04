
(function ($) {
    "use strict";
    var url1 = "";
    var submit_btn;
    var submit_btn_spinner;

    

    function copyText(text) {
        /* Get the text field */
        var elem = document.createElement("textarea");
        elem.value = text;
        document.body.append(elem);

        /* Select the text field */
        elem.select();
        /* Copy the text inside the text field */
        if(document.execCommand("copy")){
          $.notify({
          message:"Copied!"
          },{
            type : "success"  
          });
        }

        document.body.removeChild(elem);

        /* Alert the copied text */
    }
    

    function signUpCallback(response) {
        console.log(response);
        if(response.status === "PARTIALLY_AUTHENTICATED") {
            window.scrollTo(0,document.body.scrollHeight);
            var code = response.code;
            var state = response.state;
            console.log(code)
            var form_data = {
                code : code,
                state : state
            }
            console.log(form_data)
            submit_btn_spinner.show();
            submit_btn.addClass("disabled");

            $.ajax({
                url : url1,
                type : "POST",
                responseType : "json",
                dataType : "json",
                data : form_data,
                success : function (response) {
                    submit_btn_spinner.hide();
                    submit_btn.removeClass("disabled");
                    console.log(response)
                    if(response.success && response.url != ""){
                        var url = response.url;
                        // var user_name = response.user_name;
                        window.location.assign(url);
                    }else{
                         $.notify({
                          message:"Sorry Something Went Wrong."
                          },{
                            type : "warning"  
                          });
                    }
                },error : function (jqXHR,error, errorThrown) {
                    submit_btn_spinner.hide();
                    submit_btn.removeClass("disabled");
                    $.notify({
                      message:"Sorry Something Went Wrong."
                      },{
                        type : "danger"  
                      });
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
    
    /*==================================================================
    [ Validate ]*/
    
    $('.validate-form').on('submit',function(evt){
        evt.preventDefault();
        window.scrollTo(0,document.body.scrollHeight);
        var check = true;
        var input = $(this).find('.validate-input .input100');
        var id = $(this).attr("id");
        for(var i=0; i<input.length; i++) {
            var error = validate(input[i]);
            if(error !== ""){
                showValidate(input[i],error);
                check=false;
            }else{
                hideValidate(input[i])
            }
        }

        if(check){
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
                    spinner.hide();
                    btn.removeClass("disabled");
                    
                    if(id == "login-form"){
                        if(response.success ){
                            document.location.assign(response.url);
                        }else if(response.half_registered){
                            var url = response.url;
                            var user_name = response.user_name;
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
                        }
                        else{
                           $.each(response.messages, function (key,value) {

                            var element = $('#'+key);
                            if(value != ""){
                                showValidate(element,value);
                            }
                           });
                            
                        }
                        
                    }else if(id == "sign-up-form"){
                        if(response.success ){
                            
                        
                            url1 = response.url;
                            var code = "+" + response.code;
                            var phone_number = response.phone_number;
                            var email = response.email;
                            
                            submit_btn = btn;
                            submit_btn_spinner = spinner;

                            $("#sign-up-form").hide();
                            $("#signup-otp-form .login100-form-title").html("OTP Sent To <br> <small><em class='text-primary'>" + email + "</em></small>");
                            $("#signup-otp-form").show();


                            // AccountKit.login('PHONE', {
                            //   countryCode: code, phoneNumber: phone_number
                            // }, // will use default values if this is not specified
                            //   signUpCallback);
                            // $.ajax({
                            //     url : url1,
                            //     type : "POST",
                            //     responseType : "json",
                            //     dataType : "json",
                            //     data : {},
                            //     success : function (response) {
                            //         submit_btn_spinner.hide();
                            //         submit_btn.removeClass("disabled");
                            //         console.log(response)
                            //         if(response.success && response.url != ""){
                            //             var url = response.url;
                            //             // var user_name = response.user_name;
                            //             window.location.assign(url);
                            //         }else{
                            //              $.notify({
                            //               message:"Sorry Something Went Wrong."
                            //               },{
                            //                 type : "warning"  
                            //               });
                            //         }
                            //     },error : function (jqXHR,error, errorThrown) {
                            //         submit_btn_spinner.hide();
                            //         submit_btn.removeClass("disabled");
                            //         $.notify({
                            //           message:"Sorry Something Went Wrong."
                            //           },{
                            //             type : "danger"  
                            //           });
                            //     }
                            // });
                        }else if(response.half_registered){
                            var url = response.url;
                            var user_name = response.user_name;
                            window.location.assign(url);
                        }else if(response.phone_used){
                            $.notify({
                              message:"This Phone Number Has Already Been Used."
                              },{
                                type : "warning"  
                            });
                        }else{
                           $.each(response.messages, function (key,value) {

                            var element = $('#'+key);
                            if(value != ""){
                                showValidate(element,value);
                            }
                           });
                            
                        }  
                    }else if(id == "signup-otp-form"){
                        
                        if(response.success){
                            var url = response.url;
                            window.location.assign(url);
                        }else{
                            // console.log('test');
                            if(response.expired){
                                $.notify({
                                  message:"This OTP Has Expired. Please Request Another One."
                                  },{
                                    type : "warning"  
                                });
                            }else if(response.sponsor_does_not_exist){
                                swal({
                                  title: 'Error',
                                  text: "The Sponsor Username Does Not Exist. Go Back And Enter Another Sponsor.",
                                  type: 'error',                                          
                                })
                            }else if(response.incorrect_otp){
                                $.notify({
                                  message:"This OTP Entered Is Incorrect. Please Enter The Valid One"
                                  },{
                                    type : "warning"  
                                });
                            }else if(response.phone_used){
                                $.notify({
                                  message:"This Phone Number Has Already Been Used."
                                  },{
                                    type : "warning"  
                                });
                            }
                        }

                        // AccountKit.login('PHONE', {
                        //   countryCode: code, phoneNumber: phone_number
                        // }, // will use default values if this is not specified
                        //   signUpCallback);
                        // $.ajax({
                        //     url : url1,
                        //     type : "POST",
                        //     responseType : "json",
                        //     dataType : "json",
                        //     data : {},
                        //     success : function (response) {
                        //         submit_btn_spinner.hide();
                        //         submit_btn.removeClass("disabled");
                        //         console.log(response)
                        //         if(response.success && response.url != ""){
                        //             var url = response.url;
                        //             // var user_name = response.user_name;
                        //             window.location.assign(url);
                        //         }else{
                        //              $.notify({
                        //               message:"Sorry Something Went Wrong."
                        //               },{
                        //                 type : "warning"  
                        //               });
                        //         }
                        //     },error : function (jqXHR,error, errorThrown) {
                        //         submit_btn_spinner.hide();
                        //         submit_btn.removeClass("disabled");
                        //         $.notify({
                        //           message:"Sorry Something Went Wrong."
                        //           },{
                        //             type : "danger"  
                        //           });
                        //     }
                        // });
                    }else if(id == "forgot-pass-otp-form"){
                        if(response.success ){
                            var user_name = response.user_name;
                            submit_btn = btn;
                            submit_btn_spinner = spinner;

                            $("#forgot-pass-otp-form").hide();
                            $("#enter-new-password-form .login100-form-title").html("Enter New Password For Username <br> <small><em class='text-primary'>" + user_name + "</em></small>");
                            $("#enter-new-password-form").show();

                        }else if(response.expired){
                            $.notify({
                              message:"It Seems The Requested OTP Has Expired. Please Request A New One"
                              },{
                                type : "warning"
                            });
                        }else if(response.otp_not_present){
                            $.notify({
                              message:"The OTP Field Is Required."
                              },{
                                type : "warning"  
                            });
                        }else if(response.incorrect_otp){
                            $.notify({
                              message:"The OTP Entered Is Incorrect. Please Enter The Valid One."
                              },{
                                type : "warning"  
                            });
                        }else{
                           $.notify({
                              message:"Something Went Wrong."
                              },{
                                type : "warning"  
                            });
                            
                        }  
                    }else if(id == "enter-new-password-form"){
                        if(response.success ){
                            var url = response.url;

                            window.location.assign(url);
                        }else if(response.expired){
                            $.notify({
                              message:"It Seems The Requested OTP Has Expired. Please Request A New One"
                              },{
                                type : "warning"
                            });
                        }else if(response.new_password_absent){
                            $.notify({
                              message:"The New Password Is Required."
                              },{
                                type : "warning"  
                            });
                        }else{
                           $.notify({
                              message:"Something Went Wrong."
                              },{
                                type : "warning"  
                            });
                        }  
                    }    
                    
                },
                error : function () {
                  spinner.hide();
                  btn.removeClass("disabled");
                  $.notify({
                  message:"Sorry Something Went Wrong. Please Check Your Internet Connection And Try Again"
                  },{
                    type : "warning"  
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
    });


    $('.validate-form .input100').each(function(){
        $(this).focus(function(){
           hideValidate(this);
        });
    });

    function validate (input) {
        var placeholder = $(input).attr("placeholder");
        if($(input).val().trim() == ''){
            if($(input).attr("id") != "phone"){
                return "The " + placeholder + " Field Is Required";
            }else{
                return "The Mobile Number Field Is Required";
            }
        }else if($(input).attr('name') == 'otp_input') {
            if($(input).val().length != 5) {
                return "OTP Must Be 5 Characters";
            }else{
                return "";
            }
        }else if($(input).attr('name') == 'new_password') {
            if($(input).val().length < 5) {
                return "New Password Must Be At Least 5 Characters";
            }else{
                return "";
            }
        }else if($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
            if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                return "Email Should Be Valid e.g mgr@meetglobalresources.com";
            }else{
                return "";
            }
        }else if($(input).attr('name') == 'user_name_sign_up') {
            if($(input).val().length < 5 || $(input).val().length > 40) {
                return "Username Must Be Between 5 - 40 Characters";
            }else{
                return "";
            }
        }else if($(input).attr('name') == 'user_name_login') {
            if($(input).val().length > 40) {
                return "Username Must Not Be Above 40 Characters";
            }else{
                return "";
            }
        }else if($(input).attr('name') == 'password_login' || $(input).attr('name') == 'password_sign_up'){
            if($(input).val().length < 5) {
                return "Password must be at least 5 characters";
                return "";
            }else{
                return "";
            }
        }else if($(input).attr('name') == 'phone'){
            if($(input).val().length > 15) {
                return "Phone Number Must Be Maximum Of 15 Characters";
            }else{
                return "";
            }
        }else if($(input).attr('name') == 'full_name'){
            if($(input).val().length < 5 || $(input).val().length > 100) {
                return "Full Name Must Be Between 5 - 100 Characters";
            }else{
                return "";
            }
        }else {
            return "";
        }
    }

    function showValidate(input,error) {
        // console.log($(input).parent())
        var thisAlert = $(input).parent();
        // console.log(thisAlert)
        $(thisAlert).attr({
            'data-validate' : error
        });
        $(thisAlert).addClass('alert-validate');
        
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();
        // console.log(thisAlert)
        $(thisAlert).attr({
            'data-validate' : ""
        });
        $(thisAlert).removeClass('alert-validate');
    }
    
    

})(jQuery);