(function () {

  var classifyEl = $('.school-classify');
  var schooltype = classifyEl.attr('data-schooltype');

  var schoolYearEl = $('#schoolyear-dropdown');
  var schoolTermEl = $('#schoolterm-dropdown');
  var schoolGradeEl = $('#grade-dropdown');

  var examInfo;
  var courseList = [],
      examName = [],
      uploaddate;

  var schoolyear = [],
      schoolterm = [],
      grade = [],
      examname = [];

  var schoollist = [];

  $.get("/home/Queryexam/ajax_get_school", {schooltype: schooltype}, function(data){
    schoollist = $.parseJSON(data);
  });

  $.get("/home/Queryexam/ajax_get_exam", {schooltype: schooltype}, function(data){
    if(data) {

      var contList1 = '';
      var contList2 = '';
      var contList3 = '';

      examInfo = $.parseJSON(data);
      console.log(examInfo);

      for (var i = 0; i < examInfo.length; i++) {
        schoolyear.push(examInfo[i].schoolyear);
        schoolterm.push(examInfo[i].schoolterm);
        grade.push(examInfo[i].grade);
      }

      schoolyear = $.unique(schoolyear);
      schoolterm = $.unique(schoolterm);
      grade = $.unique(grade);

      for (var i in examInfo) {
        if(examInfo[i].schoolyear == schoolyear[schoolyear.length-1] && examInfo[i].schoolterm == schoolterm[schoolterm.length-1] && examInfo[i].grade == grade[grade.length-1]) {
          examname.push(examInfo[i].examname);
        }
      }

      examname = $.unique(examname);

      for (var i = 0; i < schoolyear.length; i++) {
        contList1 += '<li><a href="#">' + schoolyear[i] + '</a></li>'
      }

      for (var i = 0; i < schoolterm.length; i++) {
        contList2 += '<li><a href="#">' + schoolterm[i] + '</a></li>'
      }

      for (var i = 0; i < grade.length; i++) {
        contList3 += '<li><a href="#">' + grade[i] + '</a></li>'
      }

      schoolYearEl.children('.dropdown-menu').html(contList1);
      schoolTermEl.children('.dropdown-menu').html(contList2);
      schoolGradeEl.children('.dropdown-menu').html(contList3);

      schoolYearEl.find('.name').text(schoolyear[schoolyear.length-1]);
      schoolTermEl.find('.name').text(schoolterm[schoolterm.length-1]);
      schoolGradeEl.find('.name').text(grade[grade.length-1]);

      var examfullname = '';

      for (var i = 0; i < examname.length; i++) {
        examfullname = schoolYearEl.find('.name').text() + '学年' + schoolTermEl.find('.name').text() + schoolGradeEl.find('.name').text() + examname[i];
        for (var j in examInfo) {
          if(examInfo[j].fullname == examfullname) {
            courseList.push(examInfo[j].courselist);
            examName.push(examInfo[j].fullname);
          }
        }
      }

      cntTPL(examName, courseList);

    } else {
      classifyEl.find('.btn-search').addClass('disabled');
      schoolYearEl.find('.dropdown-toggle').addClass('disabled');
      schoolTermEl.find('.dropdown-toggle').addClass('disabled');
      schoolGradeEl.find('.dropdown-toggle').addClass('disabled');
    }
  });

  $('.dropdown-menu').on('click', 'a', function(){
      var currType = $(this).parents('.dropdown-menu').attr('aria-labelledby').split('-')[0],
          currData = $(this).text();

      switch(currType)
      {
        case 'schoolyear':
          schoolterm = [];
          var contList = '';
          for (var i = 0; i < examInfo.length; i++) {
            if(currData == examInfo[i].schoolyear) {
              schoolterm.push(examInfo[i].schoolterm);
            }
          }
          schoolterm = $.unique(schoolterm);

          for (var i = 0; i < schoolterm.length; i++) {
            contList += '<li><a href="#">' + schoolterm[i] + '</a></li>'
          }

          schoolTermEl.find('.name').text('学期');
          schoolGradeEl.find('.name').text('年级');
          schoolTermEl.children('.dropdown-menu').html(contList);
          schoolTermEl.find('.dropdown-toggle').removeClass('disabled');
          if(!schoolGradeEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolGradeEl.find('.dropdown-toggle').addClass('disabled')
          }
          if(!$('.btn-search').hasClass('disabled')) {
            $('.btn-search').addClass('disabled')
          }
          break;

        case 'schoolterm':
          grade = [];
          var schoolyear = schoolYearEl.find('.name').text();
          var contList = '';
          for (var i = 0; i < examInfo.length; i++) {
            if(currData == examInfo[i].schoolterm && schoolyear == examInfo[i].schoolyear) {
              grade.push(examInfo[i].grade);
            }
          }
          grade = $.unique(grade);

          for (var i = 0; i < grade.length; i++) {
            contList += '<li><a href="#">' + grade[i] + '</a></li>'
          }

          schoolGradeEl.find('.name').text('年级');
          schoolGradeEl.children('.dropdown-menu').html(contList);
          schoolGradeEl.find('.dropdown-toggle').removeClass('disabled');
          if(!$('.btn-search').hasClass('disabled')) {
            $('.btn-search').addClass('disabled')
          }
          
          break;
          
        case 'grade':
          var schoolyear = schoolYearEl.find('.name').text();
          var schoolterm = schoolTermEl.find('.name').text();
          courseList = [];
          examName = [];
          uploaddate = [];
          for (var i = 0; i < examInfo.length; i++) {
            if(currData == examInfo[i].grade && schoolyear == examInfo[i].schoolyear && schoolterm == examInfo[i].schoolterm) {
              courseList.push(examInfo[i].courselist);
              examName.push(examInfo[i].fullname);
              uploaddate.push(examInfo[i].uploaddate);
            }
          }
          $('.btn-search').removeClass('disabled');
          
          break;
      }

  });

  $('.btn-search').on('click', function(){
    cntTPL(examName, courseList);
  });

  function cntTPL(name, list) {
    var listEl = $('.table-section tbody');
    var len = name.length,
        tpl = '',
        link = '';

    for (var i = 0; i < len; i++) {
      var listItem = list[i].split(',')
      tpl += '<tr>';
      tpl += '<td>' + name[i] + '</td>';
      tpl += '<td>';
      for (var j = 0; j < listItem.length; j++) {
        link = '/Data/Word/' + name[i] + '/' + listItem[j] + '.docx';
        tpl += '<a href="'+ link +'">'+ listItem[j] +'</a>';
      }
      tpl += '</td>';
      tpl += '</tr>';
    }
    listEl.html(tpl);
    $('.table-section').show();
  }

})();