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

    q_pages_0idx = set()
    sol_pages_0idx = set()
    dir_pattern = re.compile(rf'(?:Direction|Directions|निर्देश)\s*[\(:\s]*{q_start}\s*[-–to]+\s*{q_end}', re.IGNORECASE)

    for i in range(total_pages):
        page = doc[i]
        txt = page.get_text()

        has_dir = bool(dir_pattern.search(txt))
        has_q = any(
            bool(re.search(rf'(?:Q\.?|प्रश्न\s*)\s*{qn}[\.\s\):]', txt, re.IGNORECASE))
            for qn in range(q_start, q_end + 1)
        )
        has_options = bool(re.search(r'\(a\)\s+.*\n\s*\(b\)', txt))

        # Avoid pure solution page without options for question paper section
        is_pure_sol = ('Ans.(' in txt or 'Solutions' in txt) and not has_options

        if (has_dir or has_q) and not is_pure_sol:
            q_pages_0idx.add(i)

        # Look for corresponding solution/explanation page
        has_sol = bool(re.search(rf'(?:Sol\.?|S)\s*[\(\[]?{q_start}', txt, re.IGNORECASE)) or any(
            f'S{qn}. Ans.' in txt or f'S{qn}.' in txt for qn in range(q_start, q_end + 1)
        )
        # Solutions are typically in the second half of the book (page index >= 15)
        if has_sol and i >= min(15, total_pages // 2):
            sol_pages_0idx.add(i)

    if not q_pages_0idx:
        return {"focused": False, "pages": [], "focused_pdf": pdf_path}

    # Combine question pages and corresponding solution pages (cap solutions to 2 pages max)
    combined_0idx = sorted(list(q_pages_0idx)) + sorted(list(sol_pages_0idx))[:2]
    # Remove duplicates preserving order
    final_0idx = []
    for p in combined_0idx:
        if p not in final_0idx:
            final_0idx.append(p)

    os.makedirs(output_dir, exist_ok=True)
    out_filename = f"focus_{os.path.basename(pdf_path).replace('.pdf', '')}_{q_start}_{q_end}.pdf"
    out_path = os.path.join(output_dir, out_filename)

    new_doc = pymupdf.open()
    for p in final_0idx:
        new_doc.insert_pdf(doc, from_page=p, to_page=p)
    new_doc.save(out_path)
    new_doc.close()
    doc.close()

    return {
        "focused": True,
        "question_pages": [p + 1 for p in sorted(list(q_pages_0idx))],
        "solution_pages": [p + 1 for p in sorted(list(sol_pages_0idx))[:2]],
        "pages": [p + 1 for p in final_0idx],
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
