<template lang="html">
  <div class="bet" v-show="modal">
    <div class="pk_bet">
    </div>
    <div class="modal">
      <div class="header">
        <h3>下注金额</h3>
      </div>
      <p>当前投注：</p>
      <div class="bet_center">
        <p class="center_top" style="width:80%;">
          <span class="one" style="width:110px">类型</span>
          <span class="two" style="width:100px">号码</span>
          <span class="three" style="width: 124px;border-right: none;">赔率</span>
          <!--<span class="four">金额</span>-->
        </p>
        <div class="bet_five_modal" style="overflow:auto;height: 145px;width: 80%;margin: 0 auto;">
          <p class="center_content" style="width:100%" v-for="item in lists" v-if="item.money">
            <span class="one" style="width:110px">{{item.remark.split('#')[1]}}</span>
            <span class="two" style="width:100px" v-if="!isNaN(item.remark.split('#')[2])"><i class="box">{{item.remark.split('#')[1]}}</i></span>
            <span v-else class="two" style="width:100px">{{item.remark.split('#')[2]}}</span>
            <span class="three" style="width:78px;text-align:right;border-right: none;">{{item.odd}}</span>
            <!--<span class="four">{{item.money}}</span>-->
          </p>
        </div>
      </div>
      <div style="text-align: center;padding: 10px 0">
        <span style="margin-right:10px">共1注</span>
        <span>金额合计：{{money}}</span>
      </div>
      <div style="padding: 10px 0">
        <button type="button" v-if="!loading" class="bet_bottom color1" @click="pour()">下注</button>
        <button type="button" v-else class="bet_bottom color1">下单中...</button>
        <button type="button" class="bet_bottom color2 font" @click="cancel()">取消</button>
      </div>
    </div>
  </div>
</template>

<script>
  import api from '../api/config'
  import share from './share'
  import mymousewheel from '../assets/js/mousewheel'
  import {Modal} from 'iview';
  import '../assets/css/bet.scss';
  export default {
    components: {Modal},
    props:{
      modal:{
        type:Boolean,
        default: false
      }
    },
    data(){
      return{
        loading:false,
        lists:[],
        arr:[],
        qishu:'',
        money: 0
      }
    },
    created() {
      this.$root.$on('id-selected-five', (e,m) => {
        // console.log(e)
        for(let i = 0;i< e.length;i++){
          for(let j = 0;j<e[i].object.length;j++){
            //添加金额参数入对象
            let money = e[i].object[j].money;
            this.arr.push(Object.assign(e[i].object[j],{'money':money}));
          }
        }
        if(this.arr){
          this.lists = this.unique(this.arr);
          this.money = m;
        }
      });
      this.$root.$on('reset',(e)=>{
        this.money = e
      });
      this.$root.$on('c_data',(e)=>{
        this.qishu = e.qishu
      })
    },
    mounted(){
      let bet_five_modal = document.querySelector(".bet_five_modal");
      mymousewheel(bet_five_modal);
    },
    methods:{
      pour: function(){
        window.pour_status = 1;
        var send_data = this.lists;
        var send_op = [];
        var send_arr = [];
        var new_arr = [];
        //拼接明细
        var mingxi_arr = [];
        var old_arr = [];
        var send_hp = [];
        for(let l = 0;l<send_data.length;l++){
          if(send_data[l].money){
              //input_name
              new_arr.push(send_data[l].input_name);
              send_arr.push(new_arr);
              send_arr = this.unique(send_arr);
              send_op.push(send_data[l]);
              var input_name = [];
              send_arr.forEach((item, index) => {
                input_name[index] = JSON.parse(JSON.stringify(send_op[0]));
                input_name[index].input_name = item.join()
              });
              //mingxi
              mingxi_arr.push(send_data[l].mingxi);
              old_arr.push(mingxi_arr);
              console.log(old_arr);
//              old_arr = this.unique(old_arr);
              send_hp.push(send_data[l]);
              var mingxi = [];
              old_arr.forEach((item, index) => {
                mingxi[index] = JSON.parse(JSON.stringify(send_hp[0]));
                mingxi[index].mingxi = item.join()
              });
//              var end_data = Object.assign(input_name[0],mingxi[0]);
              input_name[0].mingxi = mingxi[0].mingxi;
                console.log(input_name);
                console.log(mingxi);
          }
        }
        const body = {
          fc_type:JSON.stringify(this.$route.query.page),
          qishu:JSON.stringify(this.qishu),
          data:JSON.stringify(input_name)
        };
        this.loading = true;
        api.addbet(this,body,(res)=>{
          console.log(res.data.ErrorCode);
          if(res.data.ErrorCode == 1){
            window.pour_status = 2;
            this.loading = false;
            this.$Modal.success({
              content: "下注成功",
              onOk: () => {
                this.$root.$emit('bet_success',true);
                this.$root.$emit('success',true)
              },
            });
            window.setTimeout(() => {
              this.$root.$emit('bet_success',true);
              this.$root.$emit('success',true);
              this.$Modal.remove()
            }, share.bet_time)
          }else if(res.data.ErrorCode == 2){
            this.loading = false;
          }
        })
      },
      reset: function(){
        // this.modal = false;
        this.lists = [];
        this.$root.$emit('reset','');
      },
      cancel: function(){
        this.loading = false;
        this.$emit('cancel',false)
      },
      //数组去重
      unique: function(arr){
        var newArr = [];
        for(var i in arr) {
          if(newArr.indexOf(arr[i]) == -1) {
            newArr.push(arr[i])
          }
        }
        return newArr;
      }
    }
  }
</script>
