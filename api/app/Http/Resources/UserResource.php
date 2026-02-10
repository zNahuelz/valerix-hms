<?php

namespace App\Http\Resources;

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
            'role' => [
                'id' => $this->role->id,
                'name' => $this->role->name,
            ],
            'permission' => $this->permissions(),
            'profile_type' => $this->profileType(),
            'profile' => $this->profileResource(),
        ];
    }

    protected function profileType(): ?string
    {
        if ($this->doctor) return 'doctor';
        if ($this->nurse) return 'nurse';
        if ($this->worker) return 'worker';

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
