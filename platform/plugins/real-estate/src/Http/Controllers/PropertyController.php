<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Models\Buyer;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\MemberInterface;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\FeatureInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\RealEstate\Tables\PropertyTable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;
use Illuminate\Support\Facades\Storage;
use SeoHelper;
use EmailHandler;

use Theme;
use function React\Promise\all;

class PropertyController extends BaseController
{
    /**
     * @var PropertyInterface $propertyRepository
     */
    protected $propertyRepository;

    /**
     * @var ProjectInterface
     */
    protected $projectRepository;

    /**
     * @var FeatureInterface
     */
    protected $featureRepository;

    /**
     * PropertyController constructor.
     * @param PropertyInterface $propertyRepository
     * @param ProjectInterface $projectRepository
     * @param FeatureInterface $featureRepository
     */
    public function __construct(
        PropertyInterface $propertyRepository,
        ProjectInterface  $projectRepository,
        FeatureInterface  $featureRepository
    )
    {
        $this->propertyRepository = $propertyRepository;
        $this->projectRepository = $projectRepository;
        $this->featureRepository = $featureRepository;
    }

    /**
     * @param PropertyTable $dataTable
     * @return JsonResponse|View
     * @throws Throwable
     */
    public function index(PropertyTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/real-estate::property.name'));

        return $dataTable->renderTable();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy($id, Request $request, BaseHttpResponse $response)
    {
        try {
            $property = $this->propertyRepository->findOrFail($id);
            $property->features()->detach();
            $this->propertyRepository->delete($property);

            event(new DeletedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.cannot_delete'));
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $property = $this->propertyRepository->findOrFail($id);
            $property->features()->detach();
            $this->propertyRepository->delete($property);

            event(new DeletedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function agent_search()
    {

        /* if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.agent-search')) {

             return Theme::scope('real-estate.agent-search')->render();
         }

         return view('plugins/real-estate::agent-search');*/
        SeoHelper::setTitle(trans('plugins/real-estate::account.wanted'));
        $data = array();
        // print_r($cityChoices);exit;
        if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.agent-search')) {

            return Theme::scope('real-estate.agent-search', $data)->render();

            return view('plugins/real-estate::agent-search', $data);
        }
    }

    public function mailForPayment(Request $request, AccountInterface $accountRepo, MemberInterface $memberRepo, BaseHttpResponse $response)
    {
        try {
            if ($request->has('id') && $request->has('type')) {
                $type = $request->input('type');
                $id = $request->get('id');
                $propertyId = $request->get('property_id');
                $title = $request->get('title');
                $from = $request->get('from');

                $variables = [
                    'name' => 'Name',
                    'property_url' => 'Property Url',
                    'title' => 'Title',
                    'credits_url' => 'Credits Url'
                ];

                if ($type == 'agent') {
                    $account = $accountRepo->findOrFail($id);
                    if ($account) {
                        EmailHandler::setModule('real-estate')
                            ->addVariables($variables)
                            ->setVariableValues([
                                'name' => $account->first_name . ' ' . $account->last_name,
                                'property_url' => route('public.account.properties.edit', ['property' => $propertyId]),
                                'title' => $title,
                                'credits_url' => route('public.account.packages'),
                            ])
                            ->sendUsingTemplate('paymentmail', $account->email, [], false, 'plugins', 'GEM - Payment Pending');

                        if ($from == 'agent') {
                            return $response
                                ->setPreviousUrl(route('public.account.properties.edit', ['property' => $propertyId]))
                                ->setNextUrl(route('public.account.properties.edit', ['property' => $propertyId]))
                                ->setMessage('Email has been sent.');
                        } else {
                            return $response
                                ->setPreviousUrl(route('property.edit', ['property' => $propertyId]))
                                ->setNextUrl(route('property.edit', ['property' => $propertyId]))
                                ->setMessage('Email has been sent.');
                        }


                    }
                } else if ($type == 'member') {
                    $member = $memberRepo->findOrFail($id);
                    if ($member) {
                        EmailHandler::setModule('real-estate')
                            ->addVariables($variables)
                            ->setVariableValues([
                                'name' => $member->full_name,
                                'property_url' => route('public.member.properties.edit', ['id' => $propertyId]),
                                'title' => $title,
                                'credits_url' => route('public.member.packages'),
                            ])
                            ->sendUsingTemplate('paymentmail', $member->email, [], false, 'plugins', 'GEM - Payment Pending');

                        if ($from == 'agent') {
                            return $response
                                ->setPreviousUrl(route('public.account.properties.edit', ['property' => $propertyId]))
                                ->setNextUrl(route('public.account.properties.edit', ['property' => $propertyId]))
                                ->setMessage('Email has been sent.');
                        } else {
                            return $response
                                ->setPreviousUrl(route('property.edit', ['property' => $propertyId]))
                                ->setNextUrl(route('property.edit', ['property' => $propertyId]))
                                ->setMessage('Email has been sent.');
                        }
                    }
                } else {
                    return $response
                        ->setError()
                        ->setMessage('Something went wrong. Cannot send email.');
                }
            }
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage('Something went wrong. Cannot send email');
        }

    }

    public function saveBuyerInfo(Request $request)
    {
        // Step 1: Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^\+?[1-9][0-9]{7,14}$/'],
            'email' => 'required|email',
            'amount' => 'required|numeric|min:0',
        ], [
            'phone.regex' => 'The phone number format is invalid. It must be a valid international number, e.g., +1234567890.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Step 2: Check for existing buyer by property_id
            $buyer = Buyer::where('property_id', $request->input('property_id'))->first();

            if ($buyer) {
                // Update existing buyer
                $buyer->update([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'seller_id' => $request->input('seller_id'),
                    'agent_id' => $request->input('agent_id'),
                    'amount' => $request->input('amount'),
                    'transaction_type' => $request->input('transaction_type'),
                ]);

                return response()->json([
                    'message' => 'Buyer info updated successfully!',
                    'buyer' => $buyer
                ]);
            } else {
                // Create new buyer
                $buyer = Buyer::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'property_id' => $request->input('property_id'),
                    'seller_id' => $request->input('seller_id'),
                    'agent_id' => $request->input('agent_id'),
                    'amount' => $request->input('amount'),
                    'transaction_type' => $request->input('transaction_type'),
                ]);

                return response()->json([
                    'message' => 'Buyer info saved successfully! Now click the Save button under Publish.',
                    'buyer' => $buyer
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save buyer info. Please try again later.'
            ], 500);
        }
    }
}
