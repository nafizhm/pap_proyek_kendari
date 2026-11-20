<?php
require __DIR__.'/../vendor/autoload.php';
$app=require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$files=glob(storage_path('app/private/database-backups/pap_kendari-before-project-*.json'));sort($files);
$backup=json_decode(file_get_contents(end($files)),true,512,JSON_THROW_ON_ERROR);
$pdo=Illuminate\Support\Facades\DB::connection()->getPdo();$bad=[];
foreach($backup as $table=>$before){
 $schema=$pdo->query('SHOW CREATE TABLE `'.$table.'`')->fetch(PDO::FETCH_NUM)[1];
 $rows=$pdo->query('SELECT * FROM `'.$table.'`')->fetchAll(PDO::FETCH_ASSOC);
 if($schema!==$before['sql'] || json_encode($rows)!==json_encode($before['rows'])) $bad[]=$table;
}
echo count($backup).' original tables checked; changed: '.json_encode($bad).PHP_EOL;
$errors=[];
foreach(['app','config','routes','database/migrations'] as $dir){
 foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path($dir))) as $file){
 if(!$file->isFile() || $file->getExtension()!=='php') continue;
 try{token_get_all(file_get_contents($file->getPathname()),TOKEN_PARSE);}catch(Throwable $e){$errors[]=$file->getPathname().': '.$e->getMessage();}
 }}
echo 'PHP syntax errors: '.count($errors).PHP_EOL;foreach($errors as $error) echo $error.PHP_EOL;
exit($bad||$errors?1:0);

