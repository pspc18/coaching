import sys
import os
import json
from PIL import Image
import numpy as np

def clean_watermark(image_path, output_path=None):
    if not os.path.exists(image_path):
        return {"success": False, "error": f"File not found: {image_path}"}
    
    if output_path is None:
        base, ext = os.path.splitext(image_path)
        output_path = f"{base}_clean.png"

    try:
        orig = Image.open(image_path).convert("RGB")
        w, h = orig.size
        
        arr = np.array(orig, dtype=np.uint8)
        arr_f = arr.astype(np.float32)
        r, g, b = arr_f[:, :, 0], arr_f[:, :, 1], arr_f[:, :, 2]
        
        # 1. Perceived Luminance (brightness 0 - 255)
        lum = 0.299 * r + 0.587 * g + 0.114 * b
        
        # 2. Color Variance / Saturation (Max channel - Min channel)
        max_c = np.maximum(np.maximum(r, g), b)
        min_c = np.minimum(np.minimum(r, g), b)
        color_diff = max_c - min_c
        
        # 3. Watermark Detection:
        # Faint colored watermark: High luminance (>= 170) AND significant color difference (>= 12)
        # Typically pink, reddish, light blue, or pastel coaching institute watermark stamps
        faint_colored_wm = (lum >= 170) & (color_diff >= 12)
        
        colored_pixel_count = int(np.sum(faint_colored_wm))
        total_pixels = w * h
        colored_ratio = colored_pixel_count / total_pixels
        
        # Watermark detected if significant cluster of faint colored pixels exist
        has_watermark = (colored_ratio > 0.003) or (colored_pixel_count > 250)
        
        # 4. Non-Destructive Cleaning
        # CRITICAL: Start with the authentic original image array so NO diagram geometry,
        # grids, gray shades, borders, or text are ever erased or distorted!
        clean_arr = arr.copy()
        
        if has_watermark:
            # Mask pixels to clean:
            # - Must be faint colored watermark (lum >= 170 and color_diff >= 12)
            # - Must NOT be dark lines/text/axes (lum < 160 is strictly protected)
            # - Must NOT be neutral gray shading, table borders, or hatched bars (color_diff < 10 is strictly protected)
            to_clean = faint_colored_wm & (lum >= 165)
            clean_arr[to_clean] = [255, 255, 255]
            
        clean_img = Image.fromarray(clean_arr)
        clean_img.save(output_path, "PNG", quality=95)
        
        return {
            "success": True,
            "has_watermark": bool(has_watermark),
            "colored_pixels": colored_pixel_count,
            "colored_ratio": round(colored_ratio, 4),
            "output_path": output_path,
            "width": w,
            "height": h
        }
    except Exception as e:
        return {"success": False, "error": str(e)}

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Usage: python clean_watermark.py <input_image> [output_image]"}))
        sys.exit(1)
        
    in_img = sys.argv[1]
    out_img = sys.argv[2] if len(sys.argv) > 2 else None
    res = clean_watermark(in_img, out_img)
    print(json.dumps(res))
