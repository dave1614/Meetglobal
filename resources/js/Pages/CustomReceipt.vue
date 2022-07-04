<style>
  body{
    background: #fff !important;
  }
</style>

<template>
  <div >
    <div class="container pt-2" style="height:100vh;">
      <div class="row justify-content-center">
        <div class="col-12" style=" ">
          <div class="text-center">
            <img src="/images/logo-img.jpeg" style="width: 80px;">
            <h4 class="text-bold mt-1" style="color: #2596be;">MEETGLOBAL RESOURCES</h4>
          </div>

          <div id="message" >
                  
          </div>
        </div>
   
        
      </div>
    </div>

    <div class="" style="position: absolute; bottom: 0; display: none;">
      <div class="row">
        <div class="col-2 pr-1 text-right" style="background: #2596be;">
          <i class="fas fa-map-marker-alt" style="color: #fff;"></i> 
        </div>
        <span class="text-bold col-10" style="font-size: 10px; line-height: 1.2;">NO8B AFOLABI ESAN CLOSE, ADEXON ILEKPO BUS STOP, AKESAN LAGOS</span>
      </div>
      <div class="row">
        <div class="col-2 pr-1 text-right" style="background: #2596be;">
          <i class="fas fa-phone" style="color: #fff;"></i> 
        </div>
        <span class="text-bold col-10 mt-1" style="font-size: 10px; line-height: 1.2;">08095812402</span>
      </div>
      <div class="row">
        <div class="col-2 pr-1 text-right" style="background: #2596be;">
          <i class="fas fa-envelope" style="color: #fff;"></i> 
        </div>
        <span class="text-bold col-10" style="font-size: 10px; line-height: 1.2; text-transform: lowercase;">geeklickservices@gmail.com</span>
      </div>

    </div>
  </div>
 
</template>

<script>




export default {
  
  metaInfo() {
    return {
      title: `${this.page_title}` 
    }
  },components: {
   
  },
  
  props: {
    page_title: String,
    details: String,

    

  },
  data() {
    return {
      edit_quill: "",
      
    }
  },
  
  mounted() {
    var self = this;
    // self.edit_quill = new Quill('#message', {
    //   theme : 'snow'
    // });

    if(self.details != ""){
      
      // self.edit_quill.setContents(JSON.parse(self.details));

      self.edit_quill = new Quill('#message', {
        theme : 'snow',
        readOnly : true,
        modules : {
            "toolbar": false
        }
      });

    
      
      self.edit_quill.setContents(JSON.parse(self.details));
    }
  },
  created() {
    
    
  },
  methods: {
    
    isNumber(value) 
    {
       return typeof value === 'number' && isFinite(value);
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
