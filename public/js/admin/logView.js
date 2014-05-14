$('.log').on('click', '.line', function() {
  $(this).next().slideToggle('fast');
});

$('.log-settings').on('click', 'span', function() {
  var $this = $(this);
  if($this.hasClass('sel')) {
    $this.removeClass('sel');
    $('.line.line-'+$this.attr('title')).removeClass('hidden');
  } else {
    $this.addClass('sel');
    $('.line.line-'+$this.attr('title')).addClass('hidden');
  }
  $('.log .debug').hide();
});
