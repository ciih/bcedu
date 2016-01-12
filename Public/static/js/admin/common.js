(function(){
    $('.btn-ok').on('click', function(){
    	var title = $('.upload-loading img').attr('title');
        $('.portlet-title .caption span').text(title);
        $('.info-section').hide();
        $('.loading').show();
    })

    $('.form-control').on('blur', function(){

    	var score = $(this).val();

    	if(!/^[0-9]*$/.test(score)) {  
		    alert("请输入数字!");
		}
		if(score < 0 || score > 200) {
		    alert("注意分数的上限是否正确！！");
		}
    })

})();