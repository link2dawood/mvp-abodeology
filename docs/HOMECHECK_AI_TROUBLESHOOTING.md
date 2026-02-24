# HomeCheck AI – Why you see “Property is in good condition with minor wear and tear…”

That message is the **fallback** (simulated) analysis. It means the app **did not** use your OpenAI Assistant when you clicked “Process AI Analysis”. It only appears when:

1. **OpenAI is not configured** on the server (missing or empty `OPENAI_API_KEY` or `OPENAI_ASSISTANT_ID` in `.env`), or  
2. **The OpenAI call failed** (network, timeout, API error), or  
3. **The assistant’s response failed the sanity check** (e.g. missing or invalid `rooms` in the JSON).

---

## What to do

### 1. Check `.env` on the **same server** that serves the app

The server that runs Laravel (e.g. `id-dci-web1367` / `public_html`) must have:

```env
OPENAI_API_KEY=sk-proj-...your-full-key...
OPENAI_ASSISTANT_ID=asst_VRDghnrYNDINGS1diNGtHCZh
```

- No extra characters (no `"` or `>` at the end of the line).  
- No spaces around `=`.  
- Values in quotes if they contain special characters; no quotes is fine for these.

### 2. Clear config cache after changing `.env`

Laravel caches config. After editing `.env` on the server run:

```bash
cd /path/to/your/app   # e.g. public_html or the Laravel root
php artisan config:clear
```

If you use a deployment script, add `php artisan config:clear` (and optionally `php artisan cache:clear`) after deploy.

### 3. Check the Laravel log when you run “Process AI Analysis”

On the server, trigger “Process AI Analysis” once, then check the log:

```bash
tail -100 storage/logs/laravel.log
```

You will see one of:

- **`HomeCheck AI: Using OpenAI Assistant`** – Keys are set and the app **attempted** the API. If you still see the fallback, look for a following line:
  - **`HomeCheck OpenAI analysis failed. Falling back...`** – Note the `error` message (e.g. timeout, 401, invalid JSON).
  - **`HomeCheck OpenAI analysis returned unexpected structure...`** – The assistant’s JSON didn’t have a valid `rooms` object.
- **`HomeCheck AI: OpenAI not used (missing OPENAI_API_KEY or OPENAI_ASSISTANT_ID)`** – Keys are missing or empty in the environment the web app is using. Fix `.env` and run `php artisan config:clear`.

### 4. If the API is attempted but fails

- **401 / invalid API key** – Key in `.env` must match the one that works in your curl (no trailing `>`, no extra quote).  
- **Timeout** – Assistant may be slow; the app polls for up to ~30 seconds. Check OpenAI status page or try again.  
- **Unexpected structure** – Assistant must respond with **JSON** in the expected shape (see `docs/HOMECHECK_AI_OUTPUT_SOURCE.md`). The user message in the thread already asks for that; ensure the assistant doesn’t override it with plain text.

### 5. Quick test that config is loaded in the app

On the server:

```bash
php artisan tinker
```

Then:

```php
config('services.openai.api_key') ? 'API key is set' : 'API key is MISSING';
config('services.openai.assistant_id') ? 'Assistant ID is set' : 'Assistant ID is MISSING';
exit
```

If either says MISSING, fix `.env` and run `php artisan config:clear`, then try again.

---

## Summary

| You see in log | Meaning | Action |
|----------------|--------|--------|
| `OpenAI not used (missing...)` | Keys not loaded in web app | Set `OPENAI_API_KEY` and `OPENAI_ASSISTANT_ID` in `.env` on server, run `config:clear` |
| `Using OpenAI Assistant` then `analysis failed` | API was called but threw | Check `error` in log (key, network, timeout); fix key or environment |
| `unexpected structure` | Assistant reply wasn’t valid JSON / rooms | Ensure assistant returns the exact JSON structure the app expects |

Once the app uses the real API successfully, you’ll see your assistant’s summary and per-room text (e.g. moisture and recommendations), not the generic “Property is in good condition with minor wear and tear…”.
