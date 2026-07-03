import json
import time
from deep_translator import GoogleTranslator

# Load the file
with open('lang/ar.json', 'r', encoding='utf-8') as f:
    data = json.load(f)

translator = GoogleTranslator(source='auto', target='ar')

translated_data = {}
keys = list(data.keys())
total = len(keys)

print(f"Starting translation of {total} strings...")

batch_size = 50
for i in range(0, total, batch_size):
    batch_keys = keys[i:i+batch_size]
    # We translate the keys themselves since values are identical initially
    try:
        translated_batch = translator.translate_batch(batch_keys)
        for j, key in enumerate(batch_keys):
            translated_data[key] = translated_batch[j]
        print(f"Translated {i + len(batch_keys)} / {total}")
        time.sleep(1) # Be nice to the API
    except Exception as e:
        print(f"Error at batch {i}: {e}")
        # fallback to single translations
        for key in batch_keys:
            try:
                translated_data[key] = translator.translate(key)
            except:
                translated_data[key] = key
        
# Fix specific strings like variables
for k, v in translated_data.items():
    if k == "pts":
        translated_data[k] = "نقطة"
    if k == "tasks":
        translated_data[k] = "مهام"
    if k == "followers":
        translated_data[k] = "متابعون"
        
with open('lang/ar.json', 'w', encoding='utf-8') as f:
    json.dump(translated_data, f, ensure_ascii=False, indent=4)

print("Translation complete!")
