<?php
require_once __DIR__.'/../includes/bootstrap.php';$u=require_login('recruiter');
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Méthode non autorisée');}verify_csrf();
$id=(int)($_POST['application_id']??0);$decision=$_POST['decision']??'';
if(!in_array($decision,['interview','rejected'],true)){http_response_code(400);exit('Décision invalide');}
try {
 db()->beginTransaction();
 $q=db()->prepare('SELECT a.id,a.student_id,a.status,o.title FROM applications a JOIN offers o ON o.id=a.offer_id WHERE a.id=? AND o.recruiter_id=? FOR UPDATE');$q->execute([$id,$u['id']]);$a=$q->fetch();
 if(!$a||$a['status']!=='pending'){db()->rollBack();flash('Décision non autorisée ou déjà effectuée.');redirect('recruiter/dashboard.php');}
 db()->prepare('UPDATE applications SET status=? WHERE id=?')->execute([$decision,$id]);
 $message=$decision==='interview'?'Votre candidature pour « '.$a['title'].' » a été retenue pour passer un entretien.':'Votre candidature pour « '.$a['title'].' » n’a pas été retenue.';
 notify((int)$a['student_id'],$message);log_action($u,'application_'.$decision,'application',$id);db()->commit();
 flash('Décision enregistrée et notification envoyée.');redirect('recruiter/dashboard.php');
} catch(Throwable $e){if(db()->inTransaction())db()->rollBack();http_response_code(500);exit('Une erreur est survenue.');}
