(function () {
	'use strict';

	var script;
	var form_ids = mailster_recaptcha ? mailster_recaptcha.forms : [];
	var queue = [];

	// legacy forms
	form_ids.forEach(function (id) {
		var forms = document.querySelectorAll('.mailster-form-' + id);
		forms.forEach(init);
	});

	// block forms
	document.addEventListener('mailster:load', handler);
	document.addEventListener('mailster:open', handler);

	function handler(event) {
		if (!event.detail.el) return;
		var formEl = event.detail.el;
		init(formEl);
	}

	function init(formEl) {
		if (!script) {
			script = document.createElement('script');
			script.type = 'text/javascript';
			script.onload = function () {
				queue.forEach(recaptcha);
				queue.push = recaptcha;
			};
			script.src =
				'https://www.google.com/recaptcha/api.js?render=' +
				mailster_recaptcha.public_key;
			document.getElementsByTagName('head')[0].appendChild(script);
		}
		queue.push(formEl);
	}

	function recaptcha(formEl) {
		var el = formEl.querySelector('.g-recaptcha-response');
		if (!el) {
			el = document.createElement('input');
			el.type = 'hidden';
			el.name = '_g-recaptcha-response';
			el.className = 'g-recaptcha-response';
			formEl.appendChild(el);
		}

		grecaptcha.ready(function () {
			grecaptcha
				.execute(mailster_recaptcha.public_key, {
					action: 'submit',
				})
				.then(function (token) {
					el.value = token;
				});
		});
	}
})();
