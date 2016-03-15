(function () {

  var classifyEl = $('.school-classify');
  var schooltype = classifyEl.attr('data-schooltype');
  var schoolname = classifyEl.attr('data-schoolname');

  var schoolYearEl = $('#schoolyear-dropdown');
  var schoolTermEl = $('#schoolterm-dropdown');
  var schoolGradeEl = $('#grade-dropdown');
  var schoolExamnameEl = $('#examname-dropdown');
  var schoolCoureseEl = $('#course-dropdown');

  var examInfo;

  var course,
      examFullname;

  var schoolyear = [],
      schoolterm = [],
      grade = [],
      examname = [],
      courselist = [];

  var schoollist;

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
      schoolExamnameEl.find('.dropdown-toggle').addClass('disabled');
      schoolCoureseEl.find('.dropdown-toggle').addClass('disabled');
    }
  });

  $.get("/home/Queryexam/ajax_get_school", {schooltype: schooltype}, function(data){
    if(data) {
      schoollist = $.parseJSON(data);
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
          schoolExamnameEl.find('.name').text('考试名称');
          schoolCoureseEl.find('.name').text('考试科目');
          schoolTermEl.children('.dropdown-menu').html(contList);
          schoolTermEl.find('.dropdown-toggle').removeClass('disabled');
          if(!schoolGradeEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolGradeEl.find('.dropdown-toggle').addClass('disabled')
          }
          if(!schoolExamnameEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolExamnameEl.find('.dropdown-toggle').addClass('disabled')
          }
          if(!schoolCoureseEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolCoureseEl.find('.dropdown-toggle').addClass('disabled')
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
          schoolExamnameEl.find('.name').text('考试名称');
          schoolCoureseEl.find('.name').text('考试科目');
          schoolGradeEl.children('.dropdown-menu').html(contList);
          schoolGradeEl.find('.dropdown-toggle').removeClass('disabled');
          if(!schoolExamnameEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolExamnameEl.find('.dropdown-toggle').addClass('disabled')
          }
          if(!schoolCoureseEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolCoureseEl.find('.dropdown-toggle').addClass('disabled')
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
          examname.push('全选');
          examname = $.unique(examname);

          for (var i = 0; i < examname.length; i++) {
            contList += '<li><a href="#">' + examname[i] + '</a></li>'
          }

          schoolExamnameEl.find('.name').text('考试名称');
          schoolCoureseEl.find('.name').text('考试科目');
          schoolExamnameEl.children('.dropdown-menu').html(contList);
          schoolExamnameEl.find('.dropdown-toggle').removeClass('disabled');

          if(!schoolCoureseEl.find('.dropdown-toggle').hasClass('disabled')) {
            schoolCoureseEl.find('.dropdown-toggle').addClass('disabled')
          }
          if(!$('.btn-search').hasClass('disabled')) {
            $('.btn-search').addClass('disabled')
          }
          
          break;

        case 'examname':
          var schoolyear = schoolYearEl.find('.name').text();
          var schoolterm = schoolTermEl.find('.name').text();
          var schoolgrade = schoolGradeEl.find('.name').text();

          var contList = '';

          for (var i = 0; i < examInfo.length; i++) {
            if(currData == examInfo[i].examname && schoolyear == examInfo[i].schoolyear && schoolterm == examInfo[i].schoolterm && schoolgrade == examInfo[i].grade) {
              courselist = examInfo[i].courselist.split(',');
            }
          }

          for (var i = 0; i < courselist.length; i++) {
            contList += '<li><a href="#">' + courselist[i] + '</a></li>'
          }

          schoolCoureseEl.find('.name').text('考试科目');
          schoolCoureseEl.children('.dropdown-menu').html(contList);
          schoolCoureseEl.find('.dropdown-toggle').removeClass('disabled');

          if(!$('.btn-search').hasClass('disabled')) {
            $('.btn-search').addClass('disabled')
          }
          
          break;

        case 'course':
          var schoolyear = schoolYearEl.find('.name').text();
          var schoolterm = schoolTermEl.find('.name').text();
          var schoolgrade = schoolGradeEl.find('.name').text();
          var schoolexamname = schoolExamnameEl.find('.name').text();
          var schoolcourse = schoolCoureseEl.find('.name').text();

          course = schoolcourse;

          for (var i = 0; i < examInfo.length; i++) {
            if(schoolyear == examInfo[i].schoolyear && schoolterm == examInfo[i].schoolterm && schoolgrade == examInfo[i].grade) {
              if(schoolexamname == examInfo[i].examname) {
                examFullname = examInfo[i].fullname;
              } else if (schoolexamname == '全选') {
                examFullname.push(examInfo[i].fullname);
              }
            }
          }
          $('.btn-search').removeClass('disabled');
          console.log(course);
          console.log(examFullname);
          break;
      }

  });

  $('.btn-search').on('click', function(){
    cntTPL(examName, courseList);
  });

  function cntTPL() {

    $('#highcharts-section').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Column chart with negative values'
        },
        xAxis: {
            categories: ['Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas']
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'John',
            data: [5, 3, 4, 7, 2]
        }, {
            name: 'Jane',
            data: [2, -2, -3, 2, 1]
        }, {
            name: 'Joe',
            data: [3, 4, 4, -2, 5]
        }]
    });
  }
})();