@php
    $getSetting = Helper::getSetting();
@endphp
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    window.IMAGE_SHOW_PATH = "{{ env('IMAGE_SHOW_PATH') }}";
</script>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Cards</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .page {
            /*width: 210mm;*/
             width: 38%;
            margin: auto;
            padding: 5mm;
            box-sizing: border-box;
        }
        .id-card {
            width: 92mm;
            height: 55mm;
            box-sizing: border-box;
            page-break-inside: avoid;
            break-inside: avoid;
            border: 1px solid #ccc;
            padding: 2mm;
            background-size: cover;
            position: relative;
        }
        .photo {
            width: 100%;
            height: 30mm;
            background-color: #ccc;
            margin-bottom: 5mm;
        }
        .details {
            font-size: 12px;
        }
        table {
            width: 100%;
        }
        td {
            text-transform: capitalize;
            font-size: 12px;
            line-height: 14px;
        }
        .logo_size {
            max-width: 100%;
        }

        .background-image {
            /*background-image: url('{{ asset("schoolimage/setting/id_card_background/{$getSetting->id_card_background}") }}');*/
           
            z-index: 1;
             background-repeat: no-repeat;
             background-position: center;
             background-size: 100% 100%; /* This will be overridden by JS */
        }

        .controls-panel {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 270px;
            background-color: #f0f0f0;
            border-right: 2px solid #ccc;
            padding: 20px;
            overflow-y: auto;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
            z-index: 9999;
        }
        .controls-panel h3 {
            margin-top: 0;
            font-size: 18px;
            color: #333;
            text-align: center;
        }
        .controls-panel label {
            display: block;
            margin-bottom: 3px;
            font-size: 13px;
            color: #222;
        }
        .controls-panel input[type="number"],
        .controls-panel input[type="range"] {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-top: 3px;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .controls-panel fieldset {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .controls-panel legend {
            font-weight: bold;
            font-size: 14px;
        }

        /*@media print {*/
        /*    @page {*/
        /*        size: A4 portrait;*/
        /*        margin: 5mm !important;*/
        /*    }*/
        /*    body, .page {*/
        /*        margin: 0 !important;*/
        /*        padding: 0 !important;*/
        /*        -webkit-print-color-adjust: exact;*/
        /*        print-color-adjust: exact;*/
        /*    }*/
        /*    .controls-panel {*/
        /*        display: none !important;*/
        /*    }*/
        /*    .id-card {*/
        /*        page-break-inside: avoid;*/
        /*        break-inside: avoid;*/
        /*    }*/
        /*}*/
        .draggable {
    cursor: move;
    position: absolute;
    }
    #applyToAllBtn{
        width: 100%;
        background: blue;
        color: white;
        border: 0;
        padding: 5px;
        border-radius: 5px;
        font-size: 16px;
        font-weight: 700;
        cursor:pointer;
    }
    #showSavedTemp{
      margin-top: 10px;
      width: 100%;
      border-radius: none;
      border-radius: 0;
      border: 1px solid;
      padding: 5px;
      font-weight: 600;
      margin-bottom: 10px;
      cursor:pointer;
    
    }
    #boldToggle.active {
        color: #000000;
        font-weight: 900;
        border: none;
        font-size: large;
    }
    </style>
</head>
<body>
<div class="controls-panel">
   <div style="display:flex;justify-content: space-between;margin:0px 0px 5px 0px;">
       <div>
            <h3 style="margin:0;text-align: start;">Customize ID Cards</h3>
            <span style="font-size:12px;color:orange;">Click To Download Single Id Card </span>
       </div>
       <div>
           <button id="downloadBtn" style="cursor: pointer;padding:2px;"> <img class="" src="{{ env('IMAGE_SHOW_PATH').'/icons/download_icon.png' }}" width="30px"></button>
        </div>
   </div>
   
    
    <button id="applyToAllBtn" >Apply to All ID Cards</button>
<button id="showSavedTemp" >Show Saved Template</button>
 
<div id="templateList" class="mb-3" style="display: none;">
               @foreach($templates as $template)
               <button class="load-template-btn " style="width: 45%;padding: 6px;border: none; background: #004f51;color: white;font-weight: bold;font-size: 16px;cursor: pointer;margin-bottom: 10px;" data-template='@json(["design_content" => $template->design_content, "bg_image" => $template->bg_image])' data-template-id="{{ $template->id }}" data-template-name="{{ $template->name }}">
        {{ $template->name }}
    </button>
    @endforeach
</div>
    
    
    
<fieldset>
    <legend style="color:orange;">Set Background Sizes (%) :-</legend>
    <div style="display:flex;justify-content: space-around;">
        <label> Width :
            <input type="number" id="bgWidthPercent" value="100" min="0" max="200" style="width:90%;">
        </label>
        <label> Height :
          <input type="number" id="bgHeightPercent" value="99" min="0" max="200" style="width:90%;">
        </label>
    </div>
    <legend style="color:orange;">Set Card Sizes (mm) :-</legend>
    <div style="display:flex;justify-content: space-around;">
        <label>Card Width (mm):
            <input type="number" id="cardWidth" value="109"  style="width:90%;">
        </label>
        <label>Card Height (mm):
            <input type="number" id="cardHeight" value="61"  style="width:90%;">
        </label>
    </div>
    <legend style="color:orange;display:none;">Student Image Sizes (PX) :-</legend>
    <div style="display:flex;justify-content: space-around;display:none;">
         <label>Width :
        <input type="number" id="studentImgWidth" value="75" style="width:90%;">
        </label>
        <label>Height:
            <input type="number" id="studentImgHeight" value="76" style="width:90%;">
        </label>
        <label style="display: grid;">Round:
            <input type="range" id="imageBorderRadius" min="0" max="100" value="0" style="width:90%;">
        </label>
    </div>
    <legend style="color:orange; display:none;"> Seal Sign Sizes (PX):-</legend>
    <div style="display:flex;justify-content: space-around;display:none;">
            <label>Width:
             <input type="number" id="sealWidth" value="75">
            </label>
            <label>Height:
                <input type="number" id="sealHeight" value="33">
            </label>
     </div>
</fieldset>
    
    <fieldset>
      <legend>Show/Hide ID Content</legend>
      <div style="display:flex;justify-content: space-around;">
          <div>
               <label><input type="checkbox" class="field-toggle" data-field="image" checked> Show Image</label><br>
               <label><input type="checkbox" class="field-toggle" data-field="name" checked> Show Name</label><br>
               <label><input type="checkbox" class="field-toggle" data-field="srno" checked> Show S.R.No</label><br>
               <label><input type="checkbox" class="field-toggle" data-field="father" checked> Show Father</label><br>
               <label><input type="checkbox" class="field-toggle" data-field="class" checked> Show Class</label><br>
          </div>
          <div>
              <label><input type="checkbox" class="field-toggle" data-field="dob" checked> Show DOB</label><br>
              <label><input type="checkbox" class="field-toggle" data-field="phone" checked> Show Phone</label><br>
              <label><input type="checkbox" class="field-toggle" data-field="address" checked> Show Address</label><br>
              <label><input type="checkbox" class="field-toggle" data-field="seal" checked> Show Seal</label><br>
          </div>
      </div>
    </fieldset>
    
    
    <fieldset>
          <legend style="color:orange;">Add Custom Label</legend>
          <div style="display:flex;justify-content: space-around;">
              <label style="width:40%;">
                Label Text:
                <input type="text" id="customLabelText" style="width:90%;">
                 <button type="button" id="addCustomLabelBtn">Add Label</button>
              </label>
              <label style="width:40%;">Font Size:
                        <input type="number" id="fontSizeControl" min="6" max="30" value="14" style="width:90%;">
              </label>
          </div>
          <legend style="color:orange;">Selected Field Styles</legend>
          <div style="display:flex;justify-content: space-around;flex-wrap: wrap">
                <div class="form-group" style="width:40%;">
                    <label>Font Size</label>
                    <input type="number" id="selectedFontSize" class="form-control" placeholder="e.g. 14" / style="width:90%;">
                </div>
               
               
                <div class="form-group" style="width:40%;">
                    <label>Color</label>
                    <input type="color" id="selectedFontColor" class="form-control" value="#000000" / style="width:60%;">
                     <button type="button" id="boldToggle" class="btn btn-light border fw-bold" style="font-weight:normal;border:none;border: .5px solid #c7c7c7;border-radius: 5px;">B</button>
                </div>
                <!-- Width -->
                <div class="form-group" style="width:45%;">
                    <label>Width ( %)</label>
                    <input type="number" id="selectedWidth" class="form-control" placeholder="e.g.  80%" style="width:90%;">
                </div>
            
                <!-- Text Align -->
                <div class="form-group" style="width:45%;">
                    <label>Text Align</label>
                    <select id="selectedTextAlign" class="form-control" style="width:90%;">
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                        <option value="justify">Justify</option>
                    </select>
                </div>
            
                <!-- Font Family -->
                <div class="form-group" style="width:100%;">
                    <label>Font Family</label>
                    <select id="selectedFontFamily" class="form-control" style="width:95%;">
                        <option value="Arial">Arial</option>
                        <option value="Times New Roman">Times New Roman</option>
                        <option value="Verdana">Verdana</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Courier New">Courier New</option>
                        <option value="Tahoma">Tahoma</option>
                    </select>
                </div>
            </div>
    </fieldset>

    <fieldset>
    <legend>Set Page</legend>
    
    <label>Cards per Row:
        <input type="number" id="columns" value="2" min="1" max="5">
    </label>
     <legend style="color:orange;">Gap Between Cards</legend>
    <div style="display:flex;justify-content: space-around;">
        <label>Row Gap (mm):
            <input type="number" id="rowGap" value="2" style="width:90%;">
        </label>
        <label>Column Gap (mm):
            <input type="number" id="colGap" value="15" style="width:90%;">
        </label>
    </div>
    </fieldset>
    
    
    <fieldset>
        <legend>Print Margin (mm) <span style="font-size:10px;color:red;">( Min. Requirement )</span></legend>
        <label>Top:
            <input type="number" id="printMarginTop" value="5">
        </label>
        <label>Left:
            <input type="number" id="printMarginLeft" value="5">
        </label>
        <label>Right:
            <input type="number" id="printMarginRight" value="5">
        </label>
        <label>Bottom:
            <input type="number" id="printMarginBottom" value="5">
        </label>
    </fieldset> 
  
</div>








@if(!empty($data))
<div class="page">
    <div id="gridContainer" style="display: grid; grid-template-columns: repeat(2, 1fr); grid-gap: 5mm 20mm;">
        @foreach($data as $item)
           <div class="id-card  background-image " data-firstname="{{ $item['first_name'] }}" data-admissionno="{{ $item['admissionNo'] }}">

    <!-- Student Image -->
    <img src="{{ env('IMAGE_SHOW_PATH') . '/profile/' . ($item['image'] ?? '') }}"
         onerror="this.src='{{ env('IMAGE_SHOW_PATH') }}/default/user_image.jpg'"
         class="draggable student-img"
         style="top: 5mm; left: 65mm; width: 25mm; height: 30mm;" data-field="image">

   
    <div class="draggable editable" style="top: 4mm; left: 25mm; font-size: 12px;" data-field="name">
        <span class="value">{{ $item['first_name'] }} {{ $item['last_name'] }}</span>
    </div>

    
    
    <div class="draggable editable" style="top: 10mm; left: 25mm; font-size: 12px;" data-field="srno">
        <span class="value">{{ $item['admissionNo'] }}</span>
    </div>

    <div class="draggable editable" style="top: 16mm; left: 25mm; font-size: 12px;" data-field="father">
        <span class="value">{{ $item['father_name'] }}</span>
    </div>

    <div class="draggable editable" style="top: 22mm; left: 25mm; font-size: 12px;" data-field="class">
        <span class="value">{{ $item['class_name'] }}</span>
    </div>

    
    <div class="draggable editable" style="top: 28mm; left: 25mm; font-size: 12px;" data-field="dob">
        <span class="value">{{ date('d-m-Y', strtotime($item['dob'])) }}</span>
    </div>

   
    <div class="draggable editable" style="top: 34mm; left: 25mm; font-size: 12px;" data-field="phone">
        <span class="value">{{ $item['mobile'] }}</span>
    </div>
  
    <div class="draggable editable" style="top: 40mm; left: 25mm; font-size: 11px; width: 50mm;" data-field="address">
        <span class="value">{{ $item['address'] }}</span>
    </div>

    <!-- Seal -->
    <img src="{{ env('IMAGE_SHOW_PATH') . '/setting/seal_sign/' . $getSetting['seal_sign'] }}"
         onerror="this.src='{{ env('IMAGE_SHOW_PATH') }}/default/seal.png'"
         class="draggable seal-img"
         style="top: 50mm; left: 65mm; width: 25mm; height: 15mm;" data-field="seal">
</div>

        @endforeach
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const colInput = document.getElementById('columns');
    const rowGapInput = document.getElementById('rowGap');
    const colGapInput = document.getElementById('colGap');
    const cardWidthInput = document.getElementById('cardWidth');
    const cardHeightInput = document.getElementById('cardHeight');
    const fontSizeInput = document.getElementById('fontSizeControl');
    const studentImgWidth = document.getElementById('studentImgWidth');
    const studentImgHeight = document.getElementById('studentImgHeight');
    const sealImgWidth = document.getElementById('sealWidth');
    const sealImgHeight = document.getElementById('sealHeight');
    const gridContainer = document.getElementById('gridContainer');

    const labelInput = document.getElementById('customLabelText');
    const addLabelBtn = document.getElementById('addCustomLabelBtn');
    const applyBtn = document.getElementById('applyToAllBtn');


    enableInlineEditing();

    // document.getElementById('selectedFontColor').addEventListener('input', function () {
    //         if (selectedLabel) {
    //             selectedLabel.style.color = this.value;
    //         }
    //     });

    // document.getElementById('selectedFontSize').addEventListener('input', function () {
    //         if (selectedLabel) {
    //             selectedLabel.style.fontSize = this.value + 'px';
    //         }
    //     });
    
    // Track the currently selected element
       
       let selectedElement = null;
        document.addEventListener("click", function (e) {
            if (e.target.closest(".editable")) {
                document.querySelectorAll(".editable").forEach(el => el.classList.remove("selected"));
                selectedElement = e.target.closest(".editable");
                selectedElement.classList.add("selected");
                let styles = window.getComputedStyle(selectedElement);
                document.getElementById("selectedFontSize").value = parseInt(styles.fontSize) || "";
                document.getElementById("selectedFontColor").value = rgbToHex(styles.color);
                document.getElementById("selectedWidth").value = selectedElement.style.width || "";
                document.getElementById("boldToggle").checked = (styles.fontWeight === "700" || styles.fontWeight === "bold");
                document.getElementById("selectedTextAlign").value = styles.textAlign;
                document.getElementById("selectedFontFamily").value = styles.fontFamily.replace(/["']/g, "");
            }
        });
        
       
        function rgbToHex(rgb) {
            if (!rgb) return "#000000";
            const result = rgb.match(/\d+/g).map(Number);
            return "#" + result.map(x => x.toString(16).padStart(2, '0')).join('');
        }
        
       
        function applyStyleToSelected(style, value) {
            if (selectedElement) {
                selectedElement.style[style] = value;
            }
        }
        
       
        document.getElementById("selectedFontSize").addEventListener("input", function () {
            applyStyleToSelected("fontSize", this.value + "px");
        });
        
       
        document.getElementById("selectedFontColor").addEventListener("input", function () {
            applyStyleToSelected("color", this.value);
        });
        
        
       document.getElementById("selectedWidth").addEventListener("input", function () {
            let selected = document.querySelector(".selected"); // current selected field
            if (selected) {
                let val = this.value.trim();
                if (val !== "") {
                    selected.style.width = val + "%"; // always set width in %
                }
            }
        });
        
        
        document.getElementById("selectedTextAlign").addEventListener("change", function () {
            applyStyleToSelected("textAlign", this.value);
        });
        
      
        document.getElementById("selectedFontFamily").addEventListener("change", function () {
            applyStyleToSelected("fontFamily", this.value);
            
        });
        
        
        document.getElementById("boldToggle").addEventListener("click", function () {
            if (!selectedElement) return;
        
            // toggle bold
            if (selectedElement.style.fontWeight === "bold") {
                selectedElement.style.fontWeight = "normal";
                this.classList.remove("active");
            } else {
                selectedElement.style.fontWeight = "bold";
                this.classList.add("active");
            }
        });


    document.getElementById('imageBorderRadius').addEventListener('input', function () {
        const radius = this.value + 'px';
            const studentImage = document.querySelector('.draggable[data-field="image"]');
            if (studentImage) {
                studentImage.style.borderRadius = radius;
                studentImage.dataset.borderRadius = radius; // store for saving
            }
        });


    function updateGrid() {
        const columns = parseInt(colInput.value);
        const rowGap = rowGapInput.value + 'mm';
        const colGap = colGapInput.value + 'mm';
        gridContainer.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
        gridContainer.style.rowGap = rowGap;
        gridContainer.style.columnGap = colGap;
    }

    function updateCardSize() {
        const width = cardWidthInput.value + 'mm';
        const height = cardHeightInput.value + 'mm';
        document.querySelectorAll('.id-card').forEach(card => {
            card.style.width = width;
            card.style.height = height;
        });
    }

    function updateFontSize() {
        const fontSize = fontSizeInput.value + 'px';
        document.querySelectorAll('.id-card .draggable').forEach(el => {
            el.style.fontSize = fontSize;
        });
    }
    
    
   
    function applyImageSizes() {
        const stuW = studentImgWidth.value;
        const stuH = studentImgHeight.value;
        const sealW = sealImgWidth.value;
        const sealH = sealImgHeight.value;

        document.querySelectorAll('.student-img').forEach(img => {
            img.style.width = stuW + 'px';
            img.style.height = stuH + 'px';
        });

        document.querySelectorAll('.seal-img').forEach(seal => {
            seal.style.width = sealW + 'px';
            seal.style.height = sealH + 'px';
        });
    }

    function updatePrintMargins() {
        const top = document.getElementById('printMarginTop').value || 0;
        const left = document.getElementById('printMarginLeft').value || 0;
        const right = document.getElementById('printMarginRight').value || 0;
        const bottom = document.getElementById('printMarginBottom').value || 0;

        let styleTag = document.getElementById("dynamicPrintStyle");
        if (!styleTag) {
            styleTag = document.createElement('style');
            styleTag.id = "dynamicPrintStyle";
            document.head.appendChild(styleTag);
        }

        styleTag.innerHTML = `
            @media print {
                @page {
                    margin: ${top}mm ${right}mm ${bottom}mm ${left}mm !important;
                    size: auto;
                }
                body, .page {
                    margin: 0 !important;
                    padding: 0 !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
                .controls-panel {
                    display: none !important;
                }
                .id-card {
                    page-break-inside: avoid;
                    break-inside: avoid;
                }
            }
        `;
    }

    function makeDraggable(el) {
        let offsetX = 0, offsetY = 0, startX = 0, startY = 0;

        el.onmousedown = function(e) {
            e.preventDefault();
            startX = e.clientX;
            startY = e.clientY;
            const rect = el.getBoundingClientRect();
            offsetX = startX - rect.left;
            offsetY = startY - rect.top;

            document.onmousemove = function(e) {
                const parentRect = el.parentElement.getBoundingClientRect();
                el.style.left = (e.clientX - parentRect.left - offsetX) + 'px';
                el.style.top = (e.clientY - parentRect.top - offsetY) + 'px';
            };

            document.onmouseup = function() {
                document.onmousemove = null;
                document.onmouseup = null;
            };
        };
    }

    function enableInlineEditing() {
    document.querySelectorAll('.draggable').forEach(el => {
        el.addEventListener('click', function () {
            selectLabel(this);
        });

        // For editable text only, you can add these conditionally or for elements with .editable class
        if(el.classList.contains('editable')) {
            el.addEventListener('dblclick', function () {
                el.setAttribute('contenteditable', 'true');
                el.focus();
            });

            el.addEventListener('blur', function () {
                el.setAttribute('contenteditable', 'false');
            });

            el.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    el.blur();
                }
            });
        }
    });
}

    // let selectedLabel = null;
    
    // function selectLabel(label) {
    //     selectedLabel = label;
    //     document.getElementById('selectedFontSize').value = parseInt(label.style.fontSize || '14');
    //     document.getElementById('selectedFontColor').value = label.style.color || '#000000';
    // }





    function addCustomLabel(text) {
    if (!text) return;

    const cards = document.querySelectorAll('.id-card');
    if (cards.length === 0) return;

    const uniqueField = 'custom-' + Date.now();

    cards.forEach((card, index) => {
       
        const label = document.createElement('div');
        label.className = 'draggable editable';
        label.textContent = text;
        label.dataset.field = uniqueField;
        label.style.position = 'absolute';
        label.style.top = '5mm';
        label.style.left = '5mm';
        label.style.fontSize = '12px';
        label.style.fontWeight = 'bold';
        label.style.cursor = 'move';

        card.appendChild(label);
        makeDraggable(label);
    });

    enableInlineEditing();
}

    function applyToAllCards() {
    const cards = document.querySelectorAll('.id-card');
    if (cards.length <= 1) return;

    const firstCard = cards[0];
    const { width, height } = firstCard.style;

    const firstElements = firstCard.querySelectorAll('.draggable');

    cards.forEach((card, index) => {
        if (index === 0) return;

        card.style.width = width;
        card.style.height = height;

        const currentElements = card.querySelectorAll('.draggable');

        firstElements.forEach(sourceEl => {
            const field = sourceEl.dataset.field;
            const targetEl = Array.from(currentElements).find(el => el.dataset.field === field);

            if (targetEl) {
                
                targetEl.style.top = sourceEl.style.top;
                targetEl.style.left = sourceEl.style.left;
                targetEl.style.fontSize = sourceEl.style.fontSize;
                targetEl.style.width = sourceEl.style.width;
                targetEl.style.textAlign = sourceEl.style.textAlign;
                targetEl.style.color = sourceEl.style.color;
                targetEl.style.fontFamily = sourceEl.style.fontFamily;
                targetEl.style.fontWeight = sourceEl.style.fontWeight;

               
                if (sourceEl.dataset.spacing) {
                    targetEl.dataset.spacing = sourceEl.dataset.spacing;
                }
            }
        });
    });
}

   
    document.querySelectorAll('.draggable').forEach(el => {
        el.style.position = 'absolute';
        makeDraggable(el);
    });

    enableInlineEditing();

    // Event bindings
    [colInput, rowGapInput, colGapInput].forEach(input => input.addEventListener('input', updateGrid));
    [cardWidthInput, cardHeightInput].forEach(input => input.addEventListener('input', updateCardSize));
    fontSizeInput.addEventListener('input', updateFontSize);
    [studentImgWidth, studentImgHeight, sealImgWidth, sealImgHeight].forEach(input => input.addEventListener('input', applyImageSizes));
    ['printMarginTop', 'printMarginLeft', 'printMarginRight', 'printMarginBottom'].forEach(id => {
        document.getElementById(id).addEventListener('input', updatePrintMargins);
    });

    if (addLabelBtn && labelInput) {
        addLabelBtn.addEventListener('click', () => {
            const text = labelInput.value.trim();
            if (text) {
                addCustomLabel(text);
                labelInput.value = '';
            } else {
                alert('Please enter a label name.');
            }
        });
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', applyToAllCards);
    }

    document.querySelectorAll('.field-toggle').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const field = this.dataset.field;
            document.querySelectorAll(`.draggable[data-field="${field}"]`).forEach(el => {
                el.style.display = this.checked ? 'block' : 'none';
            });
        });
    });

    // Initial setup
    updateGrid();
    updateCardSize();
    updateFontSize();
    applyImageSizes();
    updatePrintMargins();
   
});
</script>
<script>
  const bgWidthInput = document.getElementById('bgWidthPercent');
  const bgHeightInput = document.getElementById('bgHeightPercent');

  function updateBackgroundSize() {
    const width = bgWidthInput.value + '%';
    const height = bgHeightInput.value + '%';

    document.querySelectorAll('.background-image').forEach(el => {
      el.style.backgroundSize = `${width} ${height}`;
    });
  }

  
  bgWidthInput.addEventListener('input', updateBackgroundSize);
  bgHeightInput.addEventListener('input', updateBackgroundSize);

 
  updateBackgroundSize();
</script>


<script>
    document.querySelectorAll('.load-template-btn').forEach(button => {
        button.addEventListener('click', function () {
            const data = JSON.parse(this.dataset.template);
            const design_content = data.design_content;
            const bgImage = data.bg_image;

            document.querySelectorAll('.id-card.background-image').forEach(card => {
                if (bgImage) {
                    card.style.backgroundImage = `url('${window.IMAGE_SHOW_PATH}id_template_bg/${bgImage}')`;
                } else {
                    card.style.backgroundImage = 'none';
                }
            });

          
            document.getElementById('cardWidth').value = design_content.cardWidth;
            document.getElementById('cardHeight').value = design_content.cardHeight;
            document.getElementById('bgWidthPercent').value = design_content.bgWidthPercent;
            document.getElementById('bgHeightPercent').value = design_content.bgHeightPercent;
            document.getElementById('fontSizeControl').value = design_content.fontSize;
            document.getElementById('studentImgWidth').value = design_content.studentImgWidth;
            document.getElementById('studentImgHeight').value = design_content.studentImgHeight;
            document.getElementById('sealWidth').value = design_content.sealWidth;
            document.getElementById('sealHeight').value = design_content.sealHeight;

          
            document.querySelectorAll('.field-toggle').forEach(toggle => {
                const field = design_content.fields.find(f => f.field === toggle.dataset.field);
                toggle.checked = !!field?.visible;

                
                document.querySelectorAll(`.draggable[data-field="${toggle.dataset.field}"]`).forEach(el => {
                    el.style.display = field?.visible ? 'block' : 'none';
                });
            });

           
           document.querySelectorAll('.draggable').forEach(draggable => {
            const pos = design_content.positions.find(p => p.field === draggable.dataset.field);
            if (pos) {
                draggable.style.top = pos.top;
                draggable.style.left = pos.left;
                if (pos.fontSize) {
                    draggable.style.fontSize = pos.fontSize;
                }
                if (pos.color) {
                    draggable.style.color = pos.color;
                }
                if (pos.borderRadius) {
                    draggable.style.borderRadius = pos.borderRadius;
                    draggable.dataset.borderRadius = pos.borderRadius;
                
                    // Update range input if it's the student image
                    if (draggable.dataset.field === 'image') {
                        const radiusValue = pos.borderRadius?.replace('px', '') || 0;
                        document.getElementById('imageBorderRadius').value = radiusValue;
                    }
                }
                
            }
        });

           
            document.querySelectorAll('input').forEach(input => {
    if(input.id !== 'fontSizeControl') {
        input.dispatchEvent(new Event('input'));
    }
});

          
            setTimeout(() => {
                if (typeof applyToAllCards === 'function') {
                    applyToAllCards();
                } else {
                    console.warn('applyToAllCards() is not defined.');
                }
            }, 100);
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function () {
    $('#showSavedTemp').on('click', function () {
        const $templateList = $('#templateList');

        if ($templateList.is(':visible')) {
            $templateList
                .stop(true, true)   
                .animate({ opacity: 0 }, 500, function () {
                    $templateList.slideUp(500);
                });
            $(this).text('Show Saved Templates');
        } else {
            $templateList
                .stop(true, true)
                .css({
                    opacity: 0,
                    display: 'flex', 
                    justifyContent: 'space-evenly' 
                })
                .hide() 
                .slideDown(500, function () {
                    $templateList.animate({ opacity: 1 }, 500);
                });
            $(this).text('Hide Saved Templates');
        }
    });
});
</script>

// <script>
// document.getElementById("downloadBtn").addEventListener("click", function () {
//     document.querySelectorAll(".id-card").forEach((card, index) => {
//         const rect = card.getBoundingClientRect();

//         html2canvas(card, {
//             backgroundColor: null,
//             scale: 1,
//             width: rect.width,
//             height: rect.height
//         }).then(canvas => {
//             const link = document.createElement("a");
//             const firstName = card.querySelector('[data-field="name"] .value')?.textContent || "Student";
//             const admissionNo = card.querySelector('[data-field="srno"] .value')?.textContent || index+1;

//             link.download = `${firstName} (${admissionNo}).jpeg`;
//             link.href = canvas.toDataURL("image/jpeg", 1.0);
//             link.click();
//         });
//     });
// });
// </script>

<script>
    document.getElementById("downloadBtn").addEventListener("click", function () {
        const cards = document.querySelectorAll(".id-card");

        if (cards.length === 0) {
            alert("No ID cards found to download.");
            return;
        }

        // Temporarily hide elements you don't want in the screenshot
        const controlsPanel = document.querySelector(".controls-panel");
        if (controlsPanel) {
            controlsPanel.style.display = "none";
        }

        cards.forEach((card) => {
            const firstName = card.dataset.firstname || "Student";
            const admissionNo = card.dataset.admissionno || "ID";

            html2canvas(card, {
                // Set a higher scale for better resolution and clarity
                scale: 4, 
                logging: false,
                useCORS: true, 
            }).then(canvas => {
                const link = document.createElement("a");
                link.download = `${firstName}_${admissionNo}.png`; // Download as PNG for lossless quality
                link.href = canvas.toDataURL("image/png");
                link.click();
            });
        });

        // Restore hidden elements after a short delay
        setTimeout(() => {
            if (controlsPanel) {
                controlsPanel.style.display = ""; 
            }
        }, 500); 
    });
</script>
</body>
</html>