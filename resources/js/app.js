import './bootstrap';
import Chart from 'chart.js/auto';
import { icons } from 'lucide';

import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';
import 'flatpickr/dist/flatpickr.css';

// Expose Chart.js globally for blade templates
window.Chart = Chart;

// Expose Alpine globally
window.Alpine = Alpine;
Alpine.start();

// Font Awesome to Lucide PascalCase mapping
const faToLucide = {
	'fa-address-book': 'Contact',
	'fa-angles-left': 'ChevronsLeft',
	'fa-angles-right': 'ChevronsRight',
	'fa-arrow-down': 'ArrowDown',
	'fa-arrow-down-a-z': 'ArrowDownAZ',
	'fa-arrow-left': 'ArrowLeft',
	'fa-arrow-right': 'ArrowRight',
	'fa-arrow-rotate-left': 'RotateCcw',
	'fa-arrow-up': 'ArrowUp',
	'fa-arrow-up-a-z': 'ArrowUpAZ',
	'fa-ban': 'Ban',
	'fa-bars': 'Menu',
	'fa-barcode': 'Barcode',
	'fa-battery-empty': 'BatteryLow',
	'fa-bolt': 'Zap',
	'fa-book-open': 'BookOpen',
	'fa-box': 'Box',
	'fa-boxes-stacked': 'Boxes',
	'fa-box-open': 'PackageOpen',
	'fa-business-time': 'Briefcase',
	'fa-calendar': 'Calendar',
	'fa-calendar-alt': 'CalendarRange',
	'fa-calendar-check': 'CalendarCheck',
	'fa-calendar-day': 'CalendarDays',
	'fa-calendar-days': 'CalendarDays',
	'fa-calendar-minus': 'CalendarMinus',
	'fa-calendar-week': 'CalendarRange',
	'fa-camera': 'Camera',
	'fa-caret-left': 'ChevronLeft',
	'fa-caret-right': 'ChevronRight',
	'fa-cart-arrow-down': 'ShoppingCart',
	'fa-cart-plus': 'ShoppingCart',
	'fa-cart-shopping': 'ShoppingCart',
	'fa-cash-register': 'Store',
	'fa-chart-line': 'ChartLine',
	'fa-check': 'Check',
	'fa-check-circle': 'CircleCheck',
	'fa-chevron-down': 'ChevronDown',
	'fa-cube': 'Box',
	'fa-chevron-left': 'ChevronLeft',
	'fa-chevron-right': 'ChevronRight',
	'fa-chevron-up': 'ChevronUp',
	'fa-circle-check': 'CircleCheck',
	'fa-circle-exclamation': 'CircleAlert',
	'fa-circle-info': 'Info',
	'fa-circle-notch': 'LoaderCircle',
	'fa-circle-user': 'CircleUser',
	'fa-circle-xmark': 'CircleX',
	'fa-clipboard-check': 'ClipboardCheck',
	'fa-clipboard-list': 'ClipboardList',
	'fa-clock': 'Clock',
	'fa-cloud-arrow-up': 'CloudUpload',
	'fa-cloud-upload-alt': 'CloudUpload',
	'fa-database': 'Database',
	'fa-edit': 'Pencil',
	'fa-pen-to-square': 'Pencil',
	'fa-ellipsis-v': 'EllipsisVertical',
	'fa-ellipsis-vertical': 'EllipsisVertical',
	'fa-envelope': 'Mail',
	'fa-envelope-open': 'MailOpen',
	'fa-exclamation-circle': 'CircleAlert',
	'fa-exclamation-triangle': 'TriangleAlert',
	'fa-eye': 'Eye',
	'fa-eye-slash': 'EyeOff',
	'fa-file-arrow-down': 'FileDown',
	'fa-file-excel': 'FileSpreadsheet',
	'fa-file-import': 'FileInput',
	'fa-file-invoice': 'FileText',
	'fa-file-invoice-dollar': 'FileText',
	'fa-file-pdf': 'FileText',
	'fa-filter': 'Filter',
	'fa-filter-circle-xmark': 'FilterX',
	'fa-floppy-disk': 'Save',
	'fa-folder': 'Folder',
	'fa-folder-open': 'FolderOpen',
	'fa-gauge-high': 'Gauge',
	'fa-history': 'History',
	'fa-house': 'Home',
	'fa-id-card': 'IdCard',
	'fa-inbox': 'Inbox',
	'fa-info': 'Info',
	'fa-info-circle': 'Info',
	'fa-key': 'KeyRound',
	'fa-layer-group': 'Layers',
	'fa-lightbulb': 'Lightbulb',
	'fa-list': 'List',
	'fa-lock': 'Lock',
	'fa-minus': 'Minus',
	'fa-note-sticky': 'StickyNote',
	'fa-paper-plane': 'Send',
	'fa-pause-circle': 'CirclePause',
	'fa-pen': 'Pencil',
	'fa-phone': 'Phone',
	'fa-phone-slash': 'PhoneOff',
	'fa-play': 'Play',
	'fa-plus': 'Plus',
	'fa-plus-circle': 'CirclePlus',
	'fa-print': 'Printer',
	'fa-question-circle': 'CircleHelp',
	'fa-receipt': 'Receipt',
	'fa-rotate-left': 'RotateCcw',
	'fa-rotate-right': 'RotateCw',
	'fa-ruler': 'Ruler',
	'fa-ruler-horizontal': 'Ruler',
	'fa-save': 'Save',
	'fa-search': 'Search',
	'fa-search-minus': 'ZoomOut',
	'fa-seedling': 'Sprout',
	'fa-shield-alt': 'Shield',
	'fa-shield-check': 'ShieldCheck',
	'fa-shopping-cart': 'ShoppingCart',
	'fa-sign-in-alt': 'LogIn',
	'fa-sign-out-alt': 'LogOut',
	'fa-sliders': 'SlidersHorizontal',
	'fa-sliders-h': 'SlidersHorizontal',
	'fa-spinner': 'LoaderCircle',
	'fa-star': 'Star',
	'fa-store': 'Store',
	'fa-crown': 'Crown',
	'fa-tachometer-alt': 'Gauge',
	'fa-tags': 'Tags',
	'fa-times': 'X',
	'fa-times-circle': 'CircleX',
	'fa-trash': 'Trash2',
	'fa-trash-alt': 'Trash2',
	'fa-trash-can': 'Trash2',
	'fa-triangle-exclamation': 'TriangleAlert',
	'fa-truck-fast': 'Truck',
	'fa-upload': 'Upload',
	'fa-user': 'User',
	'fa-user-circle': 'CircleUser',
	'fa-user-edit': 'UserPen',
	'fa-user-gear': 'UserCog',
	'fa-user-plus': 'UserPlus',
	'fa-users': 'Users',
	'fa-users-cog': 'Users',
	'fa-user-shield': 'ShieldCheck',
	'fa-users-slash': 'Users',
	'fa-user-tag': 'BadgeCheck',
	'fa-user-tie': 'User',
	'fa-warehouse': 'Warehouse',
	'fa-wave-square': 'Activity',
	'fa-weight': 'Weight',
	'fa-weight-hanging': 'Scale',
	'fa-xmark': 'X',
	'fa-x': 'X'
};

// Classes to ignore when looking for icon name
const faIgnoreClasses = new Set(['fa', 'fas', 'far', 'fab', 'fa-solid', 'fa-regular', 'fa-brands', 'fa-spin', 'fa-fw', 'fa-lg', 'fa-2x', 'fa-3x']);

/**
 * Convert FA class to Lucide PascalCase icon name
 */
function getLucideIconName(classList) {
	for (const cls of classList) {
		if (cls.startsWith('fa-') && !faIgnoreClasses.has(cls)) {
			// Check if we have a mapping
			if (faToLucide[cls]) {
				return faToLucide[cls];
			}
			// Convert fa-some-icon to SomeIcon (PascalCase)
			const iconName = cls.replace(/^fa-/, '');
			return iconName.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join('');
		}
	}
	return 'CircleHelp'; // fallback
}

/**
 * Create SVG element from Lucide icon data
 */
function createSvgIcon(iconData, classes, size = '1.25em') {
	const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
	svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
	svg.setAttribute('width', size);
	svg.setAttribute('height', size);
	svg.setAttribute('viewBox', '0 0 24 24');
	svg.setAttribute('fill', 'none');
	svg.setAttribute('stroke', 'currentColor');
	svg.setAttribute('stroke-width', '2');
	svg.setAttribute('stroke-linecap', 'round');
	svg.setAttribute('stroke-linejoin', 'round');
	svg.setAttribute('class', classes);
	
	// Add icon paths
	iconData.forEach(([tag, attrs]) => {
		const child = document.createElementNS('http://www.w3.org/2000/svg', tag);
		Object.entries(attrs).forEach(([key, value]) => {
			child.setAttribute(key, String(value));
		});
		svg.appendChild(child);
	});
	
	return svg;
}

/**
 * Convert Font Awesome icons to Lucide SVG icons
 */
function convertFaToLucide() {
	const faIcons = document.querySelectorAll('i.fas, i.far, i.fab, i.fa-solid, i.fa-regular, i.fa-brands, i[class*="fa-"]');
	
	faIcons.forEach((el) => {
		// Skip if already processed
		if (el.dataset.lucideConverted === 'true') return;
		el.dataset.lucideConverted = 'true';
		
		const classList = Array.from(el.classList);
		const lucideIconName = getLucideIconName(classList);
		
		// Get icon data from Lucide
		const iconData = icons[lucideIconName] || icons['CircleHelp'];
		
		if (!iconData) {
			console.warn('Icon not found:', lucideIconName);
			return;
		}
		
		// Preserve non-FA classes
		const preservedClasses = classList.filter(cls => 
			!cls.startsWith('fa-') && 
			!['fa', 'fas', 'far', 'fab'].includes(cls)
		);
		preservedClasses.push('lucide');
		
		// Check for size classes and determine size - use 1em for consistent sizing with text
		let size = '1em';
		if (classList.some(c => c.includes('text-xs'))) size = '0.75rem';
		else if (classList.some(c => c.includes('text-sm'))) size = '0.875rem';
		else if (classList.some(c => c.includes('text-base'))) size = '1rem';
		else if (classList.some(c => c.includes('text-lg'))) size = '1.125rem';
		else if (classList.some(c => c.includes('text-xl'))) size = '1.25rem';
		else if (classList.some(c => c.includes('text-2xl'))) size = '1.5rem';
		
		// Create SVG
		const svg = createSvgIcon(iconData, preservedClasses.join(' '), size);
		
		// Handle spin animation
		if (classList.includes('fa-spin')) {
			svg.classList.add('animate-spin');
		}
		
		// Replace the <i> with <svg>
		if (el.parentNode) {
			el.parentNode.replaceChild(svg, el);
		}
	});
}

/**
 * Initialize datepickers with emerald theme
 */
let datepickerInitTimeout;

function initEmeraldDatepickers() {
	const elements = document.querySelectorAll('[data-datepicker]');

	elements.forEach((element) => {
		if (element._flatpickr) return;

		flatpickr(element, {
			dateFormat: 'Y-m-d',
			allowInput: true,
			locale: Indonesian,
			monthSelectorType: 'dropdown',
			minDate: element.dataset.minDate || null,
			maxDate: element.dataset.maxDate || null,
			disableMobile: true,
			prevArrow: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>',
			nextArrow: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>',
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
}

function scheduleDatepickerInit() {
	clearTimeout(datepickerInitTimeout);
	datepickerInitTimeout = setTimeout(initEmeraldDatepickers, 50);
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
	convertFaToLucide();
	scheduleDatepickerInit();
});

// Watch for dynamically added content
const observer = new MutationObserver((mutations) => {
	let hasNewIcons = false;
	
	mutations.forEach(({ addedNodes }) => {
		addedNodes.forEach((node) => {
			if (node.nodeType !== 1) return;
			
			if (node.matches?.('i[class*="fa-"], i.fas, i.far, i.fab') ||
				node.querySelector?.('i[class*="fa-"], i.fas, i.far, i.fab')) {
				hasNewIcons = true;
			}
		});
	});
	
	if (hasNewIcons) {
		convertFaToLucide();
	}
	
	scheduleDatepickerInit();
});

observer.observe(document.documentElement, { childList: true, subtree: true });
