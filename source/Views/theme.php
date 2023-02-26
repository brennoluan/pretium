<!doctype html>
<html lang="pt-br">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="<?= asset("css/bootstrap.min.css"); ?>" rel="stylesheet">
    <link href="<?= asset("css/style.css"); ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <?php
    if ($v->section('style')):
        echo $v->section("style");
    endif;
    ?>
    <title><?= $title; ?></title>
</head>
<body>

<header class="navbar navbar-dark bg-info sticky-top flex-md-nowrap shadow">
    <a href="<?= url("app"); ?>" class="navbar-brand col-md-3 col-lg-2 me-0 px-3"><?= SITENAME; ?></a>

    <div class="dropdown me-5 ">
        <?php
        $countNotifications = (new \Source\Models\Notification())->find("user_id=:user_id AND read_message=0",
            "user_id={$_SESSION['user']}")->count();
        ?>
        <a class="text-white" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown"
           aria-expanded="false">
            <?php if ($countNotifications): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= ($countNotifications) > 8 ? '9+' : $countNotifications; ?></span>
            <?php endif; ?>
            <i class="fas fa-bell fs-5 notification"></i>
        </a>

        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
            <li class="d-flex justify-content-between">
                <h6 class="dropdown-header">Notificações
                    (<?= $countNotifications; ?>)</h6>

                <h6 class="dropdown-header link-primary">Marcar todas como lida</h6></li>
            <?php if ($countNotifications): ?>
                <li>
                    <hr class="dropdown-divider">
                </li>
            <?php endif; ?>
            <div id="notification-content">

            </div>
        </ul>
    </div>

    <button class="navbar-toggler position-absolute d-md-none collapsed " type="button" data-bs-toggle="collapse"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
</header>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?= $router->route("app.home"); ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $router->route("app.providers"); ?>">
                            <i class="fas fa-user-friends"></i>
                            Fornecedores
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $router->route("app.products"); ?>">
                            <i class="fas fa-shopping-cart"></i>
                            Produtos
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Cotações</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $router->route("app.myquotes"); ?>">
                            <i class="fas fa-layer-group"></i>
                            Minhas cotações
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $router->route("app.quotesreceived"); ?>">
                            <i class="fas fa-layer-group"></i>
                            Cotações recebidas
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Relátorios</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $router->route("app.report.price"); ?>">
                            <i class="fas fa-funnel-dollar"></i>
                            Preços
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Outros</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $router->route("app.profile.edit"); ?>">
                            <i class="fas fa-user"></i>
                            Editar perfil
                        </a>
                    </li>
                </ul>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $router->route("app.logout"); ?>">
                            <i class="fas fa-sign-out-alt"></i>
                            Sair
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4">
            <?= $v->section('content'); ?>
        </main>
    </div>
</div>


<!-- Modals -->
<?php
if ($v->section('modal')):
    echo $v->section("modal");
endif;
?>

<!-- Scripts -->
<script src="<?= asset("js/jquery-3.6.0.min.js"); ?>"></script>
<script src="<?= asset("js/bootstrap.min.js"); ?>"></script>

<?php
if ($v->section('script')):
    echo $v->section("script");
endif;
?>

<script>
    $(document).ready(function () {
        var notifications = 0;

        setInterval(function() {
            $.ajax({
                type: "POST",
                url: "<?= url('source/Ajax/fetchNotificationsCount.php'); ?>",
                data: {user_id: <?= $_SESSION['user']; ?>},
                dataType: "json",
                success: function (data) {
                    if(data.count > notifications){
                        notifications = data.count
                        $('.badge').text((notifications > 8 ? '9+' : notifications))
                    }
                }
            });
        }, 60000);

        $(".notification").click(function () {

            $.ajax({
                type: "POST",
                url: "<?= url('source/Ajax/fetchNotifications.php'); ?>",
                data: {user_id: <?= $_SESSION['user']; ?>},
                dataType: "json",
                success: function (data) {

                    $('ul.dropdown-menu #notification-content').empty()
                    $('.badge').text((data.length > 8 ? '9+' : data.length))

                    $.each(data, function (_, item) {
                        $('ul.dropdown-menu #notification-content').append('<li><a class="dropdown-item" href=""> ' + item.message + '</a></li>')
                        $('ul.dropdown-menu #notification-content').append('<li><hr class="dropdown-divider"></li>')
                    });
                }
            });
        });
    });
</script>
</body>
</html>