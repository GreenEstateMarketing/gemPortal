<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Models\Account;
use Botble\Support\Http\Requests\Request;

class PropertyWizardAgentStepRequest extends Request
{
    public function rules(): array
    {
        return [
            'agent_id' => ['required', 'integer'],
        ];
    }

    /**
     * Which agents are selectable depends on the property's location (an
     * agent's drawn coverage area must contain it), so this can't be a
     * static rule. The already-assigned agent (if any) stays valid even if
     * they no longer match, so re-submitting the same choice never fails.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var \Botble\RealEstate\Models\Property|null $property */
            $property = $this->route('property');
            $agentId = (int) $this->input('agent_id');

            if (!$property || !$agentId) {
                return;
            }

            $alreadyAssigned = $property->author_type === Account::class
                ? (int) $property->author_id
                : null;

            if ($agentId === $alreadyAssigned) {
                return;
            }

            $eligible = $property->latitude && $property->longitude
                ? Account::query()->coveringPoint($property->longitude, $property->latitude)->pluck('id')->all()
                : [];

            if (!in_array($agentId, $eligible, true)) {
                $validator->errors()->add(
                    'agent_id',
                    'Please choose an agent available for this property\'s location.'
                );
            }
        });
    }
}
