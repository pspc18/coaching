@php
    $i = $startIndex ?? 0;
    $imageShowPath = env('IMAGE_SHOW_PATH');
    $hasBiomax = in_array('Biomax', $dataTable ?? [], true);
    $getAdmissionDatatableFields = $getAdmissionDatatableFields ?? \App\Helpers\Helper::getAdmissionDatatableFields();
    $permission = $permission ?? \App\Helpers\Helper::permissioncheck(3);
    $bloodGroupLookup = $bloodGroupLookup ?? [];
    $genderLookup = $genderLookup ?? [];
    $feesAssignLookup = $feesAssignLookup ?? [];
    $feesPaidLookup = $feesPaidLookup ?? [];
    $dataTable = $dataTable ?? [];
    $colCount = count($dataTable) + ($hasBiomax ? 1 : 0) + 2;
@endphp

@if(!empty($data) && count($data) > 0)
    @foreach($data as $item)
        @php
            $fullName = trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''));
            $className = $item['class_name'] ?? ($item['ClassTypes']['name'] ?? '');
            $bloodGroupText = $bloodGroupLookup[$item->blood_group] ?? '-';
            $genderText = $genderLookup[$item->gender_id] ?? '-';
            $admDateYmd = !empty($item['admission_date']) ? date('Y-m-d', strtotime($item['admission_date'])) : '';
            $isInactive = (int) ($item->status ?? 1) === 0;
            $rowClass = $isInactive ? 'admission-table-row--inactive' : '';
        @endphp
        <tr class="admission-row {{ $rowClass }}" data-name="{{ strtolower($fullName) }}" data-father="{{ strtolower($item['father_name'] ?? '') }}" data-mother="{{ strtolower($item['mother_name'] ?? '') }}" data-class="{{ strtolower($className) }}" data-mobile="{{ strtolower($item['mobile'] ?? '') }}" data-gender="{{ strtolower($genderText) }}" data-category="{{ strtolower($item['category'] ?? '') }}" data-biomax="{{ strtolower($item->attendance_unique_id ?? ($item['biomax_id'] ?? '')) }}" data-adm-date="{{ $admDateYmd }}" data-status="{{ (string) ($item->status ?? 1) }}">
            <td class="text-center" style="vertical-align: middle;"><input type="checkbox" class="checkbox_id" data-admission_no="{{ $item['admissionNo'] ?? '' }}" data-name="{{ $fullName }}" data-mobile="{{ $item['mobile'] ?? '' }}" data-father_name="{{ $item['father_name'] ?? '' }}" value="{{ $item->id }}" style="width: 16px; height: 16px; vertical-align: middle; cursor: pointer;"><span class="row-num font-weight-bold ml-1 text-muted">{{ ++$i }}</span></td>
            @if($hasBiomax)<td class="text-center">{{ $item->attendance_unique_id ?? ($item['biomax_id'] ?? '-') }}</td>@endif
            @php
                foreach ($dataTable as $val) {
                    if ($val === 'Biomax') continue;
                    switch ($val) {
                        case 'Student Photo':
                            echo '<td class="text-center p-1"><img width="36px" height="36px" style="border-radius:2px; object-fit:cover; border:1px solid #cbd5e1;" class="profileImg pointer" data-id="' . ($item['id'] ?? '') . '" src="' . $imageShowPath . 'profile/' . ($item['image'] ?? '') . '" onerror="this.src=\'' . $imageShowPath . 'default/user_image.jpg\'" title="Click to view/change image"><div style="display: none;">' . ($item['image'] ?? '') . '</div></td>';
                            break;
                        case 'Student Name':
                            $viewClass = ($permission->view ?? true) ? '' : 'd-none';
                            echo '<td><a href="' . url('studentDetail/' . $item->id) . '" class="student-name text-primary ' . $viewClass . '" title="Open Student Profile">' . e($fullName ?: '-') . '</a></td>';
                            break;
                        case 'Date Of .Birth':
                            echo '<td>' . (!empty($item['dob']) ? date('d-m-Y', strtotime($item['dob'])) : '-') . '</td>';
                            break;
                        case 'State':
                            echo '<td>' . e($item['State']['name'] ?? '-') . '</td>';
                            break;
                        case 'City':
                            echo '<td>' . e($item['City']['name'] ?? '-') . '</td>';
                            break;
                        case 'Blood Group':
                            echo '<td>' . e($bloodGroupText) . '</td>';
                            break;
                        case 'Gender':
                            echo '<td>' . e($genderText) . '</td>';
                            break;
                        case 'Admission Type(Non RTE)':
                            $type = (int)($item['admission_type_id'] ?? 0);
                            $badge = $type === 1 ? '<span class="badge-admission-type">Non RTE</span>' : ($type === 2 ? '<span class="badge-admission-type text-success font-weight-bold">RTE</span>' : '-');
                            echo '<td class="text-center">' . $badge . '</td>';
                            break;
                        case 'Date Of Admission':
                            echo '<td>' . (!empty($item['admission_date']) ? date('d-m-Y', strtotime($item['admission_date'])) : '-') . '</td>';
                            break;
                        case 'Class':
                            echo '<td><span class="badge-class">' . e($className ?: '-') . '</span></td>';
                            break;
                        case 'Fees Progress':
                            $assignAmt = (float) ($feesAssignLookup[$item->id] ?? 0);
                            $paidAmt = (float) ($feesPaidLookup[$item->id] ?? 0);
                            $paidPct = $assignAmt > 0 ? round(($paidAmt / $assignAmt) * 100, 2) : 0;
                            $barClass = $paidPct >= 100 ? 'bg-success' : ($paidPct > 0 ? 'bg-info' : 'bg-secondary');
                            echo '<td class="text-center" style="min-width: 90px;"><div class="progress" style="height: 14px; border-radius: 2px; background: #e2e8f0;"><div class="progress-bar ' . $barClass . '" role="progressbar" style="width: ' . min(100, $paidPct) . '%; font-size: 9px; line-height: 14px; color: #fff; font-weight: 700;" aria-valuenow="' . $paidPct . '" aria-valuemin="0" aria-valuemax="100">' . $paidPct . '%</div></div></td>';
                            break;
                        case 'Biomax ID':
                            echo '<td>' . e($item['biomax_id'] ?? '-') . '</td>';
                            break;
                        default:
                            $field = $getAdmissionDatatableFields[$val] ?? null;
                            $fieldVal = $field ? ($item[$field] ?? '') : '';
                            $colSlug = Str::slug($val);
                            echo '<td class="text-center editable" data-col-val="' . e(strtolower((string)$fieldVal)) . '" data-col-slug="' . $colSlug . '" data-id="' . ($item->id ?? '') . '" data-field="' . $field . '" data-modal="Admission">' . e($fieldVal !== '' ? (string)$fieldVal : '-') . '</td>';
                            break;
                    }
                }
            @endphp
            <td class="text-center fixed_action_col">
                <div class="table-actions">
                    @if($permission->view ?? true)
                        <a href="{{ url('studentDetail/'.$item->id) }}" class="table-btn btn-action-view" title="Student Details"><i class="fa fa-arrow-circle-right"></i></a>
                    @endif
                    <a href="{{ url('admissionStudentIdPrint/'.$item->id) }}" target="_blank" class="table-btn btn-action-id" title="Print Student ID"><i class="fa fa-credit-card"></i></a>
                    @if($permission->print ?? true)
                        <a href="{{ url('admissionStudentPrint/'.$item->id) }}" target="_blank" class="table-btn btn-action-print" title="Print Admission Form"><i class="fa fa-print"></i></a>
                    @endif
                    @if($permission->edit ?? true)
                        <button type="button" class="table-btn btn-action-edit openEditModal" data-id="{{ $item->id }}" title="Quick Edit Student"><i class="fa fa-edit"></i></button>
                    @endif
                    @if($permission->delete ?? true)
                        <button type="button" class="table-btn btn-action-delete deleteData" data-id="{{ $item->id }}" title="Delete Student"><i class="fa fa-trash-o"></i></button>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr id="empty-state-row">
        <td colspan="{{ $colCount }}" class="p-0">
            <div class="dash-empty-state">
                <div class="empty-icon"><i class="fa fa-users"></i></div>
                <div class="empty-title">No Admissions Found</div>
                <div class="empty-desc">There are no student admission records matching your current filter criteria or active session.</div>
            </div>
        </td>
    </tr>
@endif