<template lang="html">
  <div class="nav_content">
    <div class="nav">
      <ul>
        <li v-for="(item,i) in menus" :class="{'item_class':selectItem==i}" @click="type(item,i)">{{item.name}}</li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  data(){
    return{
      selectItem: 0
    }
  },
  created(){
    this.$root.$on('child_change',(e)=>{
      this.selectItem = e
    });
//    if(window.sessionStorage.getItem('child_nav')){
//      this.selectItem = window.sessionStorage.getItem('child_nav')
//    }
  },
  props:{
    menus:{
      type:Array
    },
  },
  methods:{
    type: function(key,i){
      console.log(key);
      this.selectItem = i;
//      window.sessionStorage.setItem('child_nav', i);
      this.$emit('go_child',key.item)
    }
  }
}
</script>

<style lang="scss" scoped>
@import '../../../../assets/css/function.scss';
.nav_content{
  width: 920px;
  .nav{
    width: 920px;
    margin-bottom: 17px;
    margin-top: 17px;
    overflow: hidden;
    ul{
      overflow: hidden;
      float: left;
      li{
        float: left;
        background-color: $bg_color;
        border-radius:20px;
        color: #fff;
        // width: 60px;
        padding: 5px 15px;
        margin-right: 30px;
        border: 1px solid $bg_color;
        cursor: pointer;
      }
      li:first-child{
        margin-left: 20px;
      }
      .item_class{
        color: black;
        background-color: #fff;
        border: 1px solid $bg_color
      }
      li:hover{
        color: black;
        background-color: #fff;
        border: 1px solid $bg_color
      }
      .main{
        border-bottom: 1px solid $bg_color;
      }
    }
    .pour{
      float: right;
      padding: 5px 0;
      .input{
        width:85px;
        padding:5px;
        background-color: #eeeeee;
        margin-right: 10px;
        border-radius: 5px;
      }
      .button{
        width:85px;
        padding: 10px;
        background-color: #eeeeee;
        outline: none 0;
        border-radius: 5px;
        margin-right: 20px
      }
    }
  }
}
</style>
