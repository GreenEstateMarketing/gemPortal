<?php

namespace Botble\RealEstate\Services;

use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\PropertyContract;
use Illuminate\Support\Facades\Crypt;
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

    /**
     * Every regenerated copy gets its own timestamped filename rather than
     * overwriting the last one, so earlier generations stay on disk as an
     * audit trail of what this contract looked like at each point - even
     * though only the latest row's path is ever read back (see generate()/
     * decryptCopy()).
     */
    protected function fileName(Property $property, string $copyType, string $timestamp): string
    {
        return "gem-property-listing-agreement-{$property->id}-{$timestamp}-{$copyType}.enc";
    }

    public function hasGeneratedCopies(Property $property): bool
    {
        return PropertyContract::where('property_id', $property->id)->count() === 2;
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
     * Finalizes the contract: renders it fresh from whatever property/
     * member/agent data currently exists in the database, then envelope-
     * encrypts it separately for each recipient copy - a single random
     * 256-bit Key-1 shared by both copies (they're byte-identical content,
     * just two recipients' copies of the same executed document), each copy
     * still getting its own random IV so the ciphertexts differ regardless.
     * Key-1 itself is "masked" via Laravel's own Crypt facade, which wraps
     * it with the application's root APP_KEY - never stored anywhere in the
     * clear. The plaintext PDF is never written to disk at any point; only
     * IV + GCM auth tag + ciphertext ever touch the filesystem. Returns the
     * plaintext bytes so the caller can email them directly from memory on
     * the one occasion it needs to (see PropertyWizardController::
     * finalizeContract()/notifyContractFinalized() - only the first call
     * for a given property actually emails anything) without ever
     * re-reading/decrypting the file it just wrote.
     *
     * Callable any number of times, not just once - each call writes a new
     * timestamped file under contracts/ and repoints that property's
     * PropertyContract rows at it (updateOrCreate on property_id+copy_type),
     * so the previously-current file is simply superseded rather than
     * deleted or reused.
     */
    public function generate(Property $property): string
    {
        $plaintext = $this->renderPdf($property);
        $key1 = random_bytes(32);
        $maskedKey = Crypt::encryptString($key1);
        $timestamp = now()->format('YmdHis');

        foreach (['member', 'agent'] as $copyType) {
            $iv = random_bytes(12);
            $tag = '';
            $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $key1, OPENSSL_RAW_DATA, $iv, $tag);
            $path = 'contracts/' . $this->fileName($property, $copyType, $timestamp);

            Storage::disk('local')->put($path, $iv . $tag . $ciphertext);

            PropertyContract::updateOrCreate(
                ['property_id' => $property->id, 'copy_type' => $copyType],
                ['file_path' => $path, 'masked_key' => $maskedKey]
            );
        }

        return $plaintext;
    }

    /**
     * Decrypts one party's finalized copy for viewing/downloading - reads
     * the encrypted bytes, unmasks Key-1 via APP_KEY, decrypts in memory,
     * and returns the plaintext PDF bytes without writing anything back to
     * disk. Returns null if there's no finalized copy for this property/
     * copy type, or if decryption fails (GCM auth tag mismatch - tampered
     * or corrupted file - rather than silently returning garbage).
     */
    public function decryptCopy(Property $property, string $copyType): ?string
    {
        $row = PropertyContract::where('property_id', $property->id)
            ->where('copy_type', $copyType)
            ->first();

        if (! $row || ! Storage::disk('local')->exists($row->file_path)) {
            return null;
        }

        $raw = Storage::disk('local')->get($row->file_path);
        $iv = substr($raw, 0, 12);
        $tag = substr($raw, 12, 16);
        $ciphertext = substr($raw, 28);
        $key1 = Crypt::decryptString($row->masked_key);

        $plaintext = openssl_decrypt($ciphertext, 'aes-256-gcm', $key1, OPENSSL_RAW_DATA, $iv, $tag);

        return $plaintext === false ? null : $plaintext;
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
            'agentSignatureDataUri' => $agent ? $agent->signature_data_uri : null,
            'agentDate' => $agent && $agent->signature_created_at
                ? $agent->signature_created_at->format('d/m/Y')
                : null,
            'propertyAddress' => $property->location,
            'propertyType' => optional($property->category)->name,
            'propertySize' => $property->square_text,
            'agreementDate' => now()->format('d / m / Y'),
        ];
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
