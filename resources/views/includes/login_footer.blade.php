<footer id="main-page-footer" class="shadow">
    

    <p class="text-center" style="color: #9c27b0; margin-bottom: 0;">&copy; <?php echo date("Y"); ?>, All rights reserved. Olive Care Staffing Services Inc.</p>
    
</footer>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
   
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  <script src="{{ asset('/js/bootstrap-notify.js') }} "></script>
  <script src="{{ asset('/js/sweetalert2.all.min.js') }}"></script>
  <script src="{{ asset('/js/sweetalert2.min.js') }}"></script>
  <script src="{{ asset('/js/swal-forms.js') }}"></script>
  <script src="{{ asset('/js/owl.carousel.min.js') }}"></script>

  <script>
    $(document).ready(function () {

      var owl = $('.owl-carousel');
      owl.owlCarousel({
          items:1,
          loop:true,
          margin:10,
          autoplay:true,
          autoplayTimeout:5000,
          autoplayHoverPause:true,
          // singleItem:true
      });
      // $('.play').on('click',function(){
      //     owl.trigger('play.owl.autoplay',[1000])
      // })
      // $('.stop').on('click',function(){
      //     owl.trigger('stop.owl.autoplay')
      // })

      $("#send-message-form").submit(function(evt) {
        evt.preventDefault();
        var me = $(this);
        var form_data = $(this).serializeArray();
        var url = $(this).attr("action");
        var submit_btn1 = $(this).find("button");
        var submit_btn_spinner1 = $(this).find(".spinner");
        submit_btn1.addClass('disabled');
        submit_btn_spinner1.show();
        $.ajax({
            url : url,
            type : "POST",
            responseType : "json",
            dataType : "json",
            data : form_data,
            success : function (response) {
              submit_btn_spinner1.hide();
              submit_btn1.removeClass("disabled");
              console.log(response)
              if(response.success){
                me.find("input").val("");
                me.find("textarea").val("");
                me.find("form-error").html("");
                $.notify({
                  message:"Message Sent Successfully."
                  },{
                    type : "success"  
                });
              }else{
                $.each(response.messages, function (key,value) {

                  var element = $('#'+key);
                  
                  element.closest('div.form-group')
                          
                          .find('.form-error').remove();
                  element.after(value);
                  
                 });
                $.notify({
                  message:"Sorry Something Went Wrong."
                  },{
                    type : "warning"  
                });
              }
            },error : function (jqXHR,error, errorThrown) {
              submit_btn_spinner1.hide();
              submit_btn1.removeClass("disabled");
              $.notify({
              message:"Sorry Something Went Wrong."
              },{
                type : "danger"  
              });
            }
        });  
      });
      // showLogInModal();
      
    })

   
  </script>
  
</body>

</html>