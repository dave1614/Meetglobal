<template>
  <div class="container" style="margin-top:20px;">
    <!-- <layout> -->
      <div class="row justify-content-center">
        <div class="col-sm-10" >
          <div class="card">
            <h2 class="text-center">Home Page</h2>
            <inertia-link :href="route('page2')" method="post" :data="{ info: 'This Was Sent From Home And Edited' }" as="button" type="button" class="btn btn-primary">Go To Page 2</inertia-link>
            <inertia-link :href="route('recharge_vtu')" method="get">Recharge VTU</inertia-link>
            <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
              
            </search-filter>
            <div class="" v-if="users.data.length > 0">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="user in users.data" :key="user.id">
                    <td><inertia-link :href="route('edit_user',user.id)" method="get" style="display:block;">{{user.id}}</inertia-link></td>
                    <td><inertia-link :href="route('edit_user',user.id)" method="get" style="display:block;">{{user.name}}</inertia-link></td>
                    <td><inertia-link :href="route('edit_user',user.id)" method="get" style="display:block;">{{user.email}}</inertia-link></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <pagination class="mt-6" :links="users.links" style="margin-top:30px;"/>
          <p :if="users.data.length > 0" style="margin-top:30px;">{{users.total}} Total Entries</p>
          
        </div>
        
      </div>
    <!-- </layout> -->
    <div @click="openCreateUserPage">
      <floating-action-button :styles="'background: blue;'" :title="'Create New User'">
        <i class="fas fa-plus" style="font-size: 25px; color: #fff;" aria-hidden="true"></i>
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
  metaInfo: { title: 'Home Page' },
  components: {
    Pagination,
    SearchFilter,
    FloatingActionButton
  },
  layout: Layout,
  props: {
    filters: Object,
    users: Object,
    
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get(this.route('home'), pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  mounted() {
    console.log(this.filters)
  },
   created() {
    console.log(this.users)
  },
  methods: {
    openCreateUserPage(){
      console.log('test')
      // var create_user_url = this.route('create_user');
      // window.location.assign(create_user_url)
      this.$inertia.visit('create_user')
    },
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
