<style>
  .text-primary{
    color: #9124a3 !important;
  }

  .function-card{
    cursor: pointer;
  }

  .card.transparent-card{
    background: transparent !important;
  }
</style>

<template>
  <div class="container" style="margin-top:20px;">
    
    <div class="row justify-content-center">
      <h2 class="text-center" style="margin-top:30px; margin-bottom: 60px;">Recharge VTU</h2>
      <div class="col-sm-12 card transparent-card">
        <div class="card-header">
          <h3 class="text-center">Recharge Options</h3>
        </div>
        <div class="card-body row">
        
          <div class="col-sm-3 card function-card" @click="buyAirtime">
            <div class="card-body">
              <div class="" style="height: 25px; background: #9124a3; border-radius: 12px; margin-bottom: 9px;">
                <p class="text-center" style="color: #fff; font-weight: bold;">2% Cashback</p>

              </div>
              <div class="text-center">
                <i class="material-icons text-primary" style="font-size: 100px;">
                stay_current_portrait
                </i>
                <h4 >Buy Airtime</h4>
              </div>
            </div>
          </div>

          <div class="col-sm-3 card function-card" @click="buyData">
            <div class="card-body">
              <div style="height: 25px; background: #9124a3; border-radius: 12px; margin-bottom: 9px;">
                <p class="text-center" style="color: #fff; font-weight: bold;">2% Cashback</p>
              </div>
              <div class="text-center">
                <i class="material-icons text-primary" style="font-size: 100px;">
                speaker_phone
                </i>
                <h4>Buy Databundle</h4>
              </div>
            </div>
          </div>
         
          <div class="col-sm-3 card function-card" @click="loadCableTv">
            <div class="card-body">
              <div style="height: 25px; background: #9124a3; border-radius: 12px; margin-bottom: 9px;">
                <p class="text-center" style="color: #fff; font-weight: bold;">0.105% Cashback</p>
              </div>
              <div class="text-center">
                <i class="material-icons text-primary" style="font-size: 100px;">
                tv
                </i>
                <h4 >Cable Tv Subscription</h4>
              </div>
            </div>
          </div>

          <div class="col-sm-3 card function-card" @click="loadElectricity"> 
            <div class="card-body">
              <div style="height: 25px; background: #9124a3; border-radius: 12px; margin-bottom: 9px;">
                <p class="text-center" style="color: #fff; font-weight: bold;">0.105% Cashback</p>
              </div>
              <div class="text-center">
                <i class="material-icons text-primary" style="font-size: 100px;">
                flash_on
                </i>
                <h4>Electricity Bill Payment</h4>
              </div>
            </div>
          </div>

          <div class="col-sm-3 card function-card" @click="loadAirtimeToWallet">
            <div class="card-body">
              <div class="text-center" style="margin-top: 25px;">
                <i class="fas fa-money-bill-wave text-primary" style="font-size: 100px;"></i>
                <h4>Airtime To Wallet</h4>
              </div>
            </div>
          </div>

          <div class="col-sm-3 card function-card" @click="loadEducational">
            <div class="card-body">
              <div style="height: 25px; background: #9124a3; border-radius: 12px; margin-bottom: 9px;">
                <p class="text-center" style="color: #fff; font-weight: bold;">₦100 Cashback</p>
              </div>
              <div class="text-center">
              
                <i style="font-size: 100px;" class="fas fa-user-graduate text-primary"></i>
                <h4 style="margin-top: 20px;">Educational Vouchers</h4>
              </div>
            </div>
          </div>

          <div class="col-sm-3 card function-card" @click="loadRouter">
            <div class="card-body">
              <div class="text-center">
                <i class="material-icons text-primary" style="font-size: 100px;">
                router
                </i>
                <h4 style="margin-top: 20px;">Router Recharge</h4>
              </div>
            </div>
          </div>
         
          <div class="col-sm-3 card function-card" @click="bulkSMS">
            <div class="card-body">
              <div class="text-center">
                <i class="fas fa-comment-alt text-primary" style="font-size: 100px;"></i>
                <h4 style="margin-top: 20px;">Bulk SMS</h4>
              </div>
            </div>
          </div>
        </div>  
        
      </div>
      
    </div>
    
    <div @click="viewTransactionHistory">
      <floating-action-button :styles="'background: #9124a3;'" :title="'View Transaction History'">
        <i class="fas fa-history" style="font-size: 25px; color: #fff;" aria-hidden="true"></i>
      </floating-action-button>
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


export default {
  metaInfo: { title: 'Recharge VTU' },
  components: {
    Pagination,
    SearchFilter,
    FloatingActionButton
  },
  layout: Layout,
  props: {
   
    
    
  },
  data() {
    return {
      
    }
  },
  
  mounted() {
    
  },
  created() {
    $("body").removeClass("modal-open");
  },
  methods: {
    buyAirtime() {
      this.$inertia.visit(this.route('buy_airtime'));
    },
    buyData() {
      this.$inertia.visit(this.route('buy_data'));
    },
    loadCableTv() {
      this.$inertia.visit(this.route('buy_cable'));
    },
    loadElectricity() {
      this.$inertia.visit(this.route('buy_electricity'));
    },
    loadAirtimeToWallet() {

    },
    loadEducational() {

    },
    loadRouter() {

    },
    bulkSMS() {

    },
    viewTransactionHistory() {

    },
  },
}
</script>
