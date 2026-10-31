import sys
import os
import json
import time

try:
    import pymupdf
except ImportError:
    try:
        import fitz as pymupdf
    except ImportError:
        print(json.dumps([]))
        sys.exit(0)

def extract_diagrams(pdf_path, output_dir):
    if not os.path.exists(pdf_path):
        return []
        
    os.makedirs(output_dir, exist_ok=True)
    doc = pymupdf.open(pdf_path)
    extracted = []
    ts = int(time.time())

    for pno in range(len(doc)):
        page = doc[pno]
        page_num = pno + 1
        page_rect = page.rect
        has_raster = False
        
        # 1. Check embedded raster images
        try:
            imgs = page.get_images(full=True)
            for idx, img_info in enumerate(imgs):
                xref = img_info[0]
                base_img = doc.extract_image(xref)
                if not base_img:
                    continue
                w = base_img.get('width', 0)
                h = base_img.get('height', 0)
                
                # Filter out tiny logos/icons (less than 80x50) or watermark lines
                if w < 75 or h < 50:
                    continue
                
                ext = base_img.get('ext', 'png')
                filename = f"pdf_diag_{ts}_p{page_num}_{idx}.{ext}"
                dest_file = os.path.join(output_dir, filename)
                with open(dest_file, 'wb') as f:
                    f.write(base_img['image'])
                
                extracted.append({
                    'page': page_num,
                    'image_path': 'uploads/questions/' + filename,
                    'filename': filename,
                    'width': w,
                    'height': h,
                    'type': 'raster'
                })
                has_raster = True
        except Exception:
            pass
            
        # 2. Check vector drawings (like pie charts, geometry figures, graphs drawn with curves)
        try:
            drawings = page.get_drawings()
            if drawings:
                chart_rects = []
                for d in drawings:
                    r = d.get('rect')
                    if r and r.width > 60 and r.height > 60:
                        chart_rects.append(r)
                
                if chart_rects:
                    big_rect = chart_rects[0]
                    for r in chart_rects[1:]:
                        big_rect |= r
                    
                    pad = 12
                    chart_crop = pymupdf.Rect(
                        max(0, big_rect.x0 - pad),
                        max(0, big_rect.y0 - pad),
                        min(page_rect.width, big_rect.x1 + pad),
                        min(page_rect.height, big_rect.y1 + pad)
                    )
                    
                    # Only render if it covers a sensible area and not the entire full page text
                    if chart_crop.width > 80 and chart_crop.height > 80:
                        filename = f"pdf_vector_chart_{ts}_p{page_num}.png"
                        dest_file = os.path.join(output_dir, filename)
                        
                        pix = page.get_pixmap(clip=chart_crop, dpi=200)
                        pix.save(dest_file)
                        
                        extracted.append({
                            'page': page_num,
                            'image_path': 'uploads/questions/' + filename,
                            'filename': filename,
                            'width': int(chart_crop.width),
                            'height': int(chart_crop.height),
                            'type': 'vector'
                        })
        except Exception:
            pass

    return extracted

if __name__ == '__main__':
    if len(sys.argv) < 3:
        print(json.dumps([]))
        sys.exit(0)

    pdf_file = sys.argv[1]
    out_dir = sys.argv[2]
    try:
        results = extract_diagrams(pdf_file, out_dir)
        print(json.dumps(results))
    except Exception as e:
        print(json.dumps([]))
