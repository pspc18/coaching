@php
    $getSetting = Helper::getSetting();
@endphp
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Cards</title>
  <link rel="stylesheet" href="{{ asset('public/assets/school/css/common.css') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .page {
            width: 210mm;
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
            left: 40%;
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
            background-image: url('{{ asset("schoolimage/setting/id_card_background/{$getSetting->id_card_background}") }}');
           
            z-index: 1;
             background-repeat: no-repeat;
            
              background-size: 100% 99%; /* This will be overridden by JS */
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

        @media print {
            @page {
                size: A4 portrait;
                margin: 5mm !important;
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
        .draggable {
    cursor: move;
    position: absolute;
}


#saveTemplateBtn{
    width: 100%;
    background: blue;
    color: white;
    border: 0;
    padding: 17px;
    border-radius: 17px;
    font-size: 16px;
    font-weight: 700;
}

.form-control{
    padding:0 ;
}

.controls-panel input[type="number"], .controls-panel input[type="range"] {  
        padding: 0;
}
    </style>
</head>
<body>
    


<form id="templateForm" enctype="multipart/form-data">
    @csrf
    <div class="controls-panel">
        <h3>Customize ID Cards</h3>
        <input type="hidden" id="templateId" name="templateId" value="">
        <fieldset>
            <legend>Template Info</legend>
            <label>Name:
                <input type="text" class="form-control" name="name" id="templateName" required value="">
            </label>
        </fieldset>

        <fieldset>
        <legend>Upload Background Image</legend>
        <input type="file" class="form-control" name="bg_image" id="bgImageUploader" accept="image/*">
            
        <legend style="color:orange;">Background Sizes (%) :-</legend>
         <div style="display:flex;justify-content: space-around;">
            <label>Width:
                <input type="number" id="bgWidthPercent" min="0"  value="100" max="200" style="width:90%;">
            </label>
            <label>Height:
                <input type="number" id="bgHeightPercent" min="0"  value="100" max="200" style="width:90%;">
            </label>
        </div>
        <legend style="color:orange;">Card Sizes (mm) :-</legend>
         <div style="display:flex;justify-content: space-around;">
           <label>Width:
                <input type="number" id="cardWidth" min="50" value="109" max="200" style="width:90%;">
            </label>
            <label>Height:
                <input type="number" id="cardHeight" min="30" value="61" max="150" style="width:90%;">
            </label>
        </div>
         <legend style="color:orange;">Student Image Sizes (PX) :-</legend>
        <div style="display:flex;justify-content: space-around;">
            <label style="display: grid;">Width:
                <input type="number" id="studentImgWidth" value="75" style="width:90%;">
            </label>
            <label style="display: grid;">Height:
                <input type="number" id="studentImgHeight" value="76" style="width:90%;">
            </label>
            <label style="display: grid;">Round:
                <input type="range" id="imageBorderRadius" min="0" max="100" value="0" style="width:90%;">
            </label>
        </div>
            <legend style="color:orange;"> Seal Sign Sizes (PX):-</legend>
            <div style="display:flex;justify-content: space-around;">
             <label style="display: grid;"> Width:
                <input type="number" id="sealWidth" value="75" style="width:90%;">
            </label>
            <label style="display: grid;"> Height:
                <input type="number" id="sealHeight" value="34" style="width:90%;">
            </label>
            </div>
        </fieldset>

        <fieldset>
            <legend>Show/Hide ID Content</legend>
            <!-- Field toggles -->
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
            <legend style="color:orange;">Custom Label</legend>
            <div style="display:flex;justify-content: space-around;">
                <label style="width:40%;">Label Text:
                    <input type="text" id="customLabelText" style="width:90%;">
                    <button type="button" id="addCustomLabelBtn">Add Label</button>
                </label>
                <label style="width:40%;">Font Size:
                    <input type="number" id="fontSizeControl" min="6" max="30" value="14" style="width:90%;">
                </label>
            </div>
            
             <legend style="color:orange;">Selected Field Font Size And Color</legend>
            <div style="display:flex;justify-content: space-around;">
                <div class="form-group" style="width:40%;">
                    <label>Font Size</label>
                    <input type="number" id="selectedFontSize" class="form-control" placeholder="e.g. 14" / style="width:90%;">
                </div>
                
                <div class="form-group" style="width:40%;">
                    <label>Color</label>
                    <input type="color" id="selectedFontColor" class="form-control" value="#000000" / style="width:90%;">
                </div>
            </div>
            
        </fieldset>
        
         <button type="submit" class="btn btn-success mt-3" style="color: white;background: blue;padding: 10px;  width: 100%;position: relative; bottom: -10px;PADDING-BOTTOM: 39PX;cursor: pointer;">Save Template</button>
                
    </div>
</form>
    
    <div class="border-end vh-100 overflow-auto p-3 right-sidebar" style="float: right;position: fixed;right: 0;height: 100vh;width: 270px; background-color: #f0f0f0;border-left: 2px solid #ccc; padding: 20px;overflow-y: auto;box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);z-index: 9999;">
           <i class="fa fa-arrow-left" aria-hidden="true"></i>
            <!--<img  src="{{ env('IMAGE_SHOW_PATH') . '/default/arrow_left.png'}}" width="30px" height=30px >-->
            <h3 style="display:inline;vertical-align: super;">Saved Templates</h3>
            <div id="templateList" class="mb-3">
               @foreach($templates as $template)
               
        <button class="load-template-btn " style="width: 100%;padding: 6px;border: none; background: #004f51;color: white;font-weight: bold;font-size: 16px;cursor: pointer;margin-bottom: 10px;" data-template='@json(["design_content" => $template->design_content, "bg_image" => $template->bg_image])' data-template-id="{{ $template->id }}" data-template-name="{{ $template->name }}">
        {{ $template->name }}
    </button>
    @endforeach
            </div>
        </div>
    
  

<div class="page">
    <div id="gridContainer" style="display: grid; grid-template-columns: repeat(2, 1fr); grid-gap: 5mm 20mm;">
      
           <div class="id-card background-image">

    <!-- Student Image -->
    <img src="{{ env('IMAGE_SHOW_PATH') }}/default/user_image.jpg"
         class="draggable student-img"
         style="top: 5mm; left: 65mm; width: 25mm; height: 30mm;" data-field="image">

   
    <div class="draggable editable" style="top: 4mm; left: 25mm; font-size: 12px;" data-field="name">
        <span class="value">Rajendra Yadav</span>
    </div>

    
    
    <div class="draggable editable" style="top: 10mm; left: 25mm; font-size: 12px;" data-field="srno">
        <span class="value">1042</span>
    </div>

    <div class="draggable editable" style="top: 16mm; left: 25mm; font-size: 12px;" data-field="father">
        <span class="value">Arjun Lal Yadav</span>
    </div>

    <div class="draggable editable" style="top: 22mm; left: 25mm; font-size: 12px;" data-field="class">
        <span class="value">12th</span>
    </div>

    
    <div class="draggable editable" style="top: 28mm; left: 25mm; font-size: 12px;" data-field="dob">
        <span class="value">04/03/2003</span>
    </div>

   
    <div class="draggable editable" style="top: 34mm; left: 25mm; font-size: 12px;" data-field="phone">
        <span class="value">9376550276</span>
    </div>

    <div class="draggable editable" style="top: 40mm; left: 25mm; font-size: 11px; width: 60mm;" data-field="address">
        <span class="value">Nindola , chomu , jaipur , Raj(303803)</span>
    </div>

    <!-- Seal -->
    <img src="{{ env('IMAGE_SHOW_PATH') . '/setting/seal_sign/' . $getSetting['seal_sign'] }}"
         onerror="this.src='{{ env('IMAGE_SHOW_PATH') }}/default/seal.png'"
         class="draggable seal-img"
         style="top: 50mm; left: 65mm; width: 25mm; height: 15mm;" data-field="seal">
</div>

      
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const colInput = document.getElementById('columns');
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
   
            enableInlineEditing();

    document.getElementById('selectedFontColor').addEventListener('input', function () {
            if (selectedLabel) {
                selectedLabel.style.color = this.value;
            }
        });

    document.getElementById('selectedFontSize').addEventListener('input', function () {
            if (selectedLabel) {
                selectedLabel.style.fontSize = this.value + 'px';
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
    document.querySelectorAll('.editable').forEach(el => {
        // Click to select label for styling
        el.addEventListener('click', function () {
            selectLabel(this); // highlight for color/font control
        });

        // Double-click to enable text editing
        el.addEventListener('dblclick', function () {
            el.setAttribute('contenteditable', 'true');
            el.focus();
        });

        // When user finishes editing (on blur), disable editing
        el.addEventListener('blur', function () {
            el.setAttribute('contenteditable', 'false');
        });

        // Pressing Enter key ends editing
        el.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                el.blur();
            }
        });
    });
}

    let selectedLabel = null;
    
    function selectLabel(label) {
        selectedLabel = label;
        document.getElementById('selectedFontSize').value = parseInt(label.style.fontSize || '14');
        document.getElementById('selectedFontColor').value = label.style.color || '#000000';
    }





    function addCustomLabel(text, fontSize = '14px', top = '5mm', left = '5mm', field = null) {
    if (!text) return;

    const cards = document.querySelectorAll('.id-card');
    if (cards.length === 0) return;

    // Generate unique field if not provided (for new labels)
    const uniqueField = field || 'custom-' + Date.now();

    cards.forEach((card, index) => {
        // Check if label with this field already exists
        let existingLabel = card.querySelector(`.draggable[data-field="${uniqueField}"]`);
        if (existingLabel) {
            existingLabel.textContent = text;
            existingLabel.style.fontSize = fontSize;
            existingLabel.style.top = top;
            existingLabel.style.left = left;
        } else {
            // Create new label div
            const label = document.createElement('div');
            label.className = 'draggable editable';
            label.textContent = text;
            label.dataset.field = uniqueField;
            label.style.position = 'absolute';
            label.style.top = top;
            label.style.left = left;
            label.style.fontSize = fontSize;
            label.style.fontWeight = 'bold';
            label.style.cursor = 'move';

            card.appendChild(label);
            makeDraggable(label);
        }
    });

    enableInlineEditing();
}

    

    // Initialize draggable and editable on page load
    document.querySelectorAll('.draggable').forEach(el => {
        el.style.position = 'absolute';
        makeDraggable(el);
    });

    enableInlineEditing();

  
    [cardWidthInput, cardHeightInput].forEach(input => input.addEventListener('input', updateCardSize));
    fontSizeInput.addEventListener('input', updateFontSize);
    [studentImgWidth, studentImgHeight, sealImgWidth, sealImgHeight].forEach(input => input.addEventListener('input', applyImageSizes));
    

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

   

    document.querySelectorAll('.field-toggle').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const field = this.dataset.field;
            document.querySelectorAll(`.draggable[data-field="${field}"]`).forEach(el => {
                el.style.display = this.checked ? 'block' : 'none';
            });
        });
    });
    
    
    
    
    // Initial setup
   
    updateCardSize();
    updateFontSize();
    applyImageSizes();
   
   
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

  // Attach event listeners
  bgWidthInput.addEventListener('input', updateBackgroundSize);
  bgHeightInput.addEventListener('input', updateBackgroundSize);

  // Initialize on page load
  updateBackgroundSize();
</script>




<script>
document.getElementById('bgImageUploader').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const imageUrl = URL.createObjectURL(file);
    document.querySelector('.background-image').style.backgroundImage = `url(${imageUrl})`;
});

</script>


<script>
document.getElementById('templateForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    // Collect design content and append to formData
    const templateData = collectTemplateSettings();
    formData.append('design_content', JSON.stringify(templateData));

    fetch('/save_template', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        form.reset(); // Optional: clear form
    })
    .catch(err => {
        console.error(err);
        alert('Failed to save template.');
    });
});
</script>


<script>
    function collectTemplateSettings() {
    return {
        cardWidth: document.getElementById('cardWidth').value,
        cardHeight: document.getElementById('cardHeight').value,
        bgWidthPercent: document.getElementById('bgWidthPercent').value,
        bgHeightPercent: document.getElementById('bgHeightPercent').value,
        fontSize: document.getElementById('fontSizeControl').value,
        studentImgWidth: document.getElementById('studentImgWidth').value,
        studentImgHeight: document.getElementById('studentImgHeight').value,
        sealWidth: document.getElementById('sealWidth').value,
        sealHeight: document.getElementById('sealHeight').value,
        fields: [...document.querySelectorAll('.field-toggle')].map(el => ({
            field: el.dataset.field,
            visible: el.checked
        })),
        positions: [...document.querySelectorAll('.draggable')].map(el => ({
            field: el.dataset.field,
            top: el.style.top,
            left: el.style.left,
            fontSize: el.style.fontSize || null,
            color: el.style.color || null,
            borderRadius: el.dataset.borderRadius || '',
            textContent: el.textContent || null 
            
        }))
    };
}


</script>

<script>
document.querySelectorAll('.load-template-btn').forEach(button => {
    button.addEventListener('click', function () {
        const data = JSON.parse(this.dataset.template);
        const design_content = data.design_content;
        const templateId = this.dataset.templateId; // must be available as data-template-id
        const templateName = this.dataset.templateName; // must be available as data-template-id
         
        const bgImage = data.bg_image;

        // Set background image
        const idCard = document.querySelector('.id-card.background-image');
        if (idCard && bgImage) {
            idCard.style.backgroundImage = `url('{{ env('IMAGE_SHOW_PATH') }}id_template_bg/${bgImage}')`;
        }

        // Apply dimensions
        document.getElementById('cardWidth').value = design_content.cardWidth;
         document.getElementById('templateId').value = templateId;
         document.getElementById('templateName').value = templateName;
        document.getElementById('cardHeight').value = design_content.cardHeight;
        document.getElementById('bgWidthPercent').value = design_content.bgWidthPercent;
        document.getElementById('bgHeightPercent').value = design_content.bgHeightPercent;
        document.getElementById('fontSizeControl').value = design_content.fontSize;
        document.getElementById('studentImgWidth').value = design_content.studentImgWidth;
        document.getElementById('studentImgHeight').value = design_content.studentImgHeight;
        document.getElementById('sealWidth').value = design_content.sealWidth;
        document.getElementById('sealHeight').value = design_content.sealHeight;

        // Reset all fields to hidden by default
        document.querySelectorAll('.draggable').forEach(draggable => {
            draggable.style.display = 'none';
        });

        // Apply field visibility and toggle status
        document.querySelectorAll('.field-toggle').forEach(toggle => {
            const field = design_content.fields.find(f => f.field === toggle.dataset.field);
            if (field) {
                toggle.checked = field.visible;

                // Show/hide draggable element based on visibility
                const draggableEl = document.querySelector(`.draggable[data-field="${field.field}"]`);
                if (draggableEl) {
                    draggableEl.style.display = field.visible ? 'block' : 'none';
                }
            }
        });

        design_content.positions.forEach(pos => {
        let el = document.querySelector(`.draggable[data-field="${pos.field}"]`);
    
        // If it's a custom label that doesn't already exist, create it
        if (!el && pos.field.startsWith('custom-')) {
            el = document.createElement('div');
            el.className = 'draggable editable';
            el.dataset.field = pos.field;
            el.textContent = pos.textContent || 'Label';
            document.querySelector('.id-card').appendChild(el);
            makeDraggable(el); // Make it draggable
        }
    
        if (el) {
            el.style.display = 'block';
            el.style.top = pos.top;
            el.style.left = pos.left;
            if (pos.fontSize) el.style.fontSize = pos.fontSize;
            if (pos.color) el.style.color = pos.color;
            if (pos.borderRadius) {
                el.style.borderRadius = pos.borderRadius;
                el.dataset.borderRadius = pos.borderRadius;
    
                if (pos.field === 'image') {
                    const radiusValue = pos.borderRadius.replace('px', '') || 0;
                    document.getElementById('imageBorderRadius').value = radiusValue;
                }
            }
        }
    });


        // Trigger input updates
        // document.querySelectorAll('input').forEach(input => input.dispatchEvent(new Event('input')));
        document.querySelectorAll('input').forEach(input => {
    if(input.id !== 'fontSizeControl') {
        input.dispatchEvent(new Event('input'));
    }
});
    });
});
</script>

</body>
</html>