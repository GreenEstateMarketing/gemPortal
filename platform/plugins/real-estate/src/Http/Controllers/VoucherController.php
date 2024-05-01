<?php

namespace Botble\RealEstate\Http\Controllers;
use App\Http\Controllers\Controller;
use BeyondCode\Vouchers\Models\Voucher;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Forms\VoucherForm;
use Botble\RealEstate\Models\Package;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\VoucherInterface;
use Botble\RealEstate\Repositories\Interfaces\WantedInterface;
use Botble\RealEstate\Tables\VoucherTable;
use Illuminate\Http\Request;
class VoucherController extends Controller
{
    protected $voucherRepository;

    /**
     * WantedController constructor.
     * @param CategoryInterface $wantedRepository
     */
    public function __construct(VoucherInterface $voucherRepository)
    {
        $this->voucherRepository =$voucherRepository;
    }
    public function create(Request  $request,FormBuilder $formBuilder){


        page_title()->setTitle(trans('plugins/real-estate::voucher.name'));

        return $formBuilder->create(VoucherForm::class)->renderForm();
    }
    public function save(Request  $request,BaseHttpResponse $response){

    $id=$request->model_id;
    $product=Package::findOrFail($id);

   // $discount_percent
    /*$voucher=$product->createVoucher(['discount_percent'=>10] , today()->addDays(7));
    $voucher=$product->createVoucher(['discount_percent'=>10] );*/
    /*$voucher=$product->createVouchers(2);*/
   // $voucher=$product->createVouchers(1,['discount_percent'=>$request->discount_percent]);
        //echo $request->code;exit;
        $voucher=$product->createVoucher(['discount_percent'=>$request->data] ,$request->expires_at );
        //overide code with custom input code
        $voucher_update = $this->voucherRepository->findOrFail($voucher->id);
        $voucher_update->code=$request->code;
        $this->voucherRepository->createOrUpdate($voucher_update);
        return $response
            ->setPreviousUrl(route('voucher.list'))
            ->setNextUrl(route('voucher.edit', $voucher->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
}
    public function view(Request  $request){
        page_title()->setTitle(trans('plugins/real-estate::voucher.name'));
        $id=$request->id;
        $voucher=Voucher::findOrFail($id);
        dd($voucher);
    }
    public function edit( FormBuilder $formBuilder, Request $request){

        page_title()->setTitle(trans('plugins/real-estate::voucher.name'));
        $id=$request->id;
        $voucher=Voucher::findOrFail($id);
        $data= json_decode( json_encode($voucher->data), true);
        $dp= str_replace('"', '',$data['discount_percent']);
        $voucher->data=$dp;
        // return $this->view('plugins/real-estate::voucher.edit',$voucher);
        return $formBuilder->create(VoucherForm::class, ['model' => $voucher])->renderForm();
    }
    public  function update($id,Request  $request,BaseHttpResponse $response){
        $voucher = $this->voucherRepository->findOrFail($id);
        $ar=array('discount_percent'=>$request->data);
        $json_ob=json_encode($ar);
        $request->data=$json_ob;
        $voucher->fill($request->input());
        $voucher->data=trim($json_ob,"");
        $this->voucherRepository->createOrUpdate($voucher);
        return $response
            ->setPreviousUrl(route('voucher.list'))
            ->setNextUrl(route('voucher.edit', $voucher->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }
    public function list(VoucherTable $table){

        page_title()->setTitle(trans('plugins/real-estate::voucher.name'));

        return $table->renderTable();

    }
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $voucher = $this->voucherRepository->findOrFail($id);

            $this->voucherRepository->delete($voucher);



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
            $voucher = $this->voucherRepository->findOrFail($id);
            $this->voucherRepository->delete($voucher);

        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
