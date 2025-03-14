<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Traits\HasDeleteManyItemsTrait;
use Botble\RealEstate\Forms\MemberForm;
use Botble\RealEstate\Http\Requests\MemberEditRequest;
use Botble\RealEstate\Models\Member;
use Botble\RealEstate\Repositories\Interfaces\MemberInterface;
use Botble\RealEstate\Tables\MemberTable;
use Botble\Base\Forms\FormBuilder;

class MemberController extends BaseController
{
    use HasDeleteManyItemsTrait;

    protected $memberRepository;

    public function __construct(MemberInterface $memberRepository)
    {
        $this->memberRepository = $memberRepository;
    }

    public function index(MemberTable $dataTable)
    {
        page_title()->setTitle('Members');

        return $dataTable->renderTable();
    }

    public function edit($id, FormBuilder $formBuilder)
    {
        $member = $this->memberRepository->findOrFail($id);
        page_title()->setTitle(trans('Edit', ['name' => $member->full_name]));
        $member->password = null;
        return $formBuilder
            ->create(MemberForm::class, ['model' => $member])
            ->renderForm();
    }

    public function update($id, MemberEditRequest $request, BaseHttpResponse $response)
    {
        $member = Member::find($id);
        $member->update($request->input());

        event(new UpdatedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $member));

        return $response
            ->setPreviousUrl(route('member.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
}
