'use strict';
 
export default class Notify {
	constructor() {
		this.element = $(document).find('.notification');
		this.interval = 0;
		this.timeEnd = 0;
	}

	add(type, message) {
		let newAlert = $('<div />');
		newAlert.addClass('alert').addClass('alert-'+type).html(message);

		this.element.append(newAlert);
		this.interval++;

		return this;
	}

	success(message) {
		return this.add('success', message);
	}

	danger(message) {
		return this.add('danger', message);
	}

	error(message) {
		return this.add('danger', message);
	}

	run(callback) {
		var initial = this.interval;
		let alert = this.element.find('.alert');

		alert.each((k , element) =>
		{
			setTimeout(() =>
			{
				this.animate($(element), callback);
			}, (initial - this.interval) * 100);

			this.interval--;

		});
	}

	animate(item, callback) {
		item.animate({marginLeft:20}, 100, () =>
		{
			item.delay(2000).animate({
				opacity: 0,
				marginTop: -1 * (item.height() + 10)
			}, 500, () =>
			{
				item.remove();
				if(this.interval == 0 && typeof callback == 'function') {
					callback();
				}
			});
		});
	}
}

$(function() {
	if(flashNotification.type)
	{
		let notify = new Notify;

		notify[flashNotification.type](flashNotification.message).run();
	}
});