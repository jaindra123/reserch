(function ($, Drupal) {
	Drupal.behaviors.practice = {
		attach: function (context, settings) {
					
				$( document ).ajaxComplete(function( event, request, settings ) {
					$('.autocomplete-suggestion-label').click(function () {
						$('#edit-submit-search').click();
					}
					);
				});

			$('.reveal_parent').hide();
			$('.items-content-show').click(function () {
				$(this).hide();
				jQuery(this).closest('.card-bodys').find('.reveal_parent').show();
				// class_name = $(this).attr('rel');
				// $('.'+class_name).show();
			});
			// Show Answerbox
			$('.revelAnswer').click(function () {
				$(this).hide('');
				$(this).next('.answerBox').show();
			});
			// Add minus icon for collapse element which is open by default
			$(".collapse.show").each(function () {
				$(this)
					.prev(".card-headers")
					.find(".fa")
					.addClass("fa-minus-circle")
					.removeClass("fa-plus-circle");
			});

			// Toggle plus minus icon on show hide of collapse element
			$(".collapse")
				.on("show.bs.collapse", function () {
					$(this)
						.prev(".card-headers")
						.find(".fa")
						.removeClass("fa-plus-circle")
						.addClass("fa-minus-circle");
				})
				.on("hide.bs.collapse", function () {
					$(this)
						.prev(".card-headers")
						.find(".fa")
						.removeClass("fa-minus-circle")
						.addClass("fa-plus-circle");
				});

			const $menu = $('#navbarTogglerDemo01');

			$menu.on('show.bs.collapse', function () {
				$menu.addClass('menu-show');
			});

			$menu.on('hide.bs.collapse', function () {
				$menu.removeClass('menu-show');
			});

			// $("ul.dropdown-menu [data-toggle='dropdown']").on("click", function(event) {
			// 	event.preventDefault();
			// 	event.stopPropagation();

			// 	$(this).siblings().toggleClass("show");


			// 	if (!$(this).next().hasClass('show')) {
			// 		$(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
			// 	}
			// 	$(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
			// 		$('.dropdown-submenu .show').removeClass("show");
			// 	});

			// });

			// menu dropdown js
			$(".we-mega-menu-li").click(function () {
				$(this).toggleClass("down-arrow");
				$(".we-mega-menu-submenu").toggle();
			});

			// scroll-top js
			// $(window).scroll(function () {
			// 	var topPos = $(this).scrollTop();
			// 	if (topPos > 100) {
			// 		$(".scroll-top").css("opacity", "1");

			// 	} else {
			// 		$(".scroll-top").css("opacity", "0");
			// 	}

			// });

			// $('.scroll-top').bind("click", function () {
			// 	$('html, body').animate({ scrollTop: 0 }, 800);
			// 	return false;
			// });

			// scroll-top js
			$(window).scroll(function () {
				var topPos = $(this).scrollTop();
				var bodyHeight = $('body').height();
				if (topPos > bodyHeight - 1300) {
					$(".scroll-top").css("opacity", "1");

				} else {
					$(".scroll-top").css("opacity", "0");
				}

			});

			$('.scroll-top').bind("click", function () {
				$('html, body').animate({ scrollTop: 0 }, 800);
				return false;
			});


			$('.MYclassList').change(function () {
				var url = $(this).val();
				window.location = url;
			});
			$(function () {
				$(document).scroll(function () {
					var $nav = $(".fixed-top");
					$nav.toggleClass('scrolled', $(this).scrollTop() > $nav.height());
					$('.skip').toggleClass('skip_hide', $(this).scrollTop() > $nav.height());
				});
			});

			$(document).on("click", ".UpButton", function () {
				$(".form-file").trigger("click");
				//$('.UpButton').hide();

			});
			var selector = '.right-sidebar .view-topic-view ul li a';
			$(selector).on('click', function () {
				$(selector).removeClass('active');
				$(this).addClass('active');
			});
			$(document).ready(function () {
				// Add minus icon for collapse element which is open by default
				$(".collapse.show").each(function () {
					$(this)
						.prev(".card-headers")
						.find(".fa")
						.addClass("fa-minus-circle")
						.removeClass("fa-plus-circle");
				});

				// Toggle plus minus icon on show hide of collapse element
				$(".collapse")
					.on("show.bs.collapse", function () {
						$(this)
							.prev(".card-headers")
							.find(".fa")
							.removeClass("fa-plus-circle")
							.addClass("fa-minus-circle");
					})
					.on("hide.bs.collapse", function () {
						$(this)
							.prev(".card-headers")
							.find(".fa")
							.removeClass("fa-minus-circle")
							.addClass("fa-plus-circle");
					});
			});
			
			$(document).ready(function () {
				$(".btn-linkssr").click(function () {
					if ($(this).hasClass('collapsed')) {
						$(this).find(".fa").removeClass("fa-plus-circle");
						$(this).find(".fa").addClass("fa-minus-circle");
					} else {
						$(this).find(".fa").removeClass("fa-minus-circle");
						$(this).find(".fa").addClass("fa-plus-circle");
					}
			
					setTimeout(function(){
						$(".btn-linkssr").each(function() {
							if ($( this ).hasClass( "collapsed" ) && $(this).find('.fa').hasClass('fa-minus-circle')) {
								$(this).find(".fa").removeClass("fa-minus-circle");
								$(this).find(".fa").addClass("fa-plus-circle");
							}
						});
					}, 500);		
				})
			});
		}
	};

}(jQuery, Drupal, drupalSettings));