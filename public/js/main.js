'use strict';
let csrftoken = $('meta[name=csrf-token]').attr('content') || 0;			// Токен ларавел
let territoryID = $('meta[name=territory-id]').attr('content') || 0;		// IT территориального подразделения


$(document).ready(function () {
	// --
	$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrftoken } });
    
    //
    jQuery.fn.digitalWatch = function() {
        var el = $(this),clock = this,init = function(){
            var date = new Date();
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var seconds = date.getSeconds();
            if (hours < 10) hours = "0" + hours;
            if (minutes < 10) minutes = "0" + minutes;
            if (seconds < 10) seconds = "0" + seconds;
            el.html( hours + ":" + minutes + ":" + seconds );
            setTimeout(function(){init();}, 1000);
        }; init();
    }
    $('#serverClock').digitalWatch();

    // 768px
    if ($(this).width() < 769) $('body').addClass('body-small');
    else $('body').removeClass('body-small');
	
    // Показать MetisMenu
    $('#side-menu').metisMenu();
	
	//
	$('body.canvas-menu .sidebar-collapse').slimScroll({ height: '100%', railOpacity: 0.9 });
	
    // Свернуть функцию ibox
    $('.collapse-link').on('click', function () {
        let ibox = $(this).closest('div.ibox');
        let button = $(this).find('i');
        let content = ibox.children('.ibox-content');
        content.slideToggle(200);
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () { ibox.resize(); ibox.find('[id^=map-]').resize(); }, 50);
    });
	
    // Закрыть функцию ibox
    $('.close-link').on('click', function () {
        let content = $(this).closest('div.ibox');
        content.remove();
    });
	
    // Полноэкранная функция ibox
    $('.fullscreen-link').on('click', function () {
        let ibox = $(this).closest('div.ibox');
        let button = $(this).find('i');
        $('body').toggleClass('fullscreen-ibox-mode');
        button.toggleClass('fa-expand').toggleClass('fa-compress');
        ibox.toggleClass('fullscreen');
        setTimeout(function () { $(window).trigger('resize'); }, 100);
    });
	
    // Закрыть меню в режиме холста
    $('.close-canvas-menu').on('click', function () {
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();
    });
	
    // Выполнить меню холста
    $('body.canvas-menu .sidebar-collapse').slimScroll({height: '100%', railOpacity: 0.9});
	
    // Открыть закрытую правую боковую панель
    $('.right-sidebar-toggle').on('click', function () {
        $('#right-sidebar').toggleClass('sidebar-open');
    });
	
    // Инициализировать slimscroll для правой боковой панели
    $('.sidebar-container').slimScroll({height: '100%', railOpacity: 0.4, wheelStep: 10});
	
    // Инициализировать slimscroll для небольшого чата
    $('.small-chat-box .content').slimScroll({height: '234px', railOpacity: 0.4});
        
    // Открыть закрытый небольшой чат
    $('.open-small-chat').on('click', function () {
        $(this).children().toggleClass('fa-comments').toggleClass('fa-remove');
        $('.small-chat-box').toggleClass('active');
    });

    // Малый обработчик todo
    $('.check-link').on('click', function () {
        let button = $(this).find('i');
        let label = $(this).next('span');
        button.toggleClass('fa-check-square').toggleClass('fa-square-o');
        label.toggleClass('todo-completed');
        return false;
    });
	
    // Минимальное меню
    $('.navbar-minimalize').on('click', function () {
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();
    });
	
    // Всплывающие подсказки
    $('.tooltip').tooltip({selector: "[data-toggle=tooltip]", container: "body"});
	
    // Полная высота боковой панели
    function fix_height() {
        let heightWithoutNavbar = $("body > #wrapper").height() - 61;
        $(".sidebar-panel").css("min-height", heightWithoutNavbar + "px");
		
        let navbarheight = $('nav.navbar-default').height();
        let wrapperHeight = $('#page-wrapper').height();
		
        if (navbarheight > wrapperHeight) $('#page-wrapper').css("min-height", navbarheight + "px");
		
        if (navbarheight < wrapperHeight) $('#page-wrapper').css("min-height", $(window).height() + "px");
		
        if ($('body').hasClass('fixed-nav')) {
            if (navbarheight > wrapperHeight) $('#page-wrapper').css("min-height", navbarheight + "px");
            else $('#page-wrapper').css("min-height", $(window).height() - 60 + "px");
        }
    }
	
    fix_height();
	
    // Исправлена боковая панель
    $(window).bind("load", function(){ if ($("body").hasClass('fixed-sidebar')) $('.sidebar-collapse').slimScroll({height: '100%', railOpacity: 0.9}); });
	
    // Переместить правую боковую панель сверху после прокрутки
    $(window).scroll(function () {
		if ($(window).scrollTop() > 0 && !$('body').hasClass('fixed-nav')) $('#right-sidebar').addClass('sidebar-top');
		else $('#right-sidebar').removeClass('sidebar-top');
    });
	
    $(window).bind("load resize scroll", function(){ if(!$("body").hasClass('body-small')) fix_height(); });
	
    $("[data-toggle=popover]").popover();
	
    // Добавить slimscroll для элемента
    $('.full-height-scroll').slimscroll({height: '100%'});
	
});

// Preloader
$(window).on('load', function () {
		$('#page-preloader').fadeOut('slow');
		let preloader = $('#page-preloader'), spinner   = preloader.find('.spinner');
		spinner.fadeOut();
		preloader.delay(350).fadeOut('slow');
});

// Минимальное меню, когда экран меньше 768px
$(window).bind("resize", function () {
    if ($(this).width() < 769) {
        $('body').addClass('body-small')
    } else {
        $('body').removeClass('body-small')
    }
});

// Локальные функции хранения
// Установите правильный класс тела и плагины на основе пользовательской конфигурации
$(document).ready(function () {
    if (localStorageSupport()) {

        let collapse = localStorage.getItem("collapse_menu");
        let fixedsidebar = localStorage.getItem("fixedsidebar");
        let fixednavbar = localStorage.getItem("fixednavbar");
        let boxedlayout = localStorage.getItem("boxedlayout");
        let fixedfooter = localStorage.getItem("fixedfooter");

        let body = $('body');

        if (fixedsidebar == 'on') {
            body.addClass('fixed-sidebar');
            $('.sidebar-collapse').slimScroll({
                height: '100%',
                railOpacity: 0.9
            });
        }
		
        if (collapse == 'on') {
            if (body.hasClass('fixed-sidebar')) {
                if (!body.hasClass('body-small')) {
                    body.addClass('mini-navbar');
                }
            } else {
                if (!body.hasClass('body-small')) {
                    body.addClass('mini-navbar');
                }
            }
        }

        if (fixednavbar == 'on') {
            $(".navbar-static-top").removeClass('navbar-static-top').addClass('navbar-fixed-top');
            body.addClass('fixed-nav');
        }

        if (boxedlayout == 'on') {
            body.addClass('boxed-layout');
        }

        if (fixedfooter == 'on') {
            $(".footer").addClass('fixed');
        }
    }
});

// Передвижные панели
function WinMove() {
    let element = "[class*=col]";
    let handle = ".ibox-title";
    let connect = "[class*=col]";
    $(element).sortable({
            handle: handle,
            connectWith: connect,
            tolerance: 'pointer',
            forcePlaceholderSize: true,
            opacity: 0.8
        }).disableSelection();
}

// Для демонстрационной цели - сценарий анимации css
function animationHover(element, animation) {
    element = $(element);
    element.hover(
        function () { element.addClass('animated ' + animation); },
		// дождитесь окончания анимации перед удалением классов
		function () { window.setTimeout(function () { element.removeClass('animated ' + animation); }, 2000); }
	);
}

function SmoothlyMenu() {
    if (!$('body').hasClass('mini-navbar') || $('body').hasClass('body-small')) {
        // Скрыть меню, чтобы плавное включение при максимальном меню
        $('#side-menu').hide();
        // Для плавного включения меню
        setTimeout( function () { $('#side-menu').fadeIn(400); }, 200);
    } else if ($('body').hasClass('fixed-sidebar')) {
        $('#side-menu').hide();
        setTimeout( function () { $('#side-menu').fadeIn(400); }, 100);
    } else {
        // Удалите весь встроенный стиль из функции jquery fadeIn для сброса состояния меню
        $('#side-menu').removeAttr('style');
    }
}

// проверьте, поддерживает ли браузер локальное хранилище HTML5
function localStorageSupport() {
    return (('localStorage' in window) && window['localStorage'] !== null)
}
