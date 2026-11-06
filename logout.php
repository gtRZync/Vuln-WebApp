<?php
require_once __DIR__.'/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD']!=='POST') {http_response_code(405);exit('Méthode non autorisée');}
verify_csrf();if(user())log_action(user(),'logout','user',(int)user()['id']);
$_SESSION=[];session_regenerate_id(true);flash('Vous êtes déconnecté.');redirect('login.php');
