<?php $columnNumber = 3; ?>
</head>

<body>
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="/"><img src="/images/logo.png" style="width: 50px;" /> </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="/">Эхлэл</a>
                        </li>
                        <?php if ($_SESSION['user_role'] == "1") { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/menu/list">Цэс</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/category/list">Ангилал</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/page/list">Хуудас</a>
                            </li>
                        <?php } ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Мэдээ
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="/posts/list">Мэдээллүүд</a>
                                </li>
                                <?php if ($_SESSION['user_role'] == "1") { ?>
                                    <li>
                                        <a class="dropdown-item" href="/posts/linkList">Хамтрагч байгууллага/Холбоос</a>
                                    </li>
                                <?php } ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/posts/addPost">Мэдээлэл нэмэх</a>
                                </li>
                            </ul>
                        </li>
                        <?php if ($_SESSION['user_role'] == "1") { ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Тохиргоо
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/settings/main">Сайтын үндсэн мэдээлэл</a></li>
                                    <li><a class="dropdown-item" href="/settings/lang">Орчуулга</a></li>
                                    <li><a class="dropdown-item" href="/user/list">Хэрэглэгчдийн бүртгэл</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/feedback">Санал хүсэлт</a>
                            </li>
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link" href="https://www.youtube.com/playlist?list=PLmsdsy0TbTSEYqqSkNYjv9mHbzmW07i5u" target="_blank">Заавар</a>
                        </li>
                    </ul>
                    <div class="d-flex" role="search">
                        <a class="nav-link mx-2" href="/user/config" role="button">
                            <?= $_SESSION['user_name'] ?>
                        </a>
                        <a class="nav-link" href="/sign-out" role="button">
                            Гарах
                        </a>
                    </div>
                </div>
            </div>
        </nav>