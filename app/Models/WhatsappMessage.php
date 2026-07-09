<?php

namespace App\Models;

use App\Enums\WhatsappMessageDirection;
use App\Enums\WhatsappMessageStatus;
use Database\Factories\WhatsappMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $whatsapp_conversation_id
 * @property WhatsappMessageDirection $direction
 * @property string|null $wa_message_id
 * @property string|null $body
 * @property string|null $template_name
 * @property WhatsappMessageStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['whatsapp_conversation_id', 'direction', 'wa_message_id', 'body', 'template_name', 'status'])]
class WhatsappMessage extends Model
{
    /** @use HasFactory<WhatsappMessageFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'direction' => WhatsappMessageDirection::class,
            'status' => WhatsappMessageStatus::class,
        ];
    }

    /**
     * @return BelongsTo<WhatsappConversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(WhatsappConversation::class, 'whatsapp_conversation_id');
    }
}
