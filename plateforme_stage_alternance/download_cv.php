<?php
require_once __DIR__.'/includes/bootstrap.php';$u=require_login();$sid=(int)($_GET['student_id']??0);$app=(int)($_GET['application_id']??0);
$allowed=($u['role']==='student'&&(int)$u['id']===$sid)||$u['role']==='admin';
if($u['role']==='recruiter' && $app>0){$q=db()->prepare('SELECT COUNT(*) FROM applications a JOIN offers o ON o.id=a.offer_id WHERE a.id=? AND a.student_id=? AND o.recruiter_id=?');$q->execute([$app,$sid,$u['id']]);$allowed=(bool)$q->fetchColumn();}
if(!$allowed){http_response_code(403);exit('Accès interdit');}
$q=db()->prepare('SELECT cv_path FROM student_profiles WHERE user_id=?');$q->execute([$sid]);$path=$q->fetchColumn();
if(!$path || !preg_match('~^uploads/cv/[a-f0-9]{40}\.pdf$~',$path)){http_response_code(404);exit('CV introuvable');}
$full=__DIR__.'/'.$path;if(!is_file($full)){http_response_code(404);exit('Fichier introuvable');}
header('Content-Type: application/pdf');header('Content-Disposition: attachment; filename="cv-etudiant-'.$sid.'.pdf"');header('Content-Length: '.filesize($full));header('X-Content-Type-Options: nosniff');readfile($full);
