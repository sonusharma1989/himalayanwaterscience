<?php

namespace Hws\FieldService\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Hws\FieldService\Models\SiteSurvey;
use Hws\FieldService\Models\SurveyInquiryType;

class SurveyController extends Controller
{
    /**
     * Submit or Update a Site Survey (independent of tasks).
     */
    public function submitSurvey(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'                  => 'nullable|integer|exists:hws_site_surveys,id',
            'customer_name'       => 'required|string',
            'customer_phone'      => 'nullable|string',
            'customer_address'    => 'nullable|string',
            'property_type'       => 'required|in:hotel,hospital,bungalow,other',
            'sales_type'          => 'nullable|in:trading,projects,services',
            'floors'              => 'nullable|integer',
            'built_up_area_sqft'  => 'nullable|integer',
            'rooms_units'         => 'nullable|integer',
            'water_use_kld'       => 'nullable|numeric',
            'water_source'        => 'nullable|in:municipal,borewell,tanker,river',
            'wastewater_disposal' => 'nullable|in:septic_tank,open_drain,existing_stp,none',
            'space_available'     => 'nullable|in:open_area,limited,basement_only,not_sure',
            'notes'               => 'nullable|string',
            'follow_up_date'      => 'nullable|date',
            'latitude'            => 'nullable|numeric',
            'longitude'           => 'nullable|numeric',
            'status'              => 'nullable|in:draft,submitted',
            'inquiry_types'       => 'nullable|array',
            'inquiry_types.*'     => 'in:stp,wtp,etp,ro_plant,softener,amc_only',
            'photos'              => 'nullable|array',
            'photos.*'            => 'string', // Base64 photos
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $savedPhotos = [];

            // Handle Base64 photos uploading
            if ($request->has('photos')) {
                foreach ($request->input('photos') as $photoBase64) {
                    if (str_starts_with($photoBase64, 'http')) {
                        // Already uploaded photo URL
                        $savedPhotos[] = $photoBase64;
                    } elseif (preg_match('/^data:image\/(\w+);base64,/', $photoBase64, $type)) {
                        $photoData = substr($photoBase64, strpos($photoBase64, ',') + 1);
                        $photoData = base64_decode($photoData);
                        $extension = strtolower($type[1]);
                        $fileName = uniqid() . '.' . $extension;
                        $photoPath = 'tasks/photos/' . $fileName;
                        
                        \Storage::disk('public')->put($photoPath, $photoData);
                        
                        $savedPhotos[] = url('storage/' . $photoPath);
                    }
                }
            }

            // Create or update the survey record by its own ID
            $surveyData = [
                'customer_name'       => $request->input('customer_name'),
                'customer_phone'      => $request->input('customer_phone') ?? '',
                'customer_address'    => $request->input('customer_address') ?? '',
                'property_type'       => $request->input('property_type'),
                'sales_type'          => $request->input('sales_type', 'trading'),
                'floors'              => $request->input('floors'),
                'built_up_area_sqft'  => $request->input('built_up_area_sqft'),
                'rooms_units'         => $request->input('rooms_units'),
                'water_use_kld'       => $request->input('water_use_kld'),
                'water_source'        => $request->input('water_source'),
                'wastewater_disposal' => $request->input('wastewater_disposal'),
                'space_available'     => $request->input('space_available'),
                'notes'               => $request->input('notes'),
                'follow_up_date'      => $request->input('follow_up_date'),
                'status'              => $request->input('status', 'draft'),
                'latitude'            => $request->input('latitude'),
                'longitude'           => $request->input('longitude'),
                'photos'              => $savedPhotos,
            ];

            if ($request->filled('id')) {
                $survey = SiteSurvey::findOrFail($request->input('id'));
                $survey->update($surveyData);
            } else {
                $survey = SiteSurvey::create($surveyData);
            }

            // Handle inquiry types
            if ($request->has('inquiry_types')) {
                // Delete old ones first
                SurveyInquiryType::where('survey_id', $survey->id)->delete();

                foreach ($request->input('inquiry_types') as $type) {
                    SurveyInquiryType::create([
                        'survey_id'    => $survey->id,
                        'inquiry_type' => $type,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Site Survey saved successfully.',
                'survey'  => $survey,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'   => 'Failed to save survey. Please try again.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
