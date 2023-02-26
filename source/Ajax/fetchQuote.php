<?php

require __DIR__ . "/../../vendor/autoload.php";

use Source\Models\Quotes;

if (isset($_POST['id']) && isset($_POST['userid'])) {

    $quote = (new Quotes())->findById($_POST['id']);
    $user = (new \Source\Models\User())->findById($quote->user_id);
    $userView = (new \Source\Models\User())->findById($_POST['userid']);
    $provider = (new \Source\Models\Provider())->findById($quote->provider_id);
    $status = (new \Source\Models\Status())->findById($quote->status_id);
}
?>

<div class="row mb-3">
    <div class="col-6">
        <label for="">Data de abertura</label>
        <input id='date' type="date" class="form-control" value="<?= substr($quote->created_at, 0, 10); ?>" data-quoteid="<?= $_POST['id']; ?>" disabled>
    </div>
    <div class="col-6">
        <label for="">Data de fechamento</label>
        <input type="date" class="form-control"
               value="<?= substr($quote->closure_at, 0, 10); ?>" disabled>
    </div>
</div>

<label for="">Fornecedor</label>
<input type="text" class="form-control"
       value="<?= $provider->social_reason; ?>" disabled>

<p data-created="<?= $quote->user_id; ?>" class="my-2">Status: <span class="text-info" data-status="<?= $status->name; ?>"><?= $status->name; ?></span></p>

<div class="row mt-3">
    <div class="table-responsive">
        <table id="tableProductsQuote" class="table table-sm table-striped table-bordered table-light">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Descrição</th>
                <th scope="col">Marca</th>
                <th scope="col">Qtd</th>
                <?php
                    if($quote->status()->name == "Solicitação Aceita" || $quote->status()->name == "Aguardando aprovação" || $quote->status()->name == "Recusada" || $quote->status()->name == "Aprovada"){
                        echo '<th scope="col">Preço unitário</th>';
                        echo '<th scope="col">Subtotal</th>';
                    }
                ?>
            </thead>
            <tbody>
            <?php
            $products = (new \Source\Models\QuoteProduct())->find("quote_id={$quote->id}")->fetch(true);
            foreach ($products as $product): ?>
                <tr>
                    <th scope='row'>#</th>
                    <td class="w-50"><?= $product->product()->product_name; ?></td>
                    <td><?= $product->product()->brand; ?></td>
                    <td class="qtd"><?= $product->qtd; ?></td>
                    <?php
                    if($quote->status()->name == "Solicitação Aceita" || $quote->status()->name == "Aguardando aprovação" || $quote->status()->name == "Recusada" || $quote->status()->name == "Aprovada"){

                        echo '<td class=""><div class="input-group input-group-sm"><span class="input-group-text">R$</span><input ' . ($userView->provider_id != $quote->provider_id || $quote->status()->name == "Aguardando aprovação" || $quote->status()->name == "Recusada" || $quote->status()->name == "Aprovada" ? 'disabled' : '') . ' style="width: 0px" type="number" value="'. $product->price . '" class="form-control form-control-sm price" placeholder="0" min="0" data-productid="' . $product->product()->id . '"/></div></td>';
                        echo '<td class="subtotal_">R$<span class="subtotal">'. number_format($product->subtotal,2,",",".") . '</span></td>';
                    }
                    ?>
                </tr>
            <?php endforeach; ?>
            <?php if($products > 1 && $quote->status()->name == "Solicitação Aceita" || $quote->status()->name == "Aguardando aprovação" || $quote->status()->name == "Recusada" || $quote->status()->name == "Aprovada"){
                echo '<tr class="table-light"><td colspan="6" class="text-center fs-5">Valor total: R$<span class="total fw-bold">0</span></td></tr>';
            }?>
            </tbody>
        </table>
    </div>
</div>