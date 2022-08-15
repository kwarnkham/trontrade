<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class Agent extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'key' => 'encrypted'
    ];

    protected $hidden = [''];

    public static function verify(Request $request)
    {
        $name = $request->header('x-agent');
        $key = $request->header('x-api-key');
        $ip = $request->ip();
        if (!$name || !$key || !$ip) {
            return false;
        }
        $agent = static::where('name', $name)->first();
        if (!$agent || !Hash::check($agent->id, $agent->key) || $agent->key != $key || $agent->status == 2) {
            Log::channel('agents')->alert("Unauthorized agent access: $name : $key : $ip");
            return false;
        }
        if ($agent->ip != $ip && $agent->ip != "*") {
            Log::channel('agents')->alert("Unauthorized agent ip: $name : $key : $ip");
            return false;
        }
        return true;
    }

    public static function make($name, $ip, $remark = null)
    {
        $agent = static::create([
            'name' => $name,
            'ip' => $ip,
            'key' => 'key',
            'remark' => $remark
        ]);

        $agent->key = bcrypt($agent->id);
        $agent->save();
        return $agent;
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function summon(Request $request)
    {
        return static::where('name', $request->header('x-agent'))->first();
    }

    public function resetKey()
    {
        $this->key = bcrypt($this->id);
        return $this->save();
    }
}
