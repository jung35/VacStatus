$(window).load(function() {
  function reqUser(communityId, dated) {
    request = $.ajax({
      dataType: "json",
      type: "POST",
      url: "/json/user/",
      data: {communityId: communityId, dated: dated}
    }).done(function(data) {
      if(data.status == 'success') {
        $('#user-'+communityId).parent().html(data.html);
      } else {
        $('#user-'+communityId).html('<span class="text-danger">Could not load user</span>');
      }
    }).fail(function() {
        $('#user-'+communityId).html('<span class="text-danger">Could not load user</span>');
    });
  }

  if(typeof dated == 'undefined') {
    dated = false;
  }

  for(var i = 0; i < userLoad.length; i++) {
    reqUser(userLoad[i], dated);
  }
});
