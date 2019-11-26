<?php
$page_title = 'ページで読書記録！';
require('head.php'); ?>
<?php require('header.php'); ?>
    <main class="widthsplead">
      <h2>毎日の読書記録をページ数だけで記録しましょう！</h2>
      <p class="top-sentence">
        これは面倒なタイトルや著者名登録などは省き、
        <br>単純に読んだページ数だけで読書を記録するアプリです。
      </p>
      <p>
        読んだページ数を蓄積してレベルを上げると、少しずつ木が成長していきます。
      </p>
      <p><img src="img/Lv.png" alt="level-img" class="top-img"></p>
      <p>
        グラフ表示で日々の読書量が一目瞭然！
      </p>
      <p><img src="img/graph.png" alt="graph-img" class="top-img"> </p>

      <div class="button">
        <ul class="top-ul">
          <li class="top-li"><a href="signup.php">新規登録</a></li>
          <li class="top-li"><a href="login.php">ログイン</a></li>
        </ul>
      </div>
    </main>
<?php require('footer.php'); ?>
