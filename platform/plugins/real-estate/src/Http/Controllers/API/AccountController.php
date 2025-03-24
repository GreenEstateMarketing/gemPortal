<?php

namespace Botble\RealEstate\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\description_template;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Http\Resources\AccountResource;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Exception;
use Botble\RealEstate\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Log;
use RvMedia;
use DB;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * @var AccountInterface
     */
    protected $accountRepository;

    /**
     * AuthenticationController constructor.
     *
     * @param AccountInterface $accountRepository
     */
    public function __construct(AccountInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * Get the user profile information.
     *
     * @group Profile
     * @authenticated
     *
     * @param Request $request
     * @param BaseHttpResponse $response
     *
     * @return BaseHttpResponse
     */
    public function getProfile(Request $request, BaseHttpResponse $response)
    {
        $user = $request->user();

        return $response->setData(new AccountResource($user));
    }

    /**
     * Update Avatar
     *
     * @bodyParam avatar file required Avatar file.
     *
     * @group Profile
     * @authenticated
     *
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function updateAvatar(Request $request, BaseHttpResponse $response)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return $response
                ->setError()
                ->setCode(422)
                ->setMessage(__('Data invalid!') . ' ' . implode(' ', $validator->errors()->all()) . '.');
        }

        try {

            $file = RvMedia::handleUpload($request->file('avatar'), 0, 'accounts');
            if (Arr::get($file, 'error') !== true) {
                $user = $this->accountRepository->createOrUpdate(
                    ['avatar' => $file['data']->url],
                    ['id' => $request->user()->getKey()]
                );
            }

            return $response
                ->setData([
                    'avatar' => $user->avatar_url,
                ])
                ->setMessage(__('Update avatar successfully!'));

        } catch (Exception $ex) {
            return $response
                ->setError()
                ->setMessage($ex->getMessage());
        }
    }

    /**
     * Update profile
     *
     * @bodyParam first_name string required First name.
     * @bodyParam last_name string required Last name.
     * @bodyParam email string Email.
     * @bodyParam dob string required Date of birth.
     * @bodyParam gender string Gender
     * @bodyParam description string Description
     * @bodyParam phone string required Phone.
     *
     * @group Profile
     * @authenticated
     *
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function updateProfile(Request $request, BaseHttpResponse $response)
    {
        $userId = $request->user()->getKey();

        $validator = Validator::make($request->input(), [
            'first_name' => 'required|max:120|min:2',
            'last_name' => 'required|max:120|min:2',
            'phone' => 'required|max:15|min:8',
            'dob' => 'required|max:15|min:8',
            'gender' => 'nullable',
            'description' => 'nullable',
            'email' => 'nullable|max:60|min:6|email|unique:re_accounts,email,' . $userId,
        ]);

        if ($validator->fails()) {
            return $response
                ->setError()
                ->setCode(422)
                ->setMessage(__('Data invalid!') . ' ' . implode(' ', $validator->errors()->all()) . '.');
        }

        try {
            $user = $this->accountRepository->createOrUpdate($request->input(), ['id' => $userId]);

            return $response
                ->setData($user->toArray())
                ->setMessage(__('Update profile successfully!'));

        } catch (Exception $ex) {
            return $response
                ->setError()
                ->setMessage($ex->getMessage());
        }
    }

    /**
     * Update password
     *
     * @bodyParam password string required The new password of account.
     *
     * @group Profile
     * @authenticated
     *
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function updatePassword(Request $request, BaseHttpResponse $response)
    {
        $validator = Validator::make($request->input(), [
            'password' => 'required|min:6|max:60',
        ]);

        if ($validator->fails()) {
            return $response
                ->setError()
                ->setCode(422)
                ->setMessage(__('Data invalid!') . ' ' . implode(' ', $validator->errors()->all()) . '.');
        }

        $currentUser = $request->user();

        $this->accountRepository->update(['id' => $currentUser->getKey()], [
            'password' => bcrypt($request->input('password')),
        ]);

        return $response->setMessage(trans('core/acl::users.password_update_success'));
    }
    public function agent_list(Request $request)
    {
        $lat = $request->latitude;
        $lng = $request->longitude;
        if ($lat && $lng) {
            $po = "'" . 'POINT(' . $lat . ' ' . $lng . ')' . "'";
            //SELECT ST_Within(ST_GEOMFROMTEXT('POINT($lat $lng)'),agent_area) as ceck,id FROM `re_accounts` WHERE id=33
            $col = '*,ST_Within(ST_GEOMFROMTEXT(' . $po . '),agent_area) as ceck,id';
            $w = 'ST_Within(ST_GEOMFROMTEXT(' . $po . ',4326),agent_area)=1';

//            $res = Account::select(['re_accounts.id', 'first_name', 'last_name', 'rating'])->leftJoin('ratings', 're_accounts.id', '=', 'ratings.agent_id')->whereNotNull('confirmed_at')->whereRaw($w)->groupBy('re_accounts.id')->orderBy('rating', 'DESC')->get();

            $res = Account::select([
                're_accounts.id',
                'first_name',
                'last_name',
                \DB::raw('ROUND(AVG(ratings.rating), 1) as rating')
            ])
                ->leftJoin('ratings', 're_accounts.id', '=', 'ratings.agent_id')
                ->whereNotNull('confirmed_at')
                ->whereRaw($w)
                ->groupBy('re_accounts.id', 'first_name', 'last_name')
                ->orderBy('rating', 'DESC')
                ->get();

            foreach ($res as $k => $val) {
                $res[$k]->img_src = $val->getAvatarUrlAttribute();
                if(strlen($val->first_name) + strlen($val->last_name) > 10) {
                    $val->first_name = $val->first_name[0];
                }
            }
            echo json_encode($res);
        } else {
            echo json_encode([]);
        }

    }
    public function agent_data(Request $request)
    {
        $id = $request->id;
        $res = Account::where('confirmed_at', '!=', null)->where('id', $id)->get();
        $res[0]->avatar_url = $res[0]->getAvatarUrlAttribute();
        unset($res[0]->agent_area);
        echo json_encode($res[0]);
    }
    public function getTemplate(Request $request)
    {
        $id = $request->category_id;
        $res = description_template::where('status', '=', '1')->where('category_id', $id)->first();
        if ($res) {
            $arr = array('status' => true, 'html' => $res);
        } else {
            $arr = array('status' => false, 'html' => $res);
        }
        echo json_encode($arr);
    }
    public function area_units(Request $request)
    {
        $area = $request->area;
        $unit = $request->unit;
        $res = getLandAreaUnits($area, $unit);
        $arr = array('status' => true, 'html' => $res);
        echo json_encode($arr);
    }


}
