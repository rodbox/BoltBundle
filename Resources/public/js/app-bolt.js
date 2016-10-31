(function($) {
	$.fn.bolt = function(paramSend) {
		var rand = $.rand();

		var defauts = {
			t 			: $(this),
			c			: {},
			s			: {},
			id			: 'bolt-'+rand ,
			url 		: $(this).data('url'),
			name 		: $(this).data('bolt'),
			files 		: ['single','editor','single'],
			helper 		: true,
			filter 		: true,
			strict 		: false,
			note 		: false,
			suggest 	: 'bolt-'+rand+'-suggest',
			selectID	: 'bolt-'+rand+'-select',
			initID		: 'bolt-'+rand+'-init',
			show 		: '#bolt-'+rand+'-suggest .choice-show',
			count		: 0,
			mode 		: 'init',
			fromCur		: '',
			toCur	 		: '',
			typeCur		: '',
			itemCur		: '',
			roadCur		: '',
			dataCur 	: {},
			multipleCur : true,
			stock		: {},
			pointer 	: [],
			pointerCount: 0,
			timeout 	: '',
			delay 		: 500,
			keyLoop		: false
		};
		var p		= $.extend(defauts, paramSend);

		init();

		$(this).on({
			focusin : function (e){
				clearTimeout(p.timeout);
				if(!p.c.hasClass("open")){
					p.c.addClass('bolt-suggest');
					p.c.addClass('bolt-load');

					$.ajax({
						url 	: p.url,
						type 	: 'GET',
						dataType: 'json',
						data 	: {
							name:p.name
						},
						async 	: false,
						cache 	: false,
						success : function(json, textStatus) {

							p.data = json;
							start();
							p.c.addClass("open");

						},
						error: function() {
							p.info = $.appInfo.add({
								type 	: 'error',
								open 	: true,
								msg		: 'probleme de chargement'
							});
						},
						complete:function(jq, status){
							p.c.removeClass('bolt-load');
						}
					});
				}
			},
			focusout : function (e){
				p.timeout = setTimeout(function(){
					close();
					p.c.removeClass('bolt-suggest');
				},p.delay);
				
			},
			keydown: function (e){
				var t = $(this);

				if(e.keyCode == 27){
					if(t.val()==""){
						reset();
					}
					else{
						t.val("");
						t.attr('value',"");
					}
				}
				else if(e.keyCode == 40)
					kNext();

				else if(e.keyCode == 38)
					kPrev();

				else if(e.keyCode == 13)
					valid();
				else if(e.keyCode == 32 && p.multipleCur){
					e.preventDefault();
					toggleSelect();
				}
				else{}
			},
			keyup: function(e){
				if(p.filter){
					var eval = $.inArray(e.keyCode, [40,38,13,32]);
					if (eval<0)
						filter();
				}
			}
		})

		function init(){
			p.t.parents(".bolt-group").attr('id',p.id);

			$('#'+p.id).prepend("<div id='"+p.initID+"' class='bolt-init'></div>");
			$('#'+p.id).append("<div id='"+p.suggest+"' class='bolt-suggest list-group'></div>");

			p.c = $("#"+p.id);
			p.s = $("#"+p.suggest);
			p.s.css({
				'max-height':'70vh',
				'overflow-y':'auto',
				'overflow-x':'hidden'
			})
			p.init = $("#"+p.initID);
			p.s.simplebar();
		}
		function prev(){
		}

		function next(){
		}

		function load(itemId){

		}

		function reset(){
			start();
		}

		function start(){
			clearRoad();
			p.s.html('');
			$.each(p.data.start, function(index, val) {
				var item = getItem(val);
				var itemLi = $.mustache('suggest_start',{item:item});
				
				p.s.append($(itemLi));
			});
			p.s.find("a").first().addClass('focus');
		}

		function initRoad(){
			var itemfocus = p.s.find('.focus').first().data();

			p.itemCur   = itemfocus.src;
			p.itemIdCur = itemfocus.index;
			p.roadCur	= itemfocus.roadinit;
			var metaItem = getItem(p.itemCur);
			p.tosCur  	= metaItem.tos[p.roadCur];
			//p.roadCur	= itemfocus['roads'];

			$.each(p.tosCur, function(k,v){
				p.dataCur[v]='';
			});

			p.s.simplebar('recalculate');
		}

		function clearRoad(){
			p.fromCur	= '';
			p.toCur		= '';
			p.typeCur	= '';
			p.itemCur	= '';
			p.roadCur	= '';
			p.dataCur	= {};
			p.mode 		= 'start';
			p.roadCur   = '';
		}

		function execRoad() {
			//if loop go to item
			//else clear
			console.log('exec');
			console.log(p.dataCur);

		}

		function toggleSelect() {
			p.s.find('.focus').first().toggleClass('selected');
		}


		function autoscroll() {
			// fixer le scroll a la navigation clavier
			/*var act = p.s.find('.focus');
			p.s.stop().css({'scrollTop':act.offset().top});*/
		}


		function valid(){
			var itemfocus = p.s.find('.focus').first();

			if (p.mode == 'start')
				initRoad();

			
			//init item
			// if last || alone 
			// execute
			//else{
			// load
			//}

			if(isLast(itemfocus))
				execRoad();


			var to = getTo(p.itemCur);

			var data = {
				input  : p.t.val(),
				select : itemfocus.data(),
				itemRef: p.itemCur,
				itemId : p.itemIdCur.substring(1),
				road   : p.roadCur,
				to 	   : to 
			};

			var url = Routing.generate('bolt_item');
			$.get(url, data, function(json){
				p.s.html('');
				p.mode = json.type.name;
				

				var dataClust = $.clust.init(json.data.data, 30);
				console.log('dataClust');
				console.log(dataClust);

				/**
				* TODO : Use cluster system pour les gros volumes
				**/

				$.each(json.data.data, function(k, v){
					var model 		= $.mustache('suggest_'+json.data.view, {item:v, meta:json.item});
					var valModel 	= $(model);
					valModel.attr('data-val', JSON.stringify(v));

					p.s.append(valModel);
				});

				p.s.find("a").first().addClass('focus');
				p.s.simplebar('recalculate');
				p.t.val('');
				p.t.focus();
			},'json').fail(function(err){
				$.setFlash('erreur '+ err.status,'error', err.responseText);
			});
		}

		function close(){
			setTimeout(function(){
				$('#'+p.suggest).html('');
				p.c.removeClass('open');
			},200)
		}


		$.datatype = {
			default: {
				set : function(item, data){

				},
				get : function(item){

				}
			},
			init: {
				set : function(item, data){

				},
				get : function(item){

				}
			},
			entity: {
				set : function(item, data){

				},
				get : function(item){

				}
			},
			group: {
				set : function(item, data){

				},
				get : function(item){

				}
			},
			json: {
				set : function(item,data){

				},
				get : function(item){

				}
			},
			free : {
				set : function (item, data){

				},
				get : function (item){

				}
			},
			data : {
				set : function (item, data){

				},
				get : function (item){

				}
			},
			route : {
				set : function (item, data){

				},
				get : function (item){
			
				}
			}
		}


		/**
		 * Navigation clavier suivant
		 */
		function kNext(){
			var focus = p.s.find(".focus");
			if(!focus.is(':last-child'))
				focus.removeClass('focus').next(".choice-show").addClass('focus');
			else if(p.keyLoop){
				focus.removeClass('focus');
				p.s.find(".choice-show").first().addClass('focus');
			}
			else{}
			autoscroll();
		}

		/**
		 * Navigation clavier precendent
		 */
		function kPrev(){
			var focus = p.s.find(".focus");
			if(!focus.is(':first-child'))
				focus.removeClass('focus').prev(".choice-show").addClass('focus');
			else if(p.keyLoop){
				focus.removeClass('focus');
				p.s.find(".choice-show").last().addClass('focus');
			}
			else{}
			autoscroll();
		}


		function filter (){
			var choices = p.s.find('.bolt-start-choice');
			choices.hide().removeClass('choice-show').removeClass('focus');
			p.val       = p.t.val();
			regexp();

			$.map(choices,function(val, i){
				var choice = $(val);
				var key    = choice.attr("data-key");

				var reg    = (p.strict)?"^"+p.val:p.reg;

				var patt   = new RegExp(reg, "i");
				var eval   = key.match(patt);

				if(eval){
					if(p.note)
						note(choice,eval);

                    choice.attr("data-id",i++);
					choice.show().addClass('choice-show');
				}
				else
					choice.hide().removeClass('choice-show');
				$(".choice-show").first().addClass('focus');
			})

		}

		/* créer la regexp pour trouver le resultat */
        function regexp() {
            var strReg = "";
            var strFind = p.t.val();
            var regexp = "[a-zA-Z0-9\\.\.\\s\_\-]{0,}";
            for (var i = 0; i < strFind.length; i++) strReg = strReg  + strFind[i] + "{1}(" + regexp + ")";
            p.reg = strReg;
        }
        /**
         * on note la pertinence saisie par rapport a la variable text
         */
        function note(item,matchArray){
			var text = item.attr("data-key");

			// on attribut 1pt si le 1er caractere de la saisie correspond a l'item
			var note = (p.val[0] != text[0])?1:0;

			// on attribut les notes de positions des écarts d'erreurs de la saisie chaque écart coute 1pt.
			if(matchArray.length > 0){
                // on retire le premier element match c'est la chaine trouvé
                matchArray.shift();
                // on retire le dernier il comporte tout les caractères suivant trouvé non saisie et il ne sont pas pris en compte dans le barème
                matchArray.pop();
                $.each(matchArray, function(index, val) {
                    note = note + val.length;
                });
            }

			item.attr('data-note',note);
			item.attr('data-sort',note+'__'+text);

			sortByNote();
        }

        /** on ordonne la liste en fonction de la note et de l'ordre alphabétique
		grace a la concaténation de la note et du text.
         */
        function sortByNote(){
			var itemSort = $(p.show);

			itemSort.sort(function(a,b){
				var an = a.getAttribute('data-sort');
				var	bn = b.getAttribute('data-sort');

				if(an > bn)
					return 1;
				if(an < bn)
					return -1;
				return 0;
			});

			itemSort.detach().appendTo(p.s);
        }

        function debug(id){

        }

        function suggest(item){
        	$('#'+p.suggest).html('');
        	$('#'+p.suggest).append("<div>"+item+"</div>");
        }

        function getData(itemRef){
        	return p.dataCur[itemRef];
        }

        function setData(itemRef, data) {
        	return p.dataCur.items[itemRef] = data;
        }

        function getItem(item) {
        	return p.data.items[item];
        }

        function getTo(item, road) {
        	//var item = getItem(item);
        	console.log('getTo');

        }

        function isLast(item) {
        	/*console.log('p.tosCur');
        	console.log(p.tosCur);*/
        	console.log('isLast');
        }

		$(document).on('click','a.btn-bolt-suggest',function (e){
			var t = $(this);
			if(e.ctrlKey && p.multipleCur){
				e.preventDefault();
				t.toggleClass('selected');
			}
			else{
				
				t.addClass('focus');
				valid();
				p.t.focus();
			}
		})
	}
})(jQuery);


$(document).ready(function($) {
	$('.input-bolt').bolt();
});