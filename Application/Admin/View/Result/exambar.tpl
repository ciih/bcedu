
<div id="chart-section" style="width:800px;height:1400px;"></div>

<script src="http://echarts.baidu.com/build/dist/echarts.js"></script>
<script type="text/javascript">
    // 路径配置
    require.config({
        paths: {
            echarts: 'http://echarts.baidu.com/build/dist'
        }
    });

    // 使用
    require(
        [
            'echarts',
            'echarts/chart/bar' // 使用柱状图就加载bar模块，按需加载
        ],
        function (ec) {
            // 基于准备好的dom，初始化echarts图表
            var myChart = ec.init(document.getElementById('chart-section'));
            var examNameArr = [],
                examCont1Arr = [],
                examCont2Arr = [],
                examCont3Arr = [],
                examCont4Arr = [];
            <foreach name="data" item="vo" key="k">
                examNameArr.push('<%$k%>');
                examCont1Arr.push('<%$vo["totalRate"]%>');
                examCont2Arr.push('<%$vo["excellentRate"]%>');
                examCont3Arr.push('<%$vo["passRate"]%>');
                examCont4Arr.push('<%$vo["failRate"]%>');
            </foreach>
            
            var option = {
                animation : false,
                title: {
                    show: false,
                    text: '<%$title%>',
                    x: 'center'
                },
                legend: {
                    data: ['全体','优秀','及格','未及格']
                },
                grid: {
                    top: '10%',
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'value',
                    boundaryGap: [0, 0.01]
                },
                yAxis: {
                    type: 'category',
                    data: examNameArr
                },
                series: [
                    {
                        name: '全体',
                        type: 'bar',
                        data: examCont1Arr
                    },
                    {
                        name: '优秀',
                        type: 'bar',
                        data: examCont2Arr
                    },
                    {
                        name: '及格',
                        type: 'bar',
                        data: examCont3Arr
                    },
                    {
                        name: '未及格',
                        type: 'bar',
                        data: examCont4Arr
                    }
                ]
            };
    
            // 为echarts对象加载数据 
            myChart.setOption(option); 
        }
    );      
</script>
