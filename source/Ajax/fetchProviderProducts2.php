<?php

require __DIR__ . "/../../vendor/autoload.php";

if (isset($_POST["id"])) {
    $provider = (new \Source\Models\Provider())->findById($_POST['id']);
    $products = (new \Source\Models\Products())->find("provider_id=:provider_id",
        "provider_id={$_POST['id']}")->fetch(true);
    ?>
    <p>Visualizando produtos do fornecedor: <strong><?= $provider->social_reason; ?></strong></p>
    <div class="row">
        <div class="table-responsive">
            <table id="tableProductsNewQuote" class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Marca</th>
                </thead>
                <tbody>
                <?php
                if ($products):
                    foreach ($products as $product): ?>
                        <tr>
                            <td>#</td>
                            <td><?= $product->product_name; ?></td>
                            <td><?= $product->brand; ?></td>
                        </tr>
                    <?php
                    endforeach;
                endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}