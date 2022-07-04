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
  .subhead{
    font-weight: bold;
    font-size: 16px;
    margin-top: 15px;
  }

  .network-card{
    cursor: pointer;
    transition: border 0.05s;
  }

  .network-card, .network-card .card-body, .network-card img{
    padding: 0;
  }

  .network-card.selected{
    padding: 5px;
    border: 2px solid #9c27b0;
  }

  .network-card img{
    height: 100%;
  }

  .amount-card{
    cursor: pointer;
    transition: border 0.01s;
  }

  .amount-card span{
    font-size: 14px;
    font-weight: bold;
  }

  .amount-card.selected{
    /*padding: 5px;*/
    border: 2px solid #9c27b0;
  }

  @media screen and (min-width: 574px) {
    .amount-card{
      margin-right: 10px;
    }
  }

  .data-plans-card{
    box-shadow: 0 1px 16px 0 rgb(0 0 0 / 14%);
    cursor: pointer;
    /*transition: border 0.05s;*/
    margin-bottom: 0;
  }

  .data-plans-card span{
    font-weight: bold;
    font-size: 13px;
  }

  

  .data-plans-card.selected{
    /*padding: 5px;*/
    /*border: 2px solid #9c27b0;*/
    background: darkgrey;
  }
</style>

<template>
    <div class="content">
      <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
      <div class="">
        <div class="container-fluid">
          <h2 v-html="notif_info.title"></h2>

          <div class="row">
            <div class="col-sm-10">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title text-secondary" v-html="'From: ' + notif_info.sender"></h4>
                  <div class="btn-group">
                    <button type="button" class="btn btn-primary">To:  me</button>
                    <button type="button" class="btn btn-primary btn-round dropdown-toggle dropdown-toggle-split" data-toggle="dropdown">
                    </button>
                    <div class="dropdown-menu">
                      <p class="dropdown-item" v-html="'from: ' + notif_info.sender"></p>
                      <p class="dropdown-item" v-html="'reply-to: ' + notif_info.sender"></p>
                      <p class="dropdown-item" >to: me</p>
                      <p class="dropdown-item" v-html="'date: ' + notif_info.date_sent"></p>
                      <p class="dropdown-item" v-html="'time: ' + notif_info.time_sent"></p>
                      <p class="dropdown-item" >status: read</p>
                    </div>
                  </div>
                </div>

                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-12">
                      <p style="font-size: 18px;" v-html="notif_info.message"></p>
                      <!-- <?php
                      if($action_taken == 0){
                        //Set Notif Id As Session
                        $this->session->set_userdata('notif_id',$second_addition);
                          if(!is_null($btn1)){
                            echo $btn1;
                          }
                          if(!is_null($btn2)){
                            echo $btn2;
                          }
                          if(!is_null($btn3)){
                            echo $btn3;
                          }
                        }
                      
                      ?> -->
                    </div>
                  </div>
                </div>

                <div class="card-footer">
                  <a class="btn btn-success disabled" href="<?php echo site_url('onehealth/index/reply/'.$id); ?>">Reply</a>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div @click="goBack">
        <floating-action-button :styles="'background: 9124a3;'" :title="'Go Back'">
          
          <i class="fas fa-arrow-left" style="font-size: 25px; color: #fff;"></i>
        </floating-action-button>
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


    notif_info: Object,
    
    
  

    

  },
  data() {
    return {
      show_other_overlay: false,

    }
  },
  
  mounted() {
    console.log(this.notif_info)
    console.log(this.$page.props)
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    goBack() {
      var self = this;
      self.$inertia.visit(self.route('notifications'))
    },
    openNotification(notif_id){
      var self = this;
      self.$inertia.visit(self.route('notification',notif_id));
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
