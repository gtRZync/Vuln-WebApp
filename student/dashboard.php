<?php
require_once __DIR__.'/../includes/layout.php';$u=require_login('student');
$term=trim((string)($_GET['q']??''));$type=$_GET['type']??'';if(!in_array($type,['stage','alternance'],true))$type='';
$sql="SELECT o.*,r.company_name,a.id application_id FROM offers o JOIN recruiter_profiles r ON r.user_id=o.recruiter_id LEFT JOIN applications a ON a.offer_id=o.id AND a.student_id=? WHERE o.status='open'";$args=[$u['id']];
if($term!==''){$sql.=' AND (o.title LIKE ? OR o.description LIKE ? OR o.city LIKE ? OR r.company_name LIKE ?)';$like='%'.$term.'%';array_push($args,$like,$like,$like,$like);}
if($type!==''){$sql.=' AND o.type=?';$args[]=$type;}$sql.=' ORDER BY o.created_at DESC LIMIT 100';
$q=db()->prepare($sql);$q->execute($args);$offers=$q->fetchAll();
$n=db()->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0');$n->execute([$u['id']]);$unread=$n->fetchColumn();page_header('Offres de stage et d’alternance');
?>
<p><a href="<?=h(url('student/notifications.php'))?>">Notifications non lues : <b><?=h((string)$unread)?></b></a></p>
<form class="panel search" method="get"><div><label>Recherche</label><input name="q" placeholder="Métier, ville, entreprise..." value="<?=h($term)?>"></div><div><label>Type</label><select name="type"><option value="">Tous</option><option value="stage" <?=$type==='stage'?'selected':''?>>Stage</option><option value="alternance" <?=$type==='alternance'?'selected':''?>>Alternance</option></select></div><button>Filtrer</button></form>
<div class="grid"><?php foreach($offers as $o):?><article class="card"><span class="pill"><?=h($o['type'])?></span><h2><?=h($o['title'])?></h2><p><b><?=h($o['company_name'])?></b> · <?=h($o['city'])?></p><p><?=h(mb_strimwidth($o['description'],0,220,'…'))?></p><?php if($o['application_id']):?><span class="muted">Déjà candidaté</span><?php else:?><a class="btn" href="<?=h(url('student/apply.php?id='.$o['id']))?>">Voir et postuler</a><?php endif;?></article><?php endforeach;?></div>
<?php if(!$offers):?><p>Aucune offre trouvée.</p><?php endif; page_footer();?>