import './bootstrap';

import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';
import 'flatpickr/dist/flatpickr.css';

window.Alpine = Alpine;

Alpine.start();

let datepickerInitTimeout;

const initEmeraldDatepickers = () => {
	const elements = document.querySelectorAll('[data-datepicker]');

	elements.forEach((element) => {
		if (element._flatpickr) {
			return;
		}

		flatpickr(element, {
			dateFormat: 'Y-m-d',
			allowInput: true,
			locale: Indonesian,
			monthSelectorType: 'dropdown',
			minDate: element.dataset.minDate || null,
			maxDate: element.dataset.maxDate || null,
			disableMobile: true,
			prevArrow: '<span class="flatpickr-nav-button" aria-label="Bulan sebelumnya"><i class="fas fa-chevron-left"></i></span>',
			nextArrow: '<span class="flatpickr-nav-button" aria-label="Bulan berikutnya"><i class="fas fa-chevron-right"></i></span>',
			onReady(selectedDates, dateStr, instance) {
				instance.calendarContainer.classList.add('flatpickr-theme-emerald');
			},
			onOpen(selectedDates, dateStr, instance) {
				instance.calendarContainer.classList.add('flatpickr-theme-emerald');
			},
			onChange() {
				element.dispatchEvent(new Event('input', { bubbles: true }));
				element.dispatchEvent(new Event('change', { bubbles: true }));
			}
		});
	});
};

const scheduleDatepickerInit = () => {
	clearTimeout(datepickerInitTimeout);
	datepickerInitTimeout = setTimeout(initEmeraldDatepickers, 50);
};

document.addEventListener('DOMContentLoaded', scheduleDatepickerInit);

const observer = new MutationObserver(() => scheduleDatepickerInit());
observer.observe(document.documentElement, { childList: true, subtree: true });
