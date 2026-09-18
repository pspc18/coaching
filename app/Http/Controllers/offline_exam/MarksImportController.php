<?php

namespace App\Http\Controllers\offline_exam;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\ClassType;
use App\Models\Subject;
use App\Models\exam\AssignExam;
use App\Models\exam\Exam;
use App\Models\exam\FillMarks;
use App\Models\exam\FillMinMaxMarks;
use App\Helpers\Helper;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Session;

class MarksImportController extends Controller
{
    public function FillMarksByExcel(Request $request)
    {
        $classTypeId = (int) $request->input('class_type_id', 0);
        $examId = (int) $request->input('exam_id', 0);
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $classType = Helper::classType();
        $examlist = collect();
        $subjects = collect();

        if ($classTypeId > 0) {
            $examlist = AssignExam::select('exams.id as exam_id', 'exams.name as exam_name')
                ->join('exams', 'exams.id', '=', 'assign_exams.exam_id')
                ->where('assign_exams.class_type_id', $classTypeId)
                ->where('assign_exams.branch_id', $branchId)
                ->where('assign_exams.session_id', $sessionId)
                ->whereNull('assign_exams.deleted_at')
                ->whereNull('exams.deleted_at')
                ->orderBy('exams.name')
                ->get();
        }

        if ($classTypeId > 0 && $examId > 0) {
            [$assignment, $students, $subjects] = $this->templateData($classTypeId, $examId);
            if (!$assignment) {
                $subjects = collect();
            }
        }

        return Helper::view('examination.offline_exam.fill_marks_by_excel.fill_marks_by_excel', [
            'classType' => $classType,
            'search' => ['class_type_id' => $classTypeId, 'exam_id' => $examId],
            'examlist' => $examlist,
            'subjects' => $subjects,
        ]);
    }

    public function prepareMapping(Request $request)
    {
        $validated = $request->validate([
            'class_type_id' => 'required|integer',
            'exam_id' => 'required|integer',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $classTypeId = (int) $validated['class_type_id'];
        $examId = (int) $validated['exam_id'];
        [$assignment, $students, $subjects] = $this->templateData($classTypeId, $examId);

        if (!$assignment) {
            return redirect()->back()->withInput()->with('error', 'Selected exam is not assigned to this class.');
        }
        if ($subjects->isEmpty()) {
            return redirect()->back()->withInput()->with('error', 'No subjects are configured for the selected class.');
        }

        $rows = Excel::toArray([], $request->file('excel_file'))[0] ?? [];
        $headerRowIndex = $this->findExcelHeaderRow($rows);
        if ($headerRowIndex === null) {
            return redirect()->back()->withInput()->with('error', 'No header row was found in the uploaded Excel.');
        }

        $headers = collect($rows[$headerRowIndex])
            ->map(function ($header, $index) {
                $label = trim((string) $header);
                return $label !== '' ? $label : 'Column '.($index + 1);
            })
            ->all();
        $dataRows = array_slice($rows, $headerRowIndex + 1);
        $importToken = (string) Str::uuid();
        $imports = Session::get('marks_mapping_uploads', []);
        $imports[$importToken] = [
            'class_type_id' => $classTypeId,
            'exam_id' => $examId,
            'headers' => $headers,
            'rows' => $dataRows,
            'created_at' => now()->toDateTimeString(),
        ];
        Session::put('marks_mapping_uploads', $imports);

        $examlist = AssignExam::select('exams.id as exam_id', 'exams.name as exam_name')
            ->join('exams', 'exams.id', '=', 'assign_exams.exam_id')
            ->where('assign_exams.class_type_id', $classTypeId)
            ->where('assign_exams.branch_id', Session::get('branch_id'))
            ->where('assign_exams.session_id', Session::get('session_id'))
            ->whereNull('assign_exams.deleted_at')
            ->whereNull('exams.deleted_at')
            ->orderBy('exams.name')
            ->get();

        return Helper::view('examination.offline_exam.fill_marks_by_excel.fill_marks_by_excel', [
            'search' => ['class_type_id' => $classTypeId, 'exam_id' => $examId],
            'examlist' => $examlist,
            'subjects' => $subjects,
            'mappingHeaders' => $headers,
            'mappingDefaults' => $this->suggestExcelMappings($headers, $subjects),
            'candidateColumn' => $this->findHeaderColumn($headers, ['CANDIDATE ID', 'ADMISSION NO', 'ADMISSION NUMBER']),
            'importToken' => $importToken,
            'uploadedRowCount' => count($dataRows),
        ]);
    }

    public function saveMappedMarks(Request $request)
    {
        $validated = $request->validate([
            'import_token' => 'required|string',
            'candidate_column' => 'required|integer|min:0',
            'mapping' => 'required|array',
            'mapping.*.maximum' => 'nullable|integer|min:0',
            'mapping.*.minimum' => 'nullable|integer|min:0',
            'mapping.*.r' => 'nullable|integer|min:0',
            'mapping.*.w' => 'nullable|integer|min:0',
            'mapping.*.l' => 'nullable|integer|min:0',
            'mapping.*.marks' => 'nullable|integer|min:0',
        ]);

        $imports = Session::get('marks_mapping_uploads', []);
        $upload = $imports[$validated['import_token']] ?? null;
        if (!$upload) {
            return redirect()->back()->with('error', 'Uploaded Excel session expired. Please upload the file again.');
        }

        $classTypeId = (int) $upload['class_type_id'];
        $examId = (int) $upload['exam_id'];
        $headers = $upload['headers'] ?? [];
        $rows = $upload['rows'] ?? [];
        $candidateColumn = (int) $validated['candidate_column'];
        if (!array_key_exists($candidateColumn, $headers)) {
            return redirect()->back()->with('error', 'The selected Candidate ID column is invalid.');
        }

        [$assignment, $students, $subjects] = $this->templateData($classTypeId, $examId);
        if (!$assignment) {
            return redirect()->back()->with('error', 'Selected exam is no longer assigned to this class.');
        }

        $subjectMap = $subjects->keyBy('id');
        $mapping = collect($validated['mapping'])
            ->filter(function ($columns, $subjectId) use ($subjectMap, $headers) {
                if (!$subjectMap->has((int) $subjectId) || !is_array($columns)) {
                    return false;
                }
                foreach (['maximum', 'minimum', 'r', 'w', 'l', 'marks'] as $type) {
                    if (isset($columns[$type]) && $columns[$type] !== ''
                        && !array_key_exists((int) $columns[$type], $headers)) {
                        return false;
                    }
                }
                return collect($columns)->contains(function ($column) {
                    return $column !== null && $column !== '';
                });
            });

        if ($mapping->isEmpty()) {
            return redirect()->back()->with('error', 'Map at least one Maximum, Minimum, R, W, L or Marks column.');
        }

        $studentMap = $students->keyBy(function ($student) {
            return $this->normalizeCandidateId($student->admissionNo);
        });
        $mappedLimits = [];
        foreach ($mapping as $subjectId => $columns) {
            $maximum = $this->mappedLimitValue($rows, $columns['maximum'] ?? null);
            $minimum = $this->mappedLimitValue($rows, $columns['minimum'] ?? null);
            if ($maximum === false || $minimum === false) {
                return redirect()->back()->with(
                    'error',
                    $subjectMap->get((int) $subjectId)->name
                    .': Maximum and Minimum columns must contain one consistent numeric value.'
                );
            }
            if ($maximum !== null && $maximum <= 0) {
                return redirect()->back()->with('error', $subjectMap->get((int) $subjectId)->name.': Maximum Marks must be greater than zero.');
            }
            if ($minimum !== null && $minimum < 0) {
                return redirect()->back()->with('error', $subjectMap->get((int) $subjectId)->name.': Minimum Marks cannot be negative.');
            }
            if ($maximum !== null && $minimum !== null && $minimum > $maximum) {
                return redirect()->back()->with('error', $subjectMap->get((int) $subjectId)->name.': Minimum Marks cannot exceed Maximum Marks.');
            }
            $mappedLimits[(int) $subjectId] = ['maximum' => $maximum, 'minimum' => $minimum];
        }

        $created = 0;
        $updated = 0;
        $skippedCandidates = [];
        DB::beginTransaction();
        try {
            $minMaxMap = collect();
            foreach ($mapping as $subjectId => $columns) {
                $limits = $mappedLimits[(int) $subjectId];
                $minMax = FillMinMaxMarks::withTrashed()
                    ->where('exam_id', $examId)
                    ->where('class_type_id', $classTypeId)
                    ->where('subject_id', (int) $subjectId)
                    ->where('branch_id', Session::get('branch_id'))
                    ->where('session_id', Session::get('session_id'))
                    ->first();

                if ($minMax || $limits['maximum'] !== null || $limits['minimum'] !== null) {
                    $minMax = $minMax ?: new FillMinMaxMarks();
                    $minMax->user_id = Session::get('id');
                    $minMax->branch_id = Session::get('branch_id');
                    $minMax->session_id = Session::get('session_id');
                    $minMax->exam_id = $examId;
                    $minMax->class_type_id = $classTypeId;
                    $minMax->subject_id = (int) $subjectId;
                    if ($limits['maximum'] !== null) {
                        $minMax->exam_maximum_marks = $limits['maximum'];
                    }
                    if ($limits['minimum'] !== null) {
                        $minMax->exam_minimum_marks = $limits['minimum'];
                    }
                    $minMax->deleted_at = null;
                    $minMax->save();
                    $minMaxMap->put((int) $subjectId, $minMax);
                }
            }

            foreach ($rows as $row) {
                $candidateId = $this->normalizeCandidateId($row[$candidateColumn] ?? '');
                if ($candidateId === '') {
                    continue;
                }
                $student = $studentMap->get($candidateId);
                if (!$student) {
                    $skippedCandidates[] = $candidateId;
                    continue;
                }

                foreach ($mapping as $subjectId => $columns) {
                    $values = [];
                    foreach (['r', 'w', 'l', 'marks'] as $type) {
                        $column = $columns[$type] ?? null;
                        $values[$type] = ($column === null || $column === '')
                            ? null
                            : $this->cleanMappedValue($row[(int) $column] ?? null);
                    }
                    if (collect($values)->every(function ($value) {
                        return $value === null || $value === '';
                    })) {
                        continue;
                    }

                    $fillMark = FillMarks::withTrashed()
                        ->where('exam_id', $examId)
                        ->where('class_type_id', $classTypeId)
                        ->where('admission_id', $student->id)
                        ->where('subject_id', (int) $subjectId)
                        ->where('branch_id', Session::get('branch_id'))
                        ->where('session_id', Session::get('session_id'))
                        ->first();
                    $isNew = !$fillMark;
                    $fillMark = $fillMark ?: new FillMarks();
                    $minMax = $minMaxMap->get((int) $subjectId);
                    $fillMark->user_id = Session::get('id');
                    $fillMark->branch_id = Session::get('branch_id');
                    $fillMark->session_id = Session::get('session_id');
                    $fillMark->exam_id = $examId;
                    $fillMark->class_type_id = $classTypeId;
                    $fillMark->admission_id = $student->id;
                    $fillMark->subject_id = (int) $subjectId;
                    $fillMark->fill_min_max_marks_id = $minMax->id ?? null;
                    $fillMark->r_marks = $values['r'];
                    $fillMark->w_marks = $values['w'];
                    $fillMark->l_marks = $values['l'];
                    $fillMark->student_marks = $values['marks'];
                    $fillMark->exam_maximum_marks = $minMax->exam_maximum_marks ?? null;
                    $fillMark->deleted_at = null;
                    $fillMark->save();
                    $isNew ? $created++ : $updated++;
                }
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Excel marks could not be saved: '.$exception->getMessage());
        }

        unset($imports[$validated['import_token']]);
        Session::put('marks_mapping_uploads', $imports);
        $message = "Excel mapping saved successfully. Created: {$created}, updated: {$updated}.";
        if ($skippedCandidates) {
            $message .= ' Skipped Candidate IDs not found in the selected class: '
                .implode(', ', array_slice(array_values(array_unique($skippedCandidates)), 0, 20)).'.';
        }

        return redirect('fill-marks-by-excel?class_type_id='.$classTypeId.'&exam_id='.$examId)
            ->with('message', $message);
    }

    private function findExcelHeaderRow(array $rows): ?int
    {
        foreach (array_slice($rows, 0, 20, true) as $index => $row) {
            $nonEmpty = collect($row)->filter(function ($value) {
                return trim((string) $value) !== '';
            });
            if ($nonEmpty->count() >= 2) {
                return (int) $index;
            }
        }
        return null;
    }

    private function findHeaderColumn(array $headers, array $names): ?int
    {
        $wanted = collect($names)->map(function ($name) {
            return strtoupper(trim(preg_replace('/\s+/', ' ', $name)));
        })->all();
        foreach ($headers as $index => $header) {
            $normalized = strtoupper(trim(preg_replace('/\s+/', ' ', (string) $header)));
            if (in_array($normalized, $wanted, true)) {
                return (int) $index;
            }
        }
        return null;
    }

    private function suggestExcelMappings(array $headers, $subjects): array
    {
        $defaults = [];
        foreach ($subjects as $subject) {
            $name = strtoupper(trim(preg_replace('/\s+/', ' ', (string) $subject->name)));
            $defaults[$subject->id] = [
                'maximum' => $this->findHeaderColumn($headers, [$name.' MAX', $name.' MAXIMUM']),
                'minimum' => $this->findHeaderColumn($headers, [$name.' MIN', $name.' MINIMUM']),
                'r' => $this->findHeaderColumn($headers, [$name.' R']),
                'w' => $this->findHeaderColumn($headers, [$name.' W']),
                'l' => $this->findHeaderColumn($headers, [$name.' L']),
                'marks' => $this->findHeaderColumn($headers, [$name.' MK', $name.' MARKS', $name.' MARK']),
            ];
        }
        return $defaults;
    }

    private function mappedLimitValue(array $rows, $column)
    {
        if ($column === null || $column === '') {
            return null;
        }

        $values = collect($rows)
            ->map(function ($row) use ($column) {
                return trim((string) ($row[(int) $column] ?? ''));
            })
            ->filter(function ($value) {
                return $value !== '';
            })
            ->unique()
            ->values();

        if ($values->isEmpty()) {
            return null;
        }
        if ($values->count() !== 1 || !is_numeric($values->first())) {
            return false;
        }

        return (float) $values->first();
    }

    private function normalizeCandidateId($value): string
    {
        $value = trim((string) $value);
        if ($value !== '' && is_numeric($value) && (float) $value == (int) $value) {
            return (string) (int) $value;
        }
        return strtoupper($value);
    }

    private function cleanMappedValue($value)
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);
        return $value === '' ? null : strtoupper($value);
    }

    public function downloadTemplate(Request $request)
    {
        $validated = $request->validate([
            'class_type_id' => 'required|integer',
            'exam_id' => 'required|integer',
        ]);

        [$assignment, $students, $subjects] = $this->templateData(
            (int) $validated['class_type_id'],
            (int) $validated['exam_id']
        );

        if (!$assignment) {
            return redirect()->back()->withInput()->with('error', 'Selected exam is not assigned to this class.');
        }
        if ($students->isEmpty() || $subjects->isEmpty()) {
            return redirect()->back()->withInput()->with('error', 'Active students and class subjects are required before downloading the template.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Marks Entry');
        $headers = ['Admission No', 'Student Name', 'Father Name'];
        foreach ($subjects as $subject) {
            $headers[] = $subject->name.' [subject_id:'.$subject->id.']';
        }
        $sheet->fromArray($headers, null, 'A1');

        $maximums = ['DO NOT EDIT', $assignment->exam_name, 'Maximum Marks'];
        foreach ($subjects as $subject) {
            $maximums[] = $subject->maximum_marks ?? 100;
        }
        $sheet->fromArray($maximums, null, 'A2');

        $minimums = ['DO NOT EDIT', $assignment->exam_name, 'Minimum Marks'];
        foreach ($subjects as $subject) {
            $minimums[] = $subject->minimum_marks ?? 30;
        }
        $sheet->fromArray($minimums, null, 'A3');

        $existingMarks = FillMarks::where('exam_id', $validated['exam_id'])
            ->where('class_type_id', $validated['class_type_id'])
            ->where('branch_id', Session::get('branch_id'))
            ->where('session_id', Session::get('session_id'))
            ->whereNull('deleted_at')
            ->get()
            ->keyBy(function ($mark) {
                return $mark->admission_id.':'.$mark->subject_id;
            });
        $classOrder = (int) (ClassType::where('id', $validated['class_type_id'])
            ->where('branch_id', Session::get('branch_id'))
            ->value('orderBy') ?? 0);

        $row = 4;
        foreach ($students as $student) {
            $sheet->setCellValue('A'.$row, $student->admissionNo);
            $sheet->setCellValue('B'.$row, trim($student->first_name.' '.$student->last_name));
            $sheet->setCellValue('C'.$row, $student->father_name);
            foreach ($subjects->values() as $subjectIndex => $subject) {
                $cell = Coordinate::stringFromColumnIndex($subjectIndex + 4).$row;
                if (!$this->studentHasSubject($student, (int) $subject->id, (string) $subject->name, $classOrder)) {
                    $sheet->setCellValue($cell, 'Not Assigned');
                    $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFE69C');
                    $sheet->getStyle($cell)->getFont()->setItalic(true)->getColor()->setARGB('FF856404');
                    continue;
                }
                $existingMark = $existingMarks->get($student->id.':'.$subject->id);
                if ($existingMark) {
                    $sheet->setCellValue($cell, $existingMark->student_marks);
                }
            }
            $row++;
        }

        $lastColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:'.$lastColumn.'1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:'.$lastColumn.'1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF007BFF');
        $sheet->getStyle('A2:'.$lastColumn.'3')->getFont()->setBold(true);
        $sheet->getStyle('A2:'.$lastColumn.'2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2F0D9');
        $sheet->getStyle('A3:'.$lastColumn.'3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');
        $sheet->freezePane('D4');
        for ($columnIndex = 1; $columnIndex <= Coordinate::columnIndexFromString($lastColumn); $columnIndex++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex))->setAutoSize(true);
        }
        $sheet->getProtection()->setSheet(false);

        $safeExamName = preg_replace('/[^A-Za-z0-9_-]+/', '_', $assignment->exam_name);
        $filename = 'marks_'.$safeExamName.'_class_'.$validated['class_type_id'].'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importTemplate(Request $request)
    {
        $validated = $request->validate([
            'class_type_id' => 'required|integer',
            'exam_id' => 'required|integer',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        [$assignment, $students, $subjects] = $this->templateData(
            (int) $validated['class_type_id'],
            (int) $validated['exam_id']
        );
        if (!$assignment) {
            return redirect()->back()->withInput()->with('error', 'Selected exam is not assigned to this class.');
        }

        $rows = Excel::toArray([], $request->file('excel_file'))[0] ?? [];
        if (count($rows) < 4
            || strcasecmp(trim((string) ($rows[1][2] ?? '')), 'Maximum Marks') !== 0
            || strcasecmp(trim((string) ($rows[2][2] ?? '')), 'Minimum Marks') !== 0) {
            return redirect()->back()->withInput()->with('error', 'Invalid template or no student rows found.');
        }

        $studentMap = $students->keyBy(function ($student) {
            return strtoupper(trim((string) $student->admissionNo));
        });
        $subjectMap = $subjects->keyBy('id');
        $classOrder = (int) (ClassType::where('id', $validated['class_type_id'])
            ->where('branch_id', Session::get('branch_id'))
            ->value('orderBy') ?? 0);
        $columns = [];
        foreach (($rows[0] ?? []) as $index => $header) {
            if (preg_match('/\\[subject_id:(\\d+)\\]/i', (string) $header, $match)) {
                $subjectId = (int) $match[1];
                if ($subjectMap->has($subjectId)) {
                    $columns[$index] = $subjectId;
                }
            }
        }
        if (empty($columns)) {
            return redirect()->back()->withInput()->with('error', 'No valid subject columns found. Please use the downloaded template without changing its headers.');
        }

        $markLimits = [];
        $limitErrors = [];
        foreach ($columns as $columnIndex => $subjectId) {
            $maximum = $rows[1][$columnIndex] ?? null;
            $minimum = $rows[2][$columnIndex] ?? null;
            $subjectName = $subjectMap[$subjectId]->name;

            if (!is_numeric($maximum) || (float) $maximum <= 0) {
                $limitErrors[] = $subjectName.': Maximum Marks must be greater than zero.';
                continue;
            }
            if (!is_numeric($minimum) || (float) $minimum < 0) {
                $limitErrors[] = $subjectName.': Minimum Marks must be zero or greater.';
                continue;
            }
            if ((float) $minimum > (float) $maximum) {
                $limitErrors[] = $subjectName.': Minimum Marks cannot exceed Maximum Marks.';
                continue;
            }

            $markLimits[$subjectId] = [
                'maximum' => (float) $maximum,
                'minimum' => (float) $minimum,
            ];
        }
        if (!empty($limitErrors)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $limitErrors));
        }

        $created = 0;
        $updated = 0;
        $skipped = [];
        DB::beginTransaction();
        try {
            $minMaxMap = collect();
            foreach (array_unique(array_values($columns)) as $subjectId) {
                $minMax = FillMinMaxMarks::withTrashed()
                    ->where('exam_id', $validated['exam_id'])
                    ->where('class_type_id', $validated['class_type_id'])
                    ->where('subject_id', $subjectId)
                    ->where('branch_id', Session::get('branch_id'))
                    ->where('session_id', Session::get('session_id'))
                    ->first();
                $minMax = $minMax ?: new FillMinMaxMarks();
                $minMax->exam_id = $validated['exam_id'];
                $minMax->class_type_id = $validated['class_type_id'];
                $minMax->subject_id = $subjectId;
                $minMax->exam_maximum_marks = $markLimits[$subjectId]['maximum'];
                $minMax->exam_minimum_marks = $markLimits[$subjectId]['minimum'];
                $minMax->user_id = Session::get('id');
                $minMax->branch_id = Session::get('branch_id');
                $minMax->session_id = Session::get('session_id');
                $minMax->deleted_at = null;
                $minMax->save();
                $minMaxMap->put($subjectId, $minMax);
            }

            foreach (array_slice($rows, 3) as $offset => $row) {
                $excelRow = $offset + 4;
                $admissionNo = strtoupper(trim((string) ($row[0] ?? '')));
                if ($admissionNo === '') {
                    continue;
                }
                $student = $studentMap->get($admissionNo);
                if (!$student) {
                    $skipped[] = 'Row '.$excelRow.': admission number '.$admissionNo.' is not in the selected class.';
                    continue;
                }

                foreach ($columns as $columnIndex => $subjectId) {
                    $value = $row[$columnIndex] ?? null;
                    if ($value === null || trim((string) $value) === '') {
                        continue;
                    }
                    if (!$this->studentHasSubject(
                        $student,
                        $subjectId,
                        (string) $subjectMap[$subjectId]->name,
                        $classOrder
                    )) {
                        if (strcasecmp(trim((string) $value), 'Not Assigned') !== 0) {
                            $skipped[] = 'Row '.$excelRow.': '.$subjectMap[$subjectId]->name.' is not assigned to this student.';
                        }
                        continue;
                    }
                    if (!is_numeric($value) || (float) $value < 0) {
                        $skipped[] = 'Row '.$excelRow.': invalid marks for '.$subjectMap[$subjectId]->name.'.';
                        continue;
                    }

                    $maximum = $markLimits[$subjectId]['maximum'];
                    if ((float) $value > $maximum) {
                        $skipped[] = 'Row '.$excelRow.': '.$subjectMap[$subjectId]->name.' marks exceed '.$maximum.'.';
                        continue;
                    }

                    $mark = FillMarks::withTrashed()
                        ->where('exam_id', $validated['exam_id'])
                        ->where('class_type_id', $validated['class_type_id'])
                        ->where('admission_id', $student->id)
                        ->where('subject_id', $subjectId)
                        ->where('branch_id', Session::get('branch_id'))
                        ->where('session_id', Session::get('session_id'))
                        ->first();
                    $isNew = !$mark;
                    $mark = $mark ?: new FillMarks();
                    $mark->exam_id = $validated['exam_id'];
                    $mark->class_type_id = $validated['class_type_id'];
                    $mark->admission_id = $student->id;
                    $mark->subject_id = $subjectId;
                    $mark->fill_min_max_marks_id = $minMaxMap[$subjectId]->id;
                    $mark->student_marks = $value;
                    $mark->exam_maximum_marks = $maximum;
                    $mark->user_id = Session::get('id');
                    $mark->branch_id = Session::get('branch_id');
                    $mark->session_id = Session::get('session_id');
                    $mark->deleted_at = null;
                    $mark->save();
                    $isNew ? $created++ : $updated++;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Marks import failed: '.$e->getMessage());
        }

        return redirect('fill-marks-by-excel?class_type_id='.$validated['class_type_id'].'&exam_id='.$validated['exam_id'])
            ->with('message', "Marks and subject limits imported successfully. Created: {$created}, updated: {$updated}.")
            ->with('import_warnings', $skipped);
    }

    private function templateData(int $classTypeId, int $examId): array
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $assignment = AssignExam::select('assign_exams.*', 'exams.name as exam_name')
            ->join('exams', 'exams.id', '=', 'assign_exams.exam_id')
            ->where('assign_exams.exam_id', $examId)
            ->where('assign_exams.class_type_id', $classTypeId)
            ->where('assign_exams.branch_id', $branchId)
            ->where('assign_exams.session_id', $sessionId)
            ->whereNull('assign_exams.deleted_at')
            ->whereNull('exams.deleted_at')
            ->first();
        $students = Admission::select('id', 'admissionNo', 'first_name', 'last_name', 'father_name', 'stream_subject')
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)->where('session_id', $sessionId)
            ->where('status', 1)->whereNull('deleted_at')
            ->orderBy('first_name')->get();
        $subjects = Subject::select(
                'subject.*',
                'fill_min_max_marks.exam_maximum_marks as maximum_marks',
                'fill_min_max_marks.exam_minimum_marks as minimum_marks'
            )
            ->leftJoin('fill_min_max_marks', function ($join) use ($examId, $classTypeId, $branchId, $sessionId) {
                $join->on('fill_min_max_marks.subject_id', '=', 'subject.id')
                    ->where('fill_min_max_marks.exam_id', $examId)
                    ->where('fill_min_max_marks.class_type_id', $classTypeId)
                    ->where('fill_min_max_marks.branch_id', $branchId)
                    ->where('fill_min_max_marks.session_id', $sessionId)
                    ->whereNull('fill_min_max_marks.deleted_at');
            })
            ->where('subject.class_type_id', $classTypeId)
            ->where('subject.branch_id', $branchId)
            ->where('subject.session_id', $sessionId)
            ->whereNull('subject.deleted_at')->orderBy('subject.sort_by')->get();

        return [$assignment, $students, $subjects];
    }

    private function studentHasSubject($student, int $subjectId, string $subjectName, int $classOrder): bool
    {
        if ($classOrder <= 10) {
            return true;
        }

        $assignedSubjectIds = collect(explode(',', (string) $student->stream_subject))
            ->map(function ($id) {
                return (int) trim($id);
            })
            ->filter()
            ->all();

        if (in_array($subjectId, $assignedSubjectIds, true)) {
            return true;
        }

        // Some promoted students retain subject IDs from their previous class.
        // Match the old assigned subject name with the same subject in the
        // student's current class so those valid assignments are not lost.
        static $assignedSubjectNamesByStudent = [];
        $studentId = (int) $student->id;
        if (!array_key_exists($studentId, $assignedSubjectNamesByStudent)) {
            $assignedSubjectNamesByStudent[$studentId] = Subject::withTrashed()
                ->whereIn('id', $assignedSubjectIds)
                ->pluck('name')
                ->map(function ($name) {
                    return $this->normalizeSubjectNameForAssignment($name);
                })
                ->filter()
                ->unique()
                ->all();
        }

        return in_array(
            $this->normalizeSubjectNameForAssignment($subjectName),
            $assignedSubjectNamesByStudent[$studentId],
            true
        );
    }

    private function normalizeSubjectNameForAssignment($name): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', (string) $name)));
    }

    public function previewAjax(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,csv',
        ]);

        $sheets = Excel::toArray([], $request->file('excel_file'));
        $rows = $sheets[0] ?? [];
        $preview = $this->buildExamImportPreview($rows);
        $importToken = (string) Str::uuid();

        $imports = Session::get('marks_import_uploads', []);
        $imports[$importToken] = [
            'rows' => $rows,
            'created_at' => now()->toDateTimeString(),
        ];
        Session::put('marks_import_uploads', $imports);

        return response()->json([
            'status' => true,
            'import_token' => $importToken,
            'items' => $preview['items'],
            'errors' => $preview['errors'],
        ]);
    }

    public function saveAjax(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'import_token' => 'required|string',
        ]);

        $items = collect($request->input('items', []));
        $importToken = (string) $request->input('import_token');
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $userId = Session::get('id');

        if (!$this->getStoredImportRows($importToken)) {
            return response()->json([
                'status' => false,
                'message' => 'Uploaded Excel session expired. Please upload the file again.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $created = [];
            $createdCount = 0;
            $existingCount = 0;

            foreach ($items as $item) {
                $examName = trim((string) ($item['exam_name'] ?? ''));
                $classTypeId = (int) ($item['class_type_id'] ?? 0);
                $totalMarks = is_numeric($item['total_marks'] ?? null) ? (float) $item['total_marks'] : null;
                $subjects = collect($item['subjects'] ?? [])
                    ->map(function ($subject) {
                        return $this->normalizeSubjectName((string) $subject);
                    })
                    ->filter()
                    ->unique()
                    ->values();
                $perSubjectMaximum = $this->calculatePerSubjectMaximum($totalMarks, $subjects->count());

                if ($examName === '' || $classTypeId <= 0) {
                    continue;
                }

                $exam = Exam::where('name', $examName)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->whereNull('deleted_at')
                    ->first();

                $examWasCreated = false;
                if (!$exam) {
                    $exam = new Exam();
                    $exam->user_id = $userId;
                    $exam->session_id = $sessionId;
                    $exam->branch_id = $branchId;
                    $exam->name = $examName;
                    $exam->class_type_id = $classTypeId;
                    $exam->description = 'Imported from marks Excel';
                    $exam->exam_maximum_marks = $totalMarks;
                    $exam->save();
                    $examWasCreated = true;
                } elseif ($totalMarks !== null) {
                    $exam->exam_maximum_marks = $totalMarks;
                    $exam->save();
                }

                $assignExam = AssignExam::where('exam_id', $exam->id)
                    ->where('class_type_id', $classTypeId)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->whereNull('deleted_at')
                    ->first();

                if (!$assignExam) {
                    $assignExam = new AssignExam();
                    $assignExam->user_id = $userId;
                    $assignExam->session_id = $sessionId;
                    $assignExam->branch_id = $branchId;
                    $assignExam->exam_id = $exam->id;
                    $assignExam->class_type_id = $classTypeId;
                    $assignExam->save();
                }

                $subjectRows = $this->ensureSubjectsForClass($subjects->all(), $classTypeId, $branchId, $sessionId, $userId);
                $this->syncSubjectMaximumMarks($exam->id, $classTypeId, $subjectRows, $perSubjectMaximum, $branchId, $sessionId, $userId);

                if ($examWasCreated) {
                    $createdCount++;
                } else {
                    $existingCount++;
                }

                $created[] = [
                    'exam_id' => $exam->id,
                    'exam_name' => $exam->name,
                    'class_type_id' => $classTypeId,
                    'already_created' => !$examWasCreated,
                    'exam_maximum_marks' => $totalMarks,
                    'per_subject_maximum_marks' => $perSubjectMaximum,
                    'subjects' => $subjectRows,
                ];
            }

            DB::commit();

            $message = 'Exam, class assignment and subjects prepared successfully.';
            if ($createdCount === 0 && $existingCount > 0) {
                $message = 'Exam already created.';
            } elseif ($createdCount > 0 && $existingCount > 0) {
                $message = 'Some exams were created and some were already created.';
            }

            return response()->json([
                'status' => true,
                'message' => $message,
                'created_count' => $createdCount,
                'existing_count' => $existingCount,
                'created' => $created,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveMarksAjax(Request $request)
    {
        $request->validate([
            'import_token' => 'required|string',
        ]);

        $importToken = (string) $request->input('import_token');
        $rows = $this->getStoredImportRows($importToken);
        if (!$rows) {
            return response()->json([
                'status' => false,
                'message' => 'Uploaded Excel session expired. Please upload the file again.',
            ], 422);
        }

        $parsed = $this->buildMarksImportPayload($rows);

        if (empty($parsed['records'])) {
            return response()->json([
                'status' => false,
                'message' => 'No valid marks data found in the uploaded file.',
                'errors' => $parsed['errors'],
            ], 422);
        }

        $userId = Session::get('id');
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $createdCount = 0;
        $updatedCount = 0;

        DB::beginTransaction();

        try {
            foreach ($parsed['records'] as $record) {
                $fillMinMax = FillMinMaxMarks::where('exam_id', $record['exam_id'])
                    ->where('class_type_id', $record['class_type_id'])
                    ->where('subject_id', $record['subject_id'])
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->whereNull('deleted_at')
                    ->first();

                if (!$fillMinMax && $record['per_subject_maximum_marks'] !== null) {
                    $fillMinMax = new FillMinMaxMarks();
                    $fillMinMax->user_id = $userId;
                    $fillMinMax->exam_id = $record['exam_id'];
                    $fillMinMax->branch_id = $branchId;
                    $fillMinMax->session_id = $sessionId;
                    $fillMinMax->class_type_id = $record['class_type_id'];
                    $fillMinMax->subject_id = $record['subject_id'];
                    $fillMinMax->exam_maximum_marks = $record['per_subject_maximum_marks'];
                    $fillMinMax->save();
                }

                $fillMark = FillMarks::withTrashed()
                    ->where('exam_id', $record['exam_id'])
                    ->where('class_type_id', $record['class_type_id'])
                    ->where('admission_id', $record['admission_id'])
                    ->where('subject_id', $record['subject_id'])
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->first();

                $isNew = false;
                if (!$fillMark) {
                    $fillMark = new FillMarks();
                    $isNew = true;
                }

                $fillMark->user_id = $userId;
                $fillMark->session_id = $sessionId;
                $fillMark->branch_id = $branchId;
                $fillMark->fill_min_max_marks_id = $fillMinMax->id ?? null;
                $fillMark->exam_id = $record['exam_id'];
                $fillMark->admission_id = $record['admission_id'];
                $fillMark->class_type_id = $record['class_type_id'];
                $fillMark->subject_id = $record['subject_id'];
                $fillMark->student_marks = $record['student_marks'];
                $fillMark->exam_maximum_marks = $fillMinMax->exam_maximum_marks ?? $record['per_subject_maximum_marks'];
                $fillMark->deleted_at = null;
                $fillMark->save();

                if ($isNew) {
                    $createdCount++;
                } else {
                    $updatedCount++;
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Marks imported successfully.',
                'created_count' => $createdCount,
                'updated_count' => $updatedCount,
                'items' => $parsed['items'],
                'errors' => $parsed['errors'],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function buildExamImportPreview(array $rows): array
    {
        if (count($rows) < 2) {
            return [
                'items' => [],
                'errors' => ['Excel file is empty or missing data rows.'],
            ];
        }

        $header = array_map(function ($value) {
            return $this->cleanImportValue((string) $value);
        }, $rows[0] ?? []);

        $candidateIndex = $this->findHeaderIndex($header, ['CANDIDATE ID', 'CANDIDATE_ID', 'CANDIDATEID']);
        $testIndex = $this->findHeaderIndex($header, ['Test', 'TEST']);
        $totalIndex = $this->findHeaderIndex($header, ['Total', 'TOTAL']);
        $percentageIndex = $this->findHeaderIndex($header, ['Percentage', 'PERCENTAGE']);
        $subjectColumns = $this->extractSubjectColumns($header, $testIndex, $totalIndex);

        $errors = [];
        if ($candidateIndex === null) {
            $errors[] = 'CANDIDATE ID column not found.';
        }
        if ($testIndex === null) {
            $errors[] = 'Test column not found.';
        }
        if ($totalIndex === null) {
            $errors[] = 'Total column not found.';
        }
        if (empty($subjectColumns)) {
            $errors[] = 'No subject columns found between Test and Total.';
        }
        if (!empty($errors)) {
            return ['items' => [], 'errors' => $errors];
        }

        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $grouped = [];

        for ($rowIndex = 1; $rowIndex < count($rows); $rowIndex++) {
            $row = $rows[$rowIndex] ?? [];
            $candidateId = trim((string) ($row[$candidateIndex] ?? ''));
            $examName = trim((string) ($row[$testIndex] ?? ''));

            if ($candidateId === '' || $examName === '') {
                continue;
            }

            $student = Admission::where(function ($query) use ($candidateId) {
                    $query->where('admissionNo', $candidateId);
                    if (is_numeric($candidateId)) {
                        $query->orWhere('id', (int) $candidateId);
                    }
                })
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->first();

            if (!$student) {
                $errors[] = "Candidate ID {$candidateId} not found in admissions.";
                continue;
            }

            $classTypeId = (int) ($student->class_type_id ?? 0);
            if ($classTypeId <= 0) {
                $errors[] = "Candidate ID {$candidateId} has no class type.";
                continue;
            }

            $groupKey = $examName . '|' . $classTypeId;
            if (!isset($grouped[$groupKey])) {
                $className = optional(ClassType::find($classTypeId))->name;
                $grouped[$groupKey] = [
                    'exam_name' => $examName,
                    'class_type_id' => $classTypeId,
                    'class_name' => $className ?: 'Unknown',
                    'student_count' => 0,
                    'candidate_ids' => [],
                    'total_marks' => null,
                    'subjects' => array_values(array_unique(array_column($subjectColumns, 'name'))),
                ];
            }

            $grouped[$groupKey]['student_count']++;
            $grouped[$groupKey]['candidate_ids'][] = $candidateId;

            if ($grouped[$groupKey]['total_marks'] === null) {
                $grouped[$groupKey]['total_marks'] = $this->inferTotalMarks($row, $totalIndex, $percentageIndex);
            }
        }

        return [
            'items' => array_values($grouped),
            'errors' => array_values(array_unique($errors)),
        ];
    }

    private function buildMarksImportPayload(array $rows): array
    {
        if (count($rows) < 2) {
            return [
                'items' => [],
                'records' => [],
                'errors' => ['Excel file is empty or missing data rows.'],
            ];
        }

        $header = array_map(function ($value) {
            return $this->cleanImportValue((string) $value);
        }, $rows[0] ?? []);

        $candidateIndex = $this->findHeaderIndex($header, ['CANDIDATE ID', 'CANDIDATE_ID', 'CANDIDATEID']);
        $testIndex = $this->findHeaderIndex($header, ['Test', 'TEST']);
        $totalIndex = $this->findHeaderIndex($header, ['Total', 'TOTAL']);
        $percentageIndex = $this->findHeaderIndex($header, ['Percentage', 'PERCENTAGE']);
        $subjectColumns = $this->extractSubjectColumns($header, $testIndex, $totalIndex);

        $errors = [];
        if ($candidateIndex === null) {
            $errors[] = 'CANDIDATE ID column not found.';
        }
        if ($testIndex === null) {
            $errors[] = 'Test column not found.';
        }
        if ($totalIndex === null) {
            $errors[] = 'Total column not found.';
        }
        if (empty($subjectColumns)) {
            $errors[] = 'No subject columns found between Test and Total.';
        }
        if (!empty($errors)) {
            return ['items' => [], 'records' => [], 'errors' => $errors];
        }

        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $userId = Session::get('id');
        $records = [];
        $grouped = [];
        $subjectCache = [];

        for ($rowIndex = 1; $rowIndex < count($rows); $rowIndex++) {
            $row = $rows[$rowIndex] ?? [];
            $candidateId = $this->cleanImportValue((string) ($row[$candidateIndex] ?? ''));
            $examName = $this->cleanImportValue((string) ($row[$testIndex] ?? ''));
            $rowTotalMarks = $this->inferTotalMarks($row, $totalIndex, $percentageIndex);
            $perSubjectMaximum = $this->calculatePerSubjectMaximum($rowTotalMarks, count($subjectColumns));

            if ($candidateId === '' || $examName === '') {
                continue;
            }

            $student = Admission::where(function ($query) use ($candidateId) {
                    $query->where('admissionNo', $candidateId);
                    if (is_numeric($candidateId)) {
                        $query->orWhere('id', (int) $candidateId);
                    }
                })
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->first();

            if (!$student) {
                $errors[] = "Candidate ID {$candidateId} not found in admissions.";
                continue;
            }

            $classTypeId = (int) ($student->class_type_id ?? 0);
            if ($classTypeId <= 0) {
                $errors[] = "Candidate ID {$candidateId} has no class type.";
                continue;
            }

            $exam = Exam::where('name', $examName)
                ->where('class_type_id', $classTypeId)
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->whereNull('deleted_at')
                ->first();

            if (!$exam) {
                $errors[] = "Exam {$examName} not found for Candidate ID {$candidateId}. Please create exam setup first.";
                continue;
            }

            $groupKey = $exam->id . '|' . $classTypeId;
            if (!isset($grouped[$groupKey])) {
                $className = optional(ClassType::find($classTypeId))->name;
                $grouped[$groupKey] = [
                    'exam_id' => $exam->id,
                    'exam_name' => $exam->name,
                    'class_type_id' => $classTypeId,
                    'class_name' => $className ?: 'Unknown',
                    'student_count' => 0,
                    'marks_count' => 0,
                    'candidate_ids' => [],
                    'subjects' => [],
                ];
            }

            if (!isset($subjectCache[$classTypeId])) {
                $subjectRows = $this->ensureSubjectsForClass(array_column($subjectColumns, 'name'), $classTypeId, $branchId, $sessionId, $userId);
                $subjectCache[$classTypeId] = collect($subjectRows)->keyBy(function ($subject) {
                    return strtolower($subject['name']);
                });
            }

            $grouped[$groupKey]['student_count']++;
            $grouped[$groupKey]['candidate_ids'][] = $candidateId;

            foreach ($subjectColumns as $subjectColumn) {
                $markValue = $this->cleanImportValue((string) ($row[$subjectColumn['index']] ?? ''));
                if ($markValue === '') {
                    continue;
                }

                $subject = $subjectCache[$classTypeId]->get(strtolower($subjectColumn['name']));
                if (!$subject) {
                    $errors[] = "Subject {$subjectColumn['name']} not found for class of Candidate ID {$candidateId}.";
                    continue;
                }

                $normalizedMarks = strtoupper($markValue);
                $records[] = [
                    'exam_id' => $exam->id,
                    'exam_name' => $exam->name,
                    'class_type_id' => $classTypeId,
                    'admission_id' => (int) $student->id,
                    'candidate_id' => $candidateId,
                    'subject_id' => (int) $subject['id'],
                    'subject_name' => $subject['name'],
                    'student_marks' => $normalizedMarks,
                    'per_subject_maximum_marks' => $perSubjectMaximum,
                ];

                $grouped[$groupKey]['marks_count']++;
                $grouped[$groupKey]['subjects'][$subject['id']] = $subject['name'];
            }
        }

        $items = array_map(function ($item) {
            $item['candidate_ids'] = array_values(array_unique($item['candidate_ids']));
            $item['subjects'] = array_values($item['subjects']);

            return $item;
        }, array_values($grouped));

        return [
            'items' => $items,
            'records' => $records,
            'errors' => array_values(array_unique($errors)),
        ];
    }

    private function findHeaderIndex(array $header, array $needles): ?int
    {
        foreach ($header as $index => $title) {
            $normalized = strtoupper(str_replace([' ', '-'], '', $this->cleanImportValue($title)));
            foreach ($needles as $needle) {
                $needleNormalized = strtoupper(str_replace([' ', '-'], '', $needle));
                if ($normalized === $needleNormalized) {
                    return $index;
                }
            }
        }

        return null;
    }

    private function extractSubjectColumns(array $header, ?int $testIndex, ?int $totalIndex): array
    {
        if ($testIndex === null || $totalIndex === null || $totalIndex <= $testIndex + 1) {
            return [];
        }

        $subjects = [];
        for ($index = $testIndex + 1; $index < $totalIndex; $index++) {
            $subjectName = $this->normalizeSubjectName((string) ($header[$index] ?? ''));
            if ($subjectName === '') {
                continue;
            }

            $subjects[] = [
                'index' => $index,
                'name' => $subjectName,
            ];
        }

        return $subjects;
    }

    private function inferTotalMarks(array $row, ?int $totalIndex, ?int $percentageIndex): ?float
    {
        $total = $totalIndex !== null ? ($row[$totalIndex] ?? null) : null;
        $percentage = $percentageIndex !== null ? ($row[$percentageIndex] ?? null) : null;

        if (!is_numeric($total)) {
            return null;
        }

        if (is_numeric($percentage) && (float) $percentage > 0) {
            return round(((float) $total * 100) / (float) $percentage, 2);
        }

        return (float) $total;
    }

    private function normalizeSubjectName(string $subject): string
    {
        $subject = $this->cleanImportValue($subject);
        $subject = trim(preg_replace('/\s+/', ' ', $subject));
        $subject = preg_replace('/\s+\d+$/', '', $subject);
        $subject = trim((string) $subject);

        if ($subject === '') {
            return '';
        }

        return ucwords(strtolower($subject));
    }

    private function cleanImportValue(string $value): string
    {
        $value = preg_replace('/^\x{FEFF}/u', '', $value);

        return trim((string) $value);
    }

    private function ensureSubjectsForClass(array $subjects, int $classTypeId, $branchId, $sessionId, $userId): array
    {
        $subjectRows = [];
        $sortBy = (int) (Subject::withTrashed()
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->max('sort_by') ?? 0);

        foreach ($subjects as $subjectName) {
            $subjectName = $this->normalizeSubjectName((string) $subjectName);
            if ($subjectName === '') {
                continue;
            }

            $subject = Subject::withTrashed()
                ->where('class_type_id', $classTypeId)
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->whereRaw('LOWER(name) = ?', [strtolower($subjectName)])
                ->first();

            if ($subject) {
                if ($subject->deleted_at !== null) {
                    $subject->deleted_at = null;
                    $subject->save();
                }
            } else {
                $sortBy++;
                $subject = new Subject();
                $subject->user_id = $userId;
                $subject->session_id = $sessionId;
                $subject->branch_id = $branchId;
                $subject->name = $subjectName;
                $subject->other_subject = 0;
                $subject->class_type_id = $classTypeId;
                $subject->sort_by = $sortBy;
                $subject->save();
            }

            $subjectRows[] = [
                'id' => $subject->id,
                'name' => $subject->name,
            ];
        }

        return $subjectRows;
    }

    private function syncSubjectMaximumMarks(int $examId, int $classTypeId, array $subjectRows, ?float $perSubjectMaximum, $branchId, $sessionId, $userId): void
    {
        foreach ($subjectRows as $subjectRow) {
            $subjectId = (int) ($subjectRow['id'] ?? 0);
            if ($subjectId <= 0) {
                continue;
            }

            $fillMinMax = FillMinMaxMarks::withTrashed()
                ->where('exam_id', $examId)
                ->where('class_type_id', $classTypeId)
                ->where('subject_id', $subjectId)
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->first();

            if (!$fillMinMax) {
                $fillMinMax = new FillMinMaxMarks();
                $fillMinMax->exam_id = $examId;
                $fillMinMax->class_type_id = $classTypeId;
                $fillMinMax->subject_id = $subjectId;
                $fillMinMax->branch_id = $branchId;
                $fillMinMax->session_id = $sessionId;
            }

            $fillMinMax->user_id = $userId;
            $fillMinMax->exam_maximum_marks = $perSubjectMaximum;
            $fillMinMax->deleted_at = null;
            $fillMinMax->save();
        }
    }

    private function calculatePerSubjectMaximum(?float $totalMarks, int $subjectCount): ?float
    {
        if ($totalMarks === null || $subjectCount <= 0) {
            return null;
        }

        return round($totalMarks / $subjectCount, 2);
    }

    private function getStoredImportRows(string $importToken): array
    {
        $imports = Session::get('marks_import_uploads', []);
        $rows = $imports[$importToken]['rows'] ?? [];

        return is_array($rows) ? $rows : [];
    }
}
