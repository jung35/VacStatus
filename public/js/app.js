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

function addUserList($smallId) {

}
