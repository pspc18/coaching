/**
 * Exam Online Question Builder with CKEditor & MathJax / KaTeX Support
 */
(function () {
  const toggle = document.getElementById('exam-online-toggle');
  const toggleNote = document.getElementById('exam-online-toggle-note');
  const jsonInput = document.getElementById('exam-online-questions-json');
  const omrPanel = document.getElementById('exam-omr-preview-panel');
  const onlinePanel = document.getElementById('exam-online-builder-panel');
  const countInput = document.getElementById('exam-question-count');

  const loadingEl = document.getElementById('online-builder-loading');
  const workspaceEl = document.getElementById('online-builder-workspace');
  const jumpSelect = document.getElementById('online-question-jump');
  const progressEl = document.getElementById('online-builder-progress');
  const positionEl = document.getElementById('online-builder-position');
  const questionNumEl = document.getElementById('online-question-number');
  const savedStateEl = document.getElementById('online-question-saved-state');

  const correctAnswerSelect = document.getElementById('online-correct-answer');
  const btnBack = document.getElementById('online-question-back');
  const btnSaveNext = document.getElementById('online-question-save-next');
  const nextLabel = btnSaveNext?.querySelector('[data-next-label]');

  const previewQuestion = document.getElementById('preview-question-text');
  const previewOptA = document.querySelector('#preview-option-a .opt-text');
  const previewOptB = document.querySelector('#preview-option-b .opt-text');
  const previewOptC = document.querySelector('#preview-option-c .opt-text');
  const previewOptD = document.querySelector('#preview-option-d .opt-text');
  const previewCorrectBadge = document.getElementById('preview-correct-badge');

  if (!toggle || !onlinePanel || !omrPanel) return;

  // Initialize questions data
  let questionsData = {};
  try {
    const raw = jsonInput?.value || '{}';
    questionsData = typeof raw === 'string' ? (JSON.parse(raw) || {}) : (raw || {});
  } catch (e) {
    questionsData = {};
  }

  let currentQuestion = 1;
  let totalQuestions = Math.max(1, Math.min(300, parseInt(countInput?.value || '50', 10) || 50));
  let activeEditorKey = 'question'; // 'question', 'a', 'b', 'c', 'd'
  let editorsReady = false;

  const editorIds = {
    question: 'online-question-editor-textarea',
    a: 'online-option-a-textarea',
    b: 'online-option-b-textarea',
    c: 'online-option-c-textarea',
    d: 'online-option-d-textarea',
  };

  // Helper to safely get editor HTML
  const getEditorHtml = (key) => {
    const id = editorIds[key];
    if (window.CKEDITOR && window.CKEDITOR.instances[id]) {
      return window.CKEDITOR.instances[id].getData();
    }
    const el = document.getElementById(id);
    return el ? el.value : '';
  };

  // Helper to safely set editor HTML
  const setEditorHtml = (key, html) => {
    const id = editorIds[key];
    if (window.CKEDITOR && window.CKEDITOR.instances[id]) {
      window.CKEDITOR.instances[id].setData(html || '');
    } else {
      const el = document.getElementById(id);
      if (el) el.value = html || '';
    }
  };

  // Helper to insert content into active editor
  const insertIntoActiveEditor = (textToInsert) => {
    const id = editorIds[activeEditorKey] || editorIds.question;
    if (window.CKEDITOR && window.CKEDITOR.instances[id]) {
      const instance = window.CKEDITOR.instances[id];
      instance.insertHtml(textToInsert);
    } else {
      const el = document.getElementById(id);
      if (el) {
        const start = el.selectionStart || 0;
        const end = el.selectionEnd || 0;
        el.value = el.value.substring(0, start) + textToInsert + el.value.substring(end);
        el.focus();
      }
    }
    updateLivePreview();
  };

  // Render Math with KaTeX in Live Preview
  const renderMathInPreview = () => {
    const container = document.getElementById('live-question-preview-content');
    if (!container) return;

    if (window.renderMathInElement) {
      try {
        window.renderMathInElement(container, {
          delimiters: [
            { left: '$$', right: '$$', display: true },
            { left: '$', right: '$', display: false },
            { left: '\\(', right: '\\)', display: false },
            { left: '\\[', right: '\\]', display: true },
          ],
          throwOnError: false,
        });
      } catch (e) {
        console.warn('KaTeX render error:', e);
      }
    }
  };

  const updateLivePreview = () => {
    const qHtml = getEditorHtml('question');
    const aHtml = getEditorHtml('a');
    const bHtml = getEditorHtml('b');
    const cHtml = getEditorHtml('c');
    const dHtml = getEditorHtml('d');
    const correct = correctAnswerSelect?.value || '';

    if (previewQuestion) {
      previewQuestion.innerHTML = qHtml?.trim() ? `<strong>Q${currentQuestion}.</strong> ${qHtml}` : `<em>Question ${currentQuestion} statement empty...</em>`;
    }
    if (previewOptA) previewOptA.innerHTML = aHtml?.trim() ? aHtml : '<span class="text-text-muted-light">Empty</span>';
    if (previewOptB) previewOptB.innerHTML = bHtml?.trim() ? bHtml : '<span class="text-text-muted-light">Empty</span>';
    if (previewOptC) previewOptC.innerHTML = cHtml?.trim() ? cHtml : '<span class="text-text-muted-light">Empty</span>';
    if (previewOptD) previewOptD.innerHTML = dHtml?.trim() ? dHtml : '<span class="text-text-muted-light">Empty</span>';

    if (previewCorrectBadge) {
      if (correct) {
        previewCorrectBadge.textContent = `Correct: Option ${correct}`;
        previewCorrectBadge.classList.remove('hidden');
      } else {
        previewCorrectBadge.classList.add('hidden');
      }
    }

    renderMathInPreview();
  };

  // Sync with Answer Key Radio buttons on the left
  const syncAnswerKeyOnLeft = (qNum, option) => {
    const radio = document.querySelector(`input[name="answer_key[${qNum}]"][value="${option}"]`);
    if (radio) {
      radio.checked = true;
      radio.dispatchEvent(new Event('change', { bubbles: true }));
    }
  };

  const getAnswerKeyFromLeft = (qNum) => {
    const radio = document.querySelector(`input[name="answer_key[${qNum}]"]:checked`);
    return radio ? radio.value : '';
  };

  // Populate Jump Select Dropdown
  const populateJumpSelect = () => {
    if (!jumpSelect) return;
    jumpSelect.innerHTML = '';
    totalQuestions = Math.max(1, Math.min(300, parseInt(countInput?.value || '50', 10) || 50));

    for (let i = 1; i <= totalQuestions; i++) {
      const opt = document.createElement('option');
      opt.value = i;
      const isConfigured = questionsData[i] && (questionsData[i].question?.trim() || questionsData[i].correct);
      opt.textContent = `Q${i}${isConfigured ? ' ✓' : ''}`;
      if (i === currentQuestion) opt.selected = true;
      jumpSelect.appendChild(opt);
    }
  };

  const updateProgressStats = () => {
    totalQuestions = Math.max(1, Math.min(300, parseInt(countInput?.value || '50', 10) || 50));
    let configuredCount = 0;
    for (let i = 1; i <= totalQuestions; i++) {
      if (questionsData[i] && (questionsData[i].question?.trim() || questionsData[i].correct)) {
        configuredCount++;
      }
    }

    if (progressEl) progressEl.textContent = `${configuredCount} / ${totalQuestions} completed`;
    if (positionEl) positionEl.textContent = `Question ${currentQuestion} of ${totalQuestions}`;
    if (questionNumEl) questionNumEl.textContent = currentQuestion;

    if (nextLabel) {
      nextLabel.textContent = (currentQuestion >= totalQuestions) ? 'Save Question' : 'Save & Next';
    }

    if (btnBack) {
      btnBack.disabled = (currentQuestion <= 1);
      btnBack.classList.toggle('opacity-50', currentQuestion <= 1);
      btnBack.classList.toggle('cursor-not-allowed', currentQuestion <= 1);
    }
  };

  // Load question data into editors
  const loadQuestion = (num) => {
    currentQuestion = Math.max(1, Math.min(totalQuestions, num));
    populateJumpSelect();

    const data = questionsData[currentQuestion] || {};
    setEditorHtml('question', data.question || '');
    setEditorHtml('a', (data.options && data.options.A) || '');
    setEditorHtml('b', (data.options && data.options.B) || '');
    setEditorHtml('c', (data.options && data.options.C) || '');
    setEditorHtml('d', (data.options && data.options.D) || '');

    const correct = data.correct || getAnswerKeyFromLeft(currentQuestion) || '';
    if (correctAnswerSelect) correctAnswerSelect.value = correct;

    if (savedStateEl) {
      const hasContent = !!(data.question?.trim() || correct);
      savedStateEl.textContent = hasContent ? 'Configured ✓' : 'Not saved';
      savedStateEl.className = hasContent ? 'text-[10px] font-bold text-emerald-600' : 'text-[10px] font-semibold text-amber-600';
    }

    updateProgressStats();
    setTimeout(updateLivePreview, 100);
  };

  // Save current question into data store
  const saveCurrentQuestion = () => {
    const qText = getEditorHtml('question')?.trim() || '';
    const optA = getEditorHtml('a')?.trim() || '';
    const optB = getEditorHtml('b')?.trim() || '';
    const optC = getEditorHtml('c')?.trim() || '';
    const optD = getEditorHtml('d')?.trim() || '';
    const correct = correctAnswerSelect?.value || '';

    if (qText || optA || optB || optC || optD || correct) {
      questionsData[currentQuestion] = {
        question: qText,
        options: {
          A: optA,
          B: optB,
          C: optC,
          D: optD,
        },
        correct: correct,
      };

      if (correct) {
        syncAnswerKeyOnLeft(currentQuestion, correct);
      }
    }

    // Update JSON hidden input
    if (jsonInput) {
      jsonInput.value = JSON.stringify(questionsData);
    }

    if (savedStateEl) {
      savedStateEl.textContent = 'Saved ✓';
      savedStateEl.className = 'text-[10px] font-bold text-emerald-600';
    }

    updateProgressStats();
    populateJumpSelect();
  };

  // Initialize CKEditor on all 5 textareas
  const initCKEditors = () => {
    if (!window.CKEDITOR) {
      // Fallback if CDN blocked
      loadingEl?.classList.add('hidden');
      workspaceEl?.classList.remove('hidden');
      editorsReady = true;
      loadQuestion(currentQuestion);
      return;
    }

    // MathJax Config for CKEditor
    window.CKEDITOR.config.mathJaxLib = 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js?config=TeX-AMS_HTML';
    window.CKEDITOR.config.extraPlugins = 'mathjax';
    window.CKEDITOR.config.removePlugins = 'elementspath,resize';
    window.CKEDITOR.config.height = '140px';

    const questionToolbar = [
      { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Subscript', 'Superscript'] },
      { name: 'paragraph', items: ['NumberedList', 'BulletedList'] },
      { name: 'insert', items: ['Table', 'Mathjax', 'SpecialChar'] },
      { name: 'tools', items: ['Maximize', 'Source'] },
    ];

    const optionToolbar = [
      { name: 'basicstyles', items: ['Bold', 'Italic', 'Subscript', 'Superscript'] },
      { name: 'insert', items: ['Mathjax', 'SpecialChar'] },
    ];

    // Destroy existing instances if any
    Object.values(editorIds).forEach((id) => {
      if (window.CKEDITOR.instances[id]) {
        try { window.CKEDITOR.instances[id].destroy(true); } catch (e) {}
      }
    });

    // Create Main Question Editor
    const qEditor = window.CKEDITOR.replace(editorIds.question, {
      toolbar: questionToolbar,
      height: '130px',
    });

    // Create Option Editors
    ['a', 'b', 'c', 'd'].forEach((optKey) => {
      const optEditor = window.CKEDITOR.replace(editorIds[optKey], {
        toolbar: optionToolbar,
        height: '65px',
      });

      optEditor.on('focus', () => { activeEditorKey = optKey; });
      optEditor.on('change', updateLivePreview);
    });

    qEditor.on('focus', () => { activeEditorKey = 'question'; });
    qEditor.on('change', updateLivePreview);

    let instancesLoaded = 0;
    const checkAllLoaded = () => {
      instancesLoaded++;
      if (instancesLoaded >= 5) {
        editorsReady = true;
        loadingEl?.classList.add('hidden');
        workspaceEl?.classList.remove('hidden');
        loadQuestion(currentQuestion);
      }
    };

    qEditor.on('instanceReady', checkAllLoaded);
    ['a', 'b', 'c', 'd'].forEach((optKey) => {
      if (window.CKEDITOR.instances[editorIds[optKey]]) {
        window.CKEDITOR.instances[editorIds[optKey]].on('instanceReady', checkAllLoaded);
      }
    });

    // Fallback safety timer
    setTimeout(() => {
      if (!editorsReady) {
        editorsReady = true;
        loadingEl?.classList.add('hidden');
        workspaceEl?.classList.remove('hidden');
        loadQuestion(currentQuestion);
      }
    }, 2500);
  };

  // Math Quick Insert Ribbon Handler
  document.getElementById('math-quick-insert-ribbon')?.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-insert-math]');
    if (!btn) return;
    const mathCode = btn.dataset.insertMath;
    const formatted = ` $${mathCode}$ `;
    insertIntoActiveEditor(formatted);
  });

  // Track active textarea if raw textareas used
  Object.entries(editorIds).forEach(([key, id]) => {
    const el = document.getElementById(id);
    el?.addEventListener('focus', () => { activeEditorKey = key; });
    el?.addEventListener('input', updateLivePreview);
  });

  // Event Listeners for Navigation & Actions
  correctAnswerSelect?.addEventListener('change', () => {
    const val = correctAnswerSelect.value;
    if (val) {
      syncAnswerKeyOnLeft(currentQuestion, val);
    }
    updateLivePreview();
  });

  jumpSelect?.addEventListener('change', () => {
    saveCurrentQuestion();
    loadQuestion(parseInt(jumpSelect.value, 10));
  });

  btnBack?.addEventListener('click', () => {
    if (currentQuestion > 1) {
      saveCurrentQuestion();
      loadQuestion(currentQuestion - 1);
    }
  });

  btnSaveNext?.addEventListener('click', () => {
    saveCurrentQuestion();
    if (currentQuestion < totalQuestions) {
      loadQuestion(currentQuestion + 1);
    } else {
      const mainSaveBtn = document.querySelector('#exam-create-form button[type="submit"]');
      if (mainSaveBtn) {
        mainSaveBtn.classList.add('ring-2', 'ring-emerald-500');
        setTimeout(() => mainSaveBtn.classList.remove('ring-2', 'ring-emerald-500'), 1200);
      }
    }
  });

  countInput?.addEventListener('input', () => {
    totalQuestions = Math.max(1, Math.min(300, parseInt(countInput.value || '50', 10) || 50));
    populateJumpSelect();
    updateProgressStats();
  });

  // Toggle Switch Handler
  const applyToggleState = (isOnline) => {
    if (isOnline) {
      omrPanel.classList.add('hidden');
      onlinePanel.classList.remove('hidden');
      toggleNote?.classList.remove('hidden');

      if (!editorsReady) {
        initCKEditors();
      } else {
        loadQuestion(currentQuestion);
      }
    } else {
      omrPanel.classList.remove('hidden');
      onlinePanel.classList.add('hidden');
      toggleNote?.classList.add('hidden');
    }
  };

  toggle?.addEventListener('change', () => {
    applyToggleState(toggle.checked);
  });

  // Save data on form submit
  document.getElementById('exam-create-form')?.addEventListener('submit', () => {
    if (toggle?.checked) {
      saveCurrentQuestion();
    }
  });

  // Initial State on Page Load
  applyToggleState(toggle.checked);
})();
