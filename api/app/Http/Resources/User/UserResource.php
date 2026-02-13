<?php

namespace App\Http\Resources\User;

use App\Http\Resources\ClinicResource;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\NurseResource;
use App\Http\Resources\WorkerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'role' => [
                'id' => $this->role->id,
                'name' => $this->role->name,
            ],
            'permissions' => $this->permissions(),
            'profile_type' => $this->profileType(),
            'profile' => $this->profileResource(),
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
        ];
    }

    protected function profileType(): ?string
    {
        if ($this->doctor) {
            return 'doctor';
        }
        if ($this->nurse) {
            return 'nurse';
        }
        if ($this->worker) {
            return 'worker';
        }

        return null;
    }

    protected function profileResource(): mixed
    {
        if ($this->doctor) {
            return new DoctorResource($this->doctor);
        }

        if ($this->nurse) {
            return new NurseResource($this->nurse);
        }

        if ($this->worker) {
            return new WorkerResource($this->worker);
        }

        return null;
    }

    protected function permissions(): array
    {
        return $this
            ->role
            ->permissions
            ->pluck('key')
            ->values()
            ->toArray();
    }
}
