@extends('layout.app')

@section('content')
@php
  $formExam = $savedExam ?? null;
  $previewName = old('name', $formExam->name ?? (request('name') ?? ''));
  $previewCode = old('code', $formExam->code ?? $formExam->exam_code ?? ($examCode ?? ('EXAM-' . strtoupper(\Illuminate\Support\Str::random(6)))));
  $previewQuestions = (int) old('number_of_questions', $formExam->number_of_questions ?? 50);
  $previewInstituteName = $instituteName ?? (Helper::getSetting()->name ?? 'ARISE ERP INSTITUTE');
  $preloadedQuestions = $preloadedQuestions ?? [];
  $preloadedClassId = $preloadedClassId ?? '';
  $preloadedSubjectId = $preloadedSubjectId ?? '';
  $preloadedSubjectIds = $preloadedSubjectIds ?? [];
  $preloadedExamDate = $preloadedExamDate ?? null;

  $previewAnswerKey = old('answer_key', $formExam->answer_key ?? []);
  if (is_string($previewAnswerKey)) {
      $previewAnswerKey = json_decode($previewAnswerKey, true) ?: [];
  }
  $previewSubjectId = old('subject_id', $formExam->subject_id ?? $preloadedSubjectId);
  $previewClassId = old('class_type_id', !empty($preloadedClassId) ? $preloadedClassId : ($formExam->class_type_id ?? ''));
  $previewSubjectIds = old('subject_ids', !empty($preloadedSubjectIds) ? $preloadedSubjectIds : ($formExam ? $formExam->selected_subject_ids : []));
  if (!is_array($previewSubjectIds)) {
      $previewSubjectIds = [];
  }
  $previewSubjectIds = array_values(array_filter(array_map('intval', $previewSubjectIds)));

  $perSubInitialMarks = 100;
  if (!empty($formExam) && !empty($formExam->exam_maximum_marks)) {
      $savedSubsCount = !empty($previewSubjectIds) ? count($previewSubjectIds) : 1;
      $perSubInitialMarks = ($savedSubsCount > 1) ? round($formExam->exam_maximum_marks / $savedSubsCount, 2) : $formExam->exam_maximum_marks;
  }
  $previewPerSubMarks = old('exam_maximum_marks', $perSubInitialMarks);
  $initialEffectiveSubs = !empty($previewSubjectIds) ? count($previewSubjectIds) : 1;
  $previewTotalMarks = ($initialEffectiveSubs > 1) ? round($previewPerSubMarks * $initialEffectiveSubs, 2) : $previewPerSubMarks;

  // Determine initial mode
  $initialQuestions = old('online_questions', $formExam->online_questions ?? $preloadedQuestions);
  if (is_string($initialQuestions)) {
      $initialQuestions = json_decode($initialQuestions, true) ?: [];
  }
  $initialMode = old('exam_creation_mode', $formExam->exam_creation_mode ?? (
      !empty($formExam->is_online) ? 'online' : (
          !empty($initialQuestions) ? 'question_bank' : (
              !empty($formExam->question_paper_path) ? 'pdf_upload' : 'normal'
          )
      )
  ));
  if (request()->filled('question_ids') && empty($formExam)) {
      $initialMode = 'question_bank';
  }
  if (!empty($initialQuestions) && empty($previewQuestions)) {
      $previewQuestions = count($initialQuestions);
  }
@endphp

<style>
/* ==========================================================================
   ARISE ERP - MODERN EXAM BUILDER THEME (HIGH CONTRAST & EQUAL HEIGHT)
   - Viewport fitting: height: calc(100vh - var(--header-height, 56px) - 12px)
   - Contrast Rule:
     * Dark Backgrounds -> Strictly light text (#ffffff, #f8fafc, #38bdf8, #cbd5e1)
     * Light Backgrounds -> Strictly dark text (#0f172a, #1e293b, #334155, #475569)
   - Equal-Height Dual-Column Layout with Zero Dead Space
   ========================================================================== */

.content-wrapper.exam-page {
    background: #eef2f6 !important;
}

.omr-page-layout {
    height: calc(100vh - var(--header-height, 56px) - 12px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 6px 12px 10px 12px;
    background: #eef2f6;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    color: #0f172a;
    box-sizing: border-box;
}

@media (max-width: 991px) {
    .omr-page-layout {
        height: auto;
        min-height: calc(100vh - 56px);
        overflow-y: auto;
    }
}

/* 1. Hero Header Banner (Dark Navy -> Light Fonts) */
.perf-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 7px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.18);
    margin-bottom: 8px;
    flex-shrink: 0;
}
.perf-hero-text {
    display: flex;
    flex-direction: column;
}
.perf-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #38bdf8;
    font-weight: 700;
    margin-bottom: 1px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.perf-breadcrumb {
    font-size: 11px;
    margin-bottom: 2px;
}
.perf-breadcrumb a {
    color: #93c5fd !important;
    text-decoration: none !important;
    transition: color 0.15s;
}
.perf-breadcrumb a:hover {
    color: #ffffff !important;
    text-decoration: underline !important;
}
.perf-breadcrumb .sep {
    color: #94a3b8;
    margin: 0 4px;
}
.perf-breadcrumb .current {
    color: #ffffff;
    font-weight: 700;
}
.perf-title {
    font-size: 14.5px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 7px;
}

/* Hero Action Navigation Buttons */
.perf-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.workflow-hero-nav {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all .15s ease;
    cursor: pointer;
    border: 1px solid transparent;
    height: 28px;
    line-height: 1;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001833 !important;
}
.dash-btn-outline {
    background: rgba(255, 255, 255, 0.14);
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.35);
}
.dash-btn-outline:hover {
    background: rgba(255, 255, 255, 0.24);
    border-color: #ffffff;
}
.workflow-hero-nav .dash-btn.active {
    background: #ffffff !important;
    color: #002C54 !important;
    border-color: #ffffff !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}
.dash-btn-emerald {
    background: #059669;
    color: #ffffff !important;
    border-color: #059669;
}
.dash-btn-emerald:hover {
    background: #047857;
    color: #ffffff !important;
}
.dash-btn-sky {
    background: #0284c7;
    color: #ffffff !important;
    border-color: #0284c7;
}
.dash-btn-sky:hover {
    background: #0369a1;
    color: #ffffff !important;
}

/* 2. Mode Selection 4-Grid Cards */
.mode-selector-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-bottom: 8px;
    flex-shrink: 0;
}
@media (max-width: 1200px) {
    .mode-selector-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 600px) {
    .mode-selector-grid {
        grid-template-columns: 1fr;
    }
}
.mode-option-card {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    cursor: pointer;
    transition: all 0.18s ease;
    display: flex;
    align-items: flex-start;
    gap: 9px;
    position: relative;
    user-select: none;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.mode-option-card:hover {
    border-color: #002C54;
    background: #f8fafc;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.08);
}
.mode-option-card.active {
    border-color: #002C54;
    background: #f0f7ff;
    box-shadow: 0 3px 8px rgba(0, 44, 84, 0.14);
}
.mode-option-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.mode-icon-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #002C54;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    transition: all 0.18s ease;
}
.mode-option-card.active .mode-icon-circle {
    background: #002C54;
    color: #ffffff;
}
.mode-card-title {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 1px 0;
    display: flex;
    align-items: center;
    gap: 5px;
}
.mode-option-card.active .mode-card-title {
    color: #002C54;
}
.mode-card-desc {
    font-size: 10.5px;
    color: #475569;
    margin: 0;
    line-height: 1.3;
}
.mode-option-card.active .mode-card-desc {
    color: #334155;
}
.mode-badge-pill {
    font-size: 8.5px;
    font-weight: 700;
    text-transform: uppercase;
    padding: 1px 6px;
    border-radius: 10px;
    background: #e2e8f0;
    color: #1e293b;
}
.mode-option-card.active .mode-badge-pill {
    background: #002C54;
    color: #ffffff;
}

/* 3. Equal-Height Two-Column Row */
.exam-builder-row {
    flex: 1;
    min-height: 0;
    display: flex;
    align-items: stretch;
    gap: 12px;
    margin: 0;
}
.exam-builder-col-left {
    flex: 0 0 42%;
    max-width: 42%;
    display: flex;
    flex-direction: column;
    min-height: 0;
    padding: 0;
}
.exam-builder-col-right {
    flex: 0 0 calc(58% - 12px);
    max-width: calc(58% - 12px);
    display: flex;
    flex-direction: column;
    min-height: 0;
    padding: 0;
}
@media (max-width: 991px) {
    .exam-builder-row {
        flex-direction: column;
    }
    .exam-builder-col-left,
    .exam-builder-col-right {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

/* Unified Master Card for Equal Height */
.builder-main-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    overflow: hidden;
}

/* Card Header (Deep Navy -> White Text) */
.theme-card-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
    color: #ffffff !important;
    padding: 7px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    flex-shrink: 0;
    min-height: 38px;
}
.theme-card-header .card-title {
    font-size: 12px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    margin: 0 !important;
    color: #ffffff !important;
    line-height: 1.2 !important;
}
.theme-card-header .header-badge {
    font-size: 10px;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.35);
    padding: 2px 8px;
    border-radius: 3px;
    color: #ffffff !important;
    font-weight: 700;
}

/* Left Card Scrollable Body */
.builder-card-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 10px 12px;
    background: #ffffff;
}

/* Section Header Dividers inside Left Card */
.builder-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 9px;
    background: #f1f5f9;
    border-left: 3px solid #002C54;
    border-radius: 2px;
    margin-bottom: 8px;
    margin-top: 10px;
}
.builder-section-header:first-child {
    margin-top: 0;
}
.builder-section-header h4 {
    font-size: 11px;
    font-weight: 700;
    color: #002C54;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Pinned Bottom Footer Action on Left Card */
.builder-card-footer {
    padding: 8px 12px;
    background: #f8fafc;
    border-top: 1px solid #cbd5e1;
    flex-shrink: 0;
}
.btn-create-exam-main {
    width: 100%;
    height: 36px;
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .02em;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0, 44, 84, 0.25);
    transition: all 0.15s ease;
}
.btn-create-exam-main:hover {
    background: linear-gradient(135deg, #001f3b 0%, #0b2749 100%);
    box-shadow: 0 4px 10px rgba(0, 44, 84, 0.35);
    transform: translateY(-1px);
}

/* Form Controls (Light Background -> Dark Text) */
.theme-form-group {
    margin-bottom: 8px;
}
.theme-form-group label {
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 2px;
    display: block;
}
.theme-form-control {
    height: 29px;
    padding: 4px 8px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1 !important;
    border-radius: 3px !important;
    color: #0f172a !important;
    background-color: #ffffff !important;
    width: 100%;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}
.theme-form-control:focus {
    border-color: #002C54 !important;
    outline: 0;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.15) !important;
    background-color: #ffffff !important;
    color: #0f172a !important;
}
.theme-form-control[readonly],
.theme-form-control:disabled {
    background-color: #f1f5f9 !important;
    color: #002C54 !important;
    cursor: not-allowed;
    border-color: #cbd5e1 !important;
    font-weight: 700;
}
textarea.theme-form-control {
    height: auto !important;
}
.theme-hint {
    font-size: 10px;
    color: #475569;
    margin-top: 2px;
}

/* Quick Question Chips */
.quick-chip-btn {
    display: inline-block;
    padding: 2px 8px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    font-size: 10px;
    font-weight: 700;
    color: #1e293b;
    cursor: pointer;
    margin-right: 4px;
    transition: all 0.12s ease;
}
.quick-chip-btn:hover,
.quick-chip-btn.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* Answer Key Matrix (Two-Column Responsive Grid) */
.answer-key-box {
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    background: #ffffff;
    overflow: hidden;
}
.answer-key-toolbar {
    background: #f8fafc;
    border-bottom: 1px solid #cbd5e1;
    padding: 5px 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 4px;
}
.answer-key-toolbar span {
    color: #334155;
}
.ak-btn-group button {
    padding: 2px 6px;
    font-size: 9.5px;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #1e293b;
    border-radius: 2px;
    cursor: pointer;
    transition: all 0.12s;
}
.ak-btn-group button:hover {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.answer-key-list {
    max-height: 380px;
    overflow-y: auto;
    padding: 6px 8px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4px 10px;
}
@media (max-width: 576px) {
    .answer-key-list {
        grid-template-columns: 1fr;
    }
}
.ak-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    font-size: 11px;
}
.ak-row:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}
.ak-qnum {
    font-weight: 700;
    color: #002C54;
    width: 32px;
    flex-shrink: 0;
}
.ak-options {
    display: flex;
    gap: 4px;
    justify-content: flex-end;
}
.ak-option-label {
    cursor: pointer;
    margin: 0;
}
.ak-option-label input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.ak-option-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 20px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    font-size: 9.5px;
    font-weight: 700;
    color: #334155;
    background: #ffffff;
    transition: all 0.12s ease;
    user-select: none;
}
.ak-option-label input:checked + .ak-option-pill {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 1px 2px rgba(0, 44, 84, 0.25);
}

/* Question Bank Selected Questions Cards */
.selected-q-card {
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #ffffff;
    padding: 7px 9px;
    margin-bottom: 7px;
    transition: all 0.15s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.selected-q-card:hover {
    border-color: #94a3b8;
    box-shadow: 0 2px 5px rgba(0,0,0,0.06);
}
.selected-q-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 5px;
    padding-bottom: 3px;
    border-bottom: 1px dashed #e2e8f0;
}
.selected-q-badges {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.q-badge-num {
    background: #002C54;
    color: #ffffff;
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 2px;
}
.q-badge-type {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 2px;
}
.q-badge-ans {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 2px;
}
.selected-q-text {
    font-size: 11.5px;
    color: #0f172a;
    line-height: 1.45;
    margin-bottom: 5px;
}
.selected-q-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4px;
    font-size: 11px;
}
.opt-pill-preview {
    padding: 3px 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    color: #1e293b;
}
.opt-pill-preview.is-correct {
    background: #ecfdf5;
    border-color: #10b981;
    color: #065f46;
    font-weight: 700;
}
.selected-q-actions {
    display: flex;
    align-items: center;
    gap: 4px;
}
.btn-q-action {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 2px 5px;
    font-size: 9.5px;
    border-radius: 2px;
    cursor: pointer;
    transition: all 0.12s;
}
.btn-q-action:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.btn-q-action.btn-q-delete:hover {
    background: #fee2e2;
    color: #b91c1c;
    border-color: #fca5a5;
}

/* 4. Right Column Panels (Equal Height) */
.builder-right-panel {
    height: 100%;
}
.builder-right-panel:not([style*="display: none"]) {
    display: flex !important;
    flex-direction: column;
    height: 100%;
    flex: 1;
    min-height: 0;
}
.builder-right-panel .theme-card-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 10px 12px;
    background: #ffffff;
}

/* Mode 1 Overview Styles */
.normal-summary-box {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px 12px;
    font-size: 11.5px;
    margin-bottom: 12px;
}
.normal-summary-box .meta-label {
    color: #475569;
    font-weight: 700;
}
.normal-summary-box .meta-value {
    color: #0f172a;
    font-weight: 700;
}
.workflow-steps-vertical {
    display: flex;
    flex-direction: column;
    gap: 9px;
    padding-left: 2px;
}
.wf-step-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.wf-step-icon {
    width: 24px;
    height: 24px;
    min-width: 24px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    margin-top: 1px;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.2);
}
.wf-step-content strong {
    font-size: 11.5px;
    color: #0f172a;
    display: block;
    margin-bottom: 1px;
}
.wf-step-content p {
    font-size: 10.5px;
    color: #334155;
    line-height: 1.35;
    margin-bottom: 0;
}
.normal-quick-actions {
    display: flex;
    align-items: center;
    gap: 7px;
    padding-top: 10px;
    border-top: 1px solid #e2e8f0;
    flex-wrap: wrap;
    margin-top: auto;
}
.normal-quick-actions .dash-btn {
    height: 28px;
    font-size: 10.5px;
    border-radius: 3px;
}

/* Mode 2 & Mode 3 OMR Sheet Viewport */
.omr-sheet-viewport {
    background: #475569;
    padding: 12px 10px;
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}
.omr-sheet-paper {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.3);
    width: 100%;
    max-width: 630px;
    margin: 0 auto;
    border-radius: 2px;
}

/* Mode 3 Dual Tabs Header */
#qbank-preview-tabs {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    border: none !important;
}
#qbank-preview-tabs li,
#qbank-preview-tabs .nav-item {
    list-style: none !important;
    list-style-type: none !important;
    margin: 0 !important;
    padding: 0 !important;
}
#qbank-preview-tabs li::before,
#qbank-preview-tabs li::marker,
#qbank-preview-tabs .nav-item::before,
#qbank-preview-tabs .nav-item::marker {
    display: none !important;
    content: "" !important;
}
#qbank-preview-tabs .nav-link {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 4px 12px !important;
    border-radius: 4px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
    cursor: pointer !important;
    background: rgba(255, 255, 255, 0.16) !important;
    color: #f8fafc !important;
    border: 1px solid rgba(255, 255, 255, 0.35) !important;
    outline: none !important;
    box-shadow: none !important;
}
#qbank-preview-tabs .nav-link i {
    color: #7dd3fc !important;
}
#qbank-preview-tabs .nav-link:hover {
    background: rgba(255, 255, 255, 0.28) !important;
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.6) !important;
}
#qbank-preview-tabs .nav-link.active,
#qbank-preview-tabs .nav-item.show .nav-link {
    background: #ffffff !important;
    color: #002C54 !important;
    font-weight: 800 !important;
    border-color: #ffffff !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
}
#qbank-preview-tabs .nav-link.active i,
#qbank-preview-tabs .nav-item.show .nav-link i {
    color: #002C54 !important;
}

/* Mode 3 Right Panel Tab Panes Explicit Visibility */
#right-panel-qbank .tab-content {
    flex: 1;
    min-height: 0;
    height: calc(100% - 38px);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
#right-panel-qbank .tab-content > .tab-pane {
    display: none !important;
    height: 100% !important;
    min-height: 0 !important;
    flex: 1 1 auto;
    overflow: hidden;
}
#right-panel-qbank .tab-content > .tab-pane.active {
    display: flex !important;
    flex-direction: column !important;
}

/* Printable Question Paper Layout */
.paper-preview-container {
    background: #475569;
    padding: 12px;
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}
.printable-exam-paper {
    background: #ffffff;
    box-shadow: 0 4px 18px rgba(0,0,0,0.3);
    padding: 20px 24px 30px 24px;
    width: 100%;
    max-width: 760px;
    min-height: 100%;
    height: auto;
    margin: 0 auto 24px auto;
    box-sizing: border-box;
    font-family: "DejaVu Sans", "Segoe UI", Arial, sans-serif;
    color: #0f172a;
    border-radius: 2px;
}
.exam-paper-header {
    text-align: center;
    border-bottom: 2px solid #002C54;
    padding-bottom: 6px;
    margin-bottom: 10px;
}
.exam-paper-institute {
    font-size: 15px;
    font-weight: 800;
    color: #002C54;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin: 0 0 2px;
}
.exam-paper-exam-name {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 5px;
}
.exam-paper-meta-table {
    width: 100%;
    font-size: 10.5px;
    font-weight: 600;
    color: #1e293b;
    border-collapse: collapse;
}
.exam-paper-meta-table td {
    padding: 2px 5px;
}
.exam-instructions-box {
    background: #f8fafc;
    border: 1px dashed #94a3b8;
    padding: 5px 8px;
    font-size: 10px;
    color: #334155;
    margin-bottom: 12px;
    border-radius: 2px;
}
.paper-question-block {
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f5f9;
}
.paper-question-block:last-child {
    border-bottom: none;
}
.paper-q-statement {
    font-size: 11px;
    font-weight: 600;
    line-height: 1.4;
    margin-bottom: 4px;
    color: #0f172a;
}
.paper-q-num {
    font-weight: 800;
    color: #002C54;
    margin-right: 4px;
}
.paper-q-marks {
    float: right;
    font-size: 9.5px;
    font-weight: 700;
    color: #64748b;
}
.paper-q-options-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4px 14px;
    font-size: 10.5px;
    padding-left: 8px;
}
.paper-q-opt {
    color: #1e293b;
    line-height: 1.35;
}
.paper-q-opt-label {
    font-weight: 700;
    color: #002C54;
    margin-right: 4px;
}
.paper-q-diagram {
    max-height: 130px;
    display: block;
    margin: 5px 0;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 2px;
}

/* Modal Styling */
.modal-header-theme {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
    color: #ffffff !important;
    padding: 9px 12px;
}
.modal-header-theme .modal-title {
    color: #ffffff !important;
    font-size: 13px;
    font-weight: 700;
}
.modal-filter-bar {
    background: #f8fafc;
    border-bottom: 1px solid #cbd5e1;
    padding: 7px 10px;
    display: flex;
    align-items: center;
    gap: 7px;
    flex-wrap: wrap;
}
.modal-filter-bar label {
    color: #1e293b !important;
    font-weight: 700;
}
.modal-questions-scroll {
    max-height: 58vh;
    overflow-y: auto;
    padding: 8px 10px;
    background: #ffffff;
}
.modal-q-item {
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 7px 9px;
    margin-bottom: 5px;
    background: #ffffff;
    transition: background 0.1s;
    cursor: pointer;
}
.modal-q-item:hover {
    background: #f8fafc;
}
.modal-q-item.selected {
    background: #f0f7ff;
    border-color: #0284c7;
}

/* Custom Sleek Scrollbars */
.builder-card-body::-webkit-scrollbar,
.builder-right-panel .theme-card-body::-webkit-scrollbar,
.omr-sheet-viewport::-webkit-scrollbar,
.paper-preview-container::-webkit-scrollbar,
.modal-questions-scroll::-webkit-scrollbar,
.answer-key-list::-webkit-scrollbar,
#selected-questions-list::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.builder-card-body::-webkit-scrollbar-track,
.builder-right-panel .theme-card-body::-webkit-scrollbar-track,
.omr-sheet-viewport::-webkit-scrollbar-track,
.paper-preview-container::-webkit-scrollbar-track,
.modal-questions-scroll::-webkit-scrollbar-track,
.answer-key-list::-webkit-scrollbar-track,
#selected-questions-list::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.builder-card-body::-webkit-scrollbar-thumb,
.builder-right-panel .theme-card-body::-webkit-scrollbar-thumb,
.omr-sheet-viewport::-webkit-scrollbar-thumb,
.paper-preview-container::-webkit-scrollbar-thumb,
.modal-questions-scroll::-webkit-scrollbar-thumb,
.answer-key-list::-webkit-scrollbar-thumb,
#selected-questions-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}
.builder-card-body::-webkit-scrollbar-thumb:hover,
.builder-right-panel .theme-card-body::-webkit-scrollbar-thumb:hover,
.omr-sheet-viewport::-webkit-scrollbar-thumb:hover,
.paper-preview-container::-webkit-scrollbar-thumb:hover,
.modal-questions-scroll::-webkit-scrollbar-thumb:hover,
.answer-key-list::-webkit-scrollbar-thumb:hover,
#selected-questions-list::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Print Rules */
@media print {
    @page {
        size: A4 portrait;
        margin: 8mm;
    }
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    body * {
        visibility: hidden !important;
    }
    body.printing-omr #exam-omr-print-area,
    body.printing-omr #exam-omr-print-area * {
        visibility: visible !important;
    }
    body.printing-omr #exam-omr-print-area {
        position: absolute !important;
        left: 50% !important;
        top: 0 !important;
        width: 194mm !important;
        max-width: none !important;
        transform: translateX(-50%) !important;
        border: 0 !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
    }

    body.printing-paper #exam-paper-print-area,
    body.printing-paper #exam-paper-print-area * {
        visibility: visible !important;
    }
    body.printing-paper #exam-paper-print-area {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: none !important;
        border: 0 !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 10mm 12mm !important;
        background: #ffffff !important;
    }
}
</style>

<div class="content-wrapper exam-page">
    <div class="omr-page-layout" id="exam-builder-app">

        {{-- 1. Hero Header Banner (High Contrast) --}}
        <div class="perf-hero">
            <div class="perf-hero-text">
                <span class="perf-kicker">
                    <i class="fa fa-graduation-cap"></i> Examination & OMR Sheet System
                </span>
                <div class="perf-breadcrumb">
                    <a href="{{ url('/') }}">Dashboard</a>
                    <span class="sep">/</span>
                    <a href="{{ url('view/exam') }}">Examinations</a>
                    <span class="sep">/</span>
                    <span class="current">{{ !empty($formExam) ? 'Edit Exam' : 'Create Offline Exam' }}</span>
                </div>
                <h1 class="perf-title">
                    <i class="fa fa-pencil-square-o"></i> {{ !empty($formExam) ? 'Edit Examination & OMR Sheet' : 'Offline Exam & OMR Sheet Creator' }}
                </h1>
            </div>
            <div class="perf-hero-actions">
                @php
                    $workflowClassId = $search['class_type_id'] ?? request('class_type_id') ?? request('class_name');
                    $workflowExamId = $search['exam_id'] ?? request('exam_id');
                    $workflowQuery = array_filter([
                        'class_type_id' => $workflowClassId,
                        'exam_id' => $workflowExamId,
                    ]);
                    $wfSteps = [
                        ['title' => '1. Exam Setup', 'url' => url('view/exam'), 'active' => true, 'icon' => 'fa fa-check-circle'],
                        ['title' => '2. Excel Import', 'url' => url('fill-marks-by-excel'), 'active' => false, 'icon' => 'fa fa-file-excel-o'],
                        ['title' => '3. Manual Marks', 'url' => url('fill_marks').($workflowQuery ? '?'.http_build_query(array_merge($workflowQuery, ['show_all_subjects' => 1])) : '?show_all_subjects=1'), 'active' => false, 'icon' => 'fa fa-pencil'],
                        ['title' => '4. Exam Report', 'url' => url('exam_wise_report').($workflowQuery ? '?'.http_build_query($workflowQuery) : ''), 'active' => false, 'icon' => 'fa fa-bar-chart'],
                    ];
                @endphp
                <div class="workflow-hero-nav">
                    @foreach($wfSteps as $step)
                        <a href="{{ $step['url'] }}" class="dash-btn {{ $step['active'] ? 'dash-btn-light active' : 'dash-btn-outline' }}" title="{{ $step['title'] }}">
                            <i class="{{ $step['icon'] }} mr-1"></i> {{ $step['title'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-2 flex-shrink-0" style="font-size: 11.5px; border-radius: 3px;">
                <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
            </div>
        @endif
        @if(session('error') || (isset($errors) && $errors->any()))
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-2 flex-shrink-0" style="font-size: 11.5px; border-radius: 3px;">
                <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') ?? (isset($errors) ? $errors->first() : '') }}
                <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- Form Container --}}
        <form id="exam-create-form" action="{{ !empty($formExam->id) ? url('edit/exam/' . $formExam->id) : url('add/exam') }}" method="post" enctype="multipart/form-data" class="d-flex flex-column flex-grow-1" style="min-height: 0; flex: 1;">
            @csrf
            @if(!empty($formExam->id))
                <input type="hidden" name="exam_id" value="{{ $formExam->id }}">
                <input type="hidden" name="origin_class_type_id" value="{{ $previewClassId ?? '' }}">
            @endif
            <input type="hidden" name="action_type" id="form-action-type" value="save">
            <input type="hidden" name="is_online" id="exam-is-online" value="{{ ($initialMode === 'online' || !empty($formExam->is_online)) ? '1' : '0' }}">
            <input type="hidden" name="selected_questions_json" id="selected_questions_json" value="{{ json_encode($initialQuestions, JSON_UNESCAPED_UNICODE) }}">

            {{-- 4-Mode Selector Cards --}}
            <div class="mode-selector-grid">
                {{-- Mode 1: Normal Offline Exam --}}
                <label class="mode-option-card {{ $initialMode === 'normal' ? 'active' : '' }}" id="card-mode-normal">
                    <input type="radio" name="exam_creation_mode" value="normal" {{ $initialMode === 'normal' ? 'checked' : '' }}>
                    <div class="mode-icon-circle">
                        <i class="fa fa-file-text-o"></i>
                    </div>
                    <div>
                        <div class="mode-card-title">
                            <span>Mode 1: Normal Exam</span>
                            <span class="mode-badge-pill">Excel / Manual</span>
                        </div>
                        <p class="mode-card-desc">
                            Exam name, date, class &amp; optional PDF paper. Marks uploaded via Excel or manual entry.
                        </p>
                    </div>
                </label>

                {{-- Mode 2: PDF Paper + OMR --}}
                <label class="mode-option-card {{ $initialMode === 'pdf_upload' ? 'active' : '' }}" id="card-mode-pdf">
                    <input type="radio" name="exam_creation_mode" value="pdf_upload" {{ $initialMode === 'pdf_upload' ? 'checked' : '' }}>
                    <div class="mode-icon-circle">
                        <i class="fa fa-file-pdf-o"></i>
                    </div>
                    <div>
                        <div class="mode-card-title">
                            <span>Mode 2: PDF + OMR</span>
                            <span class="mode-badge-pill">Print OMR</span>
                        </div>
                        <p class="mode-card-desc">
                            Set total question count, attach PDF paper, configure answer key, and generate printable OMR sheet.
                        </p>
                    </div>
                </label>

                {{-- Mode 3: Question Bank + OMR with Questions --}}
                <label class="mode-option-card {{ $initialMode === 'question_bank' ? 'active' : '' }}" id="card-mode-qbank">
                    <input type="radio" name="exam_creation_mode" value="question_bank" {{ $initialMode === 'question_bank' ? 'checked' : '' }}>
                    <div class="mode-icon-circle">
                        <i class="fa fa-book"></i>
                    </div>
                    <div>
                        <div class="mode-card-title">
                            <span>Mode 3: Q-Bank + OMR</span>
                            <span class="mode-badge-pill">Paper + OMR</span>
                        </div>
                        <p class="mode-card-desc">
                            Select questions from Question Bank, auto-sync answer key, and print Question Paper + OMR Sheet.
                        </p>
                    </div>
                </label>

                {{-- Mode 4: Online Exam (CBT Panel) --}}
                <label class="mode-option-card {{ $initialMode === 'online' ? 'active' : '' }}" id="card-mode-online">
                    <input type="radio" name="exam_creation_mode" value="online" {{ $initialMode === 'online' ? 'checked' : '' }}>
                    <div class="mode-icon-circle">
                        <i class="fa fa-laptop"></i>
                    </div>
                    <div>
                        <div class="mode-card-title">
                            <span>Mode 4: Online Exam</span>
                            <span class="mode-badge-pill">No OMR (CBT)</span>
                        </div>
                        <p class="mode-card-desc">
                            Browse &amp; add questions from Question Bank. Students take digital test via online exam panel (no OMR).
                        </p>
                    </div>
                </label>
            </div>

            {{-- Equal-Height Dual-Column Row --}}
            <div class="exam-builder-row">

                {{-- Left Column: Parameters & Configuration Master Card --}}
                <div class="exam-builder-col-left">
                    <div class="builder-main-card">
                        {{-- Left Header --}}
                        <div class="theme-card-header">
                            <h3 class="card-title">
                                <i class="fa fa-sliders text-warning"></i> Examination Parameters & Setup
                            </h3>
                            <span class="header-badge" id="exam-code-badge">{{ $previewCode }}</span>
                        </div>

                        {{-- Left Scrollable Body --}}
                        <div class="builder-card-body">

                            {{-- Section 1: Basic Exam Information & Schedule --}}
                            <div class="builder-section-header">
                                <h4><i class="fa fa-info-circle text-primary"></i> 1. Examination Details &amp; Schedule</h4>
                            </div>

                            <div class="row">
                                {{-- Exam Name --}}
                                <div class="col-md-8">
                                    <div class="theme-form-group">
                                        <label for="exam-name">Exam Name <span class="text-danger">*</span></label>
                                        <input type="text" id="exam-name" name="name" class="theme-form-control @error('name') is-invalid @enderror" placeholder="e.g. SSC CGL Tier-1 Mock 01 / Term 1 Science" value="{{ $previewName }}" required maxlength="255">
                                        @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                {{-- Exam Code --}}
                                <div class="col-md-4">
                                    <div class="theme-form-group">
                                        <label for="exam-code">Exam Code</label>
                                        <div class="input-group">
                                            <input type="text" id="exam-code" name="code" class="theme-form-control font-weight-bold" value="{{ $previewCode }}" readonly style="background: #f8fafc; color: #002C54; font-family: monospace; font-size: 13px;">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-light text-muted"><i class="fa fa-barcode text-primary"></i></span>
                                            </div>
                                        </div>
                                        <div class="theme-hint"><i class="fa fa-lock text-muted mr-1"></i> Unique scanner barcode</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Target Course --}}
                                <div class="col-md-4">
                                    <div class="theme-form-group">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label for="class_type_id" class="mb-0">
                                                Target Course <span class="text-danger">*</span>
                                            </label>
                                            @if(!empty($formExam->id))
                                                <span class="badge badge-light border text-muted" style="font-size: 10px; font-weight: 600; background: #f1f5f9; border-color: #cbd5e1;">
                                                    <i class="fa fa-lock text-warning mr-1"></i> Locked in Edit
                                                </span>
                                            @endif
                                        </div>

                                        @if(!empty($formExam->id))
                                            {{-- In Edit Mode: Non-editable / Read-Only Target Course --}}
                                            <select class="theme-form-control" id="class_type_id" disabled style="background-color: #f8fafc; cursor: not-allowed; color: #002C54; font-weight: 600; border: 1px solid #cbd5e1;">
                                                @if(!empty($classType))
                                                    @foreach($classType as $type)
                                                        <option value="{{ $type->id }}" {{ ($previewClassId == $type->id) ? 'selected' : '' }}>
                                                            {{ $type->name ?? '' }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <input type="hidden" name="class_type_id" value="{{ $previewClassId }}">
                                            <div class="theme-hint text-muted small mt-1" style="font-size: 10.5px;">
                                                <i class="fa fa-info-circle text-info"></i> Target course is locked. Duplicate exam to target another course.
                                            </div>
                                        @else
                                            {{-- In Create Mode: Fully Editable --}}
                                            <select class="theme-form-control @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id" required>
                                                <option value="">-- Select Course --</option>
                                                @if(!empty($classType))
                                                    @foreach($classType as $type)
                                                        <option value="{{ $type->id }}" {{ (old('class_type_id', $previewClassId) == $type->id) ? 'selected' : '' }}>
                                                            {{ $type->name ?? '' }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('class_type_id')<span class="text-danger small">{{ $message }}</span>@enderror
                                        @endif
                                    </div>
                                </div>

                                {{-- Target Batch --}}
                                <div class="col-md-4">
                                    <div class="theme-form-group">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label for="batch_id" class="mb-0">
                                                Target Batch <span class="text-muted">(Optional)</span>
                                            </label>
                                        </div>
                                        <select class="theme-form-control @error('batch_id') is-invalid @enderror" id="batch_id" name="batch_id">
                                            <option value="">-- All Batches (Course-Wide) --</option>
                                            @if(!empty($preloadedBatches))
                                                @foreach($preloadedBatches as $batch)
                                                    <option value="{{ $batch->id }}" {{ (old('batch_id', $preloadedBatchId) == $batch->id) ? 'selected' : '' }}>
                                                        {{ $batch->name }} ({{ $batch->shift ?? 'Shift' }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="theme-hint text-muted small mt-1" style="font-size: 10.5px;">
                                            Select specific batch or leave blank for all batches in course.
                                        </div>
                                        @error('batch_id')<span class="text-danger small">{{ $message }}</span>@enderror
                                    </div>
                                </div>

                                {{-- Examination Date --}}
                                <div class="col-md-4">
                                    <div class="theme-form-group">
                                        <label for="exam-date">Date of Exam <span class="text-danger">*</span></label>
                                        <input type="date" id="exam-date" name="exam_date" class="theme-form-control" value="{{ old('exam_date', !empty($preloadedExamDate) ? (is_object($preloadedExamDate) ? $preloadedExamDate->format('Y-m-d') : date('Y-m-d', strtotime($preloadedExamDate))) : ((!empty($formExam) && !empty($formExam->exam_date)) ? (is_object($formExam->exam_date) ? $formExam->exam_date->format('Y-m-d') : $formExam->exam_date) : date('Y-m-d'))) }}" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Single Subject Selector (Shown ONLY for Mode 4: Online CBT Exam) --}}
                            <div class="row" id="subject-single-wrapper" style="{{ $initialMode === 'online' ? '' : 'display: none;' }}">
                                <div class="col-md-12">
                                    <div class="theme-form-group">
                                        <label for="subject_id">Paper Subject <span class="text-danger">*</span></label>
                                        <select class="theme-form-control" id="subject_id" name="subject_id">
                                            <option value="">-- Select Subject --</option>
                                            @if(!empty($subjects))
                                                @foreach($subjects as $sub)
                                                    <option value="{{ $sub->id }}" {{ ($previewSubjectId == $sub->id) ? 'selected' : '' }}>
                                                        {{ $sub->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="theme-hint"><i class="fa fa-info-circle text-info"></i> Online CBT is conducted for a single subject paper.</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Shared Multi-Subject Checklist Container (docked into active mode pane via JS) --}}
                            <div id="subject-multi-wrapper" style="{{ $initialMode === 'online' ? 'display: none;' : '' }}">
                                <div class="theme-form-group mb-2">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="mb-0 font-weight-bold" style="color: #1e293b; font-size: 12.5px;">
                                            <i class="fa fa-book text-primary mr-1"></i> Included Exam Subjects:
                                            <span class="badge ml-1" id="subject-selected-count-badge" style="background:#e0f2fe; color:#0369a1; font-size:11px; font-weight:700; border-radius:12px; padding:2px 8px;">
                                                All Selected
                                            </span>
                                        </label>
                                        <div class="btn-group btn-group-xs" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2" id="btn-select-all-subjects" style="font-size: 11px; height: 22px;">
                                                <i class="fa fa-check-square-o mr-1"></i> Select All
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-xs py-0 px-2" id="btn-deselect-all-subjects" style="font-size: 11px; height: 22px;">
                                                <i class="fa fa-square-o mr-1"></i> Clear All
                                            </button>
                                        </div>
                                    </div>
                                    <div class="subjects-checklist-container" id="subjects-checklist-box" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; max-height: 120px; overflow-y: auto;">
                                        <div class="d-flex flex-wrap" id="subjects-chips-list" style="gap: 8px;">
                                            @if(!empty($subjects) && $subjects->count() > 0)
                                                @foreach($subjects as $sub)
                                                    @php
                                                        $isChecked = empty($previewSubjectIds) || in_array((int)$sub->id, $previewSubjectIds, true);
                                                    @endphp
                                                    <label class="subject-chip-item {{ $isChecked ? 'active' : '' }}" style="display:inline-flex; align-items:center; gap:6px; padding:5px 10px; border-radius:5px; background:{{ $isChecked ? '#eff6ff' : '#ffffff' }}; border:1px solid {{ $isChecked ? '#3b82f6' : '#cbd5e1' }}; font-size:12px; font-weight:{{ $isChecked ? '600' : '500' }}; color:{{ $isChecked ? '#1d4ed8' : '#334155' }}; margin:0; cursor:pointer;">
                                                        <input type="checkbox" name="subject_ids[]" value="{{ $sub->id }}" data-name="{{ $sub->name }}" data-subject-name="{{ $sub->name }}" class="exam-subject-checkbox" {{ $isChecked ? 'checked' : '' }} style="margin:0; accent-color:#2563eb;">
                                                        <span class="subject-chip-name">{{ $sub->name }}</span>
                                                    </label>
                                                @endforeach
                                            @else
                                                <span class="text-muted small p-1" id="subjects-empty-notice">
                                                    <i class="fa fa-info-circle"></i> Select a Target Course above to load its subjects automatically.
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ============================================================== --}}
                            {{-- MODE 1 PANE: Traditional Offline Exam (Manual / Excel Upload) --}}
                            {{-- ============================================================== --}}
                            <div id="pane-mode-normal-marks" style="{{ $initialMode === 'normal' ? 'display: block;' : 'display: none;' }}">
                                <div class="builder-section-header mt-3">
                                    <h4><i class="fa fa-sliders text-warning"></i> 2. Subject-Wise Maximum &amp; Passing Cut-off Marks</h4>
                                </div>

                                {{-- Dock location for subjects checklist in Mode 1 --}}
                                <div id="mode1-subjects-dock"></div>

                                {{-- Traditional Mode 1 Subject Marks Table --}}
                                <div class="rounded mb-3" id="subject-marks-config-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
                                    <div class="p-2 px-3 d-flex flex-wrap align-items-center justify-content-between" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; gap: 10px;">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-sliders mr-2" style="font-size: 15px; color: #002C54;"></i>
                                            <span class="font-weight-bold" style="font-size: 13.5px; color: #002C54;">Subject-Wise Marks Configuration</span>
                                        </div>
                                        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                                            <div class="d-inline-flex align-items-center" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 2px 5px;">
                                                <span class="text-muted mr-1" style="font-size: 11px; font-weight: 600;">Max:</span>
                                                <input type="number" id="bulk-subject-max" value="50" min="1" max="1000" step="1" style="width: 58px; height: 26px; font-size: 12px; font-weight: 700; text-align: center; border: 1px solid #cbd5e1; border-radius: 3px; padding: 0 4px; margin-right: 4px; color: #002C54;">
                                                <button type="button" class="btn btn-primary btn-sm py-0 px-2" id="btn-apply-bulk-max" style="height: 26px; font-size: 11px; font-weight: 600; line-height: 26px; border-radius: 3px;">
                                                    Apply All
                                                </button>
                                            </div>
                                            <div class="d-inline-flex align-items-center" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 2px 5px;">
                                                <span class="text-muted mr-1" style="font-size: 11px; font-weight: 600;">Cut-off:</span>
                                                <select id="bulk-subject-min-pct" style="width: 125px; height: 26px; font-size: 11px; font-weight: 600; border: 1px solid #cbd5e1; border-radius: 3px; padding: 0 5px; margin-right: 4px; background: #ffffff; color: #1e293b;">
                                                    <option value="0.33">33% (School)</option>
                                                    <option value="0.35">35% (State Govt)</option>
                                                    <option value="0.40" selected>40% (SSC/PSC)</option>
                                                    <option value="0.50">50% (Merit)</option>
                                                </select>
                                                <button type="button" class="btn btn-success btn-sm py-0 px-2" id="btn-apply-bulk-min" style="height: 26px; font-size: 11px; font-weight: 600; line-height: 26px; border-radius: 3px;">
                                                    Apply All
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                                        <table class="table table-sm table-bordered table-hover mb-0" id="table-subject-marks" style="font-size: 12.5px; border-collapse: separate; border-spacing: 0;">
                                            <thead style="background: #002C54; color: #ffffff;">
                                                <tr>
                                                    <th style="width: 45px; text-align: center; vertical-align: middle; padding: 8px 6px;">#</th>
                                                    <th style="vertical-align: middle; padding: 8px 12px;">Subject Name</th>
                                                    <th style="width: 140px; text-align: center; vertical-align: middle; padding: 8px 10px;">Max Marks <span class="text-warning">*</span></th>
                                                    <th style="width: 150px; text-align: center; vertical-align: middle; padding: 8px 10px;">Min / Pass Marks <span class="text-warning">*</span></th>
                                                    <th style="width: 105px; text-align: center; vertical-align: middle; padding: 8px 10px;">Cut-Off %</th>
                                                </tr>
                                            </thead>
                                            <tbody id="subject-marks-tbody">
                                                @if(!empty($subjects) && $subjects->count() > 0)
                                                    @php $rowIdx = 1; @endphp
                                                    @foreach($subjects as $sub)
                                                        @php
                                                            $isSubChecked = empty($previewSubjectIds) || in_array((int)$sub->id, $previewSubjectIds, true);
                                                            $subMax = isset($existingMinMaxMap[$sub->id]['max']) ? $existingMinMaxMap[$sub->id]['max'] : ($previewPerSubMarks ?: 50);
                                                            $subMin = isset($existingMinMaxMap[$sub->id]['min']) ? $existingMinMaxMap[$sub->id]['min'] : round($subMax * 0.40, 2);
                                                            $subPct = $subMax > 0 ? round(($subMin / $subMax) * 100, 1) : 0;
                                                        @endphp
                                                        <tr class="subject-mark-row {{ $isSubChecked ? '' : 'd-none' }}" id="subject-mark-row-{{ $sub->id }}" data-subject-id="{{ $sub->id }}" data-subject-name="{{ $sub->name }}">
                                                            <td class="text-center font-weight-bold text-muted row-number" style="vertical-align: middle; padding: 8px 6px;">{{ $rowIdx++ }}</td>
                                                            <td style="vertical-align: middle; padding: 8px 12px;">
                                                                <span class="font-weight-600 text-dark" style="font-size: 13px;">{{ $sub->name }}</span>
                                                            </td>
                                                            <td class="text-center" style="vertical-align: middle; padding: 6px 10px;">
                                                                <input type="number" name="subject_max_marks[{{ $sub->id }}]" id="sub_max_m1_{{ $sub->id }}" class="form-control form-control-sm text-center font-weight-bold sub-max-input sub-max-input-mode1" value="{{ $subMax }}" min="1" max="9999" step="0.5" {{ ($initialMode === 'normal' && $isSubChecked) ? '' : 'disabled' }} style="width: 100px; height: 32px; font-size: 13px; margin: 0 auto; border: 1px solid #94a3b8; border-radius: 4px; color: #002C54; background: #ffffff;">
                                                            </td>
                                                            <td class="text-center" style="vertical-align: middle; padding: 6px 10px;">
                                                                <input type="number" name="subject_min_marks[{{ $sub->id }}]" id="sub_min_m1_{{ $sub->id }}" class="form-control form-control-sm text-center font-weight-bold sub-min-input sub-min-input-mode1" value="{{ $subMin }}" min="0" max="9999" step="0.5" {{ ($initialMode === 'normal' && $isSubChecked) ? '' : 'disabled' }} style="width: 100px; height: 32px; font-size: 13px; margin: 0 auto; border: 1px solid #94a3b8; border-radius: 4px; color: #15803d; background: #ffffff;">
                                                            </td>
                                                            <td class="text-center" style="vertical-align: middle; padding: 6px 10px;">
                                                                <span class="sub-cutoff-badge" id="sub_cutoff_badge_m1_{{ $sub->id }}" style="display: inline-block; min-width: 58px; padding: 4px 8px; font-size: 11.5px; font-weight: 700; border-radius: 4px; border: 1px solid #bae6fd; background: #f0f9ff; color: #0284c7;">
                                                                    {{ $subPct }}%
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr id="subject-marks-empty-row">
                                                        <td colspan="5" class="text-center text-muted py-3">
                                                            <i class="fa fa-info-circle mr-1"></i> Select a Target Course above to load and configure subject marks.
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            <tfoot style="background: #f1f5f9; font-weight: 700; border-top: 2px solid #cbd5e1;">
                                                <tr>
                                                    <td colspan="2" class="text-right text-dark" style="font-size: 12.5px; vertical-align: middle; padding: 8px 12px;">
                                                        Total Weightage (<span id="total-active-subs-count">0</span> Active Subjects):
                                                    </td>
                                                    <td class="text-center" style="vertical-align: middle; padding: 8px 10px;">
                                                        <span id="grand-total-max-marks" style="font-size: 14px; font-weight: 800; color: #002C54;">0.00</span>
                                                    </td>
                                                    <td class="text-center" style="vertical-align: middle; padding: 8px 10px;">
                                                        <span id="grand-total-min-marks" style="font-size: 14px; font-weight: 800; color: #15803d;">0.00</span>
                                                    </td>
                                                    <td class="text-center" style="vertical-align: middle; padding: 8px 10px;">
                                                        <span id="grand-total-cutoff-pct" style="font-size: 12px; font-weight: 700; color: #475569;">--</span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="theme-form-group">
                                            <label for="exam-max-marks">Maximum Marks (Per Subject) <span class="text-danger">*</span></label>
                                            <input type="number" id="exam-max-marks" name="exam_maximum_marks" class="theme-form-control" value="{{ $previewPerSubMarks }}" min="1" max="9999" step="0.5">
                                            <div class="theme-hint" id="exam-max-marks-hint" style="color: #475569; font-weight: 500;">
                                                <i class="fa fa-calculator text-primary"></i> <span id="max-marks-calc-text"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="theme-form-group">
                                            <label for="normal-question-paper">Question Paper <span class="badge badge-secondary" style="font-size: 8.5px;">Optional PDF</span></label>
                                            <input type="file" id="normal-question-paper" name="question_paper" accept="application/pdf,.pdf" class="theme-form-control" style="height: 33px; padding: 3px;">
                                            @if(!empty($formExam->question_paper_path))
                                                <div class="mt-1 small">
                                                    <a href="{{ asset($formExam->question_paper_path) }}" target="_blank" class="text-primary font-weight-bold">
                                                        <i class="fa fa-file-pdf-o text-danger mr-1"></i> View Attached PDF
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ============================================================== --}}
                            {{-- MODE 2 PANE: PDF Paper + Printable OMR Sheet                   --}}
                            {{-- ============================================================== --}}
                            <div id="pane-mode-pdf-upload" style="{{ $initialMode === 'pdf_upload' ? 'display: block;' : 'display: none;' }}">
                                
                                {{-- Step 2: Paper Specifications & Default Scoring --}}
                                <div class="builder-section-header mt-3">
                                    <h4><i class="fa fa-file-pdf-o text-warning"></i> 2. Question Paper Setup &amp; Default Marking</h4>
                                    <span class="badge badge-primary py-1 px-2" id="pdf-qcount-badge" style="font-size: 10px;">{{ $previewQuestions }} Questions</span>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="theme-form-group mb-2">
                                            <label for="exam-question-count">Total Questions <span class="text-danger">*</span></label>
                                            <input type="number" id="exam-question-count" name="number_of_questions" class="theme-form-control font-weight-bold" value="{{ $previewQuestions }}" min="1" max="100" style="font-size: 14px; color: #002C54;">
                                            <div class="d-flex align-items-center mt-1" style="gap: 4px;">
                                                <span class="text-muted" style="font-size: 10px; font-weight: 600;">Presets:</span>
                                                <span class="quick-chip-btn" data-count="20">20 Qs</span>
                                                <span class="quick-chip-btn" data-count="30">30 Qs</span>
                                                <span class="quick-chip-btn {{ $previewQuestions == 50 ? 'active' : '' }}" data-count="50">50 Qs</span>
                                                <span class="quick-chip-btn" data-count="75">75 Qs</span>
                                                <span class="quick-chip-btn" data-count="100">100 Qs</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="theme-form-group mb-2">
                                            <label for="exam-question-paper">Question Paper <span class="text-muted font-weight-normal">(PDF up to 10MB)</span></label>
                                            <input type="file" id="exam-question-paper" name="question_paper" accept="application/pdf,.pdf" class="theme-form-control" style="height: 33px; padding: 3px;">
                                            @if(!empty($formExam->question_paper_path))
                                                <div class="mt-1">
                                                    <a href="{{ asset($formExam->question_paper_path) }}" target="_blank" class="badge badge-primary py-1 px-2">
                                                        <i class="fa fa-file-pdf-o mr-1"></i> View Attached Question Paper
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="theme-form-group mb-1">
                                            <label for="marks-per-correct">Default Marks for Correct <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text bg-success text-white font-weight-bold" style="font-size:11px;">+</span></div>
                                                <input type="number" id="marks-per-correct" name="marks_per_correct" class="theme-form-control font-weight-bold text-success" value="{{ old('marks_per_correct', $formExam->marks_per_correct ?? 4) }}" min="0" max="999" step="0.5">
                                            </div>
                                            <div class="theme-hint text-muted" style="font-size: 10.5px;">Applied to all questions by default (can be overridden per section).</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="theme-form-group mb-1">
                                            <label for="marks-per-incorrect">Default Negative Marks</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text bg-danger text-white font-weight-bold" style="font-size:11px;">-</span></div>
                                                <input type="number" id="marks-per-incorrect" name="marks_per_incorrect" class="theme-form-control font-weight-bold text-danger" value="{{ old('marks_per_incorrect', $formExam->marks_per_incorrect ?? -1) }}" min="-999" max="0" step="0.25">
                                            </div>
                                            <div class="theme-hint text-muted" style="font-size: 10.5px;">E.g. -1 for 1 mark deduction, -0.25 for 1/4th penalty, 0 for none.</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Step 3: Included Subjects & Unified Master Section Table --}}
                                <div class="builder-section-header mt-3">
                                    <h4><i class="fa fa-sitemap text-warning"></i> 3. Subject Sections, Question Distribution &amp; Cut-off Marks</h4>
                                </div>

                                {{-- Dock location for subjects checklist in Mode 2 --}}
                                <div id="mode2-subjects-dock"></div>

                                {{-- Unified Section Table --}}
                                <div class="mb-3 rounded" id="question-ranges-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
                                    <div class="p-2 px-3 d-flex flex-wrap align-items-center justify-content-between" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; gap: 8px;">
                                        <div class="d-flex align-items-center">
                                            <span class="font-weight-bold" style="font-size: 12.5px; color: #002C54;">
                                                <i class="fa fa-sliders text-primary mr-1"></i> Section Weightage &amp; Qualifying Cut-off Scheme
                                            </span>
                                            <span class="badge ml-2" id="range-validation-status-badge" style="font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 12px; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;">
                                                <i class="fa fa-check-circle mr-1"></i> {{ $previewQuestions }} / {{ $previewQuestions }} Mapped
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                            <button type="button" class="btn btn-sm btn-outline-success font-weight-bold" id="btn-auto-split-ranges" style="height: 26px; font-size: 11px; padding: 0 10px; border-radius: 4px; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;">
                                                <i class="fa fa-magic mr-1"></i> Auto-Split Evenly
                                            </button>
                                            <div class="d-inline-flex align-items-center" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 4px;">
                                                <span class="text-muted mr-1" style="font-size: 10.5px; font-weight: 600;">Cut-off:</span>
                                                <select id="bulk-range-min-pct" style="width: 120px; height: 24px; font-size: 11px; font-weight: 600; border: 1px solid #cbd5e1; border-radius: 3px; padding: 0 4px; background: #ffffff; color: #1e293b;">
                                                    <option value="0.33">33% (School)</option>
                                                    <option value="0.35">35% (State Govt)</option>
                                                    <option value="0.40" selected>40% (SSC/PSC)</option>
                                                    <option value="0.50">50% (Merit)</option>
                                                </select>
                                                <button type="button" class="btn btn-success btn-sm py-0 px-2 ml-1" id="btn-apply-range-bulk-min" style="height: 24px; font-size: 10.5px; font-weight: 600; line-height: 24px; border-radius: 3px;">
                                                    Apply All
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-hover mb-0" id="table-question-ranges" style="font-size: 12px; border-collapse: separate; border-spacing: 0;">
                                            <thead style="background: #002C54; color: #ffffff;">
                                                <tr>
                                                    <th style="width: 35px; text-align: center; vertical-align: middle;">#</th>
                                                    <th style="vertical-align: middle;">Subject Section</th>
                                                    <th style="width: 75px; text-align: center; vertical-align: middle;">From Q</th>
                                                    <th style="width: 75px; text-align: center; vertical-align: middle;">To Q</th>
                                                    <th style="width: 65px; text-align: center; vertical-align: middle;">Qs</th>
                                                    <th style="width: 75px; text-align: center; vertical-align: middle;">+ Correct</th>
                                                    <th style="width: 75px; text-align: center; vertical-align: middle;">- Wrong</th>
                                                    <th style="width: 90px; text-align: center; vertical-align: middle;">Section Max</th>
                                                    <th style="width: 75px; text-align: center; vertical-align: middle;">Cut-off %</th>
                                                    <th style="width: 95px; text-align: center; vertical-align: middle;">Pass Marks</th>
                                                </tr>
                                            </thead>
                                            <tbody id="question-ranges-tbody">
                                                {{-- Dynamically populated by JS --}}
                                            </tbody>
                                            <tfoot style="background: #f1f5f9; font-weight: 700; border-top: 2px solid #cbd5e1;">
                                                <tr>
                                                    <td colspan="4" class="text-right text-dark" style="font-size: 12px; vertical-align: middle; padding: 6px 10px;">Mapped Questions:</td>
                                                    <td class="text-center font-weight-bold text-success" id="range-total-q-count" style="vertical-align: middle; font-size: 12.5px;">{{ $previewQuestions }}</td>
                                                    <td colspan="2" class="text-right text-dark" style="font-size: 12px; vertical-align: middle; padding: 6px 10px;">Total Weightage:</td>
                                                    <td class="text-center font-weight-bold" style="color: #002C54; font-size: 13px; vertical-align: middle;" id="range-total-max-marks">0.00</td>
                                                    <td class="text-center font-weight-bold" style="color: #475569; font-size: 12px; vertical-align: middle;" id="grand-total-cutoff-pct">40.0%</td>
                                                    <td class="text-center font-weight-bold" style="color: #15803d; font-size: 13px; vertical-align: middle;" id="range-total-min-marks">0.00</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="px-3 py-1 text-muted" style="font-size: 10.5px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
                                        <i class="fa fa-info-circle text-primary mr-1"></i> <strong>Indian Coaching Standard:</strong> Question counts determine Section Max Marks automatically. Section Passing Cut-off marks are saved to exam evaluation and student rank reports.
                                    </div>
                                </div>

                                {{-- Hidden storage for JSON payloads --}}
                                <input type="hidden" name="subject_question_ranges" id="hidden_subject_question_ranges" value="{{ json_encode($formExam->subject_question_ranges ?? []) }}">
                                <input type="hidden" name="question_configs" id="hidden_question_configs" value="{{ json_encode($formExam->question_configs ?? []) }}">

                                {{-- Step 4: Answer Key Matrix (OMR Options) --}}
                                <div class="builder-section-header">
                                    <h4><i class="fa fa-key text-warning"></i> 4. Answer Key (OMR Options)</h4>
                                    <span class="badge badge-info py-1 px-2" id="answer-key-progress" style="font-size: 10px;">0 / {{ $previewQuestions }} Selected</span>
                                </div>
                                <div class="answer-key-box mb-2">
                                    <div class="answer-key-toolbar">
                                        <span class="small font-weight-bold" style="font-size: 10px;">Select correct option:</span>
                                        <div class="ak-btn-group">
                                            <button type="button" data-fill="A">All A</button>
                                            <button type="button" data-fill="B">All B</button>
                                            <button type="button" data-fill="C">All C</button>
                                            <button type="button" data-fill="D">All D</button>
                                            <button type="button" data-fill="RANDOM">Random</button>
                                            <button type="button" data-fill="CLEAR">Clear</button>
                                        </div>
                                    </div>
                                    <div class="answer-key-list" id="exam-answer-key-grid">
                                        {{-- Rendered by JS in responsive 2-column grid --}}
                                    </div>
                                </div>
                            </div>

                            {{-- ============================================================== --}}
                            {{-- MODE 3 & 4 PANE: Question Bank Questions                       --}}
                            {{-- ============================================================== --}}
                            <div id="pane-mode-question-bank" style="{{ in_array($initialMode, ['question_bank', 'online']) ? 'display: block;' : 'display: none;' }}">
                                <div class="builder-section-header">
                                    <h4><i class="fa fa-list-ol text-warning"></i> Questions From Question Bank</h4>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge badge-primary py-1 px-2" id="selected-questions-count-badge" style="font-size: 10px;">0 Questions</span>
                                        <button type="button" id="btn-open-question-modal" class="dash-btn dash-btn-sky" style="height: 24px; font-size: 10px; padding: 2px 8px;">
                                            <i class="fa fa-plus-circle mr-1"></i> Browse Question Bank
                                        </button>
                                    </div>
                                </div>

                                {{-- Dock location for subjects checklist in Mode 3 --}}
                                <div id="mode3-subjects-dock"></div>

                                {{-- Empty state message --}}
                                <div id="qbank-empty-state" class="text-center py-4 text-muted mb-2" style="border: 2px dashed #cbd5e1; border-radius: 4px; background: #f8fafc;">
                                    <i class="fa fa-book fa-2x mb-2 text-secondary"></i>
                                    <p class="font-weight-bold mb-1" style="font-size: 12px; color: #1e293b;">No Questions Added Yet</p>
                                    <p class="small mb-3" style="font-size: 11px; color: #475569;">Click the button below to browse questions from your Question Bank and attach them to this exam.</p>
                                    <button type="button" id="btn-open-question-modal-empty" class="dash-btn dash-btn-sky">
                                        <i class="fa fa-search mr-1"></i> Browse Question Bank
                                    </button>
                                </div>

                                {{-- Selected Questions Container --}}
                                <div id="selected-questions-list" style="max-height: 480px; overflow-y: auto;">
                                    {{-- Rendered by JS --}}
                                </div>

                                {{-- Mode 3 Only: Auto-derived Answer Key Review (Collapsible) --}}
                                <div class="mt-2 border-top pt-2" id="qbank-derived-answer-key-box" style="{{ $initialMode === 'question_bank' ? '' : 'display: none;' }}">
                                    <a class="d-flex align-items-center justify-content-between text-decoration-none" data-toggle="collapse" href="#qbank-answer-key-collapse" role="button">
                                        <span class="small font-weight-bold" style="font-size: 11px; color: #002C54;">
                                            <i class="fa fa-key mr-1 text-warning"></i> View Auto-Derived Answer Key (<span id="qbank-key-count">0</span>)
                                        </span>
                                        <i class="fa fa-chevron-down text-muted small"></i>
                                    </a>
                                    <div class="collapse mt-2" id="qbank-answer-key-collapse">
                                        <div class="answer-key-box">
                                            <div class="answer-key-list" id="qbank-derived-answer-key-grid" style="max-height: 180px;">
                                                {{-- Derived answer key rows --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Mode 4 Only: Online CBT Notice --}}
                                <div class="mt-2 border-top pt-2" id="online-exam-notice-box" style="{{ $initialMode === 'online' ? '' : 'display: none;' }}">
                                    <div class="p-2 border rounded small" style="background: #eff6ff; border-color: #bae6fd !important; color: #0c4a6e;">
                                        <i class="fa fa-info-circle text-info mr-1"></i> <strong>Online CBT Mode:</strong> Questions selected here will appear on the Student Online Examination Portal for students to answer digitally.
                                    </div>
                                </div>
                            </div>

                            {{-- Mode 4 Online Only: Test Duration in Minutes --}}
                            <div id="pane-mode-online-duration" style="{{ $initialMode === 'online' ? 'display: block;' : 'display: none;' }}">
                                <div class="theme-form-group mt-2">
                                    <label for="exam-duration-input"><i class="fa fa-clock-o text-info mr-1"></i> Online Exam Duration (Minutes) <span class="text-danger">*</span></label>
                                    <input type="number" id="exam-duration-input" name="duration" class="theme-form-control" value="{{ old('duration', $formExam->duration ?? 60) }}" min="1" max="1440" step="1">
                                    <div class="theme-hint">Total test countdown time given to student in online panel (e.g. 60, 90, 180 min)</div>
                                </div>
                            </div>

                            {{-- Common Instructions / Remarks --}}
                            <div class="theme-form-group mt-3">
                                <label for="description"><i class="fa fa-pencil-square-o text-primary mr-1"></i> Instructions / Remarks</label>
                                <textarea id="description" name="description" rows="2" class="theme-form-control" placeholder="Optional examination notes or instructions...">{{ old('description', $formExam->description ?? '') }}</textarea>
                            </div>

                        </div>

                        {{-- Pinned Footer on Left Card --}}
                        <div class="builder-card-footer">
                            <button type="button" id="btn-submit-save" class="btn-create-exam-main">
                                <i class="fa fa-{{ !empty($formExam) ? 'save' : 'plus-circle' }} mr-1"></i> {{ !empty($formExam) ? 'Update Exam' : 'Create Exam' }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Live Previews (Equal Height Matching Left Card) --}}
                <div class="exam-builder-col-right">

                    {{-- Mode 1 Right Panel: Normal Offline Exam Overview & Workflow --}}
                    <div id="right-panel-normal" class="builder-main-card builder-right-panel" style="{{ $initialMode === 'normal' ? '' : 'display: none;' }}">
                        <div class="theme-card-header">
                            <h3 class="card-title">
                                <i class="fa fa-file-text-o text-warning"></i> Standard Offline Examination Overview
                            </h3>
                            <span class="header-badge" id="normal-badge-status">Traditional Offline</span>
                        </div>
                        <div class="theme-card-body p-3 d-flex flex-column">
                            {{-- Parameters Summary Table --}}
                            <div class="normal-summary-box">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                    <span class="meta-label"><i class="fa fa-pencil-square-o mr-1"></i> Exam Name:</span>
                                    <strong class="meta-value" id="normal-summary-name">{{ $previewName ?: 'Untitled Examination' }}</strong>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <span class="meta-label">Target Course:</span> <span id="normal-summary-class" class="meta-value">--</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="meta-label">Subject:</span> <span id="normal-summary-subject" class="meta-value">All Subjects</span>
                                    </div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <span class="meta-label">Exam Date:</span> <span id="normal-summary-date" class="meta-value">{{ date('d M Y') }}</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="meta-label">Total Marks:</span> <span id="normal-summary-max-marks" class="badge badge-success px-2 font-weight-bold" style="font-size: 11px;">
                                            @if($initialEffectiveSubs > 1)
                                                {{ $previewTotalMarks }} <span style="font-size: 9.5px; opacity: 0.9; font-weight: normal;">({{ $previewPerSubMarks }} &times; {{ $initialEffectiveSubs }})</span>
                                            @else
                                                {{ $previewTotalMarks }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                    <span class="meta-label">Question Paper (PDF):</span>
                                    <span id="normal-summary-paper" class="badge badge-secondary">Optional Attached</span>
                                </div>
                            </div>

                            {{-- 4-Step Process Guide --}}
                            <h6 class="font-weight-bold mb-2" style="font-size: 12px; color: #0f172a;">
                                <i class="fa fa-map-signs text-primary mr-1"></i> Examination Lifecycle & Workflow:
                            </h6>
                            <div class="workflow-steps-vertical mb-3">
                                <div class="wf-step-item">
                                    <div class="wf-step-icon"><i class="fa fa-check"></i></div>
                                    <div class="wf-step-content">
                                        <strong>Step 1: Exam Created & Scheduled</strong>
                                        <p>Exam basic parameters saved for the assigned class. Optional question paper PDF can be downloaded anytime.</p>
                                    </div>
                                </div>
                                <div class="wf-step-item">
                                    <div class="wf-step-icon"><i class="fa fa-users"></i></div>
                                    <div class="wf-step-content">
                                        <strong>Step 2: Conduct Offline Classroom Exam</strong>
                                        <p>Print/distribute question papers to students in classroom and evaluate their written copies.</p>
                                    </div>
                                </div>
                                <div class="wf-step-item">
                                    <div class="wf-step-icon"><i class="fa fa-upload"></i></div>
                                    <div class="wf-step-content">
                                        <strong>Step 3: Upload Student Marks</strong>
                                        <p>Marks can be entered in two fast ways:
                                            <span class="d-block mt-1">
                                                &bull; <strong>Option A: Excel Sheet Upload</strong> — Download student sample template, fill numbers, and import.
                                            </span>
                                            <span class="d-block">
                                                &bull; <strong>Option B: Manual Marks Entry</strong> — Fill marks online student-by-student in table.
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="wf-step-item">
                                    <div class="wf-step-icon"><i class="fa fa-trophy"></i></div>
                                    <div class="wf-step-content">
                                        <strong>Step 4: Result Declaration & Report Cards</strong>
                                        <p>Declare result, generate student marksheets and export performance analytics.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Quick Links --}}
                            <div class="normal-quick-actions">
                                <a href="{{ url('fill-marks-by-excel') }}" class="dash-btn dash-btn-light" style="border: 1px solid #cbd5e1;">
                                    <i class="fa fa-file-excel-o text-success mr-1"></i> Excel Marks Upload
                                </a>
                                <a href="{{ url('fill_marks') }}" class="dash-btn dash-btn-light" style="border: 1px solid #cbd5e1;">
                                    <i class="fa fa-pencil text-primary mr-1"></i> Manual Marks Entry
                                </a>
                                <a href="{{ url('view/exam') }}" class="dash-btn dash-btn-outline" style="background: #e2e8f0; color: #002C54 !important; border: 1px solid #cbd5e1;">
                                    <i class="fa fa-list mr-1"></i> View All Examinations
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Mode 2 Right Panel: OMR Sheet Live Preview --}}
                    <div id="right-panel-omr" class="builder-main-card builder-right-panel" style="{{ $initialMode === 'pdf_upload' ? '' : 'display: none;' }}">
                        <div class="theme-card-header">
                            <h3 class="card-title">
                                <i class="fa fa-eye text-warning"></i> Printable OMR Sheet Preview
                            </h3>
                            <div class="d-flex align-items-center gap-2">
                                <span class="header-badge" id="exam-preview-count">{{ $previewQuestions }} Questions</span>
                                <button type="button" class="dash-btn dash-btn-light py-0 px-2 btn-trigger-print-omr" style="height: 24px; font-size: 10.5px;">
                                    <i class="fa fa-print mr-1"></i> Print OMR Sheet
                                </button>
                            </div>
                        </div>

                        <div class="omr-sheet-viewport">
                            <div class="omr-sheet-paper" id="exam-omr-print-area">
                                <svg id="exam-omr-svg" viewBox="0 0 800 1100" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" class="d-block w-100 h-auto bg-white" aria-label="Printable exam OMR sheet">
                                    <rect width="800" height="1100" fill="white"/>
                                    <rect x="24" y="24" width="752" height="1052" fill="none" stroke="#111827" stroke-width="3"/>
                                    
                                    {{-- Corner Marks --}}
                                    @foreach([[35,35],[739,35],[35,1039],[739,1039]] as $mark)
                                        <rect x="{{ $mark[0] }}" y="{{ $mark[1] }}" width="26" height="26" fill="#000000"/>
                                    @endforeach

                                    {{-- School / Institute Header --}}
                                    <text x="400" y="70" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="24" font-weight="800" fill="#002C54">{{ mb_strtoupper($previewInstituteName) }}</text>
                                    <text x="400" y="94" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="600" fill="#475569">OPTICAL MARK RECOGNITION (OMR) ANSWER SHEET</text>
                                    
                                    {{-- Exam Meta --}}
                                    <text id="omr-preview-name" x="70" y="128" text-anchor="start" font-family="Arial, Helvetica, sans-serif" font-size="12" font-weight="700" fill="#0f172a">EXAM: {{ mb_strtoupper($previewName) }}</text>
                                    <text id="omr-preview-code" x="730" y="128" text-anchor="end" font-family="Arial, Helvetica, sans-serif" font-size="11" font-weight="700" fill="#002C54">EXAM CODE: {{ $previewCode }}</text>
                                    <line x1="70" y1="145" x2="730" y2="145" stroke="#002C54" stroke-width="2"/>

                                    {{-- Student Info Lines --}}
                                    <text x="75" y="174" font-family="Arial" font-size="13" font-weight="700" fill="#0f172a">Student Name:</text>
                                    <line x1="185" y1="176" x2="470" y2="176" stroke="#94a3b8"/>
                                    <text x="500" y="174" font-family="Arial" font-size="13" font-weight="700" fill="#0f172a">Date:</text>
                                    <line x1="545" y1="176" x2="720" y2="176" stroke="#94a3b8"/>

                                    {{-- Roll Number Matrix --}}
                                    <text x="70" y="202" font-family="Arial" font-size="13" font-weight="800" fill="#002C54">ROLL NUMBER (FILL ONE BUBBLE PER DIGIT)</text>
                                    @for($column=0;$column<6;$column++)
                                        @php($rx=70+$column*110)
                                        <rect x="{{ $rx }}" y="212" width="100" height="190" fill="none" stroke="#94a3b8" stroke-width="1"/>
                                        <text x="{{ $rx+50 }}" y="228" text-anchor="middle" font-family="Arial" font-size="9" font-weight="700" fill="#002C54">DIGIT {{ $column+1 }}</text>
                                        @for($digit=0;$digit<10;$digit++)
                                            @php($ry=245+$digit*15.5)
                                            <text x="{{ $rx+24 }}" y="{{ $ry+3 }}" text-anchor="middle" font-family="Arial" font-size="8.5" fill="#334155">{{ $digit }}</text>
                                            <circle cx="{{ $rx+50 }}" cy="{{ $ry }}" r="6" fill="white" stroke="#000000" stroke-width="1.2"/>
                                            <line x1="{{ $rx+7 }}" y1="{{ $ry+7 }}" x2="{{ $rx+93 }}" y2="{{ $ry+7 }}" stroke="#f1f5f9" stroke-width="0.8"/>
                                        @endfor
                                    @endfor

                                    {{-- Question Matrix Header --}}
                                    <text id="omr-question-total" x="70" y="432" font-family="Arial" font-size="14" font-weight="800" fill="#002C54">ANSWERS — {{ $previewQuestions }} QUESTIONS</text>
                                    <text x="730" y="432" text-anchor="end" font-family="Arial" font-size="10" font-weight="700" fill="#64748b">DARKEN THE APPROPRIATE BUBBLE COMPLETELY</text>
                                    
                                    {{-- Dynamic Question Grid --}}
                                    <g id="omr-question-grid"></g>

                                    {{-- Instructions Footer --}}
                                    <text x="400" y="1042" text-anchor="middle" font-family="Arial" font-size="10" font-weight="700" fill="#0f172a">INSTRUCTIONS: Use BLUE or BLACK Ball Point Pen | Fill only one circle per question | Do not fold or tear</text>
                                    <text x="400" y="1060" text-anchor="middle" font-family="Arial" font-size="9.5" font-weight="600" fill="#475569">Keep sheet flat and clean | Ensure all 4 black corner squares remain clean &amp; visible for OMR Optical Scanner</text>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Mode 3 Right Panel: Dual Tabs (Question Paper & OMR Sheet) --}}
                    <div id="right-panel-qbank" class="builder-main-card builder-right-panel" style="{{ $initialMode === 'question_bank' ? '' : 'display: none;' }}">
                        <div class="theme-card-header">
                            <ul class="nav nav-pills" id="qbank-preview-tabs" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" id="tab-paper-btn" role="tab" aria-selected="true">
                                        <i class="fa fa-file-text-o"></i> Question Paper Preview
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" id="tab-omr-btn" role="tab" aria-selected="false">
                                        <i class="fa fa-th"></i> Matching OMR Sheet
                                    </button>
                                </li>
                            </ul>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" id="btn-print-active-tab" class="dash-btn dash-btn-light py-0 px-2" style="height: 24px; font-size: 10.5px; font-weight: 700;">
                                    <i class="fa fa-print mr-1"></i> <span id="print-active-label">Print Question Paper</span>
                                </button>
                            </div>
                        </div>

                        <div class="tab-content flex-grow-1" id="qbank-tab-content" style="height: calc(100% - 38px); min-height: 0; overflow: hidden; display: flex; flex-direction: column;">
                            {{-- Tab 1: Printable Question Paper --}}
                            <div class="tab-pane flex-grow-1 active show" id="tab-paper-content" role="tabpanel" style="height: 100%; min-height: 0; overflow: hidden; display: flex; flex-direction: column;">
                                <div class="paper-preview-container" style="height: 100%;">
                                    <div class="printable-exam-paper" id="exam-paper-print-area">
                                        {{-- Paper Header --}}
                                        <div class="exam-paper-header">
                                            <h2 class="exam-paper-institute">{{ mb_strtoupper($previewInstituteName) }}</h2>
                                            <h3 class="exam-paper-exam-name" id="paper-preview-title">{{ $previewName }}</h3>
                                            <table class="exam-paper-meta-table">
                                                <tr>
                                                    <td style="text-align: left; width: 33%;"><strong>Class:</strong> <span id="paper-preview-class">--</span></td>
                                                    <td style="text-align: center; width: 34%;"><strong>Subject:</strong> <span id="paper-preview-subject">All Subjects</span></td>
                                                    <td style="text-align: right; width: 33%;"><strong>Date:</strong> <span id="paper-preview-date">{{ date('d M Y') }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: left;"><strong>Code:</strong> {{ $previewCode }}</td>
                                                    <td style="text-align: center;"><strong>Total Questions:</strong> <span id="paper-preview-total-q">0</span></td>
                                                    <td style="text-align: right;"><strong>Max. Marks:</strong> <span id="paper-preview-max-marks">0</span></td>
                                                </tr>
                                            </table>
                                        </div>

                                        {{-- Instructions --}}
                                        <div class="exam-instructions-box">
                                            <strong style="color: #002C54;">General Instructions:</strong>
                                            <ul class="mb-0 pl-3">
                                                <li>All questions are compulsory. Fill answers in the accompanying OMR answer sheet.</li>
                                                <li>Each correct answer carries <strong id="paper-correct-marks-label">1.0</strong> mark(s). Negative marking: <strong id="paper-negative-marks-label">0</strong>.</li>
                                                <li>Use blue or black ball point pen only to darken the bubbles on the OMR sheet.</li>
                                            </ul>
                                        </div>

                                        {{-- Questions List Container --}}
                                        <div id="exam-paper-questions-flow">
                                            <div class="text-center py-4 text-muted">
                                                <em>Please add questions from Question Bank to generate paper preview...</em>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 2: Matching OMR Sheet --}}
                            <div class="tab-pane flex-grow-1" id="tab-omr-content" role="tabpanel" style="height: 100%; min-height: 0; overflow: hidden; display: none;">
                                <div class="omr-sheet-viewport" style="height: 100%;">
                                    <div class="omr-sheet-paper" id="exam-omr-mode2-paper">
                                        {{-- Injected / mirrored from SVG --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mode 4 Right Panel: Online Exam (CBT Panel) Live Preview --}}
                    <div id="right-panel-online" class="builder-main-card builder-right-panel" style="{{ $initialMode === 'online' ? '' : 'display: none;' }}">
                        <div class="theme-card-header">
                            <h3 class="card-title">
                                <i class="fa fa-laptop text-info"></i> Online Examination (CBT) Panel Preview
                            </h3>
                            <div class="d-flex align-items-center gap-2">
                                <span class="header-badge" id="online-preview-count">0 Questions</span>
                                <span class="header-badge" style="background: #0284c7; color: #ffffff;" id="online-preview-duration">60 Mins</span>
                            </div>
                        </div>

                        <div class="theme-card-body p-3 d-flex flex-column">
                            {{-- Online Notice Banner (High Contrast) --}}
                            <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2" style="background: #eff6ff; border: 1px solid #bae6fd; border-left: 4px solid #0284c7; color: #0c4a6e; font-size: 11px;">
                                <i class="fa fa-info-circle fa-lg text-info mr-1"></i>
                                <div>
                                    <strong style="color: #0369a1;">Online Computer-Based Test (CBT):</strong>
                                    Students will take this examination digitally on the online examination portal. <u>No physical OMR sheet is generated</u> for this exam mode.
                                </div>
                            </div>

                            {{-- Online Test Preview Flow --}}
                            <div style="flex: 1; min-height: 0; overflow-y: auto; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 12px;">
                                <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                                    <div>
                                        <h5 class="font-weight-bold mb-0" id="online-paper-title" style="font-size: 13.5px; color: #0f172a;">{{ $previewName ?: 'Untitled Online Examination' }}</h5>
                                        <div class="small mt-1" style="color: #475569;">
                                            <span id="online-paper-class">Class: --</span> &bull; 
                                            <span id="online-paper-subject">Subject: All Subjects</span> &bull; 
                                            <span id="online-paper-date">Date: {{ date('d M Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge badge-success px-2 py-1 font-weight-bold" id="online-preview-max-marks">0 Marks</span>
                                    </div>
                                </div>

                                {{-- Question flow rendered by JS --}}
                                <div id="online-paper-questions-flow">
                                    <div class="text-center py-5 text-muted">
                                        <i class="fa fa-laptop fa-2x mb-2 text-info"></i>
                                        <p class="mb-0 font-weight-bold" style="color: #1e293b;">No questions added yet for Online Exam.</p>
                                        <p class="small text-muted">Click "Browse Question Bank" on the left to add questions to this online exam.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>

{{-- 3. Question Bank Picker Modal --}}
<div class="modal fade" id="modal-question-bank" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 95%;">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: 1px solid #cbd5e1;">
            <div class="modal-header modal-header-theme">
                <h5 class="modal-title">
                    <i class="fa fa-book mr-1"></i> Select Questions from Question Bank
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Filter Bar --}}
            <div class="modal-filter-bar">
                <div style="min-width: 160px;">
                    <label class="small font-weight-bold mb-0" style="font-size: 10px; color: #1e293b;">Course / Batch:</label>
                    <select id="modal-filter-class" class="theme-form-control">
                        <option value="">-- All Courses / Batches --</option>
                        @if(!empty($classType))
                            @foreach($classType as $type)
                                <option value="{{ $type->id }}" {{ $previewClassId == $type->id ? 'selected' : '' }}>
                                    {{ $type->name ?? '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div style="min-width: 160px;">
                    <label class="small font-weight-bold mb-0" style="font-size: 10px; color: #1e293b;">Subject:</label>
                    <select id="modal-filter-subject" class="theme-form-control">
                        <option value="">-- All Subjects --</option>
                        @if(!empty($subjects))
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}" {{ $previewSubjectId == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div style="min-width: 140px;">
                    <label class="small font-weight-bold mb-0" style="font-size: 10px; color: #1e293b;">Question Type:</label>
                    <select id="modal-filter-type" class="theme-form-control">
                        <option value="">All Question Types</option>
                        <option value="1">1: Multiple Choice (MCQ)</option>
                        <option value="2">2: Numerical / Descriptive</option>
                    </select>
                </div>

                <div class="flex-grow-1" style="min-width: 180px;">
                    <label class="small font-weight-bold mb-0" style="font-size: 10px; color: #1e293b;">Search Questions:</label>
                    <input type="text" id="modal-filter-search" class="theme-form-control" placeholder="Search by question text or formula...">
                </div>

                <div style="align-self: flex-end;">
                    <button type="button" id="modal-btn-refresh" class="dash-btn dash-btn-sky" style="height: 29px;">
                        <i class="fa fa-search mr-1"></i> Filter Questions
                    </button>
                </div>
            </div>

            {{-- Questions List Container --}}
            <div class="modal-questions-scroll" id="modal-questions-container">
                <div class="text-center py-5 text-muted" id="modal-loading-indicator">
                    <i class="fa fa-spinner fa-spin fa-2x mb-2 text-primary"></i>
                    <p class="mb-0" style="font-size: 12px; color: #1e293b;">Loading questions from Question Bank...</p>
                </div>
                <div id="modal-questions-list">
                    {{-- Dynamically populated via AJAX --}}
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer py-2 px-3 bg-light d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <label class="mb-0 small font-weight-bold" style="cursor: pointer; color: #0f172a;">
                        <input type="checkbox" id="modal-select-all-visible" class="mr-1"> Select All Visible
                    </label>
                    <span class="badge badge-primary py-1 px-2" id="modal-selected-badge" style="font-size: 11px;">0 Selected</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="modal-btn-apply-selection" class="btn btn-sm btn-success font-weight-bold px-3">
                        <i class="fa fa-check mr-1"></i> Add Selected Questions (<span id="modal-apply-count">0</span>)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Individual Question Marks Override Modal --}}
<div class="modal fade" id="modalQuestionMarks" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 420px;">
        <div class="modal-content" style="border-radius: 6px; overflow: hidden; border: 1px solid #cbd5e1; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            <div class="modal-header py-2 px-3" style="background: #002C54; color: #ffffff;">
                <h5 class="modal-title font-weight-bold" style="font-size: 13px; color: #ffffff;">
                    <i class="fa fa-sliders mr-1 text-warning"></i> Question Scoring Override: <span id="qmodal-title-num">Q1</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" style="background: #ffffff; font-size: 12px;">
                <input type="hidden" id="qmodal-qnum" value="1">
                <div class="form-group mb-2">
                    <label class="font-weight-bold text-dark mb-1">Mapped Subject</label>
                    <select id="qmodal-subject-select" class="form-control form-control-sm font-weight-bold" style="height: 32px; border: 1px solid #cbd5e1;">
                        {{-- Populated from active subjects --}}
                    </select>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-2">
                            <label class="font-weight-bold text-success mb-1">Marks for Correct (+)</label>
                            <input type="number" id="qmodal-marks-correct" class="form-control form-control-sm font-weight-bold text-center" value="1" min="0" max="999" step="0.5" style="height: 32px; border: 1px solid #cbd5e1; color: #15803d;">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-2">
                            <label class="font-weight-bold text-danger mb-1">Negative Marks (-)</label>
                            <input type="number" id="qmodal-marks-incorrect" class="form-control form-control-sm font-weight-bold text-center" value="0" min="-999" max="0" step="0.25" style="height: 32px; border: 1px solid #cbd5e1; color: #dc2626;">
                        </div>
                    </div>
                </div>
                <div class="text-muted small mt-2" style="font-size: 10.5px;">
                    <i class="fa fa-info-circle text-primary mr-1"></i> Override marks for this question specifically (e.g. higher marks for harder questions or section weightage).
                </div>
            </div>
            <div class="modal-footer py-2 px-3 bg-light d-flex align-items-center justify-content-between">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="qmodal-btn-reset">
                    <i class="fa fa-undo mr-1"></i> Reset to Default
                </button>
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary font-weight-bold px-3" id="qmodal-btn-save">
                        <i class="fa fa-check mr-1"></i> Save Marks
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- KaTeX for LaTeX Math Rendering --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>

<script>
window.escapeHtml = function (str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
};
const escapeHtml = window.escapeHtml;

document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------
    // Core Elements & State
    // -------------------------------------------------------------
    const form = document.getElementById('exam-create-form');
    const nameInput = document.getElementById('exam-name');
    const codeInput = document.getElementById('exam-code');
    const classSelect = document.getElementById('class_type_id');
    const subjectSelect = document.getElementById('subject_id');
    const dateInput = document.getElementById('exam-date');
    const countInput = document.getElementById('exam-question-count');
    const marksCorrectInput = document.getElementById('marks-per-correct');
    const marksIncorrectInput = document.getElementById('marks-per-incorrect');

    const panePdf = document.getElementById('pane-mode-pdf-upload');
    const paneQBank = document.getElementById('pane-mode-question-bank');
    const rightPanelOmr = document.getElementById('right-panel-omr');
    const rightPanelQBank = document.getElementById('right-panel-qbank');
    const btnSubmitPaper = document.getElementById('btn-submit-print-paper');

    const grid = document.getElementById('omr-question-grid');
    const answerKeyGrid = document.getElementById('exam-answer-key-grid');
    const qbankAnswerKeyGrid = document.getElementById('qbank-derived-answer-key-grid');
    const selectedQList = document.getElementById('selected-questions-list');
    const qbankEmptyState = document.getElementById('qbank-empty-state');
    const selectedCountBadge = document.getElementById('selected-questions-count-badge');
    const qbankKeyCount = document.getElementById('qbank-key-count');
    const jsonInput = document.getElementById('selected_questions_json');

    // Initial Data
    let currentMode = "{{ $initialMode }}";
    const initialAnswerKey = @json($previewAnswerKey);
    let answerKey = Object.fromEntries(Object.entries(initialAnswerKey || {}).map(([q, ans]) => [String(q), ans]));
    
    let selectedQuestions = [];
    try {
        const serverData = @json($initialQuestions);
        if (Array.isArray(serverData)) {
            selectedQuestions = serverData;
        } else if (typeof serverData === 'string' && serverData.trim() !== '') {
            selectedQuestions = JSON.parse(serverData) || [];
        } else if (jsonInput && jsonInput.value) {
            selectedQuestions = JSON.parse(jsonInput.value) || [];
        }
    } catch (e) {
        console.warn('Initial questions parse warning:', e);
        try {
            if (jsonInput && jsonInput.value) {
                selectedQuestions = JSON.parse(jsonInput.value) || [];
            }
        } catch (err) {
            selectedQuestions = [];
        }
    }

    if (jsonInput && Array.isArray(selectedQuestions)) {
        jsonInput.value = JSON.stringify(selectedQuestions);
    }

    // Subject Question Ranges & Custom Question Marking State
    let subjectRanges = [];
    try {
        const rawSr = @json($formExam->subject_question_ranges ?? []);
        subjectRanges = typeof rawSr === 'string' ? JSON.parse(rawSr) : (Array.isArray(rawSr) ? rawSr : []);
    } catch(e) { subjectRanges = []; }

    let questionConfigs = {};
    try {
        const rawQc = @json($formExam->question_configs ?? []);
        questionConfigs = typeof rawQc === 'string' ? JSON.parse(rawQc) : (typeof rawQc === 'object' && rawQc !== null ? rawQc : {});
    } catch(e) { questionConfigs = {}; }

    const hiddenRangesInput = document.getElementById('hidden_subject_question_ranges');
    const hiddenConfigsInput = document.getElementById('hidden_question_configs');
    const questionRangesTbody = document.getElementById('question-ranges-tbody');
    const rangeTotalQCountEl = document.getElementById('range-total-q-count');
    const rangeTotalMaxMarksEl = document.getElementById('range-total-max-marks');
    const btnAutoSplitRanges = document.getElementById('btn-auto-split-ranges');

    const subjectColors = [
        { bg: '#eff6ff', color: '#1e40af', border: '#bfdbfe' }, // blue
        { bg: '#f0fdf4', color: '#166534', border: '#bbf7d0' }, // green
        { bg: '#fef3c7', color: '#92400e', border: '#fde68a' }, // amber
        { bg: '#f3e8ff', color: '#6b21a8', border: '#e9d5ff' }, // purple
        { bg: '#fdf2f8', color: '#9d174d', border: '#fbcfe8' }, // pink
        { bg: '#e0f2fe', color: '#0369a1', border: '#bae6fd' }, // sky
    ];
    const getSubjectBadgeStyle = (subId) => {
        const idx = Math.abs(parseInt(subId, 10) || 0) % subjectColors.length;
        const c = subjectColors[idx];
        return `background: ${c.bg} !important; color: ${c.color} !important; border: 1px solid ${c.border} !important;`;
    };

    const getSubjectName = (sid) => {
        sid = String(sid);
        const cb = document.querySelector(`.exam-subject-checkbox[value="${sid}"]`);
        if (cb) {
            const name = cb.getAttribute('data-name') || cb.getAttribute('data-subject-name') || cb.closest('.subject-chip-item')?.querySelector('.subject-chip-name')?.textContent?.trim() || cb.closest('.subject-chip-item')?.querySelector('span')?.textContent?.trim();
            if (name && !name.startsWith('Subject #')) return name;
        }
        const found = (subjectRanges || []).find(r => String(r.subject_id) === sid);
        if (found && found.subject_name && !found.subject_name.startsWith('Subject #')) {
            return found.subject_name;
        }
        return `Subject #${sid}`;
    };

    // Helper: Render LaTeX math in element
    const renderMath = (element) => {
        if (!element || !window.renderMathInElement) return;
        try {
            window.renderMathInElement(element, {
                delimiters: [
                    { left: '$$', right: '$$', display: true },
                    { left: '$', right: '$', display: false },
                    { left: '\\(', right: '\\)', display: false },
                    { left: '\\[', right: '\\]', display: true },
                ],
                throwOnError: false,
            });
        } catch (e) {
            console.warn('Math rendering notice:', e);
        }
    };

    // -------------------------------------------------------------
    // Mode Switcher Logic (4 Modes: normal, pdf_upload, question_bank, online)
    // -------------------------------------------------------------
    const updateNormalSummary = () => {
        const nameVal = (nameInput?.value || 'Untitled Examination').trim();
        const classText = classSelect?.options[classSelect.selectedIndex]?.text || '--';
        
        let subText = 'All Subjects';
        const allCbs = document.querySelectorAll('.exam-subject-checkbox');
        const checkedCbs = Array.from(document.querySelectorAll('.exam-subject-checkbox:checked'));
        const selectedCount = checkedCbs.length;

        if (currentMode === 'online') {
            subText = subjectSelect?.options[subjectSelect.selectedIndex]?.text || 'No Subject Selected';
        } else {
            if (allCbs.length === 0) {
                subText = 'All Subjects';
            } else if (checkedCbs.length === allCbs.length) {
                subText = `All Subjects (${checkedCbs.length})`;
            } else if (checkedCbs.length === 0) {
                subText = 'None Selected (0)';
            } else if (checkedCbs.length <= 3) {
                subText = checkedCbs.map(cb => cb.dataset.name).join(', ');
            } else {
                subText = checkedCbs.slice(0, 2).map(cb => cb.dataset.name).join(', ') + ` +${checkedCbs.length - 2} more`;
            }
        }

        const dateVal = dateInput?.value || '';
        const perSubVal = parseFloat(document.getElementById('exam-max-marks')?.value || '100') || 0;

        let effectiveCount = 1;
        if (currentMode === 'online') {
            effectiveCount = 1;
        } else if (allCbs.length > 0) {
            effectiveCount = selectedCount;
        }

        const totalMarks = Math.round(effectiveCount * perSubVal * 100) / 100;

        const calcHintText = document.getElementById('max-marks-calc-text');
        if (calcHintText) {
            if (effectiveCount > 1) {
                calcHintText.innerHTML = `${perSubVal} per subject &times; ${effectiveCount} subjects = <strong>${totalMarks} Total Marks</strong>`;
            } else if (effectiveCount === 1) {
                calcHintText.innerHTML = `Total Marks: <strong>${totalMarks}</strong> (1 subject)`;
            } else {
                calcHintText.innerHTML = `<span class="text-danger">0 subjects selected (Total: 0 Marks)</span>`;
            }
        }

        const sTitle = document.getElementById('normal-summary-name');
        const sClass = document.getElementById('normal-summary-class');
        const sSub = document.getElementById('normal-summary-subject');
        const sDate = document.getElementById('normal-summary-date');
        const sMarks = document.getElementById('normal-summary-max-marks');

        if (sTitle) sTitle.textContent = nameVal;
        if (sClass) sClass.textContent = classText;
        if (sSub) sSub.textContent = subText;
        if (sDate) sDate.textContent = dateVal ? new Date(dateVal).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '{{ date('d M Y') }}';
        if (sMarks) {
            if (effectiveCount > 1) {
                sMarks.innerHTML = `${totalMarks} <span style="font-size: 9.5px; opacity: 0.9; font-weight: normal;">(${perSubVal} &times; ${effectiveCount})</span>`;
            } else {
                sMarks.textContent = `${totalMarks}`;
            }
        }
    };

    const renderOnlineExamPreview = () => {
        const count = selectedQuestions.length;
        const marksCorrect = parseFloat(marksCorrectInput?.value || 1.0) || 1.0;
        const durationVal = document.getElementById('exam-duration-input')?.value || '60';

        const countBadge = document.getElementById('online-preview-count');
        const marksBadge = document.getElementById('online-preview-max-marks');
        const durBadge = document.getElementById('online-preview-duration');

        if (countBadge) countBadge.textContent = `${count} Questions`;
        if (marksBadge) marksBadge.textContent = `${(count * marksCorrect).toFixed(1)} Marks`;
        if (durBadge) durBadge.textContent = `${durationVal} Mins`;

        const pTitle = document.getElementById('online-paper-title');
        const pClass = document.getElementById('online-paper-class');
        const pSub = document.getElementById('online-paper-subject');
        const pDate = document.getElementById('online-paper-date');
        const dateVal = dateInput?.value || '';

        if (pTitle) pTitle.textContent = (nameInput?.value || 'Untitled Online Examination').trim();
        if (pClass) pClass.textContent = 'Class: ' + (classSelect?.options[classSelect.selectedIndex]?.text || '--');
        if (pSub) pSub.textContent = 'Subject: ' + (subjectSelect?.options[subjectSelect.selectedIndex]?.text || 'All Subjects');
        if (pDate) pDate.textContent = 'Date: ' + (dateVal ? new Date(dateVal).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '{{ date('d M Y') }}');

        const flowEl = document.getElementById('online-paper-questions-flow');
        if (!flowEl) return;

        if (count === 0) {
            flowEl.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fa fa-laptop fa-2x mb-2 text-info"></i>
                    <p class="mb-0 font-weight-bold" style="color: #1e293b;">No questions added yet for Online Exam.</p>
                    <p class="small text-muted">Click "Browse Question Bank" on the left to add questions to this online exam.</p>
                </div>`;
            return;
        }

        let html = '';
        selectedQuestions.forEach((q, idx) => {
            const qNum = idx + 1;
            const isMcq = (parseInt(q.question_type_id, 10) === 1);
            const correctLetter = q.correct_letter || (isMcq ? (['A','B','C','D','E'][parseInt(q.correct_ans, 10)] || q.correct_ans || 'A') : (q.correct_ans || ''));

            html += `
            <div class="paper-question-block">
                <div class="paper-q-statement">
                    <span class="paper-q-num">Q${qNum}.</span>
                    <span class="paper-q-marks">[${marksCorrect} Mark]</span>
                    <span class="math-content">${q.name || ''}</span>
                </div>
                ${q.image ? `<img src="${q.image}" class="paper-q-diagram" alt="Diagram">` : ''}
                ${isMcq ? `
                    <div class="paper-q-options-grid">
                        <div class="paper-q-opt"><span class="paper-q-opt-label">(A)</span> <span class="math-content">${q.ans1 || ''}</span></div>
                        <div class="paper-q-opt"><span class="paper-q-opt-label">(B)</span> <span class="math-content">${q.ans2 || ''}</span></div>
                        <div class="paper-q-opt"><span class="paper-q-opt-label">(C)</span> <span class="math-content">${q.ans3 || ''}</span></div>
                        <div class="paper-q-opt"><span class="paper-q-opt-label">(D)</span> <span class="math-content">${q.ans4 || ''}</span></div>
                    </div>
                ` : `
                    <div class="small font-italic mt-1" style="color: #475569;">
                        Descriptive / Numerical Answer: <span class="text-success font-weight-bold">${q.correct_ans || ''}</span>
                    </div>
                `}
            </div>`;
        });

        flowEl.innerHTML = html;
        renderMath(flowEl);
    };

    const switchMode = (mode) => {
        currentMode = mode;

        // 1. Radio & Cards Active State
        ['normal', 'pdf_upload', 'question_bank', 'online'].forEach(m => {
            const cardId = 'card-mode-' + (m === 'pdf_upload' ? 'pdf' : (m === 'question_bank' ? 'qbank' : m));
            const card = document.getElementById(cardId);
            if (card) {
                const isMatch = (m === mode);
                card.classList.toggle('active', isMatch);
                const inp = card.querySelector('input[type="radio"]');
                if (inp) inp.checked = isMatch;
            }
        });

        // 2. Set is_online hidden input
        const isOnlineInput = document.getElementById('exam-is-online');
        if (isOnlineInput) {
            isOnlineInput.value = (mode === 'online') ? '1' : '0';
        }

        // 3. Left Column Panes
        const paneNormalMarks = document.getElementById('pane-mode-normal-marks');
        const paneOnlineDuration = document.getElementById('pane-mode-online-duration');
        const panePdf = document.getElementById('pane-mode-pdf-upload');
        const paneQBank = document.getElementById('pane-mode-question-bank');
        const qbankDerivedKeyBox = document.getElementById('qbank-derived-answer-key-box');
        const onlineNoticeBox = document.getElementById('online-exam-notice-box');
        const subjectSingleWrapper = document.getElementById('subject-single-wrapper');
        const subjectMultiWrapper = document.getElementById('subject-multi-wrapper');

        // Dynamically dock multi-subject checklist into the active mode pane
        if (mode === 'online') {
            if (subjectSingleWrapper) subjectSingleWrapper.style.display = 'block';
            if (subjectMultiWrapper) subjectMultiWrapper.style.display = 'none';
        } else {
            if (subjectSingleWrapper) subjectSingleWrapper.style.display = 'none';
            if (subjectMultiWrapper) {
                subjectMultiWrapper.style.display = 'block';
                if (mode === 'normal') {
                    document.getElementById('mode1-subjects-dock')?.appendChild(subjectMultiWrapper);
                } else if (mode === 'pdf_upload') {
                    document.getElementById('mode2-subjects-dock')?.appendChild(subjectMultiWrapper);
                } else if (mode === 'question_bank') {
                    document.getElementById('mode3-subjects-dock')?.appendChild(subjectMultiWrapper);
                }
            }
        }

        if (paneNormalMarks) paneNormalMarks.style.display = (mode === 'normal') ? 'block' : 'none';
        if (paneOnlineDuration) paneOnlineDuration.style.display = (mode === 'online') ? 'block' : 'none';
        if (panePdf) panePdf.style.display = (mode === 'pdf_upload') ? 'block' : 'none';
        if (paneQBank) paneQBank.style.display = (mode === 'question_bank' || mode === 'online') ? 'block' : 'none';
        if (qbankDerivedKeyBox) qbankDerivedKeyBox.style.display = (mode === 'question_bank') ? 'block' : 'none';
        if (onlineNoticeBox) onlineNoticeBox.style.display = (mode === 'online') ? 'block' : 'none';

        // Enable/disable inputs based on active mode to avoid collisions
        const m1Inputs = document.querySelectorAll('#pane-mode-normal-marks input[name^="subject_max_marks"], #pane-mode-normal-marks input[name^="subject_min_marks"]');
        const m2Inputs = document.querySelectorAll('#pane-mode-pdf-upload input[name^="subject_max_marks"], #pane-mode-pdf-upload input[name^="subject_min_marks"]');
        if (mode === 'normal') {
            m1Inputs.forEach(i => i.disabled = false);
            m2Inputs.forEach(i => i.disabled = true);
            syncSubjectMarksRows();
            recalcSubjectMarksTotals();
            updateNormalSummary();
        } else if (mode === 'pdf_upload') {
            m1Inputs.forEach(i => i.disabled = true);
            m2Inputs.forEach(i => i.disabled = false);
            syncQuestionRangesTable(false);
            recalcSubjectMarksTotals();
            renderOMRPreview();
        } else if (mode === 'question_bank') {
            m1Inputs.forEach(i => i.disabled = true);
            m2Inputs.forEach(i => i.disabled = true);
            renderQBankExam();
            renderOMRPreview();
        } else if (mode === 'online') {
            m1Inputs.forEach(i => i.disabled = true);
            m2Inputs.forEach(i => i.disabled = true);
            renderOnlineExamPreview();
        }

        // 4. Right Column Previews (Equal Height with flex)
        const rightNormal = document.getElementById('right-panel-normal');
        const rightOmr = document.getElementById('right-panel-omr');
        const rightQBank = document.getElementById('right-panel-qbank');
        const rightOnline = document.getElementById('right-panel-online');

        if (rightNormal) rightNormal.style.display = (mode === 'normal') ? 'flex' : 'none';
        if (rightOmr) rightOmr.style.display = (mode === 'pdf_upload') ? 'flex' : 'none';
        if (rightQBank) rightQBank.style.display = (mode === 'question_bank') ? 'flex' : 'none';
        if (rightOnline) rightOnline.style.display = (mode === 'online') ? 'flex' : 'none';

        // 5. Trigger Respective Renders
        if (mode === 'normal') {
            updateNormalSummary();
        } else if (mode === 'pdf_upload') {
            renderOMRPreview();
        } else if (mode === 'question_bank') {
            renderQBankExam();
            renderOMRPreview();
        } else if (mode === 'online') {
            renderOnlineExamPreview();
        }
    };

    document.querySelectorAll('.mode-option-card').forEach(card => {
        card.addEventListener('click', function () {
            const input = this.querySelector('input[type="radio"]');
            if (input) {
                switchMode(input.value);
            }
        });
    });

    // -------------------------------------------------------------
    // Mode 2: Answer Key & OMR Sheet Generator with Subject Section Ranges
    // -------------------------------------------------------------
    const renderAnswerKey = (count) => {
        let html = '';
        const defaultCorrect = parseFloat(marksCorrectInput?.value || '1') || 1;
        const defaultIncorrect = parseFloat(marksIncorrectInput?.value || '0') || 0;

        for (let q = 1; q <= count; q++) {
            const currentVal = answerKey[String(q)] || '';
            const qCfg = questionConfigs[String(q)] || questionConfigs[q] || {};

            // Find subject mapping from config or ranges
            let qSubId = qCfg.subject_id;
            let qRange = null;
            if (!qSubId && Array.isArray(subjectRanges)) {
                qRange = subjectRanges.find(r => q >= parseInt(r.from_q, 10) && q <= parseInt(r.to_q, 10));
                if (qRange) qSubId = qRange.subject_id;
            }

            let subName = '';
            if (qSubId) {
                subName = getSubjectName(qSubId);
            }

            const badgeStyle = qSubId ? getSubjectBadgeStyle(qSubId) : '';

            const correctMarks = (qCfg.marks_correct !== undefined && qCfg.marks_correct !== null && qCfg.marks_correct !== '')
                ? parseFloat(qCfg.marks_correct)
                : (qRange && qRange.marks_correct !== undefined ? parseFloat(qRange.marks_correct) : defaultCorrect);

            const incorrectMarks = (qCfg.marks_incorrect !== undefined && qCfg.marks_incorrect !== null && qCfg.marks_incorrect !== '')
                ? parseFloat(qCfg.marks_incorrect)
                : (qRange && qRange.marks_incorrect !== undefined ? parseFloat(qRange.marks_incorrect) : defaultIncorrect);

            const hasCustomMarks = (qCfg.marks_correct !== undefined && qCfg.marks_correct !== '') || (qCfg.marks_incorrect !== undefined && qCfg.marks_incorrect !== '');

            html += `
            <div class="ak-row" id="ak-row-${q}" data-q="${q}">
                <div class="d-flex align-items-center" style="gap: 4px; flex: 1; min-width: 0;">
                    <span class="ak-qnum">Q${q}</span>
                    ${subName ? `<span class="badge ak-sub-badge text-truncate" title="${escapeHtml(subName)}" style="max-width: 78px; font-size: 9px; font-weight: 700; padding: 2px 4px; border-radius: 3px; ${badgeStyle}">${escapeHtml(subName)}</span>` : ''}
                </div>
                <div class="ak-options">
                    ${['A', 'B', 'C', 'D'].map(opt => `
                        <label class="ak-option-label">
                            <input type="radio" name="answer_key[${q}]" value="${opt}" ${currentVal === opt ? 'checked' : ''}>
                            <span class="ak-option-pill">${opt}</span>
                        </label>
                    `).join('')}
                </div>
                <div class="ak-marks-tag ml-1" onclick="openQuestionMarksModal(${q});" title="Click to customize marks for Q${q}" style="cursor: pointer;">
                    <span class="badge ${hasCustomMarks ? 'badge-warning font-weight-bold text-dark' : 'badge-light text-muted'}" style="font-size: 9px; padding: 2px 4px; border: 1px solid #cbd5e1; border-radius: 3px;" id="ak-marks-display-${q}">
                        +${correctMarks}/${incorrectMarks}
                    </span>
                </div>
            </div>`;
        }
        answerKeyGrid.innerHTML = html;

        answerKeyGrid.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const qNum = this.name.match(/\d+/)[0];
                answerKey[qNum] = this.value;
                updateAnswerProgress(count);
            });
        });

        updateAnswerProgress(count);
    };

    // -------------------------------------------------------------
    // Subject Question Distribution & Weightage Logic
    // -------------------------------------------------------------
    const syncQuestionRangesTable = (forceSplit = false) => {
        if (!questionRangesTbody) return;

        const totalQuestions = Math.max(1, Math.min(100, parseInt(countInput?.value || '50', 10) || 50));
        const defaultCorrect = parseFloat(marksCorrectInput?.value || '1') || 1;
        const defaultIncorrect = parseFloat(marksIncorrectInput?.value || '0') || 0;

        // Active / checked subjects
        const checkedSubs = [];
        document.querySelectorAll('.exam-subject-checkbox:checked').forEach(cb => {
            const sid = parseInt(cb.value, 10);
            const name = getSubjectName(sid);
            checkedSubs.push({ id: sid, name: name });
        });

        if (checkedSubs.length === 0) {
            questionRangesTbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center text-muted py-3">
                        <i class="fa fa-info-circle mr-1"></i> No subjects selected. Check at least one course subject above to configure question ranges.
                    </td>
                </tr>
            `;
            if (rangeTotalQCountEl) rangeTotalQCountEl.textContent = '0 / ' + totalQuestions;
            if (rangeTotalMaxMarksEl) rangeTotalMaxMarksEl.textContent = '0.00';
            const rangeTotalMinEl = document.getElementById('range-total-min-marks');
            if (rangeTotalMinEl) rangeTotalMinEl.textContent = '0.00';
            if (hiddenRangesInput) hiddenRangesInput.value = '[]';
            if (hiddenConfigsInput) hiddenConfigsInput.value = JSON.stringify(questionConfigs);
            renderAnswerKey(totalQuestions);
            return;
        }

        const currentSubIds = checkedSubs.map(s => s.id);
        const sumOfRanges = (subjectRanges || []).reduce((acc, r) => acc + Math.max(0, (parseInt(r.to_q, 10) || 0) - (parseInt(r.from_q, 10) || 0) + 1), 0);
        const rangesMatch = !forceSplit && subjectRanges.length === checkedSubs.length &&
            subjectRanges.every(r => currentSubIds.includes(parseInt(r.subject_id, 10))) &&
            sumOfRanges === totalQuestions;

        if (!rangesMatch || forceSplit) {
            // Auto split evenly across checked subjects
            let curQ = 1;
            const baseCount = Math.floor(totalQuestions / checkedSubs.length);
            const remainder = totalQuestions % checkedSubs.length;

            subjectRanges = checkedSubs.map((sub, idx) => {
                const qCount = baseCount + (idx < remainder ? 1 : 0);
                const fromQ = curQ;
                const toQ = curQ + Math.max(0, qCount - 1);
                curQ = toQ + 1;

                const prev = (subjectRanges || []).find(r => parseInt(r.subject_id, 10) === sub.id);
                return {
                    subject_id: sub.id,
                    subject_name: sub.name,
                    from_q: fromQ,
                    to_q: toQ,
                    marks_correct: prev && prev.marks_correct !== undefined ? parseFloat(prev.marks_correct) : defaultCorrect,
                    marks_incorrect: prev && prev.marks_incorrect !== undefined ? parseFloat(prev.marks_incorrect) : defaultIncorrect,
                    cutoff_pct: prev && prev.cutoff_pct !== undefined ? parseFloat(prev.cutoff_pct) : 40.0,
                    min_marks: prev && prev.min_marks !== undefined ? parseFloat(prev.min_marks) : null,
                };
            });
        } else {
            subjectRanges.forEach(r => {
                const found = checkedSubs.find(s => s.id === parseInt(r.subject_id, 10));
                if (found) r.subject_name = found.name;
            });
        }

        let html = '';
        let totalMappedQs = 0;
        let totalSectionMax = 0;
        let totalSectionMin = 0;

        subjectRanges.forEach((range, idx) => {
            const sid = range.subject_id;
            const fromQ = parseInt(range.from_q, 10) || 1;
            const toQ = parseInt(range.to_q, 10) || fromQ;
            const qCount = Math.max(0, toQ - fromQ + 1);
            const mCorrect = parseFloat(range.marks_correct !== undefined ? range.marks_correct : defaultCorrect);
            const mIncorrect = parseFloat(range.marks_incorrect !== undefined ? range.marks_incorrect : defaultIncorrect);

            let secMax = 0;
            for (let q = fromQ; q <= toQ; q++) {
                const qCfg = questionConfigs[String(q)] || questionConfigs[q];
                const qM = (qCfg && qCfg.marks_correct !== undefined && qCfg.marks_correct !== '') ? parseFloat(qCfg.marks_correct) : mCorrect;
                secMax += qM;
            }

            // Cut-off and min marks
            const defaultCutoffPct = (range.cutoff_pct !== undefined && range.cutoff_pct !== null) ? parseFloat(range.cutoff_pct) : 40.0;
            const existingMin = (existingSubjectMinMax[sid]?.min !== undefined) ? existingSubjectMinMax[sid].min : null;
            let passMarks = (range.min_marks !== undefined && range.min_marks !== null && range.min_marks !== '')
                ? parseFloat(range.min_marks)
                : (existingMin !== null ? existingMin : Math.round(secMax * (defaultCutoffPct / 100) * 10) / 10);
            
            const actualPct = secMax > 0 ? ((passMarks / secMax) * 100) : defaultCutoffPct;

            totalMappedQs += qCount;
            totalSectionMax += secMax;
            totalSectionMin += passMarks;

            range.cutoff_pct = actualPct;
            range.min_marks = passMarks;

            const badgeStyle = getSubjectBadgeStyle(sid);

            html += `
                <tr class="range-row" data-subject-id="${sid}">
                    <td class="text-center font-weight-bold text-muted" style="vertical-align: middle; padding: 6px;">${idx + 1}</td>
                    <td style="vertical-align: middle; padding: 6px 10px;">
                        <span class="badge" style="font-size: 11px; font-weight: 700; ${badgeStyle}">
                            ${escapeHtml(range.subject_name)}
                        </span>
                    </td>
                    <td class="text-center" style="vertical-align: middle; padding: 4px;">
                        <input type="number" class="form-control form-control-sm text-center font-weight-bold range-input-from" 
                               value="${fromQ}" min="1" max="${totalQuestions}" step="1" 
                               style="width: 65px; height: 28px; margin: 0 auto; border: 1px solid #94a3b8; font-size: 12px;" 
                               data-subject-id="${sid}">
                    </td>
                    <td class="text-center" style="vertical-align: middle; padding: 4px;">
                        <input type="number" class="form-control form-control-sm text-center font-weight-bold range-input-to" 
                               value="${toQ}" min="1" max="${totalQuestions}" step="1" 
                               style="width: 65px; height: 28px; margin: 0 auto; border: 1px solid #94a3b8; font-size: 12px;" 
                               data-subject-id="${sid}">
                    </td>
                    <td class="text-center" style="vertical-align: middle; padding: 4px;">
                        <span class="badge badge-light font-weight-bold range-badge-count" style="font-size: 11px; border: 1px solid #cbd5e1; padding: 4px 6px;">
                            ${qCount} Qs
                        </span>
                    </td>
                    <td class="text-center" style="vertical-align: middle; padding: 4px;">
                        <input type="number" class="form-control form-control-sm text-center font-weight-bold range-input-correct text-success" 
                               value="${mCorrect}" min="0" max="999" step="0.5" 
                               style="width: 70px; height: 28px; margin: 0 auto; border: 1px solid #86efac; font-size: 12px; background: #f0fdf4;" 
                               data-subject-id="${sid}">
                    </td>
                    <td class="text-center" style="vertical-align: middle; padding: 4px;">
                        <input type="number" class="form-control form-control-sm text-center font-weight-bold range-input-incorrect text-danger" 
                               value="${mIncorrect}" min="-999" max="0" step="0.25" 
                               style="width: 70px; height: 28px; margin: 0 auto; border: 1px solid #fca5a5; font-size: 12px; background: #fef2f2;" 
                               data-subject-id="${sid}">
                    </td>
                    <td class="text-center font-weight-bold" style="vertical-align: middle; padding: 6px; color: #002C54; font-size: 13px;">
                        <span class="range-sub-max-display">${secMax.toFixed(2)}</span>
                        <input type="hidden" name="subject_max_marks[${sid}]" id="sub_max_${sid}" class="sub-max-input" value="${secMax}">
                    </td>
                    <td class="text-center" style="vertical-align: middle; padding: 4px;">
                        <span class="sub-cutoff-badge" id="sub_cutoff_badge_${sid}" style="display: inline-block; min-width: 52px; padding: 3px 6px; font-size: 11px; font-weight: 700; border-radius: 4px; border: 1px solid #bae6fd; background: #f0f9ff; color: #0284c7;">
                            ${actualPct.toFixed(1)}%
                        </span>
                    </td>
                    <td class="text-center" style="vertical-align: middle; padding: 4px;">
                        <input type="number" name="subject_min_marks[${sid}]" id="sub_min_${sid}" class="form-control form-control-sm text-center font-weight-bold sub-min-input range-input-min" 
                               value="${passMarks}" min="0" max="9999" step="0.5" 
                               style="width: 75px; height: 28px; margin: 0 auto; border: 1px solid #86efac; font-size: 12px; color: #15803d; background: #ffffff;" 
                               data-subject-id="${sid}">
                    </td>
                </tr>
            `;
        });

        questionRangesTbody.innerHTML = html;

        if (hiddenRangesInput) hiddenRangesInput.value = JSON.stringify(subjectRanges);
        if (hiddenConfigsInput) hiddenConfigsInput.value = JSON.stringify(questionConfigs);

        recalcSubjectMarksTotals();
        attachRangeInputListeners();
        renderAnswerKey(totalQuestions);
    };

    const attachRangeInputListeners = () => {
        questionRangesTbody.querySelectorAll('.range-input-from, .range-input-to, .range-input-correct, .range-input-incorrect').forEach(inp => {
            inp.addEventListener('input', function() {
                const sid = parseInt(this.getAttribute('data-subject-id'), 10);
                const row = this.closest('tr');
                if (!row) return;

                const fromVal = parseInt(row.querySelector('.range-input-from')?.value || '1', 10) || 1;
                const toVal = parseInt(row.querySelector('.range-input-to')?.value || '1', 10) || fromVal;
                const mCorr = parseFloat(row.querySelector('.range-input-correct')?.value || '1') || 1;
                const mIncorr = parseFloat(row.querySelector('.range-input-incorrect')?.value || '0') || 0;

                const rangeItem = subjectRanges.find(r => parseInt(r.subject_id, 10) === sid);
                if (rangeItem) {
                    rangeItem.from_q = fromVal;
                    rangeItem.to_q = toVal;
                    rangeItem.marks_correct = mCorr;
                    rangeItem.marks_incorrect = mIncorr;
                }

                const qCount = Math.max(0, toVal - fromVal + 1);
                const countBadge = row.querySelector('.range-badge-count');
                if (countBadge) countBadge.textContent = `${qCount} Qs`;

                let secMax = 0;
                for (let q = fromVal; q <= toVal; q++) {
                    const qCfg = questionConfigs[String(q)] || questionConfigs[q];
                    const qM = (qCfg && qCfg.marks_correct !== undefined && qCfg.marks_correct !== '') ? parseFloat(qCfg.marks_correct) : mCorr;
                    secMax += qM;
                }
                const maxDisplay = row.querySelector('.range-sub-max-display');
                if (maxDisplay) maxDisplay.textContent = secMax.toFixed(2);

                const maxHidden = row.querySelector('.sub-max-input');
                if (maxHidden) maxHidden.value = secMax;

                // Auto update min marks if bulk cut-off is configured
                const minInp = row.querySelector('.range-input-min');
                const cutoffBadge = row.querySelector('.sub-cutoff-badge');
                const bulkPct = parseFloat(document.getElementById('bulk-subject-min-pct')?.value || '0.40') || 0.40;
                const newPass = Math.round(secMax * bulkPct * 10) / 10;
                if (minInp) minInp.value = newPass;
                if (cutoffBadge) cutoffBadge.textContent = `${(bulkPct * 100).toFixed(1)}%`;

                if (rangeItem) {
                    rangeItem.min_marks = newPass;
                    rangeItem.cutoff_pct = bulkPct * 100;
                }

                if (hiddenRangesInput) hiddenRangesInput.value = JSON.stringify(subjectRanges);
                if (hiddenConfigsInput) hiddenConfigsInput.value = JSON.stringify(questionConfigs);

                const totalQuestions = Math.max(1, Math.min(100, parseInt(countInput?.value || '50', 10) || 50));
                recalcSubjectMarksTotals();
                renderAnswerKey(totalQuestions);
            });
        });

        // Min Pass Marks direct edit listener
        questionRangesTbody.querySelectorAll('.range-input-min').forEach(inp => {
            inp.addEventListener('input', function() {
                const sid = parseInt(this.getAttribute('data-subject-id'), 10);
                const row = this.closest('tr');
                if (!row) return;

                const maxVal = parseFloat(row.querySelector('.sub-max-input')?.value || '0') || 0;
                const minVal = parseFloat(this.value || '0') || 0;
                const pct = maxVal > 0 ? ((minVal / maxVal) * 100) : 0;
                const badge = row.querySelector('.sub-cutoff-badge');
                if (badge) {
                    badge.textContent = `${pct.toFixed(1)}%`;
                }

                const rangeItem = subjectRanges.find(r => parseInt(r.subject_id, 10) === sid);
                if (rangeItem) {
                    rangeItem.min_marks = minVal;
                    rangeItem.cutoff_pct = pct;
                }

                if (hiddenRangesInput) hiddenRangesInput.value = JSON.stringify(subjectRanges);
                recalcSubjectMarksTotals();
            });
        });
    };

    // Modal Question Marks Handlers
    window.openQuestionMarksModal = function(qNum) {
        const qmodalTitleNum = document.getElementById('qmodal-title-num');
        const qmodalQnum = document.getElementById('qmodal-qnum');
        const qmodalSubSelect = document.getElementById('qmodal-subject-select');
        const qmodalCorrect = document.getElementById('qmodal-marks-correct');
        const qmodalIncorrect = document.getElementById('qmodal-marks-incorrect');

        if (!qmodalQnum) return;
        qmodalQnum.value = qNum;
        if (qmodalTitleNum) qmodalTitleNum.textContent = `Question Q${qNum}`;

        let selHtml = '<option value="">(Default from Section Range)</option>';
        document.querySelectorAll('.exam-subject-checkbox:checked').forEach(cb => {
            const sid = cb.value;
            const name = getSubjectName(sid);
            selHtml += `<option value="${sid}">${escapeHtml(name)}</option>`;
        });
        if (qmodalSubSelect) qmodalSubSelect.innerHTML = selHtml;

        const qCfg = questionConfigs[String(qNum)] || questionConfigs[qNum] || {};

        let matchedRange = null;
        if (Array.isArray(subjectRanges)) {
            matchedRange = subjectRanges.find(r => qNum >= parseInt(r.from_q, 10) && qNum <= parseInt(r.to_q, 10));
        }
        const defaultCorrect = matchedRange && matchedRange.marks_correct !== undefined ? matchedRange.marks_correct : (parseFloat(marksCorrectInput?.value || '1') || 1);
        const defaultIncorrect = matchedRange && matchedRange.marks_incorrect !== undefined ? matchedRange.marks_incorrect : (parseFloat(marksIncorrectInput?.value || '0') || 0);

        if (qmodalSubSelect) {
            qmodalSubSelect.value = qCfg.subject_id || (matchedRange ? matchedRange.subject_id : '');
        }
        if (qmodalCorrect) {
            qmodalCorrect.value = (qCfg.marks_correct !== undefined && qCfg.marks_correct !== '') ? qCfg.marks_correct : defaultCorrect;
        }
        if (qmodalIncorrect) {
            qmodalIncorrect.value = (qCfg.marks_incorrect !== undefined && qCfg.marks_incorrect !== '') ? qCfg.marks_incorrect : defaultIncorrect;
        }

        if (typeof $ !== 'undefined' && $('#modalQuestionMarks').modal) {
            $('#modalQuestionMarks').modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(document.getElementById('modalQuestionMarks')).show();
        }
    };

    document.getElementById('qmodal-btn-save')?.addEventListener('click', function() {
        const qNum = document.getElementById('qmodal-qnum')?.value;
        if (!qNum) return;

        const sid = document.getElementById('qmodal-subject-select')?.value;
        const mCorr = parseFloat(document.getElementById('qmodal-marks-correct')?.value || '1') || 1;
        const mIncorr = parseFloat(document.getElementById('qmodal-marks-incorrect')?.value || '0') || 0;

        questionConfigs[String(qNum)] = {
            subject_id: sid ? parseInt(sid, 10) : null,
            marks_correct: mCorr,
            marks_incorrect: mIncorr,
        };

        if (hiddenConfigsInput) hiddenConfigsInput.value = JSON.stringify(questionConfigs);

        if (typeof $ !== 'undefined' && $('#modalQuestionMarks').modal) {
            $('#modalQuestionMarks').modal('hide');
        } else if (typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getInstance(document.getElementById('modalQuestionMarks'))?.hide();
        }

        syncQuestionRangesTable(false);
    });

    document.getElementById('qmodal-btn-reset')?.addEventListener('click', function() {
        const qNum = document.getElementById('qmodal-qnum')?.value;
        if (!qNum) return;

        delete questionConfigs[String(qNum)];
        delete questionConfigs[qNum];

        if (hiddenConfigsInput) hiddenConfigsInput.value = JSON.stringify(questionConfigs);

        if (typeof $ !== 'undefined' && $('#modalQuestionMarks').modal) {
            $('#modalQuestionMarks').modal('hide');
        } else if (typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getInstance(document.getElementById('modalQuestionMarks'))?.hide();
        }

        syncQuestionRangesTable(false);
    });

    btnAutoSplitRanges?.addEventListener('click', () => {
        syncQuestionRangesTable(true);
    });

    const updateAnswerProgress = (count) => {
        const selected = answerKeyGrid.querySelectorAll('input:checked').length;
        const progressEl = document.getElementById('answer-key-progress');
        if (progressEl) {
            progressEl.textContent = `${selected} / ${count} Selected`;
            progressEl.className = selected === count ? 'badge badge-success py-1 px-2' : 'badge badge-info py-1 px-2';
        }
    };

    // Quick fill buttons
    document.querySelectorAll('.ak-btn-group button').forEach(btn => {
        btn.addEventListener('click', function () {
            const action = this.dataset.fill;
            const count = Math.max(1, Math.min(100, parseInt(countInput.value, 10) || 50));
            const options = ['A', 'B', 'C', 'D'];

            for (let q = 1; q <= count; q++) {
                if (action === 'CLEAR') {
                    delete answerKey[String(q)];
                } else if (action === 'RANDOM') {
                    answerKey[String(q)] = options[Math.floor(Math.random() * options.length)];
                } else {
                    answerKey[String(q)] = action;
                }
            }
            renderAnswerKey(count);
        });
    });

    // Quick chips
    document.querySelectorAll('.quick-chip-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.quick-chip-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            countInput.value = this.dataset.count;
            syncQuestionRangesTable(true);
            renderOMRPreview();
        });
    });

    // SVG OMR Sheet Render (Balanced & Centered Question Columns)
    const renderOMRPreview = () => {
        let count;
        if (currentMode === 'question_bank' || currentMode === 'online') {
            count = (Array.isArray(selectedQuestions) && selectedQuestions.length > 0)
                ? Math.min(100, selectedQuestions.length)
                : 50;
        } else {
            count = Math.max(1, Math.min(100, parseInt(countInput?.value || '50', 10) || 50));
        }
        const labels = ['A', 'B', 'C', 'D'];
        let svg = '';

        // Question blocks start from left margin (aligned with roll number and answers header at x=70)
        const totalBlocks = Math.max(1, Math.ceil(count / 25));
        const blockWidth = 155;
        const blockGap = 13.5;
        const startX = 70;

        for (let block = 0; block < totalBlocks; block++) {
            const blockStart = block * 25 + 1;
            if (blockStart > count) continue;
            const blockEnd = Math.min(count, blockStart + 24);
            const bx = startX + block * (blockWidth + blockGap);

            svg += `<text x="${bx + 77.5}" y="456" text-anchor="middle" font-family="Arial" font-size="8.5" font-weight="700" fill="#002C54">QUESTIONS ${blockStart} - ${blockEnd}</text>`;
            svg += `<rect x="${bx}" y="444" width="155" height="576" fill="none" stroke="#94a3b8" stroke-width="1"/>`;
            svg += `<text x="${bx + 14}" y="471" text-anchor="middle" font-family="Arial" font-size="9" font-weight="700" fill="#0f172a">Q</text>`;
            labels.forEach((label, idx) => {
                svg += `<text x="${bx + 48 + idx * 26}" y="471" text-anchor="middle" font-family="Arial" font-size="9" font-weight="700" fill="#0f172a">${label}</text>`;
            });

            for (let row = 1; row <= 25; row++) {
                const question = block * 25 + row;
                const qy = 486 + (row - 1) * 21.8;
                if (question <= count) {
                    svg += `<text x="${bx + 14}" y="${qy + 3}" text-anchor="middle" font-family="Arial" font-size="8" font-weight="700" fill="#002C54">${question}</text>`;
                    labels.forEach((label, idx) => {
                        svg += `<circle cx="${bx + 48 + idx * 26}" cy="${qy}" r="7" fill="white" stroke="#000000" stroke-width="1.15"/>`;
                    });
                }
                svg += `<line x1="${bx + 4}" y1="${qy + 9}" x2="${bx + 151}" y2="${qy + 9}" stroke="#f1f5f9" stroke-width="0.8"/>`;
            }
        }

        if (grid) grid.innerHTML = svg;
        const nameVal = (nameInput?.value?.trim() || 'Untitled Examination').toUpperCase();
        const codeVal = codeInput?.value || 'EXAM-CODE';

        const omrName = document.getElementById('omr-preview-name');
        const omrCode = document.getElementById('omr-preview-code');
        const omrTotal = document.getElementById('omr-question-total');
        const previewBadge = document.getElementById('exam-preview-count');
        const pdfBadge = document.getElementById('pdf-qcount-badge');

        if (omrName) omrName.textContent = `EXAM: ${nameVal}`;
        if (omrCode) omrCode.textContent = `EXAM CODE: ${codeVal}`;
        if (omrTotal) omrTotal.textContent = `ANSWERS — ${count} QUESTIONS`;
        if (previewBadge) previewBadge.textContent = `${count} Questions`;
        if (pdfBadge) pdfBadge.textContent = `${count} Questions`;

        renderAnswerKey(count);

        // Mirror SVG to Mode 3 OMR tab
        const mode2OmrPaper = document.getElementById('exam-omr-mode2-paper');
        const omrSvg = document.getElementById('exam-omr-svg');
        if (mode2OmrPaper && omrSvg) {
            const clonedSvg = omrSvg.cloneNode(true);
            clonedSvg.id = 'exam-omr-mode3-svg';
            mode2OmrPaper.innerHTML = '';
            mode2OmrPaper.appendChild(clonedSvg);
        }
    };

    nameInput?.addEventListener('input', () => {
        renderOMRPreview();
        updatePaperHeader();
        updateNormalSummary();
        renderOnlineExamPreview();
    });
    countInput?.addEventListener('input', () => {
        syncQuestionRangesTable(false);
        renderOMRPreview();
    });
    marksCorrectInput?.addEventListener('input', () => {
        const val = parseFloat(marksCorrectInput?.value || '1') || 1;
        document.querySelectorAll('#question-ranges-tbody .range-input-correct').forEach(inp => {
            inp.value = val;
        });
        (subjectRanges || []).forEach(r => {
            r.marks_correct = val;
        });
        const firstRangeInp = document.querySelector('#question-ranges-tbody .range-input-correct');
        if (firstRangeInp) {
            firstRangeInp.dispatchEvent(new Event('input'));
        } else {
            syncQuestionRangesTable(false);
        }
    });
    marksIncorrectInput?.addEventListener('input', () => {
        const val = parseFloat(marksIncorrectInput?.value || '0') || 0;
        document.querySelectorAll('#question-ranges-tbody .range-input-incorrect').forEach(inp => {
            inp.value = val;
        });
        (subjectRanges || []).forEach(r => {
            r.marks_incorrect = val;
        });
        const firstRangeInp = document.querySelector('#question-ranges-tbody .range-input-incorrect');
        if (firstRangeInp) {
            firstRangeInp.dispatchEvent(new Event('input'));
        } else {
            syncQuestionRangesTable(false);
        }
    });

    // -------------------------------------------------------------
    // Mode 3: Question Bank Integration & Paper Preview
    // -------------------------------------------------------------
    const updatePaperHeader = () => {
        const nameVal = nameInput?.value?.trim() || 'Untitled Examination';
        const classText = classSelect?.options[classSelect.selectedIndex]?.text || '--';
        
        let subText = 'All Subjects';
        if (currentMode === 'online') {
            subText = subjectSelect?.options[subjectSelect.selectedIndex]?.text || 'All Subjects';
        } else {
            const allCbs = document.querySelectorAll('.exam-subject-checkbox');
            const checkedCbs = Array.from(document.querySelectorAll('.exam-subject-checkbox:checked'));
            if (allCbs.length > 0 && checkedCbs.length === allCbs.length) {
                subText = `All Subjects (${checkedCbs.length})`;
            } else if (checkedCbs.length > 0) {
                subText = checkedCbs.map(cb => cb.dataset.name).join(', ');
            }
        }

        const dateVal = dateInput?.value || '';
        const marksCorrect = parseFloat(marksCorrectInput?.value || 1.0) || 1.0;
        const marksIncorrect = parseFloat(marksIncorrectInput?.value || 0.0) || 0.0;
        const qCount = selectedQuestions.length;

        const pTitle = document.getElementById('paper-preview-title');
        const pClass = document.getElementById('paper-preview-class');
        const pSub = document.getElementById('paper-preview-subject');
        const pDate = document.getElementById('paper-preview-date');
        const pTotalQ = document.getElementById('paper-preview-total-q');
        const pMaxMarks = document.getElementById('paper-preview-max-marks');
        const pCorr = document.getElementById('paper-correct-marks-label');
        const pIncorr = document.getElementById('paper-negative-marks-label');

        if (pTitle) pTitle.textContent = nameVal;
        if (pClass) pClass.textContent = classText;
        if (pSub) pSub.textContent = subText;
        if (pDate) pDate.textContent = dateVal ? new Date(dateVal).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '{{ date('d M Y') }}';
        if (pTotalQ) pTotalQ.textContent = qCount;
        if (pMaxMarks) pMaxMarks.textContent = (qCount * marksCorrect).toFixed(1);
        if (pCorr) pCorr.textContent = marksCorrect;
        if (pIncorr) pIncorr.textContent = marksIncorrect;

        const qbankOmrLabel = document.getElementById('qbank-omr-count-label');
        if (qbankOmrLabel) qbankOmrLabel.textContent = qCount;
    };

    marksCorrectInput?.addEventListener('input', () => { updatePaperHeader(); renderOnlineExamPreview(); });
    marksIncorrectInput?.addEventListener('input', () => { updatePaperHeader(); renderOnlineExamPreview(); });
    dateInput?.addEventListener('change', () => { updatePaperHeader(); updateNormalSummary(); renderOnlineExamPreview(); });
    document.getElementById('exam-max-marks')?.addEventListener('input', updateNormalSummary);
    document.getElementById('exam-duration-input')?.addEventListener('input', renderOnlineExamPreview);

    // -------------------------------------------------------------
    // Multi-Subject Selection Handlers
    // -------------------------------------------------------------
    const chipsContainer = document.getElementById('subjects-chips-list');

    const updateSubjectSelectionBadge = () => {
        const badge = document.getElementById('subject-selected-count-badge');
        const allCbs = document.querySelectorAll('.exam-subject-checkbox');
        const checkedCbs = document.querySelectorAll('.exam-subject-checkbox:checked');
        if (!badge) return;

        if (allCbs.length === 0) {
            badge.textContent = '0 Selected';
            badge.style.background = '#f1f5f9';
            badge.style.color = '#64748b';
        } else if (checkedCbs.length === allCbs.length) {
            badge.textContent = `All Selected (${checkedCbs.length})`;
            badge.style.background = '#e0f2fe';
            badge.style.color = '#0369a1';
        } else if (checkedCbs.length === 0) {
            badge.textContent = 'None Selected (0)';
            badge.style.background = '#fee2e2';
            badge.style.color = '#b91c1c';
        } else {
            badge.textContent = `${checkedCbs.length} of ${allCbs.length} Selected`;
            badge.style.background = '#fef3c7';
            badge.style.color = '#92400e';
        }
    };

    // -------------------------------------------------------------
    // Subject-Wise Marks & Passing Cut-off Configuration
    // -------------------------------------------------------------
    const existingSubjectMinMax = @json($existingMinMaxMap ?? []);

    const recalcSubjectMarksTotals = () => {
        let totalMax = 0;
        let totalMin = 0;
        let activeCount = 0;

        if (currentMode === 'pdf_upload') {
            const rangeRows = document.querySelectorAll('#question-ranges-tbody .range-row');
            let totalMappedQs = 0;
            const totalQuestions = Math.max(1, Math.min(100, parseInt(countInput?.value || '50', 10) || 50));

            rangeRows.forEach(row => {
                activeCount++;
                const fromVal = parseInt(row.querySelector('.range-input-from')?.value || '1', 10) || 1;
                const toVal = parseInt(row.querySelector('.range-input-to')?.value || '1', 10) || fromVal;
                const qCount = Math.max(0, toVal - fromVal + 1);
                totalMappedQs += qCount;

                const maxHidden = row.querySelector('.sub-max-input');
                const minInp = row.querySelector('.range-input-min');
                const badge = row.querySelector('.sub-cutoff-badge');

                const maxVal = parseFloat(maxHidden?.value || '0') || 0;
                const minVal = parseFloat(minInp?.value || '0') || 0;

                totalMax += maxVal;
                totalMin += minVal;

                if (badge) {
                    const pct = maxVal > 0 ? ((minVal / maxVal) * 100).toFixed(1) : '0';
                    badge.textContent = `${pct}%`;
                    if (parseFloat(pct) < 33) {
                        badge.style.color = '#b91c1c';
                        badge.style.borderColor = '#fca5a5';
                        badge.style.background = '#fef2f2';
                    } else if (parseFloat(pct) <= 40) {
                        badge.style.color = '#0284c7';
                        badge.style.borderColor = '#bae6fd';
                        badge.style.background = '#f0f9ff';
                    } else {
                        badge.style.color = '#15803d';
                        badge.style.borderColor = '#bbf7d0';
                        badge.style.background = '#f0fdf4';
                    }
                }
            });

            // Update Mode 2 footer elements
            const rangeTotalQCountEl = document.getElementById('range-total-q-count');
            if (rangeTotalQCountEl) {
                rangeTotalQCountEl.textContent = `${totalMappedQs} / ${totalQuestions} Qs`;
                if (totalMappedQs === totalQuestions) {
                    rangeTotalQCountEl.className = 'text-center font-weight-bold text-success';
                } else {
                    rangeTotalQCountEl.className = 'text-center font-weight-bold text-danger';
                }
            }

            const rangeTotalMaxEl = document.getElementById('range-total-max-marks');
            if (rangeTotalMaxEl) rangeTotalMaxEl.textContent = totalMax.toFixed(2);

            const rangeTotalMinEl = document.getElementById('range-total-min-marks');
            if (rangeTotalMinEl) rangeTotalMinEl.textContent = totalMin.toFixed(2);

            const rangeTotalPctEl = document.getElementById('grand-total-cutoff-pct');
            if (rangeTotalPctEl) {
                rangeTotalPctEl.textContent = totalMax > 0 ? `${((totalMin / totalMax) * 100).toFixed(1)}%` : '--';
            }

            const statusBadge = document.getElementById('range-validation-status-badge');
            if (statusBadge) {
                if (totalMappedQs === totalQuestions) {
                    statusBadge.innerHTML = `<i class="fa fa-check-circle mr-1"></i> ${totalMappedQs} / ${totalQuestions} Mapped`;
                    statusBadge.style.background = '#f0fdf4';
                    statusBadge.style.color = '#166534';
                    statusBadge.style.borderColor = '#bbf7d0';
                } else {
                    statusBadge.innerHTML = `<i class="fa fa-exclamation-triangle mr-1"></i> ${totalMappedQs} / ${totalQuestions} Mapped`;
                    statusBadge.style.background = '#fef2f2';
                    statusBadge.style.color = '#b91c1c';
                    statusBadge.style.borderColor = '#fca5a5';
                }
            }

            // Sync overall exam max marks input
            const examMaxInput = document.getElementById('exam-max-marks');
            if (examMaxInput) {
                examMaxInput.value = totalMax;
            }

            const calcHintText = document.getElementById('max-marks-calc-text');
            if (calcHintText) {
                if (activeCount > 0) {
                    calcHintText.innerHTML = `Total: <strong>${totalMax.toFixed(2)} Marks</strong> across ${activeCount} active subjects | Sectional Qualifying Cut-off: <strong>${totalMin.toFixed(2)}</strong>`;
                } else {
                    calcHintText.innerHTML = `<span class="text-danger">0 subjects selected (Total: 0 Marks)</span>`;
                }
            }

            return;
        }

        const rows = document.querySelectorAll('.subject-mark-row');
        rows.forEach(row => {
            if (!row.classList.contains('d-none')) {
                activeCount++;
                const maxInp = row.querySelector('.sub-max-input');
                const minInp = row.querySelector('.sub-min-input');
                const badge = row.querySelector('.sub-cutoff-badge');

                const maxVal = parseFloat(maxInp?.value || '0') || 0;
                const minVal = parseFloat(minInp?.value || '0') || 0;

                totalMax += maxVal;
                totalMin += minVal;

                if (badge) {
                    const pct = maxVal > 0 ? ((minVal / maxVal) * 100).toFixed(1) : '0';
                    badge.textContent = `${pct}%`;
                    if (parseFloat(pct) < 33) {
                        badge.style.color = '#b91c1c';
                        badge.style.borderColor = '#fca5a5';
                        badge.style.background = '#fef2f2';
                    } else if (parseFloat(pct) <= 40) {
                        badge.style.color = '#0284c7';
                        badge.style.borderColor = '#bae6fd';
                        badge.style.background = '#f0f9ff';
                    } else {
                        badge.style.color = '#15803d';
                        badge.style.borderColor = '#bbf7d0';
                        badge.style.background = '#f0fdf4';
                    }
                }

                const numCell = row.querySelector('.row-number');
                if (numCell) numCell.textContent = activeCount;
            }
        });

        const countElem = document.getElementById('total-active-subs-count');
        const maxElem = document.getElementById('grand-total-max-marks');
        const minElem = document.getElementById('grand-total-min-marks');
        const pctElem = document.getElementById('grand-total-cutoff-pct');

        if (countElem) countElem.textContent = activeCount;
        if (maxElem) maxElem.textContent = totalMax.toFixed(2);
        if (minElem) minElem.textContent = totalMin.toFixed(2);
        if (pctElem) {
            pctElem.textContent = totalMax > 0 ? `${((totalMin / totalMax) * 100).toFixed(1)}%` : '--';
        }

        // Update overall exam max marks input and hint
        const examMaxInput = document.getElementById('exam-max-marks');
        if (examMaxInput) {
            examMaxInput.value = totalMax;
        }

        const calcHintText = document.getElementById('max-marks-calc-text');
        if (calcHintText) {
            if (activeCount > 0) {
                calcHintText.innerHTML = `Total: <strong>${totalMax.toFixed(2)} Marks</strong> across ${activeCount} active subjects | Sectional Qualifying Cut-off: <strong>${totalMin.toFixed(2)}</strong>`;
            } else {
                calcHintText.innerHTML = `<span class="text-danger">0 subjects selected (Total: 0 Marks)</span>`;
            }
        }

        const emptyRow = document.getElementById('subject-marks-empty-row');
        if (emptyRow) {
            emptyRow.style.display = (activeCount === 0) ? '' : 'none';
        }

        const sMarks = document.getElementById('normal-summary-max-marks');
        if (sMarks) {
            sMarks.innerHTML = `${totalMax.toFixed(2)} <span style="font-size: 9.5px; opacity: 0.9; font-weight: normal;">(Pass: ${totalMin.toFixed(2)})</span>`;
        }
    };

    const attachSubMarksInputListeners = (row) => {
        const maxInp = row.querySelector('.sub-max-input');
        const minInp = row.querySelector('.sub-min-input');

        const handler = () => recalcSubjectMarksTotals();
        if (maxInp) {
            maxInp.removeEventListener('input', handler);
            maxInp.addEventListener('input', handler);
        }
        if (minInp) {
            minInp.removeEventListener('input', handler);
            minInp.addEventListener('input', handler);
        }
    };

    const syncSubjectMarksRows = () => {
        const tbody = document.getElementById('subject-marks-tbody');
        if (!tbody) return;

        const checkboxes = document.querySelectorAll('.exam-subject-checkbox');
        const existingRowIds = new Set();

        checkboxes.forEach(cb => {
            const subId = cb.value;
            const subName = cb.dataset.name || cb.closest('.subject-chip-item')?.querySelector('span')?.textContent?.trim() || `Subject #${subId}`;
            existingRowIds.add(String(subId));

            let row = document.getElementById(`subject-mark-row-${subId}`);
            if (!row) {
                const initialMax = existingSubjectMinMax[subId]?.max ?? (parseFloat(document.getElementById('bulk-subject-max')?.value || '50') || 50);
                const initialMin = existingSubjectMinMax[subId]?.min ?? Math.round(initialMax * 0.40 * 10) / 10;
                const initialPct = initialMax > 0 ? ((initialMin / initialMax) * 100).toFixed(1) : '40.0';

                row = document.createElement('tr');
                row.className = 'subject-mark-row';
                row.id = `subject-mark-row-${subId}`;
                row.setAttribute('data-subject-id', subId);
                row.setAttribute('data-subject-name', subName);
                row.innerHTML = `
                    <td class="text-center font-weight-bold text-muted row-number" style="vertical-align: middle; padding: 8px 6px;">1</td>
                    <td style="vertical-align: middle; padding: 8px 12px;">
                        <span class="font-weight-600 text-dark" style="font-size: 13px;">${subName}</span>
                    </td>
                    <td class="text-center" style="vertical-align: middle; padding: 6px 10px;">
                        <input type="number" 
                               name="subject_max_marks[${subId}]" 
                               id="sub_max_${subId}" 
                               class="form-control form-control-sm text-center font-weight-bold sub-max-input" 
                               value="${initialMax}" 
                               min="1" max="9999" step="0.5" 
                               style="width: 100px; height: 32px; font-size: 13px; margin: 0 auto; border: 1px solid #94a3b8; border-radius: 4px; color: #002C54; background: #ffffff;">
                    </td>
                    <td class="text-center" style="vertical-align: middle; padding: 6px 10px;">
                        <input type="number" 
                               name="subject_min_marks[${subId}]" 
                               id="sub_min_${subId}" 
                               class="form-control form-control-sm text-center font-weight-bold sub-min-input" 
                               value="${initialMin}" 
                               min="0" max="9999" step="0.5" 
                               style="width: 100px; height: 32px; font-size: 13px; margin: 0 auto; border: 1px solid #94a3b8; border-radius: 4px; color: #15803d; background: #ffffff;">
                    </td>
                    <td class="text-center" style="vertical-align: middle; padding: 6px 10px;">
                        <span class="sub-cutoff-badge" id="sub_cutoff_badge_${subId}" style="display: inline-block; min-width: 58px; padding: 4px 8px; font-size: 11.5px; font-weight: 700; border-radius: 4px; border: 1px solid #bae6fd; background: #f0f9ff; color: #0284c7;">
                            ${initialPct}%
                        </span>
                    </td>
                `;
                tbody.appendChild(row);
                attachSubMarksInputListeners(row);
            }

            if (cb.checked) {
                row.classList.remove('d-none');
                row.querySelectorAll('input').forEach(inp => inp.disabled = false);
            } else {
                row.classList.add('d-none');
                row.querySelectorAll('input').forEach(inp => inp.disabled = true);
            }
        });

        // Remove any rows for subjects no longer in checkboxes
        tbody.querySelectorAll('.subject-mark-row').forEach(row => {
            const sid = row.getAttribute('data-subject-id');
            if (!existingRowIds.has(String(sid))) {
                row.remove();
            }
        });

        recalcSubjectMarksTotals();
    };

    // Bulk Apply Handlers
    document.getElementById('btn-apply-bulk-max')?.addEventListener('click', () => {
        const val = parseFloat(document.getElementById('bulk-subject-max')?.value || '50') || 50;
        document.querySelectorAll('.subject-mark-row:not(.d-none) .sub-max-input').forEach(inp => {
            inp.value = val;
        });
        recalcSubjectMarksTotals();
    });

    document.getElementById('btn-apply-bulk-min')?.addEventListener('click', () => {
        const pct = parseFloat(document.getElementById('bulk-subject-min-pct')?.value || '0.40') || 0.40;
        document.querySelectorAll('.subject-mark-row:not(.d-none)').forEach(row => {
            const maxInp = row.querySelector('.sub-max-input');
            const minInp = row.querySelector('.sub-min-input');
            const maxVal = parseFloat(maxInp?.value || '0') || 0;
            const minVal = Math.round(maxVal * pct * 10) / 10;
            if (minInp) minInp.value = minVal;
        });
        recalcSubjectMarksTotals();
    });

    document.getElementById('btn-apply-range-bulk-min')?.addEventListener('click', () => {
        const pct = parseFloat(document.getElementById('bulk-range-min-pct')?.value || '0.40') || 0.40;
        document.querySelectorAll('#question-ranges-tbody .range-row').forEach(row => {
            const sid = parseInt(row.getAttribute('data-subject-id'), 10);
            const maxHidden = row.querySelector('.sub-max-input');
            const minInp = row.querySelector('.range-input-min');
            const cutoffBadge = row.querySelector('.sub-cutoff-badge');
            const maxVal = parseFloat(maxHidden?.value || '0') || 0;
            const minVal = Math.round(maxVal * pct * 10) / 10;
            if (minInp) minInp.value = minVal;
            if (cutoffBadge) cutoffBadge.textContent = `${(pct * 100).toFixed(1)}%`;
            const rangeItem = (subjectRanges || []).find(r => parseInt(r.subject_id, 10) === sid);
            if (rangeItem) {
                rangeItem.min_marks = minVal;
                rangeItem.cutoff_pct = pct * 100;
            }
        });
        if (hiddenRangesInput) hiddenRangesInput.value = JSON.stringify(subjectRanges);
        recalcSubjectMarksTotals();
    });

    const onSubjectCheckboxChange = function() {
        const parentLabel = this.closest('.subject-chip-item');
        if (parentLabel) {
            if (this.checked) {
                parentLabel.classList.add('active');
                parentLabel.style.background = '#eff6ff';
                parentLabel.style.borderColor = '#3b82f6';
                parentLabel.style.color = '#1d4ed8';
                parentLabel.style.fontWeight = '600';
            } else {
                parentLabel.classList.remove('active');
                parentLabel.style.background = '#ffffff';
                parentLabel.style.borderColor = '#cbd5e1';
                parentLabel.style.color = '#334155';
                parentLabel.style.fontWeight = '500';
            }
        }
        updateSubjectSelectionBadge();
        updatePaperHeader();
        syncSubjectMarksRows();
        syncQuestionRangesTable(false);
        updateNormalSummary();
    };

    const attachSubjectCheckboxListeners = () => {
        document.querySelectorAll('.exam-subject-checkbox').forEach(cb => {
            cb.removeEventListener('change', onSubjectCheckboxChange);
            cb.addEventListener('change', onSubjectCheckboxChange);
        });
    };

    document.getElementById('btn-select-all-subjects')?.addEventListener('click', () => {
        document.querySelectorAll('.exam-subject-checkbox').forEach(cb => {
            cb.checked = true;
            const parentLabel = cb.closest('.subject-chip-item');
            if (parentLabel) {
                parentLabel.classList.add('active');
                parentLabel.style.background = '#eff6ff';
                parentLabel.style.borderColor = '#3b82f6';
                parentLabel.style.color = '#1d4ed8';
                parentLabel.style.fontWeight = '600';
            }
        });
        updateSubjectSelectionBadge();
        updatePaperHeader();
        syncSubjectMarksRows();
        syncQuestionRangesTable(false);
        updateNormalSummary();
    });

    document.getElementById('btn-deselect-all-subjects')?.addEventListener('click', () => {
        document.querySelectorAll('.exam-subject-checkbox').forEach(cb => {
            cb.checked = false;
            const parentLabel = cb.closest('.subject-chip-item');
            if (parentLabel) {
                parentLabel.classList.remove('active');
                parentLabel.style.background = '#ffffff';
                parentLabel.style.borderColor = '#cbd5e1';
                parentLabel.style.color = '#334155';
                parentLabel.style.fontWeight = '500';
            }
        });
        updateSubjectSelectionBadge();
        updatePaperHeader();
        syncSubjectMarksRows();
        syncQuestionRangesTable(false);
        updateNormalSummary();
    });

    // Dynamic Class to Subject & Batch Loading
    classSelect?.addEventListener('change', function () {
        const classId = this.value;

        // Dynamic Batch Loading for Selected Course
        const batchSelect = document.getElementById('batch_id');
        if (batchSelect) {
            batchSelect.innerHTML = '<option value="">-- All Batches (Course-Wide) --</option>';
            if (classId) {
                fetch(`{{ url('getBatchesByCourse') }}/${classId}`)
                    .then(res => res.json())
                    .then(batches => {
                        let bHtml = '<option value="">-- All Batches (Course-Wide) --</option>';
                        if (Array.isArray(batches) && batches.length > 0) {
                            batches.forEach(b => {
                                const shiftStr = b.shift ? ` (${b.shift})` : '';
                                bHtml += `<option value="${b.id}">${b.name}${shiftStr}</option>`;
                            });
                        }
                        batchSelect.innerHTML = bHtml;
                    })
                    .catch(() => {});
            }
        }

        if (!classId) {
            if (chipsContainer) {
                chipsContainer.innerHTML = '<span class="text-muted small p-1" id="subjects-empty-notice"><i class="fa fa-info-circle"></i> Select a Target Course above to load its subjects automatically.</span>';
            }
            if (subjectSelect) {
                subjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';
            }
            updateSubjectSelectionBadge();
            updatePaperHeader();
            updateNormalSummary();
            return;
        }

        if (chipsContainer) {
            chipsContainer.innerHTML = '<span class="text-muted small p-1"><i class="fa fa-spinner fa-spin mr-1"></i> Loading course subjects...</span>';
        }

        fetch(`{{ url('subjectGetData') }}/${classId}`, {
            headers: { 'Accept': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                let singleHtml = '<option value="">-- Select Subject --</option>';
                let chipsHtml = '';
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(item => {
                        singleHtml += `<option value="${item.id}">${item.name}</option>`;
                        // By default, ALL subjects are checked!
                        chipsHtml += `
                            <label class="subject-chip-item active" style="display:inline-flex; align-items:center; gap:6px; padding:5px 10px; border-radius:5px; background:#eff6ff; border:1px solid #3b82f6; font-size:12px; font-weight:600; color:#1d4ed8; margin:0; cursor:pointer;">
                                <input type="checkbox" name="subject_ids[]" value="${item.id}" data-name="${item.name}" class="exam-subject-checkbox" checked style="margin:0; accent-color:#2563eb;">
                                <span>${item.name}</span>
                            </label>
                        `;
                    });
                } else {
                    chipsHtml = '<span class="text-muted small p-1"><i class="fa fa-exclamation-circle text-warning"></i> No subjects configured for this course.</span>';
                }

                if (chipsContainer) chipsContainer.innerHTML = chipsHtml;
                if (subjectSelect) subjectSelect.innerHTML = singleHtml;
                const modalSub = document.getElementById('modal-filter-subject');
                if (modalSub) modalSub.innerHTML = singleHtml;

                attachSubjectCheckboxListeners();
                updateSubjectSelectionBadge();
                syncSubjectMarksRows();
                syncQuestionRangesTable(false);
                updatePaperHeader();
                updateNormalSummary();
            })
            .catch(err => {
                console.warn('Subject load error:', err);
                if (chipsContainer) chipsContainer.innerHTML = '<span class="text-danger small p-1">Failed to load subjects.</span>';
                syncSubjectMarksRows();
                syncQuestionRangesTable(false);
            });
    });

    attachSubjectCheckboxListeners();
    updateSubjectSelectionBadge();
    syncSubjectMarksRows();
    syncQuestionRangesTable(false);

    subjectSelect?.addEventListener('change', () => {
        updatePaperHeader();
        updateNormalSummary();
        renderOnlineExamPreview();
    });

    // Render Question Bank Selected Questions Cards & Paper Preview
    const renderQBankExam = () => {
        const count = selectedQuestions.length;
        selectedCountBadge.textContent = `${count} Questions`;
        qbankKeyCount.textContent = count;

        if (countInput) {
            countInput.value = count;
        }

        if (jsonInput) {
            jsonInput.value = JSON.stringify(selectedQuestions);
        }

        if (count === 0) {
            qbankEmptyState.style.display = 'block';
            selectedQList.innerHTML = '';
            document.getElementById('exam-paper-questions-flow').innerHTML = `
                <div class="text-center py-4 text-muted">
                    <em>Please add questions from Question Bank to generate paper preview...</em>
                </div>`;
            qbankAnswerKeyGrid.innerHTML = '';
            updatePaperHeader();
            renderOMRPreview();
            if (currentMode === 'online') {
                renderOnlineExamPreview();
            }
            return;
        }

        qbankEmptyState.style.display = 'none';

        let cardsHtml = '';
        let paperHtml = '';
        let keyHtml = '';

        selectedQuestions.forEach((q, idx) => {
            const qNum = idx + 1;
            const isMcq = (parseInt(q.question_type_id, 10) === 1);
            const correctLetter = q.correct_letter || (isMcq ? (['A','B','C','D','E'][parseInt(q.correct_ans, 10)] || q.correct_ans || 'A') : (q.correct_ans || ''));

            // Selected Question Card (Left Column)
            cardsHtml += `
            <div class="selected-q-card" data-id="${q.id}" data-index="${idx}">
                <div class="selected-q-header">
                    <div class="selected-q-badges">
                        <span class="q-badge-num">#${qNum}</span>
                        <span class="q-badge-type">${isMcq ? 'MCQ' : 'Descriptive'}</span>
                        <span class="q-badge-ans">Key: ${correctLetter}</span>
                    </div>
                    <div class="selected-q-actions">
                        <button type="button" class="btn-q-action btn-q-up" title="Move Up" ${idx === 0 ? 'disabled' : ''}><i class="fa fa-arrow-up"></i></button>
                        <button type="button" class="btn-q-action btn-q-down" title="Move Down" ${idx === count - 1 ? 'disabled' : ''}><i class="fa fa-arrow-down"></i></button>
                        <button type="button" class="btn-q-action btn-q-delete" title="Remove"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
                <div class="selected-q-text math-content">${q.name || ''}</div>
                ${q.image ? `<img src="${q.image}" class="paper-q-diagram mb-1" alt="Diagram">` : ''}
                ${isMcq ? `
                    <div class="selected-q-options">
                        <div class="opt-pill-preview ${correctLetter === 'A' ? 'is-correct' : ''}"><strong>(A)</strong> <span class="math-content">${q.ans1 || '--'}</span></div>
                        <div class="opt-pill-preview ${correctLetter === 'B' ? 'is-correct' : ''}"><strong>(B)</strong> <span class="math-content">${q.ans2 || '--'}</span></div>
                        <div class="opt-pill-preview ${correctLetter === 'C' ? 'is-correct' : ''}"><strong>(C)</strong> <span class="math-content">${q.ans3 || '--'}</span></div>
                        <div class="opt-pill-preview ${correctLetter === 'D' ? 'is-correct' : ''}"><strong>(D)</strong> <span class="math-content">${q.ans4 || '--'}</span></div>
                    </div>
                ` : `
                    <div class="p-1 bg-light border rounded small font-weight-bold text-success">
                        Answer / Numerical Solution: ${q.correct_ans || 'Descriptive'}
                    </div>
                `}
            </div>`;

            // Paper Flow Block (Right Column)
            paperHtml += `
            <div class="paper-question-block">
                <div class="paper-q-statement">
                    <span class="paper-q-num">Q${qNum}.</span>
                    <span class="paper-q-marks">[${parseFloat(marksCorrectInput?.value || 1.0)} Mark]</span>
                    <span class="math-content">${q.name || ''}</span>
                </div>
                ${q.image ? `<img src="${q.image}" class="paper-q-diagram" alt="Diagram">` : ''}
                ${isMcq ? `
                    <div class="paper-q-options-grid">
                        <div class="paper-q-opt"><span class="paper-q-opt-label">(A)</span> <span class="math-content">${q.ans1 || ''}</span></div>
                        <div class="paper-q-opt"><span class="paper-q-opt-label">(B)</span> <span class="math-content">${q.ans2 || ''}</span></div>
                        <div class="paper-q-opt"><span class="paper-q-opt-label">(C)</span> <span class="math-content">${q.ans3 || ''}</span></div>
                        <div class="paper-q-opt"><span class="paper-q-opt-label">(D)</span> <span class="math-content">${q.ans4 || ''}</span></div>
                    </div>
                ` : `
                    <div style="height: 48px; border: 1px dashed #cbd5e1; border-radius: 2px; margin-top: 4px; background: #fafafa; display: flex; align-items: center; padding-left: 8px; color: #94a3b8; font-size: 10px;">
                        [ Write answer / numerical solution here ]
                    </div>
                `}
            </div>`;

            // Answer Key row
            keyHtml += `
            <div class="ak-row">
                <span class="ak-qnum">Q${qNum}</span>
                <span class="badge badge-success font-weight-bold" style="font-size: 10px;">${correctLetter}</span>
            </div>`;
        });

        selectedQList.innerHTML = cardsHtml;
        document.getElementById('exam-paper-questions-flow').innerHTML = paperHtml;
        qbankAnswerKeyGrid.innerHTML = keyHtml;

        // Render KaTeX formulas
        renderMath(selectedQList);
        renderMath(document.getElementById('exam-paper-questions-flow'));

        // Bind Question Card Actions (Delete, Move Up, Move Down)
        selectedQList.querySelectorAll('.btn-q-delete').forEach(btn => {
            btn.addEventListener('click', function () {
                const card = this.closest('.selected-q-card');
                const idx = parseInt(card.dataset.index, 10);
                selectedQuestions.splice(idx, 1);
                renderQBankExam();
            });
        });

        selectedQList.querySelectorAll('.btn-q-up').forEach(btn => {
            btn.addEventListener('click', function () {
                const card = this.closest('.selected-q-card');
                const idx = parseInt(card.dataset.index, 10);
                if (idx > 0) {
                    const temp = selectedQuestions[idx];
                    selectedQuestions[idx] = selectedQuestions[idx - 1];
                    selectedQuestions[idx - 1] = temp;
                    renderQBankExam();
                }
            });
        });

        selectedQList.querySelectorAll('.btn-q-down').forEach(btn => {
            btn.addEventListener('click', function () {
                const card = this.closest('.selected-q-card');
                const idx = parseInt(card.dataset.index, 10);
                if (idx < selectedQuestions.length - 1) {
                    const temp = selectedQuestions[idx];
                    selectedQuestions[idx] = selectedQuestions[idx + 1];
                    selectedQuestions[idx + 1] = temp;
                    renderQBankExam();
                }
            });
        });

        updatePaperHeader();
        renderOMRPreview();
        if (currentMode === 'online') {
            renderOnlineExamPreview();
        }
    };

    // -------------------------------------------------------------
    // Question Bank Picker Modal Logic
    // -------------------------------------------------------------
    const modal = $('#modal-question-bank');
    let modalFetchedQuestions = [];
    let modalSelectedMap = {};

    const openQuestionModal = () => {
        const targetClass = classSelect.value;
        const targetSub = subjectSelect.value;

        if (targetClass) document.getElementById('modal-filter-class').value = targetClass;
        if (targetSub) document.getElementById('modal-filter-subject').value = targetSub;

        modalSelectedMap = {};
        selectedQuestions.forEach(q => {
            modalSelectedMap[q.id] = true;
        });

        modal.modal('show');
        loadModalQuestions();
    };

    document.getElementById('btn-open-question-modal')?.addEventListener('click', openQuestionModal);
    document.getElementById('btn-open-question-modal-empty')?.addEventListener('click', openQuestionModal);

    const loadModalQuestions = () => {
        const classId = document.getElementById('modal-filter-class').value;
        const subId = document.getElementById('modal-filter-subject').value;
        const typeId = document.getElementById('modal-filter-type').value;
        const search = document.getElementById('modal-filter-search').value;

        const loading = document.getElementById('modal-loading-indicator');
        const list = document.getElementById('modal-questions-list');

        loading.style.display = 'block';
        list.innerHTML = '';

        const params = new URLSearchParams({
            class_type_id: classId,
            subject_id: subId,
            question_type_id: typeId,
            search: search,
        });

        fetch(`{{ url('exam/get-bank-questions') }}?${params.toString()}`)
            .then(res => res.json())
            .then(res => {
                loading.style.display = 'none';
                if (!res.questions || res.questions.length === 0) {
                    list.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-folder-open-o fa-2x mb-2 text-secondary"></i>
                        <p class="mb-0 font-weight-bold" style="color: #1e293b;">No questions found matching the selected filters.</p>
                        <p class="small text-muted">Try selecting another course or subject, or clearing search keywords.</p>
                    </div>`;
                    modalFetchedQuestions = [];
                    updateModalBadge();
                    return;
                }

                modalFetchedQuestions = res.questions;
                let html = '';

                modalFetchedQuestions.forEach(q => {
                    const isChecked = modalSelectedMap[q.id] ? 'checked' : '';
                    const isSelectedClass = modalSelectedMap[q.id] ? 'selected' : '';
                    const isMcq = (parseInt(q.question_type_id, 10) === 1);

                    html += `
                    <div class="modal-q-item ${isSelectedClass}" data-qid="${q.id}">
                        <div class="d-flex align-items-start gap-2">
                            <input type="checkbox" class="modal-q-checkbox mt-1" data-qid="${q.id}" ${isChecked} style="transform: scale(1.15);">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="q-badge-type">${isMcq ? 'MCQ (4 Options)' : 'Descriptive / Numerical'}</span>
                                    <span class="badge badge-secondary" style="font-size: 9px;">ID: #${q.id}</span>
                                    <span class="q-badge-ans">Correct: ${q.correct_letter}</span>
                                </div>
                                <div class="selected-q-text math-content">${q.name || ''}</div>
                                ${q.image ? `<img src="${q.image}" class="paper-q-diagram mb-1" alt="Diagram">` : ''}
                                ${isMcq ? `
                                    <div class="selected-q-options">
                                        <div class="opt-pill-preview ${q.correct_letter === 'A' ? 'is-correct' : ''}"><strong>(A)</strong> <span class="math-content">${q.ans1 || '--'}</span></div>
                                        <div class="opt-pill-preview ${q.correct_letter === 'B' ? 'is-correct' : ''}"><strong>(B)</strong> <span class="math-content">${q.ans2 || '--'}</span></div>
                                        <div class="opt-pill-preview ${q.correct_letter === 'C' ? 'is-correct' : ''}"><strong>(C)</strong> <span class="math-content">${q.ans3 || '--'}</span></div>
                                        <div class="opt-pill-preview ${q.correct_letter === 'D' ? 'is-correct' : ''}"><strong>(D)</strong> <span class="math-content">${q.ans4 || '--'}</span></div>
                                    </div>
                                ` : `
                                    <div class="small font-italic" style="color: #475569;">Numerical / Descriptive Answer: <strong class="text-success">${q.correct_ans || ''}</strong></div>
                                `}
                            </div>
                        </div>
                    </div>`;
                });

                list.innerHTML = html;
                renderMath(list);
                bindModalCheckboxes();
                updateModalBadge();
            })
            .catch(err => {
                loading.style.display = 'none';
                list.innerHTML = `<div class="alert alert-danger py-2 px-3 small">Failed to load questions: ${err.message}</div>`;
            });
    };

    const bindModalCheckboxes = () => {
        document.querySelectorAll('.modal-q-item').forEach(item => {
            const cb = item.querySelector('.modal-q-checkbox');
            item.addEventListener('click', function (e) {
                if (e.target !== cb) {
                    cb.checked = !cb.checked;
                }
                const qid = cb.dataset.qid;
                if (cb.checked) {
                    modalSelectedMap[qid] = true;
                    item.classList.add('selected');
                } else {
                    delete modalSelectedMap[qid];
                    item.classList.remove('selected');
                }
                updateModalBadge();
            });
        });
    };

    const updateModalBadge = () => {
        const count = Object.keys(modalSelectedMap).length;
        document.getElementById('modal-selected-badge').textContent = `${count} Selected`;
        document.getElementById('modal-apply-count').textContent = count;
    };

    document.getElementById('modal-btn-refresh')?.addEventListener('click', loadModalQuestions);
    document.getElementById('modal-filter-class')?.addEventListener('change', loadModalQuestions);
    document.getElementById('modal-filter-subject')?.addEventListener('change', loadModalQuestions);
    document.getElementById('modal-filter-type')?.addEventListener('change', loadModalQuestions);

    document.getElementById('modal-select-all-visible')?.addEventListener('change', function () {
        const isChecked = this.checked;
        document.querySelectorAll('.modal-q-checkbox').forEach(cb => {
            cb.checked = isChecked;
            const qid = cb.dataset.qid;
            const parent = cb.closest('.modal-q-item');
            if (isChecked) {
                modalSelectedMap[qid] = true;
                parent.classList.add('selected');
            } else {
                delete modalSelectedMap[qid];
                parent.classList.remove('selected');
            }
        });
        updateModalBadge();
    });

    // Apply selection from modal
    document.getElementById('modal-btn-apply-selection')?.addEventListener('click', function () {
        const existingMap = {};
        selectedQuestions.forEach(q => existingMap[q.id] = q);

        const newSelection = [];

        // 1. Keep already selected if still checked
        selectedQuestions.forEach(q => {
            const qidStr = String(q.id);
            if (modalSelectedMap[qidStr] || modalSelectedMap[q.id]) {
                newSelection.push(q);
            }
        });

        // 2. Add newly checked questions from fetched modal list
        modalFetchedQuestions.forEach(q => {
            const qidStr = String(q.id);
            const isChecked = modalSelectedMap[qidStr] || modalSelectedMap[q.id];
            if (isChecked && !newSelection.some(x => String(x.id) === qidStr)) {
                newSelection.push(q);
            }
        });

        selectedQuestions = newSelection;
        if (jsonInput) {
            jsonInput.value = JSON.stringify(selectedQuestions);
        }
        renderQBankExam();
        modal.modal('hide');
    });

    // -------------------------------------------------------------
    // Print Handlers (A4 Dedicated New Tab with Max Utilization)
    // -------------------------------------------------------------
    const printOmr = () => {
        const svgElem = (currentMode === 'question_bank')
            ? (document.getElementById('exam-omr-mode3-svg') || document.getElementById('exam-omr-svg'))
            : document.getElementById('exam-omr-svg');
        if (!svgElem) {
            alert('OMR Sheet preview is not available.');
            return;
        }

        const printWin = window.open('', '_blank');
        if (!printWin) {
            document.body.classList.add('printing-omr');
            document.body.classList.remove('printing-paper');
            window.print();
            setTimeout(() => document.body.classList.remove('printing-omr'), 500);
            return;
        }

        const examName = (document.getElementById('exam-name')?.value || 'Examination').trim();
        printWin.document.title = examName + ' - A4 OMR Sheet';

        const faCss = printWin.document.createElement('link');
        faCss.rel = 'stylesheet';
        faCss.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css';
        printWin.document.head.appendChild(faCss);

        const style = printWin.document.createElement('style');
        style.textContent = `
            @page { size: A4 portrait; margin: 4mm 5mm 5mm 5mm; }
            * { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            body { margin: 0; padding: 0; background: #ffffff; color: #0f172a; font-family: Arial, "Helvetica Neue", sans-serif; width: 100%; height: 100%; overflow-x: hidden; }
            @media screen {
                body { background: #475569; padding: 16px 10px 40px; display: flex; flex-direction: column; align-items: center; min-height: 100vh; }
                .screen-toolbar { position: sticky; top: 10px; z-index: 1000; background: #002C54; color: #ffffff; padding: 8px 18px; border-radius: 4px; box-shadow: 0 4px 14px rgba(0,0,0,0.35); display: flex; align-items: center; gap: 14px; margin-bottom: 16px; font-size: 13px; }
                .screen-toolbar button { background: #0284c7; color: #ffffff; border: none; padding: 6px 14px; border-radius: 3px; font-weight: 700; font-size: 12px; cursor: pointer; transition: background 0.15s; display: inline-flex; align-items: center; gap: 6px; }
                .screen-toolbar button:hover { background: #0369a1; }
                .screen-toolbar button.btn-close-action { background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.3); }
                .screen-toolbar button.btn-close-action:hover { background: rgba(255,255,255,0.3); }
                .a4-omr-wrapper { background: #ffffff; width: 202mm; height: 288mm; box-shadow: 0 8px 24px rgba(0,0,0,0.4); display: flex; justify-content: center; align-items: center; padding: 1mm; box-sizing: border-box; }
            }
            @media print {
                .screen-toolbar { display: none !important; }
                body { background: #ffffff !important; padding: 0 !important; margin: 0 !important; }
                .a4-omr-wrapper { width: 100% !important; height: 100% !important; padding: 0 !important; margin: 0 !important; box-shadow: none !important; display: flex; justify-content: center; align-items: center; }
            }
            .a4-omr-wrapper svg { width: 100% !important; height: 100% !important; max-height: 287mm !important; display: block !important; margin: 0 auto !important; }
        `;
        printWin.document.head.appendChild(style);

        const toolbar = printWin.document.createElement('div');
        toolbar.className = 'screen-toolbar';
        toolbar.innerHTML = `
            <span><strong><i class="fa fa-th"></i> A4 OMR Sheet View</strong> — ${examName}</span>
            <button onclick="window.print()"><i class="fa fa-print"></i> Print / Save as PDF</button>
            <button class="btn-close-action" onclick="window.close()">Close Tab</button>
        `;
        printWin.document.body.appendChild(toolbar);

        const wrapper = printWin.document.createElement('div');
        wrapper.className = 'a4-omr-wrapper';
        const svgClone = svgElem.cloneNode(true);
        svgClone.setAttribute('width', '100%');
        svgClone.setAttribute('height', '100%');
        svgClone.style.maxWidth = '100%';
        svgClone.style.maxHeight = '287mm';
        svgClone.style.display = 'block';
        svgClone.style.margin = '0 auto';
        wrapper.appendChild(svgClone);
        printWin.document.body.appendChild(wrapper);

        setTimeout(() => {
            try {
                printWin.focus();
                printWin.print();
            } catch (err) {
                console.log(err);
            }
        }, 350);
    };

    const printPaper = () => {
        const paperElem = document.getElementById('exam-paper-print-area');
        if (!paperElem || selectedQuestions.length === 0) {
            alert('Please add at least one question from the Question Bank before printing the question paper.');
            return;
        }

        const printWin = window.open('', '_blank');
        if (!printWin) {
            document.body.classList.add('printing-paper');
            document.body.classList.remove('printing-omr');
            window.print();
            setTimeout(() => document.body.classList.remove('printing-paper'), 500);
            return;
        }

        const examTitle = (document.getElementById('exam-name')?.value || 'Examination').trim();
        printWin.document.title = examTitle + ' - A4 Question Paper';

        const katexCss = printWin.document.createElement('link');
        katexCss.rel = 'stylesheet';
        katexCss.href = 'https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css';
        printWin.document.head.appendChild(katexCss);

        const faCss = printWin.document.createElement('link');
        faCss.rel = 'stylesheet';
        faCss.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css';
        printWin.document.head.appendChild(faCss);

        const style = printWin.document.createElement('style');
        style.textContent = `
            @page { size: A4 portrait; margin: 8mm 10mm 10mm 10mm; }
            * { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            html, body { margin: 0; padding: 0; background: #ffffff; color: #0f172a; font-family: "DejaVu Sans", "Segoe UI", Arial, Helvetica, sans-serif; font-size: 10.5pt; line-height: 1.45; width: 100%; }
            @media screen {
                body { background: #475569; padding: 16px 10px 40px; display: flex; flex-direction: column; align-items: center; min-height: 100vh; }
                .screen-toolbar { position: sticky; top: 10px; z-index: 1000; background: #002C54; color: #ffffff; padding: 8px 18px; border-radius: 4px; box-shadow: 0 4px 14px rgba(0,0,0,0.35); display: flex; align-items: center; gap: 14px; margin-bottom: 16px; font-size: 13px; }
                .screen-toolbar button { background: #0284c7; color: #ffffff; border: none; padding: 6px 14px; border-radius: 3px; font-weight: 700; font-size: 12px; cursor: pointer; transition: background 0.15s; display: inline-flex; align-items: center; gap: 6px; }
                .screen-toolbar button:hover { background: #0369a1; }
                .screen-toolbar button.btn-close-action { background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.3); }
                .screen-toolbar button.btn-close-action:hover { background: rgba(255,255,255,0.3); }
                .a4-paper-sheet { background: #ffffff; width: 210mm; min-height: 297mm; padding: 12mm 14mm; box-shadow: 0 8px 24px rgba(0,0,0,0.4); box-sizing: border-box; }
            }
            @media print {
                .screen-toolbar { display: none !important; }
                body { background: #ffffff !important; padding: 0 !important; margin: 0 !important; }
                .a4-paper-sheet { width: 100% !important; padding: 0 !important; margin: 0 !important; box-shadow: none !important; }
            }
            .exam-paper-header { text-align: center; border-bottom: 2px solid #002C54; padding-bottom: 6px; margin-bottom: 8px; }
            .exam-paper-institute { font-size: 15pt; font-weight: 800; color: #002C54; text-transform: uppercase; letter-spacing: .03em; margin: 0 0 2px; }
            .exam-paper-exam-name { font-size: 12pt; font-weight: 700; color: #0f172a; margin: 0 0 6px; }
            .exam-paper-meta-table { width: 100%; font-size: 9.5pt; font-weight: 600; color: #1e293b; border-collapse: collapse; }
            .exam-paper-meta-table td { padding: 2px 4px; }
            .exam-instructions-box { background: #f8fafc; border: 1px dashed #94a3b8; padding: 5px 10px; font-size: 8.5pt; color: #334155; margin-bottom: 10px; border-radius: 3px; }
            .exam-instructions-box strong { color: #002C54; }
            .exam-instructions-box ul { margin: 2px 0 0; padding-left: 18px; }
            .paper-question-block { margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid #e2e8f0; page-break-inside: avoid; break-inside: avoid; }
            .paper-question-block:last-child { border-bottom: none; }
            .paper-q-statement { font-size: 10pt; font-weight: 600; line-height: 1.4; margin-bottom: 4px; color: #0f172a; }
            .paper-q-num { font-weight: 800; color: #002C54; margin-right: 4px; }
            .paper-q-marks { float: right; font-size: 8.5pt; font-weight: 700; color: #64748b; }
            .paper-q-options-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 4px 16px; font-size: 9.5pt; padding-left: 12px; }
            .paper-q-opt { color: #1e293b; line-height: 1.35; }
            .paper-q-opt-label { font-weight: 700; color: #002C54; margin-right: 4px; }
            .paper-q-diagram { max-height: 130px; display: block; margin: 5px 0; border: 1px solid #cbd5e1; border-radius: 2px; padding: 2px; }
            .katex { font-size: 1.05em; }
        `;
        printWin.document.head.appendChild(style);

        const toolbar = printWin.document.createElement('div');
        toolbar.className = 'screen-toolbar';
        toolbar.innerHTML = `
            <span><strong><i class="fa fa-file-text-o"></i> A4 Question Paper View</strong> — ${examTitle}</span>
            <button onclick="window.print()"><i class="fa fa-print"></i> Print / Save as PDF</button>
            <button class="btn-close-action" onclick="window.close()">Close Tab</button>
        `;
        printWin.document.body.appendChild(toolbar);

        const container = printWin.document.createElement('div');
        container.className = 'a4-paper-sheet';
        container.innerHTML = paperElem.innerHTML;
        printWin.document.body.appendChild(container);

        setTimeout(() => {
            try {
                printWin.focus();
                printWin.print();
            } catch (err) {
                console.log(err);
            }
        }, 400);
    };

    document.querySelectorAll('.btn-trigger-print-omr').forEach(btn => btn.addEventListener('click', printOmr));

    // Dual tab switcher in Mode 3 (Question Paper vs Matching OMR Sheet)
    const tabPaperBtn = document.getElementById('tab-paper-btn');
    const tabOmrBtn = document.getElementById('tab-omr-btn');
    const tabPaperContent = document.getElementById('tab-paper-content');
    const tabOmrContent = document.getElementById('tab-omr-content');
    const printActiveLabel = document.getElementById('print-active-label');

    const switchMode3Tab = (targetTab) => {
        if (targetTab === 'omr') {
            tabPaperBtn?.classList.remove('active');
            tabOmrBtn?.classList.add('active');
            tabPaperBtn?.setAttribute('aria-selected', 'false');
            tabOmrBtn?.setAttribute('aria-selected', 'true');

            if (tabPaperContent) {
                tabPaperContent.classList.remove('active', 'show');
                tabPaperContent.style.setProperty('display', 'none', 'important');
            }
            if (tabOmrContent) {
                tabOmrContent.classList.add('active', 'show');
                tabOmrContent.style.setProperty('display', 'flex', 'important');
            }
            if (printActiveLabel) printActiveLabel.textContent = 'Print OMR Sheet';
            renderOMRPreview();
        } else {
            tabOmrBtn?.classList.remove('active');
            tabPaperBtn?.classList.add('active');
            tabOmrBtn?.setAttribute('aria-selected', 'false');
            tabPaperBtn?.setAttribute('aria-selected', 'true');

            if (tabOmrContent) {
                tabOmrContent.classList.remove('active', 'show');
                tabOmrContent.style.setProperty('display', 'none', 'important');
            }
            if (tabPaperContent) {
                tabPaperContent.classList.add('active', 'show');
                tabPaperContent.style.setProperty('display', 'flex', 'important');
            }
            if (printActiveLabel) printActiveLabel.textContent = 'Print Question Paper';
        }
    };

    tabPaperBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        switchMode3Tab('paper');
    });
    tabOmrBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        switchMode3Tab('omr');
    });

    document.getElementById('btn-print-active-tab')?.addEventListener('click', function () {
        const isPaperTab = tabPaperBtn?.classList.contains('active');
        if (isPaperTab) {
            printPaper();
        } else {
            printOmr();
        }
    });

    // -------------------------------------------------------------
    // Form Submission Actions
    // -------------------------------------------------------------
    const actionInput = document.getElementById('form-action-type');

    const submitBtn = document.getElementById('btn-submit-save');
    submitBtn?.addEventListener('click', () => {
        if (jsonInput && Array.isArray(selectedQuestions)) {
            jsonInput.value = JSON.stringify(selectedQuestions);
        }
        actionInput.value = 'save';
        form?.submit();
    });

    form?.addEventListener('submit', () => {
        if (jsonInput && Array.isArray(selectedQuestions)) {
            jsonInput.value = JSON.stringify(selectedQuestions);
        }
    });

    // -------------------------------------------------------------
    // Initial Render Sequence
    // -------------------------------------------------------------
    switchMode(currentMode);
    updateSubjectSelectionBadge();
    renderOMRPreview();
    if (selectedQuestions.length > 0) {
        renderQBankExam();
    }

    // Auto-trigger print if requested via query string
    @if(request()->boolean('print_omr') || request()->boolean('print'))
        window.setTimeout(printOmr, 450);
    @elseif(request()->boolean('print_paper'))
        window.setTimeout(printPaper, 450);
    @endif
});
</script>
@endsection
