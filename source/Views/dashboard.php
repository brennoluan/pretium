<?php $v->layout("theme"); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-6 col-md-3 mb-4">
        <div class="card text-white h-100 bg-success">
            <div class="card-header"><h6 class="m-0">Cotações finalizadas</h6></div>
            <div class="card-body">
                <h5 class="card-title"><?= $quotesFinish; ?></h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-4">
        <div class="card text-white h-100 bg-warning">
            <div class="card-header"><h6 class="m-0">Cotações em aberto</h6></div>
            <div class="card-body">
                <h5 class="card-title"><?= $quotesOpened; ?></h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-4">
        <div class="card text-white h-100 bg-danger">
            <div class="card-header"><h6 class="m-0">Cotações recusadas</h6></div>
            <div class="card-body">
                <h5 class="card-title"><?= $quotesRecused; ?></h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-4">
        <div class="card text-white h-100 bg-secondary">
            <div class="card-header"><h6 class="m-0">Cotações aguardando aprovação</h6></div>
            <div class="card-body">
                <h5 class="card-title"><?= $quotesWaintingApproval; ?></h5>
            </div>
        </div>
    </div>
</div>
<h2>Olá, <?= $user->username; ?></h2>

<h3 class="mt-4">Informações sobre sua empresa:</h3>
<h6><?= $user->provider()->social_reason; ?></h6>
<h6>CNPJ: <?= mask("cnpj", $user->provider()->cnpj); ?></h6>
<h6>Endereço: <?= $user->provider()->address; ?></h6>
<h6>CEP: <?= $user->provider()->cep; ?></h6>
<h3>Contatos:</h3>
<h6>Email: <?= $user->provider()->email; ?></h6>
<h6>Telefone: <?= mask("telephone", $user->provider()->phonenumber); ?></h6>