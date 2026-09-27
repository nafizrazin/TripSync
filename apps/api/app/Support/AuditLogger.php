<?php
namespace App\Support;
use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Facades\DB;
final class AuditLogger {
    public function record(string $action,Model $entity,?array $before=null,?array $after=null,?string $actorId=null): void {
        $request=app()->bound('request')?request():null;
        DB::table('audit_logs')->insert(['actor_id'=>$actorId ?: $request?->user()?->id,'action'=>$action,'entity_type'=>$entity::class,'entity_id'=>(string)$entity->getKey(),'before_data'=>$before?json_encode($before):null,'after_data'=>$after?json_encode($after):null,'ip_address'=>$request?->ip(),'user_agent'=>$request?->userAgent(),'request_id'=>$request?->attributes->get('request_id'),'created_at'=>now()]);
    }
}
