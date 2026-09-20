<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class MemberSettingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'full_name' => 'required|max:120',
            'mobile_no'      => 'required:max:20|sometimes|regex:/^([0-9\s\-\+\(\)]*)$/',
            'signature_file' => 'nullable|file|mimes:png',
            'signature_data' => 'nullable|string',

        ];
    }

    public function messages()
    {
        return [
            'signature_file.mimes' => trans('plugins/real-estate::account.signature_invalid'),
        ];
    }

    /**
     * A signature is mandatory only until the member has one on file -
     * once set, later settings saves may leave it untouched. Both
     * submission shapes (uploaded file vs. canvas-drawn base64 PNG) are
     * normalized and content-sniffed here so the controller can trust
     * getSignaturePayload().
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $submittedFile = $this->hasFile('signature_file');
            $submittedData = $this->filled('signature_data');

            if (! $submittedFile && ! $submittedData) {
                $member = auth('member')->user();

                if (! $member || ! $member->hasSignature()) {
                    $validator->errors()->add(
                        'signature_file',
                        trans('plugins/real-estate::account.signature_required')
                    );
                }

                return;
            }

            if (! $this->getSignaturePayload()) {
                $validator->errors()->add(
                    $submittedFile ? 'signature_file' : 'signature_data',
                    trans('plugins/real-estate::account.signature_invalid')
                );
            }
        });
    }

    /**
     * Normalize whichever signature input was submitted (an uploaded file
     * takes precedence over a stray leftover draw value) into raw PNG
     * bytes, content-sniffed via the PNG magic number - not just trusted
     * by file extension or the data: URI's declared mime type.
     *
     * @return array{bytes: string, source: string}|null
     */
    public function getSignaturePayload()
    {
        $pngMagic = "\x89PNG\r\n\x1a\n";

        if ($this->hasFile('signature_file')) {
            $file = $this->file('signature_file');

            if (! $file->isValid()) {
                return null;
            }

            $bytes = file_get_contents($file->getRealPath());

            if (substr($bytes, 0, 8) !== $pngMagic) {
                return null;
            }

            return ['bytes' => $bytes, 'source' => 'upload'];
        }

        if ($this->filled('signature_data')) {
            if (! preg_match('/^data:image\/png;base64,(.+)$/', $this->input('signature_data'), $matches)) {
                return null;
            }

            $bytes = base64_decode($matches[1], true);

            if ($bytes === false || substr($bytes, 0, 8) !== $pngMagic) {
                return null;
            }

            return ['bytes' => $bytes, 'source' => 'draw'];
        }

        return null;
    }
}
