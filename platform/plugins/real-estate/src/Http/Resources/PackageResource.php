<?php

namespace Botble\RealEstate\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
        $hasDiscount = $this->original_price && $this->original_price > $this->price;

        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'price'               => $this->price,
            'price_text'          => format_price($this->price, $this->currency),
            'price_per_post_text' => format_price($this->price / $this->number_of_listings, $this->currency),
            'percent_save'        => $this->percent_save,
            'number_of_listings'  => $this->number_of_listings,
            'is_recommended'      => (bool) $this->is_recommended,
            'original_price'      => $hasDiscount ? $this->original_price : null,
            'original_price_text' => $hasDiscount ? format_price($this->original_price, $this->currency) : null,
            'discount_percent'    => $hasDiscount
                ? (int) round((($this->original_price - $this->price) / $this->original_price) * 100)
                : 0,
            'savings_text'        => $hasDiscount
                ? format_price($this->original_price - $this->price, $this->currency)
                : null,
        ];
    }
}
