(function () {

  var classifyEl = $('.school-classify');
  var schooltype = classifyEl.attr('data-schooltype'),
      count      = 1;

  var userEl = $('.login-info');
  var username = userEl.attr('data-username'),
      schoolgroup = userEl.attr('data-schoolgroup'),
      role = parseInt(userEl.attr('data-role'));

  var schoolYearEl = $('#schoolyear-dropdown');
  var schoolTermEl = $('#schoolterm-dropdown');
  var schoolGradeEl = $('#grade-dropdown');
  var schoolExamnameEl = $('#examname-dropdown');

  var fullname = '',
      uploaddate = '',
      courselist = '';

  var courseEnglishName = username.split('-')[1];

  var courseObj = {
    "yuwen" : "语文",
    "shuxue" : "数学",
    "yingyu" : "英语",
    "wuli" : "物理",
    "huaxue" : "化学",
    "shengwu" : "生物",
    "zhengzhi" : "政治",
    "lishi" : "历史",
    "dili" : "地理"
  };

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

      if(role < 3) {
        for (var i = 0; i < len; i++) {
          link = '/Data/Word/' + fullname + '/' + course[i] + '.docx';
          tpl += '<li><a href="'+ link +'"><img src="../Public/static/img/icon_book.jpg" />'+ course[i] +'</a></li>';
        }
      } else if(role == 3) {
        if(courseObj[courseEnglishName] == '数学' && (grade == '高二年级' || grade == '高三年级')) {
          link = '/Data/Word/' + fullname + '/' + courseObj[courseEnglishName] + '(文).docx';
          tpl += '<li><a href="'+ link +'"><img src="../Public/static/img/icon_book.jpg" />'+ courseObj[courseEnglishName] +'(文)</a></li>';
          link = '/Data/Word/' + fullname + '/' + courseObj[courseEnglishName] + '(理).docx';
          tpl += '<li><a href="'+ link +'"><img src="../Public/static/img/icon_book.jpg" />'+ courseObj[courseEnglishName] +'(理)</a></li>';
        } else {
          link = '/Data/Word/' + fullname + '/' + courseObj[courseEnglishName] + '.docx';
          tpl += '<li><a href="'+ link +'"><img src="../Public/static/img/icon_book.jpg" />'+ courseObj[courseEnglishName] +'</a></li>';
        }
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

    if(role < 3) {
      for (var i = 0; i < len; i++) {
        link = '/Data/Word/' + fullname + '/' + course[i] + '.docx';
        tpl += '<li><a href="'+ link +'"><img src="../Public/static/img/icon_book.jpg" />'+ course[i] +'</a></li>';
      }
    } else if(role == 3) {
      if(courseObj[courseEnglishName] == '数学' && (grade == '高二年级' || grade == '高三年级')) {
        link = '/Data/Word/' + fullname + '/' + courseObj[courseEnglishName] + '(文).docx';
        tpl += '<li><a href="'+ link +'"><img src="../Public/static/img/icon_book.jpg" />'+ courseObj[courseEnglishName] +'(文)</a></li>';
        link = '/Data/Word/' + fullname + '/' + courseObj[courseEnglishName] + '(理).docx';
        tpl += '<li><a href="'+ link +'"><img src="../Public/static/img/icon_book.jpg" />'+ courseObj[courseEnglishName] +'(理)</a></li>';
      } else {
        link = '/Data/Word/' + fullname + '/' + courseObj[courseEnglishName] + '.docx';
        tpl += '<li><a href="'+ link +'"><img src="../Public/static/img/icon_book.jpg" />'+ courseObj[courseEnglishName] +'</a></li>';
      }
    }
    listEl.html(tpl);
  }
 
})();