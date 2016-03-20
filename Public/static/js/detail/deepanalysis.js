(function () {

  var classifyEl = $('.school-classify');
  var schooltype = classifyEl.attr('data-schooltype');

  var schoolYearEl = $('#schoolyear-dropdown');
  var schoolTermEl = $('#schoolterm-dropdown');
  var schoolGradeEl = $('#grade-dropdown');

  var examInfo;
  var courseList,
      examName,
      uploaddate;

  var schoolyear = [],
      schoolterm = [],
      grade = [];

  var schoollist = [];

  $.get("/home/Queryexam/ajax_get_exam", {schooltype: schooltype}, function(data){
    if(data) {

      var contList = '';

      examInfo = $.parseJSON(data);

      for (var i = 0; i < examInfo.length; i++) {
        schoolyear.push(examInfo[i].schoolyear);
      }

      schoolyear = $.unique(schoolyear);

      for (var i = 0; i < schoolyear.length; i++) {
        contList += '<li><a href="#">' + schoolyear[i] + '</a></li>'
      }

      schoolYearEl.children('.dropdown-menu').html(contList);

    } else {
      classifyEl.find('.btn-search').addClass('disabled');
      schoolYearEl.find('.dropdown-toggle').addClass('disabled');
      schoolTermEl.find('.dropdown-toggle').addClass('disabled');
      schoolGradeEl.find('.dropdown-toggle').addClass('disabled');
    }
  });

  $.get("/home/Queryexam/ajax_get_school", {schooltype: schooltype}, function(data){
    schoollist = $.parseJSON(data);
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