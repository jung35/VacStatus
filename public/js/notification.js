$(function() {
	var notif = {
		element: $('.notification'),
		interval: 0,

		add: function(type, message)
		{
			var newAlert = $('<div />').addClass('alert').addClass('alert-'+type).html(message);
			notif.element.append(newAlert);
			notif.interval++;
			return this;
		},

		run: function()
		{
			var alert = notif.element.find('.alert');
			var initial = notif.interval;

			alert.each(function(k, element)
			{
				setTimeout(function()
				{
					notif.animate($(element))
				}, (initial - notif.interval) * 800);

				notif.interval--;

			}.bind(initial));
		},

		animate: function(notificationItem)
		{
			notificationItem.animate({marginLeft:20}, 1000, function()
			{
				notificationItem.delay(2000).animate({
					opacity: 0,
					marginTop: -1 * (notificationItem.height() + 10)
				}, 500, function() {
					notificationItem.remove();
				});
			});
		}
	}

	notif
		.add('success', '123')
		.add('danger', 'asdgf')
		.add('info', 'asdasda')
		.add('warning', 'aaaaaaaaaaaa')
		.run();
});