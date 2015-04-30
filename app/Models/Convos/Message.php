<?php namespace App\Models\Convos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    protected $table = 'convos_messages';

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function conversation()
    {
        return $this->belongsTo('App\Models\Convos\Conversation');
    }

    public function user()
    {
        return $this->belongsTo('App\Model\User', 'created_by');
    }
}