import Vue from 'vue'
import VueMeta from 'vue-meta'
import PortalVue from 'portal-vue'
import { InertiaProgress } from '@inertiajs/progress'
import { createInertiaApp } from '@inertiajs/inertia-vue'
import { Inertia } from '@inertiajs/inertia'

Vue.config.productionTip = false
Vue.mixin({ methods: { route: window.route } })
Vue.use(PortalVue)
Vue.use(VueMeta)
Vue.use(require('vue-chartist'))

// InertiaProgress.init()
InertiaProgress.init({
  // The delay after which the progress bar will
  // appear during navigation, in milliseconds.
  delay: 250,

  // The color of the progress bar.
  color: '#9124a3',

  // Whether to include the default NProgress styles.
  includeCSS: true,

  // Whether the NProgress spinner will be shown.
  showSpinner: true,
})

Inertia.on('start', (event) => {
  // console.log(event)
  console.log(`Starting a visit to ${event.detail.visit.url}`)
  $(".close-layer").click()
  $(".overlay").show();
})

Inertia.on('finish', (event) => {
  console.log(`Finished Loading ${event.detail.visit.url}`)
  $(".overlay").hide();
})

createInertiaApp({
  resolve: name => require(`./Pages/${name}`),
  setup({ el, app, props }) {
    new Vue({
      metaInfo: {
        titleTemplate: title => (title ? `${title} - MGR` : 'Meetglobal Resources'),
      },
      render: h => h(app, props),
    }).$mount(el)
  },
})
