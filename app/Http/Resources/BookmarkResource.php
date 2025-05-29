<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
{
    public function toArray($request)
{
    return [
        'id'        => $this->id,
        'title'     => $this->title,
        'url'       => $this->url,
        'category'  => $this->category,
        'tags'      => $this->tags?->pluck('name') ?? [],
        'user'      => $this->whenLoaded('user', function () {
            return [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ];
        }),
        'created_at'=> $this->created_at,
    ];
}

}
