$(document).ready(function() {

    var metaProjDefault = {
        name  : 'default',
        i     : 1,
        date  : '',
        start : [],
        last  : [],
        items : {}
    };

    var operatorMetaDefault = {
        'id'          : '0',
        'name'        : 'name',
        'description' : 'content',
        'ref'         : 'ref',
        'type'        : 'type',
        'roads'       : [],
        'to'          : {},
        'tos'         : {},
        'from'        : {},
        'froms'       : {}
    }  


    /*
     flowchart :
     https://github.com/sdrdis/jquery.flowchart
     
     panzoom :
     http://timmywil.github.io/jquery.panzoom/

     simplebar:
     https://github.com/Grsmto/simplebar
    */

    // assistant de structure de model
    $.ruleModel = {
        'starts':['function','group'],
        'hook':{
            'default':[1,1],
            'function':[1,1],
            'bolt_project':[1,0],
            'group':[1,4]
        },
        'colors':[
            '#34a485',
            '#046380',
            '#323232',
            '#ff9800',
            '#dc3522',
            '#4e2472',
            '#993366',
            '#737171',
            '#ffffff',
            '#dde295'
        ]
    };

    // iterateur d'item
    $.operatorI = 0;

    // itérateur de route 
    $.roadI     = 0;

    var bb                  = $('#bolt-builder');
    var bf                  = $('#bolt-form');
    var roadSelect          = $('select#roads');
    var $container          = bb.parent();


/*
*/
  /*  var cx = bb.width() / 2;
    var cy = bb.height() / 2;
    
    
    // Panzoom initialization...
    $('.panzoom').panzoom({
        contain: true
    });
    
    // Centering panzoom
    $('.panzoom').panzoom('pan', -cx + $container.width() / 2, -cy + $container.height() / 2);

    // Panzoom zoom handling...
    var possibleZooms = [0.5, 0.75, 1, 2, 3];
    var currentZoom = 2;*/
   /* $container.on('mousewheel.focal', function( e ) {
        e.preventDefault();
        var delta = (e.delta || e.originalEvent.wheelDelta) || e.originalEvent.detail;
        var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
        currentZoom = Math.max(0, Math.min(possibleZooms.length - 1, (currentZoom + (zoomOut * 2 - 1))));
        bb.flowchart('setPositionRatio', possibleZooms[currentZoom]);
        bb.panzoom('zoom', possibleZooms[currentZoom], {
            animate: false,
            focal: e
        });
    });*/



    var data = {
        'grid' : 16,
        'defaultLinkColor' :'#2D3034',
        'defaultSelectedLinkColor': '#687c97',
        'onLinkCreate': function(linkId, linkData) {
            $.proj.hydrate();
            $.proj.setLink(linkData);
            
            return true;
          },
        'onLinkDelete': function(linkId, forced) {
            /*$.proj.hydrate()*/;
            return true;
        },
        'onAfterChange': function(e){
            if (e =='operator_data_change') {
                $.each($('.flowchart-operator-connector-label'),function(key, val){
                    $(val).attr('data-road',$(val).text());
                })
            }
        },
        'onOperatorCreate':function(operatorId, operatorData, fullElement){
            var item = $(fullElement.operator);
            
            var meta = $.proj.meta.items[operatorId];

            // si le type est un type de départ
            if ($.inArray(meta.type, $.ruleModel.starts)>=0){
                //$.proj.meta.start;.push(meta.id);

                item.attr({
                    'data-road':meta.road
                });

                // on créé un id pour chaque route
                var i = 1;
                $.each(meta.roads,function(key, road){
                    item.find('.flowchart-operator-outputs .flowchart-operator-connector-label').eq(key).attr('data-road',road).html(road);
                    $.proj.addRoad(road);
                    i++;
                })
            }
            item.attr({
                'id':operatorId
            });

            $.each(item.find('.flowchart-operator-connector-label'),function(key, label){
                $(label).attr('data-road',$(label).text());
            })


           // console.log(item.find('.flowchart-operator-title'));

            return true;
        },
        'onOperatorUnselect': function(linkId){
            setTimeout(function(){
                bb.flowchart('redrawLinksLayer');
            },50);
            return true;
        }
    };

    bb.flowchart(data);

    $.proj = {
        meta: metaProjDefault,
        data: {},
        // stockage des items de départ (group, bolt_project, function sans lien parent)
        dataLast:[],

        srcHydrate:'',
        // stockage de l'hydratation
        dataHydrate:{},
        save: function(t){
             
            $.proj.clean();
            $.proj.hydrate();
            $.proj.meta.date = new Date();

            var dataBolt  = bb.flowchart('getData');  
            var dataForm  = bf.serializeObject();
           // var dataHydrate  = $.proj.getHydrate();

            var data      = {
                'bolt'      : dataBolt,
                'meta'      : $.proj.meta,
                'project'   : dataForm,
                'date'      :  $.proj.meta.date
            }
            $.appinfoS = $.appInfo.add({
                    msg:'projet : enregisrtrement en cours',
                    open:false
                });
            $.post(t.attr('href'), data, function(json, textStatus, xhr) {
                $.appInfo.upd($.appinfoS,{});
            },'json');
        },
        open:function(t){

            var dataBolt  = bb.flowchart('getData');  
            var dataForm  = bf.serializeObject();

            var data = {
                'bolt' : dataBolt,
                'project': dataForm
            }

            $.appinfoO = $.appInfo.add({
                    msg:'ouverture ',
                    open:false
                }) 

            $.post(t.attr('href'), data, function(json, textStatus, xhr) {
                if(json.meta != undefined)
                    $.proj.meta = json.meta;
                if(json.bolt != undefined)
                    bb.flowchart('setData',json.bolt);
                
                $.form.set(json,'projects');
                
                $.appInfo.upd($.appinfoO,{});

                $.operatorI = (_.isUndefined(json.bolt) || json.bolt == null || _.isUndefined(json.bolt.operators))?1:Object.keys(json.bolt.operators).length + 2;
                /*$.proj.hydrate()*/;
            },'json');
        },
        addRoad:function(roadId){
            var opt = $("<option>",{
                "data-color":"#000",
                "value":roadId
            }).html(roadId);

            roadSelect.append(opt);
            roadSelect.val(roadId);
            roadSelect.trigger('click');
        },
        delRoad:function(roadId){
            roadSelect.find('option[value="'+roadId+'"]').remove();
        },
        clearRoads:function(){
            roadSelect.html('');
            roadSelect.trigger('click');
        },
        // hydrate toute les connexion du project
        hydrate: function(){
            $.proj.data = bb.flowchart('getData');
            $.proj.clean();

            // on reinitialise le tableau d'hydratation
            $.proj.dataHydrate  = {};

            $.proj.meta.start = [];
            $.proj.meta.last = [];

            $.each($.proj.data.links, function(key, link){
                $.proj.addItemHydrate(link);               
            })

            
            
            // on map pour trouver les extremes de chaque route
            _.map($.proj.dataHydrate, function(road){
                _.forEach(road, function(item, key){
                    if(!_.has(item,'from'))
                        $.proj.meta.start.push(key);
                    if(!_.has(item,'to'))
                        $.proj.meta.last.push(key);
                })
            })

            // on parcours tout les item de départ
            $.each($.proj.meta.start, function(k, startId){
                $.hyd = {};
                $.froms = [];
                $.each($.proj.meta.items[startId].to, function(k, v){
                    $.proj.hydrateTo(startId, k);
                })
            })

            // on parcours tout les item de départ
            $.each($.proj.meta.last, function(k, lastId){
                $.hyd = {};
                $.tos = [];

                $.each($.proj.meta.items[lastId].from, function(k, v){
                    $.proj.hydrateFrom(lastId, k);
                })
            })

            _.map($.proj.meta.items, function(item){
                if ( (_.isUndefined(item.to) || _.isEmpty(item.to)) && (_.isUndefined(item.from) || _.isEmpty(item.from))) {
                    if ($.proj.isStart(item.type))
                        $.proj.meta.start.push(item.id);
                }
            });


            $.appInfo.add({
                type:'success',
                msg:'connexion hydraté',
                timer:1000,
                open:true
            })
        },
        hydrateTo: function(itemId, roadId){
            var item = $.proj.meta.items[itemId];

            if (!_.isUndefined(item.to) && !_.isUndefined(item.to[roadId])) {
                var itemTo = $.proj.meta.items[item.to[roadId]];
                $.froms.unshift(itemId);
                
                $.hyd[itemTo.id]=$.froms.join('+');                                                                                                                     

                if(_.isUndefined(itemTo.froms))
                    itemTo.froms = {}
                if (_.isUndefined(itemTo.froms[roadId]))
                    itemTo.froms[roadId]=[];
                
                itemTo.froms[roadId]=$.hyd[itemTo.id].split('+');

                $.proj.meta.items[item.to[roadId]] = itemTo;

                $.proj.hydrateTo(itemTo.id, roadId);
            }
        },
        hydrateFrom: function(itemId, roadId){
            var item = $.proj.meta.items[itemId];
           if (!_.isUndefined(item.from) && !_.isUndefined(item.from[roadId])) {
                var itemFrom = $.proj.meta.items[item.from[roadId]];
                $.tos.unshift(itemId);
                $.hyd[itemFrom.id]=$.tos.join('+');                                                                                                                     

                if(_.isUndefined(itemFrom.tos))
                    itemFrom.tos = {}
                if (_.isUndefined(itemFrom.tos[roadId]))
                    itemFrom.tos[roadId]=[];
                
                itemFrom.tos[roadId]=$.hyd[itemFrom.id].split('+');

                $.proj.meta.items[item.from[roadId]] = itemFrom;
                $.proj.hydrateFrom(itemFrom.id, roadId);
            }
        },
       
        getItem:function(id){
            bb.flowchart('selectOperator', id);

            return $('.flowchart-operator.selected');
        },
        getMetaItem:function(id){
            return $.proj.meta.items[id];
        },
        isStart:function(type){
             return ($.inArray(type, $.ruleModel.starts)>=0);
        },
        addItemHydrate: function(link){
            var refFrom     = link.fromOperator;
            var refTo       = link.toOperator;

            var rId         = 'r'+link.fromSubConnector;

            var itemFrom    = {};
            itemFrom[rId]   = {};
            itemFrom[rId][refFrom] = {
                to:refTo
            };

            var itemTo      = {};
            itemTo[rId]     = {};
            itemTo[rId][refTo] = {
                from:refFrom
            };

            var data = _.merge(itemFrom, itemTo );
            $.proj.dataHydrate = _.merge($.proj.dataHydrate, data);
        },
        getHydrate:function(){
            return $.proj.dataHydrate;
        },
        getStart:function(){
            return $.proj.meta.start;
        },
        getRoadId:function(){
            return $('#roads').val();
        },
        getRoadColor: function(){
            return $('#roads').find(':selected').attr('data-color');
        },
        setLink: function(ld){
            $.proj.data = bb.flowchart('getData');

            var idTo          = ld.toOperator;
            var idFrom        = ld.fromOperator;

            var connectorTo   = ld.toConnector;
            var connectorFrom = ld.fromConnector;

            var itemDataFrom  = bb.flowchart('getOperatorData',idFrom);
            var itemDataTo    = bb.flowchart('getOperatorData',idTo);
            
            var itemMetaFrom  = $.proj.meta.items[idFrom];
            var itemMetaTo    = $.proj.meta.items[idTo];

            var itemTo        = $('#'+idTo);
            var itemFrom      = $('#'+idFrom);

            var roadId        = itemFrom.find('.flowchart-operator-outputs .flowchart-operator-connector-label').first().text();

            var roadSrc       = itemDataFrom.properties.outputs[connectorFrom]['label'];

            //console.log('idFrom');
            //console.log(roadId);


            itemMetaTo.roads.push(roadId);
            itemMetaTo.roads = _.uniq(itemMetaTo.roads);

            // _.isLength(value)a destination recoint le liens direct de la route
            var roadTo = {}
            roadTo[roadId] = idFrom;
            if (_.isUndefined(itemMetaTo.from))
                itemMetaTo['from'] = {};
            if (_.isUndefined(itemMetaTo.from[roadId]))
                itemMetaTo.from[roadId] = idFrom;

            var roadTo = {}
            roadTo[roadId] = idFrom;
            if (_.isUndefined(itemMetaFrom.to))
                itemMetaFrom.to = {};
            if (_.isUndefined(itemMetaFrom.to[roadId]))
                itemMetaFrom.to[roadId] = idTo;

            $.proj.meta.items[idTo] = itemMetaTo;

            itemDataTo.properties.inputs[connectorTo]['label'] = roadId;

            // item TO
            // hookTo
            var labelInput = itemTo.find('.flowchart-operator-inputs .flowchart-operator-connector-label');
            labelInput.attr('data-road', roadId);
            labelInput.html('roadId');
   
            var labelOutput = itemTo.find('.flowchart-operator-outputs .flowchart-operator-connector-label');
            
            _.map(itemDataTo.properties.outputs, function(hook){
                return hook.label = roadId;
            });         

            $.proj.hydrate();
            bb.flowchart('setOperatorData', idTo, itemDataTo);//alert('totto');


            // hook From
            var labelOutput = itemTo.find('.flowchart-operator-outputs .flowchart-operator-connector-label');
            labelOutput.attr('data-road',roadId);

            //console.log($.proj.meta);
            
        },
        setHook:function(id, type, val){
           //set un hook d'item
           /**
           * TODO : set le hook inut ou output d'un item
           **/
        },
        clear:function(){
            if (confirm('Vider le projet')) {

                $.proj.data = bb.flowchart('getData');
                $.proj.data.operators = {};
                $.proj.data.links = {};
                bb.flowchart('setData',$.proj.data);
                
                $.proj.meta = metaProjDefault;

                $.appInfo.add({
                    type:'info',
                    msg:'projet clear',
                    timer:1000,
                    open:true
                })  

                $.proj.clearRoads();
            }
        },
        clean: function(){
            // nettoie la difference de la liste des items entre meta et data 

            var kMeta = _.keys($.proj.meta.items);
            var kData = _.keys($.proj.data.operators);

            var dif = _.difference(kMeta, kData);
            $.each(dif,function(k,v){
                delete($.proj.meta.items[v]);
            })
        },
        deleteSelect: function(){
            if (confirm('supprimer la selection')){
                var id  = bb.flowchart('getSelectedOperatorId');
                var del = bb.flowchart('deleteSelected');
            
                _.without($.proj.meta.start,id);
            }
        },
        createItem: function(t, pos){
            var operatorI    =  $.operatorI;
            var operatorId   = t.data('ref') + '-' + operatorI;

            // position
            var left  = (pos == undefined || pos.left == undefined || parseInt(pos.left) < 0)?15 * operatorI + 15:pos.left;
            var top   = (pos == undefined || pos.top == undefined || parseInt(pos.top) < 0)?15 * operatorI + 15:pos.top;

            var operatorData = {
                'top'        : top,
                'left'       : left,
                'type'       : t.data('type'),
                'properties' : {
                    'id'      : operatorId,
                    'title'   : t.data('name'),
                    'class'   : 'type-'+t.data('type'),
                    'inputs'  : {},
                    'outputs' : {}
                }
            };

            var operatorMeta = {
                'id'          : operatorId,
                'name'        : t.data('name'),
                'description' : t.data('description'),
                'ref'         : t.data('ref'),
                'type'        : t.data('type'),
                'roads'       : [],
                'to'          : {},
                'from'        : {}
            }

            // Creation des outputs
            var keysModel = _.keys($.ruleModel.hook);
            // on parametre les hook de l'item en fonction du model
            var typeSrc   = ($.proj.isStart(t.data('type')))?t.data('type'):'default';

            var inputsI   = $.ruleModel.hook[typeSrc][0];
            var outputsI  = $.ruleModel.hook[typeSrc][1];

            // si le type est un type de départ
            var roadId           = 'r'+operatorId;
           

            for (var i = 1; i <= inputsI; i++) {
                var roadIdI = roadId+'-i'+i;
                operatorData.properties.inputs[roadId+'-i'+i] = {
                    'label': roadId
                }
            }

            for (var y = 1; y <= outputsI; y++) {
                var roadIdO  = roadId+'-o'+y;
                var roadIdSub = roadId+'-'+y;
                operatorData.properties.outputs[roadId+'-o'+y] = {
                    'label': roadId
                }

                // si c'est un item start
                if ($.proj.isStart(t.data('type'))){
                   // var roadColor        = $.ruleModel.colors[$.roadI];
                    operatorMeta.roads.push(roadIdSub);
                }
            }

            var metaItemPush         = {};
            metaItemPush[operatorId] = operatorMeta;
            $.proj.meta.items    = _.merge($.proj.meta.items,metaItemPush);

           
            bb.flowchart('createOperator', operatorId, operatorData);
            bb.flowchart('selectOperator', operatorId);
            
            $('.flowchart-operator.selected').trigger('click');

            $.operatorI++;
            $.proj.meta.i++;
        },
        pointers: function(itemId, sens){
            // chargement des infos meta
            var metaItem = $.proj.getMetaItem(itemId);
            var pointersSens = metaItem[sens];
            var pointerContainer = $('#item-pointers-'+sens);
            pointerContainer.html('');
            if (!_.isUndefined(pointersSens)) {
                $.each(pointersSens,function(k,v){
                    var pointersRoads = $.mustache('pointer',{road:k,pointers:v});
                    pointerContainer.append(pointersRoads);
                })
            }
        }
}


    $(document).on("click",".flowchart-operator",function (e){
        e.preventDefault();  
       
        var t    = $(this);
        var name = t.find('.flowchart-operator-title').text();
        
        var url  = Routing.generate('bolt_item_load', {'name':name});
        var data = {'name':name};
        var form = $('#item-form');
        form.loadme(true);
        $.form.clear(form);
        $.get(url, data, function(json){
            $.form.set(json,'item_projects[base]');

            var inpuyType = $('#item_projects_base_type');
            inpuyType.val(json.type.id);
            var txt = inpuyType.find(':selected').text();

            $.form.set(json.meta,'item_projects[meta]['+txt+']');
            inpuyType.val(json.type.id);

            $("#name").html(json.name);
            $("#ref").html(json.id);
            $("#type").html(txt);
            
            $.proj.pointers(t.attr('id'),'tos');
            $.proj.pointers(t.attr('id'),'froms');

            var target = '#itemsetting-tab-1';
            $('[href="'+target+'"]').trigger('click');
            $.tabFocus(target);

            $.form.filter(txt, $('#item-form'));
            $('#item-form').loadme(false);
        },'json');

    })


    $(document).on("click",".btn-boltadmin",function (e){
        e.preventDefault();    
        var t = $(this);
        $.proj[t.data('boltaction')](t, t.data());
    })


    $(document).on("click",".btn-tpl", function (e){
        e.preventDefault();

        var t      = $(this);
        var data   = {};
        var target = $(t.data('target'));

        target.loadme(true,'','inverse');
        target.html('');

        $.post(t.attr('href'), data, function(json, textStatus, xhr) {
            
            //var data = _.orderBy(json, ['type','name'], ['asc','asc']);
            var data = json;

            // each
            $.each(data, function(key, val){
                var tpl = $.mustache(t.data('tpl'),val);

                $(t.data('target')).append(tpl);
                $(tpl).popover();
            });
            // end each 

            $('.card-item').draggable({
                'cursor': "move",
                'revert': true,
                'opacity': 0.7,
                'helper':'clone',
                'rever':true,
                'revertDuration':0,
                 start : function(e, ui){
                    var t = $(this);
                    ui.helper.animate({
                        width: t.outerWidth(),
                        height: t.outerHeight()
                    });
                }, 
                'stop': function(e, ui) {

                    var t               = $(this);
                
                    var elOffset        = ui.offset;
                    var containerOffset = bb.offset();

                    var flowchartOffset = bb.offset();

                    var relativeLeft    = elOffset.left - flowchartOffset.left;
                    var relativeTop     = elOffset.top - flowchartOffset.top;

                    var pos = {
                        left:relativeLeft,
                        top:relativeTop
                    }

                    $.proj.createItem(t.find('a.create-item'), pos);
                }
            });

            $('.simplebar').simplebar('recalculate');
            target.loadme(false,'','inverse');
        },'json').fail(function(err){
            target.loadme(false);
            $.setFlash('erreur '+ err.status,'error', err.responseText);
          });
    })


    $(document).on("change",".formFilterContent",function (e){
        e.preventDefault();  
        var t   = $(this);
        var p   = t.parents("form");
        var txt = t.find(':selected').text();
        
        $.form.filter(txt, p);
    })

    $( window ).resize(function (e){
        $('.simplebar').simplebar('recalculate');
        $('#bolt-pan').simplebar('recalculate');
    })


    $(document).on("focus",".modal-edit",function (e){
        e.preventDefault();    
        var t = $(this);
        var rand = Math.random().toString(36).substring(2);
        var cloneId = t.attr('id')+'-clone-rand';
        var clone = t.clone();

        var editor = (_.isUndefined(t.data('editor')))?'codemirror':t.data('editor');

        clone.attr({
            id:'modal-ediror',
            link:'#'+t.attr('id'),
            autofocus:true,
            'data-tab':true
        })
        .removeClass('modal-edit')

        clone.val(t.val());
        var content = clone;
        
        

        $.modal.html(content,'modalLg','Coder : '+$('#item_projects_base_name').val()+' <span class="text-muted">e'+$('#item_projects_base_id').val()+'</span> <span class="text-muted pull-right">'+t.data('cm-mode')+'</span>');
        
        if(t.data('editor') == 'codemirror'){
            var cm = CodeMirror.fromTextArea(document.getElementById("modal-ediror"),{
                theme           : "tomorrow-night-bright",
                lineNumbers     : true,
                styleActiveLine : true,
                tabSize         : 8,
                lineWrapping    : true,
                mode            : t.data('cm-mode'),
                gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                keyMap          : "sublime",
                autoCloseBrackets: true,
                extraKeys: {
                    "Ctrl-Alt": "route",
                    "Ctrl-Space": "autocomplete"
                },
                matchBrackets   : true
            });
            cm.on("update",function (editor, change){
                t.val(cm.getValue());
            });
            cm.focus();
            var pointers = $('#item-pointers').clone();
            pointers.attr('id','pointer-helper');
            $('.modal-body').prepend(pointers);
        }
        else{
            clone.focus();
        }
        //$.cdn.load(editor ))
        //$.cdn.load(editor);
    })

    $(document).on("change",".color-me",function (e){
        e.preventDefault();    
        var t = $(this);
        t.css('background-color',t.val()+'!important');
    })


    $(document).on("change click","#roads",function (e){
        e.preventDefault();

        var t      = $(this);
        var color  = t.find(':selected').attr('data-color');
       
       // live style
        $('body').attr('data-road',t.val());
        $('#live-style').html($.mustache('road_css',{'road':t.val()}));

        // change metaroad
        var linkId = bb.flowchart('getSelectedLinkId');
        if (linkId != undefined)
            bb.flowchart('setLinkMainColor',linkId, color);

    })

    $(document).on("keydown",function (e){
        var t = $(this);

        if (
            ($('body').hasClass('modal-open') == false) && (
            $('.flash-container').is(':hover') ||
            $('#bolt-builder').is(':hover') ||
            $('#sidebar-inner').is(':hover'))
            ){
            if (e.keyCode =='46') {
                $('#delete_selected_button').trigger('click');
            }
            if (e.keyCode =='83' && e.ctrlKey) {
                e.preventDefault(); 
               $('#bolt-projects-save').trigger('click');
            }
            if (e.keyCode =='78' && e.altKey) {
                e.preventDefault(); 
               $('#bolt-projects-new').trigger('click');
            }
            if (e.keyCode =='27') {
              //  e.preventDefault();
              //console.log('fezfez'); 
                $('.flash-error').find('close-flash').trigger('click');
              //s $('#bolt-projects-clear').trigger('click');
            }
            if (e.keyCode =='49') {
                e.preventDefault(); 
                var target = '#itemsetting-tab-1';
                $('.nav-link[href="'+target+'"]').trigger('click');
                $.tabFocus(target);
            }

            if (e.keyCode =='50') {
                e.preventDefault(); 
                var target = '#itemsetting-tab-all';
                $('.nav-link[href="'+target+'"]').trigger('click');
                $.tabFocus(target);
            }
            if (e.keyCode =='51') {
                e.preventDefault(); 
                var contentFilter = $('#item-form').attr('data-form-filter-content');
                var link = $('.nav-item[data-form-filter-content="'+contentFilter+'"]').find('a.nav-link');
                link.trigger('click');
                var target = link.attr('href');
                $.tabFocus(target);
            }

            if(e.altKey && e.ctrlKey)
                $('.input-bolt').focus();
        }   
    })


    $.tabFocus = function(id){
        console.log('focus');
        $(id).find('textarea, input[type="text"], select').first().focus();
    }


    $(document).on("change",".set-data-entity",function (e){
        e.preventDefault();    
        var t = $(this);
        var url = Routing.generate('entity_meta');
        var data = {
            entity: t.val()
        }
        $.get(url,data,function(json){
            var select = $('#item_projects_meta_entity_index_value');
            select.html('');
            $.each(json.list,function(k, v){
                var opt = $("<option>",{"value":v}).html(v);
                select.append(opt);
            })
        },'json');
        
    })

    $(document).on("keypress","#item_projects_base_name",function (e){
        var t = $(this);
        var itemLink = $('#item_projects_base_id').val();
        $('[data-item="'+itemLink+'"]').html(t.val());
    })



    $(document).on("click mousenter",".item-helper",function (e){
        e.preventDefault(); 
        var t = $(this);
        t.toggleClass('active');
        if (t.hasClass('active') || e.type == 'mousenter') {
            var rand = $.rand();
            var helperId = 'helper-'+rand;
            t.attr('data-helper-id','#'+helperId);
            var itemId = t.data('item');
            var data = $.proj.getMetaItem(itemId);
            console.log(data);
            var helper = $($.mustache('pointer-helper',data));
            var pos = t.offset();
            var close = $("<a>",{"href":"#","class":"helper-close absolute"});
            close.css({
                'right':'0.6rem',
                'color':t.css('color'),
                'opacity':0.3,
                'z-index':6001
            });
            close.html('<i class="fa fa-remove"></i>');
            close.click(function(e){
                e.preventDefault();
                t.removeClass('active');
                helper.remove();
                t.attr('data-helper-id','');
            })
            helper.prepend(close);
            helper.attr('id',helperId);
            helper.addClass('item-helper-content');
            helper.css({
                'top': pos.top,
                'left': parseInt(t.outerWidth())+ parseInt(pos.left),
                'z-index':6000,
                'position':'fixed',
                'background-color':t.css('background-color'),
                'color':t.css('color')+'!important'
            });
            $('body').append(helper);
        }
        else
            $(t.attr('data-helper-id')).remove();
    })


    $(document).on("change",".filter",function (e){
        e.preventDefault();    
        var t = $(this);
        var srcContent = $('#item_projects_meta_data_filter-content');
        var valContent = srcContent.val();


        // var t            = $(this);
        // var src          = $(t.attr('href'));;
        // var textNoFilter = src.val();
        // var filter       = t.data('filter');
        // var urlexp       = new RegExp($.reg[filter], 'ig');

        // var list         = textNoFilter.match(urlexp);
        // var listClean    = $.cleanArray(list);

        // var container    = $(src.data('target'));
        // $.each(listClean, function(index, val) {
        //     $.proto(container,{'value':val});
        // });
        // src.val('');



        
        /*var select = $('#item_projects_meta_entity_index_value');
            select.html('');
            $.each(json.list,function(k, v){
                var opt = $("<option>",{"value":v}).html(v);
                select.append(opt);
            })
        */
    })


    $('.color-me').trigger('change');
    $('.simplebar').simplebar();
    $('#bolt-pan').simplebar();
    
    //$.cdn.load('codemirror');
 
});
