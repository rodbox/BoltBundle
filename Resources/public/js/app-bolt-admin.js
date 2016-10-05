$(document).ready(function() {
    // documentation flowchart 
    // https://github.com/sdrdis/jquery.flowchart

    

  $(document).ready(function() {
    var $flowchart = $('#bolt-builder');
    var $container = $flowchart.parent();
    
    var cx = $flowchart.width() / 2;
    var cy = $flowchart.height() / 2;
    
    
    // Panzoom initialization...
    $flowchart.panzoom();
    
    // Centering panzoom
    $flowchart.panzoom('pan', -cx + $container.width() / 2, -cy + $container.height() / 2);

    // Panzoom zoom handling...
    var possibleZooms = [0.5, 0.75, 1, 2, 3];
    var currentZoom = 2;
    $container.on('mousewheel.focal', function( e ) {
        e.preventDefault();
        var delta = (e.delta || e.originalEvent.wheelDelta) || e.originalEvent.detail;
        var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
        currentZoom = Math.max(0, Math.min(possibleZooms.length - 1, (currentZoom + (zoomOut * 2 - 1))));
        $flowchart.flowchart('setPositionRatio', possibleZooms[currentZoom]);
        $flowchart.panzoom('zoom', possibleZooms[currentZoom], {
            animate: false,
            focal: e
        });
    });



    var data = {
      grid : 15,
      defaultLinkColor :'#2D3034'
    };
    $('#bolt-builder').flowchart(data);

 	var operatorI = 0;



 	$(document).on("click",".create-item",function (e){
 		e.preventDefault();	
 		var t = $(this);
 		
 		var operatorId = 'created_operator_' + operatorI;
  	var operatorData = {
        top: 15 * operatorI,
        left: 15 * operatorI,
        properties: {
          title: t.data('name'),
          class: 'type-'+t.data('type'),
          inputs: {
            input_1: {
              label: t.data('ref'),
            }
          },
          outputs: {
            output_1: {
              label: t.data('ref'),
            }
          }
        }
      };

      operatorI++;

      $('#bolt-builder').flowchart('createOperator', operatorId, operatorData);
 	})



   $(document).on("click",".flowchart-operators-layer",function (e){
    e.preventDefault();  
    var t = $(this);
         
   })



    $('#delete_selected_button').click(function() {
      if (confirm('supprimer la selection'))
        $('#bolt-builder').flowchart('deleteSelected');
    });



    $(document).on("click","#bolt-projects-save",function (e){
      e.preventDefault();  
      
      var t         = $(this);
      
      var dataBolt  = $('#bolt-builder').flowchart('getData');  
      var dataForm  = $('#bolt-form').serializeObject();

      var data = {
        bolt : dataBolt,
        project: dataForm
      }

      console.log(data);
      console.log(dataBolt);

      $.post(t.attr('href'), data, function(json, textStatus, xhr) {
         $.setFlash('success',json.msg);
      },'json');
    })



    $(document).on("click","#bolt-projects-open",function (e){
      e.preventDefault();  
      var t         = $(this);
      
      var dataBolt  = $('#bolt-builder').flowchart('getData');  
      var dataForm  = $('#bolt-form').serializeObject();

      var data = {
        bolt : dataBolt,
        project: dataForm
      }

      $.post(t.attr('href'), data, function(json, textStatus, xhr) {
         $('#bolt-builder').flowchart('setData',json.bolt); 
      },'json');
    })



      $(document).on("click",".btn-tpl",function (e){
        e.preventDefault();  
        var t     = $(this);
        var data  = {};

        $.post(t.attr('href'), data, function(json, textStatus, xhr) {
            $(t.data('target')).html('');
            $.each(json, function(key, val){
                var tpl = $.mustache(t.data('tpl'),val);
                $(t.data('target')).append(tpl);
            })
        },'json');
        
      })
    
    
})