<!doctype html>
<html lang="pt-br">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="<?= asset("css/bootstrap.min.css"); ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <title><?= $title; ?></title>

</head>
<body style="background-image: url(https://onestopsolucoes.com.br/email/images/bg-texture.png); background-repeat: repeat; background-color: #3e5ca2;">
<div class="container">
    <div class="row justify-content-center align-items-center vh-100" >
        <div class="col-12 col-md-5 h-50 p-4 bg-white shadow" style="border-radius: 30px">
            <a href="<?= $router->route("web.login"); ?>" title="Voltar" class="link-secondary"><i
                        class="fas fa-arrow-left" style="font-size: 1.3em;"></i></a>
            <h1 class="d-flex justify-content-center pb-3"><?= SITENAME; ?></h1>
            <div class="d-flex justify-content-center form_callback">
                <?= flash(); ?>
            </div>
            <form class="mt-3" method="post">
                <div class="input-group mb-3">
                    <span class="input-group-text text-secondary"><i class="fas fa-envelope"></i></i></span>
                    <input type="email" class="form-control" name="email" placeholder="Digite seu email" required>
                </div>
                <button class="btn btn-primary w-100" style="border-radius: 30px">Recuperar Senha</button>
            </form>
        </div>
    </div>
</div>
<!-- Optional JavaScript; choose one of the two! -->

<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="<?= asset("js/bootstrap.bundle.min.js"); ?>"></script>
<script src="<?= asset("js/jquery-3.6.0.min.js"); ?>"></script>
<script src="<?= asset("js/form.js"); ?>"></script>

<!-- Option 2: Separate Popper and Bootstrap JS -->
<!--
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
-->
</body>
</html>