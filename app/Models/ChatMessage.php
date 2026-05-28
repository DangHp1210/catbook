<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatMessage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'sender_type',
        'message_text',
        'message_type',
        'related_book_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'session_id');
    }

    public function relatedBook(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'related_book_id');
    }

    public function aiLog(): HasOne
    {
        return $this->hasOne(ChatAiLog::class, 'message_id');
    }
}
