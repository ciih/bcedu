(function () {

  var classifyEl = $('.school-classify');
  var schooltype = classifyEl.attr('data-schooltype');
  var schoolname = classifyEl.attr('data-schoolname');

  var schoolYearEl = $('#schoolyear-dropdown');
  var schoolTermEl = $('#schoolterm-dropdown');
  var schoolGradeEl = $('#grade-dropdown');
  var schoolCourseEl = $('#course-dropdown');

  var examInfo;

  var course,
      examFullname;

  var schoolyear = [],
      schoolterm = [],
      grade = [],
      examname = [];

  $.get("/home/Queryexam/ajax_get_exam", {schooltype: schooltype}, function(data){
    if(data) {

      var contList = '';

      examInfo = $.parseJSON(data);

      for (var i = 0; i < examInfo.length; i++) {
        schoolyear.push(examInfo[i].schoolyear);
      }

      schoolyear = $.unique(schoolyear);

      for (var i = 0; i < schoolyear.length; i++) {
        contList += '<li><a href="#">' + schoolyear[i] + '</a></li>';
      }

      schoolYearEl.children('.dropdown-menu').html(contList);

    } else {
      classifyEl.find('.btn-search').addClass('disabled');
      schoolYearEl.find('.dropdown-toggle').addClass('disabled');
      schoolTermEl.find('.dropdown-toggle').addClass('disabled');
      schoolGradeEl.find('.dropdown-toggle').addClass('disabled');
      schoolCourseEl.find('.dropdown-toggle').addClass('disabled');
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
            schoolGradeEl.find('.dropdown-toggle').addClass('disabled')
          }
          if(!schoolCourseEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolCourseEl.find('.dropdown-toggle').addClass('disabled')
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
            schoolCourseEl.find('.dropdown-toggle').addClass('disabled')
          }
          if(!$('.btn-search').hasClass('disabled')) {
            $('.btn-search').addClass('disabled')
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
            $('.btn-search').addClass('disabled')
          }
          
          break;
        case 'course':
          $('.btn-search').removeClass('disabled');
          
          break;
      }

  });

  $('.btn-search').on('click', function(){

    $('#highcharts-section .highcharts-load').show();

    var schoolyear = schoolYearEl.find('.name').text();
    var schoolterm = schoolTermEl.find('.name').text();
    var schoolgrade = schoolGradeEl.find('.name').text();
    var course = schoolCourseEl.find('.name').text();

    $.get("/home/Queryexam/ajax_get_zvalue", {schoolyear: schoolyear, schoolterm: schoolterm, schoolgrade: schoolgrade, course: course, datatype: 'multi'}, function(data){
      if(data) {
        var zvalueData = $.parseJSON(data);
        // console.log(zvalueData);
        var term = [],
            examnameFirst = [],
            examnameSecond = [],
            examname = [],
            scoreList = [];
        var courseCont = [];
        var schoolList = [];

        var midnum = 0;
        var i = 0;

        for (var i = 0; i < zvalueData.length; i++) {
          term.push(zvalueData[i]['schoolterm']);
          if(zvalueData[i]['schoolterm'] == '第一学期') {
            examnameFirst.push(zvalueData[i]['examname']);
          } else if(zvalueData[i]['schoolterm'] == '第二学期') {
            examnameSecond.push(zvalueData[i]['examname']);
          }
        }

        examname = examname.concat(examnameFirst, examnameSecond);

        for (i = 0; i < zvalueData[0]['schoolName'].length; i++) {
          scoreList[i] = [];
        }

        for (i = 0; i < examnameFirst.length; i++) {
          for (var j = 0; j < zvalueData.length; j++) {
            if(examnameFirst[i] == zvalueData[j]['examname'] && zvalueData[j]['schoolterm'] == '第一学期') {
              for (var k = 0; k < zvalueData[j]['score'].length; k++) {
                scoreList[k][i] = parseFloat(zvalueData[j]['score'][k]);
              }
            }
          }
        }

        midnum = examnameFirst.length;
        for (i = 0; i < examnameSecond.length; i++) {
          for (var j = 0; j < zvalueData.length; j++) {
            if(examnameSecond[i] == zvalueData[j]['examname'] && zvalueData[j]['schoolterm'] == '第二学期') {
              for (var k = 0; k < zvalueData[j]['score'].length; k++) {
                scoreList[k][midnum] = parseFloat(zvalueData[j]['score'][k]);
              }
            }
          }
          midnum++;
        }

        /*var mockData = [
          [7.0, 6.9, 9.5],
          [-0.2, 0.8, 5.7],
          [-0.9, 0.6, 3.5],
          [3.9, 4.2, 5.7],
          [15.2, 17.0, 16.6],
          [18.2, 21.5, 25.2]
        ];*/

        for (i = 0; i < zvalueData[0]['schoolName'].length; i++) {
          // console.log(zvalueData[0]['schoolName'][i]+":::"+scoreList[i]);
          courseCont.push({name:zvalueData[0]['schoolName'][i], data:scoreList[i]});
          // courseCont.push({name:zvalueData[0]['schoolName'][i], data:mockData[i]});
        }

        valueaddedObj = {
          title: {
              text: course + '总分增值性评价'
              // x: -20 //center
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

        cntTPL(valueaddedObj);
      }
    });
  });

  function cntTPL(obj) {
    $('#highcharts-section').highcharts(obj);
  }
})();