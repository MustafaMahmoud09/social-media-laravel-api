<?php

namespace App\Http\Resources\User\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'content_id' => $this->data['id'],
            'content' => $this->data['content'],
            'notification_type' => $this->data['type'],
            'user_name' => $this->data['user'],
            'read_at' => $this->read_at,
            'created_at' => $this->created_at
        ];
    }
}
