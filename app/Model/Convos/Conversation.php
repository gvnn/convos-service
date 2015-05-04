<?php namespace App\Model\Convos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Conversation
 * @package App\Model\Convos
 */
class Conversation extends Model
{
    protected $table = 'convos_conversations';

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $hidden = array('deleted_at');

    protected $fillable = ['subject', 'created_by'];

    /**
     * @return \App\Model\Convos\Message array
     */
    public function messages()
    {
        return $this->hasMany('App\Model\Convos\Message');
    }

    /**
     * @return \App\Model\Convos\Participant array
     */
    public function participants()
    {
        return $this->hasMany('App\Model\Convos\Participant');
    }

    /**
     * @return \App\Model\User
     */
    public function created_by()
    {
        return $this->belongsTo('App\Model\User', 'created_by');
    }
}