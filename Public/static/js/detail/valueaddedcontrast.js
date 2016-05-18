(function () {

  var classifyEl = $('.school-classify-section');
  var schooltype = classifyEl.attr('data-schooltype');
  var schoolname = classifyEl.attr('data-schoolname');

  var schoolYearEl,
      schoolTermEl,
      schoolGradeEl,
      schoolCourseEl; 

  var examInfo;

  var course,
      examFullname;

  var schoolyearList = [],
      schooltermList = ['第一学期', '第二学期'],
      examnameList = ['一模考试', '二模考试', '三模考试', '四模考试', '五模考试', '六模考试', '七模考试', '期中考试', '期末考试'];

  $.get("/home/Queryexam/ajax_get_exam", {schooltype: schooltype}, function(data){
    if(data) {

      var contList = '';

      examInfo = $.parseJSON(data);

      for (var i = 0; i < examInfo.length; i++) {
        schoolyearList.push(examInfo[i].schoolyear);
      }

      schoolyearList = $.unique(schoolyearList);
      schoolyearList.sort();

      for (var i = 0; i < schoolyearList.length; i++) {
        contList += '<li><a href="#">' + schoolyearList[i] + '</a></li>';
      }

      classifyEl.find('.school-classify').each(function() {
        $(this).find('.dropdown:first .dropdown-menu').html(contList);
      });

    } else {
      $('.btn-submit').addClass('disabled');
    }
  });

  $('.school-classify-section .dropdown-menu').on('click', 'a', function(){
      var currType = $(this).parents('.dropdown-menu').attr('aria-labelledby').split('-')[0],
          currData = $(this).text(),
          currNum = $(this).parents('.school-classify').attr('data-itemnum');

      schoolYearEl = $('#schoolyear' + currNum + '-dropdown');
      schoolTermEl = $('#schoolterm' + currNum + '-dropdown');
      schoolGradeEl = $('#grade' + currNum + '-dropdown');
      schoolCourseEl = $('#course' + currNum + '-dropdown');

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

          contList += '<li><a href="#">全年</a></li>';

          for (var i = 0; i < schoolterm.length; i++) {
            contList += '<li><a href="#">' + schoolterm[i] + '</a></li>';
          }

          schoolTermEl.find('.name').text('学期');
          schoolGradeEl.find('.name').text('年级');
          schoolCourseEl.find('.name').text('考试科目');
          schoolTermEl.children('.dropdown-menu').html(contList);
          schoolTermEl.find('.dropdown-toggle').removeClass('disabled');

          if(!schoolGradeEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolGradeEl.find('.dropdown-toggle').addClass('disabled');
          }
          if(!schoolCourseEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolCourseEl.find('.dropdown-toggle').addClass('disabled');
          }
          if(!$('.btn-search').hasClass('disabled')) {
            $('.btn-search').addClass('disabled');
          }
          
          break;
        case 'schoolterm':
          grade = [];
          var schoolyear = schoolYearEl.find('.name').text();
          var contList = '';
          for (var i = 0; i < examInfo.length; i++) {
            if(currData == '全年' && schoolyear == examInfo[i].schoolyear) {
              grade.push(examInfo[i].grade);
            } else if(currData == examInfo[i].schoolterm && schoolyear == examInfo[i].schoolyear) {
              grade.push(examInfo[i].grade);
            }
          }
          grade = $.unique(grade);

          for (var i = 0; i < grade.length; i++) {
            contList += '<li><a href="#">' + grade[i] + '</a></li>';
          }

          schoolGradeEl.find('.name').text('年级');
          schoolCourseEl.find('.name').text('考试科目');
          schoolGradeEl.children('.dropdown-menu').html(contList);

          schoolGradeEl.find('.dropdown-toggle').removeClass('disabled');

          if(!schoolCourseEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolCourseEl.find('.dropdown-toggle').addClass('disabled');
          }
          if(!$('.btn-search').hasClass('disabled')) {
            $('.btn-search').addClass('disabled');
          }

          break;
        case 'grade':
          var schoolyear = schoolYearEl.find('.name').text();
          var schoolterm = schoolTermEl.find('.name').text();
          var schoolgrade = schoolGradeEl.find('.name').text();

          var courselist = ['理科','文科'];
          var contList = '';

          for (var i = 0; i < courselist.length; i++) {
            contList += '<li><a href="#">' + courselist[i] + '</a></li>';
          }

          schoolCourseEl.find('.name').text('考试科目');

          schoolCourseEl.children('.dropdown-menu').html(contList);
          schoolCourseEl.find('.dropdown-toggle').removeClass('disabled');

          if(!$('.btn-search').hasClass('disabled')) {
            $('.btn-search').addClass('disabled');
          }
          
          break;
        case 'course':
          if(schoolCourseEl.parents('.school-classify').attr('data-itemnum') == $('#num-dropdown').find('.name').text()) {
            $('.btn-search').removeClass('disabled');
          }
          break;
      }
  });

  $('.btn-submit').on('click', function() {
    var curNum = $('#num-dropdown').find('.name').text();

    if(curNum == 3) {
      $('#grade1-dropdown').css('visibility', 'hidden');
      $('#course1-dropdown').css('visibility', 'hidden');
      $('#grade2-dropdown').css('visibility', 'hidden');
      $('#course2-dropdown').css('visibility', 'hidden');
    } else if(curNum == 2) {
      $('#grade1-dropdown').css('visibility', 'hidden');
      $('#course1-dropdown').css('visibility', 'hidden');
      $('#grade2-dropdown').css('visibility', 'visible');
      $('#course2-dropdown').css('visibility', 'visible');
    } else if(curNum == 1) {
      $('#grade1-dropdown').css('visibility', 'visible');
      $('#course1-dropdown').css('visibility', 'visible');
    }

    $('.school-classify-section').find('.school-classify').hide();
    $('.school-classify-section').find('.school-classify .btn-search').css('visibility', 'hidden');

    for (var i = 1; i <= curNum; i++) {
      $('#schoolyear' + i + '-dropdown').find('.name').text('学年');
      $('#schoolterm' + i + '-dropdown').find('.name').text('学期');
      if(!$('#schoolterm' + i + '-dropdown').find('.dropdown-toggle').hasClass('disabled')) {
        $('#schoolterm' + i + '-dropdown').find('.dropdown-toggle').addClass('disabled');
      }
      $('#grade' + i + '-dropdown').find('.name').text('年级');
      if(!$('#grade' + i + '-dropdown').find('.dropdown-toggle').hasClass('disabled')) {
        $('#grade' + i + '-dropdown').find('.dropdown-toggle').addClass('disabled');
      }
      $('#course' + i + '-dropdown').find('.name').text('考试科目');
      if(!$('#course' + i + '-dropdown').find('.dropdown-toggle').hasClass('disabled')) {
        $('#course' + i + '-dropdown').find('.dropdown-toggle').addClass('disabled');
      }
      $('.school-classify-section').find('.school-classify[data-itemnum="' + i + '"]').show();
    }
    if(!$('.school-classify-section').find('.school-classify[data-itemnum="' + curNum + '"] .btn-search').hasClass('disabled')) {
      $('.school-classify-section').find('.school-classify[data-itemnum="' + curNum + '"] .btn-search').addClass('disabled');
    }
    $('.school-classify-section').find('.school-classify[data-itemnum="' + curNum + '"] .btn-search').css('visibility', 'visible');
    $('.school-classify-section').show();
  })

  $('.btn-search').on('click', function(){
    if(!$(this).hasClass('disabled')) {

      $('.highcharts-section-load').show();
      $('#highcharts-section').hide();
      $('.btn-search').addClass('disabled');
      
      var currNum = $(this).parents('.school-classify').attr('data-itemnum');
      var course = $('#course' + currNum + '-dropdown').find('.name').text();
      var schoolyear = [],
          schoolterm = [],
          schoolgrade = [],
          course = [];
      var _schoolyear = [],
          _schoolterm = [],
          _schoolgrade = [],
          _course = [];

      for (var i = 1; i <= currNum; i++) {
        _schoolyear[i-1] = $('#schoolyear' + i + '-dropdown').find('.name').text() == '学年' ? null : $('#schoolyear' + i + '-dropdown').find('.name').text();
        _schoolterm[i-1] = $('#schoolterm' + i + '-dropdown').find('.name').text() == '学期' ? null : $('#schoolterm' + i + '-dropdown').find('.name').text();
        if(currNum == 1) {
          _schoolgrade[i-1] = $('#grade' + i + '-dropdown').find('.name').text() == '年级' ? null : $('#grade' + i + '-dropdown').find('.name').text();
          _course[i-1] = $('#course' + i + '-dropdown').find('.name').text() == '考试科目' ? null : $('#course' + i + '-dropdown').find('.name').text();
        } else {
          _schoolgrade[i-1] = $('#grade' + currNum + '-dropdown').find('.name').text() == '年级' ? null : $('#grade' + currNum + '-dropdown').find('.name').text();
          _course[i-1] = $('#course' + currNum + '-dropdown').find('.name').text() == '考试科目' ? null : $('#course' + currNum + '-dropdown').find('.name').text();
        }
      }

      var examnameFullname = [];
      var examnameSplit;

      for (var i = 0; i < _schoolyear.length; i++) {
        if(_schoolterm[i] == '全年') {
          examnameFullname.push(_schoolyear[i] + '&第一学期&' + _schoolgrade[i] + '&' + _course[i]);
          examnameFullname.push(_schoolyear[i] + '&第二学期&' + _schoolgrade[i] + '&' + _course[i]);
        } else {
          examnameFullname.push(_schoolyear[i] + '&' + _schoolterm[i] + '&' + _schoolgrade[i] + '&' + _course[i]);
        }
      }

      examnameFullname = $.unique(examnameFullname);

      for (var i = 0; i < examnameFullname.length; i++) {
        examnameSplit = examnameFullname[i].split('&');
        schoolyear[i] = examnameSplit[0];
        schoolterm[i] = examnameSplit[1];
        schoolgrade[i] = examnameSplit[2];
        course[i] = examnameSplit[3];
      }

      $.get("/home/Queryexam/ajax_get_zvalue", {schoolyear: schoolyear, schoolterm: schoolterm, schoolgrade: schoolgrade, course: course, datatype: 'contrast'}, function(data){
        if(data) {
          var zvalueData = $.parseJSON(data);
          console.log(zvalueData);

          var zvalueList = [];

          var examname = [];

          var scoreList = [];

          var scoreSchoolList = [];

          var courseCont = [];

          var midnum = 0;
          var i = 0,
              j = 0,
              k = 0,
              l = 0;

          for (var item in zvalueData) {
            for (i = 0; i < zvalueData[item].length; i++) {
              zvalueList.push(zvalueData[item][i]);
            }
          }

          var zvalueSortList = [];

          for (i = 0; i < schoolyearList.length; i++) {
            for (j = 0; j < schooltermList.length; j++) {
              for (k = 0; k < examnameList.length; k++) {
                for (l = 0; l < zvalueList.length; l++) {
                  if(zvalueList[l].schoolyear == schoolyearList[i] && zvalueList[l].schoolterm == schooltermList[j] && zvalueList[l].examname == examnameList[k]) {
                    zvalueSortList.push(zvalueList[l]);
                  }
                }
              }
            }
          }

          console.log(zvalueSortList);

          zvalueList = zvalueSortList;

          for (i = 0; i < zvalueList.length; i++) {
            examname.push(zvalueList[i].schoolyear + '学年' + zvalueList[i].schoolterm + zvalueList[i].examname);
          }

          for (i = 0; i < zvalueList[0]['schoolName'].length; i++) {
            scoreSchoolList = [];
            for (j = 0; j < zvalueList.length; j++) {
              scoreSchoolList.push(parseFloat(zvalueList[j]['score'][i]));
            }
            scoreList.push(scoreSchoolList);
          }

          for (i = 0; i < zvalueList[0]['schoolName'].length; i++) {
            courseCont.push({name:zvalueList[0]['schoolName'][i], data:scoreList[i]});
          }

          valueaddedObj = {
            title: {
                text: course + '总分增值性评价'
            },
            xAxis: {
                categories: examname
            },
            yAxis: {
                title: {
                    text: '分数'
                }
            },
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom',
                borderWidth: 0
            },
            series: courseCont
          }
          
          $('.highcharts-section-load').hide();
          $('#highcharts-section').show();
          $('.btn-search').removeClass('disabled');

          cntTPL(valueaddedObj);
        }
      });
    }
  });

  function cntTPL(obj) {
    $('#highcharts-section').highcharts(obj);
  }
})();