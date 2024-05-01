<?php

namespace Theme\FlexHome\Http\Resources;

use Botble\RealEstate\Enums\PropertyTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use RvMedia;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $price = format_price($this->price, $this->currency);

        if ($this->type == PropertyTypeEnum::RENT) {
            $price .= ' / ' . Str::lower($this->period->label());
        }

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
            'price'           => $price,
            'location'        => $this->city->name . ', ' . $this->city->state->name,
            'number_bedroom'  => $this->number_bedroom,
            'number_bathroom' => $this->number_bathroom,
            'square'          => $this->square,
            'square_text'     => $this->square_text,
            'type'            => $this->type,
            'period'          => $this->period,
            'status_html'     => $this->status->toHtml(),
            'category_name'   => $this->category->name,
            'category_id'   => $this->category->id,
            'category_parent_id'   => $this->category->parent_id,
            'latitude'   => $this->latitude,
            'longitude'   => $this->longitude
        ];
    }
}
