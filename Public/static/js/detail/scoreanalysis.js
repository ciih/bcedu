(function () {

  var classifyEl = $('.school-classify');
  var schooltype = classifyEl.attr('data-schooltype'),
      count      = 1;

  var schoolYearEl = $('#schoolyear-dropdown');
  var schoolTermEl = $('#schoolterm-dropdown');
  var schoolGradeEl = $('#grade-dropdown');
  var schoolExamnameEl = $('#examname-dropdown');

  var fullname = '',
      uploaddate = '',
      courselist = '';

  $.get("/home/Queryexam/ajax_get_exam", {schooltype: schooltype, count: count}, function(data){
    if(data) {
      var examInfo = $.parseJSON(data),
          schoolyear = examInfo[0].schoolyear,
          schoolterm = examInfo[0].schoolterm,
          grade = examInfo[0].grade,
          examname = examInfo[0].examname;

      fullname = examInfo[0].fullname;
      uploaddate = examInfo[0].uploaddate;
      courselist = examInfo[0].courselist;

      schoolYearEl.find('.name').text(schoolyear);
      schoolTermEl.find('.name').text(schoolterm);
      schoolGradeEl.find('.name').text(grade);
      schoolExamnameEl.find('.name').text(examname);

      schoolYearEl.children('.dropdown-menu').html('<li><a href="#">' + schoolyear + '</a></li>');
      schoolTermEl.children('.dropdown-menu').html('<li><a href="#">' + schoolterm + '</a></li>');
      schoolGradeEl.children('.dropdown-menu').html('<li><a href="#">' + grade + '</a></li>');
      schoolExamnameEl.children('.dropdown-menu').html('<li><a href="#">' + examname + '</a></li>');
      
      var listEl = $('.examinfo-list ul');
      var course = courselist.split(','),
          len = course.length,
          tpl = '',
          link = '';

      for (var i = 0; i < len; i++) {
          link = '/Data/Word/' + fullname + '/' + course[i] + '.docx';
          tpl += '<li><a href="'+ link +'"><img src="../Public/static/img/icon_book.jpg" />'+ course[i] +'</a></li>';
      }
      listEl.html(tpl);

    } else {
      classifyEl.find('.btn-search').addClass('disabled');
      schoolYearEl.find('.dropdown-toggle').addClass('disabled');
      schoolTermEl.find('.dropdown-toggle').addClass('disabled');
      schoolGradeEl.find('.dropdown-toggle').addClass('disabled');
      schoolExamnameEl.find('.dropdown-toggle').addClass('disabled');
    }
  });

  $('.btn-search').on('click', function(){
    cntTPL(courselist);
  })

  function cntTPL(list) {
    var listEl = $('.examinfo-list ul');
    var course = list.split(','),
        len = course.length,
        tpl = '',
        link = '';

    for (var i = 0; i < len; i++) {
        link = '/Data/Word/' + fullname + '/' + course[i] + '.docx';
        tpl += '<li><a href="'+ link +'"><img src="../Public/static/img/icon_book.jpg" />'+ course[i] +'</a></li>';
    }
    listEl.html(tpl);
  }
 
})();