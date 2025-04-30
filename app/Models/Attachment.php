<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

/**
 * @property mixed $id
 * @property mixed $user_id
 * @property mixed $path
 * @property mixed $mime_type
 * @property mixed $original_name
 * @property mixed $size
 * @method static create(array $array)
 * @method static findOrFail($id)
 * @method static where(string $string, $id)
 */
class Attachment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'original_name', 'mime_type', 'path', 'size'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Generate a temporary signed URL for downloading the attachment.
     *
     * @param int $minutes Number of minutes until the URL expires. Default is 5.
     * @return string The signed URL.
     */
    public function getSignedUrl(int $minutes = 5): string
    {
        return URL::temporarySignedRoute(
            'attachments.download',
            now()->addMinutes($minutes),
            ['id' => $this->id]
        );
    }

    /**
     * Get the user that owns the attachment.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
