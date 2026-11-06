<?php
require_once __DIR__.'/../includes/layout.php';$u=require_login('recruiter');$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$v=[];foreach(['company_name','sector','website','city','description'] as $f)$v[$f]=trim((string)($_POST[$f]??''));
 if($v['company_name']===''||mb_strlen($v['company_name'])>190||mb_strlen($v['description'])>3000||mb_strlen($v['sector'])>150||mb_strlen($v['website'])>250||mb_strlen($v['city'])>120)$error='Vérifiez les champs.';
 elseif($v['website']!=='' && (!filter_var($v['website'],FILTER_VALIDATE_URL)||!in_array(parse_url($v['website'],PHP_URL_SCHEME),['http','https'],true)))$error='URL du site invalide.';
 else {$q=db()->prepare('UPDATE recruiter_profiles SET company_name=?,sector=?,website=?,city=?,description=? WHERE user_id=?');$q->execute([...array_values($v),$u['id']]);log_action($u,'update_recruiter_profile','user',(int)$u['id']);flash('Entreprise mise à jour.');redirect('recruiter/profile.php');}
}
$q=db()->prepare('SELECT * FROM recruiter_profiles WHERE user_id=?');$q->execute([$u['id']]);$p=$q->fetch();page_header('Mon entreprise');if($error)echo '<div class="error">'.h($error).'</div>';
?><form class="panel narrow" method="post"><?=csrf_field()?><?php foreach(['company_name'=>'Nom de l’entreprise','sector'=>'Secteur','website'=>'Site internet (https://...)','city'=>'Ville'] as $f=>$label):?><label><?=h($label)?></label><input name="<?=h($f)?>" value="<?=h($p[$f])?>" <?=$f==='company_name'?'required':''?>><?php endforeach;?><label>Description</label><textarea name="description"><?=h($p['description'])?></textarea><button>Enregistrer</button></form><?php page_footer();?>