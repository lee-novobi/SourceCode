<?php
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_line.php';
require_once 'jpgraph/jpgraph_date.php';
require_once 'jpgraph/jpgraph_utils.inc.php';
require_once 'jpgraph/jpgraph_text.inc.php';
function TimeCallback($aVal) {
	if(Date('Hi', $aVal)=='0000')
    	return date('m.d H:i', $aVal);
	// return Date('m.d H:i', $aVal);
	return Date('H:i', $aVal);
}

function BindGraphPercentUnit($aVal) {
	return round($aVal,3).'%';
}
class Jpgraph{
	function __construct() {
	}

	function Draw1Line($arrPoints, $unit, $title, $step=1){
		$graph = new Graph(PERFORMANCE_IMG_WIDTH, PERFORMANCE_IMG_HEIGHT);
		$graph->SetUserFont('Silkscreen.ttf');
		$graph->SetClipping(true);
		$graph->SetMargin(68,20,10,85);
		$graph->title->Set($title);

		if(count($arrPoints) > 1){
			$data = array();
			$xdata = array();

			for( $i=0;$i < count($arrPoints);$i+=$step ) {
				if($i >= count($arrPoints)) $i = count($arrPoints)-1;
			    $data[]  = $arrPoints[$i]['value'];
			    $xdata[] = $arrPoints[$i]['clock'];
			}

			$adjstart = $arrPoints[0]['clock'];
			$adjend   = $arrPoints[count($arrPoints)-1]['clock'];

			// $start_lower = $adjstart - ($adjstart % 3600);
			// $start_upper = $start_lower + 10800;

			$end_lower = $adjend + (3600-($adjend % 3600));
			$end_upper = ((date('H', $end_lower)%2)==0) ? $end_lower : $end_lower + 3600;

			$graph->SetScale("datelin", 0, 0, $end_upper, $adjstart);

			$graph->xaxis->scale->SetTimeAlign(HOURADJ_1);
			$graph->xaxis->scale->ticks->Set(3600, 1800);
			$graph->xaxis->HideTicks(false,false);
			$graph->xaxis->SetTextLabelInterval(2);

			$graph->xaxis->SetLabelFormatCallback('TimeCallback');
			if($unit == 'B')  $graph->yaxis->SetLabelFormatCallback('BytesToSize');
			elseif($unit == 'KB') $graph->yaxis->SetLabelFormatCallback('KilobytesToSize');
			elseif($unit == '%') $graph->yaxis->SetLabelFormatCallback('BindGraphPercentUnit');
			$graph->xaxis->SetLabelAngle(90);

			$graph->xaxis->SetFont(FF_FONT1);
			$graph->yaxis->SetFont(FF_FONT1);

			$line = new LinePlot($data, $xdata);
			$graph->Add($line);
			$line->SetColor('#00e300');
			$line->SetWeight(1);
		} else {
			$adjstart = time();
			$adjend = $adjstart - SECONDS_1_DAY - SECONDS_3_HOUR;
			$adjend = $adjend + (3600-($adjend % 3600));
			$adjend = ((date('H', $adjend)%2)==0) ? $adjend : $adjend + 3600;
			
			$graph->SetScale("datelin", 0, 1, $adjend, $adjstart);
			$graph->xaxis->scale->SetTimeAlign(HOURADJ_1);
			$graph->xaxis->scale->ticks->Set(3600, 1800);
			$graph->xaxis->HideTicks(false,false);
			$graph->xaxis->SetTextLabelInterval(2);
			
			$graph->xaxis->SetLabelFormatCallback('TimeCallback');
			$graph->xaxis->SetLabelAngle(90);
			
			$line = new LinePlot(array(null, null), array($adjstart, $adjend));
			$graph->xaxis->SetFont(FF_FONT1);
			$graph->Add($line);
			
			$caption=new Text("NO DATA", 260, 104);
			$caption->SetFont(FF_USERFONT, FS_NORMAL, 16);
			$caption->SetColor('#777777');
			$graph->AddText($caption);
		}
		$graph->Stroke();
	}

	function Draw2Line($arrPoints1, $arrPoints2, $unit, $title='', $title_line1='', $title_line2='', $step=1){
		$graph = new Graph(PERFORMANCE_IMG_WIDTH, PERFORMANCE_IMG_HEIGHT);
		$graph->SetUserFont('Silkscreen.ttf');
		$graph->img->SetAntiAliasing(true);
		$graph->SetClipping(true);
		$graph->SetMargin(68,20,10,85);
		$graph->title->Set($title);


		if(count($arrPoints1) > 0 || count($arrPoints2) > 0){
			$data1 = array();
			$xdata1 = array();

			for( $i=0;$i < count($arrPoints1);$i+=$step ) {
				if($i >= count($arrPoints1)) $i = count($arrPoints1)-1;
			    $data1[]  = $arrPoints1[$i]['value'];
			    $xdata1[] = $arrPoints1[$i]['clock'];
			}

			$adjstart1 = $arrPoints1[0]['clock'];
			$adjend1   = $arrPoints1[count($arrPoints1)-1]['clock'];
			if($adjstart1 - $adjend1 < 7200) $adjend1 -= 7200;

			$end_lower1 = $adjend1 + (3600-($adjend1 % 3600));
			$end_upper1 = ((date('H', $end_lower1)%2)==0) ? $end_lower1 : $end_lower1 + 3600;
			//p('Start 1:' . date('Y-m-d H:i:s', $adjstart1));
			//p('End 1:' . date('Y-m-d H:i:s', $adjend1));
			//p('End upper 1:' . date('Y-m-d H:i:s', $end_upper1));
			
			// ---------------------------------------------------------------------------------- //
			$data2 = array();
			$xdata2 = array();

			for( $i=0;$i < count($arrPoints2);$i+=$step ) {
				if($i >= count($arrPoints2)) $i = count($arrPoints2)-1;
			    $data2[]  = $arrPoints2[$i]['value'];
			    $xdata2[] = $arrPoints2[$i]['clock'];
			}

			$adjstart2 = $arrPoints2[0]['clock'];
			$adjend2   = $arrPoints2[count($arrPoints2)-1]['clock'];
			if($adjstart2 - $adjend2 < 7200) $adjend2 -= 7200;

			$end_lower2 = $adjend2 + (3600-($adjend2 % 3600));
			$end_upper2 = ((date('H', $end_lower1)%2)==0) ? $end_lower2 : $end_lower2 + 3600;
			//p('Start 2:' . date('Y-m-d H:i:s', $adjstart2));
			//p('End 2:' . date('Y-m-d H:i:s', $adjend2));
			//p('End upper 2:' . date('Y-m-d H:i:s', $end_upper2));
			//pd($start);
			// ---------------------------------------------------------------------------------- //
			$upper = $end_upper1 < $end_upper2 ? $end_upper1 : $end_upper2;
			$start = $adjstart1 > $adjstart2 ? $adjstart1 : $adjstart2;
			// ---------------------------------------------------------------------------------- //

			$graph->SetScale("datelin", 0, 0, $upper, $start);

			$graph->xaxis->scale->SetTimeAlign(HOURADJ_1);
			$graph->xaxis->scale->ticks->Set(3600, 1800);
			$graph->xaxis->HideTicks(false,false);
			$graph->xaxis->SetTextLabelInterval(2);

			$graph->xaxis->SetLabelFormatCallback('TimeCallback');
			if($unit == 'B')  $graph->yaxis->SetLabelFormatCallback('BytesToSize');
			elseif($unit == 'KB') $graph->yaxis->SetLabelFormatCallback('KilobytesToSize');
			elseif($unit == '%') $graph->yaxis->SetLabelFormatCallback('BindGraphPercentUnit');
			elseif($unit == 'Bps') $graph->yaxis->SetLabelFormatCallback('BpsToSize');
			elseif($unit == 'KBps') $graph->yaxis->SetLabelFormatCallback('KBpsToSize');
			$graph->xaxis->SetLabelAngle(90);

			$graph->xaxis->SetFont(FF_FONT1);
			$graph->yaxis->SetFont(FF_FONT1);

			$line1 = new LinePlot($data1, $xdata1);
			$graph->Add($line1);
			$line1->SetColor('#00e300');
			$line1->SetWeight(1);
			$line1->SetLegend($title_line1);

			$line2 = new LinePlot($data2, $xdata2);
			$graph->Add($line2);
			$line2->SetColor('#eb3232');
			$line2->SetWeight(1);
			$line2->SetLegend($title_line2);

			$graph->legend->SetPos(0.1,0.99,'right','bottom');

		} else {
			$adjstart = time();
			$adjend = $adjstart - SECONDS_1_DAY - SECONDS_3_HOUR;
			$adjend = $adjend + (3600-($adjend % 3600));
			$adjend = ((date('H', $adjend)%2)==0) ? $adjend : $adjend + 3600;
			
			$graph->SetScale("datelin", 0, 1, $adjend, $adjstart);
			$graph->xaxis->scale->SetTimeAlign(HOURADJ_1);
			$graph->xaxis->scale->ticks->Set(3600, 1800);
			$graph->xaxis->HideTicks(false,false);
			$graph->xaxis->SetTextLabelInterval(2);
			
			$graph->xaxis->SetLabelFormatCallback('TimeCallback');
			$graph->xaxis->SetLabelAngle(90);
			
			$line = new LinePlot(array(null, null), array($adjstart, $adjend));
			$graph->xaxis->SetFont(FF_FONT1);
			$graph->Add($line);
			
			$caption=new Text("NO DATA", 260, 104);
			$caption->SetFont(FF_USERFONT, FS_NORMAL, 16);
			$caption->SetColor('#777777');
			$graph->AddText($caption);
		}
		$graph->Stroke();
	}
	
	function DrawAccessTracking($arrPoint, $title='ACCESS TRACKING STATISTIC'){
		require_once ('jpgraph/jpgraph_bar.php');
		$maxTick = 50;
		
		$datax = array_keys($arrPoint);
		$datax_count = count($datax);
		#pd($datax_count);
		$graph = new Graph(1200,260);
		$graph->SetUserFont('Silkscreen.ttf');
		$graph->img->SetAntiAliasing(true);
		$graph->SetClipping(true);
		$graph->SetMargin(68,20,10,85);
		$graph->title->Set($title);
		$graph->SetScale("textint");
		$graph->xaxis->SetTickLabels($datax);
		$graph->xaxis->SetLabelAngle(90);
		if($datax_count > $maxTick){
			$graph->xaxis->SetTextLabelInterval((int)(ceil($datax_count/$maxTick)));
			$graph->xaxis->HideTicks(true,true);
			$bplot = new LinePlot(array_values($arrPoint));
		} else {
			$graph->xaxis->HideTicks(false,false);
			$bplot = new BarPlot(array_values($arrPoint));
		}
		#pd($arrPoint);
		
		$graph->Add($bplot);
		$graph->Stroke();
	}
}
?>
