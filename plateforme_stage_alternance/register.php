<?php
require_once __DIR__.'/includes/layout.php';
if (user()) redirect(user()['role'].'/dashboard.php');
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf(); $name=trim($_POST['name']??'');$email=strtolower(trim($_POST['email']??''));$password=(string)($_POST['password']??'');$role=$_POST['role']??'';
 if (mb_strlen($name)<2 || mb_strlen($name)>120 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<10 || !in_array($role,['student','recruiter'],true)) $error='Vérifiez les champs (mot de passe : au moins 10 caractères).';
 else {try {
  db()->beginTransaction();
  $q=db()->prepare('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)');$q->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),$role]);$id=(int)db()->lastInsertId();
  if ($role==='student') { $q=db()->prepare('INSERT INTO student_profiles(user_id) VALUES(?)');$q->execute([$id]); }
  else {$q=db()->prepare('INSERT INTO recruiter_profiles(user_id) VALUES(?)');$q->execute([$id]);}
  log_action(['id'=>$id,'email'=>$email],'registration','user',$id,$role);
  db()->commit();flash('Compte créé. Vous pouvez vous connecter.');redirect('login.php');
 } catch (PDOException $e) {if (db()->inTransaction()) db()->rollBack();$error='Impossible de créer le compte. Adresse e-mail peut-être déjà utilisée.';}}
}
page_header('Créer un compte'); echo '<div class="auth-heading"><span class="eyebrow">REJOIGNEZ LA COMMUNAUTÉ</span><p>Créez votre espace en quelques instants.</p></div>'; if($error) echo '<div class="error">'.h($error).'</div>';
?>
<form class="panel narrow auth-panel" method="post"><?=csrf_field()?><label>Nom complet</label><input required maxlength="120" name="name" value="<?=h($_POST['name']??'')?>"><label>Adresse e-mail</label><input type="email" required name="email" value="<?=h($_POST['email']??'')?>"><label>Mot de passe (10 caractères minimum)</label><input type="password" name="password" minlength="10" required autocomplete="new-password"><label>Type de compte</label><select name="role" required><option value="student">Étudiant</option><option value="recruiter" <?=($_POST['role']??'')==='recruiter'?'selected':''?>>Recruteur</option></select><div class="actions"><button class="btn btn-wide">Créer mon compte <span>↗</span></button><a href="<?=h(url('login.php'))?>">Déjà inscrit ?</a></div></form>
<?php page_footer(); ?>