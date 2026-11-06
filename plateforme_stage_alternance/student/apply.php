<?php
require_once __DIR__.'/../includes/layout.php';$u=require_login('student');$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);if(!$id){http_response_code(404);exit('Offre inconnue');}
$q=db()->prepare("SELECT o.*,r.company_name FROM offers o JOIN recruiter_profiles r ON r.user_id=o.recruiter_id WHERE o.id=? AND o.status='open'");$q->execute([$id]);$o=$q->fetch();if(!$o){http_response_code(404);exit('Offre indisponible');}
$error='';if($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf();$letter=trim((string)($_POST['cover_letter']??''));
 if(mb_strlen($letter)>5000)$error='Message trop long.';
 else {try {$q=db()->prepare('INSERT INTO applications(offer_id,student_id,cover_letter) VALUES(?,?,?)');$q->execute([$id,$u['id'],$letter]);$app=(int)db()->lastInsertId();notify((int)$o['recruiter_id'],'Nouvelle candidature pour : '.$o['title'],'recruiter/dashboard.php');log_action($u,'apply','application',$app,'Offre #'.$id);flash('Votre candidature a été envoyée.');redirect('student/applications.php');}catch(PDOException $e){$error='Vous avez déjà postulé ou la candidature est indisponible.';}}
}
page_header($o['title']);if($error)echo '<div class="error">'.h($error).'</div>';
?><div class="panel"><p><b><?=h($o['company_name'])?></b> · <?=h($o['city'])?> · <?=h($o['type'])?></p><h2>Description</h2><p><?=nl2br(h($o['description']))?></p><h2>Profil recherché</h2><p><?=nl2br(h($o['requirements']))?></p></div><form class="panel" method="post"><?=csrf_field()?><label>Motivation (facultatif)</label><textarea name="cover_letter" maxlength="5000"></textarea><button>Envoyer ma candidature</button></form><?php page_footer();?>