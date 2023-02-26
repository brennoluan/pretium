<?php
$v->layout("theme");
?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Preços</h1>
    </div>
    <div class="jumbotron bg-warning my-3 p-3 rounded text-white fw-bold">
        Aqui fica listado as precificações das suas cotações em aberto, ordenada do menor preço para o maior.
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">ID da Cotação</th>
                    <th scope="col">Expira em</th>
                    <th scope="col">Subtotal</th>
                    <th scope="col">Ação</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($quotes):
                    $array = [];
                    function sortByOrder($a, $b)
                    {
                        return $a[2] - $b[2];
                    }


                    foreach ($quotes

                             as $quote):
                        $subtotal = 0;
                        $quoteProducts = (new \Source\Models\QuoteProduct())->find("quote_id={$quote->id}", "",
                            "subtotal")->fetch(true);
                        foreach ($quoteProducts as $quoteProduct):
                            $subtotal += $quoteProduct->subtotal;
                        endforeach;
                        $array[] = [$quote->id, date("d/m/Y", strtotime($quote->closure_at)), $subtotal];

                        ?>
                    <?php endforeach;

                usort($array, 'sortByOrder');

                for ($i = 0; $i < count($array); $i++): ?>

                    <tr>
                        <th scope="row"><?= $i+1 ?></th>
                        <td><?= $array[$i][0]; ?></td>
                        <td><?= $array[$i][1]; ?></td>
                        <td class="fw-bold"><?= 'R$' . number_format($array[$i][2], 2, ",", "."); ?></td>
                        <td><a class="btn btn-info btn-sm view" data-id="<?= $array[$i][0]; ?>">Ver cotação</a></td>
                    </tr>
                <?php endfor;
                endif;?>
                </tbody>
            </table>
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
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <a href="<?= $router->route('app.myquotes'); ?>" class="btn btn-success w-100 text-white">Ir para
                        página de cotação</a>
                </div>
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
                        $("#viewQuote .modal-footer").addClass("d-none");
                        if (status === "Aguardando aprovação") {
                            $("#viewQuote .modal-footer").removeClass("d-none");

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

        })
    </script>
<?php $v->stop(); ?>