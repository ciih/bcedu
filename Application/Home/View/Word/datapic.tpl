<layout name="index_layout" />


<div id="main" style="width:600px;height:400px;">
  
</div>

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
            'echarts/chart/line' // 使用柱状图就加载bar模块，按需加载
        ],
        function (ec) {
            // 基于准备好的dom，初始化echarts图表
            var myChart = ec.init(document.getElementById('main')); 
            
            var option = {
                animation : false,
                title : {
                    text: '语文学科全区及不同水平组各知识范畴得分率比较折线图',
                    subtext: ''
                },
                tooltip : {
                    show: false
                },
                legend: {
                    data: <%$data2|array_keys|json_encode%>
                },
                toolbox: {
                    show : false
                },
                calculable : true,
                xAxis : [
                    {
                        type : 'category',
                        boundaryGap : false,
                        data : <%$xData%>
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],
                series : [
                    <foreach name="data2" item="vo" key="k">
                    {
                        name: '<%$k%>',
                        type: 'line',
                        data: <%$vo|array_values|json_encode%>,
                        markPoint : {
                            data : [
                                {type : 'max', name: '最大值'},
                                {type : 'min', name: '最小值'}
                            ]
                        }
                    },                     
                    </foreach>
                ]
            };
    
            // 为echarts对象加载数据 
            myChart.setOption(option); 
        }
    );      
</script>
