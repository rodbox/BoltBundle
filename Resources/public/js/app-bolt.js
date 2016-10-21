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
			show 		: '#bolt-'+rand+'-suggest .choice-show',
			count		: 0,
			current		: 0,
			from		: '',
			typeCurrent	: '',
			stock		: {},
			pointer 	: [],
			pointerCount: 0
		};
		var p		= $.extend(defauts, paramSend);

		init();

		$(this).on({
			focusin : function (e){
				e.preventDefault();
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
						/*p.data  = json.app;
						p.count = json.app.start.length;
						p.start = json.app.start;
						p.items = json.app.items;*/
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
			},
			focusout : function (e){
				close();
				p.c.removeClass('bolt-suggest');
			},
			keydown: function (e){
				var t = $(this);

				if(e.keyCode == 27){
					if(t.val()=="")
						reset();
					else
						t.val("");
				}
				else if(e.keyCode == 40)
					focusNext();

				else if(e.keyCode == 38)
					focusPrev();

				else if(e.keyCode == 13)
					valid();
			},
			keyup: function(e){
				if(p.filter){
					var eval = $.inArray(e.keyCode, [40,38,13]);
					if (eval<0)
						filter();
				}
			}
		})

		function init(){
			//p.t.addClass("bolt-input");
			p.t.parents(".bolt-group").attr('id',p.id);

			$('#'+p.id).append("<div id='"+p.suggest+"' class='bolt-suggest list-group'></div>");

			p.c = $("#"+p.id);
			p.s = $("#"+p.suggest);

			//select();
		}

		function from(fromID){
			p.pointer = p.items[fromID].pointer;
			p.t.val(itemRoad);
			p.from    = itemRoad;
			p.pointerCount = Object.keys(p.pointer).length;

			if(p.pointer)
				item(pointerID(0));
			else{
				p.pointer      = [];
				p.pointerCount = 0;

				go();
			}

		}

		function next(){
			if(p.current<p.pointerCount){
				p.current++;
				item(pointerID(p.current));
			}
			else
				go();
		}

		function prev(){

		}

		function reset(){
			p.current 	= 0;
			$.stock.reset();
			p.pointer 	= [];
			p.from		= '';
			start();
		}

		function list(){
			var itemList = p.items[item];
		}

		function start(){
			p.s.html('<div class="bolt-step">');
			$.each(p.start, function(index, val) {
				var itemLi = $('<a>',{
					href 			: '#',
					class 			: 'btn-bolt-start list-group-item bolt-start-choice choice-show',
					'data-key'		: p.items[val].title,
					'data-index'	: index,
					'data-data'		: val,
					'data-type'		: 'start'
				});
				var title = $('<h4>',{ class:'list-group-item-heading'}).html(p.items[val].title);
				itemLi.append(title);

				if(p.helper){
					var helper = $('<p>',{class:'list-group-item-text helper-text'}).html(p.items[val].helper);

					itemLi.attr({
						'data-container': p.id+'.bolt-wrapper a[data-index='+index+']',
						'data-content'	: p.items[val].helper,
						'data-placement': 'right',
						'data-toggle'	: 'popover'
					})
					itemLi.append(helper);
				}

				p.s.append(itemLi);
				p.s.append('</div>');
			});
			p.s.find("a").first().addClass('active');
		}

		function valid(){
			if(p.t.val()!=""){

				alert(p.t.val())
				var data = {
					val : p.t.val(),
					suggest: $('#'+p.suggest).find('.active').attr('data-data')
				}
				$.post('/i/load',data, function(json) {
					/*optional stuff to do after success */
				},'json');
			}



			// p.s.find(".active").trigger("click");
			// var itemVal = p.t.val();
			// // console.log(pointerID(p.current));
			// $.stock.push(pointerID(p.current),itemVal);
			// p.t.val("");

			// next();
		}

		function close(){
			$('#'+p.suggest).html('');
			p.c.removeClass('open');
		}

		/**
		 * Charge l'item en cours
		 */
		function item(id){
			console.log(id);
			p.itemCurrent = p.items[id];
			p.typeCurrent = p.itemCurrent.type;
			// $.itemLoadType[p.typeCurrent](id);
		}


		

		$.itemLoadType = {
			free : function (id){
				console.log("item load free");

				p.s.html("");
				// console.log(p.itemCurrent);
			},
			data : function (id){
				console.log("item load data");
				console.log(p.itemCurrent);
			},
			route : function (id){
				console.log("item load route");
				console.log(p.itemCurrent);
			}
		}

		$.stock = {
			push	: function(key,val){
				var data  = {}
				data[key] = val;
				p.stock   = $.extend(p.stock, data);
			},
			reset	: function(){
				p.stock = {};
			},
			extract	: function(){
				var str = "";
				$.each(p.stock, function(index, val) {
					str = str + " var "+index+" = '"+val+"';";
				});

				return str;
			}
		}

		function pointerID(step){
			return Object.keys(p.pointer)[step];
		}

		/**
		 * Execute la fonction en cours avec les parametres stockées
		 */
		function go(){
			eval($.stock.extract());
			eval(p.items[p.from].function);
		}

		/**
		 * Navigation clavier suivant
		 */
		function focusNext(){
			var active = p.s.find(".active");
			if(!active.is(':first-child'))
				active.removeClass('active').trigger("mouseleave").next(".choice-show").addClass('active').trigger("mouseover");
			else
				p.s.find("a").eq(1).addClass('active');
		}

		/**
		 * Navigation clavier precendent
		 */
		function focusPrev(){
			var active = p.s.find(".active");

				active.removeClass('active').trigger("mouseleave").prev(".choice-show").addClass('active').trigger("mouseover");
		}


		function filter (){
			var choices = p.s.find('.bolt-start-choice');
			choices.hide().removeClass('choice-show').removeClass('active');
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
				$(".choice-show").first().addClass('active');
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
			console.log('__DEBUG__:' + id);
			console.log('## Count :');
			console.log(p.count);
			console.log('## Current :');
			console.log(p.current);
			console.log('## From :');
			console.log(p.from);
			console.log('## ItemCurrent :');
			console.log(p.itemCurrent);
			console.log('## TypeCurrent :');
			console.log(p.typeCurrent);
			console.log('## Stock :');
			console.log(p.stock);
			console.log('## Pointer :');
			console.log(p.pointer);
			console.log('## PointerCount :');
			console.log(p.pointerCount);
			console.log('p');
			console.log(p);
			console.log('__END__');
        }

		$(document).on('click','.btn-bolt-suggest',function (e){
			e.preventDefault();
			p.t.val($(this).data('data'));
		})

		$(document).on('click','.btn-bolt-start',function (e){
			e.preventDefault();

			from($(this).data('data'));
		})
	}
})(jQuery);


$(document).ready(function($) {

	$('.input-bolt').bolt();

});