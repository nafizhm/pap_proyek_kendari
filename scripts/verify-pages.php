<?php
require __DIR__.'/../vendor/autoload.php';
$app=require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel=$app->make(Illuminate\Contracts\Http\Kernel::class);
$guest=$kernel->handle(Illuminate\Http\Request::create('/admin/login','GET')); if ($guest->getStatusCode()!==200) { throw new RuntimeException('Guest login page failed'); } echo '200 guest login'.PHP_EOL;
$session=$app['session']->driver();$session->start();
$user=App\Models\User::where('id_role',1)->firstOrFail();
Illuminate\Support\Facades\Auth::login($user);
$ids=App\Models\HakAkses::where('id_user',$user->id)->where('lihat',1)->pluck('id_menu');
$menus=App\Models\Menu::where('id_parent',0)->whereIn('id',$ids)->with('children')->get();
$session->put('getmenus',$menus);
$paths=['/admin/login','/admin/dashboard','/admin/siteplan','/admin/master/barang','/admin/gudang/barang-masuk/create','/admin/gudang/barang-keluar/create'];
foreach($app['router']->getRoutes() as $route){
 if(in_array('GET',$route->methods()) && !str_contains($route->uri(),'{') && str_starts_with($route->uri(),'admin/') && str_ends_with($route->getName()??'','.index')) $paths[]='/'.$route->uri();
}
$location=App\Models\LokasiKavling::firstOrFail(); $unit=App\Models\KavlingPeta::firstOrFail();
$paths[]='/admin/siteplan/'.$unit->id;
$paths[]='/admin/gudang/barang-keluar/unit-by-lokasi/'.$location->id;
$paths[]='/admin/pengaturan/setting-proyek/units-by-lokasi/'.$location->id;
$failed=0;
foreach(array_unique($paths) as $path){
 $r=Illuminate\Http\Request::create($path,'GET');$r->setLaravelSession($session); if (in_array('--ajax',$argv,true)) { $r->headers->set('X-Requested-With','XMLHttpRequest'); $r->headers->set('Accept','application/json'); }
 $response=$kernel->handle($r);$status=$response->getStatusCode();
 echo $status.' '.$path.PHP_EOL;
 if($status>=400){$failed++;file_put_contents(storage_path('app/failed-page-'.md5($path).'.html'),$response->getContent());}
}
exit($failed ? 1 : 0);
