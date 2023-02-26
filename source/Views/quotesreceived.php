<?php
$v->layout("theme");

use CoffeeCode\Paginator\Paginator;

$page = filter_input(INPUT_GET, "page", FILTER_SANITIZE_STRIPPED);
$quotes = new \Source\Models\Quotes();
$paginator = new Paginator("https://onestopsolucoes.com.br/app/cotacoesrecebidas?page=");
$paginator->pager($quotes->find("provider_id = :provider_id", "provider_id={$provider_id}")->count(), 10, $page);
?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Cotações recebidas</h1>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Status</th>
                    <th scope="col">ID da Cotação</th>
                    <th scope="col">Expira em</th>
                    <th scope="col">Remetente</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody>

                <?php
                $quotesTable = $quotes->find("provider_id = :provider_id", "provider_id={$provider_id}")/*->order("CASE WHEN status_id=1 THEN status_id WHEN status_id=1 THEN status_id=5 WHEN status_id=6 THEN status_id END DESC")*/ ->limit($paginator->offset())->offset($paginator->limit())->fetch(true);
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
                            <td><?= date("d/m/Y", strtotime($quote->closure_at)); ?></td>
                            <td><?= $quote->user()->username . ' (<strong>' . $quote->user()->provider()->social_reason . '</strong>)'; ?></td>
                            <td><a class="btn btn-info btn-sm view" data-id="<?= $quote->id; ?>"><i
                                            class="fas fa-eye"></i></a></td>
                        </tr>
                    <?php endforeach;
                else:
                    echo '<tr><td colspan="6" class="text-center py-3">Nenhum registro encontrado.</td></tr>';
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
    <div class="modal fade" id="viewQuote" tabindex="-1" aria-labelledby="viewQuoteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newQuoteLabel">Visualizar cotação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="action" action="<?= $router->route('app.myquote'); ?>" method="post" style="overflow-y:auto">
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
        function formatMoney(n, c, d, t) {
            c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        }

        $(document).ready(function () {
            $(document).on("click", ".view", function () {
                $.ajax({
                    url: "../source/Ajax/fetchQuote.php",
                    data: {userid: <?= $_SESSION['user']; ?>, id: $(this).data("id")},
                    type: "post",
                    success: function (data) {
                        $("#viewQuote").modal('show');
                        $("#viewQuote .modal-body").html(data);
                        var status = $("span[data-status]").text();
                        if (status === "Recusada") {
                            $("#viewQuote .modal-footer").empty();

                            var total = 0
                            $('.price').each(function () {
                                total += parseFloat($(this).val() * $(this).closest('tr').find('.qtd').text())
                            })

                            $('span.total').text(formatMoney(total))

                        }
                        if (status === "Solicitação Aceita") {
                            $("#viewQuote .modal-footer #yes").html('Enviar precificação').removeClass('btn-success').addClass('btn-info text-white');
                        }
                        if (status === "Aguardando aprovação") {
                            $("#viewQuote .modal-footer").empty();
                        }
                        if (status === "Aprovada") {
                            $("#viewQuote .modal-footer").empty();

                            var total = 0
                            $('.price').each(function () {
                                total += parseFloat($(this).val() * $(this).closest('tr').find('.qtd').text())
                            })

                            $('span.total').text(formatMoney(total))
                        }
                        if (status === "Finalizada (Tempo Expirou)") {
                            $("#viewQuote .modal-footer").empty();
                        }

                    }
                });
            });

            $(document).on("change", "input[type='number']", function () {
                var subtotal = $(this).val() * $(this).closest('tr').find('.qtd').text()
                $(this).closest('tr').find('.subtotal').html(formatMoney(subtotal))

                var total = 0
                $('.price').each(function (){
                    total += parseFloat($(this).val() * $(this).closest('tr').find('.qtd').text())
                })

                $('span.total').text(formatMoney(total))
            })

            $("#yes").click(function () {
                var status = $("span[data-status]").text()
                Swal.fire({
                    title: (status === 'Solicitação Aceita' ? 'Enviar precificação' : 'Aceitar cotação'),
                    text: "Você tem certeza de que deseja " + (status == "Solicitação Aceita" ? 'enviar a precificaçao dessa cotação?' : 'aceitar essa cotação?'),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: (status === "Solicitação Aceita" ? '#0dcaf0' : '#0d6efd'),
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: (status === "Solicitação Aceita" ? 'Enviar precificação' : 'Aceitar')
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
                                if (data.status !== 'Solicitação Aceita') {
                                    $("#viewQuote").modal('hide');
                                }

                                $('.status[data-id=' + $('#date').data("quoteid") + ']').html(data.status).addClass(data.class);

                                Swal.fire({
                                    icon: 'success',
                                    title: (data.status === 'Solicitação Aceita' ? 'Cotação aceita' : 'Precificação enviada'),
                                    text: '',
                                    confirmButtonColor: '#1976D2'
                                })

                                if (data.status === 'Solicitação Aceita') {
                                    $.ajax({
                                        url: "../source/Ajax/fetchQuote.php",
                                        data: {userid: <?= $_SESSION['user']; ?>, id: $('#date').data("quoteid")},
                                        type: "post",
                                        success: function (data) {
                                            $("#viewQuote").modal('show');
                                            $("#viewQuote .modal-body").html(data);
                                            var status = $("span[data-status]").text();
                                            if (status === "Recusada") {
                                                $("#viewQuote .modal-footer").empty();
                                            }
                                            if (status === "Solicitação Aceita") {
                                                $("#viewQuote .modal-footer #yes").html('Enviar precificação').removeClass('btn-success').addClass('btn-info text-white');
                                            }
                                            if (status === "Aguardando aprovação") {
                                                $("#viewQuote .modal-footer").empty();
                                            }
                                            if (status === "Aprovada") {
                                                $("#viewQuote .modal-footer").empty();
                                            }

                                        }
                                    });
                                }
                            }
                        });
                    }
                })
            });

            $("#no").click(function () {
                Swal.fire({
                    title: 'Recusar cotação',
                    text: "Você tem certeza de que deseja recusar essa cotação?",
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
                                    title: 'Cotação recusada',
                                    text: '',
                                    confirmButtonColor: '#1976D2'
                                })
                            }
                        });
                    }
                })
            });
        });
    </script>
<?php $v->stop(); ?>