<?php
require('function.php');
require('auth.php');

$chipsmsg = (mt_rand(0,1)) ? 'どちらか片方だけに数字を入力して送信すると、その値が読んだページ数として登録されるよ！' : '同じ日付で複数回登録すると、既に登録されているページ数に加算されるよ！';
// 現在の総ページ数を取得
$total = getTotalPage();
$next = 0;
$score = 0;
$now_lv = 0;

levelCreate();
// debug(print_r($level,true));

// ステータス表示処理
    // ページ数のPOSTがある場合
    if(!empty($_POST)){
      $read_date = $_POST['read_date'];
      $start = (!empty($_POST['start-p'])) ? $_POST['start-p'] : 0;
      $end = (!empty($_POST['end-p'])) ? $_POST['end-p'] : 0;
      // バリデーション
      validDate($read_date,'date');
      validNum($start,'num');
      validNum($end,'num');
      validMaxNum($start,'num');
      validMaxNum($end,'num');

      if(empty($err_msg)){
        debug('バリデーションOK');
        if(empty($_POST['end-p']) && !empty($_POST['start-p'])){
          // 読み始めページにのみ入力がある時
          $score = $start;
        }else{
          // 読み終わりページのみ or 両方に入力がある時
          $score = $end - $start;
          if($score < 0){
            $err_msg['common'] = '読み終わりのページは読み始めのページよりも<br>大きな値を入れてください。';
            $score = 0;
          }
        }
        if($score !== 0){
          $total += $score;
          registPage($read_date,$score,$total);
        }
      }
    }
  // 現在のレベルを調べる
  for ($i=1; $i <53 ; $i++) {
    if($total > $level[$i]){
    // 今までの累計ページがレベル上限よりも下回ったところで処理を止め、レベルを返す。
    }else{
      $now_lv =  $i;
      break;
    }
  }

  // ステータス表示用関数
  $lv_img = lvimgCreate($now_lv);//イメージ表示用
  $prog = (($total - $level[$now_lv-1]) / ($level[$now_lv] - $level[$now_lv-1]))*100;//progressバー表示用
  $next = $level[$now_lv] - $total;//残りページ表示用

?>
<?php
  $page_title = 'MYPAGE';
  require('head.php');
?>
<div class="slide-msg-area" id="js-show-msg" style="display:none;">
  <?php echo getSessionFlash(); ?>
</div>
<?php
  require('header.php');
?>
    <main>
      <p>読んだページ数を登録してね。<br>ページが溜まるとどんどん木が成長していくよ。</p>
      <div class="status">
        <h2 class="level">レベル<?php if(!empty($now_lv)){echo $now_lv;}else{echo 1;} ?></h2>
        <!-- <i class="fas fa-book-reader fa-3x icon"></i> -->
        <img class="lv-img" src="<?php echo $lv_img; ?>" alt="tree">
        <p class="para"><?php echo $total; ?> / <?php if(!empty($now_lv)) {echo $level[$now_lv]+1;}else{echo 500;} ?></p>
        <progress class="js-progress" value="<?php if(!empty($prog)) echo $prog; ?>" max="100"></progress>
        <p class="next">次のレベルまで、あと <?php if(!empty($next)) {echo $next+1;} else {echo 500;} ?> ページ</p>
        <div class="button reset">
          <a href="reset.php">レベルをリセットする</a>
        </div>
      </div>


      <form id="mypage" action="" method="post">
        <div class="msg-area">
          <p><?php getErrMsg('common',false); ?></p>
        </div>
        <div class="chips">
          <h3>Chips</h3>
          <p><?php echo $chipsmsg; ?></p>
        </div>
        <label for="read-date">読んだ日<br></label>
        <label class="date">
          <input id="read-date" type="date" name="read_date" value="<?php if(!empty($_POST['read_date'])){ echo $_POST['read_date']; }else{echo date("Y-m-d");} ?>">
        </label>
        <p><?php getErrMsg('date',false); ?></p>
        <div class="page">
          <label>読み始めのページ<br>
            <input type="text" name="start-p" class="js-half" value="">
          </label>
          <span> ~ </span>
          <label>読み終りのページ<br>
            <input type="text" name="end-p" class="js-half" value="">
          </label>
        </div>
        <p><?php getErrMsg('num',false); ?></p>
        <div class="button">
          <input class="js-button" type="submit" name="" value="送信">
        </div>
      </form>
    </main>
<?php
  require('footer.php');
?>
