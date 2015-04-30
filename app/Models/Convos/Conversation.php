<?php namespace App\Models\Convos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    protected $table = 'convos_conversations';

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['subject', 'created_by'];

    public function messages()
    {
        return $this->hasMany('App\Models\Convos\Message');
    }

    public function participants()
    {
        return $this->hasMany('App\Model\Convos\Participant');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Model\User', 'created_by');
    }
}