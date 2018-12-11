<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf">统计</div>
                </div>
                <div class="widget-body am-fr">                    
                    <div class="am-scrollable-horizontal am-u-sm-12" style="display:flex;">
                        <div id="main" style="width: 600px;height:400px;"></div>                        
                    </div>          
                    
                    <div class="am-scrollable-horizontal am-u-sm-12" style="display:flex;"> 
                        <?php foreach ($equip as $key => $v) : ?>
                        <div id="main<?= $key ?>" style="width: 600px;height:400px;"></div>
                        <?php endforeach; ?>
                    </div>


                    <!-- <div class="am-scrollable-horizontal am-u-sm-12" style="display:flex;">
                        <div id="container" style="width:800px;height:800px;"></div>                     
                    </div>           -->


                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/store/js/echarts1.min.js"></script>
<!-- <script src="assets/store/js/echarts-gl.min.js"></script> -->

<script>
            
    var myChart = echarts.init(document.getElementById('main'));    

    option = {
        title : {
            text: '设备总数',
            subtext: '一共<?= $pies['total_count'] ?>台',
            x:'center'
        },
        color:['#D53A35', '#6AB0B8','#9FDABF','#E98F6F'],
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: JSON.parse('<?= json_encode($pies['legend_data']) ?>')
        },
        series : [
            {
                name: '单位/台',
                type: 'pie',
                radius : '55%',
                center: ['50%', '60%'],
                data:JSON.parse('<?= json_encode($pies['data']) ?>'),
                itemStyle: {                          
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    };
    
    myChart.setOption(option);
     
    <?php foreach ($equip as $key => $v) : ?>
    var myChart_<?= $key ?>= echarts.init(document.getElementById('main<?= $key ?>'));     
    
    var option_<?= $key ?> = {
        title : {
            text: '<?= $v['text'] ?>',
            subtext: '一共<?= $v['total_count'] ?>台',
            x:'center'
        },
        color:['#3CB2EF','#FFAE8B', '#71F6F9','#F66BBF','#FFF065'],
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: JSON.parse('<?= json_encode($v['legend_data']) ?>')
        },
        series : [
            {
                name: '单位/台',
                type: 'pie',
                radius : '55%',
                center: ['50%', '60%'],
                data:JSON.parse('<?= json_encode($v['data']) ?>'),
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    };  
    myChart_<?= $key ?>.setOption(option_<?= $key ?>);
    <?php endforeach; ?>


    // 
    


















</script>

