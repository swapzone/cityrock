/// <reference path="../typings/jquery/jquery.d.ts" />

module cityrock {
  'use strict';

  export function validateForm(form) {

    // TODO: check on wrong date/time/duration format

    var empty = $(form).find("input").filter(function() {
      return this.value === "";
    });
    if(empty.length) {
      alert('Bitte alle Felder ausfüllen!');
    }

    return !empty.length;
  }

  export function initialize():void {

    var html = $('html');
    var navigation = $('#navigation');

    // add day link
    $('#add-day').on('click', function(event) {

      var numberOfDays:number = $(event.target).parent().parent().find('input[name=days]').val();
      var index:number = Number(numberOfDays)  + 1;

      if(index<6) {
        // set new value for days input
        $(event.target).parent().parent().find('input[name=days]').val(index.toString());

        var label:string = "<label for='date-" + index + "'>Datum Tag " + index + " (in der Form <span class='italic'>dd.mm.yyyy</span>)</label>";
        $(event.target).before($(label));
        var input:string = "<input type='text' placeholder='z.B. 02.10.2015' name='date-" + index + "'>";
        $(event.target).before($(input));
        var label:string = "<label for='time-" + index + "'>Startuhrzeit Tag " + index + " (in der Form <span class='italic'>hh:mm</span>)</label>";
        $(event.target).before($(label));
        var input:string = "<input type='text' placeholder='z.B. 09:00' name='time-" + index + "'>";
        $(event.target).before($(input));
        var label:string = "<label for='duraration-" + index + "'>Dauer Tag " + index + " (in Minuten)</label>";
        $(event.target).before($(label));
        var input:string = "<input type='text' name='duration-" + index + "'>";
        $(event.target).before($(input));
      }
      else {
        alert('Es können nicht mehr als 5 Tage hinzugefügt werden.');
      }
    });

    // confirmation links
    $('.confirm').on('click', function(event) {

      if(confirm("Bist du dir sicher?"))
        $(event.target).parent().submit();
    });

    // move registrant links
    $('.move').on('click', function(event) {

      // TODO implementation
      alert('Noch nicht implementiert.');
    });

    // print button
    $('#print').on('click', function(event) {

      // TODO implementation
      alert('Noch nicht implementiert.');
    });

    // responsive menu
    $('.navigation-menu-toggle').on('click', function (event) {
      event.preventDefault();

      $(navigation).toggleClass('is-expanded');
      $(this).find('i').toggleClass('fa-bars fa-close');
    });

    $(navigation).find('a:not(.navigation-menu-toggle)').on('click', function (event) {
      //event.preventDefault();

      $(navigation).removeClass('is-expanded');
      $('.navigation-menu-toggle i').addClass('fa-bars').removeClass('fa-close');
    });

    /**
     * Special treatment for our friends from Cupertino
     *
     */
    if (navigator.userAgent.match(/(iPod|iPhone|iPad)/) ||
      navigator.userAgent.match(/Android; (Mobile|Tablet).*Firefox/)) {

      // add class 'apple' to html element
      $(html).addClass('apple');

      // something else?
    }
    else {
      $(html).addClass('no-apple');

      // something else?
    }

    // Course Overview Filter
    var filterLinks = $('#filter').find('span');

    $(filterLinks).on('click', function (event) {

      $(filterLinks).each(function (index, element) {
        $(element).removeClass('active');
      });

      $(event.target).addClass('active');

      if ($(event.target).hasClass('all')) {
        $('.list-item').each(function (index, element) {
          $(element).show(0);
        });
      }
      else {
        $('.list-item').each(function (index, element) {
          if ($(element).attr('class').indexOf($(event.target).text().toLocaleLowerCase()) === -1) {
            $(element).hide(0);
          }
          else {
            $(element).show(0);
          }
        });
      }
    });
  }
}

$(():void => {
  'use strict';

  cityrock.initialize();
});