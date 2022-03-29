<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use App\Models\ClearanceTemplateImage;
use App\Models\ClearanceTemplate;

use App\Models\ClearanceCategory;
use App\Models\ClearanceType;
use App\Models\Barangay;




class ClearanceTemplateController extends Controller
{

    public function store(Request $request){


        $validator = Validator::make($request->all(),[
            'clearance_type_id' => 'required|integer|min:1',
            'clearance_category_id' => 'required|integer|min:1',
            'barangay_id' => 'required|integer|min:1',
            'template_image_id' => 'required|numeric|min:1'
        ]);

        if($validator->fails()){
            return customResponse()
                ->data(null)
                ->message($validator->errors()->all()[0])
                ->failed()
                ->generate();
        }

        $categoryData = ClearanceCategory::find($request->clearance_category_id);
        if(empty($categoryData)){
            return customResponse()
                ->data(null)
                ->message("Category not found.")
                ->failed()
                ->generate();
        }

        $barangayData = Barangay::find($request->barangay_id);
        if(empty($barangayData)){
            return customResponse()
                ->data(null)
                ->message("Barangay not found.")
                ->failed()
                ->generate();
        }

        $clearanceData = ClearanceType::find($request->clearance_type_id);
        if(empty($clearanceData)){
            return customResponse()
                ->data(null)
                ->message("Type not found.")
                ->success()
                ->generate();
        }

        $templateData = ClearanceTemplateImage::find($request->template_image_id);
        if(empty($clearanceData)){
            return customResponse()
                ->data(null)
                ->message("Template not found.")
                ->success()
                ->generate();
        }

        $templateData = new ClearanceTemplate;
        $templateData->clearance_type_id = $request->clearance_type_id;
        $templateData->clearance_category_id = $request->clearance_category_id;
        $templateData->barangay_id = $request->barangay_id;
        $templateData->template_image_id = $request->template_image_id;
        $templateData->save();

        return customResponse()
                ->data(null)
                ->message("Clearance Template Added.")
                ->success()
                ->generate();

    }

    public function edit(Request $request, $id){




        $templateData = ClearanceTemplate::find($id);
        if(!empty($templateData)){
            return customResponse()
                ->data($templateData)
                ->message("Template Data.")
                ->success()
                ->generate();
        }else{
            return customResponse()
                ->data(null)
                ->message("Template found.")
                ->failed()
                ->generate();
        }

    }

    public function update(Request $request){

        $validator = Validator::make($request->all(),[
            'clearance_type_id' => 'required|integer|min:1',
            'clearance_category_id' => 'required|integer|min:1',
            'barangay_id' => 'required|integer|min:1',
            'template_image_id' => 'required|numeric|min:1',
            'id' => 'required|numeric|min:1'
        ]);


        if($validator->fails()){
            return customResponse()
                ->data(null)
                ->message($validator->errors()->all()[0])
                ->failed()
                ->generate();
        }

        $barangayData = Barangay::find($request->barangay_id);
        if(empty($barangayData)){
            return customResponse()
                ->data(null)
                ->message("Barangay not found.")
                ->failed()
                ->generate();
        }

        $categoryData = ClearanceCategory::find($request->clearance_category_id);
        if(empty($categoryData)){
            return customResponse()
                ->data(null)
                ->message("Category category not found.")
                ->failed()
                ->generate();
        }

        $clearanceData = ClearanceType::find($request->clearance_type_id);

        if(empty($clearanceData)){
            return customResponse()
                ->data(null)
                ->message("Clearance type not found.")
                ->success()
                ->generate();
        }

        $clearanceData = ClearanceTemplateImage::find($request->template_image_id);
        if(empty($clearanceData)){
            return customResponse()
                ->data(null)
                ->message("Template not found.")
                ->success()
                ->generate();
        }

        $templateData = ClearanceTemplate::find($request->id);
        if(!empty($templateData)){
            $templateData->clearance_type_id = $request->clearance_type_id;
            $templateData->clearance_category_id = $request->clearance_category_id;
            $templateData->template_image_id = $request->template_image_id;
            $templateData->barangay_id = $request->barangay_id;
            $templateData->save();
            return customResponse()
            ->data(null)
            ->message("Clearance Template updated.")
            ->success()
            ->generate();
        }else{
            return customResponse()
            ->data(null)
            ->message("Clearance Template not found.")
            ->failed()
            ->generate();
        }





    }

    public function delete(Request $request){

        $validator = Validator::make($request->all(),[
            'id' => 'required|integer|min:1'
        ]);


        if($validator->fails()){
            return customResponse()
            ->data(null)
            ->message($validator->errors()->all()[0])
            ->failed()
            ->generate();
        }

        $templateData = ClearanceTemplate::find($request->id);
        if(!empty($templateData)){
            $templateData->delete();
            return customResponse()
            ->data(null)
            ->message("Clearance Template deleted.")
            ->success()
            ->generate();
        }else{
            return customResponse()
            ->data(null)
            ->message("Clearance Template not found")
            ->failed()
            ->generate();
        }

    }

    public function templateList(Request $request) {
        $templates = ClearanceTemplate::select(
            'clearance_template.id',
            'clearance_template.barangay_id',
            'barangays.description as barangay_desc',
            'clearance_template.clearance_type_id',
            'clearance_type.clearance_name as clearance_type_desc',
            'clearance_template.clearance_category_id',
            'clearance_category.description as clearance_category_desc',
            'clearance_template.template_image_id',
            'clearance_template.created_at'
        )
        ->leftJoin('barangays', 'barangays.id', 'clearance_template.barangay_id')
        ->leftJoin('clearance_type', 'clearance_type.id', 'clearance_template.clearance_type_id')
        ->leftJoin('clearance_category', 'clearance_category.id', 'clearance_template.clearance_category_id')
        ->with("templateImg");

        if ($request->clearance_type_id) {
            $templates = $templates->where("clearance_template.clearance_type_id", $request->clearance_type_id);
        }

        if ($request->clearance_category_id) {
            $templates = $templates->where("clearance_template.clearance_category_id", $request->clearance_category_id);
        }

        if ($request->barangay_id) {
            $templates = $templates->where("clearance_template.barangay_id", $request->barangay_id);
        }

        $templates = $templates->paginate(
            (int) $request->get('per_page', 10),
            ['*'],
            'page',
            (int) $request->get('page', 1)
        );

        return customResponse()
            ->message("Clearance Tempaltes.")
            ->data($templates)
            ->success()
            ->generate();
    }

    public function templateImageList(Request $request){

        $return = array();
        $imageData = ClearanceTemplateImage::all();
        if(!empty($imageData)){
            foreach($imageData as $row){
                $path = $row->file_path.'/'.$row->file_name;
                $path = str_replace("public/", "/", $path);
                // $path = Storage::url($path);
                $return[] = array(
                    'id' => $row->id,
                    'description' => $row->description,
                    'path' => $path
                );
            }
        }else{
            return customResponse()
            ->data(null)
            ->message("Clearance template list not found.")
            ->failed()
            ->generate();
        }

        return customResponse()
            ->data($return)
            ->message("Clearance template list.")
            ->success()
            ->generate();

    }

    public function show(Request $request, $id){

        $templateData = ClearanceTemplate::find($id);


        if(!empty($templateData)){

            return customResponse()
                ->data($templateData)
                ->message("Template Data.")
                ->success()
                ->generate();
        }else{
            return customResponse()
                ->data(null)
                ->message("Template not  found.")
                ->failed()
                ->generate();
        }

    }


}
