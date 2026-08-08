<?php

namespace Hws\FieldService\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Hws\FieldService\Models\Task;
use Hws\FieldService\Models\TaskPhoto;
use Hws\FieldService\Models\TaskMaterial;

class TaskController extends Controller
{
    /**
     * List all tasks assigned to the logged-in employee.
     */
    public function index(Request $request)
    {
        $employeeId = auth()->guard('admin-api')->id();

        $query = Task::where('assigned_to', $employeeId);

        // Optional status filter
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'pending') {
                $query->where('step', 0);
            } elseif ($status === 'progress') {
                $query->whereIn('step', [1, 2, 3]);
            } elseif ($status === 'done') {
                $query->where('step', 4);
            }
        }

        $tasks = $query->with(['photos', 'materials'])->orderBy('scheduled_at', 'asc')->get();

        // Map database model attributes to match React/Next.js frontend field names
        $mappedTasks = $tasks->map(function ($task) {
            return [
                'id'              => $task->id,
                'taskNo'          => $task->task_no,
                'name'            => $task->customer_name,
                'owner'           => $task->customer_name,
                'phone'           => $task->customer_phone,
                'address'         => $task->customer_address,
                'type'            => $this->mapTaskTypeToFrontend($task->type),
                'priority'        => $task->priority,
                'step'            => $task->step,
                'time'            => $task->scheduled_at ? $task->scheduled_at->format('h:i A') : 'N/A',
                'isSurvey'        => $task->type === 'site_survey',
                'workDescription' => $task->work_description,
                'rating'          => $task->rating,
                'materials'       => $task->materials->pluck('material_name')->toArray(),
                'surveyPhotos'    => $task->photos->where('type', 'survey_site')->map(function ($photo) {
                    return url('storage/' . $photo->file_path);
                })->values()->toArray(),
                'beforePhotos'    => $task->photos->where('type', 'before')->map(function ($photo) {
                    return url('storage/' . $photo->file_path);
                })->values()->toArray(),
                'afterPhotos'     => $task->photos->where('type', 'after')->map(function ($photo) {
                    return url('storage/' . $photo->file_path);
                })->values()->toArray(),
            ];
        });

        return response()->json($mappedTasks);
    }

    /**
     * Advance the task step status (0 to 4).
     */
    public function updateStep(Request $request, $id)
    {
        $employeeId = auth()->guard('admin-api')->id();

        $task = Task::where('id', $id)
            ->where('assigned_to', $employeeId)
            ->first();

        if (!$task) {
            return response()->json(['error' => 'Task not found or not assigned to you.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'step'             => 'required|integer|min:0|max:4',
            'work_description' => 'nullable|string',
            'rating'           => 'nullable|integer|min:0|max:5',
            'materials'        => 'nullable|array',
            'before_photos'    => 'nullable|array',
            'before_photos.*'  => 'string',
            'after_photos'     => 'nullable|array',
            'after_photos.*'   => 'string',
            'signature'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $task->update([
                'step'             => $request->input('step'),
                'work_description' => $request->input('work_description', $task->work_description),
                'rating'           => $request->input('rating', $task->rating),
            ]);

            $newStep = (int) $request->input('step');
            $titles = [
                1 => 'Task Accepted',
                2 => 'Started Travel',
                3 => 'Started Work',
                4 => 'Task Completed',
            ];
            $messages = [
                1 => "You accepted task #{$task->task_no} for customer {$task->customer_name}.",
                2 => "You started travelling for task #{$task->task_no}.",
                3 => "You started working on task #{$task->task_no}.",
                4 => "You completed task #{$task->task_no}.",
            ];
            if (isset($titles[$newStep])) {
                \Hws\FieldService\Models\Notification::create([
                    'admin_id' => $employeeId,
                    'title'    => $titles[$newStep],
                    'message'  => $messages[$newStep],
                    'is_read'  => false,
                ]);
            }

            // Save Signature Base64
            if ($request->filled('signature')) {
                $sigBase64 = $request->input('signature');
                if (preg_match('/^data:image\/(\w+);base64,/', $sigBase64, $type)) {
                    $sigData = substr($sigBase64, strpos($sigBase64, ',') + 1);
                    $sigData = base64_decode($sigData);
                    $extension = strtolower($type[1]);
                    $fileName = uniqid() . '.' . $extension;
                    $sigPath = 'tasks/signatures/' . $fileName;
                    Storage::disk('public')->put($sigPath, $sigData);

                    $task->update(['signature_path' => $sigPath]);
                }
            }

            // Save Materials
            if ($request->has('materials')) {
                TaskMaterial::where('task_id', $task->id)->delete();
                foreach ($request->input('materials') as $materialName) {
                    if (!empty($materialName)) {
                        TaskMaterial::create([
                            'task_id'       => $task->id,
                            'material_name' => $materialName,
                        ]);
                    }
                }
            }

            // Save Before Photos
            if ($request->has('before_photos')) {
                TaskPhoto::where('task_id', $task->id)->where('type', 'before')->delete();
                foreach ($request->input('before_photos') as $photoBase64) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $photoBase64, $type)) {
                        $photoData = substr($photoBase64, strpos($photoBase64, ',') + 1);
                        $photoData = base64_decode($photoData);
                        $extension = strtolower($type[1]);
                        $fileName = uniqid() . '.' . $extension;
                        $photoPath = 'tasks/photos/' . $fileName;
                        Storage::disk('public')->put($photoPath, $photoData);

                        TaskPhoto::create([
                            'task_id'   => $task->id,
                            'type'      => 'before',
                            'file_path' => $photoPath,
                        ]);
                    }
                }
            }

            // Save After Photos
            if ($request->has('after_photos')) {
                TaskPhoto::where('task_id', $task->id)->where('type', 'after')->delete();
                foreach ($request->input('after_photos') as $photoBase64) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $photoBase64, $type)) {
                        $photoData = substr($photoBase64, strpos($photoBase64, ',') + 1);
                        $photoData = base64_decode($photoData);
                        $extension = strtolower($type[1]);
                        $fileName = uniqid() . '.' . $extension;
                        $photoPath = 'tasks/photos/' . $fileName;
                        Storage::disk('public')->put($photoPath, $photoData);

                        TaskPhoto::create([
                            'task_id'   => $task->id,
                            'type'      => 'after',
                            'file_path' => $photoPath,
                        ]);
                    }
                }
            }

            DB::commit();

            // Reload fresh relationships
            $task->load(['photos', 'materials']);

            return response()->json([
                'message' => 'Task step updated successfully.',
                'task'    => [
                    'id'              => $task->id,
                    'taskNo'          => $task->task_no,
                    'name'            => $task->customer_name,
                    'owner'           => $task->customer_name,
                    'phone'           => $task->customer_phone,
                    'address'         => $task->customer_address,
                    'type'            => $this->mapTaskTypeToFrontend($task->type),
                    'priority'        => $task->priority,
                    'step'            => $task->step,
                    'time'            => $task->scheduled_at ? $task->scheduled_at->format('h:i A') : 'N/A',
                    'isSurvey'        => $task->type === 'site_survey',
                    'workDescription' => $task->work_description,
                    'rating'          => $task->rating,
                    'materials'       => $task->materials->pluck('material_name')->toArray(),
                    'beforePhotos'    => $task->photos->where('type', 'before')->map(function ($photo) {
                        return url('storage/' . $photo->file_path);
                    })->values()->toArray(),
                    'afterPhotos'     => $task->photos->where('type', 'after')->map(function ($photo) {
                        return url('storage/' . $photo->file_path);
                    })->values()->toArray(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update task step: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Map database type ENUM values to exact Frontend TaskType strings.
     */
    private function mapTaskTypeToFrontend($dbType)
    {
        $mapping = [
            'installation'  => 'Installation',
            'amc_service'   => 'AMC Service',
            'complaint'     => 'Complaint',
            'service'       => 'Service',
            'sales_visit'   => 'Sales Visit',
            'site_survey'   => 'Site Survey',
        ];

        return $mapping[$dbType] ?? 'Service';
    }
}
