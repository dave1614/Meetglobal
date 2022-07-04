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
          <div class="text-right">
            <inertia-link as="button" class="btn btn-primary" :href="route('view_smart_business_loan_history')">View Loan History</inertia-link>
          </div>
          
          <div class="row justify-content-center" style="">
            
            <div class="card col-sm-7 text-center" id="main-card">
              <div class="card-header">
              </div>
              <div class="card-body">
                <p>Interested Qualified partners can get 50% of their previous month earnings as loan and pay 0.25% daily interest from their earnings only.</p>

              
                
                <p>Your Loanble Amount Is <em class="text-primary" v-html="'₦' + loanable_amount"></em></p>
                <p v-if="pending_loan">You still have unpaid loan balance of <em class="text-primary" v-html="'₦' + debt_amount"></em></p>
               
                <br><button v-if="show_request_loan_btn" class="btn btn-primary" @click="requestLoan">Request Loan</button>
               
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

    loanable_amount: [Number, String],
    debt_amount: [Number, String],
    show_request_loan_btn: Boolean,
    pending_loan: Boolean,

    

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
