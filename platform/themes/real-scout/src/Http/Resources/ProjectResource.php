<?php

namespace Theme\FlexHome\Http\Resources;

use Botble\RealEstate\Enums\PropertyTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use RvMedia;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $price_from = format_price($this->price_from, $this->currency);
        $price_to = format_price($this->price_to, $this->currency);

        $image = $this->image ? RvMedia::getImageUrl($this->image, 'small', false, RvMedia::getDefaultImage()) : null;

        $images = [];

        foreach ($this->images as $item) {
            $images[] = RvMedia::getImageUrl($item, 'small', false, RvMedia::getDefaultImage());
        }

        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'name_short'      => substr($this->name,0,40),
            'url'             => $this->url,
            'description'     => $this->description,
            'image'           => $image,
            'images'          => $this->images,
            'price_from'           => $price_from,
            'price_to'           => $price_to,
            'location'        => $this->city->name . ', ' . $this->city->state->name,
            'floor'       => $this->number_floor,
            'flat'          => $this->number_flat,
            'block'            => $this->number_block,
            'category_name'   => $this->category->name,
            'category_parent_id'   => $this->category->parent_id,
            'latitude'   => $this->latitude,
            'longitude'   => $this->longitude,
            'status_html'     => $this->status->toHtml()
        ];
    }
}
