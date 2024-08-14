<?php

namespace Botble\Contact\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Contact\Events\SentContactEvent;
use Botble\Contact\Http\Requests\ContactRequest;
use Botble\Contact\Repositories\Interfaces\ContactInterface;
/*use Botble\Support\Http\Requests\Request;*/
use Botble\RealEstate\Models\Wanted;
use EmailHandler;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;


class PublicController extends Controller
{
    /**
     * @var ContactInterface
     */
    protected $contactRepository;

    /**
     * @param ContactInterface $contactRepository
     */
    public function __construct(ContactInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    /**
     * @param ContactRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postSendContact(ContactRequest $request, BaseHttpResponse $response)
    {
        try {
            $contact = $this->contactRepository->getModel();
            $contact->fill($request->input());
            $this->contactRepository->createOrUpdate($contact);

            event(new SentContactEvent($contact));

            EmailHandler::setModule(CONTACT_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'contact_name' => $contact->name ?? 'N/A',
                    'contact_subject' => $contact->subject ?? 'N/A',
                    'contact_email' => $contact->email ?? 'N/A',
                    'contact_phone' => $contact->phone ?? 'N/A',
                    'contact_address' => $contact->address ?? 'N/A',
                    'contact_content' => $contact->content ?? 'N/A',
                ])
                ->sendUsingTemplate('notice');

            return $response->setMessage(__('Your message sent successfully!'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/contact::contact.email.failed'));
        }
    }
    public function postSendWanted(Request $request, BaseHttpResponse $response)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required',
                'category_id' => 'required',
                'name' => 'required|string|min:3|max:100|regex:^[a-zA-Z]{3,}(?: [a-zA-Z]+){0,2}$^',
                'email' => 'required|email|string',
                'mobile_no' => 'required|min:11|numeric|regex:^[0][\d]{3}[\d]{7}$^',
                'city_id' => 'required|not_in:0',
                'city_area_id' => 'not_in:0',
                'comments' => 'required|string|min:5|max:255',
                'amount' => 'required_if:type,project',
                'project_select' => 'required_if:type,project_without:new_project_value',
                'new_project_value' => 'required_if:type,project_without:project_select',
            ], [
                'city_id.required' => 'City field is required',
                'city_area_id.required' => 'City area field is required',
                'city_id.not_in' => 'Choose city from list',
                'city_area_id.not_in' => 'Choose city area from list',
                'amount.required_if' => 'Amount is required when type is project',
                'project_select.required_if' => 'You must select a project or provide a new project value',
                'new_project_value.required_if' => 'You must provide a new project value or select a project',
            ]);


            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            $data = $request->all();
            $cityAreaId = $data['city_area_id'];
            unset($data['city_area_id']);
            $data['area'] = $cityAreaId;

            if (isset($data['new_project']) && $data['new_project'] == 'on') {
                $data['project_name'] = $data['new_project_value'];
            } else {
                $data['project_name'] = $data['project_select'];
            }

            unset($data['new_project']);
            unset($data['new_project_value']);
            unset($data['project_select']);


            $response = Wanted::create($data);


            //  EmailHandler::setModule(CONTACT_MODULE_SCREEN_NAME)
            //     ->setVariableValues([
            //         'type'    => $request->type ?? 'N/A',
            //         'category_id' => $request->category_id ?? 'N/A',
            //         'city'   => $request->city_id ?? 'N/A',
            //         'city_area'   => $request->city_area_id ?? 'N/A',
            //         'phone' => $request->mobile_no ?? 'N/A',
            //         'name' => $request->name ?? 'N/A',
            //         'email' => $request->email ?? 'N/A',
            //         'message' => $request->comments ?? 'N/A',
            //     ])
            //     ->sendUsingTemplate('notice');

            return response()->json(['success' => 'Your Wanted property details submit successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
