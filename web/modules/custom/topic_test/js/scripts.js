(function($, Drupal) {
    
    $(document).ready(function(){

        $('#finish_test').click(function(){
            let form = $('form.questions-test');
            let form_data = form.serializeArray();
            form.hide();
            $('.content-without-questions').hide();

            jQuery.ajax({
                url: '/test-result',
                type: 'POST',
                data: form_data,
                success: function(response){
                    $(window).scrollTop(0);
                    $(response).insertAfter(form);
                },
            });
            

        });

        // managing test results as per the user mode

        $('body').on('click', '.finishText-wrapper #confirm-result' , function() {
            // alert('hello');
            let user_mode = $('.main-markup').attr('id');
            if( user_mode == 'lti-users-mode' ) {
                let form = $('form.questions-test');
                let form_data = form.serializeArray();
                form_data.push({
                    'name': 'result',
                    'value': 'confirmed'
                });
                form.hide();
                $('.finishText-wrapper').hide();

                jQuery.ajax({
                    url: '/test-result',
                    type: 'POST',
                    data: form_data,
                    success: function(response){
                        $(window).scrollTop(0);
                        jQuery('.subcategories_main').html('');
                        $(response).insertAfter(form);
                    },
                });
            }
        });

        // back page on click of back for lti users mode
        $('body').on('click', '.finishText-wrapper #back-to-form' , function() {
            let user_mode = $('.main-markup').attr('id');
            if( user_mode == 'lti-users-mode' ) {
                $(window).scrollTop(0);
                $('form.questions-test').show();
                $('.content-without-questions').show();
                $('.finishText-wrapper').hide();
            }
        });

        // refresh the page on click of retake test on self-study mode
        $('body').on('click', '#self-retake', function(){
            location.reload();
            $(window).scrollTop(0);
        });

        // retake test confirmation page for lti users
        $('body').on('click', '#lti-retake', function(){
            $(window).scrollTop(0);
            $('form.questions-test').hide();
            $('.finishText-wrapper').hide();
            $('.main-markup').hide();
            $('.retake-test-confirmation').show();
        });
        // Please confirm that you wish to overwrite your previous attempts.
        $('body').on('click', '#retake-test-confirm-btn', function(){
            $(window).scrollTop(0);
            let confirm_retake = $('input[name="confirm_retake_test"]').val();
            // here we will replace the current result with previous one on VLE board.
        });
        
        // backend topic test node page edit

        $('body').on('click', '.field--name-field-option-single input[type="checkbox"]', function(){
            let is_checked = $(this).prop('checked');
            $(this).closest('body .field--name-field-option-single').find('input[type="checkbox"]').prop('checked', false);
            $(this).prop('checked', true);
        });

        // manage-topictest
        let checked_input = $('.question-topic-item input:checked');
        console.log(checked_input.val());
        if( checked_input.val() == 'Restrict topic tests for LTI users only' ) {
            $('.attempt-box').show();
        } else {
            $('.attempt-box').hide();
        }

        $('.question-topic-item input').click(function() {
            let checked_value = $('.question-topic-item input:checked').val();
            if( checked_value == 'Restrict topic tests for LTI users only' ) {
                $('.attempt-box').show();
            } else {
                $('.attempt-box').hide();
            }
        }); 

        // drag and drop
        init();

    });


    // code for drag and drop

    function touchHandler(event) {
        var touch = event.changedTouches[0];
    
        var simulatedEvent = document.createEvent("MouseEvent");
            simulatedEvent.initMouseEvent({
            touchstart: "mousedown",
            touchmove: "mousemove",
            touchend: "mouseup"
        }[event.type], true, true, window, 1,
            touch.screenX, touch.screenY,
            touch.clientX, touch.clientY, false,
            false, false, false, 0, null);
    
        touch.target.dispatchEvent(simulatedEvent);
        // event.preventDefault();
    }
    
    function init() {
        document.addEventListener("touchstart", touchHandler, true);
        document.addEventListener("touchmove", touchHandler, true);
        document.addEventListener("touchend", touchHandler, true);
        document.addEventListener("touchcancel", touchHandler, true);
    }

    $('.dragzones').mousedown(function(e){
        e.preventDefault();
        if ($(window).width() <= 768) {
            $('body').css('overflow', 'hidden');
        }
        // set width of the dragzones
        var dragzone = $(this).parent();
        var width = $(dragzone).width();
        console.log(width);
        $(dragzone).css('width', width+'px');
    });
    $('.dragzones').mouseup(function(e){
        e.preventDefault();
        if ($(window).width() <= 768) {
            $('body').css('overflow', 'scroll');
        }
    });

    // quiz question visibility mode js
    function fieldDependency() {
        let accordion_expand_field = $('#edit-field-accordion-collapsed-wrapper');
        accordion_expand_field.hide();
        let visibility_mode = $('#edit-field-visibility-mode--wrapper input:checked').val();
        if( visibility_mode == 'accordion' ) {
            accordion_expand_field.show();
        }
    }
    fieldDependency();
    $('#edit-field-visibility-mode--wrapper input').click(fieldDependency);

    // js for normal card class
    $('.normal-card .card-headers button').each(function() {
        let button_text = $(this).text();
        let new_markup = '<span class="normal-card-title">'+ button_text +'</span>';
        $(this).parent().html(new_markup);
    });

    // js for nested accordion questions
    $('#block-mainpagecontent .accordion').not('.accordion .accordion').each(function(index, target) {
        var parent_id = 'accordion-'+index;
        $(this).attr('id', parent_id);
        // change the data-parent as per the new id of parent
        $(this).find('.cards .collapse').not('.cards .collapse .cards .collapse').attr('data-parent', '#'+parent_id);

        // update the same in parent
        var childs = $(this).find('.accordion');
        if( childs.length ) {
            childs.each(function(index, target) {
                var child_id = parent_id+'-'+index;
                $(target).attr('id', child_id);
                // change the data-parent as per the new id of parent
                $(target).find('.cards .collapse').attr('data-parent', '#'+child_id);

                // update the id and data-target of child accordions
                $(target).find('.cards').each(function() {
                    let button_id = $(this).find('.collapse').attr('id');
                    let new_button_id = child_id +'-'+ button_id;
                    $(this).find('.card-headers button').attr('data-target', '#'+new_button_id);
                    $(this).find('.collapse').attr('id', new_button_id);
                });
            });
        }
    });

    // js for video iframe parent div
    $('p > iframe').each(function() {
        $(this).parent().addClass('video-container');
    });

    // js for image alignment of body field
    $('figure img').each(function() {
        var figure_img_elem = $(this).parent();
    
        if( !figure_img_elem.hasClass('align-center') ) {
            var image_height = $(figure_img_elem).height();
            var para_elem = $(figure_img_elem).next()[0];
            console.log($(para_elem).html());
            if( $(para_elem).html() == '' ) {
                para_elem = $(para_elem).next()[0];
            }
            var para_height = $(para_elem).height();
            if( para_height < image_height ) {
                $(para_elem).css({
                    'height' : image_height+'px',
                });
            }
        }
    
        if( figure_img_elem.hasClass('align-right') ) {
            $(figure_img_elem).css({
                'margin-left' : '14px',
            });
        }
    
        if( figure_img_elem.hasClass('align-left') ) {
            $(figure_img_elem).css({
                'margin-right' : '14px',
            });
        }
    });  

    Drupal.behaviors.topicTest = {
        attach: function (context) {
            // character limit for drag drop questions
            $('.counter[id^="edit-field-drag-drop-question-answer-form"]').each(function() {
                var all_text = $(this).text();
                var text = all_text.split(",")[0];
                limit = text.match(/\d+/)[0];
                var input_limit = $(this).parent().find('input').attr('maxlength');
                if( input_limit > limit ) {
                    $(this).parent().find('input').attr('maxlength', limit);
                }
            });          
        }
    }

    // carousel js
    var maxSlide = 100;
    $('.carousel-item').each(function(){
        if($(this).height() > maxSlide) {
            maxSlide = $(this).height();
            maxSlide = maxSlide + 50 + 80;
            $(this).closest('.carousel').css('height', maxSlide+'px');
        } 
    });
    
    // try again button in quiz
    $('body').on('click', '.reveal_parent_button', function() {
        var try_again = $(this).closest('.card-bodys').find('.try-again-button');
        $(try_again).removeClass('d-none');
    });
    $('body').on('click', '.try-again-button button', function() {
        var reveal_button = $(this).closest('.card-bodys').find('.reveal_parent_button button');
        var reveal_parent = $(this).closest('.card-bodys').find('.reveal_parent');
        $(reveal_parent).hide();
        $(this).parent().addClass('d-none');
        $(reveal_button).show();
    });

    // hide admin link in footer for logged in user
    $('body.user-logged-in footer nav ul li:last-child').each(function() {
        var href = $(this).find('a').attr('href');
        if( href == '/user/login' ) {
            $(this).hide();
        }
    });

}) (jQuery, Drupal)

