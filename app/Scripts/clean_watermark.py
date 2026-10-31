import sys
import os
import json
from PIL import Image, ImageEnhance, ImageFilter
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
        
        arr = np.array(orig, dtype=np.float32)
        r, g, b = arr[:, :, 0], arr[:, :, 1], arr[:, :, 2]
        
        # 1. Perceived Luminance (brightness 0 - 255)
        lum = 0.299 * r + 0.587 * g + 0.114 * b
        
        # 2. Color Variance / Saturation (Max channel - Min channel)
        max_c = np.maximum(np.maximum(r, g), b)
        min_c = np.minimum(np.minimum(r, g), b)
        color_diff = max_c - min_c
        
        # 3. Watermark Detection:
        # Faint colored watermark: color_diff >= 14 and luminance >= 90
        # Faint gray watermark: color_diff < 14, but luminance between 195 and 245
        colored_wm_mask = (color_diff >= 14) & (lum >= 90)
        faint_gray_mask = (color_diff < 14) & (lum >= 200) & (lum < 250)
        
        colored_pixel_count = int(np.sum(colored_wm_mask))
        total_pixels = w * h
        colored_ratio = colored_pixel_count / total_pixels
        
        has_watermark = (colored_ratio > 0.005) or (colored_pixel_count > 300)
        
        # 4. Multi-pass Cleaning
        # Create output array initialized with pure white (255, 255, 255)
        clean_arr = np.ones((h, w, 3), dtype=np.uint8) * 255
        
        # Mask for genuine dark content (axes, text, grid lines, hatched bars)
        # Content is dark (lum < 150) AND not a pure colored watermark stroke
        is_dark_content = (lum < 150) & ~((color_diff > 25) & (lum > 115))
        
        # Very dark lines (grid, text, hatched borders, lum < 100) are always preserved
        is_solid_black = lum < 105
        
        # Intermediate grays that have virtually no color (real grayscale shading, not watermark)
        is_gray_content = (lum >= 105) & (lum < 185) & (color_diff < 12)
        
        preserve_mask = is_solid_black | is_dark_content | is_gray_content
        
        # For preserved content:
        # Boost contrast slightly so text & grid lines look crisp and dark
        clean_arr[preserve_mask] = np.clip(arr[preserve_mask] * 0.88, 0, 255).astype(np.uint8)
        
        # Check for legend box colored icons (like small brownish legend square)
        # If a region is small and dark colored (lum < 110, color_diff > 12), keep original color
        is_legend_color = (lum < 110) & (color_diff > 12)
        clean_arr[is_legend_color] = arr[is_legend_color].astype(np.uint8)
        
        clean_img = Image.fromarray(clean_arr)
        
        # Apply gentle sharpness enhancement
        enhancer = ImageEnhance.Sharpness(clean_img)
        clean_img = enhancer.enhance(1.2)
        
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
