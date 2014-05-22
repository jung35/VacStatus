function reqUser(communityId, dated, searching) {
  jQuery.ajaxSetup({ jsonp: null, jsonpCallback: null});
  request = $.ajax({
    dataType: "json",
    type: "POST",
    url: "/json/user",
    data: {communityId: communityId, dated: dated, searching: searching}
  }).done(function(data) {
    if(data.status == 'success') {
      $('#user-'+communityId).parent().html(data.html);
    } else {
      $('#user-'+communityId).parent().html('<td colspan="7" class="text-danger text-center">Could not load user</td>');
    }
  }).fail(function() {
      $('#user-'+communityId).parent().html('<td colspan="7" class="text-danger text-center">Could not load user</td>');
  });
}

function changeFormButton(communityId) {
  var $form = $('#user-'+communityId).parent().find('form');
  var currentStatus = $form.attr('action').split('/');
  if(currentStatus[currentStatus.length-1] == 'remove') {
    $form.attr('action', 'add');
    $form.find('.btn-danger').addClass('btn-info').removeClass('btn-danger').val('Add');
  } else {
    $form.attr('action', 'remove');
    $form.find('.btn-info').addClass('btn-danger').removeClass('btn-info').val('Delete');
  }
}

$(window).load(function() {

  if(typeof dated == 'undefined') {
    var dated = false;
  }

  if(typeof searching == 'undefined') {
    var searching = false;
  }

  for(var i = 0; i < userLoad.length; i++) {
    reqUser(userLoad[i], dated, searching);
  }
});
