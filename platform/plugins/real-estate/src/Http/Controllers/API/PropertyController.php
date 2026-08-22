<?php

namespace Botble\RealEstate\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\catgeories_document;
use App\Models\document;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Models\Account;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\table_properties_check_lists;
use Botble\RealEstate\Models\Property;
use Illuminate\Support\Facades\Validator;
use RvMedia;

class PropertyController extends Controller
{
    function __construct()
    {
    }
    function getChecklist(Request $request)
    {
        $property_id = $request['property_id'];
        $res = table_properties_check_lists::where('property_id', '=', $property_id)->get();

        /* if(!empty($res)) {
             $d=$res[0]->document_checklist;
             $dd=json_decode($d,true);
             $res->document_checklist=$dd;
         }*/

        if (count($res) > 0) {
            //$check_list=$res[0]->document_checklist;
            //$res=array('documents'=>json_decode($check_list,true));
            echo json_encode(array('status' => 1, 'data' => $res, 'message' => 'get success'));
        } else {

            echo json_encode(array('status' => 0, 'message' => 'unable to get'));
        }
    }
    function updateChecklist(Request $request)
    {
        $verify_document = $request['verify_document'];
        $property_id = $request['property_id'];
        $category_id = $request['category_id'];
        $value = 0;
        $prop_count = catgeories_document::where('category_id', '=', $category_id)->with('documents')->whereHas('documents', function ($q) use ($value) {
            $q->where('is_delete', '=', $value);
        })->get();
        $res = table_properties_check_lists::where('property_id', '=', $property_id)->get();
        if (count($res) > 0) {
            $arr = array('document_checklist' => json_encode($verify_document, true), 'is_verify' => 0);
            $resupdate = table_properties_check_lists::where('property_id', $property_id)->update($arr);
            if ($resupdate) {
                $approved = false;
                if (count($verify_document) == count($prop_count)) {
                    $verify = array('is_verify' => 1);
                    $resupdate = table_properties_check_lists::where('property_id', $property_id)->update($verify);
                    $approved = true;


                }
                //all one then moderation_status=Approved
                echo json_encode(array('status' => 1, 'message' => 'Updated success', 'approved' => $approved));
            } else
                echo json_encode(array('status' => 0, 'message' => 'unbale to update'));
        } else {
            $arr = array('document_checklist' => json_encode($verify_document, true), 'property_id' => $property_id, 'is_verify' => 0);
            $res = table_properties_check_lists::create($arr);
            if ($res) {
                $approved = false;
                if (count($verify_document) == count($prop_count)) {
                    $verify = array('is_verify' => 1);
                    $resupdate = table_properties_check_lists::where('property_id', $property_id)->update($verify);
                    $approved = true;


                }
                echo json_encode(array('status' => 1, 'message' => 'added success', 'approved' => $approved));
            } else
                echo json_encode(array('status' => 0, 'message' => 'unbale to add'));
        }


    }
    function assignChecklist(Request $request)
    {
        $author_id = $request['author_id'];
        $property_id = $request['property_id'];
        $arr = array('author_id' => $author_id);
        $approved = false;
        $res = table_properties_check_lists::where('property_id', '=', $property_id)->get();
        if (count($res) > 0) {
            $verify = $res[0]->is_verify;
            if ($verify == 1)
                $approved = true;
        }

        $rpropupdate = Property::where('id', $property_id)->update(array('author_id' => $author_id, 'author_type' => Account::class));
        if ($rpropupdate) {


            //all one then moderation_status=Approved
            echo json_encode(array('status' => 1, 'message' => 'assigned success', 'approved' => $approved));
        } else
            echo json_encode(array('status' => 0, 'message' => 'unbale to update'));



    }
    function getChecklistDocuments(Request $request)
    {
        $category_id = $request['category_id'];
        $property_id = $request['property_id'];
        $value = 0;
        $ress = catgeories_document::where('category_id', '=', $category_id)->orderBy('required', 'desc')->with(['documents'])
            ->whereHas('documents', function ($q) use ($value) {
                // Query the name field in status table
                $q->where('is_delete', '=', $value); // '=' is optional
            })->get(); ///*->with('documents')->where('documents.is_delete',0)*/
        $document_images = null;
        if ($property_id > 0) {
            $res = Property::select('documents')->where('id', '=', $property_id)->where('category_id', $category_id)->get();
            if (count($res) > 0)
                $document_images = $res[0]->documents;
        }
        $ar = array('document_images' => json_decode($document_images, true), 'documents' => $ress);
        if (count($ress) > 0) {
            echo json_encode(array('status' => 1, 'data' => $ar, 'message' => 'get success'));
        } else {

            echo json_encode(array('status' => 0, 'message' => 'unable to get'));
        }
    }
    function getDocumentDetails(Request $request)
    {
        $document_id = $request['document_id'];
        $res = document::where('id', '=', $document_id)->with('category_document')->get();
        if (count($res) > 0) {
            echo json_encode(array('status' => 1, 'data' => $res, 'message' => 'get success'));
        } else {

            echo json_encode(array('status' => 0, 'message' => 'unable to get'));
        }
    }
}
