<style>
  tbody tr{
    /*cursor: pointer;*/
  }
  .frame-area {
     display: block;
    width: 100%;
    /* max-width: 400px; */
    height: 1000px;
    /* overflow: auto; */
    border: #999999 1px solid;
    margin: 0px;
    padding: 0px;
  }
  .name h6 {
    margin-top: 10px;
    margin-bottom: 10px;
  }


  .profile-page .page-header {
      height: 100px;
      background-position:center;
  }

  .page-header {
      /*height: 100vh;*/
      height: 300px;
      min-height: 200px;
      background-size: cover;
      margin: 0;
      padding: 0;
      border: 0;
      display: flex;
      align-items: center;
  }

  .header-filter:after, .header-filter:before {
      position: absolute;
      z-index: 1;
      width: 100%;
      height: 100%;
      display: block;
      left: 0;
      top: 0;
      content: "";
  }

  .header-filter::before {
      background: rgba(0,0,0,.5);
  }

  .main-raised {
      margin: -60px 30px 0;
      border-radius: 6px;
      box-shadow: 0 16px 24px 2px rgba(0,0,0,.14), 0 6px 30px 5px rgba(0,0,0,.12), 0 8px 10px -5px rgba(0,0,0,.2);
  }

  .main {
      background: #e6ecf0;
      position: relative;
      z-index: 3;
  }

  .profile-page .profile {
      text-align: center;
  }

  .profile-page .profile img {
      max-width: 160px;
      width: 100%;
      margin: 0 auto;
      -webkit-transform: translate3d(0,-50%,0);
      -moz-transform: translate3d(0,-50%,0);
      -o-transform: translate3d(0,-50%,0);
      -ms-transform: translate3d(0,-50%,0);
      transform: translate3d(0,-50%,0);
  }

  .img-raised {
      box-shadow: 0 5px 15px -8px rgba(0,0,0,.24), 0 8px 10px -5px rgba(0,0,0,.2);
  }

  .rounded-circle {
      border-radius: 50%!important;
  }

  .img-fluid, .img-thumbnail {
      max-width: 100%;
      height: auto;
  }

  .title {
      margin-top: 30px;
      margin-bottom: 25px;
      min-height: 32px;
      color: #3C4858;
      font-weight: 700;
      font-family: "Roboto Slab","Times New Roman",serif;
  }

  .profile-page .description {
      margin: 1.071rem auto 0;
      max-width: 600px;
      color: #999;
      font-weight: 300;
  }

  p {
      font-size: 14px;
      margin: 0 0 10px;
  }

  .profile-page .profile-tabs {
      margin-top: 4.284rem;
  }

  .nav-pills, .nav-tabs {
      border: 0;
      border-radius: 3px;
      padding: 0 15px;
  }

  .nav .nav-item {
      position: relative;
      margin: 0 2px;
  }

  .nav-pills.nav-pills-icons .nav-item .nav-link {
      border-radius: 4px;
  }

  .nav-pills .nav-item .nav-link.active {
      color: #fff;
      background-color: #9c27b0;
      box-shadow: 0 5px 20px 0 rgba(0,0,0,.2), 0 13px 24px -11px rgba(156,39,176,.6);
  }

  .nav-pills .nav-item .nav-link {
      line-height: 24px;
      font-size: 12px;
      font-weight: 500;
      min-width: 100px;
      color: #555;
      transition: all .3s;
      border-radius: 30px;
      padding: 10px 15px;
      text-align: center;
  }

  .nav-pills .nav-item .nav-link:not(.active):hover {
      background-color: rgba(200,200,200,.2);
  }


  .nav-pills .nav-item i {
      display: block;
      font-size: 30px;
      padding: 15px 0;
  }

  .tab-space {
      padding: 20px 0 50px;
  }

  .profile-page .gallery {
      margin-top: 3.213rem;
      padding-bottom: 50px;
  }

  .profile-page .gallery img {
      width: 100%;
      margin-bottom: 2.142rem;
  }

  .profile-page .profile .name{
      margin-top: -80px;
  }

  img.rounded {
      border-radius: 6px!important;
  }

  .tab-content>.active {
      display: block;
  }

  /*buttons*/
  .btn {
      position: relative;
      padding: 12px 30px;
      margin: .3125rem 1px;
      font-size: .75rem;
      font-weight: 400;
      line-height: 1.428571;
      text-decoration: none;
      text-transform: uppercase;
      letter-spacing: 0;
      border: 0;
      border-radius: .2rem;
      outline: 0;
      transition: box-shadow .2s cubic-bezier(.4,0,1,1),background-color .2s cubic-bezier(.4,0,.2,1);
      will-change: box-shadow,transform;
  }

  .btn.btn-just-icon {
      font-size: 20px;
      height: 41px;
      min-width: 41px;
      width: 41px;
      padding: 0;
      overflow: hidden;
      position: relative;
      line-height: 41px;
  }

  .btn.btn-just-icon fa{
      margin-top: 0;
      position: absolute;
      width: 100%;
      transform: none;
      left: 0;
      top: 0;
      height: 100%;
      line-height: 41px;
      font-size: 20px;
  }
  .btn.btn-link{
      background-color: transparent;
      color: #999;
  }

  /* dropdown */




  .dropdown-menu {
      position: absolute;
      top: 100%;
      left: 0;
      z-index: 1000;
      float: left;
      min-width: 11rem !important;
      margin: .125rem 0 0;
      font-size: 1rem;
      color: #212529;
      text-align: left;
      background-color: #fff;
      background-clip: padding-box;
      border-radius: .25rem;
      transition: transform .3s cubic-bezier(.4,0,.2,1),opacity .2s cubic-bezier(.4,0,.2,1);
  }

  .dropdown-menu.show{
      transition: transform .3s cubic-bezier(.4,0,.2,1),opacity .2s cubic-bezier(.4,0,.2,1);
  }


  .dropdown-menu .dropdown-item:focus, .dropdown-menu .dropdown-item:hover, .dropdown-menu a:active, .dropdown-menu a:focus, .dropdown-menu a:hover {
      box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(156,39,176,.4);
      background-color: #9c27b0;
      color: #FFF;
  }
  .show .dropdown-toggle:after {
      transform: rotate(180deg);
  }

  .dropdown-toggle:after {
      will-change: transform;
      transition: transform .15s linear;
  }

  body.modal-open {
      overflow: hidden;
  }

  .dropdown-menu .dropdown-item, .dropdown-menu li>a {
      position: relative;
      width: auto;
      display: flex;
      flex-flow: nowrap;
      align-items: center;
      color: #333;
      font-weight: 400;
      text-decoration: none;
      font-size: .8125rem;
      border-radius: .125rem;
      margin: 0 .3125rem;
      transition: all .15s linear;
      min-width: 7rem;
      padding: 0.625rem 1.25rem;
      min-height: 1rem !important;
      overflow: hidden;
      line-height: 1.428571;
      text-overflow: ellipsis;
      word-wrap: break-word;
  }

  .dropdown-menu.dropdown-with-icons .dropdown-item {
      padding: .75rem 1.25rem .75rem .75rem;
  }

  .dropdown-menu.dropdown-with-icons .dropdown-item .material-icons {
      vertical-align: middle;
      font-size: 24px;
      position: relative;
      margin-top: -4px;
      top: 1px;
      margin-right: 12px;
      opacity: .5;
  }

  .profile .avatar img{
      -webkit-transform: translate3d(0,-50%,0);
      transform: translate3d(0,-50%,0); 
  }
  .name h3{
    margin-top: 0;
  }
  .logo-link-cont{
    cursor: pointer;
  }
  .add-cover-photo-div{
    cursor: pointer;
    position: absolute;
    border: 2px solid white;
    padding: 5px;
    z-index: 100;
    top: 7px;
    left: 10px;
    color: white;

  }

  .add-friend-div{
    cursor: pointer;
    position: absolute;
    border: 2px solid white;
    padding: 5px;
    z-index: 100;
    top: 7px;
    left: 10px;
    color: white;

  }

  .add-message-div{
    cursor: pointer;
    position: absolute;
    border: 2px solid white;
    padding: 5px;
    z-index: 100;
    top: 7px;
    right: 10px;
    color: white;

  }

  #registeredPatientsModal .modal-body{
    height: 400px;
    overflow-y: auto;
  }
  .dp-thumb{
    width: 30px;
    height: 30px;
    border-radius: 50%;
  }  

  .small-loader{
    width: 30px;
    height: 30px;
    display: none;
  }
  .profile-amt{
    /*color: darkgrey;*/
    font-size: 22px;
    font-weight: 800;
  }

  .small-cover-img{
    height: 150px;
    margin-top: -79px;
  }
  .card-avatar{
    z-index: 1;
    margin-right: 113px;
  }
  .hide{
    display: none;
  }
  #trigger-upload {
      color: white;
      background-color: #00ABC7;
      font-size: 14px;
      padding: 7px 20px;
      background-image: none;
  }

  #fine-uploader-manual-trigger .qq-upload-button {
      margin-right: 15px;
  }

  #fine-uploader-manual-trigger .buttons {
      width: 36%;
  }

  #fine-uploader-manual-trigger .qq-uploader .qq-total-progress-bar-container {
      width: 60%;
  }
  .owl-demo .item img{
      display: block;
      width: 100%;
      height: auto;
  }
  i.liked{
    color: #e91e63;
}

</style>

<template>
    <div class="content">
      <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
      <div class="">
        <div class="container-fluid">
         
          <div class="" style="position: relative;">
            <!-- <div class="add-cover-photo-div" data-toggle='modal' data-target='#change-cover-photo-modal' data-backdrop='false'>
              <i class="fas fa-camera"></i>
              &nbsp;&nbsp;Change Cover Photo
            </div> -->
            <div class="page-header header-filter" id="cover-image" data-parallax="true" style="background-image:url('http://localhost/meetsocial/assets/images/'); background-color: #8d949e;">
              
            </div>
          </div>
          
          <div class="main main-raised">
            <div class="profile-content">
              <div class="container">
                <div class="row">
                  <div class="col-md-6 ml-auto mr-auto text-center">
                    <div class="profile">
                      <div class="avatar">
                        <div class="logo-link-cont">
                          <img id='logo-image' width='100' height='100' class='round img-raised rounded-circle img-fluid logo-link-cont' :src='user_logo'>
                            
                            
                        </div>
                      </div>
                      <div class="name">
                        <h3 class="title">{{users_name}}</h3>
                        <!-- <div class="row" style="margin-top: 50px;">
                          <a href='http://localhost/meetsocial/index.php/meetglobal/index/dave1614/edit-profile' class="btn btn-primary btn-round col-sm-12">Edit Profile</a>
                                                                    
                        </div> -->
                               
                      </div>
                    </div>
                    
                  </div>
                </div>
                
              </div>
            </div>
          </div>


        </div>
      </div>

      
      
      <footer class="footer">
        <div class="container-fluid">
          
        </div>
      </footer>
    </div>
  
  <!--   Core JS Files   -->
 
</template>

<script>

import Layout from '../Shared/Layout'
import AdminLayout from '../Shared/AdminLayout'
import Pagination from '../Shared/Pagination'
import SearchFilter from '../Shared/SearchFilter'
import FloatingActionButton from '../Shared/FloatingActionButton'
import mapValues from 'lodash/mapValues'
import throttle from 'lodash/throttle'
import pickBy from 'lodash/pickBy'
import TextInput from '../Shared/TextInput'
import SelectInput from '../Shared/SelectInput'
import LoadingButton from '../Shared/LoadingButton'


export default {
  layout(h, page) {
    
    if(page.data.props.user_info.is_admin == 0){
      return h(Layout, [
        page,
        ]
      )
    }else{
      return h(AdminLayout, [
        page,
        ]
      )
    }
  },  
  metaInfo() {
    return {
      title: `${this.page_title}` 
    }
  },components: {
    Pagination,
    SearchFilter,
    FloatingActionButton,
    TextInput,
    SelectInput,
    LoadingButton,

  },
  // ...(this.user_info.is_admin == 0) && {layout: Layout},
  // layout: Layout,
  props: {
    response_arr: Object,
    previous_page: String,
    user_info: Object,
    upgrade_to_business: false,
    active_page: String,
    new_message_count: String,
    new_notifs_count: String,
    page_title: String,
    global_search_val: String,
    all_messages: Array,
    conversations_num: Number,
    noti_count: String,
    all_notifs: Array,


    user_logo: String,
    users_name: String,
  },
  data() {
    return {
      
      show_other_overlay: false,

    }
  },
 
  mounted() {
    
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    
    copyText(text) {
      /* Get the text field */
      var elem = document.createElement("textarea");
      elem.value = text;
      document.body.append(elem);

      /* Select the text field */
      elem.select();
      /* Copy the text inside the text field */
      if(document.execCommand("copy")){
        $.notify({
          message:"Copied To Clipboard"
        },{
          type : "success",
          z_index: 20000,
        });
      }


      document.body.removeChild(elem);

      /* Alert the copied text */
    },
    addCommas(nStr){
      nStr += '';
      var x = nStr.split('.');
      var x1 = x[0];
      var x2 = x.length > 1 ? '.' + x[1] : '';
      var rgx = /(\d+)(\d{3})/;
      while (rgx.test(x1)) {
          x1 = x1.replace(rgx, '$1' + ',' + '$2');
      }
      return x1 + x2;
    },
  },
}
</script>
