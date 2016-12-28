(function () {

  var classifyEl = $('.school-classify');
  var schooltype = classifyEl.attr('data-schooltype');
  var schoolname = classifyEl.attr('data-schoolname');

  var schoolYearEl = $('#schoolyear-dropdown');
  var schoolTermEl = $('#schoolterm-dropdown');
  var schoolGradeEl = $('#grade-dropdown');
  var schoolExamnameEl = $('#examname-dropdown');
  var schoolCourseEl = $('#course-dropdown');

  var userEl = $('.login-info');
  var username = userEl.attr('data-username'),
      schoolgroup = userEl.attr('data-schoolgroup'),
      role = parseInt(userEl.attr('data-role'));

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
      schoolExamnameEl.find('.dropdown-toggle').addClass('disabled');
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

          for (var i = 0; i < schoolterm.length; i++) {
            contList += '<li><a href="#">' + schoolterm[i] + '</a></li>';
          }

          schoolTermEl.find('.name').text('学期');
          schoolGradeEl.find('.name').text('年级');
          schoolExamnameEl.find('.name').text('考试名称');
          schoolCourseEl.find('.name').text('考试科目');
          schoolTermEl.children('.dropdown-menu').html(contList);
          schoolTermEl.find('.dropdown-toggle').removeClass('disabled');

          if(!schoolGradeEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolGradeEl.find('.dropdown-toggle').addClass('disabled')
          }
          if(!schoolExamnameEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolExamnameEl.find('.dropdown-toggle').addClass('disabled')
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
            if(currData == examInfo[i].schoolterm && schoolyear == examInfo[i].schoolyear) {
              grade.push(examInfo[i].grade);
            }
          }
          grade = $.unique(grade);

          for (var i = 0; i < grade.length; i++) {
            contList += '<li><a href="#">' + grade[i] + '</a></li>';
          }

          schoolGradeEl.find('.name').text('年级');
          schoolExamnameEl.find('.name').text('考试名称');
          schoolCourseEl.find('.name').text('考试科目');
          schoolGradeEl.children('.dropdown-menu').html(contList);

          schoolGradeEl.find('.dropdown-toggle').removeClass('disabled');

          if(!schoolExamnameEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolExamnameEl.find('.dropdown-toggle').addClass('disabled')
          }
          if(!schoolCourseEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolCourseEl.find('.dropdown-toggle').addClass('disabled')
          }
          if(!$('.btn-search').hasClass('disabled')) {
            $('.btn-search').addClass('disabled')
          }

          break;
        case 'grade':
          examname = [];
          var schoolyear = schoolYearEl.find('.name').text();
          var schoolterm = schoolTermEl.find('.name').text();
          var contList = '';
          for (var i = 0; i < examInfo.length; i++) {
            if(currData == examInfo[i].grade && schoolyear == examInfo[i].schoolyear && schoolterm == examInfo[i].schoolterm) {
              examname.push(examInfo[i].examname);
            }
          }
          examname = $.unique(examname);

          for (var i = 0; i < examname.length; i++) {
            contList += '<li><a href="#">' + examname[i] + '</a></li>';
          }

          schoolExamnameEl.find('.name').text('考试名称');
          schoolCourseEl.find('.name').text('考试科目');

          schoolExamnameEl.children('.dropdown-menu').html(contList);
          schoolExamnameEl.find('.dropdown-toggle').removeClass('disabled');

          if(!schoolCourseEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolCourseEl.find('.dropdown-toggle').addClass('disabled')
          }
          if(!$('.btn-search').hasClass('disabled')) {
            $('.btn-search').addClass('disabled')
          }
          
          break;
        case 'examname':
          var schoolyear = schoolYearEl.find('.name').text();
          var schoolterm = schoolTermEl.find('.name').text();
          var schoolgrade = schoolGradeEl.find('.name').text();

          var courselist = [];
          var contList = '';

          for (var i = 0; i < examInfo.length; i++) {
            if(currData == examInfo[i].examname && schoolyear == examInfo[i].schoolyear && schoolterm == examInfo[i].schoolterm && schoolgrade == examInfo[i].grade) {
              courselist = examInfo[i].courselist.split(',');
            }
          }

          if(role < 3) {
            for (var i = 0; i < courselist.length; i++) {
              contList += '<li><a href="#">' + courselist[i] + '</a></li>';
            }
          } else if(role == 3) {
            if(courseObj[courseEnglishName] == '数学' && (schoolgrade == '高二年级' || schoolgrade == '高三年级')) {
              contList += '<li><a href="#">' + courseObj[courseEnglishName] + '(文)</a></li>';
              contList += '<li><a href="#">' + courseObj[courseEnglishName] + '(理)</a></li>';
            } else {
              contList += '<li><a href="#">' + courseObj[courseEnglishName] + '</a></li>';
            }
          }

          schoolCourseEl.find('.name').text('考试科目');

          schoolCourseEl.children('.dropdown-menu').html(contList);
          schoolCourseEl.find('.dropdown-toggle').removeClass('disabled');

          if(!$('.btn-search').hasClass('disabled')) {
            $('.btn-search').addClass('disabled');
          }
          
          break;
        case 'course':
          $('.btn-search').removeClass('disabled');

          break;
      }

  });

  $('.btn-search').on('click', function(){

    if($('.btn-search').hasClass('disabled')) {
      return false;
    }

    $('.highcharts-section-load').show();
    $('#highcharts-section').hide();

    $('.btn-search').addClass('disabled');

    var schoolyear = schoolYearEl.find('.name').text();
    var schoolterm = schoolTermEl.find('.name').text();
    var schoolgrade = schoolGradeEl.find('.name').text();
    var schoolexamname = schoolExamnameEl.find('.name').text();
    var course = schoolCourseEl.find('.name').text();

    var examFullname = schoolyear + '学年' + schoolterm + schoolgrade + schoolexamname;

    $.get("/home/Queryexam/ajax_get_scattervalue", {fullname: examFullname, course: course}, function(data){
      if(data) {
        var scattervalue = $.parseJSON(data);

        // console.log(scattervalue);

        valueaddedObj = {
          chart: {
            type: 'scatter',
            zoomType: 'xy'
          },
          title: {
              text: '散点图分布明细'
          },
          xAxis: {
              title: {
                  enabled: true,
                  text: '分数'
              },
              startOnTick: true,
              endOnTick: true,
              showLastLabel: true
          },
          yAxis: {
              title: {
                  text: '散点值'
              }
          },
          legend: {
              layout: 'vertical',
              align: 'left',
              verticalAlign: 'top',
              x: 100,
              y: 0,
              floating: true,
              backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF',
              borderWidth: 1
          },
          plotOptions: {
              scatter: {
                  marker: {
                      radius: 5,
                      states: {
                          hover: {
                              enabled: true,
                              lineColor: 'rgb(100,100,100)'
                          }
                      }
                  },
                  states: {
                      hover: {
                          marker: {
                              enabled: false
                          }
                      }
                  },
                  tooltip: {
                      headerFormat: '<b>{series.name}</b><br>',
                      pointFormat: '{point.x}, {point.y}'
                  }
              }
          },
          series: [{
              name: course,
              color: 'rgba(223, 83, 83, .5)',
              data: scattervalue['scatterValue']
          }]
        }
          
        $('.highcharts-section-load').hide();
        $('#highcharts-section').show();
        $('.btn-search').removeClass('disabled');

        cntTPL(valueaddedObj);
      }
    });

  });

  function cntTPL(obj) {

    $('#highcharts-section').highcharts(obj);
  }
})();