<?php $v->layout("theme"); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar perfil</h1>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-6">
                <form id="editProfile" action="<?= $router->route("app.profile.edit"); ?>" method="POST">
                    <h1 class="h6 text-muted mb-3">Seus dados</h1>
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" placeholder="Nome" name="username" value="<?= $user->username; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-mail pessoal</label>
                        <input type="email" class="form-control" placeholder="E-mail pessoal" name="emailpessoal" value="<?= $user->email; ?>" disabled>
                    </div>
                    <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Alterar senha</a>
                    <h1 class="h6 text-muted mb-3">Dados da empresa</h1>
                    <div class="mb-3">
                        <label class="form-label">Razão Social</label>
                        <input type="text" class="form-control" placeholder="Razão Social" name="razaosocial" value="<?= $provider->social_reason; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CNPJ</label>
                        <input type="text" class="form-control" placeholder="CNPJ" id="cnpj" name="cnpj" value="<?= $provider->cnpj; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-mail corporativo</label>
                        <input type="email" class="form-control" placeholder="E-mail corporativo" name="emailcorporativo" value="<?= $provider->email; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" class="form-control" placeholder="Telefone" id="phonenumber" name="phonenumber" value="<?= $provider->phonenumber; ?>" required>
                    </div>
                    <button class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $v->start('modal'); ?>
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Alterar senha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changePassword" action="<?= $router->route("app.changepassword"); ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Senha atual</label>
                        <input type="text" class="form-control" placeholder="Senha atual" name="senhaatual" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nova senha</label>
                        <input type="text" class="form-control" placeholder="Nova senha" name="novasenha" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar nova senha</label>
                        <input type="text" class="form-control" placeholder="Confirmar nova senha" name="confirmanovasenha" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button class="btn btn-primary">Alterar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $v->stop(); ?>

<?php $v->start('script'); ?>
<script src="<?= asset("js/jquery-3.6.0.min.js"); ?>"></script>
<script src="<?= url("vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"); ?>"></script>
<script src="<?= asset("js/sweetalert2.all.min.js"); ?>"></script>

<script>
    $(document).ready(function(){
        $('#phonenumber').mask('(00) 00000-0000');
        $('#cnpj').mask('00.000.000/0000-00', {reverse: true});

        $("form#changePassword").submit(function (e) {

            e.preventDefault();

            var form = $(this);
            var action = form.attr("action");
            var data = form.serialize();

            $.ajax({
                url: action,
                data: data,
                type: "post",
                dataType: "json",
                success: function (data) {
                    Swal.fire({
                        icon: data.icon,
                        title: data.title,
                    }).then(() => {
                        $("#changePasswordModal").modal('hide');
                    })
                }
            });
        });

        $("form#editProfile").submit(function (e) {

            e.preventDefault();

            if($("#cnpj").val().length < 18){

                Swal.fire({
                    icon: 'error',
                    title: 'CNPJ inválido',
                })
                return;
            }

            var form = $(this);
            var action = form.attr("action");
            var data = form.serialize();

            $.ajax({
                url: action,
                data: data,
                type: "post",
                success: function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Perfil editado com sucesso',
                    }).then(() => {
                        location.reload()
                    })
                }
            });
        });
    });
</script>
<?php $v->stop(); ?>

