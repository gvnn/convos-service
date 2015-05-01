<?php namespace App\Model\Convos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    protected $table = 'convos_messages';

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['user_id', 'body'];

    protected $hidden = array('deleted_at');

    public function conversation()
    {
        return $this->belongsTo('App\Model\Convos\Conversation');
    }

    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
}