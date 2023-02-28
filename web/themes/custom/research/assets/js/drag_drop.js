
$(init);
function init() {
  $(".dragzones").draggable({
    start: handleDragStart,
    cursor: 'move',
    revert: "invalid",
  });
  $(".dropzones").droppable({
    drop: handleDropEvent,
    tolerance: "touch",              
  });
  validateDropzones();
}
function handleDragStart (event, ui) {
  $(this).css('z-index', 9999 );
}
let  drag_data_value = [];       
function handleDropEvent (event, ui) {
  var current_dropzone = $(ui.draggable[0]).parent();
  //console.log(ui);
 // console.log(ui.draggable[0].id);
  var id = ui.draggable[0].id;
  if ($.inArray(id, drag_data_value) == -1) {
    drag_data_value.push(id);
    // set current dropzone width to auto
    $(current_dropzone).css('width', 'auto');
  }
  console.log('drag_data_value',drag_data_value);
  $.cookie('draggedData', drag_data_value);                  // set a cookie
  var drag_data_values = $.cookie('draggedData');           // Read the cookie

  if ($(this).hasClass('occupied')) {
    ui.draggable.draggable('option', 'revert', true);
    return false;
  }
  $(this).append(ui.draggable);
  ui.draggable.position({of: $(this), my: '', at: ''});
  ui.draggable.css('z-index', 0);
  setTimeout(validateDropzones, 0);
  //alert('cc');
}

function validateDropzones() {
  $(".dropzones").each(function(){
    if ($(".dragzones", this)[0]) {
      $(this).addClass("occupied");
    } else {
      $(this).removeClass("occupied");
    }
  });
}
