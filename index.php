<?php

ob_flush();
session_start();

require __DIR__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;
$router = new Router(ROOT);

$router->namespace("Source\Controllers");

/*
 * GROUP NULL
 */
$router->group(null);
$router->get("/", "Web:home", "web.home");
$router->get("/entrar", "Web:login", "web.login");
$router->get("/cadastro", "Web:register", "web.register");
$router->get("/recuperarsenha", "Web:forgotpassword", "web.forgotpassword");
$router->get("/recuperarsenha/senha", "Web:forgotpassword2", "web.forgotpassword2");

/**
 * GROUP AUTH
 */
$router->group(null);
$router->post("/entrar", "Auth:login", "auth.login");
$router->post("/cadastro", "Auth:register", "auth.register");
$router->post("/recuperarsenha", "Auth:forgotpassword", "auth.forgotpassword");
$router->post("/recuperarsenha/senha", "Auth:forgotpassword2", "auth.forgotpassword2");

/**
 * GROUP APP
 */
$router->group("app");
$router->get("/", "App:home", "app.home");
$router->get("/confirmaremail", "App:confirmemail", "app.confirmemail");
$router->get("/fornecedores", "App:providers", "app.providers");
$router->get("/produtos", "App:products", "app.products");
$router->post("/importarprodutos", "App:importProducts", "app.importproducts");
$router->post("/produtos", "App:product", "app.product");
$router->get("/cotacoesrecebidas", "App:quotesReceiveds", "app.quotesreceived");
$router->post("/cotacoesrecebidas", "App:quotesReceived", "app.quotesreceiveds");
$router->get("/minhascotacoes", "App:myQuotes", "app.myquotes");
$router->post("/minhascotacoes", "App:newQuote", "app.newQuote");
$router->get("/notificacoes/{id}", "App:notification", "app.notification");

$router->get("/editarperfil", "App:profileEdit", "app.profile.edit");
$router->post("/editarperfil", "App:profileEdit", "app.profile.edit");
$router->post("/alterarsenha", "App:changePassword", "app.changepassword");

$router->group("relatorios");
$router->get("/precos", "App:reportPrices", "app.report.price");

$router->group(null);
$router->get("/logout", "App:logout", "app.logout");

/*
 * GROUP ERROR
 */
$router->group("error");
$router->get("/{errcode}", "Web:error", "web.error");

/*
 * ROUTE PROCESS
 */
$router->dispatch();

if($router->error()){
    $router->redirect("web.error", ["errcode" => $router->error()]);
}

ob_end_flush();