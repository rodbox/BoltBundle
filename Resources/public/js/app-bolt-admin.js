$(document).ready(function() {
    /*
     flowchart :
     https://github.com/sdrdis/jquery.flowchart
     
     panzoom :
     http://timmywil.github.io/jquery.panzoom/

     simplebar:
     https://github.com/Grsmto/simplebar
    */


    var $flowchart          = $('#bolt-builder');
    var $draggableOperators = $('.card-item');
    var $container          = $flowchart.parent();

/*
*/
  /*  var cx = $flowchart.width() / 2;
    var cy = $flowchart.height() / 2;
    
    
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
        $flowchart.flowchart('setPositionRatio', possibleZooms[currentZoom]);
        $flowchart.panzoom('zoom', possibleZooms[currentZoom], {
            animate: false,
            focal: e
        });
    });*/



    var data = {
        'grid' : 15,
        'defaultLinkColor' :'#2D3034',
        'defaultSelectedLinkColor': '#687c97', 
        'data':{
            'bolt':{
                'start':[],
                'item':[]
            }
        },
        'onLinkCreate': function(linkId, linkData) {
            $.proj.hydrate();
            return true;
          },
        'onLinkDelete': function(linkId, forced) {
            $.proj.hydrate();
            return true;
        }
    };

    $flowchart.flowchart(data);

 	$.operatorI = 0;

    $.proj = {
        data: {},
        // stockage des items de départ (group, bolt_project, function sans lien parent)
        dataStart:{},
        // stockage de l'hydratation
        dataHydrate:{},
        // hydrate toute les connexion du project
        hydrate: function(){
            $.proj.data = $flowchart.flowchart('getData');

            // on reinitialise le tableau d'hydratation
            $.proj.dataHydrate = {};
            $.each($.proj.data.links, function(key, link){
                $.proj.addItemHydrate(link);               
            })
            $.proj.getHydrate();

            $.appInfo.add({
                type:'success',
                msg:'connexion hydraté',
                timer:1000,
                open:true
            })
        },
        // Hydrate un lien
        hydrateLink:function(linkId, linkData){

        },
        // retourne la ref d'un item
        getItemRef: function (id){
            return $.proj.data.operators[id]['meta']['ref'];
        },
        getItemHydrate: function (id, data){
            return $.proj.data.operators[id]['meta']['ref'];
        },
        // ajoute un lien de ref
        addItemHydrate: function(link){
            var refFrom     = $.proj.getItemRef(link.fromOperator);
            var refTo       = $.proj.getItemRef(link.toOperator);

            var rId         = 'r'+link.fromSubConnector;

            var itemFrom          = {};
            itemFrom[rId]      = {};
            itemFrom[rId][refFrom] = {
                to:refTo
            };

            var itemTo           = {};
            itemTo[rId]      = {};
            itemTo[rId][refTo] = {
                from:refFrom
            };

            var data = _.merge(itemFrom, itemTo );
            $.proj.dataHydrate = _.merge($.proj.dataHydrate, data);

        },
        setFrom :function(ref){},
        setTo :function(ref){},
        getHydrate:function(){
            console.log("$.proj.dataHydrate");
            console.log($.proj.dataHydrate);
            return $.proj.dataHydrate;
        },
        mergeHydrate:function(data){

            $.proj.dataHydrate = data;
            console.log($.proj.dataHydrate);

            return $.proj.dataHydrate;
        },
        getStart:function(){

        }
    }
    $.proj.hydrate();

    // créer un element
    function createItem(t, pos){
        var operatorI    = $.operatorI;
        var operatorId   = 'item-' + operatorI;

        var bb = $('#bolt-builder');

        var left  = (pos == undefined || pos.left == undefined || parseInt(pos.left) < 0)?15 * operatorI + 15:pos.left;
        var top   = (pos == undefined || pos.top == undefined || parseInt(pos.top) < 0)?15 * operatorI + 15:pos.top;

        var operatorData = {
            'top': top,
            'left': left,
            'properties': {
                'id'    : operatorId,
                'title': t.data('name'),
                'class': 'type-'+t.data('type'),
                'inputs': {
                    'input_1': {
                        'label': t.data('ref')
                    }
                },
                'outputs': {
                    'output_1': {
                        'label': t.data('ref')
                    }
                },
            },
            'meta': {
                'id'    : operatorId,
                'ref'   : t.data('ref'),
                'type'  : t.data('type')
            }
        };
    
        $.operatorI++;

        var itemID = bb.flowchart('addOperator', operatorData);
        var itemData = bb.flowchart('getOperatorData', itemID);
        bb.flowchart('selectOperator', itemID);

        var item =  $('.flowchart-operator.selected');

        item.attr({
            'id'    : operatorId,
            'ref'   : t.data('ref'),
            'type'  : t.data('type')
        });

        item.trigger('click');

        return item;
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

            $.form.filter(txt, $('#item-form'));
            $('#item-form').loadme(false);
        },'json');

    })



    $('#delete_selected_button').click(function(e) {
        e.preventDefault();
        if (confirm('supprimer la selection'))
          $('#bolt-builder').flowchart('deleteSelected');
    });



    $(document).on("click", "#bolt-projects-save", function (e){
        e.preventDefault();
        $.proj.hydrate();
        
        var t         = $(this);

        var dataBolt  = $('#bolt-builder').flowchart('getData');  
        var dataForm  = $('#bolt-form').serializeObject();
        var dataHydrate  = $.proj.getHydrate();

        var data      = {
            'bolt'      : dataBolt,
            'project': dataForm,
            'hydrate': dataHydrate
        }

        $('#bolt-form').loadme(true,'','inverse');
        $.post(t.attr('href'), data, function(json, textStatus, xhr) {
            $('#bolt-form').loadme(false);
        },'json');
    })



    $(document).on("click","#bolt-projects-open", function (e){
        e.preventDefault();

        var t         = $(this);

        var dataBolt  = $('#bolt-builder').flowchart('getData');  
        var dataForm  = $('#bolt-form').serializeObject();

        var data = {
            'bolt' : dataBolt,
            'project': dataForm
        }

        $('#bolt-form').loadme(true,'','inverse');
        $('#bolt-builder').loadme(true);

        $.post(t.attr('href'), data, function(json, textStatus, xhr) {
            if(json.bolt != undefined)
                $('#bolt-builder').flowchart('setData',json.bolt);
            
            $.form.set(json,'projects');
            
            $('#bolt-form').loadme(false,'','inverse');
            $('#bolt-builder').loadme(false);

            $.operatorI = Object.keys(json.bolt.operators).length + 2;
            console.log(json.bolt);
        },'json');
    })



    $(document).on("click",".btn-tpl", function (e){
        e.preventDefault();

        var t      = $(this);
        var data   = {};
        var target = $(t.data('target'));

        target.loadme(true,'','inverse');
        target.html('');
        $.post(t.attr('href'), data, function(json, textStatus, xhr) {
          
            // each
            $.each(json, function(key, val){
                var tpl = $.mustache(t.data('tpl'),val);

                $(tpl).popover({
                    'trigger': 'hover'
                });

                $(t.data('target')).append(tpl);
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
                    var containerOffset = $('#bolt-builder').offset();

                    var flowchartOffset = $flowchart.offset();

                    var relativeLeft    = elOffset.left - flowchartOffset.left;
                    var relativeTop     = elOffset.top - flowchartOffset.top;

                    var pos = {
                        left:relativeLeft,
                        top:relativeTop
                    }

                    createItem(t.find('a.create-item'), pos);
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


    $(document).on("click","#bolt_hydrate",function (e){
        $.proj.hydrate();
    })
  
    $(document).on("change","#item_projects_all_hook",function (e){
        e.preventDefault();    
        var t = $(this);
        console.log('addItemHook');
        
    })

    $(document).on("resize",function (e){
        $('.simplebar').simplebar('recalculate');
        $('.panzoom').simplebar('recalculate');
        
        
    })

    $('.simplebar').simplebar();
    $('.panzoom').simplebar();
});