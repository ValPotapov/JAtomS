/*
 * @package    Joomla Atom-S Showcases Module
 * @version    1.1.0
 * @author     Septdir Workshop - septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

document.addEventListener("DOMContentLoaded", function () {
	document.querySelectorAll('[mod_jatoms_showcases="container"]').forEach(function (container) {
		let loading = container.querySelector('[mod_jatoms_showcases="loading"]'),
			error = container.querySelector('[mod_jatoms_showcases="error"]'),
			items = container.querySelector('[mod_jatoms_showcases="items"]');

		loading.style.display = '';
		error.style.display = 'none';
		items.style.display = 'none';

		error.innerText = '';
		items.innerHTML = '';

		let request = new XMLHttpRequest(),
			requestUrl = container.getAttribute('data-url'),
			formData = new FormData();

		formData.set('module_id', container.getAttribute('data-module_id'));
		formData.set('format', 'json');

		request.open('POST', requestUrl);
		request.send(new URLSearchParams(formData));
		request.onreadystatechange = function () {
			if (this.readyState === 4) {
				loading.style.display = 'none';
			}

			if (this.readyState === 4 && this.status === 200) {
				let response = false;
				try {
					response = JSON.parse(this.response);
				} catch (e) {
					response = false;
					error.innerText = request.status + ' ' + request.message;
					error.style.display = '';
					items.style.display = 'none';
					return;
				}
				if (response.success) {
					items.innerHTML = response.data;
					items.style.display = '';
				} else {
					error.innerText = response.message;
					error.style.display = '';
				}
			} else if (this.readyState === 4 && this.status !== 200) {
				error.innerText = request.status + ' ' + request.message;
				error.style.display = '';
			}
		};
	});
});