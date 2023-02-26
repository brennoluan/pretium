<?php
$v->layout("theme");

use Source\Models\Products;
use CoffeeCode\Paginator\Paginator;

$page = filter_input(INPUT_GET, "page", FILTER_SANITIZE_STRIPPED);
$products = new Products();
$paginator = new Paginator("https://onestopsolucoes.com.br/app/produtos?search=&page=");
$paginator->pager($products->find((isset($_GET["search"]) ? "provider_id=:provider_id AND product_name LIKE '%{$_GET['search']}%' OR brand LIKE '%{$_GET['search']}%' OR code LIKE '%{$_GET['search']}%'" : "provider_id=:provider_id"), "provider_id=$provider_id")->count(), 10, $page);

?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Produtos</h1>
        <div>
            <a class="btn btn-success" id="addProductModal"><i class="fas fa-plus"></i> Adicionar</a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importProductModal">
                <i class="fas fa-file-import"></i> Importar
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-4">

            <form method="GET">
                <div class="input-group mb-3">
                    <input name="search" type="text" class="form-control px-2"
                           placeholder="Pesquisar por descrição, marca ou código"
                           aria-label="Pesquisar por descrição, marca, código ou unidade de medida"
                           aria-describedby="button-addon2">
                    <button class="btn bg-white border-end border-bottom border-top"><i
                                class="fas fa-search"></i></button>

                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table id="tableProducts" class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Marca</th>
                    <th scope="col">Código</th>
                    <th scope="col">Unidade de medida</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php

                $productsTable = $products->find((isset($_GET["search"]) ? "provider_id=:provider_id AND product_name LIKE '%{$_GET['search']}%' OR brand LIKE '%{$_GET['search']}%' OR code LIKE '%{$_GET['search']}%'" : "provider_id=:provider_id"),
                    "provider_id=$provider_id")->limit($paginator->offset())->offset($paginator->limit())->fetch(true);
                if ($productsTable) {

                    $count = 0;
                    foreach ($productsTable as $product) {
                        $count++;
                        echo "<tr class='product' data-id='" . $product->id . "'>";
                        echo "<th scope='row'>" . ($paginator->offset() + $count) . "</th>";
                        echo "<td class='w-50'>$product->product_name</td>";
                        echo "<td>$product->brand</td>";
                        echo "<td>$product->code</td>";
                        echo "<td>{$product->measurementUnit()->unity} ({$product->measurementUnit()->symbol})</td>";
                        echo "<td><a class='btn btn-warning btn-sm me-2 edit' data-id='$product->id'><i class='fas fa-pen text-white'></i></a><a  class='delete btn btn-danger btn-sm' data-id='$product->id' data-bs-toggle='modal' data-bs-target='#productDeleteModal'><i class='fas fa-trash''></i></a></td>";
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
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= $router->route("app.product"); ?>" method="post">
                    <div class="modal-body">
                        <input type="text" name="id" id="id" hidden>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingInputDescription"
                                   name="description" placeholder="Descrição do produto">
                            <label for="floatingInputDescription">Descrição do produto</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingInputBrand"
                                   name="brand" placeholder="Marca do produto">
                            <label for="floatingInputBrand">Marca do produto</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingInputCode"
                                   name="code" placeholder="Código do produto">
                            <label for="floatingInputCode">Código do produto</label>
                        </div>
                        <div class="form-floating">
                            <select class="form-select" name="measurementUnit" id="floatingSelectMeasurementUnit"
                                    aria-label="Floating label select example">
                                <?php

                                $measurementUnits = (new \Source\Models\MeasurementUnits())->find()->fetch(true);
                                if ($measurementUnits) {
                                    foreach ($measurementUnits as $measurementUnit) {
                                        echo "<option value='$measurementUnit->id'>$measurementUnit->unity ($measurementUnit->symbol)</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingSelectMeasurementUnit">Unidade de medida</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button class="btn btn-success" id="productModalButton"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importProductModal" tabindex="-1" aria-labelledby="importProductModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importProductModalLabel">Importar produtos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="importProducts" action="<?= $router->route("app.importproducts"); ?>" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <p class="text-secondary">Utilize a planilha base para inserção dos produtos que deseja importar
                            para a plataforma (não altere a ordem das colunas).</p>
                        <a href="<?= url('base.csv'); ?>" class="btn btn-secondary text-white"><i
                                    class="fas fa-cloud-download-alt"></i> Baixar planilha base</a>

                        <p class="text-secondary mt-3">Códigos da unidade de medida:</p>
                        <p class="text-secondary">1 - Centímetros</p>
                        <p class="text-secondary">2 - Metros</p>
                        <p class="text-secondary">3 - Unidade</p>

                        <input class="form-control mt-3" type="file" name="file" id="file" accept=".csv">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button class="btn btn-success">Importar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $v->stop(); ?>

<?php $v->start('script'); ?>
    <script src="<?= asset("js/jquery-3.6.0.min.js"); ?>"></script>
    <script src="<?= asset("js/sweetalert2.all.min.js"); ?>"></script>
    <script>
        $(document).ready(function () {


            $('.modal').on('hidden.bs.modal', function () {

                $(this).find('form').trigger('reset');
            });

            $("#addProductModal").click(function () {
                $("#productModal").modal('show');
                $("#productModal #productModalLabel").text('Adicionar produto');
                $("#productModal #productModalButton").text('Adicionar');
            });

            $(document).on("click", ".edit", function () {
                $.ajax({
                    type: "POST",
                    url: "../source/Ajax/fetchProduct.php",
                    data: {id: $(this).data("id")},
                    dataType: "json",
                    success: function (data) {
                        $("#productModal").modal('show');
                        $("#productModal #productModalLabel").text('Editar produto');
                        $("#productModal #productModalButton").text('Editar');
                        $("#productModal #floatingInputDescription").val(data.product_name);
                        $("#productModal #floatingInputBrand").val(data.brand);
                        $("#productModal #floatingInputCode").val(data.code);
                        $("#productModal #id").val(data.id);
                        $("#productModal #floatingSelectMeasurementUnit").val(data.measurement_unit);
                    }
                });
            });

            $(document).on("click", ".delete", function () {
                Swal.fire({
                    title: 'Excluir produto',
                    text: "Você tem certeza de que deseja excluir o produto selecionado?",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Excluir'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "../source/Ajax/deleteProduct.php",
                            data: {id: $(this).data("id")}
                        });
                        $(this).closest("tr").remove();
                    }
                })
            });
        });

        $("form#importProducts").submit(function (e) {

            e.preventDefault();

            if ($('#file').get(0).files.length === 0){
                Swal.fire({
                    icon: 'error',
                    title: 'Selecione um arquivo',
                })
            }
            var form = $(this);
            var action = form.attr("action");
            var formData = new FormData($(this)[0]);

            $.ajax({
                url: action,
                data: formData,
                type: "post",
                async: false,
                cache: false,
                contentType: false,
                enctype: 'multipart/form-data',
                processData: false,
                dataType: "json",
                success: function (data) {

                    $('#importProductModal').modal('hide');

                    if(data.data){

                    }
                    /*$.each(data.data, function(index, value) {
                        Swal.fire({
                            title: 'Produto já existe',
                            html: "Deseja susbtituir ou cancelar o produto <strong>" + value.product_name + "</strong>",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sim, substituir!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire(
                                    'Produto substituido',
                                    'Produto substituido com sucesso.',
                                    'success'
                                )
                            }
                        })
                    });*/

                    if(data.icon) {
                        Swal.fire({
                            icon: data.icon,
                            title: data.title,
                            text: data.message
                        }).then(() => {
                            window.location.reload();
                        })
                    }
                }
            });
        });
    </script>
<?php $v->stop(); ?>