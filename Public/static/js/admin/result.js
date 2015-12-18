(function(){
    $('#btn-upload').on('click', function(){
        $('.portlet-title .caption span').text('正在上传中');
        $('.upload-section').hide();
        $('.upload-loading').show();
    })
})();