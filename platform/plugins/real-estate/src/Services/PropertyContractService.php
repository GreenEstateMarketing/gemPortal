<?php

namespace Botble\RealEstate\Services;

use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Property;
use Illuminate\Support\Facades\Storage;
use PDF;

/**
 * Generates the two-party (member/agent) sale agreement PDF for a property
 * and writes both recipient copies to the private contracts disk. The PDF
 * is regenerated on demand from whatever data currently exists - see
 * PropertyWizardController::signContractPlaceholder() - rather than
 * generated once and left static.
 */
class PropertyContractService
{
    public function requiresMember(Property $property): bool
    {
        return (bool) $property->member_id;
    }

    protected function agent(Property $property): ?Account
    {
        return $property->author_type === Account::class && $property->author_id
            ? Account::find($property->author_id)
            : null;
    }

    public function memberHasSignature(Property $property): bool
    {
        return ! $this->requiresMember($property) || (bool) optional($property->member)->hasSignature();
    }

    public function agentHasSignature(Property $property): bool
    {
        return (bool) optional($this->agent($property))->hasSignature();
    }

    public function isReadyToGenerate(Property $property): bool
    {
        return $this->memberHasSignature($property) && $this->agentHasSignature($property);
    }

    public function directory(Property $property): string
    {
        return "contracts/{$property->id}";
    }

    public function memberCopyPath(Property $property): string
    {
        return $this->directory($property) . '/signed-contract-member-copy.pdf';
    }

    public function agentCopyPath(Property $property): string
    {
        return $this->directory($property) . '/signed-contract-agent-copy.pdf';
    }

    public function hasGeneratedCopies(Property $property): bool
    {
        return Storage::disk('local')->exists($this->memberCopyPath($property))
            && Storage::disk('local')->exists($this->agentCopyPath($property));
    }

    /**
     * Renders the agreement from whatever data currently exists - including
     * blank signature boxes for a party that hasn't signed yet - without
     * writing anything to disk. This is what the always-visible preview on
     * the wizard page uses, regardless of whether the contract is finalized.
     */
    public function renderPdf(Property $property): string
    {
        return PDF::loadView(
            'plugins/real-estate::wizard.contract.pdf-template',
            $this->buildViewData($property)
        )->output();
    }

    /**
     * Finalizes the contract: renders it once and writes identical bytes to
     * both the member's and agent's copy - they're two recipients' copies
     * of the same executed document, not different renderings. Only called
     * from the "Save & Continue" action once both required signatures are
     * present - see PropertyWizardController::finalizeContract().
     */
    public function generate(Property $property): void
    {
        $pdfContent = $this->renderPdf($property);

        Storage::disk('local')->put($this->memberCopyPath($property), $pdfContent);
        Storage::disk('local')->put($this->agentCopyPath($property), $pdfContent);
    }

    protected function buildViewData(Property $property): array
    {
        $member = $this->requiresMember($property) ? $property->member : null;
        $agent = $this->agent($property);

        return [
            'logoDataUri' => $this->logoDataUri(),
            'sellerName' => $member ? $member->full_name : null,
            'sellerContact' => $member ? $this->formatPhone($member->mobile_no) : null,
            'sellerSignatureDataUri' => $member ? $member->signature_data_uri : null,
            'sellerDate' => $member && $member->signature_created_at
                ? $member->signature_created_at->format('d/m/Y')
                : null,
            'agentName' => $agent ? $agent->getFullName() : null,
            'agentContact' => $agent ? $this->formatPhone($agent->phone) : null,
            'agentSignatureDataUri' => $agent ? $agent->signature_data_uri : null,
            'agentDate' => $agent && $agent->signature_created_at
                ? $agent->signature_created_at->format('d/m/Y')
                : null,
            'propertyAddress' => $property->location,
            'propertyType' => optional($property->category)->name,
            'propertySize' => $property->square_text,
            'salePrice' => $this->formatExactPrice($property),
            'agreementDate' => now()->format('d / m / Y'),
        ];
    }

    /**
     * format_price()/human_price_text() shortens large numbers to "X
     * million"/"X billion" by default (display_big_money_in_million_billion),
     * which is unacceptable for a legal document stating an exact agreed
     * price - this bypasses that entirely.
     */
    protected function formatExactPrice(Property $property): string
    {
        $decimals = optional($property->currency)->decimals ?? 0;

        return number_format(
            (float) $property->price,
            $decimals,
            setting('real_estate_decimal_separator', '.'),
            setting('real_estate_thousands_separator', ',')
        );
    }

    /**
     * Stored phone numbers already carry a full country code (confirmed
     * against real data) but are inconsistent about the leading "+" and
     * sometimes contain spaces/dashes/parens (both mobile_no's and phone's
     * own validation regexes allow those characters). Strip everything but
     * digits, then add back exactly one leading "+".
     */
    protected function formatPhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', $phone);

        return $digits !== '' ? '+' . $digits : null;
    }

    /**
     * The theme's master logo file is a 1.3MB PNG - too large to embed
     * as-is on every PDF regeneration. Downscaled on the fly via GD rather
     * than requiring a separate pre-shrunk static asset.
     */
    protected function logoDataUri(): string
    {
        $path = public_path('themes/real-scout/images/gemlogo.png');

        if (! is_file($path)) {
            return '';
        }

        $source = @imagecreatefrompng($path);

        if (! $source) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
        }

        $targetWidth = 220;
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $targetHeight = (int) round($sourceHeight * ($targetWidth / $sourceWidth));

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        ob_start();
        imagepng($resized);
        $bytes = ob_get_clean();

        imagedestroy($source);
        imagedestroy($resized);

        return 'data:image/png;base64,' . base64_encode($bytes);
    }
}
