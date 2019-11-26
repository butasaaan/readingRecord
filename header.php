  </head>
  <body>
    <header>
      <nav>
        <ul>
          <?php if((basename($_SERVER['PHP_SELF']) == 'login.php') ||
                (basename($_SERVER['PHP_SELF']) == 'signup.php') ||
                (basename($_SERVER['PHP_SELF']) == 'index.php')){?>
            <li><a href="index.php"><span></span><i class="fas fa-book"></i> TOP</a></li>
            <li><a href="signup.php"><span></span><i class="fas fa-user-plus"></i> SIGNUP</a></li>
            <li><a href="login.php"><span></span><i class="fas fa-sign-in-alt"></i> LOGIN</a></li>
          <?php }else{ ?>
            <li><a href="mypage.php"><span></span><i class="fas fa-book"></i> MYPAGE</a></li>
            <li><a href="graph.php"><span></span><i class="fas fa-hourglass-half"></i> LOG</a></li>
            <li><a href="logout.php"><span></span><i class="fas fa-sign-out-alt"></i> LOGOUT</a></li>
            <li><a href="quit.php"><span></span><i class="fas fa-door-open"></i> QUIT</a></li>
            <?php } ?>

        </ul>
      </nav>
      <img src="img/line00.png" alt="line">
    </header>
