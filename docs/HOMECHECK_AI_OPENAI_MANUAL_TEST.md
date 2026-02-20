# HomeCheck AI – OpenAI API Payload & Manual Test

The text you see (“360° images captured successfully. Moisture levels within normal range…”) is the **fallback** analysis (used when OpenAI is not configured or the API call fails). To use the **real AI**, you need valid API keys and an Assistant ID.

---

## 1. Required configuration

In your `.env`:

```env
OPENAI_API_KEY=sk-proj-...your-key...
OPENAI_ASSISTANT_ID=asst_...your-assistant-id...
```

- **OPENAI_API_KEY**: From [OpenAI API keys](https://platform.openai.com/api-keys).
- **OPENAI_ASSISTANT_ID**: Create an Assistant in [OpenAI Assistants](https://platform.openai.com/assistants) (no need to add extra instructions; the app sends the full prompt in the thread). Copy the Assistant ID (starts with `asst_`).

---

## 2. Payload sent to the AI (user message)

The app sends **one user message** to the Assistant. It contains this **exact prompt** (with the dynamic `payload` JSON at the end):

### Prompt (fixed part)

```
You are the Abodeology HomeCheck AI assistant. You will receive structured JSON data for a property and its rooms. For each room, analyse condition, moisture risk, and presentation quality based ONLY on the data provided. Respond with a single JSON object with this exact structure:

{
  "overall_rating": number (1-10),
  "summary": string,
  "rooms": {
    "Room Name": {
      "rating": number (1-10),
      "comments": string,
      "moisture": number|null,
      "issues": [string, ...]
    },
    ...
  },
  "recommendations": [string, ...],
  "issues_found": [string, ...]
}

JSON ONLY, no markdown or extra text.

Here is the data to analyse:
```

### Payload (dynamic part – appended to the prompt)

The app appends a single JSON object. Structure:

```json
{
  "property": {
    "id": 1,
    "address": "123 Example Street",
    "postcode": "SW1A 1AA",
    "property_type": "terraced",
    "bedrooms": 3,
    "tenure": "freehold"
  },
  "rooms": {
    "Kitchen": {
      "images_count": 2,
      "images_360": 0,
      "images_regular": 2,
      "moisture_readings": [45, 48],
      "existing_ai_rating": null,
      "existing_ai_comments": null
    },
    "Living Room": {
      "images_count": 1,
      "images_360": 1,
      "images_regular": 0,
      "moisture_readings": [],
      "existing_ai_rating": null,
      "existing_ai_comments": null
    }
  }
}
```

So the **full user message** = prompt text above + newline + `json_encode($payload)`.

---

## 3. API flow (run manually)

The app uses the **OpenAI Assistants API** (threads + runs + messages). You need a valid **API key** and **Assistant ID**.

### Step 1: Create a thread with the user message

Replace `YOUR_API_KEY` and put your full prompt+payload in the `content[0].text.value` (one string).

```bash
curl -s -X POST "https://api.openai.com/v1/threads" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "messages": [
      {
        "role": "user",
        "content": [
          {
            "type": "text",
            "text": "You are the Abodeology HomeCheck AI assistant. You will receive structured JSON data for a property and its rooms. For each room, analyse condition, moisture risk, and presentation quality based ONLY on the data provided. Respond with a single JSON object with this exact structure:\n\n{\n  \"overall_rating\": number (1-10),\n  \"summary\": string,\n  \"rooms\": {\n    \"Room Name\": {\n      \"rating\": number (1-10),\n      \"comments\": string,\n      \"moisture\": number|null,\n      \"issues\": [string, ...]\n    }\n  },\n  \"recommendations\": [string, ...],\n  \"issues_found\": [string, ...]\n}\n\nJSON ONLY, no markdown or extra text.\n\nHere is the data to analyse:\n{\"property\":{\"id\":1,\"address\":\"123 Example St\",\"postcode\":\"SW1A 1AA\",\"property_type\":\"terraced\",\"bedrooms\":3,\"tenure\":\"freehold\"},\"rooms\":{\"Kitchen\":{\"images_count\":2,\"images_360\":0,\"images_regular\":2,\"moisture_readings\":[45,48],\"existing_ai_rating\":null,\"existing_ai_comments\":null},\"Living Room\":{\"images_count\":1,\"images_360\":1,\"images_regular\":0,\"moisture_readings\":[],\"existing_ai_rating\":null,\"existing_ai_comments\":null}}}"
          }
        ]
      }
    ]
  }'
```

From the response, take `id` → this is your **thread_id**.

### Step 2: Run the assistant on the thread

```bash
curl -s -X POST "https://api.openai.com/v1/threads/THREAD_ID/runs" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"assistant_id": "YOUR_ASSISTANT_ID"}'
```

From the response, take `id` → this is your **run_id**.

### Step 3: Poll run status until completed

```bash
curl -s "https://api.openai.com/v1/threads/THREAD_ID/runs/RUN_ID" \
  -H "Authorization: Bearer YOUR_API_KEY"
```

Repeat until `"status": "completed"` (or `failed` / `cancelled`). Wait a few seconds between calls.

### Step 4: Get the assistant’s reply (AI comment payload)

```bash
curl -s "https://api.openai.com/v1/threads/THREAD_ID/messages?limit=10" \
  -H "Authorization: Bearer YOUR_API_KEY"
```

In `data[]`, find the message with `"role": "assistant"`. The text is in:

`data[0].content[0].text.value`

That value should be **raw JSON** (or JSON inside markdown code blocks). The app expects this shape:

```json
{
  "overall_rating": 8,
  "summary": "Property in good condition...",
  "rooms": {
    "Kitchen": {
      "rating": 8,
      "comments": "Kitchen in good order. Appliances present.",
      "moisture": 46,
      "issues": []
    },
    "Living Room": {
      "rating": 9,
      "comments": "Spacious, 360° capture completed.",
      "moisture": null,
      "issues": []
    }
  },
  "recommendations": ["Ready for market."],
  "issues_found": []
}
```

The **AI comment** you see in the app for each room/image comes from `rooms[Room Name].comments`.

---

## 4. Minimal sample payload (for copy-paste)

Use this as the **data to analyse** (the part after “Here is the data to analyse:” in the user message):

```json
{
  "property": {
    "id": 1,
    "address": "109 Eagle City Sargodha",
    "postcode": "40100",
    "property_type": "terraced",
    "bedrooms": 42,
    "tenure": "freehold"
  },
  "rooms": {
    "Kitchen": {
      "images_count": 2,
      "images_360": 0,
      "images_regular": 2,
      "moisture_readings": [45, 48],
      "existing_ai_rating": null,
      "existing_ai_comments": null
    },
    "Living Room": {
      "images_count": 1,
      "images_360": 1,
      "images_regular": 0,
      "moisture_readings": [],
      "existing_ai_rating": null,
      "existing_ai_comments": null
    }
  }
}
```

---

## 5. Quick check that keys are used

- If the UI shows **“✓ AI connected”** on the HomeCheck page, `OPENAI_API_KEY` and `OPENAI_ASSISTANT_ID` are set and the app will call the API (or fall back only on errors).
- If you see **“○ AI fallback”**, the app is using the built-in fallback (hence the generic “360° images captured successfully…” text). Set both env vars and restart the app, then run “Generate HomeCheck Report using AI” again.

Using the payload and steps above you can run the same flow manually with your API keys and confirm how the API and AI comments behave.
