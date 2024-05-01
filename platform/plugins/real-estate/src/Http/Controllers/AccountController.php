<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Traits\HasDeleteManyItemsTrait;
use Botble\RealEstate\Forms\AccountForm;
use Botble\RealEstate\Http\Requests\AccountCreateRequest;
use Botble\RealEstate\Http\Requests\AccountEditRequest;
use Botble\RealEstate\Http\Resources\AccountResource;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Tables\AccountTable;
use Exception;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class AccountController extends BaseController
{
    use HasDeleteManyItemsTrait;

    /**
     * @var AccountInterface
     */
    protected $accountRepository;

    /**
     * @param AccountInterface $accountRepository
     */
    public function __construct(AccountInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param AccountTable $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(AccountTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/real-estate::account.name'));

        return $dataTable->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/real-estate::account.create'));

        return $formBuilder
            ->create(AccountForm::class)
            ->remove('is_change_password')
            ->renderForm();
    }

    /**
     * @param AccountCreateRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(AccountCreateRequest $request, BaseHttpResponse $response)
    {
         $request->merge([
            'password'     => bcrypt($request->input('password')),
            'confirmed_at' =>Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $data=json_decode($request['agent_area']);
      //  dd($request['agent_area']);exit;die;
        $i=0;
        if($request['agent_area']!="") {
            $total_ar = count($data);
            // echo $total_ar;exit;
            if ($total_ar == 1)
                $po = 'POLYGON((';
            else
                $kp = 'MultiPolygon((';
            //'MultiPolygon(((0 0,0 3,3 3,3 0,0 0),(1 1,1 2,2 2,2 1,1 1)))';
            //MultiPolygon(('(33.636439241201 70.973612444285,33.723290719941 71.62180580366,33.636439241201 70.973612444285),(33.7735330363 72.28098549116,35.225962172694 73.277578568501,34.951777200511 74.716787552876,34.721834185944 74.041128373189,33.7735330363 72.28098549116)))',4326)
            $ap = 'ST_GeomFromText(';
            $mo = '';
            $first = '';
            foreach ($data as $k => $item) {
                $mo .= '(';
                $rp = '';
                $total = count($item);
                $q = 0;
                foreach ($item as $w => $kk) {
                    $first = $item[0]->lat . ' ' . $item[0]->lng;
                    if ($total_ar == 1) {
                        if ($i == $total - 1) {
                            /* echo $i; echo  't '.($total-1);
                             echo '<br>';*/
                            // $po .= $item[0]->lat . ' ' . $item[0]->lng; //first as last
                            $po .= $kk->lat . ' ' . $kk->lng;
                        } else
                            $po .= $kk->lat . ' ' . $kk->lng;
                        $po .= ',';
                        $i++;
                    } else {
                        if ($q == $total - 1) {
                            /* echo $i; echo  't '.($total-1);
                             echo '<br>';*/
                            $rp .= $kk->lat . ' ' . $kk->lng;
                            //$rp .= $item[0]->lat . ' ' . $item[0]->lng; //first as last
                            // $po.=$kk->lat.' '.$kk->lng;
                        } else
                            $rp .= $kk->lat . ' ' . $kk->lng;
                        $rp .= ',';
                        $q++;
                    }

                }
                //    echo $po;
                $rp .= $first;

                $mo .= rtrim($rp, ',');
                $mo .= '),';

            }
            if ($total_ar == 1) {
                $po .= $first;
                $ap .= "'";
                $ap .= rtrim($po, ',');
                $ap .= '))';
                $ap .= "',4326)";
            } else {
                /*$kp .= "'";

                $kp .= '))';
                $kp.="',4326)";
                //*/
                $kp .= rtrim($mo, ',');
                $ap .= "'";
                $ap .= rtrim($kp, ',');
                $ap .= '))';
                $ap .= "',4326)";

            }
            //  echo $ap;
            //   exit;
        }
        $account = Account::create($request->except('agent_area'));
        if($request['agent_area']!="") {
            $account->agent_area = \DB::raw($ap);
        }
        $account->confirmed_at=Carbon::now()->format('Y-m-d H:i:s');

      // echo $account->toSql();exit;
        $account->save();
        //dd($account);exit;
       // $account = $this->accountRepository->createOrUpdate($data)->toSql();;
       //  event(new CreatedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $account));

        return $response
            ->setPreviousUrl(route('account.index'))
            ->setNextUrl(route('account.edit', $account->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param $id
     * @param FormBuilder $formBuilder
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function edit($id, FormBuilder $formBuilder)
    {
        $account = $this->accountRepository->findOrFail($id);
        $name= $this->accountRepository->getPolygon($id);
       // echo $name;exit;
        page_title()->setTitle(trans('plugins/real-estate::account.edit', ['name' => $account->getFullName()]));
        $account->password = null;
        $account->coordinate=$name;
        return $formBuilder
            ->create(AccountForm::class, ['model' => $account])
            ->renderForm();

    }

    /**
     * @param $id
     * @param AccountEditRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, AccountEditRequest $request, BaseHttpResponse $response)
    {
        if ($request->input('is_change_password') == 1) {
            $request->merge(['password' => bcrypt($request->input('password'))]);
            $data = $request->input();
        } else {
            $data = $request->except('password');
        }
      //  echo $request['agent_area'];exit;
        //echo var_dump(is_array($request['agent_area']));exit;
        if($request['agent_area']!="") {
            //  dd($request['agent_area']);exit;die;
            $i = 0;
            $data_map=json_decode($request['agent_area']);

            $total_ar = count($data_map);
            // echo $total_ar;exit;
            if ($total_ar == 1)
                $po = 'POLYGON((';
            else
                $kp = 'MultiPolygon((';
            //'MultiPolygon(((0 0,0 3,3 3,3 0,0 0),(1 1,1 2,2 2,2 1,1 1)))';
            //MultiPolygon(('(33.636439241201 70.973612444285,33.723290719941 71.62180580366,33.636439241201 70.973612444285),(33.7735330363 72.28098549116,35.225962172694 73.277578568501,34.951777200511 74.716787552876,34.721834185944 74.041128373189,33.7735330363 72.28098549116)))',4326)
            $ap = 'ST_GeomFromText(';
            $mo = '';
            $first = '';
            foreach ($data_map as $k => $item) {
                $mo .= '(';
                $rp = '';
                $total = count($item);
                $q = 0;
                foreach ($item as $w => $kk) {
                    $first = $item[0]->lat . ' ' . $item[0]->lng;
                    if ($total_ar == 1) {
                        if ($i == $total - 1) {
                            /* echo $i; echo  't '.($total-1);
                             echo '<br>';*/
                            // $po .= $item[0]->lat . ' ' . $item[0]->lng; //first as last
                            $po .= $kk->lat . ' ' . $kk->lng;
                        } else
                            $po .= $kk->lat . ' ' . $kk->lng;
                        $po .= ',';
                        $i++;
                    } else {
                        if ($q == $total - 1) {
                            /* echo $i; echo  't '.($total-1);
                             echo '<br>';*/
                            $rp .= $kk->lat . ' ' . $kk->lng;
                            //$rp .= $item[0]->lat . ' ' . $item[0]->lng; //first as last
                            // $po.=$kk->lat.' '.$kk->lng;
                        } else
                            $rp .= $kk->lat . ' ' . $kk->lng;
                        $rp .= ',';
                        $q++;
                    }

                }
                //    echo $po;
                $rp .= $first;

                $mo .= rtrim($rp, ',');
                $mo .= '),';

            }
            if ($total_ar == 1) {
                $po .= $first;
                $ap .= "'";
                $ap .= rtrim($po, ',');
                $ap .= '))';
                $ap .= "',4326)";
            } else {
                /*$kp .= "'";

                $kp .= '))';
                $kp.="',4326)";
                //*/
                $kp .= rtrim($mo, ',');
                $ap .= "'";
                $ap .= rtrim($kp, ',');
                $ap .= '))';
                $ap .= "',4326)";

            }
            //  echo $ap;
            //   exit;
        }
        $account=Account::find($id);
        $account->update($request->except('agent_area','password'));
        if($request['agent_area']!="")
             $account->agent_area=\DB::raw($ap);
        if ($request->input('is_change_password') == 1) {

            $data = $request->input();
            $account->password=\bcrypt($request->input('password'));
        } else {
            $data = $request->except('password');
        }
        $account->save();
        /*$account = Account::update($request->except('agent_area'));
        $account->agent_area =\DB::raw($ap);
        $account->where('id',$id);*/
        //$account = $this->accountRepository->createOrUpdate($data, ['id' => $id]);
        event(new UpdatedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $account));

        return $response
            ->setPreviousUrl(route('account.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param Request $request
     * @param $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $account = $this->accountRepository->findOrFail($id);
            $this->accountRepository->delete($account);
            event(new DeletedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $account));

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
        return $this->executeDeleteItems($request, $response, $this->accountRepository, ACCOUNT_MODULE_SCREEN_NAME);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     */
    public function getList(Request $request, BaseHttpResponse $response)
    {
        $keyword = $request->input('q');

        if (!$keyword) {
            return $response->setData([]);
        }

        $data = $this->accountRepository->getModel()
            ->where('re_accounts.first_name', 'LIKE', '%' . $keyword . '%')
            ->orWhere('re_accounts.last_name', 'LIKE', '%' . $keyword . '%')
            ->select(['re_accounts.id', 're_accounts.first_name', 're_accounts.last_name'])
            ->take(10)
            ->get();

        return $response->setData(AccountResource::collection($data));
    }
    public function getAgentInAreas(Request $request)
    {
        $data=json_decode($request['agent_area']);
        //  dd($request['agent_area']);exit;die;
        $i=0;
        if($request['agent_area']!="") {
            $total_ar = count($data);
            // echo $total_ar;exit;
            if ($total_ar == 1)
                $po = 'POLYGON((';
            else
                $kp = 'MultiPolygon((';
            //'MultiPolygon(((0 0,0 3,3 3,3 0,0 0),(1 1,1 2,2 2,2 1,1 1)))';
            //MultiPolygon(('(33.636439241201 70.973612444285,33.723290719941 71.62180580366,33.636439241201 70.973612444285),(33.7735330363 72.28098549116,35.225962172694 73.277578568501,34.951777200511 74.716787552876,34.721834185944 74.041128373189,33.7735330363 72.28098549116)))',4326)
            $ap = 'ST_GeomFromText(';
            $mo = '';
            $first = '';
            foreach ($data as $k => $item) {
                $mo .= '(';
                $rp = '';
                $total = count($item);
                $q = 0;
                foreach ($item as $w => $kk) {
                    $first = $item[0]->lat . ' ' . $item[0]->lng;
                    if ($total_ar == 1) {
                        if ($i == $total - 1) {
                            /* echo $i; echo  't '.($total-1);
                             echo '<br>';*/
                            // $po .= $item[0]->lat . ' ' . $item[0]->lng; //first as last
                            $po .= $kk->lat . ' ' . $kk->lng;
                        } else
                            $po .= $kk->lat . ' ' . $kk->lng;
                        $po .= ',';
                        $i++;
                    } else {
                        if ($q == $total - 1) {
                            /* echo $i; echo  't '.($total-1);
                             echo '<br>';*/
                            $rp .= $kk->lat . ' ' . $kk->lng;
                            //$rp .= $item[0]->lat . ' ' . $item[0]->lng; //first as last
                            // $po.=$kk->lat.' '.$kk->lng;
                        } else
                            $rp .= $kk->lat . ' ' . $kk->lng;
                        $rp .= ',';
                        $q++;
                    }

                }
                //    echo $po;
                $rp .= $first;

                $mo .= rtrim($rp, ',');
                $mo .= '),';

            }
            if ($total_ar == 1) {
                $po .= $first;
                $ap .= "'";
                $ap .= rtrim($po, ',');
                $ap .= '))';
                $ap .= "',4326)";
            } else {
                /*$kp .= "'";

                $kp .= '))';
                $kp.="',4326)";
                //*/
                $kp .= rtrim($mo, ',');
                $ap .= "'";
                $ap .= rtrim($kp, ',');
                $ap .= '))';
                $ap .= "',4326)";

            }
            //DB::raw('ST_GeomFromText(agent_area) as agent_area')
          $res=Account::select(DB::raw('ST_AsGeoJson(agent_area) as agent_area'),'id','first_name','last_name','email','phone')->whereRaw("ST_CONTAINS(".$ap.",agent_area)")->get();
           foreach ($res as $k=>$val)
           {
               $res[$k]->avatar_url=$val->getAvatarUrlAttribute();
           }

            echo json_encode($res);
        }
    }
}
