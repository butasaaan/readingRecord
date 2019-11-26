<?php
  require('function.php');
  require('auth.php');

  $currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
  $sort = (!empty($_GET['sort'])) ? $_GET['sort'] : 1;

  if(!is_int((int)$currentPageNum)){
    error_log('エラー発生:指定ページに不正な値が入りました');
    header("Location:pagelog.php");
  }

  $page_list = getPages($currentPageNum,$sort);
  $total_read = getTotalPage();
  if(!empty($sort)){
    $link = '&sort='.$sort;
  }else{
    $link = '';
  }
?>
<?php
  $page_title = 'LOG';
  require('head.php');
?>
<div class="slide-msg-area" id="js-show-msg" style="display:none;">
  <?php echo getSessionFlash(); ?>
</div>
<?php
  require('header.php');
?>
    <main>
        <ul class="pagelog-top-ul">
          <li><a href="graph.php">グラフ表示</a></li>
          <li><a href="pagelog.php">リスト表示</a></li>
        </ul>
        <p class="ttl-page">合計 <span><?php echo $total_read;?></span> ページ</p>
        <form class="sort" action="" method="get">
          <select class="" name="sort">
            <option value="1" <?php if($sort == 1) echo 'selected'; ?>>日付が新しい順</option>
            <option value="2" <?php if($sort == 2) echo 'selected'; ?>>日付が古い順</option>
            <option value="3" <?php if($sort == 3) echo 'selected'; ?>>ページが多い順</option>
            <option value="4" <?php if($sort == 4) echo 'selected'; ?>>ページが少ない順</option>
          </select>
          <div class="button">
            <input type="submit" value="並び替え">
          </div>
        </form>

        <table id="page-list">
          <thead>
            <tr>
              <th>日付</th><th>ページ数</th><th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($page_list['data'] as $key => $val) : ?>
              <tr>
                <td><?php echo sanitize($val['read_date']); ?></td>
                <td><?php echo sanitize($val['page']); ?></td>
                <td><a href="trush.php?p_id=<?php echo sanitize($val['id']); ?>"><i class="fas fa-trash-alt"></i> 削除</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php pagination($currentPageNum,$page_list['total_page'],$link); ?>
    </main>
<?php
  require('footer.php');
?>
