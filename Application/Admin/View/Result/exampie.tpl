
<div id="chart-section" style="width:600px;height:400px;"></div>

<!-- <script src="http://echarts.baidu.com/build/dist/echarts.js"></script> -->
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
            'echarts/chart/pie' // 使用柱状图就加载bar模块，按需加载
        ],
        function (ec) {
            // 基于准备好的dom，初始化echarts图表
            var myChart = ec.init(document.getElementById('chart-section'));
            var examScopeNameArr = [],
                examScopeContArr = [];
            <foreach name="data" item="vo" key="k">
                examScopeNameArr.push('<%$k%>');
                examScopeContArr.push({value:'<%$vo%>', name: '<%$k%>(<%$vo%>)'});
            </foreach>
            
            var option = {
                animation : false,
                title : {
                    text: '<%$title%>',
                    x: 'center'
                },
                legend: {
                    show: false,
                    orient: 'vertical',
                    right: '0',
                    data: examScopeNameArr
                },
                series : [
                    {
                        type: 'pie',
                        radius : '55%',
                        center: ['50%', '50%'],
                        label: {
                            normal: {
                                position: 'inner'
                            }
                        },
                        data: examScopeContArr
                    }
                ]
            };
    
            // 为echarts对象加载数据 
            myChart.setOption(option); 
        }
    );      
</script>
