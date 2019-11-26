<?php
  require('function.php');
  require('auth.php');
  debug(print_r($_SESSION,true));
  if(!empty($_POST['reset'])){


      try {
        $dbh = dbConnect();
        // 総ページを０に書き換える
        $sql1 = 'UPDATE rr_users SET ttl_page = 0 WHERE id = :u_id';
        // ページデータをすべて消去する
        $sql2 = 'DELETE FROM pages WHERE user_id = :u_id';
        $data = array(':u_id' => $_SESSION['user_id']);
        $stmt1 = queryPost($dbh,$sql1,$data);
        $stmt2 = queryPost($dbh,$sql2,$data);
        if($stmt1 && $stmt2){
          debug('データのリセットが完了しました。');

        }

      } catch (Exception $e) {
        error_log('エラー発生:'. $e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }


?>
<?php
  $page_title = 'リセット';
  require('head.php');
  require('header.php');
?>
    <main>
      <h1>データのリセット</h1>
      <form id="reset" action="" method="post">
        <?php if($gestLoginFlg){ ?>
          <p>ゲストログイン状態ではデータのリセットはできません。</p>
          <div class="button button-margin">
            <a href="mypage.php">マイページへ</a>
          </div>
        <?php }elseif(!$gestLoginFlg && empty($_POST)){ ?>
          <!-- 確認画面 -->
          <p>データのリセットを行うと、今までに登録された<br>すべての読書情報が消去され、元には戻せません。<br>本当にリセットしますか？</p>
          <div class="button">
            <input type="submit" name="reset" value="リセットする">
          </div>
        <?php }elseif(!empty($_POST['reset']) && empty($err_msg)){ ?>
          <p>データのリセットが完了しました。</p>
          <div class="button button-margin">
            <a href="mypage.php">マイページへ</a>
          </div>

        <?php } ?>
      </form>
    </main>
<?php
require('footer.php');
?>
