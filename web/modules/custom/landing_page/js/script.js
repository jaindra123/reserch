(function($, Drupal) {
    
    $(document).ready(function(){

        // make type submit to button
        $(document).ajaxComplete(function() {
            $('input[id^="edit-card-group-btn-"]').each(function(e) {
                $(this).attr('type', 'button');
            });
            updateCardTitle();
        });
        $('input[id^="edit-card-group-btn-"]').each(function(e) {
            $(this).attr('type', 'button');
        });

        $('.card-group-title').each(function(){
            let title_value = $(this).find('input').val();
            if( title_value == '' ) {
                $(this).closest('.card-group-wrapper').addClass('hide');
                $(this).closest('.card-group-wrapper').removeClass('show');
            } else {
                $(this).closest('.card-group-wrapper').addClass('show');
                $(this).closest('.card-group-wrapper').removeClass('hide');
            }
        });

        // hide show for card-group-title on click
        $('body').on('click', 'input[id^="edit-card-group-btn-"]', function(e) {
            let id_nmbr = $(this).attr('id').replace('edit-card-group-btn-', '');
            let card_group_wrap = $(this).closest('.card-group-wrapper');
            if( card_group_wrap.hasClass('hide') ) {
                card_group_wrap.removeClass('hide');
                card_group_wrap.addClass('show');
                $(this).val('Remove Group Title');
            } else {
                card_group_wrap.removeClass('show');
                card_group_wrap.addClass('hide');
                $(this).val('Add Group Title');
                $(this).closest('.card-group-wrapper').find('.form-text').val('');
            }
            
        });

        function updateCardTitle() {
            $('.card-group-btn input').each(function() {
                let card_group_wrap = $(this).closest('.card-group-wrapper');
                if( card_group_wrap.hasClass('hide') ) {
                    $(this).val('Add Group Title');
                } else {
                    $(this).val('Remove Group Title');
                }
            });
        }
        updateCardTitle();

    });

}) (jQuery, Drupal)