/// <reference path="../typings/jquery/jquery.d.ts" />

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
    var empty = $(form).find(".time").filter(function() {
      //alert(this.value);
      return !(this.value).match(/\d{2}:\d{2}/);
    });

    if(empty.length) {
      alert('Bitte das richtige Zeitformat beachten!');
    }
    else {
      // check date format
      empty = $(form).find(".date").filter(function() {
        return !(this.value).match(/\d{2}.\d{2}.\d{4}/);
      });

      if(empty.length) {
        alert('Bitte das richtige Datumsformat beachten!');
      }
      else {
        // check duration format
        empty = $(form).find(".duration").filter(function() {
          return !(this.value % 1 === 0);
        });

        if(empty.length) {
          alert('Bitte nur ganzzahlige Werte für die Kursdauer angeben!');
        }
        else {
          // check zip code format
          empty = $(form).find(".zip").filter(function() {
            var zipCode = this.value;
            return (Number(zipCode)===zipCode && zipCode%1===0);
          });

          if(empty.length) {
            alert('Bitte nur gültige Postleitzahlen eingeben!');
          }
          else {
            // check if all fields are filled with content
            empty = $(form).find("input").filter(function () {
              return this.value === "";
            });
            if (empty.length) {
              alert('Bitte alle Felder ausfüllen!');
            }
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

    if(!$('#erste-hilfe-kurs').is(':checked'))
      return true;

    var dateInput = $('#erste-hilfe-kurs-date-input');

    // check time format
    var dateValue = dateInput.val();

    if(!dateValue.match(/\d{2}.\d{2}.\d{4}/)) {
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

    // filter element
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

    // confirmation links
    $('.confirm').on('click', function(event) {

      if(confirm("Bist du dir sicher?"))
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

   //
   var changePhoneLink = $("#link-modify-phone");

   $("#phone-input").hide();

   changePhoneLink.click(function() {
     $("#phone-text").hide();
     $(this).hide();

     $("#phone-input").show();
   });

   //
   var changePasswordLink = $("#link-modify-password");

   $("#password-input").hide();

   changePasswordLink.click(function() {
     $("#password-text").hide();
     $(this).hide();

     $("#password-input").show();
   });


    var firstHelp = $('#erste-hilfe-kurs');
    var firstHelpDate = $('#erste-hilfe-kurs-date');

    if(firstHelp.is(':checked'))
      firstHelpDate.css('display', 'inline-block');

   firstHelp.change(function() {
      if(this.checked) {
        firstHelpDate.css('display', 'block');
      }
     else {
        firstHelpDate.css('display', 'none');
      }
   });

   // show save button if checkboxes change
   $(":checkbox").click(function() {

     if(!showSaveButton) {
       $("#edit-user").after("<input type='submit' value='Speichern' class='button'>");
       showSaveButton = true;
     }
   });

   function checkDateFormat() {
     if(!$(firstHelpDate).find("input")
         .val().match(/\d{2}.\d{2}.\d{4}/)) {

       // wrong date format
     }
   }
  }

  /**
   *
   *
   */
  export function initializeCourseView():void {

    // add day link
    $('#add-day').on('click', function(event) {

      var numberOfDays:number = $(event.target).parent().parent().find('input[name=days]').val();
      var index:number = Number(numberOfDays)  + 1;

      if(index<6) {
        // set new value for days input
        $(event.target).parent().parent().find('input[name=days]').val(index.toString());

        var container:string = "<div class='day-container'>";
        $(event.target).before($(container));
        var heading:string = "<h3 class='inline'>Tag " + index + "</h3><span>(<a href='#' class='remove-day'>entfernen</a>)</span>";
        $(event.target).before($(heading));
        var label:string = "<label for='date-" + index + "'>Datum (in der Form <span class='italic'>dd.mm.yyyy</span>)</label>";
        $(event.target).before($(label));
        var input:string = "<input type='text' placeholder='z.B. 02.10.2015' name='date-" + index + "' class='date'>";
        $(event.target).before($(input));
        var label:string = "<label for='time-" + index + "'>Startuhrzeit (in der Form <span class='italic'>hh:mm</span>)</label>";
        $(event.target).before($(label));
        var input:string = "<input type='text' placeholder='z.B. 09:00' name='time-" + index + "' class='time'>";
        $(event.target).before($(input));
        var label:string = "<label for='duraration-" + index + "'>Dauer (in Minuten)</label>";
        $(event.target).before($(label));
        var input:string = "<input type='text' name='duration-" + index + "' class='duration'>";
        $(event.target).before($(input));
        var container:string = "</div>";
        $(event.target).before($(container));
      }
      else {
        alert('Es können nicht mehr als 5 Tage hinzugefügt werden.');
      }
    });

    // remove day link
    $('.remove-day').on('click', function(event) {

      $(event.target).parent().parent().remove();
    });

    // move registrant links
    $('.move').on('click', function(event) {

      var registrantId:string = $(event.target).attr('id');
      var moveControl = $("#move-registrant");

      moveControl.find("input[name='registrant_id']").attr('value', registrantId);
      moveControl.addClass('show');

      // remove button
      $('.remove-move-item').on('click', function(event) {
        //$(event.target).parent().hide();
        $(event.target).parent().removeClass('show');
      });
    });

    // add staff member
    $('#add-staff').click(function() {
      $(this).hide();

      var staffList = $('#staff-list');

      staffList.show();
      staffList.change(function() {
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

        sendFormDataToApi(formData, function(err, message) {
          if(err) {
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
    $('.remove-staff').click(function() {
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

      sendFormDataToApi(formData, function(err, message) {
        if(err) {
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

    $("#edit-user").click(function() {
      $(this).hide();
      $('#delete-user').hide();

      if(!showSaveButton) {
        $(this).after("<input type='submit' value='Speichern' class='button'>");
      }

      if(firstNameText)
        firstNameText.html(function(index, oldHtml) {
          return createInputField(oldHtml, 'first_name');
        });

      if(lastNameText)
        lastNameText.html(function(index, oldHtml) {
          return createInputField(oldHtml, 'last_name');
        });

      if(phoneText)
        phoneText.html(function(index, oldHtml) {
          return createInputField(oldHtml, 'phone');
        });

      if(emailText)
        emailText.html(function (index, oldHtml) {
          return createInputField(oldHtml, 'email');
        });

      if(passwordText)
        passwordText.html(function(index, oldHtml) {
          return createInputField('', 'password', 'password');
        });
    });


    // add roles to user object
    var userRoleSelection = $('#user-add-role-selection');
    var addRoleLink = $("#user-add-role");
    var userId = parseInt($('#user-id-text').text());

    addRoleLink.click(function() {
      $(this).hide();
      userRoleSelection.show();

      userRoleSelection.change(function() {
        var selectedRoleId = $(this).val();

        var formData =
          [
            {
              name: 'user_id',
              value: userId
            },
            {
              name: 'role',
              value: selectedRoleId
            }
          ];

        sendFormData(formData, function(success) {

          if(success) {
            location.reload();
          }
          else {
            addRoleLink.before("<div class='status-message' style='color: red; margin-bottom: 0.5em;'>Fehler beim Hinzufügen der Rolle.</div>");

            setTimeout(function() {
              $('.status-message').remove();
            }, 2000);
          }
        });
      });
    });

    // delete roles from user object
    $(".remove-role").click(function() {
      var roleId = $(this).attr('role');

      // serialize the data in the form
      var formData =
        [
          {
            name: 'user_id',
            value: userId
          },
          {
            name: 'role',
            value: roleId
          },
          {
            name: 'delete_role',
            value: 1
          }
        ];

      sendFormData(formData, function(success) {

        if(success) {
          location.reload();
        }
        else {
          addRoleLink.before("<div class='status-message' style='color: red; margin-bottom: 0.5em;'>Fehler beim Entfernen der Rolle.</div>");

          setTimeout(function() {
            $('.status-message').remove();
          }, 2000);
        }
      });
    });

    /**
     *
     */
    function sendFormData(formData, callback) {

      // variable to hold request
      var request;

      // abort any pending request
      if (request)
        request.abort();

      // Fire off the request to /form.php
      request = $.ajax({
        url: window.location.href,
        type: "post",
        data: formData
      });

      // Callback handler that will be called on success
      request.done(function (response, textStatus, jqXHR) {

        console.log("The response: " + response);

        if(response == "SUCCESS") {
          callback(true);
        }
        else {
          callback(false);
        }
      });

      // Callback handler that will be called on failure
      request.fail(function (jqXHR, textStatus, errorThrown) {

        // Log the error to the console
        console.error(
          "The following error occurred: "+
          textStatus, errorThrown
        );

        callback(false);
      });
    }

    /**
     *
     *
     * @param value
     * @param name
     * @param type
     * @returns {string}
     */
    function createInputField(value, name, type = null) {
      if(!type) type = 'text';

      var inputPosition = value.indexOf('<input type="hidden"');
      if(inputPosition > -1) {
        value = value.substr(0, inputPosition - 1).trim();
      }

      return "<input type='" + type + "' name='" + name + "' value='" + value + "' />";
    }
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
      callback(null, response);
    });

    // Callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
      callback(errorThrown, null);
    });
  }

  /**
   *
   */
  export function initializeArchiveView() {
    var yearFilter = $('#archive-filter-year');
    var monthFilter = $('#archive-filter-month');

    filterCourses();

    yearFilter.change(function() {
      filterCourses();
    });

    monthFilter.change(function() {
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
}

$(():void => {
  'use strict';

  cityrock.initialize();
  cityrock.initializeCourseView();
  cityrock.initializeUserView();
  cityrock.initializeProfileView();
  cityrock.initializeArchiveView();
});