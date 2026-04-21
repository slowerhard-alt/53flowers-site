(function ($) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	console.log('The script y4ymp-admin.js is loaded');

	$(document).ready(function () {

		console.log('DOM is ready to work');

		// Обработчик выбора option
		$('#y4ymp_exclude_cat_arr').on('click', 'option', function (e) {
			var isCtrlPressed = e.ctrlKey || e.metaKey; // проверка состояния Ctrl или Cmd

			if (!isCtrlPressed) { // очистка предыдущих выборов, если Ctrl не зажата
				$('option', '#y4ymp_exclude_cat_arr').prop('selected', false); // сбрасываем все выборы
			}

			selectCategory(this.value, true); // выбираем всю иерархию
		});

		/**
		 * Рекурсивно выбирает все элементы, начиная с указанной категории
		 */
		function selectCategory(categoryId, recursive) {
			var categoryOption = $(`#y4ymp_exclude_cat_arr option[value="${categoryId}"]`);

			if (categoryOption.length > 0) {
				categoryOption.prop('selected', true); // Отмечаем саму категорию

				// Если разрешено рекурсивное раскрытие (включено)
				if (recursive && categoryOption.data('parent') !== undefined) {
					let childrenOptions = $(`#y4ymp_exclude_cat_arr option[data-parent=${categoryId}]`);

					// Проходим по каждому дочернему элементу и снова запускаем процедуру
					childrenOptions.each(function () {
						selectCategory($(this).val(), true);
					});
				}
			}
		}

	});

})(jQuery);
