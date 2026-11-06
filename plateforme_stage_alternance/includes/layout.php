<?php
require_once __DIR__ . '/bootstrap.php';
function page_header(string $title): void {
 $u=user(); $flash=consume_flash();
 $home=$u ? ['student'=>'student/dashboard.php','recruiter'=>'recruiter/dashboard.php','admin'=>'admin/dashboard.php'][$u['role']] : '';
 echo '<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#101c37"><meta name="description" content="StageConnect, plateforme de stages et alternances"><title>'.h($title).' · StageConnect</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="'.url('assets/style.css').'?v=3"></head><body class="'.($u?'has-session':'guest').'"><header class="top"><div class="nav-shell"><a class="brand" href="'.url($home).'" aria-label="StageConnect, accueil"><span class="brand-mark">S<span class="brand-dot">.</span></span><span>stage<span class="brand-accent">connect</span></span></a><button class="mobile-menu-toggle" type="button" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav"><span></span><span></span><span></span></button><nav id="main-nav">';
 if (!$u) {
  echo '<a class="nav-link" href="'.url().'">Accueil</a><a class="nav-link" href="'.url().'#comment-ca-marche">Comment ça marche</a><a class="nav-link" href="'.url('login.php').'">Connexion</a><a class="btn nav-cta" href="'.url('register.php').'">Créer un compte <span aria-hidden="true">↗</span></a>';
 } else {
  echo '<a class="nav-link" href="'.url($home).'">Tableau de bord</a>';
  if ($u['role']==='student') echo '<a class="nav-link" href="'.url('student/profile.php').'">Mon profil</a><a class="nav-link" href="'.url('student/applications.php').'">Mes candidatures</a><a class="nav-link" href="'.url('student/notifications.php').'">Notifications</a>';
  if ($u['role']==='recruiter') echo '<a class="nav-link" href="'.url('recruiter/profile.php').'">Entreprise</a><a class="nav-link" href="'.url('recruiter/offers.php').'">Mes offres</a>';
  if ($u['role']==='admin') echo '<a class="nav-link" href="'.url('admin/users.php').'">Utilisateurs</a><a class="nav-link" href="'.url('admin/activity.php').'">Activité</a>';
  echo '<span class="user-chip"><span class="avatar">'.h(mb_strtoupper(mb_substr($u['name'],0,1))).'</span><span class="user-chip-name">'.h($u['name']).'</span></span><form class="inline" method="post" action="'.url('logout.php').'">'.csrf_field().'<button class="logout-btn" aria-label="Se déconnecter">Sortir ↗</button></form>';
 }
 echo '</nav></div></header><main class="container '.(!$u?'public-container':'dashboard-container').'">';
 if ($flash) echo '<div class="flash" role="status">'.h($flash).'</div>';
 if ($u) echo '<div class="page-eyebrow">'.h(['student'=>'ESPACE ÉTUDIANT','recruiter'=>'ESPACE RECRUTEUR','admin'=>'ADMINISTRATION'][$u['role']]).'</div>';
 if ($title!=='Bienvenue sur StageConnect') echo '<h1>'.h($title).'</h1>';
}
function page_footer(): void {
 echo '</main><footer class="site-footer"><div class="footer-inner"><div><span class="footer-logo">stage<span>connect</span></span><p>Les bonnes opportunités, les bonnes rencontres.</p></div><span>© '.date('Y').' StageConnect · Stages & alternances</span></div></footer><script src="'.url('assets/app.js').'" defer></script></body></html>';
}
