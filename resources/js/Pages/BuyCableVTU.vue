<style>
  .text-primary{
    color: #9124a3 !important;
  }

  .function-card{
    cursor: pointer;
    padding: 0;
  }

  .card.transparent-card{
    background: transparent !important;
  }
</style>

<template>
  <div class="container" style="margin-top:20px;">
    
    <div class="row justify-content-center">
      <h2 class="text-center" style="margin-top:30px; margin-bottom: 60px;">Buy Cable Tv VTU</h2>
      <div class="col-sm-12 card transparent-card">
        <div class="card-header">
          
          <!-- <inertia-link :href="route('recharge_vtu')" method="get" as="button" type="button" class="btn btn-warning">Go Back</inertia-link> -->
          <button class="btn btn-warning" @click="goBack">Go Back</button>
          <h3 class="text-center">Select Operator For Cable Tv:</h3>
        </div>
        <div class="card-body">
          
          <div class="row">

            <div class="card col-sm-2 function-card" style="" @click="selectedTvOperator('dstv')">
              <div class="card-body" style="padding: 0;">
                <img src="/images/dstv_logo.jpg" style="width: 100%; height: 160px;" alt="DStv">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">DStv</h4>
                </div>
              </div>
            </div>

            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="" id="glo-airtime" @click="selectedTvOperator('gotv')">
              <div class="card-body" style="padding: 0;">
                <img src="/images/gotv_logo.jpg" style="width: 100%; height: 160px;" alt="GoTv">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">GoTv</h4>
                </div>
              </div>
            </div>

            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="" id="airtel-airtime" @click="selectedTvOperator('startimes')">
              <div class="card-body" style="padding: 0;">
                <img src="/images/startimes_logo.jpg" style="width: 100%; height: 160px;" alt="STARTIMES">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">Startimes</h4>
                </div>
              </div>
            </div>

          </div>
          
          
        </div>  
        
      </div>
      
    </div>
    
    <div class="modal fade" data-backdrop="static" id="enter-iuc-no-data-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content" >
          <div class="modal-header">
            <h3 class="modal-title">{{iuc_modal_title}}</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body" id="modal-body">
              
            <form id="enter-iuc-no-data-form" @submit.prevent="submitIucNoForm">

              <text-input v-if="chose_multichoice" v-model="form.iuc_number" :error="form.errors.iuc_number" type="number" :label="iuc_modal_form_label" id="iuc_no" required/>
              <text-input v-if="chose_startimes" v-model="form.smart_card_number" :error="form.errors.smart_card_number" type="number" :label="iuc_modal_form_label" id="smart_card_number" required/>

              <!-- <input type="submit" class="btn btn-primary col-12" style="margin-top: 20px;"> -->
              <loading-button :loading="form.processing" class="btn-indigo ml-auto btn btn-primary col-12" type="submit">Submit</loading-button>

            </form>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
          </div>
        </div>
      </div>
     
    </div>
  
  </div>
</template>

<script>

import Layout from '../Shared/Layout'
import Pagination from '../Shared/Pagination'
import SearchFilter from '../Shared/SearchFilter'
import FloatingActionButton from '../Shared/FloatingActionButton'
import mapValues from 'lodash/mapValues'
import throttle from 'lodash/throttle'
import pickBy from 'lodash/pickBy'
import TextInput from '../Shared/TextInput'
import LoadingButton from '../Shared/LoadingButton'


export default {
  metaInfo: { title: 'Buy Data VTU' },
  components: {
    Pagination,
    SearchFilter,
    FloatingActionButton,
    TextInput,
    LoadingButton,

  },
  layout: Layout,
  props: {
    response_arr: Object,
    previous_page: String,
  },
  data() {
    return {
      type: "",
      iuc_modal_title: "",
      chose_multichoice: false,
      chose_startimes: false,
      iuc_modal_form_label: "",
      form: this.$inertia.form({
        iuc_number: "",
        smart_card_number: "",
        type: ""

      }),
      
    }
  },
  

  mounted() {

  },
  created() {
    $("body").removeClass("modal-open");
  },
  methods: {
    submitIucNoForm() {
      var self = this;
      // console.log(this.form)


      var iuc_number = self.form.iuc_number;
      var smart_card_number = self.form.smart_card_number;
      var type = self.form.type;
      
      
      self.form.post(self.route('verify_cable_tv_number',type), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success && response.customer_name != ""){
            
            var customer_name = response.customer_name;
           

            
            Swal.fire({
              title: 'Info',
              html: "Is This Your Name ? <br> <em class='text-center text-primary'>"+customer_name+"</em>",
              icon: 'info',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes Proceed!',
              cancelButtonText : "No",
            }).then((result) => {
              if (result.isConfirmed) {
                
                $("#enter-iuc-no-data-modal").modal("hide");
                if(type == "startimes"){
                  var decoder_number = smart_card_number;
                }else{
                  var decoder_number = iuc_number;
                }
                self.$inertia.visit(self.route('cable_tv_plans',type) + '?dn='+decoder_number);
              }
            });
          }else if(response.invalid_user){
            Swal.fire({
              title: 'Error!',
              text: "The Details Entered Were Invalid. Please Try Again." ,
              icon: 'error'
            })
          }else{
            $.notify({
            message:"Something Went Wrong. Please Try Again"
            },{
              type : "warning",
              z_index: 20000,
            });
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
    
    selectedTvOperator(type) {
      var self = this;
      if(type == "startimes"){
        self.chose_startimes = true;
        self.chose_multichoice = false;
        self.iuc_modal_title = "Enter Smart Card Number";
        self.iuc_modal_form_label = "Smart Card Number";
      }else{
        self.chose_multichoice = true;
        self.chose_startimes = false;
        self.iuc_modal_title = "Enter IUC Number";
        self.iuc_modal_form_label = "IUC Number";
      }
      self.form.type = type
      $("#enter-iuc-no-data-modal").modal({
        backdrop: false,
        show: true
      });
    },
    goBack() {
      if(this.previous_page != this.route('recharge_vtu')){
        this.$inertia.visit(this.route('recharge_vtu'));
      }else{
        window.history.back()
      }
    },
    
  },
}
</script>
