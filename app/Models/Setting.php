<?php

namespace App\Models;

use App;
use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public static function setLocale(Request $request, User $user = null)
    {
        if (array_key_exists('locale', $request->header())) {
            $locale = $request->header('locale', 'en');
            if (in_array($locale, ['en', 'zh'])) {
                App::setLocale($locale);
                if (!$user) {
                    $user = Auth::guard('sanctum')->user();
                }

                if ($user) {
                    if ($user->setting) {
                        $user->setting->locale = $locale;
                        $user->setting->save();
                    } else {
                        if ($user instanceof User)
                            $user->setting()->create(['locale' => $locale]);
                    }
                }
            }
        }
    }
}
