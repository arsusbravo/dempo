<?php

namespace App\Models;

use Database\Factories\WhatsappConversationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $phone_number
 * @property string|null $name
 * @property bool $window_open
 * @property Carbon|null $last_customer_message_at
 * @property Carbon|null $last_message_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['phone_number', 'name', 'window_open', 'last_customer_message_at', 'last_message_at'])]
class WhatsappConversation extends Model
{
    /** @use HasFactory<WhatsappConversationFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'window_open' => 'boolean',
            'last_customer_message_at' => 'datetime',
            'last_message_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<WhatsappMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class);
    }
}
