<!DOCTYPE html>
<html>
<head>
  <title>Welcome To Meetglobal Resources</title>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('/images/logo-img.jpeg') }}">
  <link rel="icon" type="image/png" href="{{ asset('/images/logo-img.jpeg') }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- <link rel="preload" href="https://cdn.shareaholic.net/assets/pub/shareaholic.js" as="script" />
  <meta name="shareaholic:site_id" content="5867ee1e631cfcddacf637057c0c658b" />
  <script data-cfasync="false" async src="https://cdn.shareaholic.net/assets/pub/shareaholic.js"></script> -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">
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
  <link href="https://fonts.googleapis.com/css?family=Questrial&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{ asset('/css/main_page.css?v=1.1') }}">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <script data-ad-client="ca-pub-4869278735732619" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
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
<body class="animated fadeInLeft">
  <nav class="navbar navbar-expand-lg navbar-light static-top mb-5 shadow animated bounceInDown" style="background-color: transparent;">
    <div class="container">
      <a class="navbar-brand" href="#"><img src="{{ asset('/images/logo-img.jpeg') }}" style="width: 50px; height: 50px; border-radius: 50%;" alt=""></a>
      <button class="navbar-toggler navbar-dark" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon " style=""></span>
          </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active">
            <a class="nav-link" style="" href="#">Home
                  <span class="sr-only">(current)</span>
                </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" style="" href="#about-us">About Us</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" style="" href="#services">Our Services</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" style="" href="#contact">Contact Us</a>
          </li>
          <?php if(!$logged_in){ ?>
            <li class="nav-item">
              <a class="nav-link text-primary" style="" href="/login_page">Sign Up / Log In</a>
            </li>
          <?php } ?>  
        </ul>
      </div>
    </div>
  </nav>

<!-- Page Content -->
  <div class="container">
    <div class="card border-0 shadow my-5" style="background: transparent;">
      <div class="card-body p-5" style="margin-top: 56px;">
        <h1 class="font-weight-light animated fadeInLeft head">Meet Global Resources</h1>
        <p class="lead animated fadeInLeft">Global Resources At Your Reach!</p>
        <button class="btn-orange text-center" id="createAccountBtn">Create Your Account</button>
        <!-- <p class="lead">Scroll down...</p> -->
        <!-- <div style="height: 700px"></div> -->
        
      </div>
    </div>

    <section id="about-us" style="margin-top: 100px;">

      <h2 class="text-center sub-head" style="">About Us</h2>
      <div class="card border-0 shadow my-5" style="background: transparent;">
        <div class="card-body p-5">
          <p class="lead animated fadeInLeft section-text"> MGR is a community of "Consumers" and "Producers" of products and services for a mutual symbiotic relationship that leads to a better patronage for the producers and active/passive patronage income for the consumers. <a href="#" onclick="showMoreAboutUs(this,event)">Read More</a></p>

          <p class="lead animated fadeInLeft section-text" style="display: none;"> 

            MGR is a community of "Consumers" and "Producers" of products and services for a mutual symbiotic relationship that leads to a better patronage for the producers and active/passive patronage income for the consumers. <br> <br>

            The community buys quality products and services cheaper because of its community purchasing power and share the trade profit in the community which result to active income for life. Become a beneficiary of the on going global trade and communication system.<a href="#" onclick="showLessAboutUs(this,event)">Read Less</a></p>
        </div>
      </div>

      <h3 class="text-center sub-sub-head" style="">Benefits</h3>
      <div class="card border-0 shadow my-5" style="background: transparent;">
        <div class="card-body p-5">
          <ul>
            <li>Register, Chat Socialise and Advertise.</li>
            <li>Buy and Sell, Export and Import at the cheapest rates.</li>
            <li>Recharge your Airtime/Data, renew your cable subscription and pay NEPA bills.</li>
            <li>Receive health and love tips and suggested solutions.</li>
            <li>Use a working phone number for registration and withdrawal verification.</li>
            <li>Earn and withdraw your income at your own convenience, without admin permission.</li>
            <li>Join and share your invite link to all your friends, start relating with them and start making money.</li>
          </ul>
        </div>
      </div>



    </section>


    <section id="services" style="margin-top: 100px;">

      <h2 class="text-center sub-head" style="">Our Services</h2>
      <div class="card border-0 shadow my-5" style="background: transparent;">
        <div class="card-body p-5">
          <div class="container">
            <div class="row">
              <div class="card border-0 shadow my-5 col-sm-3" id="vtu-card" style="padding: 0;">
                <div class="card-body" style="padding: 0;">
                  <img src="{{ asset('/images/vtu_image.jpg') }}" style="padding: 0;" class="col-12" alt="">
                  <h3 class="text-center" style="text-transform: uppercase; margin-top: 10px; color: #000;">Recharge vtu</h3>
                  <div class="card-body-lower" style="padding: 10px;">
                    <p style="color: #000;">
                    1. Self service: Using our portal to recharge your family and yourself saves a lot of money. <a onclick="readMore(this,event)" href="#">Read More</a>
                    
                    </p>


                    <p style="color: #000; display: none;">
                      1. Self service: Using our portal to recharge your family and yourself saves a lot of money. <br>
                      2. Dealership: You will be making serious money reselling to others using MGR VTU portal. Because you will be selling at dealers rate to others and be making serious profit doing so.<br> <br>

                      Earning Income Projection:

                      <br> <br> <br>

                      If thousands of members in your generation buy airtime or data worth at least N1,000 each month from the system, you will earn N0.21% multiply by the traded volume.

                      <br><br>

                      The MGR community patronage with the Telecoms like MTN, Glo, Airtel and especially 9Mobile give room for this Leverage.
                      So you can register with your existing line from any network, "recharge on the platform", and or get a new 9Mobile sim from MGR and recharge just N1,000 each month to allow you call FREE for a whole month and you use same N1,000 which gives you 152 minutes to browse or call other network at 11k/sec, still you generate continues income for yourself and others month in, month out for life.

                      <br> <br>

                      #Remember that people will never stop recharging their phones, using airtime for calls and data for browsing or watching cable, hence the financial opportunity is endless with MGR community.


                      <a onclick="readLess(this,event)" href="#">Read Less</a>
                    </p>
                  </div>
                </div>
              </div>

              <div class="card border-0 shadow my-5 col-sm-3 offset-sm-1" id="import-card" style="padding: 0;">
                <div class="card-body" style="padding: 0;">
                  <img src="{{ asset('/images/mini_importation.jpg') }}" style="padding: 0;" class="col-12" alt="">
                  <h3 class="text-center" style="text-transform: uppercase; margin-top: 10px; color: #000;">Mini Importation</h3>
                  <div class="card-body-lower" style="padding: 10px;">
                    <p style="color: #000;">
                      
                    &#149; Imagine working with you,  taking you by the hand to start a business you ha <a onclick="readMore(this,event)" href="#">Read More</a>
                    </p>

                    <p style="color: #000; display: none;">
                      
                    &#149; Imagine working with you,  taking you by the hand to start a business you have been thinking of. You want to become an importer-exporter, but you could not achieve it because of the cost implication or because you do not have the supports needed to make it through - What to buy, where to buy from, how and where to sell - might all be hindrances. But guess what - MGR is here to help you achieve all that. <br>
                    &#149; Come to think of it,  MGR share mini-importation opportunity with you and show you how to raise money to start importing if possible the same week of coming in contact with MGR. <br>
                    &#149; There is an opportunity for joint purchases from your back office to meet up with bulk buying which reduces the cost of doing business. <br>
                    &#149; You have the opportunity to make more money as you invite others to join you in the mini-importion business opportunity. <br>
                    &#149; Minimum Profit Projection per Week: <br>
                    Assuming you start Mini importation with just twenty thousand Naira only(N20,000) and import goods worth of N20,000, instead of looking for 100-200% gain, just go for 40% profit. 40% of N20,000 worth of goods = N8,000 per week. N8,000 x 4 = N32,000 per month - after all cost and import expenses removed. This is the minimum income generation and it's not an exaggerated figure. Everyone knows that this is 500% possible. As you increase your purchasing power, you make more profits. <br>
                    &#149; Can you see the gap that MGR is filling in creating business opportunity for you and for many others through you!
                    <a onclick="readLess(this,event)" href="#">Read Less</a>
                      
                    </p>
                  </div>
                </div>
              </div>

              <div class="card border-0 shadow my-5 col-sm-3 offset-sm-1" id="health-card" style="padding: 0;">
                <div class="card-body" style="padding: 0;">
                  <img src="{{ asset('/images/health_img.jpg') }}" style="padding: 0;" class="col-12" alt="">
                  <h3 class="text-center" style="text-transform: uppercase; margin-top: 10px; color: #000;">Health</h3>
                  <div class="card-body-lower" style="padding: 10px;">
                    <p style="color: #000;">
                      
                    Ones health is as important as his life. Good health plays one of the major roles in the actualisation of one's dream, greater productivity, <a onclick="readMore(this,event)" href="#">Read More</a>
                    </p>

                    <p style="color: #000; display: none;">
                      
                    Ones health is as important as his life. Good health plays one of the major roles in the actualisation of one's dream, greater productivity, greater joy and greater savings. <br><br>

                    We will from time to time post some health related issues and suggested solutions. Users too are also allowed to give their own solutions through chats with other members. Some big names and companies in the health industry will also be part of this platform.<br><br>

                    NB: Acceptance of any health prescription/solution is at each members discretion.<br><br>

                    Click on here to join the health forum.
                    <a onclick="readLess(this,event)" href="#">Read Less</a>
                      
                    </p>


                  </div>
                </div>
              </div>

              <div class="card border-0 shadow my-5 col-sm-3" id="e-commerce-card" style="padding: 0; ">
                <div class="card-body" style="padding: 0;">
                  <img src="{{ asset('/images/ecommerce-img.jpg') }}" style="padding: 0;" class="col-12" alt="">
                  <h3 class="text-center" style="text-transform: uppercase; margin-top: 10px; color: #000;">E-commerce</h3>
                  <div class="card-body-lower" style="padding: 10px;">
                    <p style="color: #000;">
                    E-Business <br> <br>
                    Innovations in technology and communications have allowed businesses to operate globally like never. <a onclick="readMore(this,event)" href="#">Read More</a>
                    
                    </p>
                    <p style="color: #000; display: none;">
                    E-Business <br> <br>
                    Innovations in technology and communications have allowed businesses to operate globally like never before. In the past, communications could take days if not weeks; now all business transactions can take place in only minutes.  
                    This free-to-use online marketplace allows you to purchase from multiple suppliers.

                    MGR brings together whole lots of other commercial platforms like Jumia, Jiji, Amazon, Alibaba, 1688 etc in order to make exchange of goods and services quite easier, greater speed, lesser time, etc, all for the satisfaction of our numerous members. <a onclick="readLess(this,event)" href="#">Read Less</a>
                    </p>
                  </div>
                </div>
              </div>

              <div class="card border-0 shadow my-5 col-sm-3 offset-sm-1" id="compensation-card" style="padding: 0;">
                <div class="card-body" style="padding: 0;">
                  <img src="{{ asset('/images/mlm-image.jpg') }}" style="padding: 0;" class="col-12" alt="">
                  <h3 class="text-center" style="text-transform: uppercase; margin-top: 10px; color: #000;">PARTNERS INCOME PLAN AND BENEFITS</h3>
                  <div class="card-body-lower" style="padding: 10px;">
                    <div>
                    
                      <p style="color: #000;">As MGR community partner, your dream to own a market - a system where real life forces of demand and supply operates - is guaranteed. You earn loyalty and income from your existing and potential customers whose need to trade their choices' quality of goods and services and improved deliveries at a lower cost are sat
                        <a href="#" onclick="showMore(this,event)">Read More</a></p>
                    </div>
                    
                    <div style="color: #000; display: none;">
                      <p>
                      <p style="color: #000;">As MGR community partner, your dream to own a market - a system where real life forces of demand and supply operates - is guaranteed. You earn loyalty and income from your existing and potential customers whose need to trade their choices' quality of goods and services and improved deliveries at a lower cost are satisfied.
                       <br><br>

                      The community buys quality products and services cheaper because of its community purchasing power and share the trade profit in the community which result to active/passive income for life. <br> <br>
                      The MGR community patronage with the Telecoms like MTN, Glo, Airtel and especially 9Mobile give room for this Leverage.
                      So you can register with your existing line from any network, "recharge on the platform", and or get a new 9Mobile sim from MGR and recharge just N1,000 each month to allow you call FREE for a whole month and you use same N1,000 which gives you 152 minutes to browse or call other network at 11k/sec, still you generate continues income for yourself and others month in, month out for life. <br><br>


                      <h4 style="color: #000;" class="text-center">Earning Channels</h4>
                      
                      <p style="margin-top: 10px; color: #000;">1. Sponsor income N700. <br><br>                                           
                      2. Placement income N150 × 16th generation(over N19,000,000 per account). <br><br>
                      3. Trade Income N0.21%<br><br>
                      4. Leadership income, N0.50%
                      </p>

                      <h4 style="color: #000;" class="text-center">AWARDS.</h4>

                      <p style="margin-top: 10px; color: #000;">1. Trip Award worth N200k with active 10,000 downlines from either left or right. <br><br>
                      2. 1st Car Award worth N2.5M with 15,000 active downlines from either left or right.<br><br>
                      3. 2nd Car Award  worth N5.2M with 40,000 Down lines. {20,000 left and 20,000 right} <br><br>
                      iv. You earns N1,500 sponsor bonus when you refer Business Partners.</p>

                      <h4 style="color: #000;" class="text-center">POTENTIAL INCOME STREAM DURATION.</h4>

                      <p style="margin-top: 10px; color: #000;">As a member of the community, assumed... <br> <br>
                        1st week.....U invite 2 <br>
                        2nd week....2 invite 4  <br>
                        3rd week.....4 invite 8 <br>
                        4th week.....8 invite 16 <br>
                        5th week...16 invite 32 <br>
                        6th week...32 invite 64 Etc.....
                      </p>


                      <p style="margin-top: 10px; color: #000;">
                        If it takes you one whole week to invite two persons, and others, each week to invite two persons each to join the community.....
                        Within 16 weeks...the system would have earned you over N19 Million from Sponsorship, Placement and Trade income. The system creates you Active and Passive Income.
                        You withdraw at anytime any day straight to your account. You don't need permission.
                        
                        <br><br>
                        Let's Get Started!!!
                        <a href="#" onclick="showLess(this,event)">Read Less</a>
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card border-0 shadow my-5 col-sm-3 offset-sm-1" id="love-affairs-card" style="padding: 0;">
                <div class="card-body" style="padding: 0;">
                  <img src="{{ asset('/images/love-img.jpg') }}" style="padding: 0;" class="col-12" alt="">
                  <h3 class="text-center" style="text-transform: uppercase; margin-top: 10px; color: #000;">Love , Sex And Relationship</h3>

                  <div class="card-body-lower" style="padding: 10px;">
                    <p style="color: #000; ">Our Love, Sex and Relationship Matters page aims to have an open, honest, and non-judgmental attitude towards sex, love and relationship. <a href="#" onclick="readMore(this,event)">Read More</a></p>

                    <p style="color: #000; display: none;">Our Love, Sex and Relationship Matters page aims to have an open, honest, and non-judgmental attitude towards sex, love and relationship. We balance this approach by working closely with other partners to make sure our content is culturally appropriate and does not cause offense. <br><br><br>

                    We see sex, love and relationship as a wonderful thing to explore, share, and enjoy. We think that if you provide young people with honest and positive information and news on sex, love and relationship, they'll be more likely to have safer, healthier sex, love and relationship and maintain a healthy relationship as they grow and engage in one. We therefore provides easy-to-access information, news, chat, questions and answers forum on sex, sexual, love and relationship health for young and adults around the world. <a href="#" onclick="readLess(this,event)">Read Less</a></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </section>

    <section id="contact" style="margin-top: 100px;">

      <h2 class="text-center sub-head" style="">Contact Us</h2>
      <div class="card border-0 shadow my-5" style="background: transparent;">
        <div class="card-body p-5 row">
          <div class="col-sm-6">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.9072138157712!2d3.2185126176460983!3d6.533401790708362!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103b850293bcbf9b%3A0xfe6207d4e84e8328!2sAdexson!5e0!3m2!1sen!2sng!4v1569059020642!5m2!1sen!2sng" width="400" height="300" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
          </div>

          <div class="col-sm-6">
            <a target="_blank" href="https://instagram.com/meetglobalresources">
              <div class="row">
              
                <div class="col-2 text-right" style="padding: 0;">
                  <i class="fab fa-instagram" style="font-size: 21px; color: white;"></i> 
                </div>

                <div class="col-10" style="color: white;">
                  meetglobalresources
                </div>
              
              </div>
            </a>

            <a target="_blank" href="https://instagram.com/meetglobalresources">
              <div class="row" style="margin-top: 13px">
              
                <div class="col-2 text-right" style="padding: 0;">
                  <i class="fab fa-twitter" style="font-size: 21px; color: white;"></i> 
                </div>

                <div class="col-10" style="color: white;">
                  MeetglobalR
                </div>
              
              </div>
            </a>

            <a target="_blank" href="https://web.facebook.com/MGR-345096286158334/?__tn__=kCH-R&eid=ARDGmEiAIBL6rK6ie3wpfAEgZILDZk2mw5yNddttctHlXMXo2xXcq4J_D7j08H5rWRZRsk3JoLB6yMcw&hc_ref=ARSwy0U1vsR_KSUscFc1tFUpj7PetF0b3dGKtyKCCKosQN-Z_U9lgiELGZGCyubk6Uw&__xts__[0]=68.ARCbjZi_6Qu_KCfM3L2kQ17qiXGLNTLcI9gFpdgGIuBckgeNPTHmJVEHDQTStQQ1wmRODU_yCB55tujDLWUz3-tUFFNCJSVFAuSPtf6ajuxlwx4ztDsZOIie6uxKsfdR-oaI3OLroFhge4UbxhkT_TuZ722S9WWoX-qhAH3-Wq6qG1lwYYGkcZCWkLC0lS8GtxUgIywDUspLbrhQueZXBClxrsabro0YgWriDeWTnS92gEp8nlPLvAW5NUYxSkJ533sGjB0XNaNqru5iyOK5DLdbf27USv9vRhy0Fb9o1HXushylOX1Mq1UtBWyOiKcTzxFQr3gd7WtI_u0">
              <div class="row" style="margin-top: 13px">
              
                <div class="col-2 text-right" style="padding: 0;">
                  <i class="fab fa-facebook-f" style="font-size: 21px; color: white;"></i> 
                </div>

                <div class="col-10" style="color: white;">
                  meetglobal_resources
                </div>
              
              </div>
            </a>

            <a target="_blank" href="tel:+08181006998 ">
              <div class="row" style="margin-top: 13px">
              
                <div class="col-2 text-right" style="padding: 0;">
                  <i class="fas fa-phone" style="font-size: 21px; color: white;"></i> 
                </div>

                <div class="col-10" style="color: white;">
                  08181006998, 08060505093
                </div>
              
              </div>
            </a>

            <div class="send-us-message row" style="margin-top: 40px;">
              <div class="col-1"></div>
              <div class="col-11" style="">
                <h4 style="font-weight: bold;">Send Us A Message</h4>
                
                <form action="process_send_message" method="POST" id="send-message-form">
                   @csrf
                  <div class="form-group">
                    <textarea placeholder="Type Something Here......" name="message" id="message" cols="30" rows="10" class="form-control"></textarea>
                    <span class="form-error"></span>
                  </div>
                  <div class="form-group">
                    <input type="text" name="name" placeholder="Enter Your Name" id="name" class="form-control" >
                    <span class="form-error"></span>
                  </div>
                  <div class="form-group">
                    <input type="number" name="mobile_number" id="mobile_number" placeholder="Enter Your Mobile Number" class="form-control" >
                    <span class="form-error"></span>
                  </div>
                  <button type="submit" class="btn-orange">Submit <img src="{{ asset('/images/ajax-loader.gif') }}"  class="spinner"></button>
                </form>
              </div>

            </div>
          </div>
        </div>
      </div>

      

    </section>
  </div>

  <div class="modal fade" data-backdrop="static" id="login-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title text-center" style="color: #000;">Create Your Free Account</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="modal-body">
          
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal" >Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" data-backdrop="static" id="love-affairs-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title text-center" style="color: #000;">LOVE , SEX AND RELATIONSHIP</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="modal-body">
          
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal" >Close</button>
        </div>
      </div>
    </div>
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js"></script>
  <script src="{{ asset('/js/bootstrap-notify.js')}} "></script>
  <script src="{{ asset('/js/sweetalert2.all.min.js') }}"></script>
  <script src="{{ asset('/js/sweetalert2.min.js') }}"></script>
  <script src="{{ asset('/js/swal-forms.js') }}"></script>

  <script>
    $(document).ready(function () {
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
                setTimeout(function () {
                  document.location.reload()
                }, 1500)
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
      });
      // showLogInModal();
      <?php if(!$logged_in){ ?>
        // setInterval(showLogInModal, 300000);
      <?php } ?>
    })

    $("#createAccountBtn").click(function (evt) {
      document.location.assign("<?php echo '/login_page' ?>");
    })
  </script>
  <script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5f340c284c7806354da5d9a6/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
  
  
</body>

</html>