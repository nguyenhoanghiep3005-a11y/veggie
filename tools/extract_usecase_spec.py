import os
import re
import unicodedata
import zipfile
import xml.etree.ElementTree as ET
from pathlib import Path

from PIL import Image, ImageDraw, ImageFont


DOWNLOADS = Path(r"C:\Users\nguye\Downloads")
OUTPUT = Path(r"C:\tmp\usecase_spec")
W_NS = "http://schemas.openxmlformats.org/wordprocessingml/2006/main"
NS = {"w": W_NS}


def normalize(value):
    value = unicodedata.normalize("NFD", value)
    value = "".join(ch for ch in value if unicodedata.category(ch) != "Mn")
    return re.sub(r"\s+", " ", value).strip().lower()


def find_source():
    candidates = []
    for path in DOWNLOADS.glob("*.docx"):
        name = normalize(path.name)
        if "do danh manh" in name and "final" in name:
            candidates.append(path)
    if not candidates:
        raise FileNotFoundError("Không tìm thấy file luận văn mẫu.")
    return sorted(candidates, key=lambda p: p.stat().st_mtime, reverse=True)[0]


def extract_text(node):
    parts = []
    for element in node.iter():
        if element.tag == f"{{{W_NS}}}t" and element.text:
            parts.append(element.text)
        elif element.tag == f"{{{W_NS}}}tab":
            parts.append("\t")
        elif element.tag == f"{{{W_NS}}}br":
            parts.append("\n")
    return re.sub(r"[ \t]+", " ", "".join(parts)).strip()


def extract_tables(source):
    with zipfile.ZipFile(source) as archive:
        root = ET.fromstring(archive.read("word/document.xml"))
    tables = []
    for table_index, table in enumerate(root.findall(".//w:tbl", NS), start=1):
        rows = []
        for row in table.findall("./w:tr", NS):
            cells = []
            for cell in row.findall("./w:tc", NS):
                paragraphs = []
                for paragraph in cell.findall(".//w:p", NS):
                    text = extract_text(paragraph)
                    if text:
                        paragraphs.append(text)
                cells.append("\n".join(paragraphs))
            rows.append(cells)
        tables.append((table_index, rows))
    return tables


KEYWORDS = (
    "use case",
    "dac ta",
    "luong chinh",
    "luong thay the",
    "luong ngoai le",
    "tac nhan",
    "dieu kien truoc",
    "dieu kien sau",
    "kich ban",
    "mo ta",
)


def select_candidates(tables):
    candidates = []
    for index, rows in tables:
        text = " ".join(cell for row in rows for cell in row)
        normalized = normalize(text)
        score = sum(1 for keyword in KEYWORDS if keyword in normalized)
        if score >= 2 or ("use case" in normalized and score >= 1):
            candidates.append((index, rows, score))
    return candidates


def load_font(size, bold=False):
    font_name = "arialbd.ttf" if bold else "arial.ttf"
    return ImageFont.truetype(str(Path(r"C:\Windows\Fonts") / font_name), size)


def wrapped_lines(draw, text, font, width):
    lines = []
    for raw_line in (text or "").splitlines() or [""]:
        words = raw_line.split()
        if not words:
            lines.append("")
            continue
        current = words[0]
        for word in words[1:]:
            trial = current + " " + word
            if draw.textbbox((0, 0), trial, font=font)[2] <= width:
                current = trial
            else:
                lines.append(current)
                current = word
        lines.append(current)
    return lines


def render_index(candidates):
    OUTPUT.mkdir(parents=True, exist_ok=True)
    page_width = 1800
    margin = 70
    title_font = load_font(38, bold=True)
    body_font = load_font(25)
    header_font = load_font(27, bold=True)
    per_page = 6
    for page_no in range(0, len(candidates), per_page):
        chunk = candidates[page_no : page_no + per_page]
        canvas = Image.new("RGB", (page_width, 3200), "white")
        draw = ImageDraw.Draw(canvas)
        y = margin
        draw.text((margin, y), "Các bảng đặc tả use case tìm thấy", fill="black", font=title_font)
        y += 75
        for index, rows, score in chunk:
            flattened = " | ".join(cell.replace("\n", " / ") for row in rows for cell in row)
            preview = flattened[:800]
            draw.rounded_rectangle(
                (margin, y, page_width - margin, y + 430),
                radius=12,
                outline="#444444",
                width=2,
                fill="#fafafa",
            )
            draw.text(
                (margin + 25, y + 20),
                f"Bảng {index} | {len(rows)} dòng | điểm nhận diện {score}",
                fill="black",
                font=header_font,
            )
            line_y = y + 70
            for line in wrapped_lines(draw, preview, body_font, page_width - 2 * margin - 50)[:12]:
                draw.text((margin + 25, line_y), line, fill="#202020", font=body_font)
                line_y += 31
            y += 465
        crop = canvas.crop((0, 0, page_width, min(y + margin, canvas.height)))
        crop.save(OUTPUT / f"index_{page_no // per_page + 1}.png")


def render_table(index, rows):
    page_width = 1900
    margin = 70
    title_font = load_font(38, bold=True)
    row_font = load_font(25, bold=True)
    cell_font = load_font(25)
    pages = []
    canvas = Image.new("RGB", (page_width, 3600), "white")
    draw = ImageDraw.Draw(canvas)
    y = margin
    page_no = 1

    def flush():
        nonlocal canvas, draw, y, page_no
        crop = canvas.crop((0, 0, page_width, min(y + margin, canvas.height)))
        crop.save(OUTPUT / f"table_{index:03d}_p{page_no}.png")
        pages.append(page_no)
        page_no += 1
        canvas = Image.new("RGB", (page_width, 3600), "white")
        draw = ImageDraw.Draw(canvas)
        y = margin

    draw.text((margin, y), f"Bảng {index}", fill="black", font=title_font)
    y += 75
    for row_no, row in enumerate(rows, start=1):
        cells = [cell for cell in row if cell.strip()]
        if not cells:
            continue
        estimated = 65
        for cell in cells:
            estimated += 32 * max(1, len(wrapped_lines(draw, cell, cell_font, page_width - 2 * margin - 120)))
        if y + estimated > 3500:
            flush()
            draw.text((margin, y), f"Bảng {index} (tiếp)", fill="black", font=title_font)
            y += 75
        draw.line((margin, y, page_width - margin, y), fill="#777777", width=2)
        y += 15
        draw.text((margin, y), f"Dòng {row_no}", fill="#333333", font=row_font)
        y += 42
        for cell_no, cell in enumerate(cells, start=1):
            draw.text((margin + 25, y), f"Ô {cell_no}:", fill="#333333", font=row_font)
            text_x = margin + 120
            for line in wrapped_lines(draw, cell, cell_font, page_width - text_x - margin):
                draw.text((text_x, y), line, fill="black", font=cell_font)
                y += 32
            y += 12
        y += 12
    flush()


def write_text_summary(source, candidates):
    lines = [f"Source: {source}", f"Candidate tables: {len(candidates)}", ""]
    for index, rows, score in candidates:
        lines.append(f"=== TABLE {index} | score={score} ===")
        for row_no, row in enumerate(rows, start=1):
            lines.append(f"ROW {row_no}: " + " || ".join(cell.replace("\n", " / ") for cell in row))
        lines.append("")
    (OUTPUT / "candidates.txt").write_text("\n".join(lines), encoding="utf-8")


def main():
    source = find_source()
    tables = extract_tables(source)
    candidates = select_candidates(tables)
    if not candidates:
        raise RuntimeError("Không tìm thấy bảng đặc tả use case phù hợp.")
    write_text_summary(source, candidates)
    render_index(candidates)
    for index, rows, _ in candidates:
        render_table(index, rows)


if __name__ == "__main__":
    main()
