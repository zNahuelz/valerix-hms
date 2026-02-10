<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
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
            'names' => $this->names,
            'paternal_surname' => $this->paternal_surname,
            'maternal_surname' => $this->maternal_surname,
            'full_name' => trim("{$this->names} {$this->paternal_surname} {$this->maternal_surname}"),
            'dni' => $this->dni,
            'phone' => $this->phone,
            'address' => $this->address,
            'hired_at' => $this->hired_at?->toDateString(),
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
        ];
    }
}
