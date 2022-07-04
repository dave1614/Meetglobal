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
          <div class="row justify-content-center" style="">

            <div class="container" v-if="all_notifications.data.length > 0">
              <div @click="openNotification(notification.id)" :class="notification.received == 0 ? 'data-plans-card card selected' : 'data-plans-card card' " v-for="notification in all_notifications.data" :key="notification.index" >
                <div class="card-body row">

                  <div class="col-3 col-sm-1">
                    <img :src="notification.logo" :alt="notification.sender" class="col-12" style="border-radius: 50%;">
                  </div>

                  <div class="col-4 col-sm-6">
                    <span v-html="notification.notif_title"></span>
                  </div>

                  <div class="col-5 col-sm-5">
                    <span v-html="notification.format_time"></span>
                  </div>

                </div>
              </div>
            </div>
            <h4 v-else class="text-warning text-center">You Don't Have Any Notifications</h4>

            <pagination class="mt-6" :links="all_notifications.links" style="margin-top:30px;"/>

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


    all_notifications: Object,
    
    
  

    

  },
  data() {
    return {
      show_other_overlay: false,

    }
  },
  
  mounted() {
    console.log(this.all_notifications)
    console.log(this.$page.props)
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
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
