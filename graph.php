<?php
  require('function.php');
  require('auth.php');

  $thisMonth = date('Y-m-01');//selectbox計算用
  $month = (!empty($_GET['month'])) ? $_GET['month'] : date('Y-m');
  $start_date = date('Y-m-d',strtotime('first day of' . $month));
  $end_date =  date('Y-m-d',strtotime('last day of' . $month));
  $days =  (int)date('d',strtotime('last day of' . $month));
  $month_ttl = 0;

  $page_list = getListForGraph($start_date,$end_date);
  foreach ($page_list as $key => $val) {
    $month_ttl += $val['page'];
  }
  // グラフ表示用配列作成
  $for_display = array();
  // DBに登録されている日付と同じインデックスにデータを入れる(例：8/15日のデータは$for_display[15]に)
  for ($i=0; $i <count($page_list); $i++) {
    for ($j=1; $j <= $days ; $j++) {
      if(substr($page_list[$i]['read_date'], -2) == $j){
        $for_display[$j] = $page_list[$i];
      }
    }
  }
  // DBに登録されていない日の分には日付とページ数０を入れる
  for ($j=1; $j <= $days ; $j++) {
    if(empty($for_display[$j])){
      $for_display[$j] = array('read_date'=>$month.'-'.str_pad($j, 2, 0, STR_PAD_LEFT), 'page' => 0);
    }
  }
  // 日付順に並び替え
  array_multisort($for_display);
?>
<?php
  $page_title = 'GRAPH';
  require('head.php');
?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages:['corechart']});
      google.setOnLoadCallback(drawChart);

      // データ読み込み

      function drawChart() {
      var data = google.visualization.arrayToDataTable([
      ['日付', 'ページ数'],
      // ['8/01','10'],
      <?php
      foreach ($for_display as $key => $val) {
      echo '[\''.date("j",strtotime($val["read_date"])).'\', '.$val["page"].'],';
      }
      ?>
      ]);
      var options = {
        title: '<?php echo $month; ?>',
        titleTextStyle: {
          fontSize:30,
          bold: false,
          color: '#849CBE'
        },
        hAxis: {title: '日付',
          titleTextStyle:{fontSize:16,color: '#849CBE'},

          textStyle:{
            color: '#849CBE'
          },
          showTextEvery:1},
        fontSize: 10,
        vAxis:{gridlines:{count:6},
        baselineColor: '#849CBE',
        minValue: 100,
        textStyle:{
          color: '#849CBE'
        }},
        chartArea:{
          width: '80%',
          height: '80%'
        },
        // reverseAxis: true,
        // width:1000,
        // height:600,
        colors:['#849CBE'],
        color: '#849CBE',
        backgroundColor:'#F0F8FE',
        animation:{duration: 1000,
                   startup: true
                   },
        legend: {position:'none'},

      }
      var chart = new google.visualization.ColumnChart(document.getElementById('graph'));
      chart.draw(data, options);
      };

    </script>
    <?php require('header.php'); ?>
    <body>
      <main class="widthsplead">
        <ul class="pagelog-top-ul">
          <li><a href="graph.php">グラフ表示</a></li>
          <li><a href="pagelog.php">リスト表示</a></li>
        </ul>
        <p class="month-ttl">月間合計 <span><?php echo $month_ttl;?></span> ページ</p>
        <form class="sort" action="" method="get">
          <select class="" name="month">
            <option value="<?php echo date('Y-m'); ?>" ><?php echo date("Y-m"); ?></option>
            <?php for ($i=1; $i <= 12 ; $i++) {
              $selected = ($month == date('Y-m',strtotime('-'.$i.' month' . $thisMonth))) ? 'selected' : '';
              echo "<option value='".date('Y-m',strtotime('-'.$i.' month' . $thisMonth))."' ".$selected.">".date('Y-m',strtotime('-'.$i.' month' . $thisMonth))."</option>";

            } ?>
          </select>
          <div class="button">
            <input type="submit" value="表示">
          </div>
        </form>
        <div id="graph"></div>
        <div class="">

        </div>
      </main>
<?php require('footer.php'); ?>
