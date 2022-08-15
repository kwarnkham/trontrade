<?php

namespace App\Models;

use App\Utility\Utility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Identifier extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getNameAttribute($attribute)
    {
        return __("messages.$attribute");
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('first_name', 'middle_name', 'last_name', 'number', 'sub_number', 'images', 'status', 'confirmed_at')
            ->as('identity')
            ->withTimestamps();
    }

    public function deleteImages(User $user)
    {
        $images = json_decode($this->users()->where('user_id', $user->id)->first()->identity->images);

        foreach ($images as $image) {
            Utility::deleteFromGoogleBucket(Utility::parseObjectNameFromUrl($image));
        }
    }
}
