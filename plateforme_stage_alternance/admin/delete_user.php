<?php
require_once __DIR__.'/../includes/bootstrap.php';$u=require_login('admin');
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Méthode non autorisée');}verify_csrf();$id=(int)($_POST['id']??0);
$q=db()->prepare("SELECT id,role,email FROM users WHERE id=? AND role IN ('student','recruiter')");$q->execute([$id]);$target=$q->fetch();
if(!$target){http_response_code(403);exit('Suppression non autorisée');}
$files=[];
if($target['role']==='student'){$q=db()->prepare('SELECT cv_path FROM student_profiles WHERE user_id=?');$q->execute([$id]);$files=array_filter([$q->fetchColumn()]);}
else {$q=db()->prepare('SELECT p.cv_path FROM student_profiles p JOIN applications a ON a.student_id=p.user_id JOIN offers o ON o.id=a.offer_id WHERE o.recruiter_id=?');$q->execute([$id]);/* CV partagés entre offres : ne pas effacer ici. */}
try {db()->beginTransaction();log_action($u,'delete_user','user',$id,'Compte : '.$target['email']);db()->prepare('DELETE FROM users WHERE id=?')->execute([$id]);db()->commit();foreach($files as $path)if(preg_match('~^uploads/cv/[a-f0-9]{40}\.pdf$~',$path)&&is_file(__DIR__.'/../'.$path))unlink(__DIR__.'/../'.$path);flash('Utilisateur supprimé.');redirect('admin/users.php');}catch(Throwable $e){if(db()->inTransaction())db()->rollBack();http_response_code(500);exit('Erreur de suppression.');}
