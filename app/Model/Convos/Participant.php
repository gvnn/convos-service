<?php namespace App\Model\Convos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    protected $table = 'convos_participants';

    use SoftDeletes;

    protected $fillable = ['is_creator', 'is_read', 'read_at'];

    protected $dates = ['deleted_at', 'read_at'];

    protected $casts = [
        'is_creator' => 'boolean',
        'is_read' => 'boolean'
    ];

    public function conversation()
    {
        return $this->belongsTo('App\Model\Convos\Conversation');
    }

    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }
}