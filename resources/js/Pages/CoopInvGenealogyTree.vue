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


    .ratio{
      font-weight: bold;
    }
    .tf-tree{
      text-align: center;
      /*cursor: col-resize;*/
    }
  
    .tf-tree .tf-nc .name{
      font-size: 13px;
    }

    .tf-tree .tf-nc {
      border: 0;
      
      /*width: 100%;*/
      /*position: relative;*/


      /*width: 150px;
      height: 220px;
      background: #fff;
      border: 0;
      border-radius: 4px;*/
      
      /*cursor: pointer;*/

    }

    .tf-tree .tf-nc .tree_icon{
      cursor: pointer;
      width: 74px;
      border: 2px solid #c8d5d8;
      border-radius: 50%;
      padding: 4px;
      background: #fff;
    }

    .tf-tree .tf-nc .demo_name_style{
      background-color: #5c519f;
      padding: 2px 3px 4px 3px;
      border-radius: 2px;
      margin-top: 5px;
      margin-bottom: 0;
      color: #fff;
      /*width: 100px;*/
    }

    .tf-tree .tf-nc .icons-div{
      /*margin-top: 10px;
      margin-bottom: 20px;*/
    }

    /*.tf-nc.business{
      border: 5px solid #89229b;
      box-shadow: 0 2px 6px 0 #89229b;
    }

    .tf-nc.basic{
      border: 5px solid #4caf50;
      box-shadow: 0 2px 6px 0 #4caf50;
    }

    .tf-nc.basic .package{
      color: #4caf50;
      text-transform: uppercase;
      font-weight: 700;
    }

    .tf-nc.business .package{
      color: #89229b;
      text-transform: uppercase;
      font-weight: 700;
    }*/
    /*.tf-nc .register-text{
      line-height: 200px;
      font-size: 19px;
      font-weight: bold;
    }

    .tf-nc.register{
      border: 5px solid #000;
    }*/

    .tf-tree .tf-nc .user-name{
      font-weight: bold;
      font-size: 12px;
    }

    .tf-custom .tf-nc:before,
    .tf-custom .tf-nc:after {
      /* css here */
    }

    .tf-custom li li:before {
      /* css here */
    }

    .spinner{
      display: none;
    }

    .tf-hover-more-info{
      padding: 0;
    }

    .tf-hover-more-info .first-row{
      background: #40b7e5;
    }
    
    .tf-hover-more-info .first-row img{
      margin-top: 7px;
      width: 60px!important; 
      height: 60px!important; 
      border-radius: 50%!important; 
      border: 1px solid #0bb4f5; 
      background: #fff; 
      padding: 4px;
    }

    .tf-hover-more-info .first-row p{
      font-weight: bold;
      color: #fff; 
      
    }

    .tf-hover-more-info .first-row p:first-child{
      margin-top: 10px; 
      margin-bottom: 2px; 

    }
    .tf-hover-more-info .second-row{
      padding: 0;
      padding: 15px;
    }

    .tf-hover-more-info .second-row p{
      color: #000;
    }

    .tf-hover-more-info .second-row p:first-child{
      margin-bottom: 0; 
    }

    .tf-hover-more-info .second-row p span{
      font-weight: bold;
    }

    img {
        -webkit-touch-callout: none;
    }
</style>



<template>
    <div class="content">
      <div class="overlay" style="display: none;"></div>
      <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
      <div class="">
        <div class="container-fluid">
          <div class="row justify-content-center" style="">
            
            <div class="card col-md-6" v-if="user_info.is_admin == 1">
              <div class="card-body">
                
                <form action="" id="search-mlm-form" onsubmit="submitSearchMlmInput(this,event)">
                  <div class="form-group">
                    <input type="text" id="search-mlm-input" name="search-mlm-input" class="form-control" :value="user_info.user_name">
                  </div>
                  <input type="submit" class="btn btn-primary" value="Search">
                  <input type="button" class="btn btn-info" value="Clear" onclick="clearSearchInputTextField(this,event)">
                </form>
              </div>
            </div>
            
            <div class="col-md-12" id="main-page-col-md-12" v-html="downline_html">
              
             
            </div>
          </div>

          
        </div>
      </div>

      <!-- <div @click="goBack">
        <floating-action-button :styles="'background: 9124a3;'" :title="'Go Back'">
          
          <i class="fas fa-arrow-left" style="font-size: 25px; color: #fff;"></i>
        </floating-action-button>
      </div> -->
      
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


  
    downline_html: String,

  },
  data() {
    return {
      
      show_other_overlay: false,
      // data: "",
      
    }
  },
  
  mounted() {
    // setTimeout(function () {
      // $('.tf-nc').tooltip({
      //   trigger: "hover"
      // })
    // },1500)
    
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    
    goDownMlm(mlm_db_id,your_mlm_db_id,package1){

    },

    is_numeric(num) {
      return !isNaN(parseFloat(num)) && isFinite(num);
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
    }
  },
}
</script>
