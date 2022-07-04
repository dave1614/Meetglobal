<template>
  <!-- <div v-if="links.length > 3">
    <div class="flex flex-wrap -mb-1">
      <template v-for="(link, key) in links">
        <div v-if="link.url === null" :key="key" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
        <inertia-link v-else :key="key" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500" :class="{ 'bg-white': link.active }" :href="link.url" v-html="link.label" />
      </template>
    </div>
  </div> -->

  <!-- <div v-if="links.length > 3"> -->
    <nav>
      <ul class="pagination row">
        <template v-for="(link, key) in links">

          
          <li v-if="link.url == null && link.active == false" :key="key" class="page-item disabled" aria-disabled="true">
            <span class="page-link" aria-hidden="true" v-html="link.label"></span>
          </li>
          <li v-if="link.active == false && link.url != null" :key="key" class="page-item">
            <inertia-link :key="key" class="page-link"  :href="link.url" v-html="link.label"/>
          </li>
          <li v-if="link.url != null && link.active == true" :key="key" class="page-item active" aria-current="page">
            <span class="page-link" v-html="link.label"></span>
          </li>
        </template>
      </ul>
    </nav>
  <!-- </div> -->
</template>

<script>
export default {
  props: {
    links: Array,
  },
}
</script>
