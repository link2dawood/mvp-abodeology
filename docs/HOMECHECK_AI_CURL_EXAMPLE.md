# HomeCheck AI – Payload and cURL example

For HomeCheck **6** (`http://localhost:8005/admin/homechecks/6`), the app calls the **OpenAI Assistants API**. Below are the payload shape and cURL commands. Replace `YOUR_OPENAI_API_KEY` and `YOUR_ASSISTANT_ID` with your `.env` values (for HomeCheck 6, use the same assistant ID as in the app).

---

## 1. Payload structure (what gets sent)

The app builds a **JSON payload** from the property and HomeCheck data, then sends it as part of a **single user message** (instruction text + this JSON).

### Property + rooms payload (example for HomeCheck 6)

```json
{
  "property": {
    "id": 6,
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
    },
    "Bathroom": {
      "images_count": 2,
      "images_360": 0,
      "images_regular": 2,
      "moisture_readings": [52, 55],
      "existing_ai_rating": null,
      "existing_ai_comments": null
    }
  }
}
```

- **property**: from `properties` (id, address, postcode, property_type, bedrooms, tenure).
- **rooms**: one key per `room_name` in `homecheck_data` for this report; values are:
  - `images_count`: number of images in that room
  - `images_360`: count where `is_360 = true`
  - `images_regular`: count where `is_360 = false`
  - `moisture_readings`: array of `moisture_reading` values (non-null)
  - `existing_ai_rating` / `existing_ai_comments`: from the first image in that room (or null)

To get the **real** payload for HomeCheck 6, you can temporarily log it in `HomeCheckReportService::generateAIAnalysisWithOpenAI()` after `$payload = [...]` (e.g. `Log::info('HomeCheck payload', ['payload' => $payload])`) and trigger “Process AI Analysis” for that homecheck.

---

## 2. Full user message (prompt + payload)

The **content** of the single user message is this text (the app concatenates instruction + payload):

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
<JSON payload here>
```

---

## 3. cURL – Step 1: Create thread with user message

Creates a thread and posts the prompt + payload as one user message.

```bash
export OPENAI_API_KEY="YOUR_OPENAI_API_KEY"

# Escape the prompt + payload for JSON (use a file or single-line JSON for real use)
# Below: minimal single-line prompt; replace the "Here is the data..." part with your actual payload.
curl -s -X POST "https://api.openai.com/v1/threads" \
  -H "Authorization: Bearer $OPENAI_API_KEY" \
  -H "Content-Type: application/json" \
  -H "OpenAI-Beta: assistants=v2" \
  -d '{
    "messages": [
      {
        "role": "user",
        "content": [
          {
            "type": "text",
            "text": "You are the Abodeology HomeCheck AI assistant. You will receive structured JSON data for a property and its rooms. For each room, analyse condition, moisture risk, and presentation quality based ONLY on the data provided. Respond with a single JSON object with this exact structure:\n\n{\n  \"overall_rating\": number (1-10),\n  \"summary\": string,\n  \"rooms\": {\n    \"Room Name\": {\n      \"rating\": number (1-10),\n      \"comments\": string,\n      \"moisture\": number|null,\n      \"issues\": [string, ...]\n    },\n    ...\n  },\n  \"recommendations\": [string, ...],\n  \"issues_found\": [string, ...]\n}\n\nJSON ONLY, no markdown or extra text.\n\nHere is the data to analyse:\n{\"property\":{\"id\":6,\"address\":\"123 Example Street\",\"postcode\":\"SW1A 1AA\",\"property_type\":\"terraced\",\"bedrooms\":3,\"tenure\":\"freehold\"},\"rooms\":{\"Kitchen\":{\"images_count\":2,\"images_360\":0,\"images_regular\":2,\"moisture_readings\":[45,48],\"existing_ai_rating\":null,\"existing_ai_comments\":null},\"Living Room\":{\"images_count\":1,\"images_360\":1,\"images_regular\":0,\"moisture_readings\":[],\"existing_ai_rating\":null,\"existing_ai_comments\":null},\"Bathroom\":{\"images_count\":2,\"images_360\":0,\"images_regular\":2,\"moisture_readings\":[52,55],\"existing_ai_rating\":null,\"existing_ai_comments\":null}}}"
          }
        ]
      }
    ]
  }'
```

Save the returned **`id`** as `THREAD_ID` (e.g. `thread_abc123`).

---

## 4. cURL – Step 2: Run the assistant on the thread

```bash
export OPENAI_API_KEY="YOUR_OPENAI_API_KEY"
export THREAD_ID="thread_xxxx"   # from step 1
export ASSISTANT_ID="YOUR_ASSISTANT_ID"   # same as OPENAI_ASSISTANT_ID in .env

curl -s -X POST "https://api.openai.com/v1/threads/$THREAD_ID/runs" \
  -H "Authorization: Bearer $OPENAI_API_KEY" \
  -H "Content-Type: application/json" \
  -H "OpenAI-Beta: assistants=v2" \
  -d "{
    \"assistant_id\": \"$ASSISTANT_ID\"
  }"
```

Save the returned **`id`** as `RUN_ID` (e.g. `run_xyz789`).

---

## 5. cURL – Step 3: Poll run status until completed

```bash
export OPENAI_API_KEY="YOUR_OPENAI_API_KEY"
export THREAD_ID="thread_xxxx"
export RUN_ID="run_xxxx"

# Poll every few seconds until status is "completed" (or "failed"/"cancelled")
curl -s "https://api.openai.com/v1/threads/$THREAD_ID/runs/$RUN_ID" \
  -H "Authorization: Bearer $OPENAI_API_KEY" \
  -H "OpenAI-Beta: assistants=v2"
```

Repeat until `"status": "completed"`.

**Optional: loop until completed (avoids quoting issues).** API returns `"status": "completed"` (with space), so we allow optional space in the match:
```bash
THREAD_ID="thread_xxxx"
RUN_ID="run_xxxx"
while true; do
  RESP=$(curl -s "https://api.openai.com/v1/threads/$THREAD_ID/runs/$RUN_ID" \
    -H "Authorization: Bearer $OPENAI_API_KEY" \
    -H "OpenAI-Beta: assistants=v2")
  STATUS=$(echo "$RESP" | sed -n 's/.*"status"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p')
  echo "Status: $STATUS"
  [ "$STATUS" = "completed" ] && break
  [ "$STATUS" = "failed" ] || [ "$STATUS" = "cancelled" ] || [ "$STATUS" = "expired" ] && { echo "Run ended: $STATUS"; exit 1; }
  sleep 3
done
echo "Done."
```
If `STATUS` is always empty, check: `echo "$RESP" | head -c 500` (and that `OPENAI_API_KEY` is set in that shell).

---

## 6. cURL – Step 4: Get thread messages (assistant reply)

```bash
export OPENAI_API_KEY="YOUR_OPENAI_API_KEY"
export THREAD_ID="thread_xxxx"

curl -s "https://api.openai.com/v1/threads/$THREAD_ID/messages?limit=10" \
  -H "Authorization: Bearer $OPENAI_API_KEY" \
  -H "OpenAI-Beta: assistants=v2"
```

The app uses the **first message** where `role == "assistant"` and reads `content[0].text.value` — that string is the AI output (JSON). Strip markdown code fences if present, then `json_decode` to get `overall_rating`, `summary`, `rooms`, `recommendations`, `issues_found`.

---

## 7. One-shot script (create thread → run → poll → get messages)

Save as `homecheck-ai-curl.sh` and run: `OPENAI_API_KEY=sk-... ASSISTANT_ID=asst_... ./homecheck-ai-curl.sh`. Uses the example payload above; replace the `text` body with your real payload if needed.

```bash
#!/usr/bin/env bash
set -e
OPENAI_API_KEY="${OPENAI_API_KEY:?Set OPENAI_API_KEY}"
ASSISTANT_ID="${ASSISTANT_ID:?Set ASSISTANT_ID}"
OPENAI_BETA="OpenAI-Beta: assistants=v2"

# 1) Create thread with user message (prompt + payload)
TEXT='You are the Abodeology HomeCheck AI assistant. You will receive structured JSON data for a property and its rooms. For each room, analyse condition, moisture risk, and presentation quality based ONLY on the data provided. Respond with a single JSON object with this exact structure:

{
  "overall_rating": number (1-10),
  "summary": string,
  "rooms": {
    "Room Name": {
      "rating": number (1-10),
      "comments": string,
      "moisture": number|null,
      "issues": [string, ...]
    }
  },
  "recommendations": [string, ...],
  "issues_found": [string, ...]
}

JSON ONLY, no markdown or extra text.

Here is the data to analyse:
{"property":{"id":6,"address":"123 Example Street","postcode":"SW1A 1AA","property_type":"terraced","bedrooms":3,"tenure":"freehold"},"rooms":{"Kitchen":{"images_count":2,"images_360":0,"images_regular":2,"moisture_readings":[45,48],"existing_ai_rating":null,"existing_ai_comments":null},"Living Room":{"images_count":1,"images_360":1,"images_regular":0,"moisture_readings":[],"existing_ai_rating":null,"existing_ai_comments":null},"Bathroom":{"images_count":2,"images_360":0,"images_regular":2,"moisture_readings":[52,55],"existing_ai_rating":null,"existing_ai_comments":null}}}'

THREAD_RESP=$(curl -s -X POST "https://api.openai.com/v1/threads" \
  -H "Authorization: Bearer $OPENAI_API_KEY" \
  -H "Content-Type: application/json" \
  -H "$OPENAI_BETA" \
  --data-binary @- <<EOF
{"messages":[{"role":"user","content":[{"type":"text","text":$(echo "$TEXT" | jq -Rs .)}]}]}
EOF
)

THREAD_ID=$(echo "$THREAD_RESP" | jq -r '.id')
if [ -z "$THREAD_ID" ] || [ "$THREAD_ID" = "null" ]; then
  echo "Failed to create thread:"; echo "$THREAD_RESP" | jq .
  exit 1
fi
echo "Thread ID: $THREAD_ID"

# 2) Run assistant
RUN_RESP=$(curl -s -X POST "https://api.openai.com/v1/threads/$THREAD_ID/runs" \
  -H "Authorization: Bearer $OPENAI_API_KEY" \
  -H "Content-Type: application/json" \
  -H "$OPENAI_BETA" \
  -d "{\"assistant_id\":\"$ASSISTANT_ID\"}")
RUN_ID=$(echo "$RUN_RESP" | jq -r '.id')
echo "Run ID: $RUN_ID"

# 3) Poll until completed
for i in {1..20}; do
  STATUS=$(curl -s "https://api.openai.com/v1/threads/$THREAD_ID/runs/$RUN_ID" \
    -H "Authorization: Bearer $OPENAI_API_KEY" \
    -H "$OPENAI_BETA" | jq -r '.status')
  echo "Run status: $STATUS"
  [ "$STATUS" = "completed" ] && break
  [ "$STATUS" = "failed" ] || [ "$STATUS" = "cancelled" ] || [ "$STATUS" = "expired" ] && exit 1
  sleep 3
done

# 4) Get messages
curl -s "https://api.openai.com/v1/threads/$THREAD_ID/messages?limit=10" \
  -H "Authorization: Bearer $OPENAI_API_KEY" \
  -H "$OPENAI_BETA" | jq '.data[] | select(.role=="assistant") | .content[0].text.value' -r
```

---

## Getting the real payload for HomeCheck 6

To dump the **exact** payload the app would send for HomeCheck 6:

1. **Option A – Log in code**  
   In `App\Services\HomeCheckReportService::generateAIAnalysisWithOpenAI()`, right after `$payload = [...];` add:
   ```php
   Log::info('HomeCheck AI payload', ['payload' => $payload]);
   ```
   Then trigger “Process AI Analysis” for HomeCheck 6 and check `storage/logs/laravel.log` for the JSON.

2. **Option B – Tinker**  
   ```bash
   php artisan tinker
   ```
   ```php
   $report = \App\Models\HomecheckReport::with('property')->find(6);
   $property = $report->property;
   $homecheckData = \App\Models\HomecheckData::where('homecheck_report_id', 6)->orWhere('property_id', $property->id)->orderBy('room_name')->orderBy('created_at')->get();
   $grouped = $homecheckData->groupBy('room_name');
   $rooms = [];
   foreach ($grouped as $roomName => $images) {
       $rooms[$roomName] = [
           'images_count' => $images->count(),
           'images_360' => $images->where('is_360', true)->count(),
           'images_regular' => $images->where('is_360', false)->count(),
           'moisture_readings' => $images->pluck('moisture_reading')->filter()->values(),
           'existing_ai_rating' => $images->first()->ai_rating ?? null,
           'existing_ai_comments' => $images->first()->ai_comments ?? null,
       ];
   }
   $payload = ['property' => ['id' => $property->id, 'address' => $property->address, 'postcode' => $property->postcode, 'property_type' => $property->property_type, 'bedrooms' => $property->bedrooms, 'tenure' => $property->tenure ?? null], 'rooms' => $rooms];
   echo json_encode($payload, JSON_PRETTY_PRINT);
   ```

Use that JSON in the “Here is the data to analyse:” part of the user message in the curl or script above.
