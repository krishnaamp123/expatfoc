<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'city_name' => $this->city_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
