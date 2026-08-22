<?php

namespace Botble\RealEstate\Http\Resources;

use Botble\RealEstate\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Account
 */
class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'full_name'   => $this->full_name,
            'email'       => $this->email,
            'mobile_no'   => $this->phone,
            'credits'     => $this->credits,
        ];
    }
}
