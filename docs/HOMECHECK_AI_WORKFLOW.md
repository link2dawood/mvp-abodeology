# HomeCheck AI – Full workflow

End-to-end flow from trigger to stored data and UI.

---

## 1. Trigger

| How | Where | Action |
|-----|--------|--------|
| **Button on HomeCheck detail** | `GET /admin/homechecks/{id}` (show) | User clicks **“Process AI Analysis”** and confirms. Form **POST**s to `admin.homechecks.process-ai`. |
| **Optional from Edit** | Edit HomeCheck with “Process AI analysis after update” checked | On save, controller redirects to process-ai (see routes: must be POST for actual processing). |

**Route**

- `POST /admin/homechecks/{id}/process-ai` → `AdminController@processHomeCheckAI`  
- `GET` to same URL → redirect with error (“Please use the Process AI Analysis button”).

**Code:** `routes/web.php` (homechecks.process-ai), `resources/views/admin/homechecks/show.blade.php` (form).

---

## 2. Controller entry and checks

**Method:** `App\Http\Controllers\AdminController::processHomeCheckAI($id)`

1. **Auth:** User must be authenticated; middleware `role.web:admin,agent` (admin or agent).
2. **Load:** `HomecheckReport::with(['property', 'homecheckData'])->findOrFail($id)`; `$property = $homecheckReport->property`.
3. **Agent access:** If role is agent, verifies property is in their allowed list.
4. **Images:** If `$homecheckReport->homecheckData->isEmpty()` → redirect back with error “No images found…”.

**Code:** `app/Http/Controllers/AdminController.php` (processHomeCheckAI, ~2663–2693).

---

## 3. Load HomeCheck data

- **Query:** `HomecheckData::where('homecheck_report_id', $homecheckReport->id)->orWhere('property_id', $property->id)->orderBy('room_name')->orderBy('created_at')->get()`.
- **Result:** Collection of image records (room_name, image_path, is_360, moisture_reading, ai_rating, ai_comments, etc.) for this report/property.

**Code:** `AdminController::processHomeCheckAI` (~2699–2705).

---

## 4. Generate AI analysis

**Call:** `$reportService->generateAIAnalysis($homecheckData, $property)`  
**Service:** `App\Services\HomeCheckReportService`

### 4a. Decision: OpenAI vs fallback

- If `OPENAI_API_KEY` and `OPENAI_ASSISTANT_ID` are set → **OpenAI path** (`generateAIAnalysisWithOpenAI`).
- Else or on failure → **Fallback** (`generateFallbackAnalysis`): simulated ratings/comments, no real AI.

### 4b. OpenAI path (Assistants API)

1. **Build payload (text only, no images):**
   - `property`: id, address, postcode, property_type, bedrooms, tenure.
   - `rooms`: for each room name → images_count, images_360, images_regular, moisture_readings (array), existing_ai_rating, existing_ai_comments (from first image in room).

2. **Build prompt:** Instructions to respond with a single JSON object (overall_rating, summary, rooms with per-room rating/comments/moisture/issues, recommendations, issues_found). Then: “Here is the data to analyse:” + JSON payload.

3. **API calls:**
   - `POST https://api.openai.com/v1/threads` with one user message (prompt + payload).
   - `POST …/threads/{threadId}/runs` with `assistant_id` = `OPENAI_ASSISTANT_ID`.
   - Poll `GET …/threads/{threadId}/runs/{runId}` until status is `completed` (or timeout).
   - `GET …/threads/{threadId}/messages` → take first message with `role === 'assistant'` → `content[0].text.value`.

4. **Parse:** Strip markdown code fences, `json_decode` the text. Normalise room keys (e.g. comment vs comments) and remap room names to match DB (case-insensitive). Return decoded array.

**Code:** `HomeCheckReportService::generateAIAnalysisWithOpenAI()` (~207–382).

### 4c. Fallback path

- Group images by room; for each room call `analyzeRoom()` (random rating 7–10, optional moisture, generic issues). Build `recommendations` from `generateRecommendations()`. Return same shape as OpenAI (rooms, overall_rating, summary, recommendations, issues_found).

**Code:** `HomeCheckReportService::generateFallbackAnalysis()`, `analyzeRoom()`, `generateRecommendations()`.

### 4d. Return shape (minimum)

- `rooms`: object, keys = room names, each with at least `rating`, `comments` (or equivalent).
- Optional: `overall_rating`, `summary`, `recommendations[]`, `issues_found[]`, and any extra keys (preserved and shown in report).

---

## 5. Apply room-level analysis to each image (controller)

Still in `processHomeCheckAI`:

- Build normalised lookup from `$aiAnalysis['rooms']` (by room name, case-insensitive).
- For each `HomecheckData` row:
  - Find room analysis by `room_name` (or normalised key).
  - Set `ai_rating` = room `rating`, `ai_comments` = room `comments` (or overall summary if room comment empty).
  - If no room match but there is an overall summary, set `ai_comments` (and optionally `ai_rating`) from that.
- **Result:** Every image in a room gets the same room-level rating and comments in the DB.

**Code:** `AdminController::processHomeCheckAI` (~2709–2745).

---

## 6. Generate and save report (service)

**Call:** `$reportService->processAndGenerateReport($homecheckReport)`

Inside the service:

1. **Load homecheck data again** (same report/property query as in controller).
2. **Call `generateAIAnalysis()` again** (same as step 4) to get `$aiAnalysis` for report content.
3. **Build HTML:** `generateReportContent($property, $homecheckData, $aiAnalysis, $homecheckReport)`:
   - Executive summary: overall_rating, summary, plus any extra top-level keys.
   - Room-by-room: for each room, rating, moisture, image counts, comments, issues, plus any extra per-room keys.
   - Recommendations list, issues_found list.
4. **Save file:** `saveReport()` → writes HTML to storage (e.g. `homecheck-reports/{property_id}/{filename}.html` on default disk – S3 or public).
5. **Update report:** `HomecheckReport::update(['report_path' => $filePath, 'provider' => 'Abodeology AI'])`.
6. **Property document:** `PropertyDocument::updateOrCreate` for this property with `document_type = 'homecheck'`, `file_path` = report path.
7. **Update HomecheckData again:** Same room-level logic as in the controller – set `ai_rating`, `ai_comments` (and optionally moisture) on each image from `$aiAnalysis['rooms']`. (So if the controller already did this, this is redundant for those fields; report path and PropertyDocument are the new side effects.)

**Code:** `HomeCheckReportService::processAndGenerateReport()`, `generateReportContent()`, `saveReport()` (~22–121, 562–761).

---

## 7. Optional: per-image Vision analysis

If `config('services.openai.analyze_per_image')` is true (e.g. `OPENAI_ANALYZE_PER_IMAGE=true`):

- **Call:** `$reportService->updatePerImageAnalysis($homecheckData, $property)`.
- For each `HomecheckData` that has an image:
  - Resolve image URL (e.g. S3 signed or local asset).
  - Call OpenAI **Chat Completions** (Vision) with that image URL and a short prompt; expect JSON `{ "rating", "comments" }`.
  - Update that row’s `ai_rating` and `ai_comments` with the vision result (overwriting the room-level values for that image).

**Code:** `HomeCheckReportService::updatePerImageAnalysis()`, `analyzeSingleImageWithVision()` (~395–498). Config: `config/services.php` (openai.analyze_per_image, openai.vision_model).

---

## 8. Response to user

- **Success:** Redirect to `admin.homechecks.show` with success flash: “AI analysis completed successfully! Analysis has been added to each image.”
- **Report save failed:** Redirect to show with warning; analysis is still on images.
- **Exception:** Redirect to show with error message; log the exception.

**Code:** `AdminController::processHomeCheckAI` (~2756–2772).

---

## 9. Where data is stored

| What | Where |
|------|--------|
| Per-image AI | `homecheck_data.ai_rating`, `homecheck_data.ai_comments` (and optionally moisture from room analysis). |
| Report file | Storage (e.g. `homecheck-reports/{property_id}/homecheck-report-{property_id}-{report_id}-{time}.html`). |
| Report path | `homecheck_reports.report_path`, `homecheck_reports.provider`. |
| Property document | `property_documents` (property_id, document_type = 'homecheck', file_path). |

---

## 10. Where data appears in the UI

| Screen | What’s shown |
|--------|----------------|
| **Admin HomeCheck list** (`/admin/homechecks`) | “✓ Report” (link) if `report_path` set; “✓ AI connected” or “○ AI fallback” from config. |
| **Admin HomeCheck detail** (`/admin/homechecks/{id}`) | “Process AI Analysis” button (if images, etc.); AI Report & Analysis card (overall + per room from `getHomeCheckAnalysis()`); per-room sections and per-image cards/modal use `homecheck_data.ai_rating` and `ai_comments`. |
| **Admin HomeCheck edit** | “Process AI analysis after update” option; AI preview from first image in room. |
| **Seller HomeCheck report** | Report and per-room / per-image AI from same `homecheck_data` and report path. |

`getHomeCheckAnalysis()` builds the “AI Report & Analysis” structure from the first image per room’s `ai_rating` / `ai_comments` (so it reflects whatever is in the DB after room-level or per-image update).

**Code:** `AdminController::showHomeCheck()`, `getHomeCheckAnalysis()`; views: `admin/homechecks/show.blade.php`, `admin/homechecks/index.blade.php`; seller: `seller/homecheck-report.blade.php` (and related).

---

## 11. Code reference (summary)

| Step | Class / method / file |
|------|------------------------|
| Route | `routes/web.php` – `homechecks.process-ai` (POST/GET). |
| Trigger | `resources/views/admin/homechecks/show.blade.php` – form to process-ai. |
| Entry | `App\Http\Controllers\AdminController::processHomeCheckAI($id)`. |
| Load data | Same controller – HomecheckReport, property, HomecheckData. |
| AI analysis | `App\Services\HomeCheckReportService::generateAIAnalysis()`. |
| OpenAI call | `HomeCheckReportService::generateAIAnalysisWithOpenAI()` (thread → run → messages → parse JSON). |
| Fallback | `HomeCheckReportService::generateFallbackAnalysis()`, `analyzeRoom()`. |
| Apply to DB (controller) | `AdminController::processHomeCheckAI` – foreach HomecheckData, update ai_rating, ai_comments. |
| Report generation | `HomeCheckReportService::processAndGenerateReport()` → `generateReportContent()`, `saveReport()`, update report + PropertyDocument + HomecheckData. |
| Per-image Vision | `HomeCheckReportService::updatePerImageAnalysis()`, `analyzeSingleImageWithVision()`. |
| Display | `AdminController::showHomeCheck()`, `getHomeCheckAnalysis()`; admin/seller views above. |

---

## 12. Config / env

- **Room-level AI (Assistants API):** `OPENAI_API_KEY`, `OPENAI_ASSISTANT_ID`.
- **Per-image AI (Vision):** `OPENAI_ANALYZE_PER_IMAGE=true`, optional `OPENAI_VISION_MODEL` (e.g. `gpt-4o-mini`).
- **Where AI output comes from:** Your assistant’s first reply text (see `docs/HOMECHECK_AI_OUTPUT_SOURCE.md`).
