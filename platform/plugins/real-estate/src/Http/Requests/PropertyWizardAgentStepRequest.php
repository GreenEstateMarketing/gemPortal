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
     * Admins aren't limited to location coverage - they can assign any agent
     * in the system (matches the picker shown to them, see
     * PropertyWizardController::nearbyAgentsFor()).
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->currentRole() === 'admin') {
                return;
            }

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

    /**
     * Mirrors PropertyWizardController::currentRole() - each role owns its
     * own route names, so the role can be derived from the matched route.
     */
    protected function currentRole(): string
    {
        $name = optional($this->route())->getName() ?? '';

        if (strpos($name, 'public.account.') === 0) {
            return 'agent';
        }

        if (strpos($name, 'public.member.') === 0) {
            return 'member';
        }

        if (strpos($name, 'general-property-wizard') === 0) {
            return 'guest';
        }

        return 'admin';
    }
}
