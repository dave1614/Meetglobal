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
      <h2 class="text-center" style="margin-top:30px; margin-bottom: 60px;">Buy Data VTU</h2>
      <div class="col-sm-12 card transparent-card">
        <div class="card-header">
          
          <!-- <inertia-link :href="route('recharge_vtu')" method="get" as="button" type="button" class="btn btn-warning">Go Back</inertia-link> -->
          <button class="btn btn-warning" @click="goBack">Go Back</button>
          <h3 class="text-center">Select Operator For Data:</h3>
        </div>
        <div class="card-body">
          
          <div class="row">

            <div class="card col-sm-2 function-card" style="" @click="selectedDataOperator('mtn')">
              <div class="card-body" style="padding: 0;">
                <img src="/images/mtn_logo.png" style="width: 100%; height: 160px;" alt="MTN">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">MTN</h4>
                </div>
              </div>
            </div>

            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="" id="glo-airtime" @click="selectedDataOperator('glo')">
              <div class="card-body" style="padding: 0;">
                <img src="/images/glo_logo.jpg" style="width: 100%; height: 160px;" alt="GLO">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">GLO</h4>
                </div>
              </div>
            </div>

            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="" id="airtel-airtime" @click="selectedDataOperator('airtel')">
              <div class="card-body" style="padding: 0;">
                <img src="/images/airtel_logo.png" style="width: 100%; height: 160px;" alt="AIRTEL">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">AIRTEL</h4>
                </div>
              </div>
            </div>


            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="">
              <div class="card-body" style="padding: 0;">
                <img src="/images/9mobile-1.png" id="9mobile-airtime" @click="selectedDataOperator('9mobile')" style="width: 100%; height: 160px;" alt="9 MOBILE">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">9 MOBILE</h4>
                </div>
              </div>
            </div>


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
      epin_checkbox_checked: false,
      epins_amount: "",
      epins_json: "",
      
    }
  },
  
  mounted() {
    
  },
  created() {
    $("body").removeClass("modal-open");
  },
  methods: {
    
    selectedDataOperator(type) {
      var self = this;
      console.log(type)
      console.log(this.route('data_plans_list',type))
      if(type == "9mobile"){
        Swal.fire({
          title: 'Choose Option',
          text: "Choose Recharge Option: ",
          icon: 'info',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#4caf50',
          confirmButtonText: 'Normal Recharge',
          cancelButtonText : "SME DATA Recharge"
        }).then((result) => {
          if (result.isConfirmed) {
            self.$inertia.visit(self.route('data_plans_list',type));
            // console.log('accepted')
          } else if (result.dismiss === Swal.DismissReason.cancel) {
          
            // console.log('canceled')
            
            self.$inertia.visit(self.route('9mobile_combo_data_plans_list',type));
            
          }
        });  
      }else{
        self.$inertia.visit(self.route('data_plans_list',type));
      }
      

      
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
