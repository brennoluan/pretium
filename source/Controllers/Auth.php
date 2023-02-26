<?php

namespace Source\Controllers;

use Source\Models\Provider;
use Source\Models\RecoveryPassword;
use Source\Models\User;
use Source\Support\Email;

class Auth extends Controller
{
    public function __construct($router)
    {
        parent::__construct($router);
    }

    public function login(array $data)
    {
        $email = filter_var($data["email"], FILTER_DEFAULT);
        $passwd = filter_var($data["passwd"], FILTER_DEFAULT);

        if (!$email || !$passwd) {
            echo $this->ajaxResponse("message", [
                "type" => "warning",
                "message" => "Informe login e senha para entrar",
            ]);
            return;
        }

        $user = (new User())->find("email = :email",
            ":email={$data["email"]}")->fetch();

        if (!$user) {
            echo $this->ajaxResponse("message", [
                "type" => "danger",
                "message" => "Usuário não cadastrado",
            ]);
            return;
        }
        if (!password_verify($passwd, $user->password)) {
            echo $this->ajaxResponse("message", [
                "type" => "danger",
                "message" => "Login ou senha incorreta",
            ]);
            return;
        }

        $_SESSION["user"] = $user->id;

        echo $this->ajaxResponse("redirect", ["url" => $this->router->route("app.home")]);
    }

    public function register(array $data)
    {
        $nomeCompleto = filter_var($data["fullName"], FILTER_DEFAULT);
        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
        $emailBusiness = filter_var($data["businessEmail"], FILTER_VALIDATE_EMAIL);
        $telefone = filter_var($data["phoneNumber"], FILTER_DEFAULT);
        $socialReason = filter_var($data["socialReason"], FILTER_DEFAULT);
        $cnpj = filter_var($data["cnpj"], FILTER_DEFAULT);
        $address = filter_var($data["address"], FILTER_DEFAULT);
        $cep = filter_var($data["cep"], FILTER_DEFAULT);
        $uf = filter_var($data["uf"], FILTER_DEFAULT);
        $city = filter_var($data["city"], FILTER_DEFAULT);
        $passwd = filter_var($data["passwd"], FILTER_DEFAULT);
        $confirmPasswd = filter_var($data["confirmPasswd"], FILTER_DEFAULT);

        if (!$nomeCompleto && !$email && !$emailBusiness && !$telefone && !$socialReason && !$cnpj && !$address && !$cep && !$uf && !$city && !$passwd && !$confirmPasswd) {
            echo $this->ajaxResponse("message", [
                "type" => "warning",
                "message" => "Preencha todos os campos",
            ]);
            return;
        }

        $user = (new User())->find("email = :email",
            ":email={$email}")->fetch();

        if ($user) {
            echo $this->ajaxResponse("message", [
                "type" => "danger",
                "message" => "Esse endereço de e-mail já está em uso",
            ]);
            return;
        }

        if ($passwd != $confirmPasswd) {
            echo $this->ajaxResponse("message", [
                "type" => "danger",
                "message" => "As senhas digitadas não coincidem",
            ]);
            return;
        }

        $provider = new Provider();
        $provider->social_reason = $socialReason;
        $provider->cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        $provider->address = $address;
        $provider->cep = $cep;
        $provider->city = $city;
        $provider->uf = $uf;
        $provider->email = $emailBusiness;
        $provider->phonenumber = preg_replace('/[^0-9]/', '', $telefone);
        $provider->save();

        $user = new User();
        $user->username = $nomeCompleto;
        $user->email = $email;
        $user->password = password_hash($passwd, PASSWORD_DEFAULT);
        $user->provider_id = $provider->id;
        $user->save();

        $_SESSION["user"] = $user->id;

        echo $this->ajaxResponse("redirect", ["url" => $this->router->route("app.home")]);
    }

    public function forgotpassword(array $data)
    {
        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);

        $user = (new User())->find("email=:email", "email={$email}", "id, username")->fetch();
        if(!$user){
            echo $this->ajaxResponse("message", [
                "type" => "warning",
                "message" => "Não existe nenhuma conta vinculada com este e-mail",
            ]);
            return;
        }

        $recoverys = (new RecoveryPassword())->find("email=:email", "email={$email}")->fetch(true);
        foreach ($recoverys as $recovery) {
            $recovery->destroy();
        }

        $recovery = (new RecoveryPassword());
        $recovery->email = $email;
        $recovery->code = random_int(10000000, 99999999);
        $recovery->ip = getClientIP();
        $recovery->expire_time = time()+60*10;
        $recovery->save();

        $firstName = explode(" ", $user->username);

        $html = '<div style="height: 120px; background-image: url(https://onestopsolucoes.com.br/email/images/bg-texture.png); background-repeat: repeat; background-color: #3e5ca2;">
            <img height=80 src="https://onestopsolucoes.com.br/email/images/logo-pretium.png">
        </div>
        <div style="text-align: center; margin-bottom: 23px">
            <h2><strong>Esqueceu sua senha?</strong></h2>
            <span style="display: block;">Olá, ' . $firstName[0] . '</span>
            Não consegue acessar sua conta no Pretium e precisa redefinir sua senha? <br>Você só precisa usar o código de segurança abaixo para confirmar esta solicitação :)
            <p><strong>Seu código é: ' . $recovery->code .  '</strong></p>
            <span style="display: block; margin-bottom: 15px;">Anotou? Então clique no botão abaixo:</span>
            <a href="' . $this->router->route("web.forgotpassword2") . '" style="border-radius: 10px; border: 2px solid #3e5ca2; color: #3e5ca2; padding: 10px; text-decoration: none;">Gerar nova senha</a>
        </div>
        <div style="height: 120px; padding: 1px 0px 0px 10px; color: #fff; background-image: url(https://onestopsolucoes.com.br/email/images/bg-texture.png); background-repeat: repeat; background-color: #3e5ca2;">
            <p><strong>IMPORTANTE</strong></p>
            <span>Caso não tenha efetuado esta solicitação basta ignorar este e-mail e nenhuma alteração será feita em sua conta</span>
        </div>';
        
        /*$html = "Não consegue acessar sua conta no Pretium e precisa redefinir sua senha? <br>Você só precisa usar o código de segurança abaixo para confirmar esta solicitação :)
        <br><br>
        Seu código é: <strong>$recovery->code</strong><br><br>
        Anotou? Então <a href='{$this->router->route("web.forgotpassword2")}'>acesse este link</a> e informe seu código e sua nova senha!<br><br>
        IMPORANTE: Caso não tenha efetuado esta solicitação basta ignorar este e-mail e nenhuma alteração será feita em sua conta.";
        */

        $mail = new Email();
        $mail->add("Recupere sua senha {$firstName[0]}", $html, $user->username, $email)->send();


        echo $this->ajaxResponse("redirect", ["url" => $this->router->route("web.forgotpassword2")]);
    }

    public function forgotpassword2(array $data)
    {
        $code = filter_var($data["code"], FILTER_DEFAULT);
        $newpassword = filter_var($data["newpassword"], FILTER_DEFAULT);
        $confirmnewpassword = filter_var($data["confirmnewpassword"], FILTER_DEFAULT);

        $recovery = (new RecoveryPassword())->find("code=:code", "code={$code}")->fetch();

        if(!$recovery || time() > $recovery->expire_time || $recovery->ip != getClientIP()){
            echo $this->ajaxResponse("message", [
                "type" => "warning",
                "message" => "Talvez você já tenha alterado sua senha ou este código de segurança expirou ou não existe!",
            ]);
            return;
        }

        if($newpassword != $confirmnewpassword){
            echo $this->ajaxResponse("message", [
                "type" => "danger",
                "message" => "As senhas digitadas não coincidem",
            ]);
            return;
        }

        $user = (new User())->find("email=:email", "email={$recovery->email}")->fetch();
        $user->password = password_hash($newpassword, PASSWORD_DEFAULT);
        $user->save();

        $recovery->destroy();

        $_SESSION["user"] = $user->id;

        echo $this->ajaxResponse("redirect", ["url" => $this->router->route("app.home")]);
    }
}