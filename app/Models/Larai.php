<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Larai extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'question',
        'response',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 親の会話（最初の質問）
    public function parent()
    {
        return $this->belongsTo(Larai::class, 'parent_id');
    }

    // 子の会話（続きの質問）
    public function children()
    {
        return $this->hasMany(Larai::class, 'parent_id')->orderBy('created_at');
    }

    // 会話のルート（最初の質問）を取得
    public function getRoot()
    {
        if ($this->parent_id === null) {
            return $this;
        }
        return $this->parent->getRoot();
    }

    // 会話スレッド全体を取得（親を含むすべての質問と回答）
    public function getThread()
    {
        $root = $this->getRoot();
        return collect([$root])->merge($root->children);
    }
}
