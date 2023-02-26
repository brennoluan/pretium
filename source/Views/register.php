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
    <div class="row justify-content-center align-items-center vh-100">
        <!---<div class="col-12 col-md-5  p-5 bg-info shadow" style="height:95%; border-radius: 30px">
            <h4 class="d-flex justify-content-center pb-2 pt-4" style="margin-top: 130px">Bem Vindo ao Pretium</h4>
            <p class="d-flex justify-content-center h5 text-center text-muted pb-2">Já possue uma conta? Faça Login!</p>
            <div class="d-flex justify-content-center">
                <a href="<?= $router->route("web.login"); ?>" class="btn btn-primary btn-lg" style="border-radius: 30px">Login</a>
            </div>
        </div>!-->
        <div class="col-12 col-md-8  p-4 bg-white shadow" style="height:95%; border-radius: 30px">
            <a href="<?= $router->route("web.login"); ?>" title="Voltar" class="link-secondary"><i
                        class="fas fa-arrow-left" style="font-size: 1.3em;"></i></a>
            <h1 class="d-flex justify-content-center pb-3"><?= SITENAME; ?></h1>
            <div class="d-flex justify-content-center form_callback">
                <?= flash(); ?>
            </div>
            <form id="formCadastro" action="<?= $router->route("web.register"); ?>" method="post">

                <span class="text-muted">Seu dados</span>

                <div class="input-group mt-2 mb-2">
                    <input type="text" class="form-control" name="fullName" placeholder="Nome *" required>
                </div>
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="email" placeholder="E-mail pessoal *" required>
                </div>
                <div class="row mb-2">
                    <div class="col-6">
                        <div class="input-group">
                            <input type="password" class="form-control" name="passwd" placeholder="Senha *" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group">
                            <input type="password" class="form-control" name="confirmPasswd"
                                   placeholder="Confirmar senha *" required>
                        </div>
                    </div>
                </div>

                <span class="text-muted">Dados da empresa</span>
                <div class="row mt-2">
                    <div class="col-6">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="socialReason" placeholder="Razão Social *"
                                   required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" id="cnpj" name="cnpj" placeholder="CNPJ *" required>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="businessEmail" placeholder="E-mail corporativo *"
                           required>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" id="phonenumber" name="phoneNumber"
                                   placeholder="Telefone *" required>

                        </div>
                        <div class="">
                            <input type="text" class="form-control" name="city" placeholder="Cidade *" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="address" placeholder="Endereço *" required>
                            <input type="text" class="form-control" name="cep" placeholder="CEP *"
                                   onkeypress="$(this).mask('00.000-000') " required>
                        </div>
                        <div>
                            <input type="text" class="form-control" name="uf" placeholder="UF *" required>
                        </div>

                    </div>
                    <!---<div class="row" id="rowClient">
                    <div class="col-6">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="cargo" placeholder="Cargo">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group mb-2">
                            <input type="number" class="form-control" name="ncompradores" placeholder="N° de compradores">
                        </div>
                    </div>
                </div>
                <div class="input-group mb-2">
                    <select name="categoryBusiness" id="categoryBusiness" class="form-control">
                        <option value="">Escolha uma categoria</option>
                        <?php foreach (CATEGORY as $c): ?>
                            <option value="<?= $c; ?>"><?= $c; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>-->

                    <span class="d-flex justify-content-end pb-2">(*) CAMPOS OBRIGATORIOS</span>
                    <span class="btn btn-primary w-100 cadastrar" style="border-radius: 30px">Finalizar cadastro</span>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalPolitica" tabindex="-1" aria-labelledby="modalPoliticaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPoliticaLabel">Política de Privacidade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table>
                    <tbody>
                    <tr>
                        <td>
                            <h4><strong>Pol&iacute;tica de Privacidade</strong></h4>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p><span style="font-weight: 400;">Nossa Pol&iacute;tica de Privacidade regula o modo por meio do qual processamos os seus dados pessoais quando voc&ecirc; visita o nosso site &lsquo;https://onestopsolucoes.com.br/entrar&rsquo; ou usa nossos servi&ccedil;os contendo um link para essa Pol&iacute;tica de privacidade.</span>
                </p>
                <p><span style="font-weight: 400;">Essa Pol&iacute;tica descreve ainda as fontes utilizadas para obter seus dados pessoais, bem como instrui sobre como as utilizamos, as compartilhamos e as protegemos. Al&eacute;m disso, esclarece quais s&atilde;o seus direitos e op&ccedil;&otilde;es em rela&ccedil;&atilde;o ao uso de seus dados pessoais. Tudo isso de acordo com a previs&atilde;o da Lei n.&ordm; 13.709/18 (&ldquo;Lei Geral de Prote&ccedil;&atilde;o de Dados ou &ldquo;LGPD&rdquo;).&nbsp;</span>
                </p>
                <p><span style="font-weight: 400;">Ao utilizar qualquer dos Servi&ccedil;os, voc&ecirc; concorda com os termos e condi&ccedil;&otilde;es desta Pol&iacute;tica e consente com a coleta, utiliza&ccedil;&atilde;o e divulga&ccedil;&atilde;o de seus dados pessoais, conforme estabelecido abaixo.</span>
                </p>
                <p>&nbsp;</p>
                <ul>
                    <li>
                        <h4><strong><strong>Dados Pessoais Coletados</strong></strong></h4>
                    </li>
                </ul>
                <p>&nbsp;</p>
                <p><strong>1.1 Informa&ccedil;&otilde;es fornecidas diretamente pelo usu&aacute;rio</strong></p>
                <p><span style="font-weight: 400;">Os dados pessoais s&atilde;o fornecidos diretamente pelo usu&aacute;rio ao realizar o cadastro para ter acesso aos nossos servi&ccedil;os. Esses dados podem incluir nome completo, e-mail, data de nascimento, CPF, raz&atilde;o social, CNPJ, endere&ccedil;o, cidade, estado, pa&iacute;s, telefone/celular, entre outras.&nbsp;</span>
                </p>
                <p>&nbsp;</p>
                <p><strong>1.2 Informa&ccedil;&otilde;es que podemos coletar de forma autom&aacute;tica</strong></p>
                <p><span style="font-weight: 400;">Al&eacute;m dos dados pessoais que voc&ecirc; nos fornece, n&oacute;s e nossos provedores de servi&ccedil;os utilizamos tecnologias que automaticamente coletam outras informa&ccedil;&otilde;es.&nbsp;</span>
                </p>
                <p><span style="font-weight: 400;">Exemplos:</span></p>
                <ul>
                    <li><span style="font-weight: 400;">Endere&ccedil;o IP e Outras Informa&ccedil;&otilde;es de Identifica&ccedil;&atilde;o</span><span
                                style="font-weight: 400;">.&nbsp;</span></li>
                    <li><span style="font-weight: 400;"> Informa&ccedil;&otilde;es e Conte&uacute;do de M&iacute;dia Social.</span>
                    </li>
                    <li><span style="font-weight: 400;">Cookies</span><span style="font-weight: 400;">.</span></li>
                </ul>
                <p>&nbsp;</p>
                <p><strong>1.3 Informa&ccedil;&otilde;es que voc&ecirc; incluir como usu&aacute;rio&nbsp;</strong></p>
                <p><span style="font-weight: 400;">Em determinadas funcionalidades, voc&ecirc; poder&aacute; usar nossos Servi&ccedil;os para enviar arquivos, como planilhas para importa&ccedil;&atilde;o de produtos. O Pretium classifica as informa&ccedil;&otilde;es que voc&ecirc; publicar nestes campos como p&uacute;blicas e n&atilde;o limita o uso ou divulga&ccedil;&atilde;o dessas informa&ccedil;&otilde;es de acordo com essa Pol&iacute;tica, podendo, inclusive, utilizar tais informa&ccedil;&otilde;es em nossa plataforma.</span>
                </p>
                <p>&nbsp;</p>
                <ul>
                    <li>
                        <h4><strong><strong>Tratamento de Dados Pessoais</strong></strong></h4>
                    </li>
                </ul>
                <p>&nbsp;</p>
                <p><span style="font-weight: 400;">N&oacute;s e nossos provedores de servi&ccedil;os podemos usar seus dados pessoais para as finalidades</span>
                </p>
                <p><span style="font-weight: 400;">abaixo:&nbsp;</span></p>
                <ul>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">Criar, manter e administrar sua conta em nosso site, de modo a aprimorar sua experi&ecirc;ncia com os Servi&ccedil;os, bem como te fornecer os Servi&ccedil;os que voc&ecirc; requisitar.</span>
                    </li>
                    <li style="font-weight: 400;"><span
                                style="font-weight: 400;">Exibir an&uacute;ncios relevantes.</span></li>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">Conduzir an&aacute;lises ou pesquisas com o objetivo de alavancar os Servi&ccedil;os que oferecemos.</span>
                    </li>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">Comunicar a voc&ecirc; o conte&uacute;do e sobre os Servi&ccedil;os de seu interesse, ou ainda para responder seus coment&aacute;rios e perguntas em nosso site.</span>
                    </li>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">Entrar em contato com voc&ecirc;. Podemos periodicamente lhe enviar notifica&ccedil;&otilde;es relacionadas aos Servi&ccedil;os.</span>
                    </li>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">De acordo com seu consentimento. Para aumentar o interesse e relev&acirc;ncia dos Servi&ccedil;os, podemos usar seus dados pessoais para tirar conclus&otilde;es e realizar previs&otilde;es sobre suas poss&iacute;veis &aacute;reas de interesse. Quando seus dados forem anonimizados e, portanto, n&atilde;o forem capazes de identificar voc&ecirc; detalhadamente, podemos utiliz&aacute;-las para qualquer fim e/ou compartilh&aacute;-las com terceiros.</span>
                    </li>
                </ul>
                <p>&nbsp;</p>
                <ul>
                    <li>
                        <h4><strong><strong>Compartilhamento e Divulga&ccedil;&atilde;o de Dados
                                    Pessoais</strong></strong></h4>
                    </li>
                </ul>
                <p>&nbsp;</p>
                <p><span style="font-weight: 400;">Usaremos e compartilharemos seus dados pessoais nos termos desta Pol&iacute;tica, inclusive:</span>
                </p>
                <ul>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">Com entidades que nos d&atilde;o suporte para administrar e fornecer os Servi&ccedil;os (exemplos: outros usu&aacute;rios da plataforma, distribui&ccedil;&atilde;o de e-mails, implementa&ccedil;&atilde;o e manuten&ccedil;&atilde;o de sites, entre outros), na medida em que estas entidades precisam acessar Suas Informa&ccedil;&otilde;es para executar seus servi&ccedil;os ou de acordo com a legisla&ccedil;&atilde;o.</span>
                    </li>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">Para responder a processos judiciais ou quando entendermos que: (i) &eacute; exigido por lei, e/ou (ii) &eacute; necess&aacute;rio para proteger bens, direitos ou seguran&ccedil;a de terceiros, e/ou (iii) precisamos investigar potencial viola&ccedil;&atilde;o da lei.</span>
                    </li>
                    <li style="font-weight: 400;"><span
                                style="font-weight: 400;">De acordo com seu consentimento.</span></li>
                </ul>
                <p><span style="font-weight: 400;">Quando os dados pessoais coletados s&atilde;o agregados, an&ocirc;nimos ou n&atilde;o identificam voc&ecirc; pessoalmente, podemos us&aacute;-los para qualquer fim ou compartilh&aacute;-los com terceiros.</span>
                </p>
                <p>&nbsp;</p>
                <ul>
                    <li>
                        <h4><strong><strong>Suas Escolhas e Direitos</strong></strong></h4>
                    </li>
                </ul>
                <p>&nbsp;</p>
                <p><span style="font-weight: 400;">Abaixo listamos quais s&atilde;o os seus direitos e como voc&ecirc; pode exerc&ecirc;-los em algumas situa&ccedil;&otilde;es</span>
                </p>
                <p><span style="font-weight: 400;">espec&iacute;ficas:</span></p>
                <ul>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">Voc&ecirc; poder&aacute; ter o direito de solicitar o acesso, revis&atilde;o, corre&ccedil;&atilde;o, atualiza&ccedil;&atilde;o, supress&atilde;o, exclus&atilde;o ou a limita&ccedil;&atilde;o do uso de seus dados pessoais por meio do e-mail suporte@onestopsolucoes.com.br. N&oacute;s tomaremos as medidas apropriadas para verificar sua identidade antes de garantir o acesso a seus dados pessoais e envidaremos os melhores esfor&ccedil;os para investigar, cumprir e responder suas solicita&ccedil;&otilde;es nos termos da LGPD.</span>
                    </li>
                </ul>
                <p><span style="font-weight: 400;">Vale notar que pode haver situa&ccedil;&otilde;es em que a legisla&ccedil;&atilde;o n&atilde;o permite a remo&ccedil;&atilde;o do conte&uacute;do, ainda mediante solicita&ccedil;&atilde;o nesse sentido, em raz&atilde;o de uma outra obriga&ccedil;&atilde;o legal.</span>
                </p>
                <ul>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">Se voc&ecirc; n&atilde;o deseja mais receber futuros e-mails que enviamos automaticamente. Por favor escreva para suporte@onestopsolucoes para cancelar o recebimento destes e-mails.</span>
                    </li>
                </ul>
                <p><span style="font-weight: 400;">Se lhe enviarmos mensagens de publicidade eletr&ocirc;nica com base no seu consentimento ou de outra forma permitida pela LGPD, voc&ecirc; poder&aacute;, a qualquer momento e de forma gratuita, retirar</span>
                </p>
                <p><span style="font-weight: 400;">a sua autoriza&ccedil;&atilde;o ou declarar a sua recusa. As mensagens de publicidade eletr&ocirc;nica enviadas</span>
                </p>
                <p><span style="font-weight: 400;">atrav&eacute;s de e-mail - incluem mecanismo de recusa dentro da pr&oacute;pria mensagem - por exemplo,</span>
                </p>
                <p><span style="font-weight: 400;">link para cancelamento da subscri&ccedil;&atilde;o nos e-mails que lhe enviamos</span><span
                            style="font-weight: 400;">.</span></p>
                <p>&nbsp;</p>
                <ul>
                    <li>
                        <h4><strong><strong>Seguran&ccedil;a da Informa&ccedil;&atilde;o</strong></strong></h4>
                    </li>
                </ul>
                <p>&nbsp;</p>
                <p><span style="font-weight: 400;">N&oacute;s adotamos as medidas administrativas, t&eacute;cnicas, pessoais e f&iacute;sicas apropriadas contra perda, roubo, uso ou modifica&ccedil;&atilde;o n&atilde;o autorizada de seus dados pessoais. Contudo, tenha ci&ecirc;ncia de que nenhum sistema &eacute; completamente seguro. As medidas de seguran&ccedil;a implementadas consideram, como prev&ecirc; a LGPD, a natureza dos dados e de tratamento, os riscos envolvidos, a tecnologia existente e sua disponibilidade</span>
                </p>
                <p>&nbsp;</p>
                <ul>
                    <li>
                        <h4><strong><strong>Armazenamento, conserva&ccedil;&atilde;o e elimina&ccedil;&atilde;o de
                                    dados</strong></strong></h4>
                    </li>
                </ul>
                <p>&nbsp;</p>
                <p><span style="font-weight: 400;">Informamos que o Pretium armazena os dados coletados em servidores em nuvem, que s&atilde;o administrados por terceiros e que podem ser armazenados fora do territ&oacute;rio nacional. Nestas hip&oacute;teses, os sistemas dever&atilde;o, obrigatoriamente, atender os padr&otilde;es de seguran&ccedil;a estabelecidos pela OneStop, de acordo com a LGPD.&nbsp;</span>
                </p>
                <p><span style="font-weight: 400;">Conservamos os seus dados pessoais enquanto for necess&aacute;rio para prestar os Servi&ccedil;os e para fins comerciais leg&iacute;timos e essenciais, tais como para aperfei&ccedil;oar nossos Servi&ccedil;os, tomar decis&otilde;es empresariais acerca de funcionalidades e ofertas com base em dados, cumprir as nossas obriga&ccedil;&otilde;es legais, e resolver disputas.</span>
                </p>
                <p><span style="font-weight: 400;">Se voc&ecirc; solicitar, vamos eliminar ou anonimizar os seus dados pessoais de modo que n&atilde;o o identifiquem, exceto se for legalmente permitido ou obrigat&oacute;rio manter determinados dados pessoais, incluindo situa&ccedil;&otilde;es como as seguintes:</span>
                </p>
                <ul>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">Se formos obrigados a manter os dados pessoais para as nossas obriga&ccedil;&otilde;es jur&iacute;dicas, fiscais, de auditoria e contabilidade, iremos reter os dados pessoais necess&aacute;rios pelo per&iacute;odo exigido pela legisla&ccedil;&atilde;o aplic&aacute;vel;</span>
                    </li>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">Sempre que necess&aacute;rio para nossa defesa judicial ou extrajudicial, para os nossos leg&iacute;timos interesses comerciais, como a preven&ccedil;&atilde;o contra fraudes, ou para manter a seguran&ccedil;a dos nossos Usu&aacute;rios.</span>
                    </li>
                </ul>
                <p>&nbsp;</p>
                <ul>
                    <li>
                        <h4><strong><strong>Diversos</strong></strong></h4>
                    </li>
                </ul>
                <p>&nbsp;</p>
                <p><strong>7.1 Altera&ccedil;&otilde;es a esta Pol&iacute;tica.&nbsp;</strong></p>
                <p><span style="font-weight: 400;">Podemos alterar esta Pol&iacute;tica a qualquer momento, indicando a data da &uacute;ltima altera&ccedil;&atilde;o. Quando realizarmos altera&ccedil;&otilde;es materiais &agrave; presente Pol&iacute;tica, faremos uma comunica&ccedil;&atilde;o vis&iacute;vel e adequada de acordo com as circunst&acirc;ncias, como por exemplo, apresentando uma comunica&ccedil;&atilde;o vis&iacute;vel em nosso site ou atrav&eacute;s do envio de um e-mail para os usu&aacute;rios.</span>
                </p>
                <p>&nbsp;</p>
                <p><strong>7.2 Contato.&nbsp;</strong></p>
                <p><span style="font-weight: 400;">Se voc&ecirc; tiver qualquer d&uacute;vida / coment&aacute;rio sobre esta Pol&iacute;tica, por favor entre em contato conosco pelo e-mail contato@onestopsolucoes.com.br </span>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button id="aceitar" type="submit" form="formCadastro" type="button" class="btn btn-primary">Aceito os
                    termos
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Optional JavaScript; choose one of the two! -->

<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="<?= asset("js/bootstrap.bundle.min.js"); ?>"></script>
<script src="<?= asset("js/jquery-3.6.0.min.js"); ?>"></script>
<script src="<?= url("vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"); ?>"></script>
<script src="<?= asset("js/form.js"); ?>"></script>

<script>
    $(document).ready(function () {

        $('#phonenumber').mask('(00) 00000-0000');
        $('#cnpj').mask('00.000.000/0000-00', {reverse: true});
        $(".cadastrar").click(function () {
            $('#modalPolitica').modal('show')
        });
        $("#aceitar").click(function () {
            $('#modalPolitica').modal('hide')

        });
    });
</script>

<!-- Option 2: Separate Popper and Bootstrap JS -->
<!--
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
-->
</body>
</html>