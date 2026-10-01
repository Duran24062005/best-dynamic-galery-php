<?php
declare(strict_types=1);
final class Database {
 public static function connect(array $config): PDO {
  $url=(string)(getenv('DATABASE_URL')?:getenv('POSTGRES_URL')?:'');
  if($url!==''){ $p=parse_url($url); if(!$p||empty($p['host']))throw new RuntimeException('DATABASE_URL no es valida.'); $q=[];parse_str($p['query']??'',$q);$dsn='pgsql:host='.$p['host'].';port='.($p['port']??5432).';dbname='.ltrim($p['path']??'','/').';sslmode='.($q['sslmode']??'require');return new PDO($dsn,urldecode($p['user']??''),urldecode($p['pass']??''),[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]); }
  return new PDO('pgsql:host='.(getenv('PGHOST')?:'localhost').';port='.(getenv('PGPORT')?:'5432').';dbname='.(getenv('PGDATABASE')?:$config['name']),getenv('PGUSER')?:$config['user'],getenv('PGPASSWORD')?:$config['pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
 }
}
