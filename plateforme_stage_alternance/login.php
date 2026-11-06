<?php
require_once __DIR__.'/includes/layout.php';
if (user()) redirect(user()['role'].'/dashboard.php');
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf();$email=strtolower(trim($_POST['email']??''));$pass=(string)($_POST['password']??'');
 $q=db()->prepare('SELECT id,name,email,password_hash,role,active FROM users WHERE email=?');$q->execute([$email]);$u=$q->fetch();
 if (!$u || !$u['active'] || !password_verify($pass,$u['password_hash'])) {$error='Identifiants incorrects ou compte désactivé.';}
 else {session_regenerate_id(true);$_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role'],'active'=>$u['active']];log_action($_SESSION['user'],'login','user',(int)$u['id']);redirect($u['role'].'/dashboard.php');}
}
page_header('Connexion');echo '<div class="auth-heading"><span class="eyebrow">HEUREUX DE VOUS REVOIR</span><p>Connectez-vous pour retrouver vos opportunités et vos candidatures.</p></div>';if($error)echo '<div class="error">'.h($error).'</div>';
?>
<form class="panel narrow auth-panel" method="post"><?=csrf_field()?><label>Adresse e-mail</label><input name="email" type="email" required autocomplete="email"><label>Mot de passe</label><input type="password" name="password" required autocomplete="current-password"><div class="actions"><button class="btn btn-wide">Se connecter <span>↗</span></button><a href="<?=h(url('register.php'))?>">Créer un compte</a></div></form>
<?php page_footer(); ?>