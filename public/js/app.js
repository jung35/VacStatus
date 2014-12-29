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

$(function() {
  var footer = $(".footer");
  var pos = footer.position();
  var height = $(window).height();
  height = height - pos.top - footer.height() - 25;
  if (height > 0) {
    footer.css({
      'margin-top': height + 'px'
    });
  }
});

$(document).bind("ajaxComplete", function(){
  $(document).foundation();
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

function disableFormButton(form) {
  $(form.button).prop("disabled",true);
  form.submit();
}

var $addProfileUser = $('#addProfileUser');
function addUserList(profileId) {
  $addProfileUser.find('#profile_id').val(profileId);
  $addProfileUser.foundation('reveal', 'open');
}

function doAddUserList(form) {
  var action = form.action,
    list_id = form.list_id.value,
    profile_id = form.profile_id.value,
    profile_description = form.profile_description.value,
    _token = form._token.value;

  $(form.submit).prop("disabled",true);

  $.ajax({
    url: action,
    type: "POST",
    data: {
      'list_id': list_id,
      'profile_id': profile_id,
      'profile_description': profile_description,
      '_token': _token
    },
    beforeSend: fadeInLoader('Adding user to list')
  }).done(function(data) {
    if(data != 'success') {
      fadInOutAlert("<strong>Error</strong> "+data, 2);
    } else {
      fadInOutSuccess("<strong>Success</strong> The user has been added to list.", 2);
    }
    fadeOutLoader();
    $(form.submit).prop("disabled", false);
    $addProfileUser.foundation('reveal', 'close');
  }).error(function() {
    fadeOutLoader();
    fadInOutAlert("<strong>Error</strong> There was an error adding this user to the list.", 2);
    $(form.submit).prop("disabled", false);
    $addProfileUser.foundation('reveal', 'close');
  });
}

function doDeleteUserList(form) {
  var action = form.action,
    list_id = form.list_id.value,
    profile_id = form.profile_id.value,
    _token = form._token.value;

  $(form.submit).prop("disabled",true);

  $.ajax({
    url: action,
    type: "POST",
    data: {
      'list_id': list_id,
      'profile_id': profile_id,
      '_token': _token
    },
    beforeSend: fadeInLoader('Removing user from list')
  }).done(function(data) {
    if(data != 'success') {
      fadInOutAlert("<strong>Error</strong> "+data, 2);
    } else {
      var thisData = $(this)[0].data.split('&'),
        thisProfileId = thisData[1].split('=')[1];

      $('.vacstatus-multilist').find('.profileId_'+thisProfileId).remove();
      fadInOutSuccess("<strong>Success</strong> The user has been deleted from the list.", 2);
    }
    fadeOutLoader();
    $(form.submit).prop("disabled", false);
  }).error(function() {
    fadeOutLoader();
    fadInOutAlert("<strong>Error</strong> There was an error removing this user from the list.", 2);
    $(form.submit).prop("disabled", false);
  });
}

function doCreateList(form) {
  var action = form.action,
    privacy = form.privacy.value,
    title = form.title.value,
    _token = form._token.value;

  $(form.submit).prop("disabled",true);

  $.ajax({
    url: action,
    type: "POST",
    data: {
      'privacy': privacy,
      'title': title,
      '_token': _token
    },
    beforeSend: fadeInLoader('Creating List')
  }).done(function(data) {
    if(data != 'success') {
      fadInOutAlert("<strong>Error</strong> "+data, 2);
    } else {
      fadInOutSuccess("<strong>Success</strong> List has been created.", 2);
      $.ajax({
        url: '/list/get',
        type: "POST",
        data: {
          '_token':_token
        },
        beforeSend: fadeInLoader('Fetching List')
      }).done(function(data) {
        var listList = $('#addProfileUser').find('select'),
          personalList = $('#personalList');

        // Clear list
        listList.html("");
        personalList.html("");
        // append update list
        $.each(data, function(k, list) {
          console.log(list);
          listList.append('<option value="'+ list.id +'">'+ list.title +'</option>');
          personalList.prepend('<li><a href="/l/'+ list.user_id +'/'+ list.id +'">'+ list.title +'</a></li>');
        });
        personalList.append('<li class="divider"></li>');
        personalList.append('<li><a data-reveal-id="addList">New List</a></li>');

        fadInOutSuccess("<strong>Success</strong Updated List", 2);
        fadeOutLoader();
      }).error(function() {
        fadInOutAlert("<strong>Error</strong> Could not update list.", 2);
        fadeOutLoader();
      });
    }
    fadeOutLoader();
    $(form.submit).prop("disabled", false);
    $addProfileUser.foundation('reveal', 'close');
  }).error(function() {
    fadeOutLoader();
    fadInOutAlert("<strong>Error</strong> There was an error adding this user to the list.", 2);
    $(form.submit).prop("disabled", false);
    $addProfileUser.foundation('reveal', 'close');
  });
}

function showListLink(userId, listId) {
  url = "http://VacStat.us/l/"+userId+"/"+listId;
  window.prompt("Here's the link to the list", url);
}

var $editList = $('#editList');
function showEditForm(listId, privacy) {
  var listTitle = $('.list-title').text();

  $editList.find('.editList_id_element').html(listId);
  $editList.find('.editList_privacy').val(privacy);
  $editList.find('.editList_title').val(listTitle);
  $editList.find('.editList_id').val(listId);

  $editList.foundation('reveal','open');
}

function userMultiUpdate(list) {
  if(list.length == 0 || typeof list != 'object') {
    return;
  }
  $.ajax({
    url: '/list/update',
    type: "POST",
    data: {
      'list': list,
      '_token': _token
    },
    beforeSend: fadeInLoader('Updating Profiles')
  }).done(function(data) {
    fadeOutLoader();
    var $data = $(data);
    var $e;
    $data.each(function(k, e) {
      $e = $(e);
      if($e.html() != undefined) {
        var profileId = $e.attr('class').split('_')[2];
        var profileContent = $e.find('tr').html();
        $('.profileId_'+profileId).find('.list-replaceable').remove();
        $('.profileId_'+profileId).prepend(profileContent);
      }
    });
  }).error(function() {
    fadeOutLoader(function() {
      fadInOutAlert("<strong>Error</strong> There was an error updating the list. Please try again soon", 2);
    });
  });
}

$(window).load(function() {
  if(typeof userToUpdate != "undefined") {
    userMultiUpdate(userToUpdate);
  }
});
