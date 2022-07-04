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
</style>

<template>
    <div class="content">
      <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
      <div class="">
        <div class="container-fluid">
          
          <div class="row justify-content-center" style="">
            
            <h3 v-html="'Earnings For ' + month_year"></h3>
            <div class="card">
              <div class="card-body">
                <h3 style="font-weight: bold;" class="text-center">Airtime</h3>
                <h5><b>Mtn Airtime Earnings: </b> <em class="text-primary" v-html="'₦' + mtn_airtime_earnings"></em></h5>
                <h5><b>Airtel Airtime Earnings: </b> <em class="text-primary" v-html="'₦' + airtel_airtime_earnings"></em></h5>
                <h5><b>Glo Airtime Earnings: </b> <em class="text-primary" v-html="'₦' + glo_airtime_earnings"></em></h5>
                <h5><b>9mobile Airtime Earnings: </b> <em class="text-primary" v-html="'₦' + mobile_airtime_earnings"></em></h5>

                <h3 style="font-weight: bold;" class="text-center">Data</h3>
                <h5><b>Mtn Data Earnings: </b> <em class="text-primary" v-html="'₦' + mtn_data_earnings"></em></h5>
                <h5><b>Airtel Data Earnings: </b> <em class="text-primary" v-html="'₦' + airtel_data_earnings"></em></h5>
                <h5><b>Glo Data Earnings: </b> <em class="text-primary" v-html="'₦' + glo_data_earnings"></em></h5>
                <h5><b>9mobile Data Earnings: </b> <em class="text-primary" v-html="'₦' + mobile_data_earnings"></em></h5>

                <h3 style="font-weight: bold;" class="text-center">Cable Tv</h3>
                <h5><b>Dstv Earnings: </b> <em class="text-primary" v-html="'₦' + dstv_cable_earnings"></em></h5>
                <h5><b>Gotv Earnings: </b> <em class="text-primary" v-html="'₦' + gotv_cable_earnings"></em></h5>
                <h5><b>Startimes Earnings: </b> <em class="text-primary" v-html="'₦' + startimes_cable_earnings"></em></h5>

                <h3 style="font-weight: bold;" class="text-center">Electricity</h3>
                <h5><b>Ikeja Electric: </b> <em class="text-primary" v-html="'₦' + ikeja_electricity_earnings"></em></h5>
                <h5><b>Eko Electric: </b> <em class="text-primary" v-html="'₦' + eko_electricity_earnings"></em></h5>
                <h5><b>Abuja Electric: </b> <em class="text-primary" v-html="'₦' + abuja_electricity_earnings"></em></h5>
                <h5><b>Kano Electric: </b> <em class="text-primary" v-html="'₦' + kano_electricity_earnings"></em></h5>
                <h5><b>Jos Electric: </b> <em class="text-primary" v-html="'₦' + jos_electricity_earnings"></em></h5>
                <h5><b>Ibadan Electric: </b> <em class="text-primary" v-html="'₦' + ibadan_electricity_earnings"></em></h5>
                <h5><b>Enugu Electric: </b> <em class="text-primary" v-html="'₦' + enugu_electricity_earnings"></em></h5>
                <h5><b>PHC Electric: </b> <em class="text-primary" v-html="'₦' + phc_electricity_earnings"></em></h5>
                <h5><b>Kaduna Electric: </b> <em class="text-primary" v-html="'₦' + kaduna_electricity_earnings"></em></h5>
                

                <h3 style="font-weight: bold;" class="text-center">Airtime To Wallet</h3>
                <h5><b>Earnings: </b> <em class="text-primary" v-html="'₦' + airtime_to_wallet_earnings"></em></h5>

                <h3 style="font-weight: bold;" class="text-center">Educational</h3>
                <h5><b>Earnings: </b> <em class="text-primary" v-html="'₦' + educational_earnings"></em></h5>

                <h3 style="font-weight: bold;" class="text-center">Router</h3>
                <h5><b>SMILE: </b> <em class="text-primary" v-html="'₦' + smile_router_earnings"></em></h5>

                <h3 style="font-weight: bold;" class="text-center">Bulk SMS</h3>
                <h5><b>Earnings: </b> <em class="text-primary" v-html="'₦' + bulk_sms_earnings"></em></h5>
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

import Layout from '../../Shared/Layout'
import AdminLayout from '../../Shared/AdminLayout'
import Pagination from '../../Shared/Pagination'
import SearchFilter from '../../Shared/SearchFilter'
import FloatingActionButton from '../../Shared/FloatingActionButton'
import mapValues from 'lodash/mapValues'
import throttle from 'lodash/throttle'
import pickBy from 'lodash/pickBy'
import TextInput from '../../Shared/TextInput'
import SelectInput from '../../Shared/SelectInput'
import LoadingButton from '../../Shared/LoadingButton'


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


    

    month_year: [Number,String],
    mtn_airtime_earnings: [Number,String],
    airtel_airtime_earnings: [Number,String],
    glo_airtime_earnings: [Number,String], 
    mobile_airtime_earnings: [Number,String], 

    mtn_data_earnings: [Number,String], 
    airtel_data_earnings: [Number,String], 
    glo_data_earnings: [Number,String], 
    mobile_data_earnings: [Number,String], 

    dstv_cable_earnings: [Number,String], 
    gotv_cable_earnings: [Number,String], 
    startimes_cable_earnings: [Number,String], 

    ikeja_electricity_earnings: [Number,String], 
    eko_electricity_earnings: [Number,String], 
    abuja_electricity_earnings: [Number,String], 
    kano_electricity_earnings: [Number,String], 
    jos_electricity_earnings: [Number,String], 
    ibadan_electricity_earnings: [Number,String], 
    enugu_electricity_earnings: [Number,String], 
    phc_electricity_earnings: [Number,String], 
    kaduna_electricity_earnings: [Number,String], 

    airtime_to_wallet_earnings: [Number,String], 
    educational_earnings: [Number,String], 
    smile_router_earnings: [Number,String], 
    bulk_sms_earnings: [Number,String], 
    
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
    }
  },
}
</script>
