Vue.component('suggest', {
	props:['name'],
  	template:`
    	<a href="#" class="list-group-item" @click="to()">{{name}}</a>
	`
})

let vm = new Vue({
    el:'#bolt-input',
    template:`
    <div>
    	<div class="bolt-container" style="text-align:left !important;" :data-multiple="multiple">
    		<input id="multiple" type="checkbox" value="true" v-model="multiple" /> <label for="multiple" class="sm">Multiple</label>
    		<span class="text-multiple" style="">multiple</span>
	    	<div class="tag-list" style="height:30px;">
	    	<span class="tag tag-default" :class="check" v-for="itemroad in itemsroad" style="margin:0.3rem 0.1rem;" >{{itemroad}}</span>
	    		<span class="count-selected" :data-count="countselected">{{countselected}}</span>
	    	</div>
		    <div class="input-group input-group-lg bolt-group">
				<input type="text" class="form-control" @focus.prevent="init()" :data-multiple="multiple" @blur.prevent="storage()" @keyup="filter()" :placeholder="name" @keydown.13="submit()" @keydown.38.prevent="kPrev()" @keydown.40.prevent="kNext()" @keydown.37.prevent="kLeft()" @keydown.39.prevent="kRight()" @keydown.27.prevent="clear()" @keydown.32="toggleselect()" @keyup.32="cleaninput()" v-model="inputvalue" style="border-right-width:0px; text-align:left !important;" autofocus=true  />
				<div class="input-group-addon" style="background-color:#fff;"><i class="fa fa-circle-o-notch bolt-signal" :class="cls()"></i></div>
		    </div>
		    <div :id="name" class="bolt-suggest list-group" style="margin-top: 0.3rem; position: fixed; width: calc(80% - 0.5rem);">
				<a href="#" class="list-group-item" v-for="datafilter in datasfilter" :data-type="datafilter.type.name" :data-id="datafilter.id" @click.prevent="initItem(datafilter.id)" @click.prevent="clicktoggleselect"><h4 class="list-group-item-heading">{{datafilter.name}}
						<span class="text-muted"></span>
					</h4>
					<p class="list-group-item-text helper-text">
						{{datafilter.description}}
					</p>
				</a>
		    </div>
    	</div>
    </div>
	`,
    data:{
    	keyloop      : false,
    	focus        : false,
    	load         : false,
    	focusstorage : false,
    	multiple 	 : true,
    	name         : 'Default',
    	road         : '',
    	item         : '',
    	itemsroad    : [],
    	inputvalue   : '',
    	data         : [],
    	datasfilter  : [],
    	regfilter 	 : '',
    	view         : '',
    	countselected: 0,
    	itemselected : {},
    	bolt         : Object
    },
    computed: {
    	check:function(itemId){
	   		console.log('check');
	   	}
    },
    methods:{
    	init: function (e){
			var url = Routing.generate('bolt_player',{name:this.name});
    		this.focus=true;

    		$.get(url,function(json){
    			vm.data       = json.data
    			vm.view       = json.view
    			vm.setData(vm.data)

    			$(vm.$el).find('.list-group-item').removeClass('focus').first().addClass('focus')
    		},'json');
	   	},
	   	setData: function (data) {
	   		return vm.datasfilter = data;
	   	},
	   	initItem: function(itemId) {
	   		console.log($('.bolt-suggest [data-id="'+itemId+'"]').data());
   			return vm.itemsroad = ['e321','e320','e323','e325']
	   	},
	   	clearRoad: function() {
	   		return vm.itemsroad = []
	   	},
	   	cleaninput: function (e) {
	   		return vm.inputvalue = vm.inputvalue.replace(/\s\s/g,' ');
	   	},
	   	storage: function(e) {
    		vm.focus=false;
    		vm.focusstorage=true;
	   	},
	   	filter: function(e) {
	   		vm.regexp();

	   		let datafilter = {};
	   		$.each(vm.data, function(k,v){
	   			let patt   = new RegExp(vm.regfilter, "i")
				let eval   = v.name.match(patt)

				if (eval)
					datafilter[k] = v;
	   		})

	   		vm.setData(datafilter)

	   		return vm.inputvalue.trim();
	   	},
	   	focus: function(){
			var focus = $(vm.$el).find(".focus")
			if(focus.length<1)
	   			$(vm.$el).find('.list-group-item').removeClass('focus').first().addClass('focus')
	   	},
	   	kPrev: function(e) {
			var focus = $(vm.$el).find(".focus")
			if(!focus.is(':first-child'))
				focus.removeClass('focus').prev('a').addClass('focus')
			else if(vm.keyLoop){
				focus.removeClass('focus')
				p.s.find('a').last().addClass('focus')
			}
			else{}
			if(focus.length<1)
	   			$(vm.$el).find('.list-group-item').removeClass('focus').first().addClass('focus')  		
	   	},
	   	kNext: function(e) {
	   		var focus = $(vm.$el).find(".focus");
			if(!focus.is(':last-child'))
				focus.removeClass('focus').next('a').addClass('focus');
			else if(vm.keyLoop){
				focus.removeClass('focus');
				p.s.find('a').first().addClass('focus');
			}
			else{}

			if(focus.length<1)
	   			$(vm.$el).find('.list-group-item').removeClass('focus').first().addClass('focus')
	   	},
	   	kLeft: function(e){
	   		var focus = $(vm.$el).find(".focus");
   			console.log('left');

	   	},
	   	kRight: function(e){
	   		var focus = $(vm.$el).find(".focus").first();
	   		console.log(focus);
	   		if (focus.has('[data-type="group"]')) 
	   			console.log('right');
	   	},
	   	submit: function(e){
	   		let url  = Routing.generate('bolt_item',{name:vm.name, road:vm.road, item:vm.item});
	   		vm.load  = true;
	   		let dataSuggested = {};

	   		if(vm.multiple){
	   			let suggested = $('.bolt-suggest .selected')
	   			if (suggested.length < 1)
	   				dataSuggested = $('.bolt-suggest .focus').data()
	   			else{
	   				$.each(suggested, function(k,v){
	   					dataSuggested[k] = $(v).data()
	   				})
	   			}
	   		}
	   		else
	   			dataSuggested = $('.bolt-suggest .focus').data()

	   		let data = {
    			val : vm.inputvalue,
    			suggest: dataSuggested
    		}
    		$.get(url, data, function(json){
	    		vm.load=false;
	    		vm.initRoad();
    			console.log(json);
    		},'json');
    		console.log(url);
	   		vm.to();
	   		return vm.inputvalue='';
	   	},
	   	execute:function (e) {
	   		console.log('bolt_road_execute')
	   		vm.focusstorage=false
	   		
	   		var url = Routing.generate('bolt_road_execute')
    		vm.load=true
    		
    		$.get(url, function(json){
	    		vm.load=false
	    		vm.init()
    		},'json');
	   		vm.value='';
	   	},
	   	from:function () {
	   		console.log('from');
	   		vm.init();
	   		vm.rotate('-360');
	   	},
	   	to:function () {
	   		console.log('to');

	   		vm.execute();
	   		vm.rotate('+360');
	   	},
	   	clear: function(e){
			if(vm.inputvalue==''){
				vm.from()
				vm.clearRoad()
			}

			vm.clearselected()
			return vm.inputvalue=''
	   	},
	   	clearselected: function (e) {
	   		$('.bolt-suggest .selected').removeClass('selected');
	   		return vm.countselected = 0;
	   	},
	   	cls: function 	() {
	   		return this.focus ? 'fa-rotate-180':''
	   		console.log('cls');
	   	},
	   	toggleselect:function(e){
	   		vm.cleaninput()
	   		if (vm.multiple){
	   			$('.focus').toggleClass('selected')
	   			return vm.countselected = $('a.selected').length;
	   		}
	   	},
	   	clicktoggleselect:function(e){
	   		if (vm.multiple)
	   			$('a.list-group-item:hover').toggleClass('selected')
   			
	   		if (e.ctrlKey)
	   			console.log("check ID pour selectionné l'interval");

   			return vm.countselected = $('a.selected').length;
	   	},
        regexp: function () {
	   		/* créer la regexp pour trouver le resultat */
            var strReg  = "";
            var strFind = vm.inputvalue.replace(/\s/g,'');
            var regexp  = "[a-zA-Z0-9\\.\.\\s\_\-]{0,}";

            for (var i = 0; i < strFind.length; i++) strReg = strReg  + strFind[i] + "{1}(" + regexp + ")";
            return vm.regfilter = strReg;
        },
        rotate: function (deg) {
			let icon   = $('.bolt-signal');
			let tr     = icon.css('transform')
			let values = tr.split('(')[1].split(')')[0].split(',');
			let a      = values[0];
			let b      = values[1];
			let c      = values[2];
			let d      = values[3];

			let angle = Math.round(Math.atan2(b, a) * (180/Math.PI));
			let newangle = (parseInt(angle) + parseInt(deg))-360;
			icon.css('transform','rotate('+newangle+'deg)')
        }

    }
})