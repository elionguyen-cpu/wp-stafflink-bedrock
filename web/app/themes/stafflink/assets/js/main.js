(function () {
	'use strict';

	var stickyHeader = document.getElementById('sticky-header') || document.querySelector('[id="sticky-header "]');
	var scrollButton = document.querySelector('.scroll-to-top');

	function updateScrollState() {
		var isScrolled = window.scrollY > 24;

		if (stickyHeader) {
			stickyHeader.classList.toggle('is-sticky', isScrolled);
		}

		if (scrollButton) {
			scrollButton.classList.toggle('is-visible', window.scrollY > 360);
		}
	}

	function bindMobileSubmenus() {
		var offcanvas = document.querySelector('.header .offcanvas');

		if (!offcanvas) {
			return;
		}

		offcanvas.querySelectorAll('.dropdown-toggle').forEach(function (toggle) {
			toggle.addEventListener('click', function (event) {
				if (window.innerWidth >= 992) {
					return;
				}

				event.preventDefault();

				var menu = toggle.nextElementSibling;
				var isOpen = toggle.getAttribute('aria-expanded') === 'true';

				toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');

				if (menu) {
					menu.classList.toggle('show', !isOpen);
				}
			});
		});
	}

	function initSelect2(context) {
		if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.select2) {
			return;
		}

		var $ = window.jQuery;
		var scope = context || document;

		$('select.js-select2, .js-select2 select', scope).each(function () {
			var select = $(this);
			var icon = select.data('placeholder-icon');
			var placeholder = select.data('placeholder') || select.find('option:first').text();
			var dropdownParent = select.closest('.jobseekers-field, .submit-form, .apply-job');
			var selectOptions = {
				width: '100%',
				minimumResultsForSearch: 0,
				placeholder: placeholder,
				templateSelection: function (data) {
					var text = data.text || placeholder;

					if (data.id || !icon) {
						return text;
					}

					return $('<span class="select2-placeholder-with-icon"></span>')
						.append($('<i aria-hidden="true"></i>').addClass(icon))
						.append($('<span></span>').text(text));
				}
			};

			if (select.data('select2-ready')) {
				return;
			}

			if (dropdownParent.length) {
				selectOptions.dropdownParent = dropdownParent;
			}

			try {
				select.select2(selectOptions);
			} catch (error) {
				select.removeData('select2-ready');
				return;
			}

			select.data('select2-ready', true);

			var select2 = select.data('select2');

			if (!select2 || !select2.$container) {
				return;
			}

			if (select.hasClass('js-job-filter-select')) {
				select2.$container.addClass('job-filter-select2');
			}

			select2.$container
				.find('.select2-selection__arrow')
				.html('<i class="bi bi-chevron-down" aria-hidden="true"></i>');
		});
	}

	function initStafflinkApplicationUploads() {
		var maxSize = 5 * 1024 * 1024;
		var uploadSettings = {
			resume: {
				action: 'upload_resume',
				allowedTypes: ['pdf', 'doc', 'docx'],
				defaultText: 'Attach Resume',
				hiddenName: 'resume_attachment_id',
				messageClass: '.resume-upload-message'
			},
			photo: {
				action: 'upload_photo',
				allowedTypes: ['jpg', 'jpeg', 'png', 'webp'],
				defaultText: 'Attach Photo',
				hiddenName: 'photo_attachment_id',
				messageClass: '.photo-upload-message'
			}
		};

		function getUploadSettings(input) {
			return uploadSettings[input.name] || uploadSettings.resume;
		}

		function getUploadElements(input) {
			var item = input.closest('.resume-upload');
			var upload = input.closest('.file-upload');
			var settings = getUploadSettings(input);
			var form = item ? item.closest('form') : null;

			return {
				upload: upload,
				label: upload ? upload.querySelector('.file-label') : null,
				button: upload ? upload.querySelector('.input-group-text') : null,
				hidden: item ? item.querySelector('input[name="' + settings.hiddenName + '"]') : null,
				message: item ? item.querySelector(settings.messageClass) : null,
				submit: form ? form.querySelector('.wpuf-submit-button') : null
			};
		}

		function setMessage(elements, message, isError) {
			if (!elements.message) {
				return;
			}

			elements.message.textContent = message || '';
			elements.message.classList.toggle('is-error', !!isError);
		}

		function resetUpload(input) {
			var elements = getUploadElements(input);
			var settings = getUploadSettings(input);

			input.value = '';

			if (elements.hidden) {
				elements.hidden.value = '';
			}

			if (elements.upload) {
				elements.upload.classList.remove('has-file', 'is-uploading');
			}

			if (elements.label) {
				elements.label.textContent = settings.defaultText;
			}

			if (elements.button) {
				elements.button.textContent = 'Browse';
			}

			if (elements.submit) {
				elements.submit.disabled = false;
			}

			setMessage(elements, '');
		}

		function failUpload(input, message) {
			var elements = getUploadElements(input);

			resetUpload(input);
			setMessage(elements, message, true);
		}

		document.addEventListener('click', function (event) {
			var button = event.target.closest('.resume-upload .input-group-text');

			if (!button) {
				return;
			}

			var upload = button.closest('.file-upload');
			var input = upload ? upload.querySelector('input[type="file"]') : null;

			if (!input || !upload.classList.contains('has-file')) {
				return;
			}

			event.preventDefault();
			resetUpload(input);
		}, true);

		document.addEventListener('change', function (event) {
			var input = event.target;

			if (!input.matches('.resume-upload input[type="file"]')) {
				return;
			}

			var elements = getUploadElements(input);
			var file = input.files && input.files[0] ? input.files[0] : null;
			var extension = file && file.name.indexOf('.') > -1 ? file.name.split('.').pop().toLowerCase() : '';
			var settings = getUploadSettings(input);
			var data = new FormData();
			var request = new XMLHttpRequest();

			if (!file) {
				resetUpload(input);
				return;
			}

			if (settings.allowedTypes.indexOf(extension) === -1) {
				failUpload(input, 'You are not allowed to upload files of this type.');
				return;
			}

			if (file.size > maxSize) {
				failUpload(input, 'The uploaded file is too large.');
				return;
			}

			if (!window.resumeUpload || !window.resumeUpload.ajaxUrl) {
				failUpload(input, 'Upload is not ready.');
				return;
			}

			if (elements.label) {
				elements.label.textContent = file.name;
			}

			if (elements.button) {
				elements.button.textContent = 'Uploading';
			}

			if (elements.upload) {
				elements.upload.classList.add('is-uploading');
				elements.upload.classList.remove('has-file');
			}

			if (elements.hidden) {
				elements.hidden.value = '';
			}

			if (elements.submit) {
				elements.submit.disabled = true;
			}

			setMessage(elements, 'Uploading...');

			data.append('action', settings.action);
			data.append('nonce', window.resumeUpload.nonce || '');
			data.append(input.name, file);

			request.open('POST', window.resumeUpload.ajaxUrl, true);
			request.onreadystatechange = function () {
				var response;

				if (request.readyState !== 4) {
					return;
				}

				if (elements.submit) {
					elements.submit.disabled = false;
				}

				try {
					response = JSON.parse(request.responseText);
				} catch (error) {
					failUpload(input, 'Upload failed.');
					return;
				}

				if (!response || !response.success || !response.data || !response.data.attachment_id) {
					failUpload(input, response && response.data ? response.data : 'Upload failed.');
					return;
				}

				if (elements.hidden) {
					elements.hidden.value = response.data.attachment_id;
				}

				if (elements.upload) {
					elements.upload.classList.remove('is-uploading');
					elements.upload.classList.add('has-file');
				}

				if (elements.button) {
					elements.button.textContent = 'Remove';
				}

				setMessage(elements, '');
			};

			request.send(data);
		}, true);
	}

	window.addEventListener('scroll', updateScrollState, { passive: true });
	window.addEventListener('load', updateScrollState);

	document.addEventListener('DOMContentLoaded', function () {
		var navbarToggler = document.querySelector('.header .navbar-toggler');
		var headerCanvas = document.getElementById('btn-canvas-navbar');

		updateScrollState();
		bindMobileSubmenus();
		initStafflinkApplicationUploads();
		initSelect2();

		if (navbarToggler && headerCanvas) {
			headerCanvas.addEventListener('show.bs.offcanvas', function () {
				navbarToggler.classList.add('active');
			});

			headerCanvas.addEventListener('hidden.bs.offcanvas', function () {
				navbarToggler.classList.remove('active');
			});
		}

		if (scrollButton) {
			scrollButton.addEventListener('click', function () {
				window.scrollTo({
					top: 0,
					behavior: 'smooth'
				});
			});
		}
	});
})();
