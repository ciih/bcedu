
<div id="chart-section" style="width:600px;height:400px;"></div>

<script src="__ROOT__/Public/static/js/lib/echarts/echarts.baidu.min.js"></script>
<script type="text/javascript">
    // 路径配置
    require.config({
        paths: {
            echarts: '__ROOT__/Public/static/js/lib/echarts'
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
            var choiceNameArr = [],
                choiceCont1Arr = [],
                choiceCont2Arr = [];
            <foreach name="data" item="vo" key="k">
                choiceNameArr.push('单选<%$k%>');
                choiceCont1Arr.push('<%$vo[0]%>');
                choiceCont2Arr.push('<%$vo[1]%>');
            </foreach>
            
            var option = {
                animation : false,
                title: {
                    show: false,
                    text: '<%$title%>',
                    x: 'center'
                },
                legend: {
                    data: ['难度','区分度']
                },
                grid: {
                    top: '10%',
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: choiceNameArr
                },
                yAxis: {
                    type: 'value',
                    boundaryGap: [0, 0.01]
                },
                series: [
                    {
                        name: '难度',
                        type: 'bar',
                        label: {
                            normal: {
                                show: true,
                                position: 'outside'
                            }
                        },
                        data: choiceCont1Arr
                    },
                    {
                        name: '区分度',
                        type: 'bar',
                        label: {
                            normal: {
                                show: true,
                                position: 'outside'
                            }
                        },
                        data: choiceCont2Arr
                    }
                ]
            };
    
            // 为echarts对象加载数据 
            myChart.setOption(option); 
        }
    );      
</script>
