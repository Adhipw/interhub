import urllib.request
import os

def generate_image(mmd_file, out_file):
    if not os.path.exists(mmd_file):
        print(f"Skipping {mmd_file}, does not exist.")
        return

    with open(mmd_file, 'r', encoding='utf-8') as f:
        diagram = f.read()

    url = 'https://kroki.io/mermaid/svg'
    data = diagram.encode('utf-8')
    headers = {
        'Content-Type': 'text/plain',
        'Accept': 'image/svg+xml',
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
    }

    req = urllib.request.Request(url, data=data, headers=headers)
    try:
        with urllib.request.urlopen(req) as response:
            with open(out_file, 'wb') as out:
                out.write(response.read())
        print(f"Success: {out_file}")
    except Exception as e:
        print(f"Error on {mmd_file}: {e}")

generate_image('docs/diagram1.mmd', 'docs/diagram1.svg')
generate_image('docs/diagram2.mmd', 'docs/diagram2.svg')
generate_image('docs/diagram3.mmd', 'docs/diagram3.svg')
generate_image('docs/diagram3_recruitment.mmd', 'docs/diagram3_recruitment.svg')
