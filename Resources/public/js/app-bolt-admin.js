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
  var cx = $flowchart.width() / 2;
  var cy = $flowchart.height() / 2;

  // Panzoom initialization...
  $flowchart.panzoom({
    $zoomIn: $(".zoom-in"),
    $zoomOut: $(".zoom-out"),

    $reset: $(".reset"),
    
    maxScale: 0.2,
    increment: 0.05,
    contain: true
  }).panzoom();


  // Panzoom zoom handling...
  $container.on('mousewheel.focal', function( e ) {

  });
*/


    var data = {
        'grid' : 15,
        'defaultLinkColor' :'#2D3034',
        'data':{}
    };

    $flowchart.flowchart(data);

 	$.operatorI = 0;

  
    function createItem(t, pos){
        var operatorI    = $.operatorI;
        var operatorId   = 'created_operator_' + operatorI;

        var left  = (pos == undefined || pos.left == undefined)?15 * operatorI + 15:pos.left;
        var top   = (pos == undefined || pos.top == undefined)?15 * operatorI + 15:pos.top;

        var operatorData = {
            'top': top,
            'left': left,
            'properties': {
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
                }
            }
        };
    
        $.operatorI++;

        return $('#bolt-builder').flowchart('createOperator', operatorId, operatorData);
    }



    $(document).on("click",".flowchart-operator",function (e){
        e.preventDefault();  
       
        var t    = $(this);
        var name = t.find('.flowchart-operator-title').text();
        
        var url  = Routing.generate('bolt_item_load', {'name':name});
        var data = {'name':name};
        $('#item-form').loadme(true);

        $.get(url, data, function(json){
            $.form.set(json,'item_projects[base]');

            var inpuyType = $('[name="item_projects[base][type]"]');

            inpuyType.val(json.type.id);
            var txt = inpuyType.find(':selected').text();

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

        var t         = $(this);

        var dataBolt  = $('#bolt-builder').flowchart('getData');  
        var dataForm  = $('#bolt-form').serializeObject();

        var data      = {
            'bolt' : dataBolt,
            'project': dataForm
        }

        $('#bolt-form').loadme(true);
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

        $('#bolt-form').loadme(true);
        $('#bolt-builder').loadme(true);

        $.post(t.attr('href'), data, function(json, textStatus, xhr) {
            $('#bolt-builder').flowchart('setData',json.bolt);
            $.form.set(json,'projects');
            
            $('#bolt-form').loadme(false);
            $('#bolt-builder').loadme(false);

            $.operatorI = Object.keys(json.bolt.operators).length + 2;

        },'json');
    })



    $(document).on("click",".btn-tpl", function (e){
        e.preventDefault();

        var t      = $(this);
        var data   = {};
        var target = $(t.data('target'));

        target.loadme(true);
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

            //$('#sidebar-left').simplebar('recalculate');
            target.loadme(false);
        },'json');
    })


    $(document).on("change",".formFilterContent",function (e){
        e.preventDefault();  
        var t = $(this);
        var p = t.parents("form");
        var txt = t.find(':selected').text();
        
        $.form.filter(txt, p);
    })


  //$('#sidebar-left').simplebar();
});