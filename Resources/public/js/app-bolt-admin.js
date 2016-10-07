$(document).ready(function() {
    /*
     flowchart :
     https://github.com/sdrdis/jquery.flowchart
     
     panzoom :
     http://timmywil.github.io/jquery.panzoom/
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
        'top': left,
        'left': top,
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

      $('#bolt-builder').flowchart('createOperator', operatorId, operatorData);
  }



  $(document).on("click",".create-item",function (e){
    //e.preventDefault(); 
    var t = $(this);
    createItem(t);
  });



  $(document).on("click",".flowchart-operator",function (e){
    e.preventDefault();  
   
    var t    = $(this);
    var name = t.find('.flowchart-operator-title').text();
    
    var url  = Routing.generate('bolt_item_load', {'name':name});
    var data = {'name':name};
    $('#item-form').loadme(true);
    $.get(url, data, function(json){
      $.form.set(json,'item');
      $('[name="item[type]"]').val(json.type.id);

      $('#item-form').loadme(false);
      console.log(json);
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

  var data = {
    'bolt' : dataBolt,
    'project': dataForm
  }
  $('#bolt-form').loadme(true);
  $.post(t.attr('href'), data, function(json, textStatus, xhr) {
    $('#bolt-form').loadme(false);
    
     //$.setFlash('success',json.msg);
  },'json');
})



$(document).on("click","#bolt-projects-open",function (e){
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

    $.operatorI = Object.keys(json.bolt.operators).length + 1;

    console.log($.operatorI);
  },'json');
})



  $(document).on("click",".btn-tpl",function (e){
    e.preventDefault();

    var t      = $(this);
    var data   = {};
    var target = $(t.data('target'));

    target.loadme(true);
    target.html('');
    $.post(t.attr('href'), data, function(json, textStatus, xhr) {
      $.each(json, function(key, val){
        var tpl = $.mustache(t.data('tpl'),val);
        $(t.data('target')).append(tpl);
      });

      target.loadme(false);
    },'json');
  })





    var $draggableOperators = $('.draggable_operator');
    
    function getOperatorData($element) {
      var nbInputs = parseInt($element.data('nb-inputs'));
      var nbOutputs = parseInt($element.data('nb-outputs'));
      var data = {
        properties: {
          title: $element.text(),
          inputs: {},
          outputs: {}
        } 
      };
      
      var i = 0;
      for (i = 0; i < nbInputs; i++) {
        data.properties.inputs['input_' + i] = {
          label: 'Input ' + (i + 1)
        };
      }
      for (i = 0; i < nbOutputs; i++) {
        data.properties.outputs['output_' + i] = {
          label: 'Output ' + (i + 1)
        };
      }
      
      return data;
    }
    
    var operatorId = 0;
        
    $draggableOperators.draggable({
        cursor: "move",
        opacity: 0.7,
        helper: function(e) {
          var $this = $(this);
          var data = getOperatorData($this);
          return $flowchart.flowchart('getOperatorElement', data);
        },
        stop: function(e, ui) {
            var $this = $(this);
            var elOffset = ui.offset;
            var containerOffset = $container.offset();
            if (elOffset.left > containerOffset.left &&
                elOffset.top > containerOffset.top && 
                elOffset.left < containerOffset.left + $container.width() &&
                elOffset.top < containerOffset.top + $container.height()) {

                var flowchartOffset = $flowchart.offset();

                var relativeLeft = elOffset.left - flowchartOffset.left;
                var relativeTop = elOffset.top - flowchartOffset.top;

                var positionRatio = $flowchart.flowchart('getPositionRatio');
                relativeLeft /= positionRatio;
                relativeTop /= positionRatio;
                
                var data = getOperatorData($this);
                data.left = relativeLeft;
                data.top = relativeTop;
                $flowchart.flowchart('createOperator', 'op_' + operatorId, data);
                operatorId++;
            }
        }
    });
    











})