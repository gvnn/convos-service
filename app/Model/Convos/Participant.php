<?php namespace App\Model\Convos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Conversation Participant
 *
 * @package App\Model\Convos
 */
class Participant extends Model
{
    protected $table = 'convos_participants';

    use SoftDeletes;

    protected $fillable = ['user_id', 'is_creator', 'is_read', 'read_at'];

    protected $dates = ['deleted_at', 'read_at'];

    protected $hidden = array('deleted_at');

    protected $casts = [
        'is_creator' => 'boolean',
        'is_read' => 'boolean'
    ];

    /**
     * @return \App\Model\Convos\Conversation
     */
    public function conversation()
    {
        return $this->belongsTo('App\Model\Convos\Conversation');
    }

    /**
     * @return \App\Model\User
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }
}