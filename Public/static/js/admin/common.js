(function(){
    $('.btn-ok').on('click', function(){
    	var title = $('.upload-loading img').attr('title');
        $('.portlet-title .caption span').text(title);
        $('.info-section').hide();
        $('.loading').show();
    })
})();