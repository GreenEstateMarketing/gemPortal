<?php

namespace Theme\FlexHome\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use RvMedia;

/**
 * Public-facing agent search result. Deliberately excludes fields that
 * AccountResource exposes for an agent's own dashboard (credits) - this
 * resource is served to anonymous visitors. Phone/email are intentionally
 * included so visitors can contact agents directly from the directory.
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
            'email' => $this->email,
            'description' => $this->description,
            'years_of_experience' => $this->years_of_experience,
            'languages' => $this->spokenLanguages->pluck('name')->values(),
            'specialties' => $this->specialties->pluck('name')->values(),
            'properties_count' => (int) $this->properties_count,
            'distance' => $this->distance !== null ? round((float) $this->distance, 1) : null,
            'city' => $this->city->name ?: null,
            'country' => $this->city->country->name ?: null,
            'rating' => $this->rating_avg !== null ? round((float) $this->rating_avg, 1) : null,
        ];
    }
}
