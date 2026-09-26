<?php

namespace Theme\FlexHome\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use RvMedia;

/**
 * Public-facing agent search result. Deliberately excludes fields that
 * AccountResource exposes for an agent's own dashboard (email, credits) -
 * this resource is served to anonymous visitors.
 */
class AgentSearchResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $avatar = $this->image_path
            ? RvMedia::getImageUrl($this->image_path)
            : $this->avatar_url;

        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->getFullName(),
            'avatar' => $avatar,
            'phone' => $this->phone,
            'description' => $this->description,
            'years_of_experience' => $this->years_of_experience,
            'languages' => $this->spokenLanguages->pluck('name')->values(),
            'specialties' => $this->specialties->pluck('name')->values(),
            'properties_count' => (int) $this->properties_count,
            'distance' => $this->distance !== null ? round((float) $this->distance, 1) : null,
        ];
    }
}
