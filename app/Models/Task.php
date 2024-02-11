<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'name',
        'detail',
        'due_date',
        'status',
        'user_id', // Ditambahkan
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
