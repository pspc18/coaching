import sys
import os
import re
import json

try:
    import pymupdf
except ImportError:
    try:
        import fitz as pymupdf
    except ImportError:
        print(json.dumps({"focused": False, "pages": [], "error": "PyMuPDF not installed"}))
        sys.exit(0)

def find_focused_pages(pdf_path, query, output_dir):
    if not os.path.exists(pdf_path) or not query:
        return {"focused": False, "pages": [], "focused_pdf": pdf_path}

    doc = pymupdf.open(pdf_path)
    total_pages = len(doc)
    if total_pages <= 3:
        return {"focused": False, "pages": list(range(1, total_pages + 1)), "focused_pdf": pdf_path}

    # 1. Match range like 86-90, 86 to 90, 86 - 90, 108-110
    m_range = re.search(r'(\d+)\s*[-–to]+\s*(\d+)', query)
    # 2. Match single question like 85, Q85, question 85, प्रश्न 85
    m_single = re.search(r'(?:question|q\.?|प्रश्न)\s*(\d+)', query, re.IGNORECASE)
    if not m_single:
        m_standalone = re.search(r'\b(\d{1,3})\b', query)
        if m_standalone and not m_range:
            m_single = m_standalone

    q_start, q_end = None, None
    if m_range:
        q_start, q_end = int(m_range.group(1)), int(m_range.group(2))
    elif m_single:
        q_start = int(m_single.group(1))
        q_end = q_start
    else:
        return {"focused": False, "pages": [], "focused_pdf": pdf_path}

    matched_0indexed = set()

    for i in range(total_pages):
        page = doc[i]
        txt = page.get_text()

        # Check for direction e.g. Direction (86-90)
        has_dir = False
        if q_start != q_end:
            for pat in [f'{q_start}-{q_end}', f'{q_start} - {q_end}', f'{q_start} to {q_end}', f'({q_start}-{q_end})']:
                if pat in txt:
                    has_dir = True
                    break

        # Check if actual question statement appears on this page (e.g. Q108., Q109., 108.)
        has_q = any(
            re.search(rf'(?:Q\.?|प्रश्न\s*|\b){qn}[\.\s\):]', txt, re.IGNORECASE)
            for qn in range(q_start, q_end + 1)
        )

        # A page is only a pure answer-key page if it has 'Ans.(' AND DOES NOT have the question statement with options (a), (b), (c)
        has_options = bool(re.search(r'\(a\)\s+.*\n\s*\(b\)', txt))
        is_pure_answer_key = ('Ans.(' in txt or 'S' + str(q_start) + '. Ans.' in txt) and not has_options

        if (has_dir or has_q) and not is_pure_answer_key:
            matched_0indexed.add(i)

    if not matched_0indexed:
        return {"focused": False, "pages": [], "focused_pdf": pdf_path}

    sorted_pages = sorted(list(matched_0indexed))
    os.makedirs(output_dir, exist_ok=True)
    out_filename = f"focus_{os.path.basename(pdf_path).replace('.pdf', '')}_{q_start}_{q_end}.pdf"
    out_path = os.path.join(output_dir, out_filename)

    new_doc = pymupdf.open()
    for p in sorted_pages:
        new_doc.insert_pdf(doc, from_page=p, to_page=p)
    new_doc.save(out_path)
    new_doc.close()
    doc.close()

    return {
        "focused": True,
        "pages": [p + 1 for p in sorted_pages],
        "focused_pdf": out_path
    }

if __name__ == '__main__':
    if len(sys.argv) < 4:
        print(json.dumps({"focused": False, "pages": []}))
        sys.exit(0)

    pdf_file = sys.argv[1]
    user_query = sys.argv[2]
    out_dir = sys.argv[3]

    try:
        res = find_focused_pages(pdf_file, user_query, out_dir)
        print(json.dumps(res))
    except Exception as e:
        print(json.dumps({"focused": False, "pages": [], "error": str(e)}))
