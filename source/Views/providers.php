<?php
$v->layout("theme");

use Source\Models\Provider;
use CoffeeCode\Paginator\Paginator;

$page = filter_input(INPUT_GET, "page", FILTER_SANITIZE_STRIPPED);
$providers = new Provider();
$paginator = new Paginator("https://onestopsolucoes.com.br/app/fornecedores?page=");
$paginator->pager($providers->find()->count(), 10, $page);
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Fornecedores</h1>
</div>
<div class="row">
    <div class="table-responsive">
        <table id="tableProviders" class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Razão Social</th>
                <th scope="col">CNPJ</th>
                <th scope="col">Endereço</th>
                <th scope="col">Email</th>
                <th scope="col">Telefone</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $providersTable = $providers->find()->limit($paginator->offset())->offset($paginator->limit())->fetch(true);
            if ($providersTable) {
                $count = 0;
                foreach ($providersTable as $provider) {
                    $count++;
                    echo "<tr>";
                    echo "<th scope='row'>" . ($paginator->offset() + $count) . "</th>";
                    echo "<td>$provider->social_reason</td>";
                    echo "<td>" . mask('cnpj', $provider->cnpj) . "</td>";
                    echo "<td>$provider->address $provider->city-$provider->uf</td>";
                    echo "<td>$provider->email</td>";
                    echo "<td>" . mask('telephone', $provider->phonenumber) . "</td>";
                    echo "<td><a class='btn btn-sm btn-info text-white products' data-id='$provider->id'>Ver produtos</a></td>";
                    echo "</tr>";
                }
            } else {
                echo '<tr><td colspan="6" class="text-center py-3">Nenhum registro encontrado.</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between">
        <?= "<p>Página {$paginator->page()} de {$paginator->pages()}</p>"; ?>
        <?= $paginator->render("paginator"); ?>
    </div>
</div>


<?php $v->start('modal'); ?>
<div class="modal fade" id="viewProducts" tabindex="-1" aria-labelledby="viewProductsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newQuoteLabel">Visualizar produtos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<?php $v->stop(); ?>

<?php $v->start('script'); ?>
<script src="<?= asset("js/jquery-3.6.0.min.js"); ?>"></script>
<script>
    $(document).ready(function () {
        $(document).on("click", ".products", function () {
            $.ajax({
                url: "../source/Ajax/fetchProviderProducts2.php",
                data: {id:$(this).data("id")},
                type: "post",
                success: function (data) {
                    $("#viewProducts").modal('show');
                    $("#viewProducts .modal-body").html(data);
                }
            });
        });
    });
</script>
<?php $v->stop(); ?>

