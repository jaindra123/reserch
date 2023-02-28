
$(function() {
  let  drag_data_value = [];
  $( "#sortable1, #sortable2" ).sortable({
    receive: function(event, ui) {
      var id = ui.item.attr("id");
      drag_data_value.push(id);
      console.log('drag_data_value',drag_data_value);
      $.cookie('draggedData', drag_data_value);                  // set a cookie
      var drag_data_values = $.cookie('draggedData');         // Read the cookie
     // alert(drag_data_values);
      //console.log('All Dragged Data Value',drag_data_values);
    },
    connectWith: ".connectedSortable"
  })
});
 