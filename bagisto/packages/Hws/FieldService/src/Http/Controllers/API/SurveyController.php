<?php

namespace Hws\FieldService\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Hws\FieldService\Models\Task;
use Hws\FieldService\Models\SiteSurvey;
use Hws\FieldService\Models\SurveyInquiryType;
use Hws\FieldService\Models\TaskPhoto;

class SurveyController extends Controller
{
    /**
     * Submit a site survey for a task.
     */
    public function submitSurvey(Request $request, $id)
    {
        $employeeId = auth()->guard('admin-api')->id();

        $task = Task::where('id', $id)
            ->where('assigned_to', $employeeId)
            ->first();

        if (!$task) {
            return response()->json(['error' => 'Task not found or not assigned to you.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'property_type'       => 'required|in:hotel,hospital,bungalow,other',
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
            'inquiry_types'       => 'nullable|array',
            'inquiry_types.*'     => 'in:stp,wtp,etp,ro_plant,softener,amc_only',
            'photos'              => 'nullable|array',
            'photos.*'            => 'string', // Frontend sends photos as Base64 strings
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Create or update the survey record
            $survey = SiteSurvey::updateOrCreate(
                ['task_id' => $task->id],
                [
                    'property_type'       => $request->input('property_type'),
                    'floors'              => $request->input('floors'),
                    'built_up_area_sqft'  => $request->input('built_up_area_sqft'),
                    'rooms_units'         => $request->input('rooms_units'),
                    'water_use_kld'       => $request->input('water_use_kld'),
                    'water_source'        => $request->input('water_source'),
                    'wastewater_disposal' => $request->input('wastewater_disposal'),
                    'space_available'     => $request->input('space_available'),
                    'notes'               => $request->input('notes'),
                    'follow_up_date'      => $request->input('follow_up_date'),
                    'status'              => 'submitted',
                    'latitude'            => $request->input('latitude'),
                    'longitude'           => $request->input('longitude'),
                ]
            );

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

            // Handle Base64 photos uploading
            if ($request->has('photos')) {
                foreach ($request->input('photos') as $photoBase64) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $photoBase64, $type)) {
                        $photoData = substr($photoBase64, strpos($photoBase64, ',') + 1);
                        $photoData = base64_decode($photoData);
                        $extension = strtolower($type[1]); // png, jpg, etc.
                        $fileName = uniqid() . '.' . $extension;
                        $photoPath = 'tasks/photos/' . $fileName;
                        
                        // Save image to public storage
                        \Storage::disk('public')->put($photoPath, $photoData);

                        TaskPhoto::create([
                            'task_id'   => $task->id,
                            'type'      => 'survey_site',
                            'file_path' => $photoPath,
                        ]);
                    }
                }
            }

            // Automatically advance task to 'done' (step 4) when survey is submitted
            $task->update(['step' => 4]);

            DB::commit();

            return response()->json([
                'message' => 'Survey submitted and task marked as completed successfully.',
                'survey'  => $survey,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'   => 'Failed to submit survey. Please try again.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
