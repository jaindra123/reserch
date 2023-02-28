(function($, Drupal) {
    // js for upload_image module : image preview after uploading
    $('body').on('change', '.institution-logo-input input', previewFile);
    function previewFile(){
        var input = this;
        var file = $(input).get(0).files[0];
        if(file){
            var reader = new FileReader();
 
            reader.onload = function(){
                $(".img-preview").attr("src", reader.result);
            }
 
            reader.readAsDataURL(file);
        }
    } 

    $(document).ajaxComplete(function() {
        var file_value = $('.grey-box-2 input[name="my_file[fids]"]').val();
        if( !file_value ) {
            $('.grey-box-2 .img-preview-wrap img').attr('src', '');
        }
    })

}) (jQuery, Drupal);