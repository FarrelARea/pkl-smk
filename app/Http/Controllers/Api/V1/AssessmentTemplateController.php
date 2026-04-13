<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AssessmentTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentTemplateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AssessmentTemplate::with(['schoolClass', 'creator', 'sections']);

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('academic_year')) {
            $academicYear = $request->academic_year;
            $query->whereHas('schoolClass', function ($q) use ($academicYear) {
                $q->where('academic_year', $academicYear);
            });
        }

        $templates = $query->paginate($request->get('per_page', 15));

        return response()->json($templates);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'sections' => 'required|array|min:1',
            'sections.*.number' => 'required|integer',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.order' => 'sometimes|integer',
            'sections.*.indicators' => 'required|array|min:1',
            'sections.*.indicators.*.number' => 'required|string|max:50',
            'sections.*.indicators.*.description' => 'required|string',
            'sections.*.indicators.*.order' => 'sometimes|integer',
            'sections.*.indicators.*.children' => 'sometimes|array',
            'sections.*.indicators.*.children.*.number' => 'required_with:sections.*.indicators.*.children|string|max:50',
            'sections.*.indicators.*.children.*.description' => 'required_with:sections.*.indicators.*.children|string',
            'sections.*.indicators.*.children.*.order' => 'sometimes|integer',
        ]);

        $template = DB::transaction(function () use ($data, $request) {
            // Deactivate existing active template for this class
            AssessmentTemplate::where('class_id', $data['class_id'])
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $template = AssessmentTemplate::create([
                'name' => $data['name'],
                'class_id' => $data['class_id'],
                'created_by' => auth('api')->id(),
                'is_active' => true,
            ]);

            $this->createSections($template, $data['sections']);

            return $template;
        });

        return response()->json(
            $template->load('sections.indicators.children'),
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $template = AssessmentTemplate::with([
            'schoolClass',
            'creator',
            'sections.indicators.children',
        ])->findOrFail($id);

        return response()->json($template);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $template = AssessmentTemplate::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
            'sections' => 'sometimes|array|min:1',
            'sections.*.number' => 'required_with:sections|integer',
            'sections.*.title' => 'required_with:sections|string|max:255',
            'sections.*.order' => 'sometimes|integer',
            'sections.*.indicators' => 'required_with:sections|array|min:1',
            'sections.*.indicators.*.number' => 'required_with:sections|string|max:50',
            'sections.*.indicators.*.description' => 'required_with:sections|string',
            'sections.*.indicators.*.order' => 'sometimes|integer',
            'sections.*.indicators.*.children' => 'sometimes|array',
            'sections.*.indicators.*.children.*.number' => 'required_with:sections.*.indicators.*.children|string|max:50',
            'sections.*.indicators.*.children.*.description' => 'required_with:sections.*.indicators.*.children|string',
            'sections.*.indicators.*.children.*.order' => 'sometimes|integer',
        ]);

        DB::transaction(function () use ($template, $data) {
            if (isset($data['is_active']) && $data['is_active']) {
                AssessmentTemplate::where('class_id', $template->class_id)
                    ->where('id', '!=', $template->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $template->update(collect($data)->only(['name', 'is_active'])->toArray());

            if (isset($data['sections'])) {
                $template->sections()->delete();
                $this->createSections($template, $data['sections']);
            }
        });

        return response()->json(
            $template->fresh()->load('sections.indicators.children')
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $template = AssessmentTemplate::findOrFail($id);
        $template->delete();

        return response()->json(['message' => 'Assessment template deleted successfully']);
    }

    private function createSections(AssessmentTemplate $template, array $sections): void
    {
        foreach ($sections as $index => $sectionData) {
            $section = $template->sections()->create([
                'number' => $sectionData['number'],
                'title' => $sectionData['title'],
                'order' => $sectionData['order'] ?? $index,
            ]);

            foreach ($sectionData['indicators'] as $iIndex => $indicatorData) {
                $indicator = $section->allIndicators()->create([
                    'number' => $indicatorData['number'],
                    'description' => $indicatorData['description'],
                    'order' => $indicatorData['order'] ?? $iIndex,
                ]);

                if (!empty($indicatorData['children'])) {
                    foreach ($indicatorData['children'] as $cIndex => $childData) {
                        $section->allIndicators()->create([
                            'parent_id' => $indicator->id,
                            'number' => $childData['number'],
                            'description' => $childData['description'],
                            'order' => $childData['order'] ?? $cIndex,
                        ]);
                    }
                }
            }
        }
    }
}
