<?php


namespace App\Http\Controllers\Api;


use App\Http\Models\Inverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InverterController extends ResponseController
{

    public function addInverter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plant_id' => 'required',
            'serial_no' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $inverter = new Inverter;
        $input = $request->all();
        $serialNo = $input['serial_no'];
        
        $serialValidation = Inverter::where('serial_no',$serialNo)->first();

        if($serialValidation){
           $new_inverter = Inverter::findOrFail($serialValidation->id);
           $invert = $new_inverter->update($input);
           return $this->sendResponse(1, 'Inverter updated successfully', $invert);
        }else {
        	$invert = $inverter->fill($input)->save();
        	return $this->sendResponse(1, 'Inverter added successfully', $invert);
    	}
    }


}
