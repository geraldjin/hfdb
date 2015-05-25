<?php
class Echarts{
 
     public static function show($id, array $data){
    
       $xaxis = "";
	   $tooltrigger = "";
       $series = "";
	   $grid="";
             
        if (empty($data)) {           
            $data = array(
                'legend' => array(
                    'data' => array('-')
                ),
                'xaxis' => array(
                    'type' => 'category',
                    'boundaryGap' => 'false',
                    'data' => array('')
                ),
                'series' => array(
                    array(
                        'name' => '-',
                        'type' => 'line',
                        'itemStyle' => "{normal: {areaStyle: {type: 'default'}}}",
                        'data' => array()
                    ),
                )
            );
        }
 
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'legend':
                    foreach ($value as $k => $v) {
						$legend[] =  json_encode($v);
                        /*switch ($k) {
                            case 'data':
                                $legend[] =  json_encode($v);
                                break;
                        }*/
                    }
					$legend = 'data:[' . implode(', ', $legend) . ']';
                    break;
                     
                case 'xaxis':
                    foreach ($value as $k => $v) {
                        switch ($k) {
                            case 'type':
                                $xaxis[] = $k . ":'" . $v . "'";
                                break;
                            case 'boundaryGap':
                                $xaxis[] = $k . ':' . $v;
                                break;
                            case 'data':
                                $xaxis[] = $k . ':' . json_encode($v);
                                break;
                        }
                    }
                    $xaxis = '{' . implode(', ', $xaxis) . '}';
                    break;
                     
                case 'series':
                    foreach ($value as $list) {
                        $tmp = array();
                        foreach ($list as $k => $v) {
                            switch ($k) {
                                case 'name':
                                case 'type':
                                    $tmp[] = $k . ":'" . $v . "'";
									if($v!="pie")
									{
										$tooltrigger="'axis'";
									}
									else
									{
										$tooltrigger="'item'";
										$grid="borderWidth:0";
									}
                                    break;
                                case 'itemStyle':
                                    $tmp[] = $k . ':' . $v;
                                    break;
                                case 'data':
                                    $tmp[] = $k . ':' . json_encode($v);
                            }
                        }
                        $series[] = '{' . implode(', ', $tmp) . '}';
                    }
                    $series = implode(', ', $series);
                    break;
            }
        }
 
        $script = <<<eof
            <script type='text/javascript'>
            
            require.config({
                paths:{
                    echarts: './js'
                }
            });
  
            require(
                [
                    'echarts',
                    'echarts/chart/bar',
                    'echarts/chart/line',
                  	'echarts/chart/pie'
                ],
                function(ec) {
                    var myChart = ec.init(document.getElementById('$id'));
                    var option = {
                        title : {
                            text: '',
                            subtext: ''
                        },
                        tooltip : {
                            trigger: $tooltrigger
                        },
                        legend: {
							show:true,
                            $legend
                        },
                        toolbox: {
                            show : true,
                            feature : {
                                mark : {show:true},
                                dataView : {show:true,readOnly: false},
                                magicType:{show:true,type:['line', 'bar']},
                                restore : {show:true}
                            }
                        },
						grid:{
							$grid
						},
                        calculable : false,
                        xAxis : [$xaxis],
                        yAxis : [{type : 'value'}],
                        series : [$series]
                    };
 
                    myChart.setOption(option);
                }
            );
            </script>
eof;
        echo $script;
    }
}
?>

