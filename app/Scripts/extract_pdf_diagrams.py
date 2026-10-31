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

def filter_contained_boxes(boxes):
    """Keep only parent/outermost bounding boxes when one is inside another."""
    filtered = []
    # Sort by area descending so largest boxes come first
    boxes = sorted(boxes, key=lambda b: (b[2]-b[0]) * (b[3]-b[1]), reverse=True)
    for b in boxes:
        is_inside = False
        for parent in filtered:
            if b[0] >= parent[0] - 12 and b[1] >= parent[1] - 12 and b[2] <= parent[2] + 12 and b[3] <= parent[3] + 12:
                is_inside = True
                break
        if not is_inside:
            filtered.append(b)
    return filtered

def extract_diagrams(pdf_path, output_dir):
    if not os.path.exists(pdf_path):
        return []
        
    os.makedirs(output_dir, exist_ok=True)
    doc = pymupdf.open(pdf_path)
    extracted = []
    ts = int(time.time())

    # Step 1: Detect template / repeating images (e.g. Adda247 header logo, Google Play ad, watermark)
    xref_page_counts = {}
    for page in doc:
        for img in page.get_images():
            xref = img[0]
            xref_page_counts[xref] = xref_page_counts.get(xref, 0) + 1

    # Any image repeating on more than 2 pages is a template logo/ad/watermark, NOT a question diagram
    repeating_xrefs = {xref for xref, count in xref_page_counts.items() if count > 2}

    # Step 2: Extract real question diagrams and vector charts
    for pno in range(len(doc)):
        page = doc[pno]
        page_num = pno + 1
        page_rect = page.rect

        # 2A. Legitimate embedded raster images (isolated figures/photos)
        try:
            imgs = page.get_images(full=True)
            for idx, img_info in enumerate(imgs):
                xref = img_info[0]
                if xref in repeating_xrefs:
                    continue

                rects = page.get_image_rects(xref)
                if not rects:
                    continue
                r = rects[0]

                # Filter out header (y <= 95) or footer (y >= height - 75)
                if r.y1 <= 95 or r.y0 >= page_rect.height - 75:
                    continue
                if r.width < 50 or r.height < 40:
                    continue

                base_img = doc.extract_image(xref)
                if not base_img:
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
                    'width': base_img.get('width', int(r.width)),
                    'height': base_img.get('height', int(r.height)),
                    'type': 'raster',
                    'y0': r.y0
                })
        except Exception:
            pass

        # 2B. Vector drawings / charts / graphs (e.g. Bar graphs, line charts, pie charts, geometry figures)
        try:
            drawings = page.get_drawings()
            chart_boxes = []

            for d in drawings:
                r = d.get('rect')
                if not r:
                    continue
                # Skip header/footer area and full-page rectangles
                if r.y1 <= 95 or r.y0 >= page_rect.height - 75:
                    continue
                if r.width > page_rect.width * 0.92 or r.height > page_rect.height * 0.92:
                    continue
                
                # Check for chart container or significant diagram element
                if r.width >= 100 and r.height >= 70:
                    chart_boxes.append((r.x0, r.y0, r.x1, r.y1))

            if chart_boxes:
                unique_boxes = filter_contained_boxes(chart_boxes)

                for c_idx, u in enumerate(unique_boxes):
                    pad = 6
                    crop_rect = pymupdf.Rect(
                        max(0, u[0] - pad),
                        max(0, u[1] - pad),
                        min(page_rect.width, u[2] + pad),
                        min(page_rect.height, u[3] + pad)
                    )

                    filename = f"pdf_vector_chart_{ts}_p{page_num}_{c_idx}.png"
                    dest_file = os.path.join(output_dir, filename)

                    pix = page.get_pixmap(clip=crop_rect, dpi=200)
                    pix.save(dest_file)

                    extracted.append({
                        'page': page_num,
                        'image_path': 'uploads/questions/' + filename,
                        'filename': filename,
                        'width': pix.width,
                        'height': pix.height,
                        'type': 'vector',
                        'y0': u[1]
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
