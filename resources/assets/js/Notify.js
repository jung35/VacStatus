'use strict';
 
class Notify {
	constructor() {
		this.element = $('.notification');
		this.interval = 0;
		this.timeEnd = 0;
	}

	add(type, message) {
		let newAlert = $('<div />').addClass('alert').addClass('alert-'+type).html(message);
		this.element.append(newAlert);
		this.interval++;
		return this;
	}

	success(message) {
		this.add('success', message);
	}

	danger(message) {
		this.add('danger', message);
	}

	error(message) {
		this.add('danger', message);
	}

	run(callback) {
		let alert = this.element.find('.alert');
		let initial = this.interval;

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
				if(notif.interval == 0 && typeof callback == 'function') {
					callback();
				}
			});
		});
	}
}
