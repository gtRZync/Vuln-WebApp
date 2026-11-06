<?php
require_once __DIR__.'/../includes/layout.php';$u=require_login('student');$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf();
 $fields=['university','program','level','city','skills','interests','bio'];$values=[];
 foreach($fields as $f) $values[$f]=trim((string)($_POST[$f]??''));
 $type=$_POST['search_type']??'both';if(!in_array($type,['stage','alternance','both'],true))$type='both';
 foreach($values as $k=>$v)if(mb_strlen($v)>($k==='bio'||$k==='skills'||$k==='interests'?3000:180))$error='Un champ est trop long.';
 $cvPath=null;
 if (!$error && isset($_FILES['cv']) && $_FILES['cv']['error']!==UPLOAD_ERR_NO_FILE) {
  $f=$_FILES['cv'];
  if($f['error']!==UPLOAD_ERR_OK || $f['size']>MAX_CV_BYTES || $f['size']<1)$error='CV invalide (PDF, 2 Mo maximum).';
  else {
   $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);$head=file_get_contents($f['tmp_name'],false,null,0,5);
   if($mime!=='application/pdf'||$head!=='%PDF-')$error='Seuls les fichiers PDF sont autorisés.';
   else {$cvPath='uploads/cv/'.bin2hex(random_bytes(20)).'.pdf';if(!move_uploaded_file($f['tmp_name'],__DIR__.'/../'.$cvPath))$error='Échec de l’enregistrement du CV.';}
  }
 }
 if (!$error) {
  $q=db()->prepare('SELECT cv_path FROM student_profiles WHERE user_id=?');$q->execute([$u['id']]);$old=$q->fetchColumn();
  $sql='UPDATE student_profiles SET university=?,program=?,level=?,city=?,skills=?,interests=?,bio=?,search_type=?'.($cvPath?',cv_path=?':'').' WHERE user_id=?';
  $args=array_values($values); // réordonner conformément à la requête
  $args=[$values['university'],$values['program'],$values['level'],$values['city'],$values['skills'],$values['interests'],$values['bio'],$type];
  if($cvPath)$args[]=$cvPath;$args[]=$u['id'];db()->prepare($sql)->execute($args);
  if($cvPath && $old && is_file(__DIR__.'/../'.$old))unlink(__DIR__.'/../'.$old);
  log_action($u,'update_student_profile','user',(int)$u['id']);flash('Profil mis à jour.');redirect('student/profile.php');
 }
}
$q=db()->prepare('SELECT * FROM student_profiles WHERE user_id=?');$q->execute([$u['id']]);$p=$q->fetch();page_header('Mon profil étudiant');if($error)echo '<div class="error">'.h($error).'</div>';
?>
<form class="panel narrow" method="post" enctype="multipart/form-data"><?=csrf_field()?>
<?php foreach(['university'=>'Université / école','program'=>'Formation','level'=>'Niveau d’études','city'=>'Ville'] as $f=>$l):?><label><?=h($l)?></label><input maxlength="180" name="<?=h($f)?>" value="<?=h($p[$f])?>"><?php endforeach;?>
<label>Compétences</label><textarea name="skills"><?=h($p['skills'])?></textarea><label>Centres d’intérêt</label><textarea name="interests"><?=h($p['interests'])?></textarea><label>Présentation / recherche</label><textarea name="bio"><?=h($p['bio'])?></textarea><label>Je cherche</label><select name="search_type"><option value="both" <?=$p['search_type']==='both'?'selected':''?>>Stage ou alternance</option><option value="stage" <?=$p['search_type']==='stage'?'selected':''?>>Stage</option><option value="alternance" <?=$p['search_type']==='alternance'?'selected':''?>>Alternance</option></select><label>CV (PDF, 2 Mo maximum)</label><input type="file" name="cv" accept="application/pdf,.pdf"><?php if($p['cv_path']):?><p><a href="<?=h(url('download_cv.php?student_id='.$u['id']))?>">Voir mon CV actuel</a></p><?php endif;?><div class="actions"><button>Enregistrer</button></div></form>
<?php page_footer();?>