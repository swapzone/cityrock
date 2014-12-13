/// <reference path="../typings/jquery/jquery.d.ts" />
/// <reference path="../bower_components/v.js/V.d.ts" />

module cityrock {
  'use strict';

  export function initialize(): void {

    var html = $('html');
    var navigation = $('#navigation');

    /*
    $('.form-subscribe-email input')
      .on('focus', function() {
        $('.form-subscribe-email').addClass('is-focused');
      }).on('blur', function() {
        $('.form-subscribe-email').removeClass('is-focused');
      });

    // subscription forms
    $('#form-subscribe-submit').on('click', function(event) {
      event.preventDefault();
      $('#form-subscribe').submit();
    });

    $('#form-subscribe').on('submit', function(event) {
      return $('#form-subscribe').validate({affectsParent: 'fieldset'});
    });
    */

    // responsive menu
    $('.navigation-menu-toggle').on('click', function(event) {
      event.preventDefault();

      $(navigation).toggleClass('is-expanded');
      $(this).find('i').toggleClass('fa-bars fa-close');
    });

    $(navigation).find('a:not(.navigation-menu-toggle)').on('click', function(event) {
      //event.preventDefault();

      $(navigation).removeClass('is-expanded');
      $('.navigation-menu-toggle i').addClass('fa-bars').removeClass('fa-close');
    });

    /**
     * Special treatment for our friends from Cupertino
     *
     */
    if(navigator.userAgent.match(/(iPod|iPhone|iPad)/) ||
      navigator.userAgent.match(/Android; (Mobile|Tablet).*Firefox/)) {

      // add class 'apple' to html element
      $(html).addClass('apple');

      // something else?
    }
    else {
      $(html).addClass('no-apple');

      // something else?
    }
  }
}

$((): void => {
  'use strict';

  cityrock.initialize();
});
