<?php
// EXÉCUTION CLI UNIQUEMENT : php scripts/create_admin.php admin@exemple.fr "MotDePasseSolide" "Administrateur"
if(PHP_SAPI!=='cli'){http_response_code(403);exit('CLI uniquement');}
require_once __DIR__.'/../includes/bootstrap.php';
$email=strtolower(trim($argv[1]??''));$password=$argv[2]??'';$name=trim($argv[3]??'Administrateur');
if(!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<12||$name===''){fwrite(STDERR,"Usage : php scripts/create_admin.php email mot_de_passe_12_caracteres [nom]\n");exit(1);}
try{$q=db()->prepare("INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,'admin')");$q->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);echo "Administrateur créé : ".$email.PHP_EOL;}catch(PDOException $e){fwrite(STDERR,"Échec : vérifiez l'e-mail et MySQL.\n");exit(1);}
