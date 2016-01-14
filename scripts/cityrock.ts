/// <reference path="../typings/tsd.d.ts" />

module cityrock {
  'use strict';

  // configuration
  var rootDirectory = "/cityrock";

  // state variables
  var showSaveButton = false;

  /**
   *
   *
   * @param form
   * @returns {boolean}
   */
  export function validateForm(form) {

    // check time format
    var empty = $(form).find(".time").filter(function () {
      //alert(this.value);
      return !(this.value).match(/\d{2}:\d{2}/);
    });

    if (empty.length) {
      alert('Bitte das richtige Zeitformat beachten!');
    }
    else {
      // check date format
      empty = $(form).find(".date").filter(function () {
        return !(this.value).match(/\d{2}.\d{2}.\d{4}/);
      });

      if (empty.length) {
        alert('Bitte das richtige Datumsformat beachten!');
      }
      else {
        // check duration format
        empty = $(form).find(".duration").filter(function () {
          return !(this.value % 1 === 0);
        });

        if (empty.length) {
          alert('Bitte nur ganzzahlige Werte für die Kursdauer angeben!');
        }
        else {
          // check zip code format
          empty = $(form).find(".zip").filter(function () {
            var zipCode = this.value;
            return (Number(zipCode) === zipCode && zipCode % 1 === 0);
          });

          if (empty.length) {
            alert('Bitte nur gültige Postleitzahlen eingeben!');
          }
          else {
            /*
            // check if all fields are filled with content
            empty = $(form).find("input").filter(function () {
              return this.value === "";
            });
            if (empty.length) {
              alert('Bitte alle Felder ausfüllen!');
            }
            */
          }
        }
      }
    }

    return !empty.length;
  }

  /**
   *
   *
   * @param form
   * @returns {boolean}
   */
  export function validateProfile(form) {

    if (!$('#erste-hilfe-kurs').is(':checked'))
      return true;

    var dateInput = $('#erste-hilfe-kurs-date-input');

    // check time format
    var dateValue = dateInput.val();

    if (!dateValue.match(/\d{2}.\d{2}.\d{4}/)) {
      dateInput.addClass('error');
      return false;
    }
    else {
      return true;
    }
  }

  /**
   *
   *
   */
  export function initialize():void {

    var html = $('html');
    var navigation = $('#navigation');

    // responsive menu
    $('.navigation-menu-toggle').on('click', function (event) {
      event.preventDefault();

      $(navigation).toggleClass('is-expanded');
      $(this).find('i').toggleClass('fa-bars fa-close');
    });

    $(navigation).find('a:not(.navigation-menu-toggle)').on('click', function () {
      $(navigation).removeClass('is-expanded');
      $('.navigation-menu-toggle i').addClass('fa-bars').removeClass('fa-close');
    });

    // filter elements
    $('.filter').change(function () {
      var filterValue = $(this).val();

      if (filterValue == "Alle") {
        $('.list-item').each(function (index, element) {
          $(element).show(0);
        });
      }
      else {
        $('.list-item').each(function (index, element) {
          if ($(element).attr('class').indexOf(filterValue.toLocaleLowerCase()) === -1) {
            $(element).hide(0);
          }
          else {
            $(element).show(0);
          }
        });
      }
    });

    // confirmation links
    $('.confirm').on('click', function (event) {

      if (confirm("Bist du dir sicher?"))
        $(event.target).parent().submit();
    });

    // special treatment for our friends from Cupertino
    if (navigator.userAgent.match(/(iPod|iPhone|iPad)/) ||
      navigator.userAgent.match(/Android; (Mobile|Tablet).*Firefox/)) {

      // add class 'apple' to html element
      $(html).addClass('apple');
    }
    else {
      $(html).addClass('no-apple');
    }
  }

  /**
   *
   *
   */
  export function initializeProfileView():void {

    var firstHelp = $('#erste-hilfe-kurs');
    var firstHelpDate = $('#erste-hilfe-kurs-date');

    if (firstHelp.is(':checked'))
      firstHelpDate.css('display', 'inline-block');

    firstHelp.change(function () {
      if (this.checked) {
        firstHelpDate.css('display', 'block');
      }
      else {
        firstHelpDate.css('display', 'none');
      }
    });

    // show save button if checkboxes change
    $(":checkbox").click(function () {

      if (!showSaveButton) {
        $("#edit-user").after("<input type='submit' value='Speichern' class='button'>");
        showSaveButton = true;
      }
    });
  }

  /**
   *
   *
   */
  export function initializeCourseView():void {

    var dayCounter = 1;

    // add day link
    $('#add-day').on('click', function (event) {

      //console.log("Day counter: " + dayCounter);
      var index:number = dayCounter + 1;

      if (index < 6) {
        dayCounter++;

        // set new value for days input
        $(document).find('input[name=days]').val(dayCounter.toString());

        var container:JQuery = $("<div class='day-container'>");
        $(event.target).before(container);

        var heading:JQuery = $("<h3 class='inline'>Tag " + index + "</h3><span>(<a class='remove-day'>entfernen</a>)</span>");
        container.append(heading);

        var label:string = "<label for='date-" + index + "'>Datum (in der Form <span class='italic'>dd.mm.yyyy</span>)</label>";
        container.append($(label));

        var input:string = "<input type='text' placeholder='z.B. 02.10.2015' name='date-" + index + "' class='date'>";
        container.append($(input));

        var label:string = "<label for='time-" + index + "'>Startuhrzeit (in der Form <span class='italic'>hh:mm</span>)</label>";
        container.append($(label));

        var input:string = "<input type='text' placeholder='z.B. 09:00' name='time-" + index + "' class='time'>";
        container.append($(input));

        var label:string = "<label for='duration-" + index + "'>Dauer (in Minuten)</label>";
        container.append($(label));

        var input:string = "<input type='text' name='duration-" + index + "' class='duration'>";
        container.append($(input));

        // remove day link
        heading.find('.remove-day').on('click', function (event) {

          var elementIndex = $(".day-container").index(container);
          if(elementIndex < dayCounter - 2)
            alert("Du kannst nur jeweils den letzten Tag entfernen.");
          else {
            dayCounter--;
            $(document).find('input[name=days]').val((dayCounter).toString());
            $(event.target).parent().parent().remove();
          }
        });
      }
      else {
        alert('Es können nicht mehr als 5 Tage hinzugefügt werden.');
      }
    });

    /*
    // remove day link
    $(document).on('click', '.remove-day', function (event) {
      console.log("Remove day...");
      console.log($(event.target).parent().parent().attr('class'));

      //$(event.target).parent().parent().remove();
    });
    */

    // move registrant links
    $('.move').on('click', function (event) {

      var registrantId:string = $(event.target).attr('id');
      var moveControl = $("#move-registrant");

      moveControl.find("input[name='registrant_id']").attr('value', registrantId);
      moveControl.addClass('show');

      // remove button
      $('.remove-move-item').on('click', function (event) {
        //$(event.target).parent().hide();
        $(event.target).parent().removeClass('show');
      });
    });

    // subscribe user to event
    $('.event-subscribe').click(function() {
      var subscribeButton = $(this);

      var eventId = $(this).attr('event-id');
      var userId = $(this).attr('user-id');

      var formData =
        [
          {
            name: 'action',
            value: 'COURSE_ADD_STAFF'
          },
          {
            name: 'user_id',
            value: userId
          },
          {
            name: 'course_id',
            value: eventId
          }
        ];

      sendFormDataToApi(formData, function (err, message) {
        if (err) {
          subscribeButton.after("<span class='status-message' style='color: red; margin-left: 0.3em;'>Fehler!</span>");

          setTimeout(function () {
            $('.status-message').remove();
          }, 2000);
        }
        else {
          location.reload();
        }
      });
    });

    // unsubscribe user from event
    $('.event-unsubscribe').click(function() {
      var unsubscribeButton = $(this);

      var eventId = $(this).attr('event-id');
      var userId = $(this).attr('user-id');
      var deadline = $(this).attr('deadline');

      var formData =
        [
          {
            name: 'action',
            value: 'COURSE_REMOVE_STAFF'
          },
          {
            name: 'user_id',
            value: userId
          },
          {
            name: 'course_id',
            value: eventId
          },
          {
            name: 'deadline',
            value: deadline
          }
        ];

      sendFormDataToApi(formData, function (err, message) {
        if (err) {
          unsubscribeButton.parent().parent().after("<span class='status-message' style='color: red; margin-bottom: 0.5em;'>" + err.message + "</span>");

          setTimeout(function () {
            $('.status-message').remove();
          }, 2500);
        }
        else {
          location.reload();
        }
      });
    });

    // add staff member
    $('#add-staff').click(function () {
      $(this).hide();

      var staffList = $('#staff-list');

      staffList.show();
      staffList.change(function () {
        var selectedUserId = $(this).val();

        var formData =
          [
            {
              name: 'action',
              value: 'COURSE_ADD_STAFF'
            },
            {
              name: 'user_id',
              value: selectedUserId
            },
            {
              name: 'course_id',
              value: $('#course-id').text()
            }
          ];

        sendFormDataToApi(formData, function (err, message) {
          if (err) {
            staffList.before("<span class='status-message' style='color: red; margin-bottom: 0.5em;'>Nutzer konnte nicht hinzugefügt werden.</span>");

            setTimeout(function () {
              $('.status-message').remove();
            }, 2000);
          }
          else {
            location.reload();
          }
        });
      });
    });

    // remove staff member
    $('.remove-staff').click(function () {
      var staffItem = $(this).parent();
      var userId = $(this).attr('user-id');
      var courseId = $('#course-id').text();

      var formData =
        [
          {
            name: 'action',
            value: 'COURSE_REMOVE_STAFF'
          },
          {
            name: 'user_id',
            value: userId
          },
          {
            name: 'course_id',
            value: courseId
          }
        ];

      sendFormDataToApi(formData, function (err, message) {
        if (err) {
          staffItem.after("<span class='status-message' style='color: red; margin-bottom: 0.5em;'>Nutzer konnte nicht entfernt werden.</span>");

          setTimeout(function () {
            $('.status-message').remove();
          }, 2000);
        }
        else {
          location.reload();
        }
      });
    });
  }

  /**
   *
   *
   */
  export function initializeUserView() {
    // var usernameText = $("#username-text");
    var firstNameText = $("#first-name-text");
    var lastNameText = $("#last-name-text");
    var phoneText = $("#phone-text");
    var emailText = $("#email-text");
    var passwordText = $("#password-text");

    $("#edit-user").click(function () {
      $(this).hide();

      if (!showSaveButton) {
        $(this).after("<input type='submit' value='Speichern' class='button'>");
      }

      if (firstNameText)
        firstNameText.html(function (index, oldHtml) {
          return createInputField(oldHtml, 'first_name');
        });

      if (lastNameText)
        lastNameText.html(function (index, oldHtml) {
          return createInputField(oldHtml, 'last_name');
        });

      if (phoneText)
        phoneText.html(function (index, oldHtml) {
          return createInputField(oldHtml, 'phone');
        });

      if (emailText)
        emailText.html(function (index, oldHtml) {
          return createInputField(oldHtml, 'email');
        });

      if (passwordText)
        passwordText.html(function (index, oldHtml) {
          return createInputField('', 'password', 'password');
        });
    });


    $('.delete-user').click(function () {
      var deleteButton = $(this);
      var userId = $(this).attr('user-id');

      var result = confirm("Willst du den Nutzer wirklich löschen?");

      if (result) {

        // serialize the data in the form
        var formData =
          [
            {
              name: 'action',
              value: 'USER_DELETE'
            },
            {
              name: 'user_id',
              value: userId
            }
          ];

        sendFormDataToApi(formData, function (err, message) {

          if (err) {
            deleteButton.after("<div class='status-message' style='color: red; margin-bottom: 0.5em;'>Fehler beim Löschen des Benutzers.</div>");

            setTimeout(function () {
              $('.status-message').remove();
            }, 2000);
          }
          else {
            window.location.assign(window.location.protocol + "//" + window.location.hostname + rootDirectory + "/user");
          }
        });
      }
    });

    // add roles to user object
    var userRoleSelection = $('#user-add-role-selection');
    var addRoleLink = $("#user-add-role");
    var userId = parseInt($('#user-id-text').text());

    addRoleLink.click(function () {
      $(this).hide();
      userRoleSelection.show();

      userRoleSelection.change(function () {
        var selectedRoleId = $(this).val();

        var formData =
          [
            {
              name: 'action',
              value: 'USER_ADD_ROLE'
            },
            {
              name: 'user_id',
              value: userId
            },
            {
              name: 'role_id',
              value: selectedRoleId
            }
          ];

        sendFormDataToApi(formData, function (err, message) {

          if (err) {
            addRoleLink.before("<div class='status-message' style='color: red; margin-bottom: 0.5em;'>Fehler beim Hinzufügen der Rolle.</div>");

            setTimeout(function () {
              $('.status-message').remove();
            }, 2000);
          }
          else {
            location.reload();
          }
        });
      });
    });

    // delete roles from user object
    $(".remove-role").click(function () {
      var roleId = $(this).attr('role');

      // serialize the data in the form
      var formData =
        [
          {
            name: 'action',
            value: 'USER_REMOVE_ROLE'
          },
          {
            name: 'user_id',
            value: userId
          },
          {
            name: 'role_id',
            value: roleId
          }
        ];

      sendFormDataToApi(formData, function (err, message) {

        if (err) {
          addRoleLink.before("<div class='status-message' style='color: red; margin-bottom: 0.5em;'>Fehler beim Entfernen der Rolle.</div>");

          setTimeout(function () {
            $('.status-message').remove();
          }, 2000);
        }
        else {
          location.reload();
        }
      });
    });

    /**
     *
     *
     * @param value
     * @param name
     * @param type
     * @returns {string}
     */
    function createInputField(value, name, type = null) {
      if (!type) type = 'text';

      var inputPosition = value.indexOf('<input type="hidden"');
      if (inputPosition > -1) {
        value = value.substr(0, inputPosition - 1).trim();
      }

      return "<input type='" + type + "' name='" + name + "' value='" + value + "' />";
    }
  }

  /**
   *
   */
  export function initializeArchiveView() {
    var yearFilter = $('#archive-filter-year');
    var monthFilter = $('#archive-filter-month');

    filterCourses();

    yearFilter.change(function () {
      filterCourses();
    });

    monthFilter.change(function () {
      filterCourses();
    });

    function filterCourses() {
      $('.list-item').each(function (index, element) {
        if ($(element).attr('year') == yearFilter.val() && $(element).attr('month') == monthFilter.val()) {
          $(element).css('display', 'table-row');
        }
        else {
          $(element).css('display', 'none');
        }
      });
    }
  }

  /**
   *
   */
  export function initializeCalendarView() {

    var calendar = $('#calendar');

    var eventType = 'all';
    var userId = '-1';

    // calendar events filter
    var filterLinks = $('#filter').find('span');

    $(filterLinks).on('click', function (event) {

      $(filterLinks).each(function (index, element) {
        $(element).removeClass('active');
      });

      $(event.target).addClass('active');

      // selected event type
      eventType = $(event.target).attr('event-type');
      userId = '-1';

      if(eventType == 'user')
        userId = $(event.target).attr('user-id');

      // re-fetch events
      calendar.fullCalendar( 'refetchEvents' );

      //console.log("Event type: " + eventType);
      //console.log("User id: " + userId);
    });


    // initialize calendar
    calendar.fullCalendar({
      // put your options and callbacks here
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek'
      },

      // the event source object
      events: {
        url: rootDirectory + '/event_feed.php',
        type: 'post',
        data: {
          event_type: function() { return eventType; },
          user_id: function() { return userId; }
        },
        error: function() {
          console.error('Could not retrieve events.');
        },

        textColor: 'black'
      },


      dayClick: function(fullDate:moment.Moment, jsEvent:MouseEvent, view:FullCalendar.View) {

        //console.log('Clicked on: ' + fullDate.toString());
        //console.log('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);

        var date = fullDate.format("DD.MM.YYYY").replace(/\./g, '-I-');
        var time = fullDate.format("HH:mm");

        var newUrl = "course/new/" + date;

        if(fullDate.hour() > 0)
          newUrl += "-S-" + time;

        window.location.href = newUrl;
      },

      eventClick: function(calEvent, jsEvent, view) {

        console.log('Event: ' + calEvent.title);
        console.log('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);


        // change the border color just for fun
        //$(this).css('border-color', 'red');
      }
    });
  }

  /**
   *
   *
   * @param formData
   * @param callback
   */
  function sendFormDataToApi(formData, callback) {

    // variable to hold request
    var request;

    // abort any pending request
    if (request)
      request.abort();

    var url = window.location.protocol + "//" + window.location.hostname + rootDirectory + "/api";
    console.log(url);

    // Fire off the request to /form.php
    request = $.ajax({
      url: url,
      type: "post",
      data: formData
    });

    // Callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
      if(response.indexOf('ERROR') == 0) {
        callback(Error(response.substr(7)), null);
        return;
      }

      callback(null, response);
    });

    // Callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
      callback(errorThrown, null);
    });
  }
}

$(():void => {
  'use strict';

  cityrock.initialize();
  cityrock.initializeCourseView();
  cityrock.initializeUserView();
  cityrock.initializeProfileView();
  cityrock.initializeArchiveView();
  cityrock.initializeCalendarView();
});