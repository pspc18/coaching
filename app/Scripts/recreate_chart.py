import sys
import os
import json
import matplotlib.pyplot as plt
import numpy as np
import matplotlib.font_manager as fm

# Detect Devanagari / Hindi font on the system
hindi_font = None
for f in fm.fontManager.ttflist:
    name = f.name.lower()
    if any(k in name for k in ['nirmala', 'mangal', 'aparajita', 'kokila', 'gautami', 'latha', 'arial unicode ms']):
        hindi_font = f.name
        break

if hindi_font:
    plt.rcParams['font.family'] = hindi_font
else:
    # Fallback font
    plt.rcParams['font.sans-serif'] = ['Nirmala UI', 'Segoe UI', 'DejaVu Sans', 'Arial']

def render_chart_from_spec(spec, output_path):
    chart_type = spec.get("chart_type", "bar").lower()
    title = spec.get("title", "")
    x_label = spec.get("x_label", "")
    y_label = spec.get("y_label", "")
    categories = spec.get("categories", [])
    series = spec.get("series", [])
    
    fig, ax = plt.subplots(figsize=(8, 4.5), dpi=150)
    
    if chart_type in ["grouped_bar", "bar"]:
        x = np.arange(len(categories))
        num_series = len(series)
        width = 0.8 / max(num_series, 1)
        
        hatches = ['++', '///', '\\\\\\', 'xx', '..', '**', 'OO']
        colors = ['#ffffff', '#f8fafc', '#f1f5f9', '#e2e8f0']
        
        for i, s in enumerate(series):
            vals = s.get("values", [])
            label = s.get("name", f"Series {i+1}")
            hatch = s.get("hatch", hatches[i % len(hatches)])
            offset = (i - (num_series - 1) / 2) * width
            ax.bar(x + offset, vals, width, label=label,
                   color='white', edgecolor='black', hatch=hatch, linewidth=1.2)
            
        ax.set_xticks(x)
        ax.set_xticklabels(categories, fontsize=11)
        
        # Grid lines
        ax.yaxis.grid(True, linestyle='-', color='black', alpha=0.5, linewidth=0.8)
        ax.set_axisbelow(True)
        
        if y_label:
            ax.set_ylabel(y_label, fontsize=11, fontweight='bold', labelpad=8)
        if x_label:
            ax.set_xlabel(x_label, fontsize=11, fontweight='bold', labelpad=8)
            
        # Spines
        for spine in ax.spines.values():
            spine.set_linewidth(1.2)
            spine.set_color('black')
            
        if num_series > 1:
            ax.legend(loc='upper center', bbox_to_anchor=(0.5, 1.15),
                      ncol=min(num_series, 4), frameon=False, fontsize=11)
                      
    elif chart_type == "line":
        markers = ['o', 's', '^', 'D', 'v']
        linestyles = ['-', '--', '-.', ':']
        for i, s in enumerate(series):
            vals = s.get("values", [])
            label = s.get("name", f"Series {i+1}")
            m = markers[i % len(markers)]
            ls = linestyles[i % len(linestyles)]
            ax.plot(categories, vals, label=label, marker=m, linestyle=ls,
                    color='black', linewidth=1.8, markersize=6)
                    
        ax.grid(True, linestyle='--', color='gray', alpha=0.5)
        if y_label: ax.set_ylabel(y_label, fontsize=11, fontweight='bold')
        if x_label: ax.set_xlabel(x_label, fontsize=11, fontweight='bold')
        if len(series) > 1:
            ax.legend(loc='best', fontsize=10)
            
    elif chart_type == "pie":
        vals = [s.get("value", 0) for s in series] if series else spec.get("values", [])
        labels = [s.get("name", "") for s in series] if series else categories
        
        ax.clear()
        wedges, texts, autotexts = ax.pie(
            vals, labels=labels, autopct='%1.1f%%',
            startangle=90, textprops=dict(color="black", fontsize=10),
            wedgeprops=dict(edgecolor='black', linewidth=1.2, fill=False)
        )
        for w, hatch in zip(wedges, ['++', '///', 'xx', '..', '\\\\\\', '**']):
            w.set_hatch(hatch)
            
    if title:
        plt.title(title, fontsize=12, fontweight='bold', pad=12)
        
    plt.tight_layout()
    plt.savefig(output_path, dpi=200, facecolor='white', bbox_inches='tight')
    plt.close()
    return True

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print(json.dumps({"error": "Usage: python recreate_chart.py <spec_json_path_or_string> <output_png_path>"}))
        sys.exit(1)
        
    spec_input = sys.argv[1]
    out_path = sys.argv[2]
    
    if os.path.exists(spec_input):
        with open(spec_input, 'r', encoding='utf-8') as f:
            spec = json.load(f)
    else:
        spec = json.loads(spec_input)
        
    render_chart_from_spec(spec, out_path)
    print(json.dumps({"success": True, "output_path": out_path}))
