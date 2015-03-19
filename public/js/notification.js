var notif = {
	element: $('.notification'),
	interval: 0,
	timeEnd: 0,

	add: function(type, message)
	{
		var newAlert = $('<div />').addClass('alert').addClass('alert-'+type).html(message);
		notif.element.append(newAlert);
		notif.interval++;
		return this;
	},

	run: function(callback)
	{
		var alert = notif.element.find('.alert');
		var initial = notif.interval;

		alert.each(function(k, element)
		{
			setTimeout(function()
			{
				notif.animate($(element), callback)
			}, (initial - notif.interval) * 100);

			notif.interval--;

		}.bind(initial));
	},

	animate: function(notificationItem, callback)
	{
		notificationItem.animate({marginLeft:20}, 100, function()
		{
			notificationItem.delay(2000).animate({
				opacity: 0,
				marginTop: -1 * (notificationItem.height() + 10)
			}, 500, function() {
				notificationItem.remove();
				if(notif.interval == 0 && callback !== null && callback !== undefined) {
					callback();
				}
			});
		});
	}
}

$(function() {
	// notif
	// 	.add('success', '123')
	// 	.add('danger', 'asdgf')
	// 	.add('info', 'asdasda')
	// 	.add('warning', 'aaaaaaaaaaaa')
	// 	.run();
});