<?php

namespace Botble\RealEstate\Http\Controllers;

use Assets;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Repositories\Interfaces\MemberInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Repositories\Interfaces\AccountActivityLogInterface;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Tables\AccountPropertyTable;
use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use SeoHelper;
use EmailHandler;

class AccountPropertyController extends Controller
{
    /**
     * @var AccountInterface
     */
    protected $accountRepository;

    /**
     * @var PropertyInterface
     */
    protected $propertyRepository;

    /**
     * @var AccountActivityLogInterface
     */
    protected $activityLogRepository;

    /**
     * PublicController constructor.
     * @param Repository $config
     * @param AccountInterface $accountRepository
     * @param PropertyInterface $propertyRepository
     * @param AccountActivityLogInterface $accountActivityLogRepository
     */
    public function __construct(
        Repository $config,
        AccountInterface $accountRepository,
        PropertyInterface $propertyRepository,
        AccountActivityLogInterface $accountActivityLogRepository
    ) {
        $this->accountRepository = $accountRepository;
        $this->propertyRepository = $propertyRepository;
        $this->activityLogRepository = $accountActivityLogRepository;

        Assets::setConfig($config->get('plugins.real-estate.assets'));
    }

    /**
     * @param Request $request
     * @param AccountPropertyTable $propertyTable
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Response
     * @throws \Throwable
     */
    public function index(AccountPropertyTable $propertyTable)
    {
        SeoHelper::setTitle(__('Properties'));

        return $propertyTable->render('plugins/real-estate::account.table.base');
    }

    /**
     * @param $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function destroy($id, BaseHttpResponse $response)
    {
        $property = $this->propertyRepository->getFirstBy([
            'id' => $id,
            'author_id' => auth('account')->user()->getAuthIdentifier(),
            'author_type' => Account::class,
        ]);

        if (!$property) {
            abort(404);
        }

        $this->propertyRepository->delete($property);

        $this->activityLogRepository->createOrUpdate([
            'action' => 'delete_property',
            'reference_name' => $property->name,
        ]);

        //Send Email        
        //send to self
        $variables = [
            'name' => 'Name',
            'property_url' => 'Property Url',
            'by' => 'By',
            'title' => 'Title',
            'action' => 'Action'
        ];

        if ($property->author_id) {
            $author = $this->accountRepository->findOrFail($property->author_id);

            EmailHandler::setModule('real-estate')
                ->addVariables($variables)
                ->setVariableValues([
                    'name' => $author->first_name . ' ' . $author->last_name,
                    'property_url' => route('public.account.properties.edit', ['property' => $property->id]),
                    'by' => 'you',
                    'title' => $property->name,
                    'action' => 'deleted'
                ])
                ->sendUsingTemplate('propertymodify', $author->email, [], false, 'plugins', 'Property Deleted');
        }

        //send to admin
        EmailHandler::setModule('real-estate')
            ->addVariables($variables)
            ->setVariableValues([
                'name' => 'Admin',
                'property_url' => route('property.edit', ['property' => $property->id]),
                'by' => 'Agent: ' . $author->first_name . ' ' . $author->last_name,
                'title' => $property->name,
                'action' => 'deleted'
            ])
            ->sendUsingTemplate('propertymodify', 'admin@botble.com', [], false, 'plugins', 'Property Deleted');

        return $response->setMessage(__('Delete property successfully!'));
    }

    /**
     * @param $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function renew($id, BaseHttpResponse $response)
    {
        $job = $this->propertyRepository->findOrFail($id);

        $account = auth('account')->user();

        if ($account->credits < 1) {
            return $response->setError(true)->setMessage(__('You don\'t have enough credit to renew this property!'));
        }

        $job->expire_date = $job->expire_date->addDays(config('plugins.real-estate.real-estate.property_expired_after_x_days'));
        $job->save();

        $account->credits--;
        $account->save();

        return $response->setMessage(__('Renew property successfully'));
    }

    public function verify($id, BaseHttpResponse $response, MemberInterface $memberRepository)
    {
        $property = $this->propertyRepository->findOrFail($id);

        $account = auth('account')->user();

        if ($property->author_id == $account->id) {
            $property->verified = true;
            $property->save();

            //send emails to corresponding people
            $variables = [
                'name' => 'Name',
                'property_url' => 'Property Url',
                'by' => 'By',
                'title' => 'Title',
                'action' => 'Action'
            ];

            EmailHandler::setModule('real-estate')
            ->addVariables($variables)
            ->setVariableValues([
                'name' => 'Admin',
                'property_url' => route('property.edit', ['property' => $property->id]),
                'by' => 'Agent: ' . $account->first_name . ' ' . $account->last_name,
                'title' => $property->name,
                'action' => 'verified'
            ])
            ->sendUsingTemplate('propertymodify', 'admin@botble.com', [], false, 'plugins', 'Property Verified');

            //to member if its owned by member
            if($property->member_id) {
                $member = $memberRepository->findOrFail($property->member_id);
                EmailHandler::setModule('real-estate')
                ->addVariables($variables)
                ->setVariableValues([
                    'name' => $member->full_name,
                    'property_url' => route('public.member.properties.edit', ['id' => $property->id]),
                    'by' => 'Agent: ' . $account->first_name . ' ' . $account->last_name,
                    'title' => $property->name,
                    'action' => 'verified'
                ])
                ->sendUsingTemplate('propertymodify', $member->email, [], false, 'plugins', 'Property Verified');
            }

            return $response
                ->setPreviousUrl(route('public.account.properties.edit', $property->id))
                ->setNextUrl(route('public.account.properties.edit', $property->id))
                ->setMessage('This property has been successfully verified by you.');
        } else {
            return $response
                ->setError()
                ->setMessage("Something went wrong. Couldn't verify property.");
        }
    }
}
