<?php
$v->layout("theme");

use CoffeeCode\Paginator\Paginator;

$page = filter_input(INPUT_GET, "page", FILTER_SANITIZE_STRIPPED);
$quotes = new \Source\Models\Quotes();
$paginator = new Paginator("https://onestopsolucoes.com.br/app/minhascotacoes?page=");
$paginator->pager($quotes->find("user_id=:user_id", "user_id=$user_id")->count(), 10, $page);
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Minhas cotações</h1>
    <a class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newQuote">+ nova cotação</a>
</div>
<div class="row">
    <div class="table-responsive">
        <table id="tableMyQuotes" class="table table-sm">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Status</th>
                <th scope="col">ID da Cotação</th>
                <th scope="col">Expira em</th>
                <th scope="col">Destinatário</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $quotesTable = $quotes->find("user_id=:user_id",
                "user_id=$user_id")/*->order("CASE WHEN status_id=1 THEN status_id WHEN status_id=1 THEN status_id=5 WHEN status_id=6 THEN status_id END DESC")*/ ->limit($paginator->offset())->offset($paginator->limit())->fetch(true);
            if ($quotesTable):
                $count = 0;
                foreach ($quotesTable as $quote):
                    $dateNow = new DateTime('now');
                    $dateQuoteClosure = new DateTime($quote->closure_at);
                    if ($dateQuoteClosure < $dateNow && $quote->status_id != 7 && $quote->status_id != 4 && $quote->status_id != 3) {
                        $quote->status_id = 7;
                        $quote->save();
                    }
                    $count++;
                    ?>
                    <tr>
                        <th scope="row"><?= ($paginator->offset() + $count); ?></th>
                        <td data-id="<?= $quote->id; ?>"
                            class="fw-bold status <?= (STATUS_CLASS["{$quote->status()->name}"]); ?>"><?= $quote->status()->name; ?></td>
                        <td><?= $quote->id; ?></td>
                        <td><?= date("d/m/Y", strtotime($quote->closure_at)) ?></td>
                        <td><?= $quote->provider()->social_reason; ?></td>
                        <td>
                            <a title="Visualizar" class="btn btn-info btn-sm view" data-id="<?= $quote->id; ?>"><i
                                        class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                <?php endforeach;
            else:
                echo '<tr id="nan"><td colspan="6" class="text-center py-3">Nenhum registro encontrado.</td></tr>';
            endif; ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between">
        <?= "<p>Página {$paginator->page()} de {$paginator->pages()}</p>"; ?>
        <?= $paginator->render("paginator"); ?>
    </div>
</div>

<?php $v->start('modal'); ?>
<div class="modal fade" id="newQuote" tabindex="-1" aria-labelledby="newQuoteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newQuoteLabel">Nova cotação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form" autocomplete="off" action="<?= $router->route("app.newQuote"); ?>" method="post" style="overflow-y:auto">
                <div class="modal-body">
                    <div class="row mb-3">

                        <?php $date = date('Y-m-d'); ?>

                        <div class="col-6">
                            <label for="">Data de abertura</label>
                            <input type="date" class="form-control" value="<?= $date; ?>" disabled>
                        </div>
                        <div class="col-6">
                            <label for="">Data de fechamento</label>
                            <input type="date" class="form-control" name="closure_at"
                                   value="<?= date('Y-m-d', strtotime("+3 day", strtotime($date))); ?>"
                                   min="<?= date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <label for="providerDataList" class="form-label">Escolha um fornecedor</label>
                    <input class="form-control mb-3" name="provider" list="providerDatalistOptions"
                           id="providerDataList"
                           placeholder="Escreva para pesquisar...">
                    <datalist id="providerDatalistOptions">
                        <?php
                        $providers = (new \Source\Models\Provider())->find("id != {$provider_id}")->fetch(true);
                        if ($providers):
                        foreach ($providers

                        as $provider): ?>
                        <option value="<?= $provider->social_reason; ?>">
                            <?php
                            endforeach;
                            endif; ?>
                    </datalist>
                </div>
                <div class="modal-footer">
                    <span id="closeNewQuote" class="btn btn-secondary">Fechar</span>
                    <span id="sendQuote" class="btn btn-primary">Enviar cotação</span>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="viewQuote" tabindex="-1" aria-labelledby="viewQuoteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newQuoteLabel">Visualizar cotação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="action" action="<?= $router->route('app.quotesreceived'); ?>" method="post" style="overflow-y:auto">
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <div class="row w-100">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <span id="yes" type="button" class="btn btn-success">Aceitar</span>
                            <span id="no" type="button" class="btn btn-danger">Recusar</span>
                        </div>
                    </div>
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

    var product = [];

    function formatMoney(n, c, d, t) {
        c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    }

    $(document).ready(function () {

        $("#newQuote").on('hidden.bs.modal', function () {
            $("#products").remove()
            $("#tableProducts").remove()
            $("#providerDataList").val('')
            product = []
        });

        $(document).on("change", "#providerDataList", function () {
            $("#products").remove()
            $("#tableProducts").remove()
            product = []
            if ($(this).val()) {
                $.post("../source/Ajax/fetchProviderProducts.php", {provider: $(this).val()}, function (data) {
                    $("#newQuote .modal-body").append(data)
                })
            }
        });

        $(document).on("change", "#providerDataList2", function () {
            if ($(this).val()) {

                $.ajax({
                    type: "POST",
                    url: "../source/Ajax/fetchProviderProduct.php",
                    data: {provider: $("#providerDataList").val(), product_name: $(this).val()},
                    dataType: "json",
                    success: function (data) {

                        $("#tableProductsNewQuote tbody").append('<tr><th scope=\'row\'>#</th><td class=\'description\'\'>' + data.product_name + '</td><td>' + data.brand + '</td><td><input class=\'form-control form-control-sm unity\' style="width: 60px;" type=\"number\" value=\"1\"></td><td><span title="Excluir" class="btn btn-danger btn-sm delete"><i class="fas fa-trash"></i></span></tr></tr>');

                        product.push([data.product_name, 1]);

                        $("#providerDatalistOptions2").find("option[value='" + data.product_name + "']").val('');
                        $("#providerDataList2").val('');
                    }
                });
            }
        });

        $(document).on("change", ".unity", function () {
            for (var i = product.length - 1; i >= 0; i--) {
                if (product[i][0] === $(this).closest('tr').find('.description').text()) {
                    product[i][1] = $(this).val();
                }
            }
        });

        $(document).on("click", ".delete", function () {

            Swal.fire({
                title: 'Excluir item',
                text: "Você tem certeza de que deseja excluir o item selecionado?",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Excluir'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#providerDatalistOptions2').append('<option value=\'' + $(this).closest('tr').find('.description').text() + '\'>' + $(this).closest('tr').find('.description').text() + '</option>');

                    for (var i = product.length - 1; i >= 0; i--) {
                        if (product[i][0] === $(this).closest('tr').find('.description').text()) {
                            product.splice(i, 1);
                        }
                    }

                    $(this).closest('tr').remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Item excluído',
                        confirmButtonColor: '#3085d6',
                    })
                }
            })
        });

        $(document).on("click", ".view", function () {
            $.ajax({
                url: "../source/Ajax/fetchQuote.php",
                data: {userid: <?= $_SESSION['user']; ?>, id: $(this).data("id")},
                type: "post",
                success: function (data) {
                    $("#viewQuote").modal('show');
                    $("#viewQuote .modal-body").html(data);
                    var status = $("span[data-status]").text();
                    var footer = $("#viewQuote .modal-footer").html();
                    $("#viewQuote .modal-footer").empty();
                    if (status === "Aguardando aprovação") {
                        $("#viewQuote .modal-footer").html(footer);

                        var total = 0
                        $('.price').each(function () {
                            total += parseFloat($(this).val() * $(this).closest('tr').find('.qtd').text())
                        })

                        $('span.total').text(formatMoney(total))
                    } else if (status === "Recusada" || status === "Aprovada") {

                        var total = 0
                        $('.price').each(function () {
                            total += parseFloat($(this).val() * $(this).closest('tr').find('.qtd').text())
                        })

                        $('span.total').text(formatMoney(total))
                    }
                }
            });
        });

        $("#closeNewQuote").click(function () {

            Swal.fire({
                title: 'Fechar cotação',
                text: "Você tem certeza de que deseja fechar a cotação?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Fechar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#newQuote").modal('hide')
                }
            })
        })

        $(document).on("click", "#yes",function() {
            Swal.fire({
                title: 'Aceitar precificação',
                text: "Você tem certeza de que deseja aceitar a precificação dessa cotação?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Aceitar precificação'
            }).then((result) => {
                if (result.isConfirmed) {
                    var action = $('form#action').attr("action");

                    var product = []
                    $(".price").each(function () {
                        product.push([$(this).data('productid'), $(this).val()])
                    })

                    $.ajax({
                        url: action,
                        data: {action: 'yes', quoteid: $('#date').data("quoteid"), product},
                        type: "post",
                        dataType: "json",
                        success: function (data) {
                            $("#viewQuote").modal('hide');
                            console.log(data)
                            $('.status[data-id=' + $('#date').data("quoteid") + ']').html(data.status).removeClass('text-dark').addClass(data.class);

                            Swal.fire({
                                icon: 'success',
                                title: 'Precificação aceita',
                                text: '',
                                confirmButtonColor: '#1976D2'
                            })
                        }
                    });
                }
            })
        })

        $(document).on("click", "#no",function() {
            Swal.fire({
                title: 'Recusar precificação',
                text: "Você tem certeza de que deseja recusar a precificação dessa cotação?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Recusar'
            }).then((result) => {
                if (result.isConfirmed) {

                    var action = $('form#action').attr("action");

                    $.ajax({
                        url: action,
                        data: {action: 'no', quoteid: $('#date').data("quoteid")},
                        type: "post",
                        dataType: "json",
                        success: function (data) {

                            $("#viewQuote").modal('hide');
                            $('.status[data-id=' + $('#date').data("quoteid") + ']').html(data.status).addClass(data.class);

                            Swal.fire({
                                icon: 'error',
                                title: 'Precificação recusada',
                                text: '',
                                confirmButtonColor: '#1976D2'
                            })
                        }
                    });
                }
            })
        });

        $("#sendQuote").click(function (e) {

            if ($('#providerDataList').val().length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Selecione um fornecedor'
                })
                return;
            }
            if (product.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Selecione pelo menos um produto'
                })
                return;
            }

            Swal.fire({
                title: 'Você tem certeza?',
                text: "Esta ação não poderá ser revertida",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Tenho certeza!'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.preventDefault();

                    var form = $("#form");
                    var action = form.attr("action");
                    var data = form.serialize();

                    $.ajax({
                        url: action,
                        data: {data, product},
                        type: "post",
                        dataType: "json",
                        success: function (data) {
                            $("#newQuote").modal('hide');

                            Swal.fire({
                                icon: 'success',
                                title: 'Nova cotação criada',
                                text: '',
                                confirmButtonColor: '#1976D2'
                            })

                            $('#nan').remove();

                            if ($('table tbody tr').length < 11) {
                                $('#tableMyQuotes').append('<tr><th scope="row">' + $('table tbody tr').length + '</th><td class="fw-bold ' + data.class + '">' + data.status + '</td><td>' + data.id + '</td><td>' + data.closure_at + '</td><td>' + data.dest + '</td><td><a title="Visualizar" class="btn btn-info btn-sm view" data-id="' + data.id + '"><i class="fas fa-eye"></i></a></td></tr>')
                            }

                        }
                    });
                }
            })

        });
    });
</script>
<?php $v->stop(); ?>
