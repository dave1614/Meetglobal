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
            
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Edit Front Page Message</h3>
              </div>
              <div class="card-body">
                
                  
                <form id="front-page-message-form" @submit.prevent="submitFrontPageMessageForm">

                  <div id="message" style="height: 200px;">
                  
                  </div>
                  

                  <button :disabled="front_page_form.processing" class="d-flex align-items-center btn btn-primary col-12">
                  Submit
                    <div style="" v-if="front_page_form.processing" class="spinner-border spinner-border-sm ml-auto" />
                    
                  </button>
                </form>
                

                
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
import TextareaInput from '../../Shared/TextareaInput'
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
    TextareaInput,
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


    front_page_text: String,

    
  },
  data() {
    return {
      credit_user_earnings_form: this.$inertia.form({
        amount: "",
        user_name: ""
      }),

      front_page_form: this.$inertia.form({
        text: "",
      }),
        
      edit_quill: "",
      show_other_overlay: false,
      total_sim_incentive_earning: "0",
      show_credit_user_form: false,
      total_sim_incentive_earning_title: "",
    }
  },
  
  mounted() {
    var self = this;
    self.edit_quill = new Quill('#front-page-message-form #message', {
      theme : 'snow'
    });

    if(self.front_page_text != ""){
      
      self.edit_quill.setContents(JSON.parse(self.front_page_text));
    }
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    goBackToUsernameForm() {
      var self = this
      self.total_sim_incentive_earning = "0";
      self.show_credit_user_form = false;
    },
    submitCreditUserEarningForm() {
      var self = this;
      self.credit_user_earnings_form.user_name = self.user_name_form.user_name;
      self.credit_user_earnings_form.post(self.route('credit_user_sim_incentive_earning'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            swal({
              title: 'Success',
              text: self.credit_user_earnings_form.user_name + "'s Sim Incentive Income Has Been Credited Successfully",
              type: 'success',
              confirmButtonColor: '#3085d6',
              allowOutsideClick: false,
            }).then(function(){
              self.$inertia.visit(self.route('sim_activation_initiative'));
            });
          }else if(response.user_name_invalid){
            swal({
              title: 'Ooops',
              text: "This Username Entered Is Invalid",
              type: 'error'
            })
          }else{
            swal({
              title: 'Ooops',
              text: "Something Went Wrong. Please Try Again",
              type: 'error'
            })
          }
        },onError: (errors) => {
          
          var errors = JSON.parse(JSON.stringify(errors))
          var errors_num = Object.keys(errors).length;
          
          if(errors_num > 0){
            $.notify({
              message: errors_num + " Field(s) Have Error(s). Please Correct Them."
            },{
              type : "warning",
              z_index: 20000,
            });
          }
        },
      });
    },
    submitFrontPageMessageForm() {
      var self = this;
      var content_length = self.edit_quill.getLength();
      if(content_length >= 2){
        let formData = new FormData();

        var content = self.edit_quill.getContents();
        // console.log(content)
        content = JSON.stringify(content);
        self.front_page_form.text = content
        self.front_page_form.post(self.route('change_front_page_message'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success){
             $.notify({
                message: "Front Page Message Changed Successfully"
              },{
                type : "success",
                z_index: 20000,
              });
            }else{
              swal({
                title: 'Ooops',
                text: "Something Went Wrong. Please Try Again",
                type: 'error'
              })
            }
          },onError: (errors) => {
            
            var errors = JSON.parse(JSON.stringify(errors))
            var errors_num = Object.keys(errors).length;
            
            if(errors_num > 0){
              $.notify({
                message: errors_num + " Field(s) Have Error(s). Please Correct Them."
              },{
                type : "warning",
                z_index: 20000,
              });
            }
          },
        });
      }
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
