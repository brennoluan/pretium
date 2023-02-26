<?php

namespace Source\Controllers;

use React\Promise\Deferred;
use Source\Models\Notification;
use Source\Models\Products;
use Source\Models\Provider;
use Source\Models\QuoteProduct;
use Source\Models\Quotes;
use Source\Models\RecoveryPassword;
use Source\Models\User;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use Source\Support\Email;

class App extends Controller
{

    public function __construct($router)
    {
        parent::__construct($router);


        if (empty($_SESSION["user"]) || !$this->user = (new User())->findById($_SESSION["user"])) {
            unset($_SESSION["user"]);
            flash("alert-danger", "Acesso negado. Faça o login primeiro.");
            $this->router->redirect("web.login");
        }

        if($_SESSION["user"] && !$this->user->email_confirmed && (strpos($_SERVER['REQUEST_URI'],'confirmaremail') == false) && $_SERVER["REQUEST_URI"] != '/pretium/logout'){

            $recovery = (new RecoveryPassword())->find("email=:email AND length(code) > 20", "email={$this->user->email}")->fetch();

            if(!$recovery) {
                $recovery = (new RecoveryPassword());
                $recovery->email = $this->user->email;
                $recovery->code = md5(uniqid(rand(), true));
                $recovery->ip = getClientIP();
                $recovery->expire_time = 0;
                $recovery->save();

                $firstName = explode(" ", $this->user->username);

                $link = $this->router->route("app.confirmemail") . "?c={$recovery->code}";

                $html = '<div style="height: 120px; background-image: url(https://onestopsolucoes.com.br/email/images/bg-texture.png); background-repeat: repeat; background-color: #3e5ca2;">
                    <img height=80 src="https://onestopsolucoes.com.br/email/images/logo-pretium.png">
                </div>
                <div style="text-align: center; margin-bottom: 23px">
                    <h2><strong>Confirme seu e-mail</strong></h2>
                    <span style="display: block;">Olá, ' . $firstName[0] . '</span>
                    Confirme seu e-mail agora para termos certeza de que ele pertence a você.<br>
                    Assim que fizer isso, você estará pronto para usar o Pretium.<br><br>
                    Se você não conseguir clicar no botão abaixo para confirmar seu e-mail, siga este link<br>
                    <a href=" ' . $link . '">' . $link . '</a><br><br>
                    <a href="' . $link . '" style="border-radius: 10px; border: 2px solid #3e5ca2; color: #3e5ca2; padding: 15px; text-decoration: none;">Confirmar e-mail</a>
                </div>';

                $mail = new Email();
                $mail->add("Confirme seu e-mail {$firstName[0]}", $html, $this->user->username, $this->user->email)->send();
            }

            $this->router->redirect("app.confirmemail");
        }
    }

    public function home(): void
    {
        $user = (new User())->findById($_SESSION["user"]);
        $date = date("Y-m-d");
        echo $this->view->render("dashboard", [
            "title" => "Dashboard | " . SITENAME,
            "router" => $this->router,
            "user" => $user,
            "quotesFinish" => (new Quotes())->find("status_id=3 AND (user_id = :user_id OR provider_id = :provider_id) AND closure_at >= $date",
                "user_id=$user->id&provider_id=$user->provider_id")->count(),
            "quotesOpened" => (new Quotes())->find("(status_id=1 OR status_id=5) AND (user_id = :user_id OR provider_id = :provider_id) AND closure_at >= $date",
                "user_id=$user->id&provider_id=$user->provider_id")->count(),
            "quotesRecused" => (new Quotes())->find("status_id=4 AND (user_id = :user_id OR provider_id = :provider_id) AND closure_at >= $date",
                "user_id=$user->id&provider_id=$user->provider_id")->count(),
            "quotesWaintingApproval" => (new Quotes())->find("status_id=6 AND (user_id = :user_id OR provider_id = :provider_id) AND closure_at >= $date",
                "user_id=$user->id&provider_id=$user->provider_id")->count(),
        ]);
    }

    public function providers(): void
    {
        $fornecedores = (new Provider())->find()->fetch(true);
        echo $this->view->render("providers", [
            "title" => "Fornecedores | " . SITENAME,
            "router" => $this->router,
            "fornecedores" => $fornecedores,

        ]);
    }

    public function confirmemail(): void
    {
        $user = (new User())->find("id = :id", "id={$_SESSION["user"]}")->fetch();

        if($user->email_confirmed){
            $this->router->redirect("app.home");
            return;
        }

        if(isset($_GET["c"])){
            $recovery = (new RecoveryPassword())->find("code=:code", "code={$_GET["c"]}")->fetch();

            if($recovery){

                $user->email_confirmed = 1;
                $user->save();
                $recovery->destroy();
            } else {
                $this->router->redirect("app.home");
            }

            echo $this->view->render("confirmemail_success", [
                "title" => "Obrigado | " . SITENAME,
                "router" => $this->router,
            ]);
        } else {
            echo $this->view->render("confirmemail", [
                "title" => "Confirme seu email | " . SITENAME,
                "router" => $this->router,
            ]);
        }
    }

    public function products(): void
    {
        $user = (new User())->find("id = :id", "id={$_SESSION["user"]}", "provider_id")->fetch();
        $products = (new Products())->find("provider_id = :id", "id={$user->provider_id}")->fetch(true);

        echo $this->view->render("products", [
            "title" => "Produtos | " . SITENAME,
            "router" => $this->router,
            "products" => $products,
            "provider_id" => $user->provider_id,
        ]);
    }

    public function importProducts(array $data)
    {
        $file_parts = pathinfo($_FILES["file"]["name"]);
        if($file_parts['extension'] != "csv"){
            echo json_encode([
                "icon" => "error",
                "title" => "Produtos não importado",
                "message" => "Tipo de extensão de arquivo não aceitada",
            ]);
            return;
        }

        $user = (new User())->find("id = :id", "id={$_SESSION["user"]}", "provider_id")->fetch();

        $file = __DIR__ . "/../../uploads/" . strtotime("now") . $_FILES["file"]["name"];
        move_uploaded_file($_FILES["file"]["tmp_name"], $file);

        $reader = Reader::createFromPath($file, 'r');
        $reader->setOutputBOM(Writer::BOM_UTF8);
        $reader->setDelimiter(';');
        $reader->setHeaderOffset(0);

        foreach ($reader->getRecords() as $records) {
            if (!is_numeric($records["Unidade de medida"]) || $records["Unidade de medida"] < 0 || $records["Unidade de medida"] > 3) {
                echo json_encode([
                    "icon" => "error",
                    "title" => "Produtos não importado",
                    "message" => "Não foi possivel importar os produtos devido aos códigos de unidade de medida conterem letras ou não existirem.",
                ]);
                return;
            }
            if (!is_numeric($records["Código"])) {
                echo json_encode([
                    "icon" => "error",
                    "title" => "Produtos não importado",
                    "message" => "Não foi possivel importar os produtos devido há alguns código do produtos conterem letras.",
                ]);
                return;
            }
        }

        foreach ($reader->getRecords() as $records) {
            $product = (new Products());
            $product->product_name = $records["Descrição"];
            $product->brand = $records["Marca"];
            $product->code = $records["Código"];
            $product->provider_id = $user->provider_id;
            $product->measurement_units_id = $records["Unidade de medida"];
            $product->save();
        }

        echo json_encode(["icon" => "success", "title" => "", "message" => "Produtos importado com sucesso!"]);
    }

    public function product(array $data): void
    {
        $user = (new User())->find("id = :id", "id={$_SESSION["user"]}", "provider_id")->fetch();
        $product = (new Products())->findById((empty($data["id"]) ? 0 : $data["id"]));

        if (!$product) {
            $product = new Products();
        }

        $product->product_name = filter_var($data["description"], FILTER_DEFAULT);
        $product->brand = filter_var($data["brand"], FILTER_DEFAULT);
        $product->code = filter_var($data["code"], FILTER_DEFAULT);
        $product->provider_id = $user->provider_id;
        $product->measurement_units_id = filter_var($data["measurementUnit"], FILTER_DEFAULT);
        $product->save();
        $this->router->redirect("app.products");
    }

    public function quotesReceiveds(): void
    {
        $user = (new User())->find("id = :id", "id={$_SESSION["user"]}", "provider_id")->fetch();
        $quotes = (new Quotes())->find("provider_id = :provider_id", "provider_id={$user->provider_id}")->fetch(true);

        echo $this->view->render("quotesreceived", [
            "title" => "Cotações recebidas | " . SITENAME,
            "router" => $this->router,
            "quotes" => $quotes,
            "provider_id" => $user->provider_id,
        ]);
    }

    public function quotesReceived(array $data)
    {
        // var_dump($data);
        $quote = (new Quotes())->findById($data['quoteid']);

        $getProviderId = $quote->user_id;
        $dest = (new User())->findByProvider($getProviderId);
        $firstName = explode(" ", $dest->username);

        if ($data['action'] == 'yes') {
            if ($quote->status_id == 6) {
                $quote->status_id = 3;
                $getProviderId = $quote->provider_id;
                $message = "Sua precificação da cotação (ID: $quote->id) foi aprovada";
                $class = 'text-success';

                $subject = "Cotação Aprovada";

                $dest2 = (new User())->findByProvider($getProviderId);
                $firstName2 = explode(" ", $dest2->username);

                $html = '<div style="height: 120px; background-image: url(https://onestopsolucoes.com.br/email/images/bg-texture.png); background-repeat: repeat; background-color: #3e5ca2;">
                <img height=80 src="https://onestopsolucoes.com.br/email/images/logo-pretium.png">
                </div>
                <div style="text-align: center; margin-bottom: 23px">
                    <h2><strong>Cotação Aprovada</strong></h2>
                    <span style="display: block;">Olá, ' . $firstName2[0] . '</span>
                    A cotação (ID: ' . $quote->id .  ') enviada enviada pela empresa <strong>' . $quote->user()->provider()->social_reason . '</strong> foi aprovada.<br><br>
                    Contatos para prosseguir com a compra dos produtos:<br>
                    E-mail:<strong>' . $quote->user()->provider()->email . '</strong><br>
                    Telefone:<strong>' . $quote->user()->provider()->phonenumber . '</strong><br>
                    <br><br>
                    <a href="' . $this->router->route("app.home") . '" style="border-radius: 10px; border: 2px solid #3e5ca2; color: #3e5ca2; padding: 10px; text-decoration: none;">Acessar o Pretium</a>
                </div>';

                $mail = new Email();
                $mail->add($subject, $html, $dest2->username, $dest2->email)->send();

                $html = '<div style="height: 120px; background-image: url(https://onestopsolucoes.com.br/email/images/bg-texture.png); background-repeat: repeat; background-color: #3e5ca2;">
                <img height=80 src="https://onestopsolucoes.com.br/email/images/logo-pretium.png">
                </div>
                <div style="text-align: center; margin-bottom: 23px">
                    <h2><strong>Cotação Aprovada</strong></h2>
                    <span style="display: block;">Olá, ' . $firstName[0] . '</span>
                    A sua solicitação de cotação (ID: ' . $quote->id .  ') enviada para a empresa <strong>' . $quote->provider()->social_reason . '</strong> foi aprovada.<br><br>
                    Contatos para prosseguir com a compra dos produtos:<br>
                    E-mail:<strong>' . $quote->provider()->email . '</strong><br>
                    Telefone:<strong>' . $quote->provider()->phonenumber . '</strong><br>
                    <br><br>
                    <a href="' . $this->router->route("app.home") . '" style="border-radius: 10px; border: 2px solid #3e5ca2; color: #3e5ca2; padding: 10px; text-decoration: none;">Acessar o Pretium</a>
                </div>';
            } else {
                if ($quote->status_id == 5) {
                    $quote->status_id = 6;
                    for ($i = 0; $i < count($data['product']); $i++) {
                        $product = (new QuoteProduct())->find('product_id=:product_id AND quote_id=:quote_id',
                            "product_id={$data['product'][$i][0]}&quote_id={$data['quoteid']}")->fetch();
                        $product->price = $data['product'][$i][1];
                        $product->subtotal = ($product->price * $product->qtd);
                        $product->save();
                    }
                    $message = "Sua cotação (ID: $quote->id) está aguardando a aprovação do preço";
                    $class = 'text-secondary';

                    $subject = "Sua cotação está aguardando a sua aprovação de preço";
                    $html = '<div style="height: 120px; background-image: url(https://onestopsolucoes.com.br/email/images/bg-texture.png); background-repeat: repeat; background-color: #3e5ca2;">
                    <img height=80 src="https://onestopsolucoes.com.br/email/images/logo-pretium.png">
                    </div>
                    <div style="text-align: center; margin-bottom: 23px">
                    <h2><strong>Sua cotação está aguardando a sua aprovação de preço</strong></h2>
                    <span style="display: block;">Olá, ' . $firstName[0] . '</span>
                    A sua cotação (ID: ' . $quote->id .  ') está aguardando a sua aprovação de preço.<br>Que você solicitou para a empresa <strong>' . $quote->provider()->social_reason . '</strong><br>
                    <br><br>
                    <a href="' . $this->router->route("app.home") . '" style="border-radius: 10px; border: 2px solid #3e5ca2; color: #3e5ca2; padding: 10px; text-decoration: none;">Acessar o Pretium</a>
                    </div>';
                } else {
                    $quote->status_id = 5;
                    $message = "Sua solicitação da cotação (ID: $quote->id) foi aceita";
                    $class = 'text-info';

                    $subject = "Solicitação de cotação aceita";
                    $html = '<div style="height: 120px; background-image: url(https://onestopsolucoes.com.br/email/images/bg-texture.png); background-repeat: repeat; background-color: #3e5ca2;">
                    <img height=80 src="https://onestopsolucoes.com.br/email/images/logo-pretium.png">
                    </div>
                    <div style="text-align: center; margin-bottom: 23px">
                    <h2><strong>Solicitação de cotação aceita</strong></h2>
                    <span style="display: block;">Olá, ' . $firstName[0] . '</span>
                    A sua solicitação de cotação (ID: ' . $quote->id .  ') enviada para a empresa <strong>' . $quote->provider()->social_reason . '</strong> foi aceita.<br>
                    <br><br>
                    <a href="' . $this->router->route("app.home") . '" style="border-radius: 10px; border: 2px solid #3e5ca2; color: #3e5ca2; padding: 10px; text-decoration: none;">Acessar o Pretium</a>
                    </div>';
                }
            }

        } else {
            if ($quote->status_id == 6) {
                $quote->status_id = 4;
                $getProviderId = $quote->provider_id;
                $message = "Sua precificação da cotação (ID: $quote->id) foi recusada";
                $class = 'text-danger';

                $dest = (new User())->findByProvider($getProviderId);
                $firstName = explode(" ", $dest->username);

                $subject = "Precificação da cotação recusada";
                $html = '<div style="height: 120px; background-image: url(https://onestopsolucoes.com.br/email/images/bg-texture.png); background-repeat: repeat; background-color: #3e5ca2;">
                <img height=80 src="https://onestopsolucoes.com.br/email/images/logo-pretium.png">
                </div>
                <div style="text-align: center; margin-bottom: 23px">
                <h2><strong>Precificação da cotação recusada</strong></h2>
                <span style="display: block;">Olá, ' . $firstName[0] . '</span>
                A sua precifação da cotação (ID: ' . $quote->id .  ') enviada para a empresa <strong>' . $quote->provider()->social_reason . '</strong> foi recusada.<br>
                <br><br>
                <a href="' . $this->router->route("app.home") . '" style="border-radius: 10px; border: 2px solid #3e5ca2; color: #3e5ca2; padding: 10px; text-decoration: none;">Acessar o Pretium</a>
                </div>';
            } else {
                $quote->status_id = 4;
                $message = "Sua cotação (ID: $quote->id) foi recusada";
                $class = 'text-danger';

                $subject = "Solicitação de cotação recusada";
                $html = '<div style="height: 120px; background-image: url(https://onestopsolucoes.com.br/email/images/bg-texture.png); background-repeat: repeat; background-color: #3e5ca2;">
                <img height=80 src="https://onestopsolucoes.com.br/email/images/logo-pretium.png">
                </div>
                <div style="text-align: center; margin-bottom: 23px">
                <h2><strong>Solicitação de cotação recusada</strong></h2>
                <span style="display: block;">Olá, ' . $firstName[0] . '</span>
                A sua solicitação de cotação (ID: ' . $quote->id .  ') enviada para a empresa <strong>' . $quote->provider()->social_reason . '</strong> foi recusada.<br>
                <br><br>
                <a href="' . $this->router->route("app.home") . '" style="border-radius: 10px; border: 2px solid #3e5ca2; color: #3e5ca2; padding: 10px; text-decoration: none;">Acessar o Pretium</a>
                </div>';
            }
        }

        $quote->save();

        $notification = new Notification();
        $notification->user_id = $dest->id;
        $notification->message = $message;
        $notification->read_message = 0;
        $notification->save();

        $mail = new Email();
        $mail->add($subject, $html, $dest->username, $dest->email)->send();

        echo json_encode(["status" => $quote->status()->name, "class" => $class]);
    }

    public function myQuotes(): void
    {
        $user = (new User())->find("id = :id", "id={$_SESSION["user"]}", "provider_id, id")->fetch();
        $quotes = (new Quotes())->find("user_id = :user_id", "user_id={$_SESSION["user"]}")->fetch(true);

        echo $this->view->render("myquotes", [
            "title" => "Minhas cotações | " . SITENAME,
            "router" => $this->router,
            "quotes" => $quotes,
            "user_id" => $user->id,
            "provider_id" => $user->provider_id,
        ]);
    }

    public function newQuote(array $data): void
    {
        parse_str($data['data'], $output);
        unset($data['data']);
        $data['data'] = $output;

        $user = (new User())->find("id = :id", "id={$_SESSION["user"]}", "id, provider_id")->fetch();
        $provider = (new Provider())->find("social_reason=:name", "name={$data['data']['provider']}", "id")->fetch();

        $quote = (new Quotes());
        $quote->user_id = $user->id;
        $quote->provider_id = $provider->id;
        $quote->status_id = 1;
        $quote->closure_at = $data['data']['closure_at'];
        $quote->save();

        for ($i = 0; $i < count($data['product']); $i++) {

            $product = (new Products())->find("provider_id=:provider_id AND product_name=:product_name",
                "provider_id=$provider->id&product_name={$data['product'][$i][0]}",
                "id")->fetch();
            $quoteProduct = new QuoteProduct();
            $quoteProduct->quote_id = $quote->id;
            $quoteProduct->product_id = $product->id;
            $quoteProduct->qtd = $data['product'][$i][1];
            $quoteProduct->price = 0;
            $quoteProduct->subtotal = 0;
            $quoteProduct->save();
        }

        $dest = (new User())->findByProvider($provider->id);
        $notification = new Notification();
        $notification->user_id = $dest->id;
        $notification->message = "Nova solicitação de cotação (ID: $quote->id)";
        $notification->read_message = 0;
        $notification->save();

        $firstName = explode(" ", $dest->username);

        $providerUser = (new Provider())->find("id=:id", "id={$user->provider_id}")->fetch();

        $html = '<div style="height: 120px; background-image: url(https://onestopsolucoes.com.br/email/images/bg-texture.png); background-repeat: repeat; background-color: #3e5ca2;">
            <img height=80 src="https://onestopsolucoes.com.br/email/images/logo-pretium.png">
        </div>
        <div style="text-align: center; margin-bottom: 23px">
            <h2><strong>Nova solicitação de cotação</strong></h2>
            <span style="display: block;">Olá, ' . $firstName[0] . '</span>
            Você tem uma nova solicitação de cotação.<br>
            Enviada pela empresa: '. $providerUser->social_reason . '
            <br><br><br>
            <a href="' . $this->router->route("app.home") . '" style="border-radius: 10px; border: 2px solid #3e5ca2; color: #3e5ca2; padding: 10px; text-decoration: none;">Acessar o Pretium</a>
        </div>';

        $mail = new Email();
        $mail->add("Nova solicitação de cotação", $html, $dest->username, $dest->email)->send();

        echo json_encode([
            "id" => $quote->id,
            "status" => $quote->status()->name,
            "dest" => $quote->provider()->social_reason,
            "closure_at" => date("d/m/Y", strtotime($quote->closure_at)),
            "class" => STATUS_CLASS["{$quote->status()->name}"],
        ]);
    }

    public function reportPrices(): void
    {
        $quotes = (new Quotes())->find("status_id=6 and user_id={$_SESSION["user"]}")->fetch(true);
        echo $this->view->render("reportprices", [
            "title" => "Relatórios de Preços | " . SITENAME,
            "router" => $this->router,
            "quotes" => $quotes,
        ]);
    }

    public function notification(array $data): void
    {
        var_dump($data);
        //$notification = (new Notification())->findById($data['id']);
    }

    public function profileEdit(array $data)
    {
        $user = (new User())->find("id = :id", "id={$_SESSION["user"]}")->fetch();
        $provider = (new Provider())->find("id=:id", "id={$user->provider_id}")->fetch();
        if (!empty($data)) {
            $user->username = filter_var($data["username"], FILTER_DEFAULT);
            $user->save();

            $provider->social_reason = filter_var($data["razaosocial"], FILTER_DEFAULT);
            $provider->cnpj = preg_replace('/[^0-9]/', '', filter_var($data["cnpj"], FILTER_DEFAULT));
            $provider->email = filter_var($data["emailcorporativo"], FILTER_VALIDATE_EMAIL);
            $provider->phonenumber = preg_replace('/[^0-9]/', '', filter_var($data["phonenumber"], FILTER_DEFAULT));
            $provider->save();
            return;
        }


        echo $this->view->render("profileedit", [
            "title" => "Editar perfil | " . SITENAME,
            "router" => $this->router,
            "user" => $user,
            "provider" => $provider,
        ]);
    }

    public function changePassword(array $data)
    {
        $user = (new User())->find("id = :id", "id={$_SESSION["user"]}")->fetch();
        if (!password_verify(filter_var($data["senhaatual"], FILTER_DEFAULT), $user->password)) {
            echo json_encode(["icon" => "error", "title" => "Sua senha atual está incorreta"]);
            return;
        }

        $data["novasenha"] = filter_var($data["novasenha"], FILTER_DEFAULT);
        $data["confirmanovasenha"] = filter_var($data["confirmanovasenha"], FILTER_DEFAULT);
        if ($data["confirmanovasenha"] != $data["novasenha"]) {
            echo json_encode(["icon" => "error", "title" => "As senhas digitadas não coincidem"]);
            return;
        }

        $user->password = password_hash($data["novasenha"], PASSWORD_DEFAULT);
        $user->save();

        echo json_encode(["icon" => "success", "title" => "Senha alterada com sucesso"]);
    }

    public function logout(): void
    {
        unset($_SESSION["user"]);
        $this->router->redirect("web.login");
    }
}