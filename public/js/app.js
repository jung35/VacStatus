// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation();

if (Modernizr.localstorage) {
  var vacstatusAlertBox = localStorage.getItem('vacstatusAlertBox');

  if(vacstatusAlertBox === 'true') {
    $('.vacstatus-alert-box').remove();
  }
}

$('.vacstatus-alert-box').on('close.fndtn.alert-box', function(event) {
  if (Modernizr.localstorage) {
    localStorage.setItem('vacstatusAlertBox', 'true');
  }
});

/**
 * Fades alert in and out based on time
 * @param  String  message alert message
 * @param  Integer time    Fade in and out time in seconds
 * @return void
 */
function fadInOutAlert(message, time) {
  $('.error-notification').html(message).fadeIn('slow', function() {
    setTimeout(function() {
      $('.error-notification').fadeOut('slow');
    }, time * 1000);
  });
}

function fadInOutSuccess(message, time) {
  $('.success-notification').html(message).fadeIn('slow', function() {
    setTimeout(function() {
      $('.success-notification').fadeOut('slow');
    }, time * 1000);
  });
}

var $loader = $('.loader');
function fadeInLoader(message) {
  $loader.find('span').html(message);
  $loader.fadeIn('slow');
}

function fadeOutLoader(callback) {
  $loader.fadeOut('slow', callback);
}

var $addProfileUser = $('#addProfileUser');
function addUserList(smallId) {
  $addProfileUser.find('#profile_id').val(smallId);
  $addProfileUser.foundation('reveal', 'open');
}

function showList(req) {
  $.ajax({
    url: '/list/fetch',
    type: "POST",
    async: false,
    data: {
      'req': req,
      '_token': _token
    },
  }).done(function(data) {
    $('.list-display').html(data);
    $('.list-display-wrapper').animate({
      height : $(".list-display").height() + 100
    },600);
  }).error(function() {
    fadeOutLoader(function() {
      fadInOutAlert("<strong>Error</strong> There was an error loading list. Please try again soon", 2);
    });
  });
}
