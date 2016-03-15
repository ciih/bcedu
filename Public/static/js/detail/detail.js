(function () {

	$('.dropdown-menu').on('click', 'a', function(e){
		e.preventDefault();
		var cnt = $(this).text();
		$(this).parents('.dropdown').find('.name').text(cnt);
	})

})();